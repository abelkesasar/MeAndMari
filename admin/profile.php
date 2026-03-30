<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

$user_name = $_SESSION['user_name'] ?? 'Admin';
$username_lower = strtolower($user_name);

// Fetch current user data
$u_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username_lower'");
$user_db = mysqli_fetch_assoc($u_query);

if (isset($_POST['update_profile'])) {
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "../uploads/";
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $new_name = "profile_" . $username_lower . "_" . time() . "." . $ext;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_dir . $new_name)) {
            // Update DB
            mysqli_query($conn, "UPDATE users SET profile_pic = '$new_name' WHERE username = '$username_lower'");
            $msg = "Foto profil berhasil diperbarui!";
            // Refresh user data
            $u_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username_lower'");
            $user_db = mysqli_fetch_assoc($u_query);
        }
    }
}

// Set profile pic for display
$display_pic = "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=random&size=128";
if (!empty($user_db['profile_pic'])) {
    $display_pic = "../uploads/" . $user_db['profile_pic'];
} elseif (strtolower($user_name) == 'abel') {
    $display_pic = "https://ui-avatars.com/api/?name=Abel&background=4f46e5&color=fff&size=128";
} elseif (strtolower($user_name) == 'mari') {
    $display_pic = "https://ui-avatars.com/api/?name=Mari&background=ec4899&color=fff&size=128";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - Me and Mari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex">

    <!-- Sidebar -->
    <aside id="adminSidebar" class="fixed inset-y-0 left-0 w-60 bg-white border-r border-slate-200 z-50 transform -translate-x-full md:translate-x-0 md:relative transition-transform duration-300 ease-in-out flex flex-col shadow-2xl md:shadow-none">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <a href="../index.php" class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                    <i class="fas fa-heart text-xs"></i>
                </div>
                <span class="font-bold text-slate-800 tracking-tight">Me & Mari</span>
            </a>
            <button onclick="toggleAdminSidebar()" class="md:hidden text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="dashboard.php" class="flex items-center space-x-3 p-3 text-slate-500 hover:bg-slate-50 hover:text-slate-800 rounded-xl transition-all">
                <i class="fas fa-th-large w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="add.php" class="flex items-center space-x-3 p-3 text-slate-500 hover:bg-slate-50 hover:text-slate-800 rounded-xl transition-all">
                <i class="fas fa-plus-circle w-5"></i>
                <span>Tambah Kenangan</span>
            </a>
            <a href="profile.php" class="flex items-center space-x-3 p-3 bg-indigo-50 text-indigo-700 rounded-xl font-semibold">
                <i class="fas fa-user-circle w-5"></i>
                <span>Profil Saya</span>
            </a>
        </nav>
        <div class="p-4 border-t border-slate-100">
            <a href="../logout.php" class="flex items-center space-x-3 p-3 text-red-500 hover:bg-red-50 rounded-xl transition-all">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="font-semibold">Logout</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 min-w-0 overflow-auto p-8 relative">
        <button onclick="toggleAdminSidebar()" class="md:hidden fixed top-6 right-6 z-40 w-12 h-12 bg-white rounded-2xl shadow-lg border border-slate-100 flex items-center justify-center text-slate-600">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <div class="max-w-xl mx-auto">
            <div class="mb-8">
                <a href="dashboard.php" class="text-slate-400 hover:text-slate-600 transition-colors flex items-center font-medium mb-4">
                    <i class="fas fa-chevron-left mr-2 text-xs"></i> Kembali ke Dashboard
                </a>
                <h1 class="text-3xl font-bold text-slate-900">Pengaturan Profil</h1>
                <p class="text-slate-500 mt-2">Ganti foto profil kamu di sini.</p>
            </div>

            <?php if (isset($msg)): ?>
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center shadow-sm">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span class="font-medium text-sm"><?php echo $msg; ?></span>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="p-10 text-center">
                    <form method="POST" enctype="multipart/form-data" class="space-y-8">
                        <div class="flex flex-col items-center">
                            <div class="relative group">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-indigo-50 shadow-xl mb-4">
                                    <img src="<?php echo $display_pic; ?>" class="w-full h-full object-cover">
                                </div>
                                <label for="profile_pic" class="absolute bottom-4 right-0 w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:bg-indigo-700 transition-all border-4 border-white">
                                    <i class="fas fa-camera text-sm"></i>
                                </label>
                                <input type="file" id="profile_pic" name="profile_pic" class="hidden" accept="image/*" onchange="this.form.submit()">
                            </div>
                            <h2 class="text-xl font-bold text-slate-800"><?php echo $user_name; ?></h2>
                            <p class="text-sm text-slate-400 font-medium uppercase tracking-widest mt-1">Ganti Foto Profil</p>
                        </div>

                        <div class="pt-6 border-t border-slate-50">
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Klik ikon kamera untuk memilih foto baru.<br>
                                Foto akan otomatis tersimpan dan tampil di halaman depan.
                            </p>
                        </div>
                        
                        <input type="hidden" name="update_profile" value="1">
                    </form>
                </div>
            </div>
        </div>
    </main>

    <div id="adminOverlay" onclick="toggleAdminSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 backdrop-blur-sm"></div>

    <script>
    function toggleAdminSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('adminOverlay');
        const isHidden = sidebar.classList.contains('-translate-x-full');
        
        if (isHidden) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }
    </script>
</body>
</html>