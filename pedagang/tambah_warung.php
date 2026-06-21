<?php
require_once '../backend/session_config.php';
requireLogin();
if (!isPedagang()) {
    header("Location: ../login.php");
    exit;
}
$activePage = 'tambah';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Warung – CariMakan.ID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body class="pedagang-theme">
<div id="loading"><img src="../assets/1.Logo_CariMakan.png" alt="Logo"><p>Memuat Dashboard…</p></div>

<div class="dashboard-layout">
    
    <!-- SIDEBAR -->
    <?php include 'sidebar.php'; ?>

    <!-- MAIN -->
    <main class="main-content">
        <header class="main-header">
            <h1 id="page-title"> Tambah Warung Baru</h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="role-badge pedagang"><img src="../assets/6.login-avatar.png" class="logout" alt="user"> Pedagang</span>
                <span id="pedagang-name" style="font-weight:700;font-size:13px;"><?php echo $_SESSION['nama']; ?></span>
            </div>
        </header>

        <div class="main-body">
            
            <div class="card-panel" style="max-width:700px;">
                <h2 id="form-title">➕ Tambah Warung</h2>
                <input type="hidden" id="edit-id">

                <!-- ── INFO DASAR ── -->
                <div style="background:#f8f8f8;border-radius:10px;padding:16px;margin-bottom:16px;">
                    <h3 style="margin:0 0 12px;font-size:14px;">📋 Informasi Dasar</h3>
                    <div class="form-group">
                        <label>Nama Warung *</label>
                        <input type="text" id="f-nama" placeholder="Contoh: Bakso Pak Budi">
                    </div>
                    <div class="form-group">
                        <label>Kategori *</label>
                        <select id="f-kategori">
                            <option>Bakso</option>
                            <option>Mie Ayam</option>
                            <option>Ayam</option>
                            <option>Sate</option>
                            <option>Seafood</option>
                            <option>Nasi</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Lokasi *</label>
                        <select id="f-lokasi">
                            <option>Mataram</option>
                            <option>Cakranegara</option>
                            <option>Ampenan</option>
                            <option>Narmada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kisaran Harga</label>
                        <select id="f-harga">
                            <option>Murah</option>
                            <option>Sedang</option>
                            <option>Mahal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea id="f-deskripsi" placeholder="Ceritakan tentang warung makan Anda…"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Foto Warung</label>
                        <input type="file" id="f-gambar-warung" accept="image/*">
                        <img id="preview-warung"
                            style="display:none;
                            width:100%;
                            max-height:220px;
                            object-fit:cover;
                            border-radius:10px;
                            margin-top:10px;">
                    </div>
                </div>

                <!-- ── KONTAK & JAM OPERASIONAL ── -->
                <div style="background:#f8f8f8;border-radius:10px;padding:16px;margin-bottom:16px;">
                    <h3 style="margin:0 0 12px;font-size:14px;">📞 Kontak & Jam Operasional</h3>
                    <div class="form-group">
                        <label>Nomor WhatsApp (Contoh: 08123456789)</label>
                        <input type="tel" id="f-wa" placeholder="Contoh: 08123456789">
                    </div>
                    <div class="form-group">
                        <label>Hari Buka *</label>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px;" id="hari-checkboxes"></div>
                    </div>
                    <div style="display:flex;gap:12px;">
                        <div class="form-group" style="flex:1;">
                            <label>Jam Buka *</label>
                            <input type="time" id="f-jam-buka" value="08:00">
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Jam Tutup *</label>
                            <input type="time" id="f-jam-tutup" value="21:00">
                        </div>
                    </div>
                </div>

                <!-- ── MENU ── -->
                <div style="background:#f8f8f8;border-radius:10px;padding:16px;margin-bottom:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                        <h3 style="margin:0;font-size:14px;">🍽️ Daftar Menu Makanan</h3>
                        <button class="btn btn-sm btn-primary" onclick="tambahMenu()">+ Tambah Menu</button>
                    </div>
                    <div id="menu-list" style="display:flex;flex-direction:column;gap:8px;"></div>
                    <p id="menu-kosong" style="font-size:12px;color:#aaa;text-align:center;padding:12px 0;">
                        Belum ada menu. Klik "+ Tambah Menu" untuk menambahkan.
                    </p>
                </div>

                <!-- ── LOKASI PETA ── -->
                <div style="background:#f8f8f8;border-radius:10px;padding:16px;margin-bottom:16px;">
                    <h3 style="margin:0 0 8px;font-size:14px;">📍 Lokasi di Peta</h3>
                    <p style="font-size:12px;color:#aaa;margin-bottom:8px;">Klik pada peta di bawah ini untuk menentukan titik koordinat warung.</p>
                    <div id="map" style="height:280px;width:100%;border-radius:10px;margin-bottom:10px;z-index:0;"></div>
                    <input type="hidden" id="latitude">
                    <input type="hidden" id="longitude">
                    <div id="koordinat-info" style="font-size:12px;color:#aaa;display:none;">
                        📌 Koordinat Terpilih: <span id="koordinat-text"></span>
                    </div>
                </div>

                <div style="display:flex;gap:10px;">
                    <button class="btn btn-primary" onclick="simpanWarung()">💾 Simpan Data</button>
                    <button class="btn btn-outline" onclick="resetDanRedirect()">✖ Batal</button>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="../assets/js/app.js"></script>
