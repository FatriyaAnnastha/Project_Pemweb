<?php
require_once '../backend/session_config.php';
requireLogin();
if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}
$activePage = 'laporan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan & Analitik – CariMakan.ID</title>
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
            <h1>📋 Laporan & Analitik</h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="role-badge admin"><img src="../assets/6.login-avatar.png" class="logout" alt="user"> Admin</span>
                <span id="admin-name" style="font-weight:700;font-size:13px;"><?php echo $_SESSION['nama']; ?></span>
            </div>
        </header>

        <div class="main-body">
            
            <div class="stats-grid">
                <div class="stat-card kota-card">
                    <div class="stat-icon">📍</div>
                    <div class="stat-info"><h3 id="l-kota">–</h3><p>Kota Terbanyak</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🍜</div>
                    <div class="stat-info"><h3 id="l-kat">–</h3><p>Kategori Terpopuler</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-info"><h3 id="l-rating">–</h3><p>Rating Tertinggi</p></div>
                </div>
            </div>

            <div class="card-panel">
                <h2>📊 Distribusi Kategori Warung</h2>
                <div id="chart-container" style="padding:10px 0;"></div>
            </div>

            <div class="card-panel">
                <h2>📋 Log Aktivitas Sistem</h2>
                <div id="log-body" style="font-size:13px;line-height:2;color:var(--text-muted);"></div>
            </div>

        </div>
    </main>
</div>

<script src="../assets/js/app.js"></script>
<script>
window.addEventListener('load', async () => {
    await renderLaporan();
    await updatePendingBadge();
});

async function renderLaporan() {
    const list = await getWarung();
    if (list.length === 0) {
        document.getElementById('l-kota').textContent = '–';
        document.getElementById('l-kat').textContent = '–';
        document.getElementById('l-rating').textContent = '–';
        document.getElementById('chart-container').innerHTML = '<p style="color:#aaa;text-align:center;">Belum ada data warung aktif</p>';
        return;
    }
    
    // Hitung kota terbanyak
    const kotaCount = {};
    list.forEach(w => kotaCount[w.lokasi] = (kotaCount[w.lokasi]||0)+1);
    const topKota = Object.entries(kotaCount).sort((a,b)=>b[1]-a[1])[0];
    document.getElementById('l-kota').textContent = topKota ? topKota[0] : '–';
    
    // Hitung kategori terpopuler
    const katCount = {};
    list.forEach(w => katCount[w.kategori] = (katCount[w.kategori]||0)+1);
    const topKat = Object.entries(katCount).sort((a,b)=>b[1]-a[1])[0];
    document.getElementById('l-kat').textContent = topKat ? topKat[0] : '–';
    
    // Hitung rating tertinggi
    const ratings = list.map(w=>parseFloat(w.rating) || 0);
    const topRating = Math.max(...ratings);
    document.getElementById('l-rating').textContent = topRating > 0 ? '⭐ ' + topRating : '–';
    
    // Render chart kategori
    const maxCount = Math.max(...Object.values(katCount));
    document.getElementById('chart-container').innerHTML = Object.entries(katCount).map(([k,v]) => `
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
            <div style="width:100px;font-size:12.5px;font-weight:600;">${k}</div>
            <div style="flex:1;background:#f0ece8;border-radius:8px;height:26px;overflow:hidden;">
                <div style="width:${(v/maxCount)*100}%;background:var(--admin-color);height:100%;border-radius:8px;display:flex;align-items:center;padding-left:10px;">
                    <span style="color:white;font-size:11px;font-weight:700;">${v} warung</span>
                </div>
            </div>
        </div>
    `).join('');
    
    // Render log
    const now = new Date();
    document.getElementById('log-body').innerHTML = `🟢 ${now.toLocaleString('id')} — Admin login berhasil<br>
    📋(${list.length} warung aktif)<br>
    👥 Sinkronisasi data pengguna selesai`;
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
