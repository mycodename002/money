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
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $ad_id = $_SESSION['auth']['admin_group'];
}
$dataOfCheckRule = protectSelect($conn,"SELECT id FROM `group_admin_event` WHERE id = :id AND id_group_admin = :ad_id",["id" => $id,"ad_id"=>$ad_id],1);
if(empty($dataOfCheckRule)){
    header('location: ' . base_url('/VIEW/ADMIN/showEvent.php'));
    exit;
}
$page = 1;
if(isset($_GET['page'])){
    $page = (int)$_GET['page'];
}


$admin_group = $_SESSION['auth']['admin_group']; 
$count_max = select($conn, "SELECT COUNT(m.id) AS C FROM `mem_event`AS e JOIN `members` as m ON e.id_mem = m.id WHERE e.id_event = $id AND m.is_deleted = 0; ");
$count_max = $count_max[0]['C'];
// echo $count_max;
$start = ($page-1)*25;

$sql = "SELECT m.id, m.fname, m.lname FROM `mem_event`AS e JOIN `members` as m ON e.id_mem = m.id WHERE e.id_event = $id AND m.is_deleted = 0 LIMIT $start,25;";
$data = select($conn,$sql);
?>
<?php $data_event = protectSelect($conn,"SELECT titel, details FROM `events` WHERE id = :id_event;",["id_event"=>$id],0) ?>
<div class="container mx-auto px-4 mt-4">
    <div class="overflow-x-auto">
        <h2 class="text-xl font-bold mb-4">รายชื่อผู้ใช้ใน <?php echo $data_event['titel'] ?> </h2>
        <p><?php echo $data_event['details'] ?></p>
        <button class = "btn btn-soft btn-primary" onclick="Modal_add_member.showModal()">เพิ่มสมาชิก</button>
        <table class="table w-full">
            <!-- head -->
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อ</th>
                    <th>นามสกุล</th>
                    <th>จัดการสมาชิก</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = $start + 1;
                foreach ($data as $value) { ?>
                <tr>
                    <th><?php echo $count++; ?></th>
                    <td><?php echo $value['fname']; ?></td>
                    <td><?php echo $value['lname']; ?></td>
                    
                    <td>
                        <!-- ปุ่มแก้ไข: ส่งข้อมูลไปยัง JS เพื่อเปิด Modal พร้อมเติมข้อมูลเดิม -->
                        <button onclick="openDeleteModal('<?php echo $value['id']; ?>','<?php echo $id; ?>')" class="btn btn-soft btn-error btn-sm">ลบ</button>
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
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/detail.php?page=1'); ?>'">1</button>
                <?php if ($page > 4) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
            <?php }

            for ($i = $start_page; $i <= $end_page; $i++) { 
                $active_class = ($i == $page) ? 'btn-active' : ''; 
                ?>
                <button class="join-item btn <?php echo $active_class; ?>" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/detail.php?page='.$i); ?>'">
                    <?php echo $i; ?>
                </button>
            <?php }

            if ($page < $count_btn - $range) { ?>
                <?php if ($page < $count_btn - $range - 1) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/detail.php?page='.$count_btn); ?>'">
                    <?php echo $count_btn; ?>
                </button>
            <?php } ?>
        </div>
    </div>
</div>

