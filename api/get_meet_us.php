<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../db.php';

// Ambil data profil Abel
$res_abel = mysqli_query($conn, "SELECT username, profile_pic FROM users WHERE username = 'abel'");
$abel_info = mysqli_fetch_assoc($res_abel);

// Ambil data profil Mari
$res_mari = mysqli_query($conn, "SELECT username, profile_pic FROM users WHERE username = 'mari'");
$mari_info = mysqli_fetch_assoc($res_mari);

// Ambil semua foto/video dari semua kenangan (tanpa filter deteksi)
$all_media = [];

// Dari cover
$res = mysqli_query($conn, "SELECT photo FROM memories WHERE photo != '' LIMIT 10");
while($r = mysqli_fetch_assoc($res)) $all_media[] = ['type' => 'photo', 'url' => $r['photo']];

// Dari album tambahan
$res = mysqli_query($conn, "SELECT photo FROM memory_photos LIMIT 10");
while($r = mysqli_fetch_assoc($res)) $all_media[] = ['type' => 'photo', 'url' => $r['photo']];

echo json_encode([
    'status' => 'success',
    'profiles' => [
        'abel' => $abel_info,
        'mari' => $mari_info
    ],
    'all_media' => $all_media
]);
?>
