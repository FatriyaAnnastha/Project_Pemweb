<?php
require_once '../backend/session_config.php';
requireLogin();
if (!isPedagang()) {
    header("Location: ../login.php");
    exit;
}
$activePage = 'warung';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Warung Saya – CariMakan.ID</title>
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
            <h1>🏪 Kelola Warung Makan</h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="role-badge pedagang"><img src="../assets/6.login-avatar.png" class="logout" alt="user"> Pedagang</span>
                <span id="pedagang-name" style="font-weight:700;font-size:13px;"><?php echo $_SESSION['nama']; ?></span>
            </div>
        </header>

        <div class="main-body">
            
            <div class="card-panel">
                <h2>Manajemen Status & Operasional Warung</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Warung</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Kisaran Harga</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="warung-table-body"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="../assets/js/app.js"></script>
<script>
window.addEventListener('load', async () => {
    await renderWarungTable();
});

async function renderWarungTable() {
    const list = await getMyWarung();
    const body = document.getElementById('warung-table-body');
    body.innerHTML = list.map(w => `
        <tr>
            <td><strong>${w.nama}</strong></td>
            <td>${w.kategori}</td>
            <td>${w.lokasi}</td>
            <td>${w.harga}</td>
            <td>⭐ ${w.rating || 0}</td>
            <td>
                <span class="badge ${w.status === 'tunggu' ? 'badge-yellow' : (w.status === 'aktif' ? 'badge-green' : 'badge-red')}">
                    ${w.status}
                </span>
            </td>
            <td style="display:flex;gap:6px;">
                <button class="btn btn-sm btn-warning" onclick="editWarung(${w.id})">✏️</button>
                ${w.status === 'tunggu'
                    ? `<button class="btn btn-sm btn-outline" disabled style="opacity:0.5;cursor:not-allowed;">⏳</button>`
                    : `<button class="btn btn-sm btn-outline" onclick="toggleStatus(${w.id})">${w.status === 'aktif' ? '⛔' : '✅'}</button>`
                }
                <button class="btn btn-sm" style="background:#fdecea;color:#e74c3c;" onclick="hapusWarung(${w.id})">🗑️</button>
            </td>
        </tr>
    `).join('') || '<tr><td colspan="7" style="text-align:center;">Belum ada warung terdaftar</td></tr>';
}

function editWarung(id) {
    window.location.href = `tambah_warung.php?edit=${id}`;
}

async function toggleStatus(id) {
    const result = await toggleWarungStatus(id);
    if (result.success) {
        await renderWarungTable();
        showToast('Status warung diperbarui!');
    } else {
        showToast('❌ ' + result.error);
    }
}

async function hapusWarung(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus warung ini beserta seluruh menu makanan di dalamnya?')) return;
    await deleteWarung(id);
    await renderWarungTable();
    showToast('🗑️ Warung berhasil dihapus!');
}
</script>
</body>
</html>
