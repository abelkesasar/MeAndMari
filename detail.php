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
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Lightbox Overlay */
        #lightbox {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s ease;
        }
        #lightbox.active {
            display: flex;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes scaleIn {
            from { transform: scale(0.88); opacity: 0; }
            to   { transform: scale(1);    opacity: 1; }
        }
        #lightbox-content {
            position: relative;
            max-width: 92vw;
            max-height: 90vh;
            animation: scaleIn 0.25s cubic-bezier(.34,1.56,.64,1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #lightbox-img {
            max-width: 88vw;
            max-height: 85vh;
            border-radius: 1.5rem;
            object-fit: contain;
            box-shadow: 0 30px 80px rgba(0,0,0,0.6);
            display: none;
        }
        #lightbox-video {
            max-width: 88vw;
            max-height: 85vh;
            border-radius: 1.5rem;
            box-shadow: 0 30px 80px rgba(0,0,0,0.6);
            display: none;
            background: #000;
        }
        #lightbox-close {
            position: fixed;
            top: 1.25rem;
            right: 1.5rem;
            width: 2.75rem;
            height: 2.75rem;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            color: #fff;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, transform 0.2s;
            z-index: 10001;
        }
        #lightbox-close:hover { background: rgba(255,255,255,0.25); transform: scale(1.1); }

        #lightbox-prev, #lightbox-next {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            width: 3rem;
            height: 3rem;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, transform 0.2s;
            z-index: 10001;
        }
        #lightbox-prev { left: 1rem; }
        #lightbox-next { right: 1rem; }
        #lightbox-prev:hover, #lightbox-next:hover { background: rgba(255,255,255,0.25); transform: translateY(-50%) scale(1.1); }

        #lightbox-counter {
            position: fixed;
            bottom: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255,255,255,0.6);
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            z-index: 10001;
        }

        /* Clickable media items */
        .media-clickable {
            cursor: zoom-in;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    
    <?php include 'sidebar.php'; ?>

    <button onclick="toggleSidebar()" class="md:hidden fixed top-6 left-6 z-40 w-12 h-12 bg-white rounded-2xl shadow-lg border border-slate-100 flex items-center justify-center text-slate-600 hover:text-indigo-600 transition-all">
        <i class="fas fa-bars text-xl"></i>
    </button>

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
                    
                    <!-- Build media list for JS -->
                    <?php
                    $media_items = [];
                    if (!empty($memory['video'])) {
                        $media_items[] = ['type' => 'video', 'src' => 'uploads/' . $memory['video']];
                    }
                    mysqli_data_seek($videos_result, 0);
                    while($v = mysqli_fetch_assoc($videos_result)) {
                        $media_items[] = ['type' => 'video', 'src' => 'uploads/' . $v['video']];
                    }
                    mysqli_data_seek($photos_result, 0);
                    while($p = mysqli_fetch_assoc($photos_result)) {
                        $media_items[] = ['type' => 'photo', 'src' => 'uploads/' . $p['photo']];
                    }
                    ?>
                    <script>
                        const mediaItems = <?php echo json_encode($media_items); ?>;
                    </script>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Main Video -->
                        <?php if (!empty($memory['video'])): 
                            $idx = 0;
                        ?>
                        <div class="media-clickable group relative rounded-[2rem] overflow-hidden shadow-xl border border-slate-100 bg-black aspect-video md:aspect-square flex items-center"
                             onclick="openLightbox(<?php echo $idx; ?>)">
                            <video class="w-full pointer-events-none">
                                <source src="uploads/<?php echo $memory['video']; ?>" type="video/<?php echo pathinfo($memory['video'], PATHINFO_EXTENSION); ?>">
                            </video>
                            <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/50 transition-all duration-300">
                                <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm border border-white/40 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-play text-white text-xl ml-1"></i>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Extra Videos -->
                        <?php 
                        mysqli_data_seek($videos_result, 0);
                        $extra_v_idx = !empty($memory['video']) ? 1 : 0;
                        while($v = mysqli_fetch_assoc($videos_result)): ?>
                        <div class="media-clickable group relative rounded-[2rem] overflow-hidden shadow-xl border border-slate-100 bg-black aspect-video md:aspect-square flex items-center"
                             onclick="openLightbox(<?php echo $extra_v_idx; $extra_v_idx++; ?>)">
                            <video class="w-full pointer-events-none">
                                <source src="uploads/<?php echo $v['video']; ?>" type="video/<?php echo pathinfo($v['video'], PATHINFO_EXTENSION); ?>">
                            </video>
                            <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/50 transition-all duration-300">
                                <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm border border-white/40 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-play text-white text-xl ml-1"></i>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>

                        <!-- Photos -->
                        <?php 
                        mysqli_data_seek($photos_result, 0);
                        $photo_start_idx = count(array_filter($media_items, fn($m) => $m['type'] === 'video'));
                        $p_idx = $photo_start_idx;
                        while($p = mysqli_fetch_assoc($photos_result)): 
                        ?>
                        <div class="media-clickable group relative aspect-square rounded-[2rem] overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500"
                             onclick="openLightbox(<?php echo $p_idx; $p_idx++; ?>)">
                            <img src="uploads/<?php echo $p['photo']; ?>" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                 alt="Gallery Photo">
                            <div class="absolute inset-0 bg-indigo-900/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm border border-white/40 flex items-center justify-center opacity-0 group-hover:opacity-100 scale-75 group-hover:scale-100 transition-all duration-300">
                                    <i class="fas fa-search-plus text-white text-sm"></i>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Lightbox Modal -->
    <div id="lightbox" role="dialog" aria-modal="true" aria-label="Media viewer">
        <button id="lightbox-close" onclick="closeLightbox()" title="Tutup (Esc)">
            <i class="fas fa-times"></i>
        </button>
        <button id="lightbox-prev" onclick="prevMedia()" title="Sebelumnya">
            <i class="fas fa-chevron-left"></i>
        </button>
        <div id="lightbox-content">
            <img id="lightbox-img" src="" alt="Foto">
            <video id="lightbox-video" controls playsinline>
                <source id="lightbox-video-src" src="" type="video/mp4">
            </video>
        </div>
        <button id="lightbox-next" onclick="nextMedia()" title="Berikutnya">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div id="lightbox-counter"></div>
    </div>

    <script>
        let currentIndex = 0;

        function openLightbox(index) {
            currentIndex = index;
            showMedia(currentIndex);
            const lb = document.getElementById('lightbox');
            lb.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const lb = document.getElementById('lightbox');
            lb.classList.remove('active');
            document.body.style.overflow = '';
            // Pause video if playing
            const vid = document.getElementById('lightbox-video');
            vid.pause();
        }

        function showMedia(index) {
            const item = mediaItems[index];
            const img = document.getElementById('lightbox-img');
            const vid = document.getElementById('lightbox-video');
            const src = document.getElementById('lightbox-video-src');
            const counter = document.getElementById('lightbox-counter');

            // Reset animation
            const content = document.getElementById('lightbox-content');
            content.style.animation = 'none';
            content.offsetHeight; // reflow
            content.style.animation = 'scaleIn 0.25s cubic-bezier(.34,1.56,.64,1)';

            if (item.type === 'photo') {
                img.src = item.src;
                img.style.display = 'block';
                vid.pause();
                vid.style.display = 'none';
            } else {
                src.src = item.src;
                vid.load();
                vid.style.display = 'block';
                img.style.display = 'none';
                img.src = '';
            }

            counter.textContent = (index + 1) + ' / ' + mediaItems.length;

            // Show/hide nav buttons
            document.getElementById('lightbox-prev').style.display = mediaItems.length > 1 ? 'flex' : 'none';
            document.getElementById('lightbox-next').style.display = mediaItems.length > 1 ? 'flex' : 'none';
        }

        function prevMedia() {
            const vid = document.getElementById('lightbox-video');
            vid.pause();
            currentIndex = (currentIndex - 1 + mediaItems.length) % mediaItems.length;
            showMedia(currentIndex);
        }

        function nextMedia() {
            const vid = document.getElementById('lightbox-video');
            vid.pause();
            currentIndex = (currentIndex + 1) % mediaItems.length;
            showMedia(currentIndex);
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const lb = document.getElementById('lightbox');
            if (!lb.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') prevMedia();
            if (e.key === 'ArrowRight') nextMedia();
        });

        // Click outside content to close
        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) closeLightbox();
        });
    </script>

    <footer class="py-12 border-t border-slate-200 mt-20">
        <div class="text-center space-y-2">
            <p class="text-slate-400 text-xs font-bold tracking-[0.2em] uppercase">Built for Memories</p>
            <p class="text-slate-300 text-xs">© 2026 Abel's Project</p>
        </div>
    </footer>
</body>
</html>
