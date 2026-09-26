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
$admin_group =$_SESSION['auth']['admin_group'];
$count_max = protectSelect($conn,"SELECT COUNT(id) AS C FROM `group_mem` WHERE id_group_admin = :ad_id AND is_deleted = 0;",["ad_id"=>$admin_group],0);
$count_max =$count_max['C'];
$start = ($page-1)*25;

$sql = "SELECT id, fname, lname, user FROM `members` WHERE admin_group = '$admin_group' AND is_deleted = 0 LIMIT $start,25;";
$data = select($conn,$sql);
?>

<div class="container mx-auto px-6 py-6 mt-6 bg-base-100 rounded-2xl shadow-xl border border-base-200">
    
    <?php require_once './groupManage.php'; ?>
    <div class="overflow-x-auto mt-8 border-t border-base-200 pt-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-base-content">รายชื่อผู้ใช้</h2>
                <p class="text-sm text-base-content/60">จัดการข้อมูลและสมาชิกทั้งหมดในกลุ่มของคุณ</p>
            </div>
            <button class="btn btn-primary btn-soft shadow-sm hover:shadow-md transition-all" onclick="insert_user.showModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                เพิ่มสมาชิก
            </button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-base-200 shadow-sm">
            <table class="table w-full align-middle">
                <thead class="bg-base-200/80 text-sm">
                    <tr>
                        <th class="w-16 text-center">ลำดับ</th>
                        <th>ชื่อ</th>
                        <th>นามสกุล</th>
                        <th>User</th>
                        <th class="text-center w-48">จัดการสมาชิก</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200">
                    <?php 
                    $count =$start + 1;
                    foreach ($data as$value) { ?>
                    <tr class="hover:bg-base-200/30 transition-colors">
                        <th class="text-center font-medium opacity-70"><?php echo $count++; ?></th>
                        <td class="font-medium"><?php echo htmlspecialchars($value['fname']); ?></td>
                        <td class="font-medium"><?php echo htmlspecialchars($value['lname']); ?></td>
                        <td><span class="badge badge-ghost font-mono"><?php echo htmlspecialchars($value['user']); ?></span></td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button class="btn btn-soft btn-warning btn-sm" 
                                        onclick="openEditModal('<?php echo $value['id']; ?>', '<?php echo addslashes($value['fname']); ?>', '<?php echo addslashes($value['lname']); ?>', '<?php echo addslashes($value['user']); ?>')">
                                    แก้ไข
                                </button>
                                <button onclick="openDeleteModal('<?php echo $value['id']; ?>')" class="btn btn-soft btn-error btn-sm">ลบ</button>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="flex justify-center sm:justify-end mt-6">
            <div class="join shadow-sm border border-base-200 rounded-lg overflow-hidden">
                <?php 
                $count_btn = (int)ceil($count_max / 25);$range = 2;
                $start_page = max(1, $page -$range);
                $end_page   = min($count_btn, $page +$range);

                if ($page > 3) { ?>
                    <button class="join-item btn btn-sm md:btn-md" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/showMember.php?page=1'); ?>'">1</button>
                    <?php if ($page > 4) { ?>
                        <button class="join-item btn btn-sm md:btn-md btn-disabled">...</button>
                    <?php } ?>
                <?php }

                for ($i =$start_page; $i <=$end_page; $i++) {$active_class = ($i ==$page) ? 'btn-active' : ''; 
                    ?>
                    <button class="join-item btn btn-sm md:btn-md <?php echo $active_class; ?>" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/showMember.php?page='.$i); ?>'">
                        <?php echo $i; ?>
                    </button>
                <?php }

                if ($page < $count_btn -$range) { ?>
                    <?php if ($page < $count_btn -$range - 1) { ?>
                        <button class="join-item btn btn-sm md:btn-md btn-disabled">...</button>
                    <?php } ?>
                    <button class="join-item btn btn-sm md:btn-md" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/showMember.php?page='.$count_btn); ?>'">
                        <?php echo $count_btn; ?>
                    </button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<dialog id="Modal_edit" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box rounded-2xl shadow-2xl border border-base-200 p-6">
        <h3 class="font-bold text-xl text-base-content mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            แก้ไขข้อมูลสมาชิก
        </h3>
        
        <form id="editForm" action="../../API/admin/process.php" method="POST" class="space-y-4">
            <input type="hidden" id="edit_id" name="id">
            <input type="hidden" id="" name="action" value="editMember">

            <div class="form-control w-full">
                <label class="label"><span class="label-text font-medium">Username</span></label>
                <input type="text" id="edit_user" name="user" class="input input-bordered w-full focus:input-primary rounded-lg" required />
            </div>

            <div class="form-control w-full">
                <label class="label"><span class="label-text font-medium">ชื่อ</span></label>
                <input type="text" id="edit_fname" name="fname" class="input input-bordered w-full focus:input-primary rounded-lg" required />
            </div>

            <div class="form-control w-full">
                <label class="label"><span class="label-text font-medium">นามสกุล</span></label>
                <input type="text" id="edit_lname" name="lname" class="input input-bordered w-full focus:input-primary rounded-lg" required />
            </div>

            

            <div class="modal-action pt-4 border-t border-base-200">
                <button type="button" class="btn btn-soft btn-success" onclick="openConfirmModal('editForm')">บันทึกการแก้ไข</button>
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('Modal_edit').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<dialog id="Modal_del" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box rounded-2xl shadow-2xl border border-base-200 p-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-3 bg-error/10 text-error rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </div>
            <h3 class="font-bold text-xl text-error">ลบสมาชิก</h3>
        </div>
        <p class="py-3 text-base-content/70">คุณแน่ใจหรือไม่ว่าต้องการลบสมาชิกคนนี้? การดำเนินการนี้จะไม่สามารถย้อนกลับได้</p>
        
        <form id="deleteForm" action="../../API/admin/process.php" method="POST">
            <input type="hidden" id="delete_id" name="id">
            <input type="hidden" id="" name="action" value="deleteMember">

            <div class="modal-action pt-4 border-t border-base-200">
                <button type="button" class="btn btn-error text-white" onclick="openConfirmModal('deleteForm')">ยืนยันการลบ</button>
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('Modal_del').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<dialog id="Modal_confirm" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box rounded-2xl shadow-2xl border border-base-200 text-center p-6">
        <div class="mx-auto w-12 h-12 bg-warning/10 text-warning rounded-full flex items-center justify-center mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        </div>
        <h3 class="font-bold text-xl text-base-content mb-1">ยืนยันการทำรายการ</h3>
        <p class="py-2 text-sm text-base-content/70">คุณต้องการดำเนินการตามรายการนี้ใช่หรือไม่?</p>
        <div class="modal-action justify-center gap-3 mt-4">
            <button type="button" class="btn btn-success text-white px-6" onclick="submitEditForm()">ยืนยัน</button>
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('Modal_confirm').close()">ยกเลิก</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
var currentFormId = "";

function openEditModal(id, fname, lname, user) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_fname').value = fname;
    document.getElementById('edit_lname').value = lname;
    document.getElementById('edit_user').value = user;

    document.getElementById('Modal_edit').showModal();
}

function openDeleteModal(id) {
    // console.warn(id);
    document.getElementById('delete_id').value = id;
    document.getElementById('Modal_del').showModal();
}

function openConfirmModal(formId) {
    currentFormId = formId; 
    console.warn(currentFormId);
    const form = document.getElementById(currentFormId);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    document.getElementById('Modal_confirm').showModal();
}

function submitEditForm() {
    if (currentFormId) {
        document.getElementById(currentFormId).submit();
    }
}
</script>

