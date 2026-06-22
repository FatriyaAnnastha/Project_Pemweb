<?php
require_once 'backend/session_config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda – CariMakan.ID</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div id="loading">
    <img src="assets/1.Logo_CariMakan.png" alt="Logo">
    <p>Memuat…</p>
</div>

<!-- MODAL DETAIL WARUNG -->
<div class="modal-overlay" id="modal-detail">
    <div class="modal-box">
        <button class="modal-close" onclick="tutupModal()">✕</button>
        <div id="modal-content"></div>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar">

    <div class="nav-left">
        <img src="assets/1.Logo_CariMakan.png" class="logo" alt="CariMakan">

        <div class="location">
            <img src="assets/3.Logo lokasi.png" class="simbol" alt="lokasi">
            <span id="lokasi-text">Mendeteksi…</span>
        </div>
    </div>

    <div class="nav-right">
        <a href="index.php" class="active">Beranda</a>
        <a href="#" onclick="bukaFavorit(event)">Favorit</a>

        <a href="#" id="dashboard-link" style="display:none;">
            Dashboard
        </a>

        <span class="role-badge user" id="user-name">
            Tamu
        </span>

        <a href="login.php" onclick="handleAuth()">
            <img src="assets/6.login-avatar.png" class="simbol" alt="user"> <span id="auth-label">Login</span>
        </a>
    </div>

</nav>

<!-- HERO -->
<section class="hero">

    <h1>Temukan Tempat Makan di Sekitarmu!</h1>
    <p> Cari berdasarkan lokasi, harga, dan kategori favorit kamu</p>

    <div class="search-box">

        
        <select id="filter-harga" onchange="renderWarung()">
            <option value="">Semua Harga</option>
            <option>Murah</option>
            <option>Sedang</option>
            <option>Mahal</option>
        </select>
        
        <select id="filter-lokasi" onchange="renderWarung()">
            <option value="">Semua Lokasi</option>
            <option>Mataram</option>
            <option>Cakranegara</option>
            <option>Ampenan</option>
            <option>Narmada</option>
        </select>
        
        <select id="filter-kategori" onchange="renderWarung()">
            <option value="">Semua Kategori</option>
            <option>Bakso</option>
            <option>Mie Ayam</option>
            <option>Ayam</option>
            <option>Sate</option>
            <option>Seafood</option>
            <option>Nasi</option>
            <option>Lainnya</option>
        </select>
        
        <input
            type="text"
            id="q"
            placeholder="Cari langsung..."
            onkeyup="renderWarung()"
        >

        <button onclick="renderWarung()">
           <img src="assets/4.Logo search.png" class="cari" alt="search">
        </button>

    </div>

</section>

<!-- CONTENT -->
<section class="container">

    <h2 class="section-title">
        <img src="assets/17-utensils.png" class="cari" alt="search"> Rekomendasi <span>Tempat Makan</span>
    </h2>

    <div
        id="last-updated"
        style="font-size:11px;color:#aaa;margin-bottom:12px;display:none;"
    >
        🔄 Data diperbarui:
        <span id="updated-time"></span>
    </div>

    <div class="card-container" id="warung-list"></div>

</section>

<!-- FOOTER -->
<footer>
    © 2026 <span>CariMakan.ID</span>
    — Temukan Kelezatan Lokal
</footer>

<!-- JS EKSTERNAL -->
<script src="assets/js/app.js"></script>
<script>
window.addEventListener('load', async () => {
    const session = await checkSession();
    if (session) {
        document.getElementById('user-name').textContent = session.nama;
        document.getElementById('auth-label').textContent = 'Logout';
        const dashboardLink = document.getElementById('dashboard-link');
        if (session.role !== 'user') {
            dashboardLink.style.display = 'inline-block';
            dashboardLink.href = session.role === 'admin' ? 'admin/index.php' : 'pedagang/index.php';
        } else {
            dashboardLink.style.display = 'none';
        }
    }
    renderWarung();
    detectLokasi();
});

window.addEventListener('storage', (e) => {});

function tampilkanWaktuUpdate() {
    const jam = new Date().toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
    document.getElementById('last-updated').style.display = 'block';
    document.getElementById('updated-time').textContent = jam;
}

function detectLokasi() {
    if (!navigator.geolocation) {
        document.getElementById('lokasi-text').textContent = 'Lokasi tidak tersedia';
        return;
    }
    navigator.geolocation.getCurrentPosition(
        () => { document.getElementById('lokasi-text').textContent = 'Lokasi ditemukan'; },
        () => { document.getElementById('lokasi-text').textContent = 'Mataram, NTB'; }
    );
}

