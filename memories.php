<?php
require 'db.php';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$query = "SELECT * FROM memories";
if ($search != '') {
    $query .= " WHERE title LIKE '%$search%' OR location LIKE '%$search%' OR description LIKE '%$search%'";
}
$query .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kenangan Kita - Me and Mari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    
    <?php include 'sidebar.php'; ?>

    <button onclick="toggleSidebar()" class="md:hidden fixed top-6 left-6 z-40 w-12 h-12 bg-white rounded-2xl shadow-lg border border-slate-100 flex items-center justify-center text-slate-600 hover:text-indigo-600 transition-all">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <main class="md:ml-64 px-4 py-12">
        <header class="text-center mb-16 space-y-4">
            <h1 class="text-4xl font-bold text-slate-900 tracking-tight">Some kind of our love story ❤️</h1>
            <p class="text-slate-500 max-w-lg mx-auto font-medium text-lg">Been through up and down, but we stay.</p>
            
            <div class="max-w-md mx-auto pt-6">
                <form method="GET" action="memories.php" class="relative group">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                        placeholder="Cari kenangan..." 
                        class="w-full pl-12 pr-6 py-4 rounded-2xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all bg-white shadow-sm group-hover:shadow-md">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                        <i class="fas fa-search"></i>
                    </div>
                </form>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <a href="detail.php?id=<?php echo $row['id']; ?>" class="group block h-full">
                        <div class="bg-white rounded-[2rem] overflow-hidden border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-100 hover:-translate-y-1 h-full flex flex-col">
                            <div class="relative aspect-[4/5] overflow-hidden">
                                <img src="uploads/<?php echo $row['photo']; ?>" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                     alt="Memory">
                                <div class="absolute top-4 right-4 z-10">
                                    <span class="px-4 py-2 bg-white/90 backdrop-blur-sm text-slate-900 rounded-full text-xs font-bold shadow-lg">
                                        <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                                    <p class="text-white text-sm font-medium"><i class="fas fa-calendar-alt mr-2 opacity-75"></i><?php echo date('d M Y', strtotime($row['created_at'])); ?></p>
                                </div>
                            </div>
                            <div class="p-8 flex-1 flex flex-col">
                                <div class="space-y-2 mb-4">
                                    <h5 class="text-xl font-bold text-slate-800 leading-snug"><?php echo $row['title']; ?></h5>
                                    <p class="text-indigo-600 text-sm font-semibold inline-flex items-center">
                                        <i class="fas fa-map-marker-alt mr-1.5 opacity-70"></i> 
                                        <?php echo $row['location']; ?>
                                    </p>
                                </div>
                                <p class="text-slate-600 leading-relaxed text-sm line-clamp-2 mb-6 flex-1">
                                    <?php echo $row['description']; ?>
                                </p>
                                <div class="pt-4 border-t border-slate-50 flex items-center justify-between mt-auto">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Happy Meter</span>
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-sm font-bold">
                                        <?php echo $row['happy_meter']; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full py-20 text-center space-y-4">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-300">
                        <i class="fas fa-camera text-3xl"></i>
                    </div>
                    <p class="text-slate-400 font-medium italic">Belum ada kenangan yang dibagikan.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="py-12 border-t border-slate-200 mt-20">
        <div class="text-center space-y-2">
            <p class="text-slate-400 text-xs font-bold tracking-[0.2em] uppercase">Built for Memories</p>
            <p class="text-slate-300 text-xs">Part of Abel's Portofolio</p>
        </div>
    </footer>
</body>
</html>
