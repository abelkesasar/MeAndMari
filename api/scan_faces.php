<?php
require '../db.php';

function categorizeMedia($conn, $table, $id_col, $file_col) {
    $res = mysqli_query($conn, "SELECT * FROM $table");
    $count = 0;
    while ($row = mysqli_fetch_assoc($res)) {
        $filename = strtolower($row[$file_col]);
        $id = $row[$id_col];
        
        $detected = 'unknown';
        // Smart Detection berdasarkan nama file (ML Sim)
        if (strpos($filename, 'abel') !== false && strpos($filename, 'mari') !== false) {
            $detected = 'both';
        } elseif (strpos($filename, 'abel') !== false) {
            $detected = 'abel';
        } elseif (strpos($filename, 'mari') !== false) {
            $detected = 'mari';
        }
        
        mysqli_query($conn, "UPDATE $table SET detected_person = '$detected' WHERE $id_col = $id");
        $count++;
    }
    return $count;
}

$total = 0;
$total += categorizeMedia($conn, 'memories', 'id', 'photo');
$total += categorizeMedia($conn, 'memory_photos', 'id', 'photo');
$total += categorizeMedia($conn, 'memory_videos', 'id', 'video');

header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'Scan media selesai!',
    'total_processed' => $total
]);
?>
