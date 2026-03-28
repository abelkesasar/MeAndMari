<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require '../db.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID is required']);
    exit;
}

$id = intval($data['id']);

// Delete memory
$query = "DELETE FROM memories WHERE id = $id";
if (mysqli_query($conn, $query)) {
    // Optionally delete related photos and videos
    mysqli_query($conn, "DELETE FROM memory_photos WHERE memory_id = $id");
    mysqli_query($conn, "DELETE FROM memory_videos WHERE memory_id = $id");
    
    echo json_encode(['status' => 'success', 'message' => 'Memory deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}
?>
