<?php
session_start();
require 'db.php';
$user_type = isset($_GET['user']) ? $_GET['user'] : 'Admin';

// Fetch user data from DB
$u_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '" . mysqli_real_escape_string($conn, strtolower($user_type)) . "'");
$user_db = mysqli_fetch_assoc($u_query);

// Fetch all memory photos for background
$bg_photos = [];
$res_memories = mysqli_query($conn, "SELECT photo FROM memories WHERE photo != ''");
while($row = mysqli_fetch_assoc($res_memories)) $bg_photos[] = $row['photo'];

$res_extra = mysqli_query($conn, "SELECT photo FROM memory_photos");
while($row = mysqli_fetch_assoc($res_extra)) $bg_photos[] = $row['photo'];

shuffle($bg_photos);
// Duplicate photos to ensure smooth scrolling
$display_photos = array_merge($bg_photos, $bg_photos, $bg_photos);

if (isset($_POST['login'])) {
    $password = $_POST['password'];
    if ($password === 'paskal14022025') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['user_name'] = ucfirst($user_type);
        header('Location: admin/dashboard.php');
        exit;
    } else {
        $error = "Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Me and Mari</title>
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
            <div class="text-center">
            <?php
            $profile_pic = "https://ui-avatars.com/api/?name=" . $user_type . "&background=random&size=128";
            if (!empty($user_db['profile_pic'])) {
                $profile_pic = 'uploads/' . $user_db['profile_pic'];
            } else {
                if (strtolower($user_type) == 'abel') {
                    $profile_pic = "https://ui-avatars.com/api/?name=Abel&background=4f46e5&color=fff&size=128";
                } elseif (strtolower($user_type) == 'mari') {
                    $profile_pic = "https://ui-avatars.com/api/?name=Mari&background=ec4899&color=fff&size=128";
                }
            }
            ?>
            <div class="mb-4 flex justify-center">
                <div class="w-24 h-24 rounded-full border-4 border-indigo-50 shadow-lg overflow-hidden">
                    <img src="<?php echo $profile_pic; ?>" alt="Profile" class="w-full h-full object-cover">
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">Login as <?php echo ucfirst($user_type); ?></h3>
            <p class="text-slate-500 text-sm mt-1">Masukkan password untuk masuk ke dashboard</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                <input type="password" name="password" 
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" 
                    placeholder="••••••••" required autofocus>
            </div>

            <button type="submit" name="login" 
                class="w-full py-4 bg-indigo-600 text-white rounded-xl font-semibold shadow-lg shadow-indigo-200 transition-all hover:bg-indigo-700 hover:scale-[1.01] active:scale-95">
                Masuk
            </button>

            <div class="text-center">
                <a href="index.php" class="text-sm font-medium text-slate-400 hover:text-slate-600 transition-colors">
                    ← Kembali ke Beranda
                </a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
