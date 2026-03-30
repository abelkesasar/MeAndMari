<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

$id = intval($_GET['id']);
$query = "SELECT * FROM memories WHERE id = $id";
$result = mysqli_query($conn, $query);
$memory = mysqli_fetch_assoc($result);

if (!$memory) {
    header('Location: dashboard.php');
    exit;
}

if (isset($_POST['update'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $happy_meter = $_POST['happy_meter'];
    $created_at = $_POST['created_at'];
    $is_background = isset($_POST['is_background']) ? 1 : 0;

    // Update main memory info
    $update_query = "UPDATE memories SET 
                     title = '$title', 
                     description = '$description', 
                     location = '$location', 
                     happy_meter = '$happy_meter',
                     created_at = '$created_at',
                     is_background = '$is_background'
                     WHERE id = $id";
    
    if (mysqli_query($conn, $update_query)) {
        $target_dir = "../uploads/";

        // Handle Cover Photo Update
        if (!empty($_FILES['photo']['name'])) {
            $cover_name = strtolower($_FILES['photo']['name']);
            $tmp_cover = $_FILES['photo']['tmp_name'];
            $cover_ext = pathinfo($cover_name, PATHINFO_EXTENSION);
            
            // Check if file is actually uploaded
            if (is_uploaded_file($tmp_cover)) {
                $new_cover_name = time() . '_cover.' . $cover_ext;
                
                if (move_uploaded_file($tmp_cover, $target_dir . $new_cover_name)) {
                    mysqli_query($conn, "UPDATE memories SET photo = '$new_cover_name' WHERE id = $id");
                }
            }
        }

        // Handle Additional Photo Uploads
        if (isset($_FILES['photos'])) {
            $total_files = count($_FILES['photos']['name']);
            for ($i = 0; $i < $total_files; $i++) {
                $photo_name = strtolower($_FILES['photos']['name'][$i]);
                $tmp_name = $_FILES['photos']['tmp_name'][$i];
                
                if (!empty($photo_name)) {
                    $ext = pathinfo($photo_name, PATHINFO_EXTENSION);
                    $new_photo_name = time() . '_extra_' . $i . '.' . $ext;
                    
                    if (move_uploaded_file($tmp_name, $target_dir . $new_photo_name)) {
                        mysqli_query($conn, "INSERT INTO memory_photos (memory_id, photo) VALUES ('$id', '$new_photo_name')");
                    }
                }
            }
        }

        // Handle Additional Video Uploads
        if (isset($_FILES['videos'])) {
            $total_videos = count($_FILES['videos']['name']);
            for ($i = 0; $i < $total_videos; $i++) {
                $video_name = strtolower($_FILES['videos']['name'][$i]);
                $tmp_name = $_FILES['videos']['tmp_name'][$i];
                
                if (!empty($video_name)) {
                    $v_ext = pathinfo($video_name, PATHINFO_EXTENSION);
                    $new_video_name = "vid_" . time() . '_' . $i . '.' . $v_ext;
                    
                    if (move_uploaded_file($tmp_name, $target_dir . $new_video_name)) {
                        mysqli_query($conn, "INSERT INTO memory_videos (memory_id, video) VALUES ('$id', '$new_video_name')");
                    }
                }
            }
        }

        header('Location: dashboard.php?msg=Kenangan berhasil diperbarui!');
        exit;
    }
}

// Fetch existing photos and videos
$photos_res = mysqli_query($conn, "SELECT * FROM memory_photos WHERE memory_id = $id");
$videos_res = mysqli_query($conn, "SELECT * FROM memory_videos WHERE memory_id = $id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kenangan - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <a href="dashboard.php" class="text-slate-400 hover:text-slate-600 transition-colors flex items-center font-medium">
                <i class="fas fa-chevron-left mr-2 text-xs"></i> Kembali
            </a>
            <h1 class="text-xl font-bold text-slate-800 absolute left-1/2 -translate-x-1/2 hidden md:block">Edit Kenangan</h1>
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
                <form method="POST" enctype="multipart/form-data" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Judul Momen</label>
                                <input type="text" name="title" required value="<?php echo htmlspecialchars($memory['title']); ?>"
                                    class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi</label>
                                <input type="text" name="location" required value="<?php echo htmlspecialchars($memory['location']); ?>"
                                    class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal</label>
                                <input type="date" name="created_at" required value="<?php echo date('Y-m-d', strtotime($memory['created_at'])); ?>"
                                    class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Happy Meter</label>
                                <select name="happy_meter" required
                                    class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all appearance-none bg-white">
                                    <option value="😢 Sangat Sedih" <?php if($memory['happy_meter'] == '😢 Sangat Sedih') echo 'selected'; ?>>😢 Sangat Sedih</option>
                                    <option value="😕 Sedih" <?php if($memory['happy_meter'] == '😕 Sedih') echo 'selected'; ?>>😕 Sedih</option>
                                    <option value="😐 Biasa Aja" <?php if($memory['happy_meter'] == '😐 Biasa Aja') echo 'selected'; ?>>😐 Biasa Aja</option>
                                    <option value="😊 Senang" <?php if($memory['happy_meter'] == '😊 Senang') echo 'selected'; ?>>😊 Senang</option>
                                    <option value="🥰 Sangat Senang" <?php if($memory['happy_meter'] == '🥰 Sangat Senang') echo 'selected'; ?>>🥰 Sangat Senang</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm fo
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Ganti Foto Sampul</label>
                                <div class="flex items-center space-x-4">
                                    <div class="w-20 h-20 rounded-xl overflow-hidden border border-slate-200 shadow-sm">
                                        <img src="../uploads/<?php echo $memory['photo']; ?>" class="w-full h-full object-cover">
                                    </div>
                                    <input type="file" name="photo" accept="image/*"
                                        class="flex-1 px-5 py-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-all cursor-pointer text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Tambah Foto (Multi)</label>
                                <input type="file" name="photos[]" accept="image/*" multiple
                                    class="w-full px-5 py-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-all cursor-pointer text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Tambah Video (Multi)</label>
                                <input type="file" name="videos[]" accept="video/*" multiple
                                    class="w-full px-5 py-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition-all cursor-pointer text-sm">
                            </div>

                            <div class="pt-4">
                                <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center">
                                    <i class="fas fa-images mr-2 text-indigo-500"></i> Media Saat Ini
                                </h3>
                                <div class="grid grid-cols-3 gap-3">
                                    <!-- Photos -->
                                    <?php while($p = mysqli_fetch_assoc($photos_res)): ?>
                                        <div class="aspect-square rounded-xl overflow-hidden border border-slate-100 shadow-sm">
                                            <img src="../uploads/<?php echo $p['photo']; ?>" class="w-full h-full object-cover">
                                        </div>
                                    <?php endwhile; ?>
                                    
                                    <!-- Videos -->
                                    <?php while($v = mysqli_fetch_assoc($videos_res)): ?>
                                        <div class="aspect-square rounded-xl overflow-hidden border border-slate-100 shadow-sm bg-slate-900 flex items-center justify-center relative group">
                                            <i class="fas fa-video text-white/50"></i>
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <i class="fas fa-play text-white text-xs"></i>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                    
                                    <!-- Old Single Video -->
                                    <?php if(!empty($memory['video'])): ?>
                                        <div class="aspect-square rounded-xl overflow-hidden border border-slate-100 shadow-sm bg-indigo-900 flex items-center justify-center relative group">
                                            <i class="fas fa-film text-white/50"></i>
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <i class="fas fa-play text-white text-xs"></i>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

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
