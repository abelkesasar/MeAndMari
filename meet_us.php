<?php
require 'db.php';

// Fetch User Profiles
$res_abel = mysqli_query($conn, "SELECT profile_pic FROM users WHERE username = 'abel'");
$abel_p = mysqli_fetch_assoc($res_abel);

$res_mari = mysqli_query($conn, "SELECT profile_pic FROM users WHERE username = 'mari'");
$mari_p = mysqli_fetch_assoc($res_mari);

// Fetch All Media (Combined)
$all_media = [];
$res = mysqli_query($conn, "SELECT photo FROM memories WHERE photo != '' ORDER BY created_at DESC LIMIT 12");
while($r = mysqli_fetch_assoc($res)) $all_media[] = ['type' => 'photo', 'url' => $r['photo']];

$res = mysqli_query($conn, "SELECT photo FROM memory_photos ORDER BY id DESC LIMIT 12");
while($r = mysqli_fetch_assoc($res)) $all_media[] = ['type' => 'photo', 'url' => $r['photo']];

// Data dummy biodata
$abel_bio = "Halo! Aku Abel. Senang berbagi momen indah bersamamu.";
$mari_bio = "Hai! Aku Mari. Mari kita buat lebih banyak kenangan manis.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meet Us - Me and Mari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    
    <?php include 'sidebar.php'; ?>

    <main class="md:ml-64 px-4 py-12">
        <header class="text-center mb-16 space-y-4">
            <h1 class="text-4xl font-bold text-slate-900 tracking-tight">Meet the Souls ❤️</h1>
            <p class="text-slate-500 max-w-lg mx-auto font-medium text-lg">Every story has its heroes. Here we are.</p>
        </header>

        <div class="max-w-4xl mx-auto space-y-8">
            <!-- Abel Profile -->
            <section class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-100 flex flex-col md:flex-row gap-10">
                <div class="flex-shrink-0 text-center">
                    <div class="w-40 h-40 rounded-3xl overflow-hidden border-4 border-indigo-50 shadow-xl mx-auto rotate-3">
                        <?php if(!empty($abel_p['profile_pic'])): ?>
                            <img src="uploads/<?php echo $abel_p['profile_pic']; ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-indigo-600 flex items-center justify-center text-white text-5xl font-bold">A</div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-6 inline-block px-4 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold tracking-widest uppercase">The Boy</div>
                </div>
                
                <div class="flex-1 space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="text-3xl font-bold text-slate-900">Abel</h2>
                        <p class="text-indigo-500 font-medium italic">"Life is better with you."</p>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Birthday</span>
                            <p class="text-slate-700 font-semibold">01 January 2000</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Favorite Food</span>
                            <p class="text-slate-700 font-semibold">Nasi Goreng</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Hobbies</span>
                            <p class="text-slate-700 font-semibold">Coding & Gaming</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Zodiac</span>
                            <p class="text-slate-700 font-semibold">Capricorn</p>
                        </div>
                    </div>

                    <div class="pt-4">
                        <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block mb-2">A Message</span>
                        <p class="text-slate-600 leading-relaxed italic bg-slate-50 p-4 rounded-2xl border border-slate-100"><?php echo $abel_bio; ?></p>
                    </div>
                </div>
            </section>

            <!-- Mari Profile -->
            <section class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-100 flex flex-col md:flex-row-reverse gap-10">
                <div class="flex-shrink-0 text-center">
                    <div class="w-40 h-40 rounded-3xl overflow-hidden border-4 border-pink-50 shadow-xl mx-auto -rotate-3">
                        <?php if(!empty($mari_p['profile_pic'])): ?>
                            <img src="uploads/<?php echo $mari_p['profile_pic']; ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-pink-500 flex items-center justify-center text-white text-5xl font-bold">M</div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-6 inline-block px-4 py-1 bg-pink-100 text-pink-700 rounded-full text-xs font-bold tracking-widest uppercase">The Girl</div>
                </div>
                
                <div class="flex-1 space-y-6">
                    <div class="border-b border-slate-100 pb-4 md:text-right">
                        <h2 class="text-3xl font-bold text-slate-900">Mari</h2>
                        <p class="text-pink-500 font-medium italic">"Always and forever."</p>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm md:text-right">
                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Birthday</span>
                            <p class="text-slate-700 font-semibold">01 January 2000</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Favorite Drink</span>
                            <p class="text-slate-700 font-semibold">Matcha Latte</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Hobbies</span>
                            <p class="text-slate-700 font-semibold">Reading & Music</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Zodiac</span>
                            <p class="text-slate-700 font-semibold">Virgo</p>
                        </div>
                    </div>

                    <div class="pt-4 md:text-right">
                        <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block mb-2">A Message</span>
                        <p class="text-slate-600 leading-relaxed italic bg-slate-50 p-4 rounded-2xl border border-slate-100"><?php echo $mari_bio; ?></p>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