<script>
const HARI_LIST = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
let leafletMap = null, leafletMarker = null, mapInitialized = false;
let menuItems = []; // [{id, nama, harga, gambar}]
let gambarWarung = '';

function getPreviewUrl(path) {
    if (!path) return '';
    if (path.startsWith('uploads/')) return '../' + path;
    return path;
}

function initLeafletMap(lat = -8.5831, lng = 116.1165, zoom = 13) {
    if (mapInitialized) { 
        leafletMap.setView([lat, lng], zoom);
        leafletMap.invalidateSize(); 
        return; 
    }
    leafletMap = L.map('map').setView([lat, lng], zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(leafletMap);
    
    leafletMap.on('click', function(e) {
        const latitude = e.latlng.lat.toFixed(6);
        const longitude = e.latlng.lng.toFixed(6);
        document.getElementById('latitude').value = latitude;
        document.getElementById('longitude').value = longitude;
        document.getElementById('koordinat-info').style.display = 'block';
        document.getElementById('koordinat-text').textContent = latitude + ', ' + longitude;
        if (leafletMarker) leafletMarker.setLatLng([latitude, longitude]);
        else leafletMarker = L.marker([latitude, longitude]).addTo(leafletMap);
        leafletMarker.bindPopup('📍 Lokasi warung terpilih').openPopup();
    });
    mapInitialized = true;
}

function buatHariCheckbox() {
    const wrap = document.getElementById('hari-checkboxes');
    wrap.innerHTML = HARI_LIST.map(h => `
        <label style="display:flex;align-items:center;gap:4px;cursor:pointer;background:#fff;border:1px solid #ddd;border-radius:6px;padding:4px 10px;font-size:13px;">
            <input type="checkbox" class="hari-cb" value="${h}" style="cursor:pointer;"> ${h}
        </label>
    `).join('');
}

function getHariDipilih() {
    return [...document.querySelectorAll('.hari-cb:checked')].map(cb => cb.value);
}

function setHariDipilih(hariArr) {
    document.querySelectorAll('.hari-cb').forEach(cb => {
        cb.checked = hariArr && hariArr.includes(cb.value);
    });
}

function tambahMenu() {
    menuItems.push({ nama: '', harga: '', gambar: '' });
    renderMenuList();
}

async function hapusMenu(idx) {
    const m = menuItems[idx];
    if (m.id) { 
        await deleteMenuItem(m.id);
    }
    menuItems.splice(idx, 1);
    renderMenuList();
}

async function uploadMenuImage(event, index) {
    const file = event.target.files[0];
    if (!file) return;
    const result = await uploadImage(file);
    if (result.success) {
        menuItems[index].gambar = result.path;
        renderMenuList();
    } else {
        showToast('Gagal upload gambar menu');
    }
}

function renderMenuList() {
    const wrap = document.getElementById('menu-list');
    const kosong = document.getElementById('menu-kosong');
    kosong.style.display = menuItems.length === 0 ? 'block' : 'none';
    wrap.innerHTML = menuItems.map((m, i) => `
        <div style="border:1px solid #ddd;border-radius:10px;padding:10px;display:flex;flex-direction:column;gap:8px;background:white;">
            <input type="text" placeholder="Nama Menu Makanan (Wajib)" value="${m.nama}" oninput="menuItems[${i}].nama=this.value">
            <input type="number" placeholder="Harga (Rupiah)" value="${m.harga}" oninput="menuItems[${i}].harga=this.value">
            <input type="file" accept="image/*" onchange="uploadMenuImage(event,${i})">
            ${m.gambar ? `<img src="${getPreviewUrl(m.gambar)}" style="width:100px;height:100px;object-fit:cover;border-radius:8px;">` : ''}
            <button onclick="hapusMenu(${i})" style="background:#e74c3c;color:white;border:none;padding:8px;border-radius:8px;cursor:pointer;font-weight:700;">🗑️ Hapus Menu</button>
        </div>
    `).join('');
}

window.addEventListener('load', async () => {
    buatHariCheckbox();
    
    // Periksa apakah sedang dalam mode EDIT
    const urlParams = new URLSearchParams(window.location.search);
    const editId = urlParams.get('edit');
    
    if (editId) {
        document.getElementById('page-title').textContent = '✏️ Edit Warung Makan';
        document.getElementById('form-title').textContent = '✏️ Edit Informasi Warung';
        document.getElementById('edit-id').value = editId;
        await loadWarungData(editId);
    } else {
        document.getElementById('page-title').textContent = '➕ Tambah Warung Baru';
        document.getElementById('form-title').textContent = '➕ Tambah Warung';
        initLeafletMap();
    }
    
    // Setup upload gambar warung
    const upload = document.getElementById('f-gambar-warung');
    if (upload) {
        upload.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = async function(e) {
                const result = await uploadImage(file);
                if (result.success) {
                    gambarWarung = result.path;
                    const preview = document.getElementById('preview-warung');
                    preview.src = getPreviewUrl(gambarWarung);
                    preview.style.display = 'block';
                } else {
                    showToast('Gagal upload gambar warung');
                }
            };
            reader.readAsDataURL(file);
        });
    }
});

