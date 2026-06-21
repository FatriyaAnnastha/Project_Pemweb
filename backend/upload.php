<?php
header('Content-Type: application/json');
require_once 'session_config.php';
requireLogin();

$target_dir = "../uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}
if (!isset($_FILES['file'])) {
    echo json_encode(['error' => 'Tidak ada file']);
    exit;
}
$file = $_FILES['file'];
$filename = time() . '_' . basename($file['name']);
$target_file = $target_dir . $filename;
if (move_uploaded_file($file['tmp_name'], $target_file)) {
    echo json_encode(['success' => true, 'path' => 'uploads/' . $filename]);
} else {
    echo json_encode(['error' => 'Gagal upload']);
}
?>
