// ── SHARED UTILITIES ──

function showToast(msg, duration = 2500) {
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), duration);
}

function hideLoading() {
    const el = document.getElementById('loading');
    if (el) setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity 0.5s'; setTimeout(() => el.style.display = 'none', 500); }, 1200);
}

function getLocation() {
    const el = document.getElementById('lokasi-text');
    if (!el) return;
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.coords.latitude}&lon=${pos.coords.longitude}`)
                .then(r => r.json())
                .then(d => {
                    el.textContent = d.address.city || d.address.town || d.address.village || 'Lokasi tidak diketahui';
                }).catch(() => { el.textContent = 'Mataram'; });
        }, () => { el.textContent = 'Mataram'; });
    } else { el.textContent = 'Mataram'; }
}

// ── FAVORITE SYSTEM ──
function getFavorites() {
    return JSON.parse(localStorage.getItem('cm_favorites') || '[]');
}
function saveFavorites(favs) {
    localStorage.setItem('cm_favorites', JSON.stringify(favs));
}
function toggleFavorit(id, name) {
    let favs = getFavorites();
    const idx = favs.findIndex(f => f.id === id);
    if (idx === -1) {
        favs.push({ id, name });
        showToast('❤️ ' + name + ' ditambahkan ke Favorit!');
    } else {
        favs.splice(idx, 1);
        showToast('💔 ' + name + ' dihapus dari Favorit');
    }
    saveFavorites(favs);
    updateFavButtons();
}
function isFavorit(id) {
    return getFavorites().some(f => f.id === id);
}
function updateFavButtons() {
    document.querySelectorAll('[data-fav-id]').forEach(btn => {
        const id = btn.dataset.favId;
        btn.classList.toggle('btn-primary', isFavorit(id));
        btn.classList.toggle('btn-outline', !isFavorit(id));
    });
}

// ── SESSION / AUTH ──
function getSession() {
    return JSON.parse(localStorage.getItem('cm_session') || 'null');
}
function setSession(data) {
    localStorage.setItem('cm_session', JSON.stringify(data));
}
function clearSession() {
    localStorage.removeItem('cm_session');
}
function requireAuth(role) {
    const s = getSession();
    if (!s || s.role !== role) {
        window.location.href = 'login.html';
    }
}
function logout() {
    clearSession();
    showToast('Berhasil logout 👋');
    setTimeout(() => window.location.href = 'login.html', 1000);
}

// ── DATA STORE (localStorage mock DB) ──
const DEMO_WARUNG = [
    { id: 1, nama: 'Mie Ayam Pak Joel', kategori: 'Mie Ayam', lokasi: 'Mataram', harga: 'Murah', rating: 4.5, img: '5.mie_ayam.jpeg', pedagang: 'pak_joel', status: 'aktif', deskripsi: 'Mie ayam lezat dengan topping ayam dan bakso pilihan.' },
    { id: 2, nama: 'Bakso Pak Majid', kategori: 'Bakso', lokasi: 'Cakranegara', harga: 'Murah', rating: 4.6, img: '4.bakso.jpeg', pedagang: 'pak_majid', status: 'aktif', deskripsi: 'Bakso sapi dengan kuah kaldu segar dan mie bihun.' },
    { id: 3, nama: 'Ayam Geprek Gembul', kategori: 'Ayam', lokasi: 'Ampenan', harga: 'Murah', rating: 4.4, img: '1.ayam_geprek.jpeg', pedagang: 'gembul', status: 'aktif', deskripsi: 'Ayam geprek pedas level 1–10 dengan sambal bawang.' },
    { id: 4, nama: 'Sate Narmada', kategori: 'Sate', lokasi: 'Narmada', harga: 'Sedang', rating: 4.7, img: '6.sate.jpeg', pedagang: 'sate_narmada', status: 'aktif', deskripsi: 'Sate daging pilihan dengan bumbu kacang khas Lombok.' },
    { id: 5, nama: 'Ayam Taliwang Asli', kategori: 'Ayam', lokasi: 'Ampenan', harga: 'Sedang', rating: 4.5, img: '2.ayam_taliwang.jpeg', pedagang: 'taliwang', status: 'aktif', deskripsi: 'Ayam Taliwang bumbu merah khas Lombok, bakar arang.' },
];

function getWarung() {
    const stored = localStorage.getItem('cm_warung');
    return stored ? JSON.parse(stored) : DEMO_WARUNG;
}
function saveWarung(data) {
    localStorage.setItem('cm_warung', JSON.stringify(data));
}

// Demo users
const DEMO_USERS = [
    { email: 'user@gmail.com', password: '123', role: 'user', nama: 'Budi Santoso' },
    { email: 'pedagang@gmail.com', password: '123', role: 'pedagang', nama: 'Pak Joel', toko: 'pak_joel' },
    { email: 'admin@gmail.com', password: '123', role: 'admin', nama: 'Admin CariMakan' },
];

window.addEventListener('load', () => {
    hideLoading();
    getLocation();
    if (typeof updateFavButtons === 'function') updateFavButtons();
});

// ── REVIEW SYSTEM (Rating & Ulasan) ──
function getReviews(warungId) {
    const all = JSON.parse(localStorage.getItem('cm_reviews') || '[]');
    return all.filter(r => r.warungId == warungId);
}

function addReview(warungId, rating, komentar) {
    const session = getSession();
    if (!session || session.role !== 'user') return false;
    const reviews = JSON.parse(localStorage.getItem('cm_reviews') || '[]');
    const newReview = {
        id: Date.now(),
        warungId: warungId,
        userId: session.email,
        userName: session.nama,
        rating: rating,
        komentar: komentar.trim(),
        tanggal: new Date().toISOString()
    };
    reviews.push(newReview);
    localStorage.setItem('cm_reviews', JSON.stringify(reviews));
    recalculateWarungRating(warungId);
    return true;
}

function recalculateWarungRating(warungId) {
    const reviews = getReviews(warungId);
    if (reviews.length === 0) return;
    const avg = reviews.reduce((sum, r) => sum + r.rating, 0) / reviews.length;
    let warungList = getWarung();
    warungList = warungList.map(w => {
        if (w.id == warungId) {
            return { ...w, rating: parseFloat(avg.toFixed(1)) };
        }
        return w;
    });
    saveWarung(warungList);
}