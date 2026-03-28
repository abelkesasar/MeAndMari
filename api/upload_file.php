<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require '../db.php';

if (!isset($_FILES['file'])) {
    echo json_encode(['status' => 'error', 'message' => 'File is required']);
    exit;
}

$file = $_FILES['file'];
$original_name = strtolower($file['name']);
$ext = pathinfo($original_name, PATHINFO_EXTENSION);

$prefix = "";
if (strpos($original_name, "abel") !== false) $prefix .= "abel_";
if (strpos($original_name, "mari") !== false) $prefix .= "mari_";

$filename = $prefix . time() . '_' . rand(100, 999) . '.' . $ext;
$target = '../uploads/' . $filename;

if (move_uploaded_file($file['tmp_name'], $target)) {
    echo json_encode(['status' => 'success', 'data' => ['filename' => $filename]]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Upload failed']);
}
?>
