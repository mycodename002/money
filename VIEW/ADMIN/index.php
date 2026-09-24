<?php require_once "../../API/functions.php";
require_once '../TEMPLATES/user/head_user.php';
require_once '../TEMPLATES/user/nav_user.php';
require_once "../../API/alarmAndNotify.php";
include_once "../../db.php";
if(!($_SESSION['auth']['rule'] == 'admin')) {header(base_url('index.php')); exit();}

?>

<?php 


$admin_group = $_SESSION['auth']['admin_group']; 
$sql = "SELECT e.id, e.title, e.details  FROM `events` AS e  WHERE e.id_group_admin = $admin_group AND e.is_deleted = 0 AND e.is_success = 0 ;";

$datas = select($conn,$sql);

?>
<div class="container mx-auto px-4 mt-4 ">
    <ul class="list bg-base-100 rounded-box shadow-md">
  
  <li class="p-4 pb-2 text-ml opacity-60 tracking-wide">รายการ ดำเนินการอยู่</li>
  <?php 
  if(empty($datas)){
    echo '<li class="p-4 pb-2 text-xs opacity-60 tracking-wide">';
      echo '<div class="text-xs uppercase font-semibold opacity-60">ยังไม่มีรายการที่ดำเนินการอยู่</div>';
      echo '</li>';
      // exit;
  }
  foreach($datas  as $row){?>
  <li class="list-row flex items-center justify-between cursor-pointer" onclick="window.location.href='<?php echo base_url('VIEW/ADMIN/detail.php?id=').$row['id'] ?>'">
    <div>
        <div><?php echo $row['title'];?></div>
        <div class="text-xs uppercase font-semibold opacity-60"><?php echo $row['details']; ?></div>
    </div>
        <div class="badge badge-soft badge-success ml-auto">ดำเนินการอยู่</div>

</li>
  <?php } ?>
</ul>
</div>


<?php
$datasuccess = select($conn,"SELECT e.id, e.title, e.details  FROM `events` AS e  WHERE e.id_group_admin = $admin_group AND e.is_deleted = 0 AND e.is_success = 1 ;");
?>
<div class="container mx-auto px-4 mt-4">
    <ul class="list bg-base-100 rounded-box shadow-md">
  
  <li class="p-4 pb-2 text-ml opacity-60 tracking-wide">รายการ ดำเนินการเสร็จสิ้น</li>
  <?php 
  if(empty($datasuccess)){
    echo '<li class="p-4 pb-2 text-xs opacity-60 tracking-wide">';
      echo '<div class="text-xs uppercase font-semibold opacity-60">ยังไม่มีรายการที่ดำเนินการเสร็จสิ้น</div>';
      echo '</li>';
      // exit;
  }
  foreach($datasuccess  as $row){?>
  <li class="list-row flex items-center justify-between cursor-pointer" onclick="window.location.href='<?php echo base_url('VIEW/ADMIN/detail.php?id=').$row['id'] ?>'">
      <div>
          <div><?php echo $row['title'];?></div>
          <div class="text-xs uppercase font-semibold opacity-60"><?php echo $row['details']; ?></div>
      </div>
      <div class="badge badge-soft badge-error ml-auto">ดำเนินการเสร็จสิ้น</div>
  </li>
  <?php } ?>
</ul>
</div>

<?php require_once './modal.php'; ?>