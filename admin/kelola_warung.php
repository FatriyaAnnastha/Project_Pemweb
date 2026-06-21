<?php
require_once '../backend/session_config.php';
requireLogin();
if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}
$activePage = 'warung';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Warung Makan – CariMakan.ID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-theme">
<div id="loading"><img src="../assets/1.Logo_CariMakan.png" alt="Logo"><p>Memuat Admin Panel…</p></div>

<div class="dashboard-layout">
    
    <!-- SIDEBAR -->
    <?php include 'sidebar.php'; ?>

    <!-- MAIN -->
    <main class="main-content">
        <header class="main-header">
            <h1>🏪 Kelola Warung Makan</h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="role-badge admin"><img src="../assets/6.login-avatar.png" class="logout" alt="user"> Admin</span>
                <span id="admin-name" style="font-weight:700;font-size:13px;"><?php echo $_SESSION['nama']; ?></span>
            </div>
        </header>

        <div class="main-body">
            
            <div class="card-panel">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
                    <h2>Daftar Semua Warung</h2>
                    <input type="text" id="search-warung" placeholder="Cari warung..."
                        oninput="renderAdminWarung()"
                        style="padding:8px 14px;border:1.5px solid #e0dbd5;border-radius:8px;font-size:13px;font-family:'Poppins',sans-serif;width:220px;">
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Warung</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Pedagang</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="admin-warung-body"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="../assets/js/app.js"></script>
<script>
window.addEventListener('load', async () => {
    await renderAdminWarung();
    await updatePendingBadge();
});

async function renderAdminWarung() {
    const q = document.getElementById('search-warung')?.value.toLowerCase() || '';
    let list = await getWarung();
    // note: getWarung returns all active, but let's query all_warung since we are admin!
    // Wait! Let's check:
    const adminList = await getAllWarung();
    let displayList = adminList;
    if (q) {
        displayList = adminList.filter(w => w.nama.toLowerCase().includes(q) || w.kategori.toLowerCase().includes(q));
    }
    
    document.getElementById('admin-warung-body').innerHTML = displayList.map(w => `
        <tr>
            <td><strong>${w.nama}</strong><br><small>${w.deskripsi||''}</small></td>
            <td>${w.kategori}</td>
            <td>${w.lokasi}</td>
            <td>${w.pedagang_nama || w.pedagang_id}</td>
            <td>⭐ ${w.rating}</td>
            <td><span class="badge ${w.status==='aktif'?'badge-green':'badge-red'}">${w.status}</span></td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="adminToggle(${w.id})">${w.status==='aktif'?'⛔':'✅'}</button>
                <button class="btn btn-sm" style="background:#fdecea;color:#e74c3c;" onclick="adminHapus(${w.id})">🗑️</button>
            </td>
        </tr>
    `).join('') || '<tr><td colspan="7" style="text-align:center;">Tidak ada data warung</td></tr>';
}

async function adminToggle(id) {
    await toggleWarungStatus(id);
    await renderAdminWarung();
    showToast('Status warung diperbarui');
}

async function adminHapus(id) {
    if (!confirm('Hapus warung ini secara permanen?')) return;
    await deleteWarung(id);
    await renderAdminWarung();
    showToast('Warung berhasil dihapus');
}

async function updatePendingBadge() {
    const list = await gettungguWarung();
    const badge = document.getElementById('tunggu-badge');
    if (badge) {
        badge.textContent = list.length;
        badge.style.display = list.length > 0 ? 'inline' : 'none';
    }
}
</script>
</body>
</html>