function handleAuth() {
    event.preventDefault();
    (async () => {
        const session = await checkSession();
        if (session) logoutUser();
        else window.location.href = 'login.php';
    })();
}

function cekStatusBuka(w) {
    if (!w.jam_buka || !w.jam_tutup || !w.hari_kerja) return null;
    const hariMap = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    const now = new Date();
    const hari = hariMap[now.getDay()];
    const hm = now.getHours()*60 + now.getMinutes();
    const [bh,bm] = w.jam_buka.split(':').map(Number);
    const [th,tm] = w.jam_tutup.split(':').map(Number);
    const buka = bh*60+bm, tutup = th*60+tm;
    const hariKerjaArr = w.hari_kerja.split(',');
    return hariKerjaArr.includes(hari) && hm >= buka && hm <= tutup;
}

async function renderWarung() {
    const q = document.getElementById('q').value.toLowerCase().trim();
    const harga = document.getElementById('filter-harga').value;
    const lokasi = document.getElementById('filter-lokasi').value;
    const kat = document.getElementById('filter-kategori').value;
    let list = await getWarung({ q, harga, lokasi, kategori: kat });
    const container = document.getElementById('warung-list');
    if (list.length === 0) {
        container.innerHTML = `<div class="empty-state"><div class="big-icon"><img src="assets/17-utensils.png" class="cari" alt="search"></div><p>Tidak ada tempat makan yang ditemukan.</p></div>`;
        return;
    }
    const session = await checkSession();
    const favorites = session ? await getFavorites() : [];
    const favIds = favorites.map(f => f.id);
    container.innerHTML = list.map(w => {
        const lokasiUrl = (w.lat && w.lng) ? `https://www.google.com/maps?q=${w.lat},${w.lng}` : `https://www.google.com/maps/search/${encodeURIComponent(w.nama+' '+w.lokasi)}`;
        const sudahFavorit = favIds.includes(w.id);
        const statusBuka = cekStatusBuka(w);
        let chipStatus = '';
        if (statusBuka === true) chipStatus = `<span class="info-chip chip-buka">Buka</span>`;
        else if (statusBuka === false) chipStatus = `<span class="info-chip chip-tutup">Tutup</span>`;
        let chipJam = (w.jam_buka && w.jam_tutup) ? `<span class="info-chip"><img src="assets/14-clock.png" style="width:14px;height:auto;vertical-align:middle;"alt="jam"> ${w.jam_buka}–${w.jam_tutup}</span>` : '';
        let chipHari = (w.hari_kerja) ? `<span class="info-chip"><img src="assets/15-calendar.png" style="width:14px;height:auto;vertical-align:middle;"alt="kalender"> ${w.hari_kerja}</span>` : '';
        let chipWA = (w.wa) ? `<a href="https://wa.me/62${w.wa.replace(/^0/,'')}" target="_blank" class="info-chip chip-wa"><img src="assets/23-whatsapp.png" style="width:14px;height:auto;vertical-align:middle;"alt="wa"> WhatsApp</a>` : '';
        let previewMenu = '';
        if (w.menu && w.menu.length > 0) {
            const tampil = w.menu.slice(0, 2).map(m => `
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <img src="${m.gambar || 'assets/2.background.jpg'}" alt="${m.nama}"
                        onerror="this.src='assets/2.background.jpg'"
                        style="width:36px;height:36px;object-fit:cover;border-radius:6px;flex-shrink:0;">
                    <span style="font-size:11px;color:#555;">${m.nama}${m.harga ? ' – Rp'+Number(m.harga).toLocaleString('id-ID') : ''}</span>
                </div>`).join('');
            const sisanya = w.menu.length > 2 ? `<span style="font-size:11px;color:#aaa;margin-left:44px;">+${w.menu.length-2} menu lainnya</span>` : '';
            previewMenu = `<div style="margin:4px 0 8px;">${tampil}${sisanya}</div>`;
        }
        return `<div class="card" id="card-${w.id}">
            <img src="${w.img || 'assets/2.background.jpg'}" alt="${w.nama}" onerror="this.src='assets/2.background.jpg'">
            <div class="card-body">
                <h3>${w.nama}</h3>
                <p>⭐ ${w.rating || 0} | ${w.lokasi}</p>
                <p style="font-size:11.5px;color:#aaa;margin-bottom:4px;">${w.kategori} • ${w.harga}</p>
                <div class="info-strip">${chipStatus}${chipJam}${chipHari}${chipWA}</div>
                ${previewMenu}
                <div class="card-actions">
                    <button class="btn btn-sm btn-outline" onclick="bukaDetail(${w.id})"> Detail</button>
                    <button class="btn btn-sm ${sudahFavorit ? 'btn-primary' : 'btn-outline'}" id="fav-btn-${w.id}" onclick="toggleFavoritDanUpdate(${w.id}, '${w.nama}')"> ${sudahFavorit ? 'Difavoritkan' : 'Favorit'}</button>
                    <a href="${lokasiUrl}" target="_blank" class="btn btn-sm btn-primary" style="text-decoration:none;display:flex;align-items:center;justify-content:center;gap:5px;"> Lokasi</a>
                </div>
            </div>
        </div>`;
    }).join('');
}

