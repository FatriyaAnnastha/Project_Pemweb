## TEAM CariMakan :
1. Muhammad Mikroju Raseprian Sahid (F1D02410017)
2. Azkal Arya Habibie (F1D02410038)
3. Fatriya Annastha Putra (F1D02410046)

## 📌 Website Name
CariMakan.id

## Deskripsi CariMakan
CariMakan adalah website kuliner interaktif yang membantu pengguna menemukan tempat makan terbaik dengan mudah, cepat, dan praktis. Website ini menyediakan informasi lengkap mengenai restoran, cafe, street food, hingga UMKM kuliner lokal dengan fitur pencarian, kategori makanan, lokasi interaktif, review pengguna, dan rekomendasi makanan populer.

Pengguna dapat melihat detail tempat makan seperti menu, harga, rating, jam operasional, lokasi pada peta, hingga ulasan dari pengguna lain. Selain itu, platform ini juga mendukung peran berbeda seperti User, Pedagang, Reviewer, dan Admin untuk menciptakan ekosistem kuliner digital yang terintegrasi.

## 👥 Tim Pengembang
------------------------------------------
| Nama    | Tanggung Jawab               |
|---------|------------------------------|
| Mikroju | Handle bagian pedagang       |
| Fatriya | Handle bagian pengguna biasa |
| Azkal   | handle bagian admin          |
------------------------------------------

## 🛠️ TECH STACK
## Frontend
- HTML5
- CSS3
- JavaScript
## Backend
- PHP Native
## Database
- MySQL
## Development Tools
- Visual Studio Code
- XAMPP
- GitHub

## 🗂️ Site Map / Menu Structure
```text
CariMakan.ID  
├── index.php  
├── login.php  
├── favorit.php  
├── README.md  
├── carimakan.sql  
│  
├── admin/  
│   ├── index.php  
│   ├── sidebar.php  
│   ├── kelola_warung.php   
│   ├── manajemen_user.php   
│   ├── persetujuan.php  
│   └── laporan.php  
│  
├── pedagang/  
│   ├── index.php  
│   ├── sidebar.php  
│   ├── kelola_warung.php  
│   ├── tambah_warung.php  
│   └── profil.php  
│  
├── backend/  
│   ├── koneksi.php  
│   ├── session_config.php  
│   ├── api.php  
│   └── upload.php  
│  
├── uploads/  
│   └── *.jpg, *.png, *.webp 
│  
├── assets/  
│   ├── css/style.css 
│   ├── js/app.js  
│   └── *.png, *.jpg  
│  
└── sitemap/  
    └── *.jpg  
```

## 🗄️ DBMS Configuration
## DBMS Used : 
MySQL
## Database Name :
carimakan
## Default Port :
3306
## Database Connection Example :
```text
<?php
$host = 'localhost';
$dbname = 'carimakan';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}
?>
```

## 📋 Table Specifications
1. Users
2. Pedagang
3. Admin
4. Warung
5. Favorit
6. 
## ✨ Fitur Utama
### 👤 Untuk Pengguna Biasa
- Dashboard khusus pengguna
- Jelajah tempat makan dengan filter dan pencarian
- Detail tempat makan lengkap
- Peta lokasi interaktif
- Simpan tempat makan favorit
- Sistem review dan rating
- Profil pengguna dan riwayat review
- Dashboard rekomendasi kuliner populer

### 🧑‍🍳 Untuk Pedagang
- Dashboard khusus pedagang
- Kelola profil usaha kuliner
- Tambah dan edit menu makanan
- Upload foto makanan dan tempat usaha
- Kelola jam operasional
- Melihat review pelanggan

### 🛡 Untuk Admin
- Dashboard khusus admin
- Kelola user dan pedagang
- Verifikasi akun pedagang
- Monitoring review dan aktivitas platform
- Kelola kategori makanan
- Moderasi konten pengguna

