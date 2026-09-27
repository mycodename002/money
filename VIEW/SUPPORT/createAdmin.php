<?php require_once "../TEMPLATES/user/head_user.php"; ?>
<?php require_once "../TEMPLATES/user/nav_user.php"; ?>
<?php include_once "../../db.php"; ?>
<?php require_once "../../API/auth.php"; 
require_once "../../API/alarmAndNotify.php";

if (!isset($_SESSION['auth']['rule']) || $_SESSION['auth']['rule'] !== 'support') {
    header('Location: ' . base_url('index.php')); 
    exit();
}

?>
<div class="container mx-auto px-4 my-8 max-w-md">
    <div class="bg-base-100 p-6 sm:p-8 rounded-2xl shadow-xl border border-base-200">
        <!-- Header -->
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-base-content">เพิ่มรายชื่อผู้ดูแล</h2>
            <!-- <p class="text-sm text-base-content/60 mt-1">กรุณากรอกรหัสผ่านเดิมและตั้งรหัสผ่านใหม่</p> -->
        </div>

        <form action="<?= base_url('API/support/process.php') ?>" method="POST">
            
            <input type="hidden" name="action" value="createAdmin">
            <div class="form-control w-full mb-4">
                <label class="label">
                    <span class="label-text font-medium">ชื่อ</span>
                </label>
                <input type="text" 
                       name="fname" 
                       class="input input-bordered w-full rounded-xl focus:input-primary" 
                       placeholder="ชื่อ" 
                       required 
                        />
            </div>

            <div class="form-control w-full mb-4">
                <label class="label">
                    <span class="label-text font-medium">นามสกุล</span>
                </label>
                <input type="text" 
                       name="lname" 
                       class="input input-bordered w-full rounded-xl focus:input-primary" 
                       placeholder="นามสกุล" 
                       required 
                        />
            </div>

            <div class="form-control w-full mb-6">
                <label class="label">
                    <span class="label-text font-medium">ชื่อผู้ใช้งาน</span>
                </label>
                <input type="text" 
                       name="user" 
                       class="input input-bordered w-full rounded-xl focus:input-primary" 
                       placeholder="ชื่อผู้ใช้งาน" 
                       required 
                       oninput="validatePassword()" />
                
            </div>

            <!-- ปุ่มบันทึก -->
            <div class="form-control mt-6">
                <button type="submit" 
                        id="submit_btn" 
                        class="btn btn-primary w-full rounded-xl shadow-md" 
                        >
                    บันทึก
                </button>
            </div>

        </form>
    </div>
</div>
