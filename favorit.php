<?php
require_once 'backend/session_config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorit – CariMakan.ID</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div id="loading"><img src="assets/1.Logo_CariMakan.png" alt="Logo"><p>Memuat…</p></div>

<nav class="navbar">
    <div class="nav-left">
        <img src="assets/1.Logo_CariMakan.png" class="logo" alt="CariMakan">
        <div class="location"> <img src="assets/3.Logo lokasi.png" class="simbol" alt="lokasi"> <span id="lokasi-text">Mendeteksi…</span></div>
    </div>
    <div class="nav-right">
        <a href="index.php">Beranda</a>
        <a href="favorit.php" class="active"> Favorit</a>
        <a href="#" onclick="logoutUser()">
           <img src="assets/6.login-avatar.png" class="simbol" alt="user"> <span id="auth-label">Logout</span>
        </a>
    </div>
</nav>

<section class="container">
    <h2 class="section-title">Daftar <span>Favorit</span> Saya</h2>
    <div class="card-container" id="fav-list"></div>
</section>

<footer>© 2026 <span>CariMakan.ID</span> — Temukan Kelezatan Lokal</footer>

<script src="assets/js/app.js"></script>
<script>
window.addEventListener('load', async () => {
    const s = await checkSession();
    if (s) document.getElementById('auth-label').textContent = 'Logout';
    renderFavorit();
});
async function renderFavorit() {
    const favs = await getFavorites();
    const container = document.getElementById('fav-list');
    if (favs.length === 0) {
        container.innerHTML = '<div class="empty-state"><div class="big-icon">💔</div><p>Belum ada tempat makan di favorit kamu.<br><a href="index.php" style="color:var(--primary);font-weight:700;">Cari sekarang →</a></p></div>';
        return;
    }
    container.innerHTML = favs.map(w => `
        <div class="card">
            <img src="${w.img || 'assets/2.background.jpg'}" alt="${w.nama}" onerror="this.src='assets/2.background.jpg'">
            <div class="card-body">
                <h3>${w.nama}</h3>
                <p>⭐ ${w.rating} | ${w.lokasi}</p>
                <p style="font-size:11.5px;color:#aaa;margin-bottom:6px">${w.kategori} • ${w.harga}</p>
                <div class="card-actions">
                    <button class="btn btn-sm btn-primary" onclick="hapusFavorit(${w.id}, '${w.nama}')">Hapus</button>
                </div>
            </div>
        </div>
    `).join('');
}
async function hapusFavorit(id, nama) {
    await toggleFavorite(id);
    showToast(`💔 ${nama} dihapus dari favorit`);
    renderFavorit();
}
</script>
</body>
</html>
