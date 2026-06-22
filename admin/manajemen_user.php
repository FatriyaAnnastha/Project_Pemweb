<?php
require_once '../backend/session_config.php';
requireLogin();
if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}
$activePage = 'users';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User – CariMakan.ID</title>
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
            <h1>👥 Manajemen Pengguna</h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="role-badge admin"><img src="../assets/6.login-avatar.png" class="logout" alt="user"> Admin</span>
                <span id="admin-name" style="font-weight:700;font-size:13px;"><?php echo $_SESSION['nama']; ?></span>
            </div>
        </header>

        <div class="main-body">
            
            <div class="card-panel">
                <h2>Daftar Pengguna Platform</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="users-body"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="../assets/js/app.js"></script>
<script>
window.addEventListener('load', async () => {
    await renderUsers();
    await updatePendingBadge();
});

async function renderUsers() {
    const users = await getAllUsers();
    const roleColor = { user:'badge-green', pedagang:'badge-blue', admin:'badge-purple' };
    document.getElementById('users-body').innerHTML = users.map((u,i) => `
        <tr>
            <td>${i+1}</td>
            <td><strong>${u.nama}</strong></td>
            <td>${u.email}</td>
            <td><span class="badge ${roleColor[u.role] || 'badge-green'}">${u.role}</span></td>
            <td>
                ${u.role==='admin' ? '–' : `<button class="btn btn-sm" style="background:#fdecea;color:#e74c3c;" onclick="hapusUser(${u.id})">🗑️</button>`}
            </td>
        </tr>
    `).join('') || '<tr><td colspan="5" style="text-align:center;">Tidak ada pengguna terdaftar</td></tr>';
}


async function hapusUser(id) {
    if (!confirm('Hapus pengguna ini? Semua data terkait (warung/ulasan) mungkin juga akan terpengaruh.')) return;
    await deleteUser(id);
    await renderUsers();
    showToast('🗑️ User berhasil dihapus');
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
