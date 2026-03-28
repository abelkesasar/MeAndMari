<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require '../db.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$query = "SELECT * FROM memories";
if ($search != '') {
    $query .= " WHERE title LIKE '%$search%' OR location LIKE '%$search%' OR description LIKE '%$search%'";
}
$query .= " ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);

$memories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $memories[] = $row;
}

echo json_encode([
    'status' => 'success',
    'data' => $memories
]);
?>
