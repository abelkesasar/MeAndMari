<?php
require 'db.php';
$users_query = mysqli_query($conn, "SELECT * FROM users");
$user_data = [];
while($u = mysqli_fetch_assoc($users_query)) {
    $user_data[$u['username']] = $u['profile_pic'];
}

// Fetch all memory photos for background
$bg_photos = [];
$res_memories = mysqli_query($conn, "SELECT photo FROM memories WHERE photo != ''");
while($row = mysqli_fetch_assoc($res_memories)) $bg_photos[] = $row['photo'];

$res_extra = mysqli_query($conn, "SELECT photo FROM memory_photos");
while($row = mysqli_fetch_assoc($res_extra)) $bg_photos[] = $row['photo'];

shuffle($bg_photos);
// Duplicate photos to ensure smooth scrolling
$display_photos = array_merge($bg_photos, $bg_photos, $bg_photos);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Me and Mari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; }
        .glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .bg-scroller {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px 0;
            opacity: 0.4;
            filter: grayscale(30%) blur(1px);
        }

        .scroll-row {
            display: flex;
            gap: 20px;
            width: fit-content;
        }

        .scroll-row-left { animation: scrollLeft 60s linear infinite; }
        .scroll-row-right { animation: scrollRight 65s linear infinite; }

        @keyframes scrollLeft {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        @keyframes scrollRight {
            0% { transform: translateX(-50%); }
            100% { transform: translateX(0); }
        }

        .bg-img {
            width: 300px;
            height: 220px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <!-- Animated Background -->
    <div class="bg-scroller">
        <?php for($i=0; $i<5; $i++): ?>
            <div class="scroll-row <?php echo ($i % 2 == 0) ? 'scroll-row-left' : 'scroll-row-right'; ?>">
                <?php foreach($display_photos as $p): ?>
                    <img src="uploads/<?php echo $p; ?>" class="bg-img">
                <?php endforeach; ?>
            </div>
        <?php endfor; ?>
    </div>

    <div class="min-h-screen flex flex-col items-center justify-center p-6 bg-white/30">
        
        <div class="max-w-md w-full glass rounded-3xl p-10 shadow-2xl shadow-indigo-100/50 text-center space-y-8">
            <div class="space-y-2">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200 mb-4">
                    <i class="fas fa-heart text-2xl animate-pulse"></i>
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-slate-800">Abel & Mari</h1>
                <p class="text-slate-500 font-medium italic">"Every moment with you is a treasure"</p>
            </div>

            <div class="space-y-4">
                <a href="memories.php" class="group relative flex items-center justify-center w-full py-4 bg-slate-900 text-white rounded-2xl font-semibold transition-all hover:bg-slate-800 hover:scale-[1.02] active:scale-95 shadow-xl">
                    <span>Lihat Kenangan Kita</span>
                    <i class="fas fa-arrow-right ml-2 text-sm transition-transform group-hover:translate-x-1"></i>
                </a>
                
                <div class="grid grid-cols-2 gap-4">
                    <?php
                    $abel_pic = !empty($user_data['abel']) ? 'uploads/'.$user_data['abel'] : "https://ui-avatars.com/api/?name=Abel&background=4f46e5&color=fff&size=128";
                    $mari_pic = !empty($user_data['mari']) ? 'uploads/'.$user_data['mari'] : "https://ui-avatars.com/api/?name=Mari&background=ec4899&color=fff&size=128";
                    ?>
                    <a href="login.php?user=abel" class="flex items-center justify-center py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-medium transition-all hover:bg-slate-50 hover:border-indigo-300 px-4">
                        <div class="w-8 h-8 rounded-full overflow-hidden mr-3 border border-slate-100">
                            <img src="<?php echo $abel_pic; ?>" class="w-full h-full object-cover">
                        </div>
                        Abel
                    </a>
                    <a href="login.php?user=mari" class="flex items-center justify-center py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-medium transition-all hover:bg-slate-50 hover:border-indigo-300 px-4">
                        <div class="w-8 h-8 rounded-full overflow-hidden mr-3 border border-slate-100">
                            <img src="<?php echo $mari_pic; ?>" class="w-full h-full object-cover">
                        </div>
                        Mari
                    </a>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-400 font-medium">© 2026 Abel's Project</p>
            </div>
        </div>

    </div>
</body>
</html>
