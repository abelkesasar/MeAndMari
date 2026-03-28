<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require '../db.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
    exit;
}

$username = mysqli_real_escape_string($conn, strtolower($data['username']));
$password = $data['password'];

if ($password === 'paskal14022025') {
    $u_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $user = mysqli_fetch_assoc($u_query);
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'username' => ucfirst($username),
            'profile_pic' => $user ? $user['profile_pic'] : null
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Password salah!']);
}
?>
