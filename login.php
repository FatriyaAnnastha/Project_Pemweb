<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentikasi – CariMakan.ID</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-toggle {
            display: flex;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .auth-toggle-btn {
            flex: 1;
            padding: 8px;
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.7);
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .auth-toggle-btn.active {
            background: var(--primary);
            color: white;
        }
        .auth-form {
            display: none;
        }
        .auth-form.active {
            display: block;
        }
    </style>
</head>
<body>
<div id="loading">
    <img src="assets/1.Logo_CariMakan.png" alt="Logo">
    <p>Memuat halaman…</p>
</div>

<div class="login-page">
    <div class="login-box">
        <div class="logo-wrap">
            <img src="assets/1.Logo_CariMakan.png" alt="CariMakan">
        </div>
        <h2>CariMakan.ID</h2>
        <p>Akses Akun Anda</p>

        <!-- Toggle Form Masuk/Daftar -->
        <div class="auth-toggle">
            <button class="auth-toggle-btn active" onclick="switchForm('login', this)">Masuk</button>
            <button class="auth-toggle-btn" onclick="switchForm('register', this)">Daftar</button>
        </div>

        <!-- FORM LOGIN -->
        <div id="form-login" class="auth-form active">
            <input type="email" id="email" placeholder="Email">
            <div class="password-wrapper">
                <input type="password" id="password" placeholder="Password">
                <button type="button" class="toggle-password" onclick="togglePassword('password')">
                    <img src="assets/26-show.png" alt="Tampilkan password" style="width:20px; height:20px; vertical-align:middle;">
                </button>
            </div>
            <input type="hidden" id="role" value="user">

            <div id="hint" style="font-size:11px; opacity:0.8; margin-bottom:10px; line-height:1.5; min-height: 33px; text-align: left;">
                Masuk sebagai Pengguna untuk menjelajahi dan memfavoritkan tempat makan.
            </div>

            <button class="btn-login" onclick="doLogin()">Masuk Ke Akun</button>
        </div>

        <!-- FORM REGISTER -->
        <div id="form-register" class="auth-form">
            <div class="role-tabs">
                <button class="role-tab active" onclick="setRegisterRole('user', this)">User</button>
                <button class="role-tab" onclick="setRegisterRole('pedagang', this)">Pedagang</button>
            </div>

            <input type="text" id="r-nama" placeholder="Nama Lengkap">
            <input type="email" id="r-email" placeholder="Email">
            <div class="password-wrapper">
                <input type="password" id="r-pass" placeholder="Password">
                <button type="button" class="toggle-password" onclick="togglePassword('r-pass')">
                    <img src="assets/26-show.png" alt="Tampilkan password" style="width:20px; height:20px; vertical-align:middle;">
                </button>
            </div>
            <div class="password-wrapper">
                <input type="password" id="r-pass2" placeholder="Konfirmasi Password">
                <button type="button" class="toggle-password" onclick="togglePassword('r-pass2')">
                    <img src="assets/26-show.png" alt="Tampilkan password" style="width:20px; height:20px; vertical-align:middle;">
                </button>
            </div>
            <input type="hidden" id="r-role" value="user">

            <button class="btn-login" onclick="doRegister()">Daftar Sekarang</button>
        </div>

        <div style="margin-top: 14px;">
            <a href="index.php" class="btn-login" style="text-decoration:none; display:block; text-align:center;">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<script src="assets/js/app.js"></script>
<script>
const hints = {
    user: 'Masuk sebagai Pengguna Biasa untuk menjelajahi dan memfavoritkan tempat makan.',
    pedagang: 'Masuk sebagai Pedagang untuk mengelola warung makan dan menu Anda.',
    admin: 'Masuk sebagai Admin untuk mengelola seluruh ekosistem CariMakan.ID.'
};
function switchForm(type, btn) {
    document.querySelectorAll('.auth-toggle-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
    if (type === 'login') {
        document.getElementById('form-login').classList.add('active');
    } else {
        document.getElementById('form-register').classList.add('active');
    }
}

function setRoleTab(role, el) {
    document.querySelectorAll('#form-login .role-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('role').value = role;
    document.getElementById('hint').innerHTML = hints[role];
    document.getElementById('email').value = role + '@gmail.com';
    document.getElementById('password').value = '123';
}

function setRegisterRole(role, el) {
    document.querySelectorAll('#form-register .role-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('r-role').value = role;
}

async function doLogin() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    if (!email || !password) { showToast('⚠️ Isi email dan password'); return; }
    const result = await loginUser(email, password);
    if (result.success) {
        showToast('✅ Login berhasil! Mengalihkan...');
        const redirect = { user: 'index.php', pedagang: 'pedagang/index.php', admin: 'admin/index.php' };
        setTimeout(() => window.location.href = redirect[result.role], 1000);
    } else {
        showToast('❌ ' + (result.error || 'Login gagal'));
    }
}

async function doRegister() {
    const nama = document.getElementById('r-nama').value.trim();
    const email = document.getElementById('r-email').value.trim();
    const pass = document.getElementById('r-pass').value;
    const pass2 = document.getElementById('r-pass2').value;
    const role = document.getElementById('r-role').value;
    
    if (!nama || !email || !pass) { showToast('⚠️ Semua field wajib diisi'); return; }
    if (pass !== pass2) { showToast('⚠️ Password tidak cocok'); return; }
    if (pass.length < 3) { showToast('⚠️ Password minimal 3 karakter'); return; }
    
    const result = await registerUser(nama, email, pass, role);
    if (result.success) {
        showToast('✅ Akun berhasil dibuat! Mengalihkan ke login...');
        setTimeout(() => {
            switchForm('login', document.querySelectorAll('.auth-toggle-btn')[0]);
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
            setRoleTab(role, document.querySelector(`#form-login .role-tab[onclick*="${role}"]`));
        }, 1500);
    } else {
        showToast('❌ ' + (result.error || 'Gagal registrasi'));
    }
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const btn = input.parentElement.querySelector('.toggle-password');
    const img = btn.querySelector('img');
    if (input.type === 'password') {
        input.type = 'text';
        img.src = 'assets/27-hide.png';
        img.alt = 'Sembunyikan password';
    } else {
        input.type = 'password';
        img.src = 'assets/26-show.png';
        img.alt = 'Tampilkan password';
    }
}
</script>
</body>
</html>
