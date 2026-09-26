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

$page = 1;
if(isset($_GET['page'])){
    $page = (int)$_GET['page'];
}

$count_max = protectSelect($conn,"SELECT COUNT(id) AS C FROM `group_mem` WHERE is_deleted = 1",[],0);
$count_max = $count_max['C'];
$start = ($page-1)*25;

// เพิ่มการดึงฟิลด์ user มาด้วยเพื่อนำไปแสดงใน Modal แก้ไข
$sql = "SELECT id, fname, lname, user FROM `members` WHERE is_deleted = 1 LIMIT $start,25;";
$data = select($conn,$sql);
?>

<div class="container mx-auto px-4 mt-4 shadow-md ">
    <!-- <div class="overflow-x-auto">
        <h2 class="text-xl font-bold mb-4">กลุ่ม</h2>

    </div> -->
    
    <div class="overflow-x-auto mt-8">
        <h2 class="text-xl font-bold mb-4">รายชื่อผู้ใช้</h2>
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
                        <button class="btn btn-soft btn-success btn-sm" 
                                onclick="openRestoreModal('<?php echo $value['id']; ?>')">
                            กู้คืน
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
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/SUPPORT/showMember.php?page=1'); ?>'">1</button>
                <?php if ($page > 4) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
            <?php }

            for ($i = $start_page; $i <= $end_page; $i++) { 
                $active_class = ($i == $page) ? 'btn-active' : ''; 
                ?>
                <button class="join-item btn <?php echo $active_class; ?>" onclick="window.location.href = '<?php echo base_url('VIEW/SUPPORT/showMember.php?page='.$i); ?>'">
                    <?php echo $i; ?>
                </button>
            <?php }

            if ($page < $count_btn - $range) { ?>
                <?php if ($page < $count_btn - $range - 1) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/SUPPORT/showMember.php?page='.$count_btn); ?>'">
                    <?php echo $count_btn; ?>
                </button>
            <?php } ?>
        </div>
    </div>
</div>

<dialog id="Modal_restore" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">กู้คืนสมาชิก</h3>
        
        <form id="restoreForm" action="../../API/support/process.php" method="POST">
            <input type="hidden" id="restore_id" name="id">
                <input type="hidden" id="" name="action" value="RestoreMember">

            <div class="modal-action">
                <!-- ส่ง Form ID 'restoreForm' เข้าไป -->
                <button type="button" class="btn btn-soft btn-success" onclick="openConfirmModal('restoreForm')">บันทึกการกู้คืน</button>
                <button type="button" class="btn" onclick="document.getElementById('Modal_restore').close()">ยกเลิก</button>
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
        
        <form id="deleteForm" action="../../API/support/process.php" method="POST">
            <input type="hidden" id="delete_id" name="id">
            <input type="hidden" name="action" value="deleteMember">

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

// 1. ฟังก์ชันเปิด Modal กู้คืน และใส่ ID ผู้ใช้
function openRestoreModal(id) {
    console.warn(id);
    document.getElementById('restore_id').value = id;
    document.getElementById('Modal_restore').showModal();
}

// 2. ฟังก์ชันเปิด Modal ลบ และใส่ ID ผู้ใช้
function openDeleteModal(id) {
    console.warn(id);
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