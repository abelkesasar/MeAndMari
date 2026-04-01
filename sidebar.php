<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$base_url = '/MeAndMari';

// CEK ADMIN
$is_admin = false;
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $is_admin = true;
}

// Fallback logic for detecting admin folder
$is_in_admin_folder = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false);
?>

<aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white border-r border-slate-200 z-[60] transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl md:shadow-none flex flex-col">
    
    <!-- HEADER -->
    <div class="p-6 border-b border-slate-50">
        <div class="flex items-center justify-between">
            <a href="<?= $base_url ?>/memories.php" class="flex items-center space-x-3 group text-decoration-none">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform">
                    <i class="fas fa-heart text-sm"></i>
                </div>
                <div>
                    <span class="block font-bold text-slate-800 tracking-tight leading-none">Me & Mari</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1 block">Memory Vault</span>
                </div>
            </a>
            <button onclick="toggleSidebar()" class="md:hidden w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- NAVIGATION -->
    <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
        
        <nav class="space-y-1">

            <?php if ($is_admin && $is_in_admin_folder): ?>
                <!-- ================= ADMIN MENU ================= -->
                <div class="px-4 mb-4 mt-2">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Admin Panel</p>
                </div>

                <a href="<?= $base_url ?>/admin/dashboard.php"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all group text-decoration-none
                   <?= $current_page == 'dashboard.php' ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <i class="fas fa-th-large w-5 text-center <?= $current_page == 'dashboard.php' ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' ?>"></i>
                    <span class="font-semibold text-sm">Dashboard</span>
                </a>

                <a href="<?= $base_url ?>/admin/add.php"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all group text-decoration-none
                   <?= $current_page == 'add.php' ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <i class="fas fa-plus-circle w-5 text-center <?= $current_page == 'add.php' ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' ?>"></i>
                    <span class="font-semibold text-sm">Tambah Kenangan</span>
                </a>

                <a href="<?= $base_url ?>/admin/places.php"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all group text-decoration-none
                   <?= ($current_page == 'places.php' || $current_page == 'places_add.php' || $current_page == 'places_edit.php') && $is_in_admin_folder ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <i class="fas fa-map-marked-alt w-5 text-center <?= $current_page == 'places.php' ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' ?>"></i>
                    <span class="font-semibold text-sm">Manage Places</span>
                </a>

                <a href="<?= $base_url ?>/admin/places_add.php"
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all group text-decoration-none
                    <?= $current_page == 'places_add.php' ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
    
                    <i class="fas fa-plus w-5 text-center <?= $current_page == 'places_add.php' ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' ?>"></i>
    
                    <span class="font-semibold text-sm">Tambah Places</span>
                </a>

                <a href="<?= $base_url ?>/admin/profile.php"
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all group text-decoration-none
                    <?= $current_page == 'profile.php' ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
    
                    <i class="fas fa-user-circle w-5 text-center <?= $current_page == 'profile.php' ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' ?>"></i>
    
                    <span class="font-semibold text-sm">Profil Saya</span>
                </a>
                

                <div class="pt-4 mt-4 border-t border-slate-100">
                    <a href="<?= $base_url ?>/memories.php" class="flex items-center space-x-3 px-4 py-3 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all group text-decoration-none">
                        <i class="fas fa-external-link-alt w-5 text-center text-indigo-400 group-hover:text-indigo-600"></i>
                        <span class="font-bold text-sm">Visitor View</span>
                    </a>
                </div>

            <?php else: ?>
                <!-- ================= VISITOR MENU ================= -->
                <div class="px-4 mb-4 mt-2">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Menu Utama</p>
                </div>

                <a href="<?= $base_url ?>/memories.php"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all group text-decoration-none
                   <?= $current_page == 'memories.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <i class="fas fa-camera-retro w-5 text-center <?= $current_page == 'memories.php' ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' ?>"></i>
                    <span class="font-semibold text-sm">Our Memories</span>
                </a>

                <a href="<?= $base_url ?>/places.php"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all group text-decoration-none
                   <?= ($current_page == 'places.php' || $current_page == 'place_detail.php') && !$is_in_admin_folder ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <i class="fas fa-map-marker-alt w-5 text-center <?= ($current_page == 'places.php' || $current_page == 'place_detail.php') && !$is_in_admin_folder ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' ?>"></i>
                    <span class="font-semibold text-sm">Places to Go</span>
                </a>

                <a href="<?= $base_url ?>/meet_us.php"
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all group text-decoration-none
                   <?= $current_page == 'meet_us.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <i class="fas fa-user-friends w-5 text-center <?= $current_page == 'meet_us.php' ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' ?>"></i>
                    <span class="font-semibold text-sm">Meet Us</span>
                </a>

                <?php if ($is_admin): ?>
                <div class="pt-4 mt-4 border-t border-slate-100">
                    <a href="<?= $base_url ?>/admin/dashboard.php" class="flex items-center space-x-3 px-4 py-3 text-slate-600 hover:bg-slate-100 rounded-xl transition-all group text-decoration-none">
                        <i class="fas fa-user-shield w-5 text-center text-slate-400 group-hover:text-slate-900"></i>
                        <span class="font-bold text-sm">Admin Panel</span>
                    </a>
                </div>
                <?php endif; ?>

            <?php endif; ?>

        </nav>
    </div>

    <!-- PROFILE/BOTTOM SECTION -->
    <div class="p-4 border-t border-slate-100">
        <?php if ($is_admin): ?>
            <?php
            $user_name = $_SESSION['user_name'] ?? 'Admin';
            require_once __DIR__ . '/db.php';
            $u_q = mysqli_query($conn, "SELECT profile_pic FROM users WHERE username = '" . mysqli_real_escape_string($conn, strtolower($user_name)) . "'");
            $u_d = mysqli_fetch_assoc($u_q);
            $p_pic = ($u_d && !empty($u_d['profile_pic'])) ? $base_url . '/uploads/' . $u_d['profile_pic'] : "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=random";
            ?>
            <div class="bg-slate-50 rounded-2xl p-4 mb-3">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 rounded-full overflow-hidden ring-2 ring-white shadow-sm">
                        <img src="<?= $p_pic ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate"><?= $user_name ?></p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Online</p>
                    </div>
                </div>
                <a href="<?= $base_url ?>/logout.php" class="flex items-center justify-center space-x-2 w-full py-2 bg-white text-red-500 rounded-xl text-xs font-bold border border-red-50 hover:bg-red-50 transition-all text-decoration-none">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        <?php else: ?>
            <a href="<?= $base_url ?>/index.php" class="flex items-center space-x-3 px-4 py-3 text-slate-500 hover:text-indigo-600 transition-all text-sm font-bold text-decoration-none">
                <i class="fas fa-sign-in-alt w-5 text-center"></i>
                <span>Kembali ke Landing Page</span>
            </a>
        <?php endif; ?>
        
        
    </div>
</aside>

<!-- Mobile Overlay -->
<div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 z-[55] hidden md:hidden transition-all duration-300 backdrop-blur-sm"></div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isHidden = sidebar.classList.contains('-translate-x-full');
    
    if (isHidden) {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }
}
</script>