## 🔮Future Development
- Integrasi fitur pemesanan makanan secara online langsung melalui platform.
- Sistem rekomendasi kuliner berbasis preferensi dan riwayat aktivitas pengguna menggunakan Machine Learning.
- Integrasi GPS dan navigasi real-time untuk mempermudah pengguna menuju lokasi warung.
- Fitur promo, voucher, dan diskon dari pedagang untuk meningkatkan interaksi pengguna.
- Pengembangan aplikasi mobile berbasis Android dan iOS.
- Sistem notifikasi untuk informasi warung baru, promo, dan aktivitas favorit pengguna.
- Dashboard analitik yang lebih lengkap bagi pedagang untuk memantau performa usaha dan ulasan pelanggan.
- Integrasi pembayaran digital (QRIS, E-Wallet, dan Mobile Banking).

## 📊 Project Status
🚧 Currently Under Development

## 🎯 Project Goals
CariMakan.id dikembangkan dengan tujuan untuk menyediakan platform digital yang memudahkan masyarakat dalam menemukan informasi kuliner secara cepat, mudah, dan terpercaya. Selain membantu pengguna mencari tempat makan sesuai kebutuhan dan preferensi mereka, platform ini juga bertujuan untuk mendukung promosi usaha kuliner lokal melalui media digital yang terintegrasi.

## Bug Log
- **Gejala 1 - perbaikan session()**
- **Bug 1**
<img width="557" height="167" alt="image" src="https://github.com/user-attachments/assets/f0f27375-eb14-42c7-ae8f-070cb148084f" />
Bug yang dimana saat log out di beranda biasa user sessionnya tidak langsung hilang
- **Solusi Bug 1**
<img width="497" height="200" alt="image" src="https://github.com/user-attachments/assets/79122b84-30da-46c2-97aa-995503d94581" />  
solusi yang ditemukan menambahkan event dan prevent default Ini memastikan request logout ke backend selesai dulu sebelum browser pindah halaman, jadi session di server dijamin sudah terhapus sebelum redirect terjadi.

---

- **Gejala 2 - rating**
- **Bug 2**
<img width="502" height="105" alt="image" src="https://github.com/user-attachments/assets/b20ea3b7-147c-4ab5-99ba-ac2ea0b6f83a" />
Bug nya dan di beranda ratingnya tidak menampilkan bintang pada rating.
- **Solusi**
<img width="600" height="112" alt="image" src="https://github.com/user-attachments/assets/80103d43-c6ed-4705-b363-f028e290b290" />
Penyelesaian ternyata assets sudah diwakilkan oleh basepath.

## AI Usage Statement

- **Tool:** Claude.AI
- **Untuk apa:** Membantu integrasi dan implementasi fungsi initLeafletMap pada sistem berbasis Leaflet.
- **2-3 prompt utama:**  
  i. *"Bantu integrasikan fungsi initLeafletMap ke dalam aplikasi web agar peta dapat ditampilkan menggunakan Leaflet, termasuk inisialisasi peta, penambahan marker, dan pengaturan koordinat"*  
  ii. *"Periksa dan perbaiki kode integrasi Leaflet yang menggunakan fungsi initLeafletMap, serta berikan solusi untuk error atau masalah yang menyebabkan peta tidak tampil dengan benar"*
- **Bagian Output AI yang dipakai:**  
  i. *Arahan peletakan dan pengimplementasian code*
---
- **Tool:** Claude.AI
- **Untuk apa:** Membantu implementasi fitur toggle password agar pengguna dapat menampilkan atau menyembunyikan kata sandi pada form login dan registrasi.
- **2–3 prompt utama:**
i. *"Bantu membuat fitur toggle show/hide password menggunakan JavaScript pada form login dan registrasi, lengkap dengan ikon mata yang dapat diklik."**
ii. *"Periksa dan perbaiki kode toggle password yang tidak berfungsi dengan benar, serta pastikan kompatibel dengan HTML, CSS, dan JavaScript yang digunakan."**
- **Bagian Output AI yang dipakai:**  
  *i. Arahan peletakan dan pengimplementasian code*
```
