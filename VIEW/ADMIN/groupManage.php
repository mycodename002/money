<?php 
// require_once "../../API/functions.php";
// require_once '../TEMPLATES/user/head_user.php';
// require_once '../TEMPLATES/user/nav_user.php';
// require_once "../../API/alarmAndNotify.php";
include_once "../../db.php";
if(!isset($_SESSION['auth']['rule']) || $_SESSION['auth']['rule'] != 'admin') {
    header('Location: ' . base_url('index.php')); 
    exit();
}
?>

<?php $data_group = protectSelect($conn, "SELECT DISTINCT * FROM `group_mem`  WHERE id_group_admin = :ad_id;", ["ad_id"=>$_SESSION['auth']['admin_group']], 1)  ?>
<div class="overflow-x-auto">
    <h2 class="text-xl font-bold mb-4">กลุ่ม</h2>
        <button class = "btn btn-soft btn-primary" onclick="">เพิ่มกลุ่ม</button>

    <table class="table w-full">
            <!-- head -->
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อ</th>
                    <th>รายละเอียด</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = 1;
                foreach ($data_group as $value) { ?>
                <tr>
                    <th><?php echo $count++; ?></th>
                    <td><?php echo htmlspecialchars($value['titel']); ?></td>
                    <td><?php echo htmlspecialchars($value['details']); ?></td>
                    <td>
                        <!-- ปุ่มแก้ไข: ส่งข้อมูลไปยัง JS เพื่อเปิด Modal พร้อมเติมข้อมูลเดิม -->
                        <button onclick="openDeleteModal('<?php echo $value['id']; ?>')" class="btn btn-soft btn-error btn-sm">ลบ</button>
                        <button class="btn btn-soft btn-primary btn-sm" 
                                onclick="opendetailModal('<?php echo $value['id']; ?>', '<?php echo addslashes($value['titel']); ?>', '<?php echo addslashes($value['details']); ?>')">
                            รายละเอียด
                        </button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
</div>