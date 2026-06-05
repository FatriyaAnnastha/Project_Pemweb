<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$target_dir = "uploads/";
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
    echo json_encode(['success' => true, 'path' => $target_file]);
} else {
    echo json_encode(['error' => 'Gagal upload']);
}
?>