<?php
session_start();
require '../db.php';

$id = intval($_GET['id']);

// hapus relasi dulu
mysqli_query($conn, "DELETE FROM place_memories WHERE place_id=$id");

// hapus gambar
mysqli_query($conn, "DELETE FROM place_images WHERE place_id=$id");

// hapus place
mysqli_query($conn, "DELETE FROM places WHERE id=$id");

header("Location: places.php");