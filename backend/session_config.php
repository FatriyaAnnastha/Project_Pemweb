<?php
session_start();
require_once 'koneksi.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        // Jika request datang dari API atau Upload (menerima JSON)
        if (strpos($_SERVER['REQUEST_URI'] ?? '', 'api.php') !== false || strpos($_SERVER['REQUEST_URI'] ?? '', 'upload.php') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        } else {
            // Jika request datang dari halaman biasa, lakukan redirect
            $prefix = (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/') !== false || strpos($_SERVER['REQUEST_URI'] ?? '', '/pedagang/') !== false) ? '../' : '';
            header("Location: " . $prefix . "login.php");
            exit;
        }
    }
}

function isPedagang() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'pedagang';
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
?>
