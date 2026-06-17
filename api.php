<?php
require_once 'config.php';

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        $data = json_decode(file_get_contents('php://input'), true);
    } else {
        $data = $_POST;
    }
} else {
    $data = $_GET;
}

switch ($action) {
    case 'register':
        $nama = $_POST['nama'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        $toko = ($role == 'pedagang') ? ($_POST['toko'] ?? strtolower(str_replace(' ', '_', $nama))) : null;
        if (!$nama || !$email || !$password) {
            echo json_encode(['error' => 'Semua field harus diisi']);
            break;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role, toko) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $email, $password, $role, $toko]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Email sudah terdaftar']);
        }
        break;
        
    case 'login':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['toko'] = $user['toko'];
            echo json_encode(['success' => true, 'role' => $user['role']]);
        } else {
            echo json_encode(['error' => 'Email atau password salah']);
        }
        break;
        
    case 'logout':
        session_destroy();
        echo json_encode(['success' => true]);
        break;
        
    case 'session':
        if (isLoggedIn()) {
            echo json_encode([
                'logged_in' => true,
                'id' => $_SESSION['user_id'],
                'nama' => $_SESSION['nama'],
                'role' => $_SESSION['role'],
                'toko' => $_SESSION['toko']
            ]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
        break;
        
    case 'warung':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $stmt = $pdo->prepare("SELECT w.*, u.nama as pedagang_nama FROM warung w JOIN users u ON w.pedagang_id = u.id WHERE w.id = ?");
                $stmt->execute([$id]);
                $warung = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($warung) {
                    $stmtMenu = $pdo->prepare("SELECT * FROM menu WHERE warung_id = ?");
                    $stmtMenu->execute([$id]);
                    $warung['menu'] = $stmtMenu->fetchAll(PDO::FETCH_ASSOC);
                    $stmtRev = $pdo->prepare("SELECT r.*, u.nama as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.warung_id = ? ORDER BY r.tanggal DESC");
                    $stmtRev->execute([$id]);
                    $warung['reviews'] = $stmtRev->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($warung);
                } else {
                    echo json_encode(['error' => 'Warung tidak ditemukan']);
                }
            } else {
                $q = $_GET['q'] ?? '';
                $harga = $_GET['harga'] ?? '';
                $lokasi = $_GET['lokasi'] ?? '';
                $kategori = $_GET['kategori'] ?? '';
                $sql = "SELECT w.*, u.nama as pedagang_nama FROM warung w JOIN users u ON w.pedagang_id = u.id WHERE w.status = 'aktif'";
                $params = [];
                if ($q) {
                    $sql .= " AND (w.nama LIKE ? OR w.deskripsi LIKE ?)";
                    $params[] = "%$q%";
                    $params[] = "%$q%";
                }
                if ($harga) {
                    $sql .= " AND w.harga = ?";
                    $params[] = $harga;
                }
                if ($lokasi) {
                    $sql .= " AND w.lokasi = ?";
                    $params[] = $lokasi;
                }
                if ($kategori) {
                    $sql .= " AND w.kategori = ?";
                    $params[] = $kategori;
                }
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($list);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireLogin();
            if (!isPedagang() && !isAdmin()) {
                echo json_encode(['error' => 'Hanya pedagang atau admin']);
                break;
            }
            $id = $_POST['id'] ?? null;
            $nama = $_POST['nama'] ?? '';
            $kategori = $_POST['kategori'] ?? '';
            $lokasi = $_POST['lokasi'] ?? '';
            $harga = $_POST['harga'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $lat = $_POST['lat'] ?? null;
            $lng = $_POST['lng'] ?? null;
            $jam_buka = $_POST['jam_buka'] ?? null;
            $jam_tutup = $_POST['jam_tutup'] ?? null;
            $hari_kerja = $_POST['hari_kerja'] ?? '';
            $img = $_POST['img'] ?? '';
            $wa  = $_POST['wa'] ?? '';
            if ($id) {
                $sql = "UPDATE warung SET nama=?, kategori=?, lokasi=?, harga=?, deskripsi=?, lat=?, lng=?, jam_buka=?, jam_tutup=?, hari_kerja=?, img=?, wa=? WHERE id=?";
                $params = [$nama, $kategori, $lokasi, $harga, $deskripsi, $lat, $lng, $jam_buka, $jam_tutup, $hari_kerja, $img, $wa, $id];
                if (!isAdmin()) {
                    $sql .= " AND pedagang_id=?";
                    $params[] = $_SESSION['user_id'];
                }
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                echo json_encode(['success' => true]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO warung (pedagang_id, nama, kategori, lokasi, harga, deskripsi, lat, lng, jam_buka, jam_tutup, hari_kerja, img, wa, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?, 'pending')");
                $stmt->execute([$_SESSION['user_id'], $nama, $kategori, $lokasi, $harga, $deskripsi, $lat, $lng, $jam_buka, $jam_tutup, $hari_kerja, $img, $wa]);
                echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            requireLogin();
            parse_str(file_get_contents("php://input"), $delete_vars);
            $id = $delete_vars['id'] ?? $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['error' => 'ID diperlukan']);
                break;
            }
            if (isAdmin()) {
                $stmt = $pdo->prepare("DELETE FROM warung WHERE id = ?");
                $stmt->execute([$id]);
            } elseif (isPedagang()) {
                $stmt = $pdo->prepare("DELETE FROM warung WHERE id = ? AND pedagang_id = ?");
                $stmt->execute([$id, $_SESSION['user_id']]);
            } else {
                echo json_encode(['error' => 'Unauthorized']);
                break;
            }
            echo json_encode(['success' => true]);
        }
        break;
    case 'all_warung':
        requireLogin();
        if (!isAdmin()) { echo json_encode(['error' => 'Hanya admin']); break; }
        $stmt = $pdo->query("SELECT w.*, u.nama as pedagang_nama FROM warung w JOIN users u ON w.pedagang_id = u.id ORDER BY w.id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'pending_warung':
        requireLogin();
        if (!isAdmin()) { echo json_encode(['error' => 'Hanya admin']); break; }
        $stmt = $pdo->query("SELECT w.*, u.nama as pedagang_nama FROM warung w JOIN users u ON w.pedagang_id = u.id WHERE w.status = 'pending' ORDER BY w.id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'approve_warung':
        requireLogin();
        if (!isAdmin()) { echo json_encode(['error' => 'Hanya admin']); break; }
        $id = $_POST['id'] ?? null;
        $approve = $_POST['approve'] ?? '1';
        $newStatus = ($approve === '1') ? 'aktif' : 'nonaktif';
        $stmt = $pdo->prepare("UPDATE warung SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
        echo json_encode(['success' => true, 'status' => $newStatus]);
        break;  

    case 'my_warung':
        requireLogin();
        if (!isPedagang()) {
            echo json_encode([]);
            break;
        }
        $stmt = $pdo->prepare("SELECT * FROM warung WHERE pedagang_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $warungs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($warungs as &$w) {
            $stmtM = $pdo->prepare("SELECT * FROM menu WHERE warung_id = ?");
            $stmtM->execute([$w['id']]);
            $w['menu'] = $stmtM->fetchAll(PDO::FETCH_ASSOC);
        }
        echo json_encode($warungs);
        break;
        
    case 'toggle_status':
        requireLogin();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['error' => 'ID diperlukan']);
            break;
        }
        if (isAdmin()) {
            $stmt = $pdo->prepare("SELECT status FROM warung WHERE id = ?");
            $stmt->execute([$id]);
        } elseif (isPedagang()) {
            $stmt = $pdo->prepare("SELECT status FROM warung WHERE id = ? AND pedagang_id = ?");
            $stmt->execute([$id, $_SESSION['user_id']]);
        } else {
            echo json_encode(['error' => 'Unauthorized']);
            break;
        }
        $row = $stmt->fetch();
        if (!$row) {
            echo json_encode(['error' => 'Warung tidak ditemukan']);
            break;
        }
        $newStatus = ($row['status'] === 'aktif') ? 'nonaktif' : 'aktif';
        if (isAdmin()) {
            $upd = $pdo->prepare("UPDATE warung SET status = ? WHERE id = ?");
            $upd->execute([$newStatus, $id]);
        } else {
            $upd = $pdo->prepare("UPDATE warung SET status = ? WHERE id = ? AND pedagang_id = ?");
            $upd->execute([$newStatus, $id, $_SESSION['user_id']]);
        }
        echo json_encode(['success' => true, 'new_status' => $newStatus]);
        break;
        
    case 'add_menu':
        requireLogin();
        if (!isPedagang()) {
            echo json_encode(['error' => 'Hanya pedagang']);
            break;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $warung_id = $data['warung_id'] ?? 0;
        $nama = $data['nama'] ?? '';
        $harga = $data['harga'] ?? 0;
        $gambar = $data['gambar'] ?? '';
        $stmt = $pdo->prepare("SELECT id FROM warung WHERE id = ? AND pedagang_id = ?");
        $stmt->execute([$warung_id, $_SESSION['user_id']]);
        if (!$stmt->fetch()) {
            echo json_encode(['error' => 'Warung tidak ditemukan atau bukan milik Anda']);
            break;
        }
        $stmt = $pdo->prepare("INSERT INTO menu (warung_id, nama, harga, gambar) VALUES (?,?,?,?)");
        $stmt->execute([$warung_id, $nama, $harga, $gambar]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;
        
    case 'delete_menu':
        requireLogin();
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            echo json_encode(['error' => 'ID menu diperlukan']);
            break;
        }
        $stmt = $pdo->prepare("DELETE m FROM menu m JOIN warung w ON m.warung_id = w.id WHERE m.id = ? AND w.pedagang_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        echo json_encode(['success' => true]);
        break;
        
    case 'add_review':
        requireLogin();
        $data = json_decode(file_get_contents('php://input'), true);
        $warung_id = $data['warung_id'] ?? 0;
        $rating = $data['rating'] ?? 0;
        $komentar = $data['komentar'] ?? '';
        if ($rating < 1 || $rating > 5) {
            echo json_encode(['error' => 'Rating harus 1-5']);
            break;
        }
        $stmt = $pdo->prepare("INSERT INTO reviews (warung_id, user_id, rating, komentar) VALUES (?,?,?,?)");
        $stmt->execute([$warung_id, $_SESSION['user_id'], $rating, $komentar]);
        $stmtAvg = $pdo->prepare("SELECT AVG(rating) as avg FROM reviews WHERE warung_id = ?");
        $stmtAvg->execute([$warung_id]);
        $avg = $stmtAvg->fetch(PDO::FETCH_ASSOC)['avg'];
        $stmtUp = $pdo->prepare("UPDATE warung SET rating = ? WHERE id = ?");
        $stmtUp->execute([round($avg,1), $warung_id]);
        echo json_encode(['success' => true]);
        break;
        
    case 'favorite':
        requireLogin();
        $warung_id = $_POST['warung_id'] ?? 0;
        $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND warung_id = ?");
        $stmt->execute([$_SESSION['user_id'], $warung_id]);
        if ($stmt->fetch()) {
            $stmtDel = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND warung_id = ?");
            $stmtDel->execute([$_SESSION['user_id'], $warung_id]);
            echo json_encode(['action' => 'removed']);
        } else {
            $stmtIns = $pdo->prepare("INSERT INTO favorites (user_id, warung_id) VALUES (?,?)");
            $stmtIns->execute([$_SESSION['user_id'], $warung_id]);
            echo json_encode(['action' => 'added']);
        }
        break;
        
    case 'get_favorites':
        requireLogin();
        $stmt = $pdo->prepare("SELECT w.* FROM warung w JOIN favorites f ON w.id = f.warung_id WHERE f.user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $favs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($favs);
        break;
        
    case 'all_users':
        requireLogin();
        if (!isAdmin()) {
            echo json_encode(['error' => 'Hanya admin']);
            break;
        }
        $stmt = $pdo->query("SELECT id, nama, email, role, toko FROM users");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'add_user':
        requireLogin();
        if (!isAdmin()) {
            echo json_encode(['error' => 'Hanya admin']);
            break;
        }
        $nama = $_POST['nama'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        if (!$nama || !$email || !$password) {
            echo json_encode(['error' => 'Semua field harus diisi']);
            break;
        }
        $toko = ($role == 'pedagang') ? strtolower(str_replace(' ', '_', $nama)) : null;
        try {
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role, toko) VALUES (?,?,?,?,?)");
            $stmt->execute([$nama, $email, $password, $role, $toko]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Email sudah terdaftar']);
        }
        break;
        
    case 'delete_user':
        requireLogin();
        if (!isAdmin()) {
            echo json_encode(['error' => 'Hanya admin']);
            break;
        }
        $id = $_GET['id'] ?? 0;
        if ($id == $_SESSION['user_id']) {
            echo json_encode(['error' => 'Tidak bisa menghapus diri sendiri']);
            break;
        }
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;
        
    default:
        echo json_encode(['error' => 'Aksi tidak dikenal']);
}
?>