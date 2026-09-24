<?php require_once "../TEMPLATES/user/head_user.php"; ?>
<?php require_once "../TEMPLATES/user/nav_user.php"; ?>
<?php include_once "../../db.php"; ?>
<?php require_once "../../API/auth.php"; 
require_once "../../API/alarmAndNotify.php";

if (!isset($_SESSION['auth']['rule']) || $_SESSION['auth']['rule'] !== 'user') {
    header('Location: ' . base_url('index.php')); 
    exit();
}

$id =$_GET['id'] ?? null;
if (empty($id)) {
    backPage();
    exit();
}

// ตรวจสอบสิทธิ์ว่าผู้ใช้เป็นสมาชิกของกิจกรรมนี้หรือไม่
$checkRole = protectSelect($conn, "SELECT id FROM `mem_event` WHERE id_mem = :id AND id_event = :id_event", [
    'id' => $_SESSION['auth']['id'],
    'id_event' => $id
], 0);

if (empty($checkRole)) {
    backPage();
    exit();
}

// ดึงข้อมูลกิจกรรม
$eventData = protectSelect($conn, "SELECT title, details FROM `events` WHERE id = :id", ['id' => $id], 0);

// ดึงข้อมูลสลิปเดิมของผู้ใช้ (ถ้ามี)
$slipData = protectSelect($conn, "SELECT * FROM `slips` WHERE add_by = :id_mem AND id_event = :id_event", [
    'id_mem' => $_SESSION['auth']['id'],
    'id_event' => $id
], 0);
?>

<div class="container mx-auto px-4 my-8 max-w-2xl">
    <div class="card bg-base-100 shadow-md border border-base-200">
        <div class="card-body">
            <!-- หัวข้อกิจกรรม -->
            <h2 class="card-title text-2xl font-bold text-primary border-b pb-3 mb-2">
                <?= htmlspecialchars($eventData['title'] ?? 'รายละเอียดรายการ') ?>
            </h2>
            <p class="text-gray-600 text-sm mb-4">
                <?= htmlspecialchars($eventData['details'] ?? '-') ?>
            </p>

            <!-- สถานะปัจจุบัน (ถ้ามีการแนบแล้ว) -->
            <?php if (!empty($slipData)): ?>
                <?php 
                    $badgeClass = 'badge-warning';
                    if ($slipData['status'] === 'ผ่าน') $badgeClass = 'badge-success';
                    if ($slipData['status'] === 'ไม่ผ่าน') $badgeClass = 'badge-error';
                ?>
                <div class="alert bg-base-200 border-base-300 mb-4">
                    <div class="flex justify-between items-center w-full">
                        <span>สถานะสลิปปัจจุบัน:</span>
                        <div class="badge <?= $badgeClass ?> font-semibold">
                            <?= htmlspecialchars($slipData['status']) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ฟอร์มอัปโหลดสลิป -->
            <form action="<?= base_url('API/user/process.php') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="id_event" value="<?= htmlspecialchars($id) ?>">
                <input type="hidden" name="action" value="upload_slip">
                <input type="hidden" name="add_by" value="<?php echo  $_SESSION['auth']['id']; ?>">

                <!-- จำนวนเงิน -->
            

                <!-- เลือกไฟล์สลิป -->
                <div class="form-control w-full">
                    <label class="label mb-1">
                        <span class="label-text font-semibold">แนบหลักฐานการโอนเงิน (ไฟล์รูปภาพ) <span class="text-error">*</span></span>
                    </label>
                    <input 
                        type="file" 
                        name="slip_file" 
                        accept="image/*" 
                        class="file-input file-input-bordered w-full" 
                        onchange="previewSlipImage(this)"
                        <?= empty($slipData) ? 'required' : '' ?>
                    />
                </div>

                <!-- แสดงตัวอย่างรูปภาพ (Preview) -->
                <div id="preview_wrapper" class="mt-4 <?= !empty($slipData['file_name']) ? '' : 'hidden' ?>">
                    <p class="text-xs text-gray-500 mb-2">ตัวอย่างรูปภาพสลิป:</p>
                    <div class="flex justify-center bg-base-200 p-3 rounded-lg border">
                        <img 
                            id="slip_preview" 
                            src="<?= !empty($slipData['file_name']) ? base_url('STORAGES/IMG/' . $slipData['file_name']) : '' ?>" 
                            alt="Preview Slip" 
                            class="max-h-64 object-contain rounded-md shadow-sm"
                        >
                    </div>
                </div>

                <!-- ปุ่มส่งข้อมูล -->
                <div class="card-actions justify-end mt-6">
                    <a href="javascript:history.back()" class="btn btn-ghost">ย้อนกลับ</a>
                    <button type="submit" class="btn btn-primary px-8">
                        <?= !empty($slipData) ? 'อัปเดตสลิป' : 'ยืนยันการส่งสลิป' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewSlipImage(input) {
    const previewWrapper = document.getElementById('preview_wrapper');
    const previewImg = document.getElementById('slip_preview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewWrapper.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>