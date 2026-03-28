<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="fixed left-0 top-0 h-full w-64 bg-white border-r border-slate-200 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
    <div class="p-6">
        <a href="index.php" class="flex items-center space-x-2 mb-10">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-heart text-xs"></i>
            </div>
            <span class="font-bold text-slate-800 tracking-tight">Me & Mari</span>
        </a>

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
