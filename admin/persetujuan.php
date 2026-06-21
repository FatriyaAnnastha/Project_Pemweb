<?php
require_once '../backend/session_config.php';
requireLogin();
if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}
$activePage = 'persetujuan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Warung – CariMakan.ID</title>
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
            <h1>⏳ Persetujuan Pengajuan Warung</h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="role-badge admin"><img src="../assets/6.login-avatar.png" class="logout" alt="user"> Admin</span>
                <span id="admin-name" style="font-weight:700;font-size:13px;"><?php echo $_SESSION['nama']; ?></span>
            </div>
        </header>

        <div class="main-body">
            
            <div class="card-panel">
                <h2>Pengajuan Warung Baru Menunggu Persetujuan</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Warung</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Pedagang</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tunggu-warung-body"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="../assets/js/app.js"></script>
<script>
window.addEventListener('load', async () => {
    await rendertunggu();
});

async function rendertunggu() {
    const list = await gettungguWarung();
    const badge = document.getElementById('tunggu-badge');
    if (badge) {
        badge.textContent = list.length;
        badge.style.display = list.length > 0 ? 'inline' : 'none';
    }
    
    document.getElementById('tunggu-warung-body').innerHTML = list.length
        ? list.map(w => `
            <tr>
                <td><strong>${w.nama}</strong></td>
                <td>${w.kategori}</td>
                <td>${w.lokasi}</td>
                <td>${w.pedagang_nama || w.pedagang_id}</td>
                <td style="font-size:12px;max-width:180px;">${w.deskripsi||'–'}</td>
                <td style="display:flex;gap:6px;">
                    <button class="btn btn-sm btn-primary" onclick="doApprove(${w.id},true)">✅ Setujui</button>
                    <button class="btn btn-sm" style="background:#fdecea;color:#e74c3c;" onclick="doApprove(${w.id},false)">❌ Tolak</button>
                </td>
            </tr>`).join('')
        : '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:24px 0;">Tidak ada warung menunggu persetujuan</td></tr>';
}

async function doApprove(id, approve) {
    const actionText = approve ? 'menyetujui' : 'menolak';
    if (!confirm(`Apakah Anda yakin ingin ${actionText} pengajuan warung ini?`)) return;
    await approveWarung(id, approve);
    showToast(approve ? '✅ Warung disetujui!' : '❌ Warung ditolak');
    await rendertunggu();
}
</script>
</body>
</html>
