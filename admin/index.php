<?php
require_once '../backend/session_config.php';
requireLogin();
if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}
$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin – CariMakan.ID</title>
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
            <h1>📊 Dashboard Overview</h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="role-badge admin"><img src="../assets/6.login-avatar.png" class="logout" alt="user"> Admin</span>
                <span id="admin-name" style="font-weight:700;font-size:13px;"><?php echo $_SESSION['nama']; ?></span>
            </div>
        </header>

        <div class="main-body">
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">🏪</div>
                    <div class="stat-info"><h3 id="a-warung">0</h3><p>Total Warung</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info"><h3 id="a-aktif">0</h3><p>Warung Aktif</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⛔</div>
                    <div class="stat-info"><h3 id="a-nonaktif">0</h3><p>Warung Nonaktif</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info"><h3 id="a-users">0</h3><p>Total User</p></div>
                </div>
            </div>

            <div class="card-panel">
                <h2>📈 Warung Rating Tertinggi</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Rating</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="top-warung-body"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="../assets/js/app.js"></script>
<script>
window.addEventListener('load', async () => {
    await renderAll();
});

async function renderAll() {
    await renderStats();
    await renderTopWarung();
    await updatePendingBadge();
}

async function renderStats() {
    const list = await getWarung();
    const aktif = list.filter(w => w.status === 'aktif');
    document.getElementById('a-warung').textContent = list.length;
    document.getElementById('a-aktif').textContent = aktif.length;
    document.getElementById('a-nonaktif').textContent = list.length - aktif.length;
    
    // Fetch users for count
    const users = await getAllUsers();
    document.getElementById('a-users').textContent = users.length;
}

async function renderTopWarung() {
    const list = await getWarung();
    const sorted = [...list].sort((a,b) => b.rating - a.rating).slice(0,5);
    document.getElementById('top-warung-body').innerHTML = sorted.map((w,i) => `
        <tr>
            <td>${i+1}</td>
            <td><strong>${w.nama}</strong></td>
            <td>${w.kategori}</td>
            <td>${w.lokasi}</td>
            <td>⭐ ${w.rating}</td>
            <td><span class="badge ${w.status==='aktif'?'badge-green':'badge-red'}">${w.status}</span></td>
        </tr>
    `).join('') || '<tr><td colspan="6" style="text-align:center;">Tidak ada data warung aktif</td></tr>';
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