async function loadWarungData(id) {
    const w = await getWarungDetail(id);
    if (!w || w.error) { 
        showToast('Gagal memuat data warung'); 
        setTimeout(() => window.location.href = 'kelola_warung.php', 1500);
        return; 
    }
    
    document.getElementById('f-nama').value = w.nama;
    document.getElementById('f-kategori').value = w.kategori;
    document.getElementById('f-lokasi').value = w.lokasi;
    document.getElementById('f-harga').value = w.harga;
    document.getElementById('f-deskripsi').value = w.deskripsi || '';
    document.getElementById('f-wa').value = w.wa || '';
    document.getElementById('f-jam-buka').value = w.jam_buka || '08:00';
    document.getElementById('f-jam-tutup').value = w.jam_tutup || '21:00';
    setHariDipilih(w.hari_kerja ? w.hari_kerja.split(',') : []);
    
    menuItems = (w.menu || []).map(m => ({ ...m }));
    renderMenuList();
    
    gambarWarung = w.img || '';
    if (gambarWarung) {
        const preview = document.getElementById('preview-warung');
        preview.src = getPreviewUrl(gambarWarung);
        preview.style.display = 'block';
    }
    
    if (w.lat && w.lng) {
        const latVal = parseFloat(w.lat);
        const lngVal = parseFloat(w.lng);
        document.getElementById('latitude').value = latVal;
        document.getElementById('longitude').value = lngVal;
        document.getElementById('koordinat-info').style.display = 'block';
        document.getElementById('koordinat-text').textContent = latVal + ', ' + lngVal;
        
        initLeafletMap(latVal, lngVal, 15);
        leafletMarker = L.marker([latVal, lngVal]).addTo(leafletMap);
        leafletMarker.bindPopup('📍 Lokasi warung saat ini').openPopup();
    } else {
        initLeafletMap();
    }
}

async function simpanWarung() {
    const namaWarung = document.getElementById('f-nama').value.trim();
    const kategori = document.getElementById('f-kategori').value;
    const lokasi = document.getElementById('f-lokasi').value;
    const harga = document.getElementById('f-harga').value;
    const deskripsi = document.getElementById('f-deskripsi').value.trim();
    const wa = document.getElementById('f-wa').value.trim();
    const jamBuka = document.getElementById('f-jam-buka').value;
    const jamTutup = document.getElementById('f-jam-tutup').value;
    const hariKerja = getHariDipilih();
    const latitude = document.getElementById('latitude').value;
    const longitude = document.getElementById('longitude').value;
    const editId = document.getElementById('edit-id').value;

    if (!namaWarung) { alert('Nama warung wajib diisi!'); return; }
    if (hariKerja.length === 0) { alert('Pilih minimal satu hari buka!'); return; }
    if (!jamBuka || !jamTutup) { alert('Jam buka dan jam tutup wajib diisi!'); return; }
    if (!latitude || !longitude) { alert('Pilih koordinat lokasi warung di peta!'); return; }

    const menuValid = menuItems.filter(m => m.nama.trim() !== '');

    const dataWarung = {
        nama: namaWarung, kategori, lokasi, harga, deskripsi, wa,
        jam_buka: jamBuka, jam_tutup: jamTutup, hari_kerja: hariKerja.join(','),
        lat: parseFloat(latitude), lng: parseFloat(longitude), img: gambarWarung
    };

    const result = await saveWarung(dataWarung, editId || null);

    if (result.success) {
        const warungId = editId || result.id;

        // Simpan Menu Baru
        for (const m of menuValid) {
            if (!m.id) { 
                await addMenuItem(warungId, m.nama, parseFloat(m.harga) || 0, m.gambar || '');
            }
        }

        showToast(editId ? '✅ Warung berhasil diperbarui!' : '✅ Pengajuan warung baru dikirim!');
        setTimeout(() => window.location.href = 'kelola_warung.php', 1200);
    } else {
        showToast('❌ Gagal menyimpan data warung');
    }
}

function resetDanRedirect() {
    window.location.href = 'kelola_warung.php';
}
</script>
</body>
</html>
