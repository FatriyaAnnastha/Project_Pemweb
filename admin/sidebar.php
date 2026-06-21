<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="../assets/1.Logo_CariMakan.png" alt="CariMakan">
        <div style="margin-top:8px;font-size:11px;color:rgba(255,255,255,0.4);font-weight:600;">PANEL ADMIN</div>
    </div>
    <nav class="sidebar-menu">
        <a href="index.php" class="<?php echo ($activePage === 'dashboard') ? 'active' : ''; ?>">Dashboard</a>
        <a href="kelola_warung.php" class="<?php echo ($activePage === 'warung') ? 'active' : ''; ?>"> Kelola Warung</a>
        <a href="manajemen_user.php" class="<?php echo ($activePage === 'users') ? 'active' : ''; ?>"> Manajemen User</a>
        <a href="persetujuan.php" class="<?php echo ($activePage === 'persetujuan') ? 'active' : ''; ?>"> Persetujuan <span id="tunggu-badge" style="background:#e74c3c;color:white;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;display:none;">0</span></a>
        <a href="laporan.php" class="<?php echo ($activePage === 'laporan') ? 'active' : ''; ?>">Laporan</a>
        <a href="../index.php">Lihat Beranda</a>
    </nav>
    <div class="sidebar-footer">
        <a href="#" onclick="logoutUser()" style="color:rgba(255,255,255,0.4);text-decoration:none;font-size:12px;"><img src="../assets/7.Logout.png" class="logout" alt="logout"> Logout</a>
    </div>
</aside>
