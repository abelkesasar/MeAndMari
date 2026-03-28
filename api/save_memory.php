<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require '../db.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['title'])) {
    echo json_encode(['status' => 'error', 'message' => 'Title is required']);
    exit;
}

$title = mysqli_real_escape_string($conn, $data['title']);
$description = mysqli_real_escape_string($conn, $data['description']);
$location = mysqli_real_escape_string($conn, $data['location']);
$happy_meter = mysqli_real_escape_string($conn, $data['happy_meter']);
$photo = mysqli_real_escape_string($conn, $data['photo']);

if (isset($data['id']) && $data['id'] != 0) {
    // Update
    $id = intval($data['id']);
    $query = "UPDATE memories SET title='$title', description='$description', location='$location', happy_meter='$happy_meter', photo='$photo' WHERE id=$id";
} else {
    // Insert
    $query = "INSERT INTO memories (title, description, location, happy_meter, photo) VALUES ('$title', '$description', '$location', '$happy_meter', '$photo')";
}

if (mysqli_query($conn, $query)) {
    $memory_id = isset($data['id']) && $data['id'] != 0 ? intval($data['id']) : mysqli_insert_id($conn);

    // Handle additional photos
    if (isset($data['photos']) && is_array($data['photos'])) {
        foreach ($data['photos'] as $p) {
            $p_esc = mysqli_real_escape_string($conn, $p);
            mysqli_query($conn, "INSERT INTO memory_photos (memory_id, photo) VALUES ($memory_id, '$p_esc')");
        }
    }

    // Handle additional videos
    if (isset($data['videos']) && is_array($data['videos'])) {
        foreach ($data['videos'] as $v) {
            $v_esc = mysqli_real_escape_string($conn, $v);
            mysqli_query($conn, "INSERT INTO memory_videos (memory_id, video) VALUES ($memory_id, '$v_esc')");
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Memory saved successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}
?>
