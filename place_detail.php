<?php
require 'db.php';

if (!isset($_GET['id'])) {
    header('Location: places.php');
    exit;
}

$id = intval($_GET['id']);

// DATA PLACE
$place = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM places WHERE id=$id"));
if (!$place) {
    header('Location: places.php');
    exit;
}

// QUERY
$images = mysqli_query($conn, "SELECT * FROM place_images WHERE place_id=$id");

$memories = mysqli_query($conn, "
    SELECT m.* 
    FROM memories m
    JOIN place_memories pm ON m.id = pm.memory_id
    WHERE pm.place_id = $id
");

$memory_photos = mysqli_query($conn, "
    SELECT mp.photo
    FROM memory_photos mp
    JOIN place_memories pm ON mp.memory_id = pm.memory_id
    WHERE pm.place_id = $id
");

// =====================
// FIX COVER (ANTI BLANK)
// =====================
$cover = '';

$tmp_img = mysqli_query($conn, "SELECT image FROM place_images WHERE place_id=$id LIMIT 1");
if(mysqli_num_rows($tmp_img) > 0){
    $cover = mysqli_fetch_assoc($tmp_img)['image'];
}else{
    $tmp_mem = mysqli_query($conn, "
        SELECT mp.photo 
        FROM memory_photos mp
        JOIN place_memories pm ON mp.memory_id = pm.memory_id
        WHERE pm.place_id = $id LIMIT 1
    ");
    if(mysqli_num_rows($tmp_mem) > 0){
        $cover = mysqli_fetch_assoc($tmp_mem)['photo'];
    }
}

if($cover == ''){
    $cover = 'default.jpg';
}

// =====================
// LIGHTBOX DATA
// =====================
$media_items = [];

mysqli_data_seek($images, 0);
while($i = mysqli_fetch_assoc($images)){
    $media_items[] = ['src'=>'uploads/'.$i['image']];
}

mysqli_data_seek($memory_photos, 0);
while($m = mysqli_fetch_assoc($memory_photos)){
    $media_items[] = ['src'=>'uploads/'.$m['photo']];
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= $place['name'] ?></title>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; }

/* LIGHTBOX */
#lightbox {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.9);
    z-index:9999;
    align-items:center;
    justify-content:center;
}
#lightbox.active{display:flex;}

#lightbox img{
    max-width:85vw;
    max-height:85vh;
    border-radius:20px;
}

.nav-btn{
    position:fixed;
    top:50%;
    transform:translateY(-50%);
    background:white;
    width:45px;height:45px;
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;
    font-size:20px;
}
#prev{left:20px;}
#next{right:20px;}
</style>
</head>

<body class="bg-slate-50">

<?php include 'sidebar.php'; ?>

<main class="md:ml-60 px-6 py-12 max-w-6xl mx-auto">

<div class="bg-white rounded-[3rem] shadow-xl overflow-hidden">

<!-- HERO -->
<div class="h-[400px] overflow-hidden">
<img src="uploads/<?= $cover ?>" class="w-full h-full object-cover">
</div>

<div class="p-10 space-y-16">

<!-- TITLE -->
<div class="text-center space-y-3">
<h1 class="text-4xl font-bold"><?= $place['name'] ?></h1>
<p class="text-slate-500"><?= $place['location'] ?></p>
</div>

<!-- DESCRIPTION + MAP -->
<div class="max-w-3xl mx-auto space-y-10">

<p class="text-center italic text-lg text-slate-600">
"<?= nl2br($place['description']) ?>"
</p>

<?php if($place['maps_link']): ?>
<div class="rounded-[2rem] overflow-hidden border border-slate-100 shadow-sm">

<div class="relative group">

<iframe 
    src="https://www.google.com/maps?q=<?= urlencode($place['location']) ?>&output=embed"
    width="100%" 
    height="260"
    style="border:0;"
    class="w-full h-[260px] object-cover">
</iframe>

<a href="<?= $place['maps_link'] ?>" target="_blank"
   class="absolute inset-0 flex items-center justify-center bg-black/20">

    <div class="px-6 py-3 bg-white/90 rounded-xl font-semibold text-indigo-600 shadow">
        📍 Open in Google Maps
    </div>

</a>

</div>

</div>
<?php endif; ?>

</div>

<!-- GALLERY -->
<div>
<h2 class="text-2xl font-bold mb-6">Gallery</h2>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

<?php
mysqli_data_seek($images,0);
$idx=0;
while($i = mysqli_fetch_assoc($images)): ?>
<div onclick="openLightbox(<?= $idx++ ?>)" class="cursor-pointer aspect-square rounded-2xl overflow-hidden">
<img src="uploads/<?= $i['image'] ?>" class="w-full h-full object-cover hover:scale-110 transition">
</div>
<?php endwhile; ?>

<?php
mysqli_data_seek($memory_photos,0);
while($m = mysqli_fetch_assoc($memory_photos)): ?>
<div onclick="openLightbox(<?= $idx++ ?>)" class="cursor-pointer aspect-square rounded-2xl overflow-hidden">
<img src="uploads/<?= $m['photo'] ?>" class="w-full h-full object-cover hover:scale-110 transition">
</div>
<?php endwhile; ?>

</div>
</div>

<!-- MEMORIES -->
<div class="space-y-8">

<h3 class="text-2xl font-bold flex items-center">
<i class="fas fa-heart mr-3 text-indigo-600"></i> Memories Here
</h3>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

<?php while($m = mysqli_fetch_assoc($memories)): ?>

<a href="detail.php?id=<?= $m['id'] ?>" class="group block">

<div class="bg-white rounded-[2rem] overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl transition">

<div class="aspect-[4/5] overflow-hidden">
<img src="uploads/<?= $m['photo'] ?>" 
     class="w-full h-full object-cover group-hover:scale-110 transition">
</div>

<div class="p-5 space-y-2">
<h5 class="font-bold text-slate-800"><?= $m['title'] ?></h5>
<p class="text-sm text-slate-500"><?= $m['location'] ?></p>
</div>

</div>

</a>

<?php endwhile; ?>

</div>

</div>

</div>
</div>

</main>

<!-- LIGHTBOX -->
<div id="lightbox">
<div id="prev" class="nav-btn" onclick="prev()">‹</div>
<img id="lightbox-img">
<div id="next" class="nav-btn" onclick="next()">›</div>
</div>

<script>
const media = <?= json_encode($media_items) ?>;
let current = 0;

function openLightbox(i){
current=i;
show();
document.getElementById('lightbox').classList.add('active');
}

function show(){
document.getElementById('lightbox-img').src = media[current].src;
}

function prev(){
current=(current-1+media.length)%media.length;
show();
}

function next(){
current=(current+1)%media.length;
show();
}

document.getElementById('lightbox').onclick = function(e){
if(e.target.id==='lightbox') this.classList.remove('active');
}
</script>

</body>
</html>