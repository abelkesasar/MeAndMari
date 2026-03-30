<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white border-r border-slate-200 z-[60] transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl md:shadow-none">
    <div class="p-6">
        <div class="flex items-center justify-between mb-10">
            <a href="index.php" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-heart text-xs"></i>
                </div>
                <span class="font-bold text-slate-800 tracking-tight">Me & Mari</span>
            </a>
            <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="space-y-2">
            <a href="memories.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'memories.php' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <i class="fas fa-camera-retro w-5"></i>
                <span>Memories</span>
            </a>
            <a href="meet_us.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'meet_us.php' ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <i class="fas fa-user-friends w-5"></i>
                <span>Meet Us</span>
            </a>
        </nav>
    </div>

    <div class="absolute bottom-0 left-0 w-full p-6">
        <a href="index.php" class="flex items-center space-x-3 px-4 py-3 text-slate-400 hover:text-slate-600 transition-all text-sm">
            <i class="fas fa-arrow-left w-5"></i>
            <span>Back to Home</span>
        </a>
    </div>
</aside>

<!-- Mobile Overlay -->
<div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-[55] hidden md:hidden transition-opacity duration-300 backdrop-blur-sm"></div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isHidden = sidebar.classList.contains('-translate-x-full');
    
    if (isHidden) {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
    }
}
</script>
