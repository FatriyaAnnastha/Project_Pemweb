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
