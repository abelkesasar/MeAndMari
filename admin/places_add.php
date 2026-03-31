<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $maps_link = mysqli_real_escape_string($conn, $_POST['maps_link']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $photo = "";
    $cover_photo = "";

    // FOTO BIASA
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = time() . '_photo.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/" . $photo);
    }

    // FOTO SAMPUL 🔥 (INI YANG TADI KURANG)
    if (!empty($_FILES['cover_photo']['name'])) {
        $ext = pathinfo($_FILES['cover_photo']['name'], PATHINFO_EXTENSION);
        $cover_photo = time() . '_cover.' . $ext;
        move_uploaded_file($_FILES['cover_photo']['tmp_name'], "../uploads/" . $cover_photo);
    }

    // QUERY FIX
    $query = "INSERT INTO places (name, location, maps_link, description, photo, cover_photo)
              VALUES ('$name', '$location', '$maps_link', '$description', '$photo', '$cover_photo')";

    if (mysqli_query($conn, $query)) {
        header('Location: places.php?msg=Place berhasil ditambah!');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Place - Admin</title>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
</head>

<body class="bg-slate-50 min-h-screen py-12 px-4">

<div class="max-w-2xl mx-auto">

<!-- HEADER -->
<div class="mb-8 flex items-center justify-between">

    <a href="places.php" class="text-slate-400 hover:text-slate-600 transition-colors flex items-center font-medium">
        <i class="fas fa-chevron-left mr-2 text-xs"></i> Kembali
    </a>

    <h1 class="text-xl font-bold text-slate-800 absolute left-1/2 -translate-x-1/2 hidden md:block">
        Tambah Place
    </h1>

    <div class="flex items-center space-x-3">
        <div class="text-right hidden sm:block">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Logged in as</p>
            <p class="text-sm font-bold text-slate-800 leading-none">
                <?php echo $_SESSION['user_name'] ?? 'Admin'; ?>
            </p>
        </div>

        <?php
        $user_name = $_SESSION['user_name'] ?? 'Admin';
        $u_query = mysqli_query($conn, "SELECT profile_pic FROM users WHERE username = '" . mysqli_real_escape_string($conn, strtolower($user_name)) . "'");
        $u_db = mysqli_fetch_assoc($u_query);

        $profile_pic = "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=random&size=128";

        if (!empty($u_db['profile_pic'])) {
            $profile_pic = "../uploads/" . $u_db['profile_pic'];
        }
        ?>

        <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow-md">
            <img src="<?php echo $profile_pic; ?>" class="w-full h-full object-cover">
        </div>
    </div>

</div>

<!-- FORM CARD -->
<div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">

<div class="p-10">

<form method="POST" enctype="multipart/form-data" class="space-y-6">

<!-- NAME -->
<div>
<label class="block text-sm font-bold text-slate-700 mb-2">Nama Tempat</label>
<input type="text" name="name" required
class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
placeholder="Contoh: Lembang Park Zoo">
</div>

<!-- LOCATION -->
<div>
<label class="block text-sm font-bold text-slate-700 mb-2">Lokasi</label>
<input type="text" name="location" required
class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
placeholder="Bandung, Indonesia">
</div>

<!-- MAP LINK -->
<div>
<label class="block text-sm font-bold text-slate-700 mb-2">Link Google Maps</label>
<input type="text" name="maps_link"
class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
placeholder="https://maps.google.com/...">
</div>

<!-- PHOTO -->
<div>
<label class="block text-sm font-bold text-slate-700 mb-2">Foto Tempat</label>
<input type="file" name="photo" accept="image/*"
class="w-full px-5 py-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-all cursor-pointer">
</div>

<!-- COVER PHOTO -->
<div>
<label class="block text-sm font-bold text-slate-700 mb-2">Foto Sampul</label>
<input type="file" name="cover_photo" accept="image/*"
class="w-full px-5 py-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-all cursor-pointer">
</div>

<!-- DESCRIPTION -->
<div>
<label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
<textarea name="description" rows="4"
class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
placeholder="Ceritakan tempat ini..."></textarea>
</div>

<!-- BUTTON -->
<button type="submit" name="submit"
class="w-full py-4 bg-slate-900 text-white rounded-2xl font-semibold hover:bg-slate-800 transition">
Tambah Place
</button>

</form>

</div>
</div>

</div>

</body>
</html>