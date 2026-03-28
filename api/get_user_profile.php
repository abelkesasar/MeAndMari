<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require '../db.php';

if (!isset($_GET['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Username is required']);
    exit;
}

$username = mysqli_real_escape_string($conn, strtolower($_GET['username']));
$u_query = mysqli_query($conn, "SELECT profile_pic FROM users WHERE username = '$username'");
$user = mysqli_fetch_assoc($u_query);

echo json_encode([
    'status' => 'success',
    'data' => [
        'profile_pic' => $user ? $user['profile_pic'] : null
    ]
]);
?>
