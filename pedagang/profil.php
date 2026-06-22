<?php
require_once '../backend/session_config.php';
requireLogin();
if (!isPedagang()) {
    header("Location: ../login.php");
    exit;
}
$activePage = 'profil';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya – CariMakan.ID</title>
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
            <h1>👤 Profil Saya</h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="role-badge pedagang"><img src="../assets/6.login-avatar.png" class="logout" alt="user"> Pedagang</span>
                <span id="pedagang-name" style="font-weight:700;font-size:13px;"><?php echo $_SESSION['nama']; ?></span>
            </div>
        </header>

        <div class="main-body">
            
            <div class="card-panel" style="max-width:440px;">
                <h2>Informasi Profil Akun</h2>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" value="<?php echo $_SESSION['nama']; ?>" readonly style="background:#f4f4f4; color:#777; cursor:not-allowed;">
                </div>
                <div class="form-group">
                    <label>Email Terdaftar</label>
                    <input type="email" value="<?php echo $_SESSION['email'] ?? 'pedagang@gmail.com'; ?>" readonly style="background:#f4f4f4; color:#777; cursor:not-allowed;">
                </div>
                <div class="form-group">
                    <label>Hak Akses Platform</label>
                    <input type="text" value="Pedagang / Mitra Kuliner" readonly style="background:#f4f4f4; color:#777; cursor:not-allowed;">
                </div>
                <p style="font-size:12px; color:var(--text-muted); margin-top:8px; line-height:1.5;">
                    💡 Informasi profil akun di atas disinkronkan dengan database pusat.
                </p>
            </div>

        </div>
    </main>
</div>

<script src="../assets/js/app.js"></script>
</body>
</html>