<dialog id="Modal_add_member" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-primary mb-4">เพิ่มสมาชิกเข้าร่วมรายการ</h3>
        
        <?php $data_group = protectSelect($conn, "SELECT DISTINCT g.id, g.titel FROM `base_group_member` AS b JOIN `group_mem` AS g ON b.id_name_group = g.id WHERE g.id_group_admin = :ad_id;", ["ad_id"=>$ad_id], 1) ?>
        
        <!-- 1. เลือกกลุ่มสมาชิก -->
        <div class="form-control mb-4">
            <label class="label"><span class="label-text font-semibold">เลือกกลุ่มสมาชิก</span></label>
            <select class="select select-bordered w-full" id="select_group_id" onchange="fetchMembersByGroup(this.value)">
                <option value="" disabled selected>-- กรุณาเลือกกลุ่ม --</option>
                <?php foreach ($data_group as $value) { ?>
                    <option value="<?php echo $value['id'] ?>"><?php echo $value['titel'] ?></option>
                <?php } ?>
            </select>
        </div>

        <!-- 2. ฟอร์มคุมรายการ Checkbox และปุ่มยืนยัน -->
        <form id="addMembersForm" action="../../API/admin/addMultipleMembersToEvent.php" method="POST">
            <!-- ส่ง ID ของ Event ไปด้วย -->
            <input type="hidden" name="id_event" value="<?php echo $id; ?>">

            <!-- ส่วนควบคุม เลือกทั้งหมด / ไม่เลือกเลย -->
            <div id="select_all_container" class="flex justify-between items-center mb-2 px-1 hidden">
                <label class="label cursor-pointer gap-2">
                    <input type="checkbox" id="check_all" class="checkbox checkbox-sm checkbox-primary" checked onchange="toggleSelectAll(this.checked)" />
                    <span class="label-text font-bold">เลือกทั้งหมด</span>
                </label>
                <span id="member_count" class="text-xs text-gray-500"></span>
            </div>

            <!-- พื้นที่แสดงผล Checkbox รายชื่อสมาชิก -->
            <div id="member_list_container" class="max-h-60 overflow-y-auto border border-base-300 rounded-box p-3 mb-4 hidden">
                <!-- JavaScript จะสร้าง Checkbox มาใส่ตรงนี้ -->
            </div>

            <div class="modal-action">
                <button type="submit" id="btn_submit_members" class="btn btn-primary hidden">ยืนยันการเพิ่มสมาชิก</button>
                <button type="button" class="btn" onclick="document.getElementById('Modal_add_member').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
function fetchMembersByGroup(groupId) {
    const container = document.getElementById('member_list_container');
    const selectAllContainer = document.getElementById('select_all_container');
    const submitBtn = document.getElementById('btn_submit_members');
    const countSpan = document.getElementById('member_count');
    const id_event = <?php echo $id; ?>

    console.error(id_event);
    console.error(groupId);
    if (!groupId) return;

    // รีเซ็ตการแสดงผล
    container.classList.remove('hidden');
    container.innerHTML = '<span class="loading loading-spinner loading-md"></span> กำลังโหลดข้อมูล...';
    selectAllContainer.classList.add('hidden');
    submitBtn.classList.add('hidden');

    // ดึงข้อมูลผ่าน AJAX
    fetch(`../../API/admin/getMembersByGroup.php?group_id=${groupId}&id_event=${id_event}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500 text-center">ไม่พบสมาชิกในกลุ่มนี้</p>';
                return;
            }

            let html = '<div class="space-y-2">';
            data.forEach(member => {
                // ให้ checked ตั้งแต่เริ่มต้นทุกอัน
                html += `
                    <label class="flex justify-between items-center p-2 bg-base-200 rounded-lg hover:bg-base-300 cursor-pointer">
                        <span class="text-sm">${member.fname} ${member.lname} (@${member.user})</span>
                        <input type="checkbox" name="member_ids[]" value="${member.id}" class="checkbox checkbox-primary member-checkbox" checked />
                    </label>
                `;
            });
            html += '</div>';

            container.innerHTML = html;
            selectAllContainer.classList.remove('hidden');
            submitBtn.classList.remove('hidden');
            document.getElementById('check_all').checked = true;
            countSpan.innerText = `ทั้งหมด ${data.length} คน`;
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<p class="text-sm text-error">เกิดข้อผิดพลาดในการดึงข้อมูล</p>';
        });
}

// ฟังก์ชัน สำหรับกด เลือกทั้งหมด / ไม่เลือกเลย
function toggleSelectAll(isChecked) {
    const checkboxes = document.querySelectorAll('.member-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = isChecked;
    });
}
</script>

<dialog id="Modal_del" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error mb-4">ลบสมาชิก</h3>
        <p class="py-2">คุณแน่ใจหรือไม่ว่าต้องการลบสมาชิกคนนี้?</p>
        
        <form id="deleteForm" action="../../API/admin/deletememberFromEvent.php" method="POST">
            <input type="hidden" id="delete_id" name="id">
            <input type="hidden" id="delete_id_event" name="id_event">

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

// 2. ฟังก์ชันเปิด Modal ลบ และใส่ ID ผู้ใช้
function openDeleteModal(id,id_event) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_id_event').value = id_event;
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

<!-- 
SELECT * FROM `members` AS m JOIN `base_group_member` AS b ON m.id = b.id_mem WHERE NOT EXISTS(
    SELECT id FROM `mem_event` AS me WHERE m.id = me.id_mem
) AND b.id_name_group = 1; -->

<!-- แยม
33
รับพิชิต
แม่วะหลวง
เม็ดบัว
กล้วย -->