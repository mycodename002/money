<?php 
require_once "../../API/functions.php";
require_once '../TEMPLATES/user/head_user.php';
require_once '../TEMPLATES/user/nav_user.php';
require_once "../../API/alarmAndNotify.php";
include_once "../../db.php";

if(!isset($_SESSION['auth']['rule']) || $_SESSION['auth']['rule'] != 'support') {
    header('Location: ' . base_url('index.php')); 
    exit();
}
?>

<?php


$sql = "SELECT e.id, e.title, e.details  FROM `events` AS e  WHERE is_deleted = 1";

$datas = select($conn,$sql);

?>
<div class="container mx-auto px-4 mt-4 shadow-md ">
    <ul class="list bg-base-100 rounded-box shadow-md">
  
  <li class="p-4 pb-2 text-lg opacity-60 tracking-wide">รายการ</li>
  <?php 
 if(!isset($_SESSION['auth']['rule']) || $_SESSION['auth']['rule'] != 'support') {
    header('Location: ' . base_url('index.php')); 
    exit();
}

$page = 1;
if(isset($_GET['page'])){
    $page = (int)$_GET['page'];
    }
    $count_max = select($conn, "SELECT COUNT(e.id) AS C FROM `events` AS e  WHERE is_deleted = 1;");

$count_max = $count_max[0]['C'];
$start = ($page-1)*6; 

$sql = "SELECT id, fname, lname, user FROM `members` WHERE is_deleted = 1 LIMIT $start,6;";
$members = select($conn,$sql);

// 2. ดึงข้อมูลรายการ (Events) ที่ถูกลบ
$sql = "SELECT e.id, e.title, e.details  FROM `events` AS e WHERE is_deleted = 1 LIMIT $start,6;";
$events = select($conn,$sql);

// 3. ดึงข้อมูลกลุ่ม ที่ถูกลบ
$sql = "SELECT id, title, details FROM `group_mem` WHERE is_deleted = 1 LIMIT $start,6;";
$groups = select($conn,$sql);
?>

<div class="container mx-auto px-4 mt-6 space-y-6">

    <!-- 1. ส่วนแสดงข้อมูลสมาชิกที่ถูกลบ -->
    <div class="bg-base-100 rounded-box shadow-md p-4 border border-base-200">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-lg font-bold text-primary"> รายการสมาชิกที่ถูกลบ </h2>
        </div>
        <?php if (!empty($members)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($members as $m): ?>
                    <div class="p-3 bg-base-200 rounded-lg flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-sm"><?= htmlspecialchars($m['fname'] . ' ' . $m['lname'] ?? 'ไม่ระบุชื่อ') ?></p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($m['user'] ?? '-') ?></p>
                        </div>
                        <a href="<?= base_url('VIEW/SUPPORT/') ?>showMember.php?id=<?= $m['id'] ?>" class="btn btn-xs btn-primary">ดูข้อมูล</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-gray-400 py-2">ไม่มีข้อมูลสมาชิกที่ถูกลบ</p>
        <?php endif; ?>
    </div>

    <!-- 2. ส่วนแสดงข้อมูลรายการ (Events) ที่ถูกลบ -->
    <div class="bg-base-100 rounded-box shadow-md p-4 border border-base-200">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-lg font-bold text-secondary"> รายการกิจกรรม (Events) ที่ถูกลบ </h2>
        </div>
        <?php if (!empty($events)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($events as $e): ?>
                    <div class="p-3 bg-base-200 rounded-lg flex justify-between items-center">
                        <div class="truncate mr-2">
                            <p class="font-semibold text-sm truncate"><?= htmlspecialchars($e['title'] ?? 'ไม่มีชื่อหัวข้อ') ?></p>
                            <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($e['details'] ?? '-') ?></p>
                        </div>
                        <a href="<?= base_url('VIEW/SUPPORT/') ?>showEvent.php?id=<?= $e['id'] ?>" class="btn btn-xs btn-secondary">ดูข้อมูล</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-gray-400 py-2">ไม่มีข้อมูลรายการกิจกรรมที่ถูกลบ</p>
        <?php endif; ?>
    </div>

    <!-- 3. ส่วนแสดงข้อมูลกลุ่ม ที่ถูกลบ -->
    <div class="bg-base-100 rounded-box shadow-md p-4 border border-base-200">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-lg font-bold text-accent"> รายการกลุ่ม ที่ถูกลบ </h2>
        </div>
        <?php if (!empty($groups)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($groups as$g): ?>
                    <div class="p-3 bg-base-200 rounded-lg flex justify-between items-center">
                        <div class="truncate mr-2">
                            <p class="font-semibold text-sm truncate"><?= htmlspecialchars($g['name'] ?? 'ไม่มีชื่อกลุ่ม') ?></p>
                            <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($g['details'] ?? '-') ?></p>
                        </div>
                        <a href="<?= base_url('VIEW/SUPPORT/') ?>ShowGroup.php?id=<?= $g['id'] ?>" class="btn btn-xs btn-accent">ดูข้อมูล</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-gray-400 py-2">ไม่มีข้อมูลกลุ่มที่ถูกลบ</p>
        <?php endif; ?>
    </div>

</div>
