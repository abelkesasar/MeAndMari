<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

$query = "SELECT * FROM memories ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Me and Mari</title>
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
            <a href="dashboard.php" class="flex items-center space-x-3 p-3 bg-indigo-50 text-indigo-700 rounded-xl font-semibold">
                <i class="fas fa-th-large w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="add.php" class="flex items-center space-x-3 p-3 text-slate-500 hover:bg-slate-50 hover:text-slate-800 rounded-xl transition-all">
                <i class="fas fa-plus-circle w-5"></i>
                <span>Tambah Kenangan</span>
            </a>
            <a href="profile.php" class="flex items-center space-x-3 p-3 text-slate-500 hover:bg-slate-50 hover:text-slate-800 rounded-xl transition-all">
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

    <!-- Main Content -->
    <main class="flex-1 min-w-0 overflow-auto">
        <header class="h-20 md:h-16 bg-white border-b border-slate-200 px-6 md:px-8 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center space-x-4">
                <button onclick="toggleAdminSidebar()" class="md:hidden w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-500">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="font-bold text-slate-800">Manajemen Kenangan</h2>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-slate-500">Welcome, <?php echo $_SESSION['user_name'] ?? 'Admin'; ?></span>
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
                <div class="w-8 h-8 rounded-full overflow-hidden border border-slate-200">
                    <img src="<?php echo $profile_pic; ?>" alt="Profile" class="w-full h-full object-cover">
                </div>
            </div>
        </header>

        <div class="p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 leading-tight">Daftar Kenangan</h1>
                    <p class="text-slate-500 text-sm mt-1">Kelola momen-momen indah yang telah disimpan.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="add.php" class="inline-flex items-center px-6 py-3 bg-slate-900 text-white rounded-xl font-semibold shadow-lg hover:bg-slate-800 transition-all active:scale-95">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        Kenangan Baru
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center shadow-sm">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span class="font-medium text-sm"><?php echo $_GET['msg']; ?></span>
                </div>
            <?php endif; ?>

            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Kenangan</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Lokasi</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Happy Meter</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden shadow-inner border border-slate-100">
                                            <img src="../uploads/<?php echo $row['photo']; ?>" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800"><?php echo $row['title']; ?></div>
                                            <div class="text-xs text-slate-400"><?php echo date('d M Y', strtotime($row['created_at'])); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center text-sm font-medium text-slate-600">
                                        <i class="fas fa-map-marker-alt mr-2 opacity-50"></i>
                                        <?php echo $row['location']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold">
                                        <?php echo $row['happy_meter']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" 
                                           class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                           onclick="return confirm('Yakin ingin menghapus kenangan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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