function renderStars(rating) {
    let full = Math.floor(rating);
    let half = rating % 1 >= 0.5 ? 1 : 0;
    let starHtml = '';
    for (let i = 0; i < full; i++) starHtml += '⭐';
    if (half) starHtml += '½⭐';
    for (let i = 0; i < 5 - full - half; i++) starHtml += '☆';
    return starHtml;
}

async function submitReview(warungId) {
    const rating = parseInt(document.getElementById('review-rating').value);
    const komentar = document.getElementById('review-komentar').value;
    if (!rating || rating < 1 || rating > 5) { showToast('Pilih rating 1–5 bintang!'); return; }
    if (!komentar.trim()) { showToast('Komentar tidak boleh kosong!'); return; }
    const result = await addReview(warungId, rating, komentar);
    if (result.success) {
        showToast('✅ Ulasan berhasil ditambahkan!');
        tutupModal();
        setTimeout(() => bukaDetail(warungId), 500);
    } else {
        showToast('❌ Gagal menambahkan ulasan. Pastikan Anda login sebagai pengguna biasa.');
    }
}

async function bukaDetail(id) {
    const w = await getWarungDetail(id);
    if (!w || w.error) { showToast('Gagal memuat detail'); return; }
    const statusBuka = cekStatusBuka(w);
    const statusTeks = statusBuka === true ? '<span class="status-buka">Sedang Buka</span>' : (statusBuka === false ? '<span class="status-tutup">Sedang Tutup</span>' : '');
    const lokasiUrl = (w.lat && w.lng) ? `https://www.google.com/maps?q=${w.lat},${w.lng}` : `https://www.google.com/maps/search/${encodeURIComponent(w.nama+' '+w.lokasi)}`;
    const menuHtml = (w.menu && w.menu.length > 0) ? `
        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:8px;">
            ${w.menu.map(m => `
                <div style="display:flex;align-items:center;gap:10px;background:#f9f7f5;border-radius:8px;padding:6px 8px;">
                    <img src="${m.gambar || 'assets/2.background.jpg'}" alt="${m.nama}"
                        onerror="this.src='assets/2.background.jpg'"
                        style="width:52px;height:52px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                    <div>
                        <div style="font-size:13px;font-weight:600;">${m.nama}</div>
                        <div style="font-size:12px;color:#888;">${m.harga ? 'Rp '+Number(m.harga).toLocaleString('id-ID') : '–'}</div>
                    </div>
                </div>`).join('')}
        </div>
    ` : '<p style="color:#aaa;font-size:13px;">Belum ada menu tersedia.</p>';
    const hariHtml = w.hari_kerja || '–';
    const waHtml = w.wa ? `<a href="https://wa.me/62${w.wa.replace(/^0/,'')}" target="_blank" style="color:#1565c0;">(${w.wa})</a>` : '–';
    const reviews = w.reviews || [];
    const avgRating = w.rating || 0;
    const reviewListHtml = reviews.length ? reviews.map(rv => `
        <div style="border-bottom:1px solid #eee; padding:8px 0;">
            <div style="display:flex; justify-content:space-between;"><strong>${rv.user_name}</strong><span>${renderStars(rv.rating)}</span></div>
            <p style="font-size:12px; margin:4px 0;">${rv.komentar}</p>
            <span style="font-size:10px; color:#aaa;">${new Date(rv.tanggal).toLocaleDateString('id-ID')}</span>
        </div>
    `).join('') : '<p style="color:#aaa; font-size:12px;">Belum ada ulasan. Jadilah yang pertama!</p>';
    const session = await checkSession();
    const isUser = session && session.role === 'user';
    const formReview = isUser ? `
        <hr style="margin:12px 0;"><h4>Tambah Ulasan</h4>
        <div style="margin-bottom:8px;"><label>Rating: </label>
        <select id="review-rating" style="padding:6px;border-radius:8px;">
            <option value="">-- Pilih --</option>
            <option value="1">⭐ 1</option><option value="2">⭐⭐ 2</option>
            <option value="3">⭐⭐⭐ 3</option><option value="4">⭐⭐⭐⭐ 4</option>
            <option value="5">⭐⭐⭐⭐⭐ 5</option>
        </select></div>
        <textarea id="review-komentar" rows="2" placeholder="Tulis komentar Anda..." style="width:100%; border-radius:8px; padding:8px;"></textarea>
        <button class="btn btn-primary btn-sm" style="margin-top:8px;" onclick="submitReview(${id})">Kirim Ulasan</button>
    ` : (session ? '<p style="font-size:12px; color:#aaa;">⭐ Ulasan hanya untuk pengguna biasa.</p>' : '<p style="font-size:12px; color:#aaa;">🔒 Login sebagai pengguna untuk memberi ulasan.</p>');
    document.getElementById('modal-content').innerHTML = `
        <img src="${w.img || 'assets/2.background.jpg'}" alt="${w.nama}" onerror="this.src='assets/2.background.jpg'" style="width:100%;height:160px;object-fit:cover;border-radius:10px;margin-bottom:14px;">
        <h2 style="margin:0 0 4px;">${w.nama}</h2>
        <p style="color:#888;font-size:13px;margin:0 0 10px;">${w.kategori} • ${w.harga} • ${w.lokasi}</p>
        ${statusTeks ? `<p style="margin:0 0 10px;">${statusTeks}</p>` : ''}
        <p style="margin:0 0 6px;"><strong><img src="assets/8-star-1.png" class="info" alt="rating"> Rating:</strong> ${avgRating} ${renderStars(avgRating)}</p>
        ${w.deskripsi ? `<p style="font-size:13px;color:#555;margin:0 0 12px;">${w.deskripsi}</p>` : ''}
        <div style="display:flex;flex-direction:column;gap:6px;font-size:13px;margin-bottom:14px;">
            <div><img src="assets/14-clock.png" class="info" alt="clock"> <strong>Jam Buka:</strong> ${w.jam_buka || '–'} – ${w.jam_tutup || '–'}</div>
            <div><img src="assets/15-calendar.png" class="info" alt="calendar"> <strong>Hari Buka:</strong> ${hariHtml}</div>
            <div><img src="assets/13-contact-us.png" class="info" alt="contact"> <strong>Kontak:</strong> ${waHtml}</div>
        </div>
        <h4 style="margin:0 0 6px;"><img src="assets/17-utensils.png" class="info" alt="carimakan"> Menu</h4>${menuHtml}
        <hr style="margin:12px 0;"><h4><img src="assets/16-review.png" class="info" alt="review"> Ulasan (${reviews.length})</h4>
        <div style="max-height:200px; overflow-y:auto; margin-bottom:12px;">${reviewListHtml}</div>
        ${formReview}
        <div style="display:flex;gap:10px;margin-top:16px;">
            <a href="${lokasiUrl}" target="_blank" class="btn btn-primary" style="text-decoration:none;flex:1;text-align:center;"><img src="assets/9-pointer-on-map.png" class="info" alt="lokasi"> Lihat di Maps</a>
            ${w.wa ? `<a href="https://wa.me/62${w.wa.replace(/^0/,'')}" target="_blank" class="btn btn-outline" style="text-decoration:none;flex:1;text-align:center;"><img src="assets/23-whatsapp.png" class="info" alt="chat">  WhatsApp</a>` : ''}
        </div>
    `;
    document.getElementById('modal-detail').classList.add('open');
}

function tutupModal() {
    document.getElementById('modal-detail').classList.remove('open');
}
document.getElementById('modal-detail').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});

async function toggleFavoritDanUpdate(id, nama) {
    const result = await toggleFavorite(id);

    if (!result.success) {
        showToast('❌ Silakan login terlebih dahulu');
        return;
    }
    const sudahFavorit = result.action === 'added';
    const btn = document.getElementById('fav-btn-' + id);

    if (btn) {
        btn.className = `btn btn-sm ${
            sudahFavorit ? 'btn-primary' : 'btn-outline'
        }`;

        btn.innerHTML = sudahFavorit
            ? 'Difavoritkan'
            : 'Favorit';
    }
    showToast(
        sudahFavorit
            ? `❤️ ${nama} ditambahkan ke favorit`
            : `💔 ${nama} dihapus dari favorit`
    );
}
async function bukaFavorit(e) {
    e.preventDefault();

    const session = await checkSession();

    if (!session) {
        showToast('🔒 Silakan login terlebih dahulu');
        return;
    }

    window.location.href = 'favorit.php';
}
</script>

</body>
</html>
