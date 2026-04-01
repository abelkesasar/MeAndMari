<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

$id = intval($_GET['id']);
$query = "SELECT * FROM places WHERE id = $id";
$result = mysqli_query($conn, $query);
$place = mysqli_fetch_assoc($result);

if (!$place) {
    header('Location: places.php');
    exit;
}

if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $maps_link = mysqli_real_escape_string($conn, $_POST['maps_link']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $update = "UPDATE places SET 
                name = '$name',
                location = '$location',
                maps_link = '$maps_link',
                description = '$description'
               WHERE id = $id";

    if (mysqli_query($conn, $update)) {

        // FOTO BIASA
        if (!empty($_FILES['photo']['name'])) {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $new_name = time() . '_photo.' . $ext;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/" . $new_name)) {
                mysqli_query($conn, "UPDATE places SET photo = '$new_name' WHERE id = $id");
            }
        }

        // 🔥 COVER PHOTO (TAMBAHAN)
        if (!empty($_FILES['cover_photo']['name'])) {
            $ext = pathinfo($_FILES['cover_photo']['name'], PATHINFO_EXTENSION);
            $new_cover = time() . '_cover.' . $ext;

            if (move_uploaded_file($_FILES['cover_photo']['tmp_name'], "../uploads/" . $new_cover)) {
                mysqli_query($conn, "UPDATE places SET cover_photo = '$new_cover' WHERE id = $id");
            }
        }

        header('Location: places.php?msg=Place berhasil diupdate!');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Place - Admin</title>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
</head>

<body class="bg-slate-50 min-h-screen py-12 px-4">

<div class="max-w-4xl mx-auto">

<!-- HEADER -->
<div class="mb-8 flex items-center justify-between">

    <a href="places.php" class="text-slate-400 hover:text-slate-600 transition-colors flex items-center font-medium">
        <i class="fas fa-chevron-left mr-2 text-xs"></i> Kembali
    </a>

    <h1 class="text-xl font-bold text-slate-800 absolute left-1/2 -translate-x-1/2 hidden md:block">
        Edit Place
    </h1>

    <div class="flex items-center space-x-3">

        <div class="text-right hidden sm:block">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">
                Logged in as
            </p>
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

<!-- CARD -->
<div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">

<div class="p-10">

<form method="POST" enctype="multipart/form-data" class="space-y-8">

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">

<!-- LEFT -->
<div class="space-y-6">

    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Tempat</label>
        <input type="text" name="name" required value="<?php echo htmlspecialchars($place['name']); ?>"
        class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
    </div>

    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi</label>
        <input type="text" name="location" required value="<?php echo htmlspecialchars($place['location']); ?>"
        class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
    </div>

    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Link Google Maps</label>
        <input type="text" name="maps_link" value="<?php echo htmlspecialchars($place['maps_link']); ?>"
        class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
    </div>

</div>

<!-- RIGHT -->
<div class="space-y-6">

    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
        <textarea name="description" rows="5"
        class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"><?php echo htmlspecialchars($place['description']); ?></textarea>
    </div>

    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Ganti Foto</label>

        <div class="flex items-center space-x-4">

            <div class="w-20 h-20 rounded-xl overflow-hidden border border-slate-200 shadow-sm">
                <img src="../uploads/<?php echo $place['photo']; ?>" class="w-full h-full object-cover">
            </div>

            <input type="file" name="photo" accept="image/*"
            class="flex-1 px-5 py-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-all cursor-pointer text-sm">

        </div>
    </div>

    <!-- 🔥 COVER PHOTO BARU -->
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2">Ganti Foto Sampul</label>

        <div class="flex items-center space-x-4">

            <div class="w-20 h-20 rounded-xl overflow-hidden border border-slate-200 shadow-sm">
                <img src="../uploads/<?php echo $place['cover_photo']; ?>" class="w-full h-full object-cover">
            </div>

            <input type="file" name="cover_photo" accept="image/*"
            class="flex-1 px-5 py-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-all cursor-pointer text-sm">

        </div>
    </div>

</div>

</div>

<!-- BUTTON -->
<div class="pt-6 border-t border-slate-100">
    <button type="submit" name="update"
    class="w-full py-5 bg-slate-900 text-white rounded-2xl font-bold shadow-xl shadow-slate-200 hover:bg-slate-800 transition-all hover:scale-[1.01] active:scale-95">
        Simpan Perubahan
    </button>
</div>

</form>

</div>
</div>

</div>

</body>
</html>