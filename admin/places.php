<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

$query = "SELECT * FROM places ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Places - Me & Mari</title>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
</head>

<body class="bg-slate-50 min-h-screen flex">

<?php include '../sidebar.php'; ?>

<button onclick="toggleSidebar()" 
class="md:hidden fixed top-6 left-6 z-50 w-12 h-12 bg-white rounded-2xl shadow-lg border border-slate-100 flex items-center justify-center text-slate-600 hover:text-indigo-600 transition-all">
    <i class="fas fa-bars text-xl"></i>
</button>

<!-- MAIN -->
<main class="flex-1 min-w-0 overflow-auto md:ml-64">

<!-- HEADER -->
<header class="h-20 md:h-16 bg-white border-b border-slate-200 px-6 md:px-8 flex items-center justify-end sticky top-0 z-10">

<?php
$user_name = $_SESSION['user_name'] ?? 'Admin';

$u_query = mysqli_query($conn, "SELECT profile_pic FROM users WHERE username = '" . mysqli_real_escape_string($conn, strtolower($user_name)) . "'");
$u_db = mysqli_fetch_assoc($u_query);

$profile_pic = "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=random&size=128";

if (!empty($u_db['profile_pic'])) {
    $profile_pic = "../uploads/" . $u_db['profile_pic'];
}
?>

<div class="flex items-center space-x-3">
    <div class="text-right leading-tight">
        <p class="text-[10px] text-slate-400 uppercase tracking-widest">Logged in as</p>
        <p class="text-lg font-bold text-slate-800">
            <?php echo $user_name; ?>
        </p>
    </div>

    <div class="w-10 h-10 rounded-full overflow-hidden">
        <img src="<?php echo $profile_pic; ?>" class="w-full h-full object-cover">
    </div>
</div>

</header>

<!-- CONTENT -->
<div class="p-8">

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Manage Places</h1>
        <p class="text-slate-500 text-sm mt-1">Kelola tempat-tempat yang pernah dikunjungi.</p>
    </div>

    <a href="places_add.php" class="inline-flex items-center px-6 py-3 bg-slate-900 text-white rounded-xl font-semibold shadow hover:bg-slate-800 transition">
        <i class="fas fa-plus mr-2 text-xs"></i>
        Tambah Place
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
<div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center shadow-sm">
    <i class="fas fa-check-circle mr-3"></i>
    <span class="text-sm"><?php echo $_GET['msg']; ?></span>
</div>
<?php endif; ?>

<div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
<div class="overflow-x-auto">

<table class="w-full text-left border-collapse">

<thead>
<tr class="bg-slate-50 border-b border-slate-200">
    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Place</th>
    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Lokasi</th>
    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Maps</th>
    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase text-right">Aksi</th>
</tr>
</thead>

<tbody class="divide-y divide-slate-100">

<?php while($row = mysqli_fetch_assoc($result)): ?>

<tr class="hover:bg-slate-50">

<td class="px-6 py-4">
    <div class="flex items-center space-x-4">
        <div class="w-12 h-12 rounded-xl overflow-hidden">
            <?php
$cover = "";

// 1. cover manual
if (!empty($row['cover_photo'])) {
    $cover = "../uploads/" . $row['cover_photo'];
} else {

    // 2. dari place_photos
    $p = mysqli_query($conn, "SELECT photo FROM place_photos WHERE place_id = {$row['id']} LIMIT 1");
    $pp = mysqli_fetch_assoc($p);

    if (!empty($pp['photo'])) {
        $cover = "../uploads/" . $pp['photo'];
    } else {

        // 3. dari memories (by lokasi)
        $loc = mysqli_real_escape_string($conn, $row['location']);

        $m = mysqli_query($conn, "
            SELECT photo FROM memories 
            WHERE location LIKE '%$loc%' 
            LIMIT 1
        ");
        $mm = mysqli_fetch_assoc($m);

        if (!empty($mm['photo'])) {
            $cover = "../uploads/" . $mm['photo'];
        } else {
            // 4. fallback terakhir
            $cover = "https://via.placeholder.com/300";
        }
    }
}
?>

<img src="<?php echo $cover; ?>" class="w-full h-full object-cover">
        </div>
        <div>
            <div class="font-bold"><?php echo $row['name']; ?></div>
            <div class="text-xs text-slate-400"><?php echo $row['description']; ?></div>
        </div>
    </div>
</td>

<td class="px-6 py-4"><?php echo $row['location']; ?></td>

<td class="px-6 py-4">
    <a href="<?php echo $row['maps_link']; ?>" target="_blank" class="text-indigo-600 text-sm">
        Open Maps
    </a>
</td>

<td class="px-6 py-4 text-right space-x-2">
    <a href="places_edit.php?id=<?php echo $row['id']; ?>" class="text-indigo-600">Edit</a>
    <a href="places_delete.php?id=<?php echo $row['id']; ?>" class="text-red-500" onclick="return confirm('Yakin hapus?')">Delete</a>
</td>

</tr>

<?php endwhile; ?>

</tbody>
</table>

</div>
</div>

</div>

</main>

<div id="adminOverlay" onclick="toggleAdminSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

<script>
function toggleAdminSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminOverlay');

    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
</script>

</body>
</html>