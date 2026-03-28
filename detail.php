<?php
require 'db.php';

if (!isset($_GET['id'])) {
    header('Location: memories.php');
    exit;
}

$id = intval($_GET['id']);
$query = "SELECT * FROM memories WHERE id = $id";
$result = mysqli_query($conn, $query);
$memory = mysqli_fetch_assoc($result);

if (!$memory) {
    header('Location: memories.php');
    exit;
}

// Get all photos for this memory
$photo_query = "SELECT * FROM memory_photos WHERE memory_id = $id";
$photos_result = mysqli_query($conn, $photo_query);

// Get all videos for this memory
$video_query = "SELECT * FROM memory_videos WHERE memory_id = $id";
$videos_result = mysqli_query($conn, $video_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $memory['title']; ?> - Me and Mari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    
    <?php include 'sidebar.php'; ?>

    <main class="md:ml-64 px-4 py-12">
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <!-- Header Image / Cover -->
            <div class="relative h-[400px] md:h-[500px] overflow-hidden">
                <img src="uploads/<?php echo $memory['photo']; ?>" class="w-full h-full object-cover" alt="Main Photo">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex items-end p-8 md:p-12">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md text-white rounded-full text-xs font-bold uppercase tracking-widest border border-white/30">
                                <?php echo date('d M Y', strtotime($memory['created_at'])); ?>
                            </span>
                            <span class="px-4 py-1.5 bg-indigo-600 text-white rounded-full text-xs font-bold border border-indigo-400">
                                <?php echo $memory['happy_meter']; ?>
                            </span>
                        </div>
                        <h1 class="text-4xl md:text-6xl font-bold text-white tracking-tight leading-tight">
                            <?php echo $memory['title']; ?>
                        </h1>
                        <p class="text-white/80 text-lg md:text-xl font-medium inline-flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-indigo-400"></i>
                            <?php echo $memory['location']; ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-16 space-y-16">
                <!-- Story -->
                <div class="max-w-2xl mx-auto">
                    <div class="flex items-center justify-center mb-8">
                        <div class="h-px bg-slate-100 flex-1"></div>
                        <div class="mx-6 text-slate-300"><i class="fas fa-quote-left"></i></div>
                        <div class="h-px bg-slate-100 flex-1"></div>
                    </div>
                    <p class="text-slate-600 text-lg md:text-xl leading-relaxed text-center font-medium italic">
                        "<?php echo nl2br($memory['description']); ?>"
                    </p>
                    <div class="flex items-center justify-center mt-8 text-slate-300">
                        <i class="fas fa-heart text-xs animate-pulse"></i>
                    </div>
                </div>

                <!-- Integrated Media Gallery -->
                <div class="space-y-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-slate-800 tracking-tight flex items-center">
                            <i class="fas fa-images mr-3 text-indigo-600"></i> Galeri Momen & Video
                        </h3>
                        <span class="text-slate-400 text-sm font-semibold uppercase tracking-widest">
                            Media Moments
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Main Video -->
                        <?php if (!empty($memory['video'])): ?>
                        <div class="rounded-[2rem] overflow-hidden shadow-xl border border-slate-100 bg-black aspect-video md:aspect-square flex items-center">
                            <video class="w-full" controls>
                                <source src="uploads/<?php echo $memory['video']; ?>" type="video/<?php echo pathinfo($memory['video'], PATHINFO_EXTENSION); ?>">
                            </video>
                        </div>
                        <?php endif; ?>

                        <!-- Extra Videos -->
                        <?php while($v = mysqli_fetch_assoc($videos_result)): ?>
                        <div class="rounded-[2rem] overflow-hidden shadow-xl border border-slate-100 bg-black aspect-video md:aspect-square flex items-center">
                            <video class="w-full" controls>
                                <source src="uploads/<?php echo $v['video']; ?>" type="video/<?php echo pathinfo($v['video'], PATHINFO_EXTENSION); ?>">
                            </video>
                        </div>
                        <?php endwhile; ?>

                        <!-- Photos -->
                        <?php 
                        mysqli_data_seek($photos_result, 0);
                        while($p = mysqli_fetch_assoc($photos_result)): 
                        ?>
                        <div class="group relative aspect-square rounded-[2rem] overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500">
                            <img src="uploads/<?php echo $p['photo']; ?>" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                 alt="Gallery Photo">
                            <div class="absolute inset-0 bg-indigo-900/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-12 border-t border-slate-200 mt-20">
        <div class="text-center space-y-2">
            <p class="text-slate-400 text-xs font-bold tracking-[0.2em] uppercase">Built for Memories</p>
            <p class="text-slate-300 text-xs">© 2026 Abel's Project</p>
        </div>
    </footer>
</body>
</html>
