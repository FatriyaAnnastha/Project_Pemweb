-- Skema Database CariMakan.ID
-- Gunakan file ini untuk menginisialisasi database di phpMyAdmin / MySQL CLI

CREATE DATABASE IF NOT EXISTS carimakan;
USE carimakan;

-- 1. Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    toko VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel Warung
CREATE TABLE IF NOT EXISTS warung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedagang_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    harga VARCHAR(20) NOT NULL,
    deskripsi TEXT DEFAULT NULL,
    lat DECIMAL(10, 8) DEFAULT NULL,
    lng DECIMAL(11, 8) DEFAULT NULL,
    jam_buka TIME DEFAULT NULL,
    jam_tutup TIME DEFAULT NULL,
    hari_kerja VARCHAR(100) DEFAULT NULL,
    img VARCHAR(255) DEFAULT NULL,
    wa VARCHAR(20) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'tunggu',
    rating DECIMAL(2, 1) DEFAULT 0.0,
    FOREIGN KEY (pedagang_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel Menu
CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warung_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    harga DECIMAL(10, 2) NOT NULL,
    gambar VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (warung_id) REFERENCES warung(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabel Reviews (Ulasan & Rating)
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warung_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    komentar TEXT,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (warung_id) REFERENCES warung(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabel Favorites
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    warung_id INT NOT NULL,
    UNIQUE KEY unique_user_warung (user_id, warung_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (warung_id) REFERENCES warung(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 1. Data Users (admin, pedagang, user biasa)
-- --------------------------------------------------------
INSERT INTO users (id, nama, email, password, role, toko) VALUES
(1,  'Admin CariMakan',     'admin@gmail.com',          'admin123',     'admin',    NULL),
(2,  'Dwi Astuti',          'dwiastuti@gmail.com',      'dwi12345',     'pedagang', 'warung_nasi_bu_dwi'),
(3,  'Herman Wijaya',       'herman.wijaya@gmail.com',  'herman123',    'pedagang', 'ayam_taliwang_bu_sari'),
(4,  'Siti Sari Ningsih',   'sitisari@gmail.com',       'sitisari123',  'pedagang', 'sate_rembiga_pak_herman'),
(5,  'Budi Santoso',        'budisantoso@gmail.com',    'budi123',      'pedagang', 'mie_ayam_pak_budi'),
(6,  'Made Wirawan',        'madewirawan@gmail.com',    'made12345',    'pedagang', 'bebek_goreng_pak_made'),
(7,  'Lale Suryani',        'lalesuryani@gmail.com',    'lale12345',    'pedagang', 'pelecing_ayam_inaq_lale'),
(8,  'Agus Salim',          'agussalim@gmail.com',      'agus12345',    'pedagang', 'bakso_malang_cak_agus'),
(9,  'Ni Komang Ayu',       'komangayu@gmail.com',      'komang123',    'pedagang', 'es_campur_bu_komang'),
(10, 'Hartini',             'hartini@gmail.com',        'hartini123',   'pedagang', 'soto_ayam_bu_hartini'),
(11, 'Joko Prasetyo',       'jokoprasetyo@gmail.com',   'joko12345',    'pedagang', 'seafood_pak_joko'),
(12, 'Muhammad Mikroju',    'muhammad@gmail.com',       'mikroju123',   'user',     NULL),
(13, 'Dewi Lestari',        'dewilestari@gmail.com',    'dewi12345',    'user',     NULL),
(14, 'Andi Pratama',        'andipratama@gmail.com',    'andi12345',    'user',     NULL);

-- --------------------------------------------------------
-- 2. Data Warung (10 warung, kolom img WAJIB terisi)
-- --------------------------------------------------------
INSERT INTO warung (id, pedagang_id, nama, kategori, lokasi, harga, deskripsi, lat, lng, jam_buka, jam_tutup, hari_kerja, img, wa, status, rating) VALUES

(1, 2, 'Warung Nasi Bu Dwi', 'Nasi', 'Ampenan, Mataram', 'Murah',
   'Warung nasi campur murah, enak, dan bikin kenyang',
   -8.57900000, 116.07100000, '08:00:00', '14:00:00', 'Sen,Sel,Rab,Kam,Jum',
   'uploads/warung_nasi_bu_dwi.jpg', '081939392231', 'aktif', 4.0),

(2, 3, 'Ayam Taliwang Bu Sari', 'Ayam', 'Cakranegara, Mataram', 'Sedang',
   'Ayam Taliwang khas Lombok dengan sambal pedas otentik',
   -8.58320000, 116.12340000, '10:00:00', '21:00:00', 'Sen,Sel,Rab,Kam,Jum,Sab,Min',
   'uploads/ayam_taliwang_bu_sari.jpg', '081234567801', 'aktif', 4.5),

(3, 4, 'Sate Rembiga Pak Herman', 'Sate', 'Rembiga, Mataram', 'Murah',
   'Sate daging sapi bumbu pedas manis khas Rembiga',
   -8.56610000, 116.11470000, '16:00:00', '23:00:00', 'Sen,Sel,Rab,Kam,Jum,Sab',
   'uploads/sate_rembiga_pak_herman.jpg', '081234567802', 'aktif', 4.7),

(4, 5, 'Mie Ayam Pak Budi', 'Mie', 'Sandubaya, Mataram', 'Sedang',
   'Mie ayam dengan kuah kaldu gurih dan pangsit renyah',
   -8.60230000, 116.13560000, '10:00:00', '20:00:00', 'Sen,Sel,Rab,Kam,Jum,Sab,Min',
   'uploads/mie_ayam_pak_budi.jpg', '081234567805', 'aktif', 4.0),

(5, 6, 'Bebek Goreng Pak Made', 'Bebek', 'Selaparang, Mataram', 'Sedang',
   'Bebek goreng renyah dengan sambal matah khas Bali-Lombok',
   -8.57500000, 116.11000000, '11:00:00', '21:00:00', 'Sen,Sel,Rab,Kam,Jum,Sab',
   'uploads/bebek_goreng_pak_made.jpg', '081234567806', 'aktif', 4.6),

(6, 7, 'Pelecing Ayam Inaq Lale', 'Nasi', 'Pagutan, Mataram', 'Murah',
   'Pelecing ayam pedas khas Lombok dengan sayur urap segar',
   -8.60500000, 116.09500000, '07:00:00', '15:00:00', 'Sen,Sel,Rab,Kam,Jum',
   'uploads/pelecing_ayam_inaq_lale.jpg', '081234567807', 'aktif', 4.3),

(7, 8, 'Bakso Malang Cak Agus', 'Bakso', 'Sekarbela, Mataram', 'Murah',
   'Bakso urat dan bakso telur dengan kuah kaldu sapi kental',
   -8.61000000, 116.08500000, '09:00:00', '21:00:00', 'Sen,Sel,Rab,Kam,Jum,Sab,Min',
   'uploads/bakso_malang_cak_agus.jpg', '081234567808', 'aktif', 4.1),

(8, 9, 'Es Campur Bu Komang', 'Minuman', 'Gomong, Mataram', 'Murah',
   'Es campur segar dengan buah-buahan dan sirup aneka rasa',
   -8.59000000, 116.10500000, '10:00:00', '18:00:00', 'Sen,Sel,Rab,Kam,Jum,Sab,Min',
   'uploads/es_campur_bu_komang.jpg', '081234567809', 'aktif', 3.9),

(9, 10, 'Soto Ayam Bu Hartini', 'Soto', 'Karang Baru, Mataram', 'Murah',
   'Soto ayam kuah bening gurih dengan suwiran ayam kampung',
   -8.58500000, 116.11500000, '06:00:00', '14:00:00', 'Sen,Sel,Rab,Kam,Jum,Sab',
   'uploads/soto_ayam_bu_hartini.jpg', '081234567810', 'tunggu', 0.0),

(10, 11, 'Seafood Pak Joko', 'Seafood', 'Senggigi, Lombok Barat', 'Mahal',
   'Seafood segar bakar dan goreng dengan pemandangan pantai Senggigi',
   -8.48600000, 116.04100000, '15:00:00', '22:00:00', 'Sen,Sel,Rab,Kam,Jum,Sab,Min',
   'uploads/seafood_pak_joko.jpg', '081234567811', 'aktif', 4.8);

-- --------------------------------------------------------
-- 3. Data Menu (kolom gambar WAJIB terisi)
-- --------------------------------------------------------
INSERT INTO menu (id, warung_id, nama, harga, gambar) VALUES
-- Warung Nasi Bu Dwi
(1,  1, 'Nasi Campur',              10000.00, 'uploads/menu_nasi_campur.jpg'),
(2,  1, 'Ayam Goreng',              12000.00, 'uploads/menu_ayam_goreng.jpg'),
(3,  1, 'Tahu Tempe',                5000.00, 'uploads/menu_tahu_tempe.jpg'),

-- Ayam Taliwang Bu Sari
(4,  2, 'Ayam Taliwang Bakar',      25000.00, 'uploads/menu_ayam_taliwang_bakar.jpg'),
(5,  2, 'Ayam Taliwang Goreng',     23000.00, 'uploads/menu_ayam_taliwang_goreng.jpg'),
(6,  2, 'Plecing Kangkung',          8000.00, 'uploads/menu_plecing_kangkung.jpg'),

-- Sate Rembiga Pak Herman
(7,  3, 'Sate Rembiga 10 Tusuk',    20000.00, 'uploads/menu_sate_rembiga_10.jpg'),
(8,  3, 'Sate Rembiga 20 Tusuk',    35000.00, 'uploads/menu_sate_rembiga_20.jpg'),

-- Mie Ayam Pak Budi
(9,  4, 'Mie Ayam Original',        13000.00, 'uploads/menu_mie_ayam_original.jpg'),
(10, 4, 'Mie Ayam Bakso',           16000.00, 'uploads/menu_mie_ayam_bakso.jpg'),
(11, 4, 'Pangsit Goreng',            7000.00, 'uploads/menu_pangsit_goreng.jpg'),

-- Bebek Goreng Pak Made
(12, 5, 'Bebek Goreng Sambal Matah', 28000.00, 'uploads/menu_bebek_goreng_sambal_matah.jpg'),
(13, 5, 'Bebek Bakar',              30000.00, 'uploads/menu_bebek_bakar.jpg'),

-- Pelecing Ayam Inaq Lale
(14, 6, 'Pelecing Ayam',            15000.00, 'uploads/menu_pelecing_ayam.jpg'),
(15, 6, 'Nasi Pelecing',            10000.00, 'uploads/menu_nasi_pelecing.jpg'),
(16, 6, 'Sayur Urap',                6000.00, 'uploads/menu_sayur_urap.jpg'),

-- Bakso Malang Cak Agus
(17, 7, 'Bakso Urat',               15000.00, 'uploads/menu_bakso_urat.jpg'),
(18, 7, 'Bakso Telur',              17000.00, 'uploads/menu_bakso_telur.jpg'),
(19, 7, 'Mie Bakso',                16000.00, 'uploads/menu_mie_bakso.jpg'),

-- Es Campur Bu Komang
(20, 8, 'Es Campur',                12000.00, 'uploads/menu_es_campur.jpg'),
(21, 8, 'Es Teler',                 13000.00, 'uploads/menu_es_teler.jpg'),

-- Soto Ayam Bu Hartini
(22, 9, 'Soto Ayam',                12000.00, 'uploads/menu_soto_ayam.jpg'),
(23, 9, 'Soto Babat',               15000.00, 'uploads/menu_soto_babat.jpg'),

-- Seafood Pak Joko
(24, 10, 'Ikan Bakar',              35000.00, 'uploads/menu_ikan_bakar.jpg'),
(25, 10, 'Cumi Goreng Tepung',      30000.00, 'uploads/menu_cumi_goreng_tepung.jpg'),
(26, 10, 'Udang Saus Padang',       40000.00, 'uploads/menu_udang_saus_padang.jpg');

-- --------------------------------------------------------
-- 4. Data Reviews
-- --------------------------------------------------------
INSERT INTO reviews (id, warung_id, user_id, rating, komentar, tanggal) VALUES
(1,  1,  12, 4, 'Nasi campurnya enak, porsi pas',                 '2026-06-13 17:19:23'),
(2,  1,  13, 5, 'Murah dan kenyang, recommended!',                 '2026-06-17 13:20:28'),
(3,  2,  12, 5, 'Ayam taliwangnya enak banget, sambalnya pas!',    '2026-06-19 12:30:00'),
(4,  3,  13, 5, 'Sate rembiga terenak yang pernah saya coba',      '2026-06-19 20:10:00'),
(5,  4,  14, 4, 'Mie ayamnya enak tapi agak lama nunggu',          '2026-06-20 19:30:00'),
(6,  5,  12, 5, 'Bebeknya garing, sambal matahnya juara',          '2026-06-20 18:00:00'),
(7,  6,  13, 4, 'Pelecing ayamnya pedas mantap',                   '2026-06-20 08:15:00'),
(8,  7,  14, 4, 'Kuahnya gurih, baksonya kenyal',                  '2026-06-21 12:00:00'),
(9,  8,  12, 3, 'Es campurnya enak tapi porsinya kecil',           '2026-06-21 14:20:00'),
(10, 10, 14, 5, 'Seafoodnya segar, view pantainya bonus banget',   '2026-06-21 19:45:00');

-- --------------------------------------------------------
-- 5. Data Favorites
-- --------------------------------------------------------
INSERT INTO favorites (id, user_id, warung_id) VALUES
(1, 12, 1),
(2, 12, 2),
(3, 13, 3),
(4, 13, 5),
(5, 14, 7),
(6, 14, 10);