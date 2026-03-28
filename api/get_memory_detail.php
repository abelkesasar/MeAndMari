<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require '../db.php';

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID is required']);
    exit;
}

$id = intval($_GET['id']);
$query = "SELECT * FROM memories WHERE id = $id";
$result = mysqli_query($conn, $query);
$memory = mysqli_fetch_assoc($result);

if (!$memory) {
    echo json_encode(['status' => 'error', 'message' => 'Memory not found']);
    exit;
}

// Get all photos for this memory
$photos = [];
$photo_query = "SELECT * FROM memory_photos WHERE memory_id = $id";
$photos_result = mysqli_query($conn, $photo_query);
while ($p = mysqli_fetch_assoc($photos_result)) {
    $photos[] = $p['photo'];
}

// Get all videos for this memory
$videos = [];
$video_query = "SELECT * FROM memory_videos WHERE memory_id = $id";
$videos_result = mysqli_query($conn, $video_query);
while ($v = mysqli_fetch_assoc($videos_result)) {
    $videos[] = $v['video'];
}

echo json_encode([
    'status' => 'success',
    'data' => [
        'memory' => $memory,
        'photos' => $photos,
        'videos' => $videos
    ]
]);
?>
