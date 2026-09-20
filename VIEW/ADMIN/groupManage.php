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

<?php $data_group = protectSelect($conn, "SELECT DISTINCT * FROM `group_mem`  WHERE is_deleted = 0 AND id_group_admin = :ad_id;", ["ad_id"=>$_SESSION['auth']['admin_group']], 1)  ?>
<div class="overflow-x-auto">
    <h2 class="text-xl font-bold mb-4">กลุ่ม</h2>
        <button class = "btn btn-soft btn-primary" onclick="Modal_add_group.showModal()">เพิ่มกลุ่ม</button>

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
                    <td><?php echo htmlspecialchars($value['title']); ?></td>
                    <td><?php echo htmlspecialchars($value['details']); ?></td>
                    <td>
                        <!-- ปุ่มแก้ไข: ส่งข้อมูลไปยัง JS เพื่อเปิด Modal พร้อมเติมข้อมูลเดิม -->
                        <button onclick="openModalDelGroup('<?php echo $value['id']; ?>')" class="btn btn-soft btn-error btn-sm">ลบ</button>
                        <button class="btn btn-soft btn-primary btn-sm" 
                                onclick="window.location.href='<?php echo base_url('VIEW/ADMIN/detaliGroup.php?idGroup=').$value['id']; ?>'">
                            รายละเอียด
                        </button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
</div>



<dialog id="Modal_add_group" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-primary mb-4">เพิ่มกลุ่ม</h3>
        <p class="py-2">จัดการรายชื่อให้เป็นกลุ่มและรายชื่อที่ใช้บ่อย</p>
        
        <form id="deleteForm" action="../../API/admin/process.php" method="POST">
            <input type="hidden" id="delete_id" name="id">
            <input type="hidden" id="" name="action" value="addGroupMember">
            <div class="form-control w-full mb-3">
                <label class="label"><span class="label-text">ชื่อกลุ่ม</span></label>
                <input type="text" id="" name="name_group" class="input input-bordered w-full" required />
            </div>
            <div class="form-control w-full mb-3">
                <label class="label"><span class="label-text">รายละเอียด</span></label>
                <input type="text" id="" name="details" class="input input-bordered w-full" />
            </div>
            <div class="modal-action">
                <!-- ส่ง Form ID 'deleteForm' เข้าไป -->
                <button type="button" class="btn btn-soft btn-primary" onclick="openConfirmModal('deleteForm')">เพิ่มกลุ่ม</button>
                <button type="button" class="btn" onclick="document.getElementById('Modal_add_group').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<dialog id="Modal_del_group" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error mb-4">ลบสมาชิก</h3>
        <p class="py-2">คุณแน่ใจหรือไม่ว่าต้องการลบกลุ่มนี้?</p>
        
        <form id="delete_group" action="../../API/admin/process.php" method="POST">
            <input type="hidden" id="delete_id_group" name="id">
            <input type="hidden" id="" name="action" value="deleteGroup">

            <div class="modal-action">
                <!-- ส่ง Form ID 'deleteForm' เข้าไป -->
                <button type="button" class="btn btn-error" onclick="openConfirmModal('delete_group')">ยืนยันการลบ</button>
                <button type="button" class="btn" onclick="document.getElementById('Modal_del_group').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
    function openModalDelGroup(id){
        Modal_del_group.showModal();
        document.getElementById('delete_id_group').value = id;
    }
</script>