<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $happy_meter = $_POST['happy_meter'];
    $created_at = $_POST['created_at'];
    $is_background = isset($_POST['is_background']) ? 1 : 0;

    // Insert to memories table
    $query = "INSERT INTO memories (title, photo, happy_meter, description, location, created_at, is_background) 
              VALUES ('$title', '', '$happy_meter', '$description', '$location', '$created_at', '$is_background')";
    
    if (mysqli_query($conn, $query)) {
        $memory_id = mysqli_insert_id($conn);
        $target_dir = "../uploads/";
        $main_photo = "";

        // Handle Multiple Photo Uploads
        if (isset($_FILES['photos'])) {
            $total_files = count($_FILES['photos']['name']);
            for ($i = 0; $i < $total_files; $i++) {
                $photo_name = strtolower($_FILES['photos']['name'][$i]);
                $tmp_name = $_FILES['photos']['tmp_name'][$i];
                
                if (!empty($photo_name)) {
                    $ext = pathinfo($photo_name, PATHINFO_EXTENSION);
                    $new_photo_name = time() . '_' . $i . '.' . $ext;
                    
                    if (move_uploaded_file($tmp_name, $target_dir . $new_photo_name)) {
                        mysqli_query($conn, "INSERT INTO memory_photos (memory_id, photo) VALUES ('$memory_id', '$new_photo_name')");
                        if ($i === 0) {
                            $main_photo = $new_photo_name;
                        }
                    }
                }
            }
        }

        // Handle Multiple Video Uploads
        if (isset($_FILES['videos'])) {
            $total_videos = count($_FILES['videos']['name']);
            for ($i = 0; $i < $total_videos; $i++) {
                $video_name = strtolower($_FILES['videos']['name'][$i]);
                $tmp_name = $_FILES['videos']['tmp_name'][$i];
                
                if (!empty($video_name)) {
                    $v_ext = pathinfo($video_name, PATHINFO_EXTENSION);
                    $new_video_name = "vid_" . time() . '_' . $i . '.' . $v_ext;
                    
                    if (move_uploaded_file($tmp_name, $target_dir . $new_video_name)) {
                        mysqli_query($conn, "INSERT INTO memory_videos (memory_id, video) VALUES ('$memory_id', '$new_video_name')");
                    }
                }
            }
        }

        if (!empty($main_photo)) {
            mysqli_query($conn, "UPDATE memories SET photo = '$main_photo' WHERE id = '$memory_id'");
        }

        header('Location: dashboard.php?msg=Kenangan berhasil ditambah!');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kenangan - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <a href="dashboard.php" class="text-slate-400 hover:text-slate-600 transition-colors flex items-center font-medium">
                <i class="fas fa-chevron-left mr-2 text-xs"></i> Kembali
            </a>
            <h1 class="text-xl font-bold text-slate-800 absolute left-1/2 -translate-x-1/2 hidden md:block">Tambah Kenangan</h1>
            <div class="flex items-center space-x-3">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Logged in as</p>
                    <p class="text-sm font-bold text-slate-800 leading-none"><?php echo $_SESSION['user_name'] ?? 'Admin'; ?></p>
                </div>
                <?php
                $user_name = $_SESSION['user_name'] ?? 'Admin';
                $u_query = mysqli_query($conn, "SELECT profile_pic FROM users WHERE username = '" . mysqli_real_escape_string($conn, strtolower($user_name)) . "'");
                $u_db = mysqli_fetch_assoc($u_query);

                $profile_pic = "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=random&size=128";
                if (!empty($u_db['profile_pic'])) {
                    $profile_pic = "../uploads/" . $u_db['profile_pic'];
                } else {
                    if (strtolower($user_name) == 'abel') {
                        $profile_pic = "https://ui-avatars.com/api/?name=Abel&background=4f46e5&color=fff&size=128";
                    } elseif (strtolower($user_name) == 'mari') {
                        $profile_pic = "https://ui-avatars.com/api/?name=Mari&background=ec4899&color=fff&size=128";
                    }
                }
                ?>
                <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow-md">
                    <img src="<?php echo $profile_pic; ?>" class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="p-10">
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Judul Momen</label>
                        <input type="text" name="title" required
                            class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                            placeholder="Contoh: Kencan Pertama Kita">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi</label>
                            <input type="text" name="location" required
                                class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                                placeholder="Di mana ini terjadi?">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal</label>
                            <input type="date" name="created_at" required value="<?php echo date('Y-m-d'); ?>"
                                class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Happy Meter</label>
                            <select name="happy_meter" required
                                class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all appearance-none bg-white">
                                <option value="😢 Sangat Sedih">😢 Sangat Sedih</option>
                                <option value="😕 Sedih">😕 Sedih</option>
                                <option value="😐 Biasa Aja">😐 Biasa Aja</option>
                                <option value="😊 Senang" selected>😊 Senang</option>
                                <option value="🥰 Sangat Senang">🥰 Sangat Senang</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Foto-foto</label>
                            <input type="file" name="photos[]" accept="image/*" multiple required
                                class="w-full px-5 py-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-all cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Video</label>
                            <input type="file" name="videos[]" accept="video/*" multiple
                                class="w-full px-5 py-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-all cursor-pointer">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Cerita Singkat</label>
                        <textarea name="description" rows="4"
                            class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                            placeholder="Tuliskan apa yang membuat momen ini spesial..."></textarea>
                    </div>

                    <!-- Background Toggle -->
                    <label class="flex items-center gap-4 p-5 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300 transition-all cursor-pointer group">
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="is_background" id="is_background" class="sr-only peer">
                            <div class="w-12 h-6 bg-slate-200 rounded-full peer-checked:bg-indigo-600 transition-colors duration-300"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-300 peer-checked:translate-x-6"></div>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 group-hover:text-indigo-700 transition-colors">Jadikan Background Halaman Utama</p>
                            <p class="text-xs text-slate-400 mt-0.5">Foto-foto dari kenangan ini akan tampil sebagai background animasi di halaman utama</p>
                        </div>
                    </label>

                    <button type="submit" name="submit"
