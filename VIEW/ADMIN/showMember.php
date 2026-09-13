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
$count_max = select($conn, "SELECT COUNT(id) AS C FROM `members` WHERE admin_group = '$admin_group' AND is_deleted = 0;");

$count_max = $count_max[0]['C'];
$start = ($page-1)*25;

// เพิ่มการดึงฟิลด์ user มาด้วยเพื่อนำไปแสดงใน Modal แก้ไข
$sql = "SELECT id, fname, lname, user FROM `members` WHERE admin_group = '$admin_group' AND is_deleted = 0 LIMIT $start,25;";
$data = select($conn,$sql);
?>

<div class="container mx-auto px-4 mt-4">
    <div class="overflow-x-auto">
        <h2 class="text-xl font-bold mb-4">รายชื่อผู้ใช้</h2>
        <button class = "btn btn-soft btn-primary" onclick="insert_user.showModal()">เพิ่มสมาชิก</button>
        <table class="table w-full">
            <!-- head -->
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อ</th>
                    <th>นามสกุล</th>
                    <th>User</th>
                    <th>จัดการสมาชิก</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = $start + 1;
                foreach ($data as $value) { ?>
                <tr>
                    <th><?php echo $count++; ?></th>
                    <td><?php echo htmlspecialchars($value['fname']); ?></td>
                    <td><?php echo htmlspecialchars($value['lname']); ?></td>
                    <td><?php echo htmlspecialchars($value['user']); ?></td>
                    <td>
                        <!-- ปุ่มแก้ไข: ส่งข้อมูลไปยัง JS เพื่อเปิด Modal พร้อมเติมข้อมูลเดิม -->
                        <button class="btn btn-soft btn-warning btn-sm" 
                                onclick="openEditModal('<?php echo $value['id']; ?>', '<?php echo addslashes($value['fname']); ?>', '<?php echo addslashes($value['lname']); ?>', '<?php echo addslashes($value['user']); ?>')">
                            แก้ไข
                        </button>
                        <button onclick="openDeleteModal('<?php echo $value['id']; ?>')" class="btn btn-soft btn-error btn-sm">ลบ</button>
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
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/showMember.php?page=1'); ?>'">1</button>
                <?php if ($page > 4) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
            <?php }

            for ($i = $start_page; $i <= $end_page; $i++) { 
                $active_class = ($i == $page) ? 'btn-active' : ''; 
                ?>
                <button class="join-item btn <?php echo $active_class; ?>" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/showMember.php?page='.$i); ?>'">
                    <?php echo $i; ?>
                </button>
            <?php }

            if ($page < $count_btn - $range) { ?>
                <?php if ($page < $count_btn - $range - 1) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/showMember.php?page='.$count_btn); ?>'">
                    <?php echo $count_btn; ?>
                </button>
            <?php } ?>
        </div>
    </div>
</div>

<dialog id="Modal_edit" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">แก้ไขข้อมูลสมาชิก</h3>
        
        <form id="editForm" action="../../API/admin/editMember.php" method="POST">
            <input type="hidden" id="edit_id" name="id">

            <div class="form-control w-full mb-3">
                <label class="label"><span class="label-text">Username</span></label>
                <input type="text" id="edit_user" name="user" class="input input-bordered w-full" required />
            </div>

            <div class="form-control w-full mb-3">
                <label class="label"><span class="label-text">ชื่อ (First Name)</span></label>
                <input type="text" id="edit_fname" name="fname" class="input input-bordered w-full" required />
            </div>

            <div class="form-control w-full mb-4">
                <label class="label"><span class="label-text">นามสกุล (Last Name)</span></label>
                <input type="text" id="edit_lname" name="lname" class="input input-bordered w-full" required />
            </div>

            <div class="modal-action">
                <!-- ส่ง Form ID 'editForm' เข้าไป -->
                <button type="button" class="btn btn-soft btn-success" onclick="openConfirmModal('editForm')">บันทึกการแก้ไข</button>
                <button type="button" class="btn" onclick="document.getElementById('Modal_edit').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- Modal ลบสมาชิก -->
<dialog id="Modal_del" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error mb-4">ลบสมาชิก</h3>
        <p class="py-2">คุณแน่ใจหรือไม่ว่าต้องการลบสมาชิกคนนี้?</p>
        
        <form id="deleteForm" action="../../API/admin/deleteMember.php" method="POST">
            <input type="hidden" id="delete_id" name="id">

            <div class="modal-action">
                <!-- ส่ง Form ID 'deleteForm' เข้าไป -->
                <button type="button" class="btn btn-error" onclick="openConfirmModal('deleteForm')">ยืนยันการลบ</button>
                <button type="button" class="btn" onclick="document.getElementById('Modal_del').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- Modal ยืนยันซ้ำอีกครั้ง (Confirm Modal) -->
<dialog id="Modal_confirm" class="modal">
    <div class="modal-box text-center">
        <h3 class="font-bold text-xl text-warning mb-2">ยืนยันการทำรายการ</h3>
        <p class="py-2 text-gray-600">คุณต้องการดำเนินการตามรายการนี้ใช่หรือไม่?</p>
        <div class="modal-action justify-center gap-4 mt-4">
            <button type="button" class="btn btn-success px-6" onclick="submitEditForm()">ยืนยัน</button>
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('Modal_confirm').close()">ยกเลิก</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>


<script>
// ตัวแปรเก็บ ID ของ <form> ที่ต้องการ submit
var currentFormId = "";

// 1. ฟังก์ชันเปิด Modal แก้ไข และใส่ข้อมูลเดิม
function openEditModal(id, fname, lname, user) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_fname').value = fname;
    document.getElementById('edit_lname').value = lname;
    document.getElementById('edit_user').value = user;

    document.getElementById('Modal_edit').showModal();
}

// 2. ฟังก์ชันเปิด Modal ลบ และใส่ ID ผู้ใช้
function openDeleteModal(id) {
    document.getElementById('delete_id').value = id;
    document.getElementById('Modal_del').showModal();
}

// 3. ตรวจสอบความถูกต้องของฟอร์ม (Form ID) แล้วเปิด Modal ยืนยัน
function openConfirmModal(formId) {
    currentFormId = formId; // บันทึก id ของ form ที่ใช้งานอยู่
    const form = document.getElementById(currentFormId);
    
    // ตรวจสอบความถูกต้องของ Input (เช่น required)
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // เปิด Modal ยืนยัน
    document.getElementById('Modal_confirm').showModal();
}

// 4. ฟังก์ชันส่ง Form ไปยังไฟล์ประมวลผลจริง
function submitEditForm() {
    if (currentFormId) {
        document.getElementById(currentFormId).submit();
    }
}
</script>


<?php require_once "./modal.php"; ?>