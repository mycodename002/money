<?php require_once "../../API/functions.php";
require_once '../TEMPLATES/user/head_user.php';
require_once '../TEMPLATES/user/nav_user.php';
require_once "../../API/alarmAndNotify.php";
include_once "../../db.php";
if(!$_SESSION['auth']['rule'] == 'admin') {header(base_url('index.php')); exit();}

?>

<?php 

$admin_id = $_SESSION['auth']['id'];
$datas = select($conn,"SELECT * FROM `group_admin_event`AS g JOIN `events` AS e ON e.id = g.id_event WHERE id_group_admin = (SELECT id_group FROM group_admins WHERE id_admin = $admin_id LIMIT 1) AND e.is_deleted = 0;")

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



<!-- ส่วนของmodal -->

 <dialog id="insert_user" class="modal">
  <div class="modal-box">
    <form method="dialog">
      <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
    </form>
    <!-- <div class="card shrink-0 w-full max-w-md shadow-2xl bg-base-100"> -->
  <form class="card-body space-y-4" action="<?php echo base_url('/API/admin/insertUser.php');?>" method="post">
    <h2 class="card-title text-2xl font-bold justify-center mb-2">ลงทะเบียนใช้งาน</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <!-- fname -->
      <fieldset class="fieldset">
        <legend class="fieldset-legend">ชื่อ</legend>
        <label class="input w-full">
          <!-- Icon User / Person -->
          <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <input type="text" name="fname" class="grow" placeholder="ชื่อจริง" required />
        </label>
      </fieldset>

      <!-- lname -->
      <fieldset class="fieldset">
        <legend class="fieldset-legend">นามสกุล</legend>
        <label class="input w-full">
          <!-- Icon User / Person -->
          <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <input type="text" name="lname" class="grow" placeholder="นามสกุล" required />
        </label>
      </fieldset>
    </div>

    <!-- user -->
    <fieldset class="fieldset">
      <legend class="fieldset-legend">ชื่อผู้ใช้</legend>
      <label class="input w-full">
        <!-- Icon At-Sign / Username -->
        <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="4"/>
          <path d="M16 12v1a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"/>
        </svg>
        <input type="text" name="user" class="grow" placeholder="Username" required />
      </label>
    </fieldset>

    <!-- ปุ่มบันทึกข้อมูล -->
    <div class="pt-2">
      <button type="submit" class="btn btn-primary w-full">บันทึกข้อมูล</button>
    </div>
  </form>
<!-- </div> -->
  </div>
</dialog>