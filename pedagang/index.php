<?php
require_once '../backend/session_config.php';
requireLogin();
if (!isPedagang()) {
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
    <title>Dashboard Pedagang – CariMakan.ID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="pedagang-theme">
<div id="loading"><img src="../assets/1.Logo_CariMakan.png" alt="Logo"><p>Memuat Dashboard…</p></div>

<div class="dashboard-layout">
    
    <!-- SIDEBAR -->
    <?php include 'sidebar.php'; ?>

    <!-- MAIN -->
    <main class="main-content">
        <header class="main-header">
            <h1>📊 Ringkasan Usaha</h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="role-badge pedagang"><img src="../assets/6.login-avatar.png" class="logout" alt="user"> Pedagang</span>
                <span id="pedagang-name" style="font-weight:700;font-size:13px;"><?php echo $_SESSION['nama']; ?></span>
            </div>
        </header>

        <div class="main-body">
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info"><h3 id="s-total">0</h3><p>Total Warung</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info"><h3 id="s-aktif">0</h3><p>Warung Aktif</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info" style="display:flex; align-items:center; gap:8px;">
                        <img src="../assets/8-star.png" class="simbol" alt="star" style="width:20px;">
                        <div><h3 id="s-rating">–</h3><p>Rating Rata-rata</p></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info" style="display:flex; align-items:center; gap:8px;">
                        <img src="../assets/5.bookmark.png" class="simbol" alt="bookmark" style="width:16px;">
                        <div><h3 id="s-fav">0</h3><p>Difavoritkan</p></div>
                    </div>
                </div>
            </div>

            <div class="card-panel">
                <h2>🏪 Warung Saya</h2>
                <div class="card-container" id="my-warung-cards"></div>
            </div>

        </div>
    </main>
</div>

<script src="../assets/js/app.js"></script>
<script>
window.addEventListener('load', async () => {
    await renderOverview();
});

async function renderOverview() {
    const list = await getMyWarung();
    document.getElementById('s-total').textContent = list.length;
    document.getElementById('s-aktif').textContent = list.filter(w => w.status === 'aktif').length;
    const avg = list.length ? (list.reduce((s, w) => s + (w.rating || 0), 0) / list.length).toFixed(1) : '–';
    document.getElementById('s-rating').textContent = avg;
    const favorites = await getFavorites();
    const favIds = favorites.map(f => f.id);
    document.getElementById('s-fav').textContent = list.filter(w => favIds.includes(w.id)).length;

    const container = document.getElementById('my-warung-cards');
    if (list.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="big-icon">🏪</div>
                <p>Belum ada warung terdaftar. <a href="tambah_warung.php" style="color:var(--pedagang-color);font-weight:700;">Tambah sekarang →</a></p>
            </div>`;
        return;
    }
    
    container.innerHTML = list.map(w => {
        const imgPath = w.img ? (w.img.startsWith('uploads/') ? '../' + w.img : w.img) : '../assets/2.background.jpg';
        return `
        <div class="card">
            <img src="${imgPath}" alt="${w.nama}" onerror="this.src='../assets/2.background.jpg'">
            <div class="card-body">
                <h3>${w.nama}</h3>
                <p>⭐ ${w.rating || 0} | ${w.lokasi}</p>
                ${w.jam_buka ? `<p style="font-size:11px;color:#aaa;">🕐 ${w.jam_buka} – ${w.jam_tutup}</p>` : ''}
                ${w.wa ? `<p style="font-size:11px;color:#aaa;">📞 ${w.wa}</p>` : ''}
                <p style="font-size:11px;font-weight:600;margin-bottom:8px;color:${w.status === 'tunggu' ? '#e67e22' : (w.status === 'aktif' ? '#27ae60' : '#e74c3c')}">
                    ${w.status === 'tunggu' ? '⏳ Menunggu Persetujuan' : (w.status === 'aktif' ? '✅ Aktif' : '⛔ Nonaktif')}
                </p>
                <div class="card-actions">
                    <button class="btn btn-sm btn-primary" onclick="editWarung(${w.id})">✏️ Edit</button>
                    ${w.status === 'tunggu' 
                        ? `<button class="btn btn-sm btn-outline" disabled style="opacity:0.5;cursor:not-allowed;">⏳ tunggu</button>`
                        : `<button class="btn btn-sm btn-outline" onclick="toggleStatus(${w.id})">${w.status === 'aktif' ? '⛔ Nonaktifkan' : '✅ Aktifkan'}</button>`
                    }
                </div>
            </div>
        </div>`;
    }).join('');
}

function editWarung(id) {
    window.location.href = `tambah_warung.php?edit=${id}`;
}

async function toggleStatus(id) {
    const result = await toggleWarungStatus(id);
    if (result.success) {
        await renderOverview();
        showToast('Status warung berhasil diperbarui');
    } else {
        showToast('❌ ' + result.error);
    }
}
</script>
</body>
</html>
