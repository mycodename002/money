<?php 
require_once "../../API/functions.php";
require_once '../TEMPLATES/user/head_user.php';
require_once '../TEMPLATES/user/nav_user.php';
require_once "../../API/alarmAndNotify.php";
include_once "../../db.php";

if(!isset($_SESSION['auth']['rule']) || $_SESSION['auth']['rule'] != 'admin') {
    header('Location: ' . base_url('index.php')); 
    exit();
}

$page = 1;
if(isset($_GET['page'])){
    $page = (int)$_GET['page'];
}
$admin_group = $_SESSION['auth']['admin_group']; 
$count_max = select($conn, "SELECT COUNT(e.id) AS C FROM `group_admin_event` AS g JOIN `events` AS e ON g.id_event = e.id WHERE g.id_group_admin = 1 AND e.is_deleted = 0 AND e.is_success = 0;");

$count_max = $count_max[0]['C'];
$start = ($page-1)*25;

// เพิ่มการดึงฟิลด์ user มาด้วยเพื่อนำไปแสดงใน Modal แก้ไข
$sql = "SELECT e.id, e.titel, e.details  FROM `group_admin_event` AS g JOIN `events` AS e ON g.id_event = e.id WHERE g.id_group_admin = 1 AND e.is_deleted = 0 AND e.is_success = 0 LIMIT $start,25;";
$data = select($conn,$sql);
?>

<div class="container mx-auto px-4 mt-4">
    <div class="overflow-x-auto">
        <h2 class="text-xl font-bold mb-4">รายการ</h2>
        <button class = "btn btn-soft btn-primary" onclick="insert_user.showModal()">เพิ่มสมาชิก</button>
        <table class="table w-full">
            <!-- head -->
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>หัวข้อ</th>
                    <th>รายระเอียด</th>
                    <th>จัดการสมาชิก</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = $start + 1;
                foreach ($data as $value) { ?>
                <tr>
                    <th><?php echo $count++; ?></th>
                    <td><?php echo $value['titel']; ?></td>
                    <td><?php echo $value['details']; ?></td>
                    
                    <td>
                        <!-- ปุ่มแก้ไข: ส่งข้อมูลไปยัง JS เพื่อเปิด Modal พร้อมเติมข้อมูลเดิม -->
                        <button class="btn btn-soft btn-warning btn-sm" 
                                onclick="openEditModal('<?php echo $value['id']; ?>')">
                            แก้ไข
                        </button>
                        <button onclick="openDeleteModal('<?php echo $value['id']; ?>')" class="btn btn-soft btn-error btn-sm">ลบ</button>
                        <button onclick="openDeleteModal('<?php echo $value['id']; ?>')" class="btn btn-soft btn-primary btn-sm">รายละเอียด</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="join mt-4">
            <?php 
            $count_btn = (int)ceil($count_max / 25); 
            $range = 2;
            $start_page = max(1, $page - $range);
            $end_page   = min($count_btn, $page + $range);

            if ($page > 3) { ?>
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/showEvent.php?page=1'); ?>'">1</button>
                <?php if ($page > 4) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
            <?php }

            for ($i = $start_page; $i <= $end_page; $i++) { 
                $active_class = ($i == $page) ? 'btn-active' : ''; 
                ?>
                <button class="join-item btn <?php echo $active_class; ?>" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/showEvent.php?page='.$i); ?>'">
                    <?php echo $i; ?>
                </button>
            <?php }

            if ($page < $count_btn - $range) { ?>
                <?php if ($page < $count_btn - $range - 1) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/showEvent.php?page='.$count_btn); ?>'">
                    <?php echo $count_btn; ?>
                </button>
            <?php } ?>
        </div>
    </div>
</div>


<?php require_once "./modal.php"; ?>