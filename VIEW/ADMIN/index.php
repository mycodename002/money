<?php require_once "../../API/functions.php";
require_once '../TEMPLATES/user/head_user.php';
require_once '../TEMPLATES/user/nav_user.php';
include_once "../../db.php";
if(!$_SESSION['auth']['rule'] == 'admin') {header(base_url('index.php')); exit();}

?>

<?php 

$admin_id = $_SESSION['auth']['id'];
$datas = select($conn,"SELECT e.titel, e.details FROM `group_admin` AS g JOIN `events` AS e ON g.id_event = e.id WHERE g.id_admin = '$admin_id' AND e.is_deleted = 0;")

?>
<div class="container mx-auto px-4 mt-4">
    <ul class="list bg-base-100 rounded-box shadow-md">
  
  <li class="p-4 pb-2 text-xs opacity-60 tracking-wide">รายการ</li>
  <?php foreach($datas  as $row){?>
  <li class="list-row">
    <div>
      <div><?php echo $row['titel'];?></div>
      <div class="text-xs uppercase font-semibold opacity-60"><?php echo $row['details']; ?></div>
    </div>
    
  </li>
  <?php } ?>
</ul>
</div>