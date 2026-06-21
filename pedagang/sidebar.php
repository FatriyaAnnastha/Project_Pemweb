<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="../assets/1.Logo_CariMakan.png" alt="CariMakan">
        <div style="margin-top:8px;font-size:11px;color:rgba(255,255,255,0.4);font-weight:600;">PANEL PEDAGANG</div>
    </div>
    <nav class="sidebar-menu">
        <a href="index.php" class="<?php echo ($activePage === 'dashboard') ? 'active' : ''; ?>"> Dashboard</a>
        <a href="kelola_warung.php" class="<?php echo ($activePage === 'warung') ? 'active' : ''; ?>"> Kelola Warung</a>
        <a href="tambah_warung.php" class="<?php echo ($activePage === 'tambah') ? 'active' : ''; ?>"> Tambah Warung</a>
        <a href="profil.php" class="<?php echo ($activePage === 'profil') ? 'active' : ''; ?>"> Profil Saya</a>
        <a href="../index.php"> Lihat Beranda</a>
    </nav>
    <div class="sidebar-footer">
        <a href="#" onclick="logoutUser()" style="color:rgba(255,255,255,0.4);text-decoration:none;font-size:12px;"><img src="../assets/7.Logout.png" class="logout" alt="logout"> Logout</a>
    </div>
</aside>
