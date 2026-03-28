<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

$id = intval($_GET['id']);

// Get filename to delete from uploads folder
$res = mysqli_query($conn, "SELECT photo, video FROM memories WHERE id = $id");
$row = mysqli_fetch_assoc($res);
if ($row) {
    if (!empty($row['photo'])) @unlink("../uploads/" . $row['photo']);
    if (!empty($row['video'])) @unlink("../uploads/" . $row['video']);
}

// Delete additional photos from physical storage
$photos_res = mysqli_query($conn, "SELECT photo FROM memory_photos WHERE memory_id = $id");
while($p = mysqli_fetch_assoc($photos_res)) {
    @unlink("../uploads/" . $p['photo']);
}

// Delete additional videos from physical storage
$videos_res = mysqli_query($conn, "SELECT video FROM memory_videos WHERE memory_id = $id");
while($v = mysqli_fetch_assoc($videos_res)) {
    @unlink("../uploads/" . $v['video']);
}

$query = "DELETE FROM memories WHERE id = $id";
if (mysqli_query($conn, $query)) {
    header('Location: dashboard.php?msg=Kenangan berhasil dihapus!');
} else {
    header('Location: dashboard.php?msg=Gagal menghapus kenangan.');
}
exit;
?>
