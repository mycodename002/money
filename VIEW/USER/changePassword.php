<?php require_once "../TEMPLATES/user/head_user.php"; ?>
<?php require_once "../TEMPLATES/user/nav_user.php"; ?>
<?php include_once "../../db.php"; ?>
<?php require_once "../../API/auth.php"; 
require_once "../../API/alarmAndNotify.php";

if (!isset($_SESSION['auth']['rule']) || $_SESSION['auth']['rule'] !== 'user') {
    header('Location: ' . base_url('index.php')); 
    exit();
}

?>
<div class="container mx-auto px-4 my-8 max-w-md">
    <div class="bg-base-100 p-6 sm:p-8 rounded-2xl shadow-xl border border-base-200">
        <!-- Header -->
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-base-content">เปลี่ยนรหัสผ่าน</h2>
            <p class="text-sm text-base-content/60 mt-1">กรุณากรอกรหัสผ่านเดิมและตั้งรหัสผ่านใหม่</p>
        </div>

        <!-- Form -->
        <form id="changePasswordForm" action="<?= base_url('API/user/process.php') ?>" method="POST">
            
            <input type="hidden" name="action" value="changePass">
            <div class="form-control w-full mb-4">
                <label class="label">
                    <span class="label-text font-medium">รหัสผ่านปัจจุบัน</span>
                </label>
                <input type="password" 
                       name="old_password" 
                       id="old_password" 
                       class="input input-bordered w-full rounded-xl focus:input-primary" 
                       placeholder="••••••••" 
                       required 
                       oninput="validatePassword()" />
            </div>

            <!-- รหัสผ่านใหม่ -->
            <div class="form-control w-full mb-4">
                <label class="label">
                    <span class="label-text font-medium">รหัสผ่านใหม่</span>
                </label>
                <input type="password" 
                       name="new_password" 
                       id="new_password" 
                       class="input input-bordered w-full rounded-xl focus:input-primary" 
                       placeholder="••••••••" 
                       required 
                       oninput="validatePassword()" />
            </div>

            <!-- ยืนยันรหัสผ่านใหม่ -->
            <div class="form-control w-full mb-6">
                <label class="label">
                    <span class="label-text font-medium">ยืนยันรหัสผ่านใหม่</span>
                </label>
                <input type="password" 
                       name="confirm_password" 
                       id="confirm_password" 
                       class="input input-bordered w-full rounded-xl focus:input-primary" 
                       placeholder="••••••••" 
                       required 
                       oninput="validatePassword()" />
                <!-- ข้อความแจ้งเตือนเมื่อรหัสผ่านไม่ตรงกัน -->
                <label class="label py-1 hidden" id="password_error">
                    <span class="label-text-alt text-error font-medium">รหัสผ่านใหม่ทั้ง 2 ช่องไม่ตรงกัน</span>
                </label>
            </div>

            <!-- ปุ่มบันทึก -->
            <div class="form-control mt-6">
                <button type="submit" 
                        id="submit_btn" 
                        class="btn btn-primary w-full rounded-xl shadow-md" 
                        disabled>
                    บันทึกการเปลี่ยนรหัสผ่าน
                </button>
            </div>

        </form>
    </div>
</div>

<script>
function validatePassword() {
    const oldPassword = document.getElementById('old_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    const submitBtn = document.getElementById('submit_btn');
    const errorMsg = document.getElementById('password_error');

    // ตรวจสอบว่ากรอกข้อมูลครบทุกช่องหรือไม่
    if (oldPassword === '' || newPassword === '' || confirmPassword === '') {
        submitBtn.disabled = true;
        errorMsg.classList.add('hidden');
        return;
    }

    // ตรวจสอบว่ารหัสผ่านใหม่ทั้งสองช่องตรงกันหรือไม่
    if (newPassword !== confirmPassword) {
        submitBtn.disabled = true;
        errorMsg.classList.remove('hidden'); // แสดงข้อความเตือน
    } else {
        submitBtn.disabled = false; // เปิดให้กดปุ่มได้
        errorMsg.classList.add('hidden'); // ซ่อนข้อความเตือน
    }
}
</script>