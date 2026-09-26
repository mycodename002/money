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
$count_max = select($conn, "SELECT COUNT(e.id) AS C FROM `events` AS e  WHERE is_deleted = 1;");

$count_max = $count_max[0]['C'];
$start = ($page-1)*4;

// เพิ่มการดึงฟิลด์ user มาด้วยเพื่อนำไปแสดงใน Modal แก้ไข
$sql = "SELECT e.id, e.title, e.details  FROM `events` AS e WHERE is_deleted = 1 LIMIT $start,4;";
$data = select($conn,$sql);
?>

<div class="container mx-auto px-4 mt-4 shadow-md ">
    <div class="overflow-x-auto">
        <h2 class="text-xl font-bold mb-4">รายการ</h2>
        <table class="table w-full">
            <!-- head -->
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>หัวข้อ</th>
                    <th>รายระเอียด</th>
                    <th>จัดการรายการ</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = $start + 1;
                foreach ($data as $value) { ?>
                <tr>
                    <th><?php echo $count++; ?></th>
                    <td><?php echo $value['title']; ?></td>
                    <td><?php echo $value['details']; ?></td>
                    
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
            $count_btn = (int)ceil($count_max / 4); 
            $range = 2;
            $start_page = max(1, $page - $range);
            $end_page   = min($count_btn, $page + $range);

            if ($page > 3) { ?>
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/SUPPORT/showEvent.php?page=1'); ?>'">1</button>
                <?php if ($page > 4) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
            <?php }

            for ($i = $start_page; $i <= $end_page; $i++) { 
                $active_class = ($i == $page) ? 'btn-active' : ''; 
                ?>
                <button class="join-item btn <?php echo $active_class; ?>" onclick="window.location.href = '<?php echo base_url('VIEW/SUPPORT/showEvent.php?page='.$i); ?>'">
                    <?php echo $i; ?>
                </button>
            <?php }

            if ($page < $count_btn - $range) { ?>
                <?php if ($page < $count_btn - $range - 1) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/SUPPORT/showEvent.php?page='.$count_btn); ?>'">
                    <?php echo $count_btn; ?>
                </button>
            <?php } ?>
        </div>
    </div>
</div>


<!-- modalll -->
 <dialog id="Modal_restore" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-success mb-4">กู้คืนรายการ</h3>
        <p class="py-2">คุณแน่ใจหรือไม่ว่าต้องการกู้คืนรายการนี้?</p>
        
        <form id="restoreForm" action="../../API/support/process.php" method="POST">
            <input type="hidden" id="restore_id" name="id">
            <input type="hidden" name="action" value="restoreEvent">

            <div class="modal-action">
                <!-- ส่ง Form ID 'restoreForm' เข้าไป -->
                <button type="button" class="btn btn-success" onclick="openConfirmModal('restoreForm')">ยืนยันการกู้คืน</button>
                <button type="button" class="btn" onclick="document.getElementById('Modal_restore').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<dialog id="Modal_del" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error mb-4">ลบรายการ</h3>
        <p class="py-2">คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?</p>
        
        <form id="deleteForm" action="../../API/support/process.php" method="POST">
            <input type="hidden" id="delete_id" name="id">
            <input type="hidden" name="action" value="deleteEvent">

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
    function redirect(url){
        window.location.href = url;
    }

// ตัวแปรเก็บ ID ของ <form> ที่ต้องการ submit
var currentFormId = "";

// 1. ฟังก์ชันเปิด Modal แก้ไข และใส่ข้อมูลเดิม
function openRestoreModal(id) {
    document.getElementById('restore_id').value = id;
    document.getElementById('Modal_restore').showModal();
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