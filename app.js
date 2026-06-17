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

// ── API BASE URL
const API_BASE = 'api.php';

// ── SESSION & AUTH (via backend)
async function checkSession() {
    const res = await fetch(`${API_BASE}?action=session`);
    const data = await res.json();
    if (data.logged_in) {
        return { id: data.id, nama: data.nama, role: data.role, toko: data.toko };
    }
    return null;
}

async function loginUser(email, password) {
    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    const res = await fetch(`${API_BASE}?action=login`, { method: 'POST', body: formData });
    return res.json();
}

async function registerUser(nama, email, password, role) {
    const formData = new FormData();
    formData.append('nama', nama);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('role', role);
    if (role === 'pedagang') formData.append('toko', nama.toLowerCase().replace(/\s/g, '_'));
    const res = await fetch(`${API_BASE}?action=register`, { method: 'POST', body: formData });
    return res.json();
}

async function logoutUser() {
    await fetch(`${API_BASE}?action=logout`);
    window.location.href = 'login.html';
}

// ── WARUNG
async function getWarung(filters = {}) {
    let url = `${API_BASE}?action=warung`;
    if (filters.q) url += `&q=${encodeURIComponent(filters.q)}`;
    if (filters.harga) url += `&harga=${encodeURIComponent(filters.harga)}`;
    if (filters.lokasi) url += `&lokasi=${encodeURIComponent(filters.lokasi)}`;
    if (filters.kategori) url += `&kategori=${encodeURIComponent(filters.kategori)}`;
    const res = await fetch(url);
    return res.json();
}

async function getAllWarung() {
    const res = await fetch(`${API_BASE}?action=all_warung`);
    return res.json();
}

async function gettungguWarung() {
    const res = await fetch(`${API_BASE}?action=tunggu_warung`);
    return res.json();
}

async function approveWarung(id, approve) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('approve', approve ? '1' : '0');
    const res = await fetch(`${API_BASE}?action=approve_warung`, { method: 'POST', body: formData });
    return res.json();
}

async function getWarungDetail(id) {
    const res = await fetch(`${API_BASE}?action=warung&id=${id}`);
    return res.json();
}

async function saveWarung(data, id = null) {
    const formData = new FormData();
    for (let key in data) formData.append(key, data[key]);
    if (id) formData.append('id', id);
    const res = await fetch(`${API_BASE}?action=warung`, { method: 'POST', body: formData });
    return res.json();
}

async function deleteWarung(id) {
    const res = await fetch(`${API_BASE}?action=warung&id=${id}`, { method: 'DELETE' });
    return res.json();
}

async function toggleWarungStatus(id) {
    const formData = new FormData();
    formData.append('id', id);
    const res = await fetch(`${API_BASE}?action=toggle_status`, { method: 'POST', body: formData });
    return res.json();
}

async function getMyWarung() {
    const res = await fetch(`${API_BASE}?action=my_warung`);
    return res.json();
}

// ── MENU
async function addMenuItem(warungId, nama, harga, gambar) {
    const res = await fetch(`${API_BASE}?action=add_menu`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ warung_id: warungId, nama, harga, gambar })
    });
    return res.json();
}

async function deleteMenuItem(menuId) {
    const res = await fetch(`${API_BASE}?action=delete_menu&id=${menuId}`, { method: 'DELETE' });
    return res.json();
}

// ── REVIEW
async function addReview(warungId, rating, komentar) {
    const res = await fetch(`${API_BASE}?action=add_review`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ warung_id: warungId, rating, komentar })
    });
    return res.json();
}

// ── FAVORITE
async function toggleFavorite(warungId) {
    const formData = new FormData();
    formData.append('warung_id', warungId);
    const res = await fetch(`${API_BASE}?action=favorite`, { method: 'POST', body: formData });
    return res.json();
}

async function getFavorites() {
    const res = await fetch(`${API_BASE}?action=get_favorites`);
    return res.json();
}

// ── ADMIN USERS
async function getAllUsers() {
    const res = await fetch(`${API_BASE}?action=all_users`);
    return res.json();
}

async function addUserByAdmin(nama, email, password, role) {
    const formData = new FormData();
    formData.append('nama', nama);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('role', role);
    const res = await fetch(`${API_BASE}?action=add_user`, { method: 'POST', body: formData });
    return res.json();
}

async function deleteUser(userId) {
    const res = await fetch(`${API_BASE}?action=delete_user&id=${userId}`, { method: 'DELETE' });
    return res.json();
}

// ── UPLOAD GAMBAR
async function uploadImage(file) {
    const formData = new FormData();
    formData.append('file', file);
    const res = await fetch('upload.php', { method: 'POST', body: formData });
    return res.json();
}

// ── GLOBAL INIT
window.addEventListener('load', async () => {
    hideLoading();
    getLocation();
    const session = await checkSession();
    if (session) {
        const userNameSpan = document.getElementById('user-name');
        if (userNameSpan) userNameSpan.textContent = session.nama;
        const authLabel = document.getElementById('auth-label');
        if (authLabel) authLabel.textContent = 'Logout';
        const dashboardLink = document.getElementById('dashboard-link');
        if (dashboardLink && session.role !== 'user') {
            dashboardLink.style.display = 'inline-block';
            if (session.role === 'admin') dashboardLink.href = 'dashboard-admin.html';
            else if (session.role === 'pedagang') dashboardLink.href = 'dashboard-pedagang.html';
        } else if (dashboardLink && session.role === 'user') {
            dashboardLink.style.display = 'none';
        }
    } else {
        const authLabel = document.getElementById('auth-label');
        if (authLabel) authLabel.textContent = 'Login';
    }
});