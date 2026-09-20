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

$id = $_GET['id'] ?? null;
$ad_id = $_SESSION['auth']['admin_group'] ?? null;

$dataOfCheckRule = protectSelect($conn,"SELECT id FROM `events` WHERE id = :id AND id_group_admin = :ad_id",["id" => $id,"ad_id"=>$ad_id],1);
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
$start = ($page-1)*25;

$sql = 'SELECT 
    m.id, 
    m.fname, 
    m.lname, 
    s.status, 
    s.file_name, 
    s.date, 
    s.amount,
    s.id AS slip_id
FROM `mem_event` AS e 
JOIN `members` AS m ON e.id_mem = m.id 
LEFT JOIN `slips` AS s ON m.id = s.add_by AND s.id_event = e.id_event
WHERE e.id_event = :id_event AND m.is_deleted = 0 
LIMIT 0, 25;';
$data = protectSelect($conn,$sql,['id_event'=>$id],1);

$data_event = protectSelect($conn,"SELECT title, details FROM `events` WHERE id = :id_event;",["id_event"=>$id],0);
$data_group = protectSelect($conn, "SELECT DISTINCT g.id, g.title FROM `base_group_member` AS b JOIN `group_mem` AS g ON b.id_name_group = g.id WHERE g.id_group_admin = :ad_id;", ["ad_id"=>$ad_id], 1);
?>

<!-- ========================================== -->
<!-- 1. MAIN CONTENT / TABLE                    -->
<!-- ========================================== -->
<div class="container mx-auto p-4 mt-4 shadow-md ">
    <div class="overflow-x-auto">
        <h2 class="text-xl font-bold mb-4">รายชื่อผู้ใช้ใน <?php echo htmlspecialchars($data_event['title'] ?? '') ?> </h2>
        <p><?php echo htmlspecialchars($data_event['details'] ?? '') ?></p>
        <div class="my-4 space-x-2">
            <button class="btn btn-soft btn-primary" onclick="Modal_add_member.showModal()">เพิ่มสมาชิก</button>
            <button class="btn btn-soft btn-primary" onclick="Modal_link.showModal()">ลิงค์เชิญ</button>
            <button class="btn btn-soft btn-success" onclick="Modal_eventSuccess.showModal()">รายการเสร็จสิ้น</button>
        </div>

        <table class="table w-full">
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อ</th>
                    <th>นามสกุล</th>
                    <th>สลิป</th>
                    <th>สถานะ</th>
                    <th>จัดการสมาชิก</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $count =$start + 1;
            foreach ($data as $value) {$badge = 'badge-success'; 
                if (empty($value['status'])) {$badge = 'badge-error';
                } else if ($value['status'] == 'ไม่ผ่าน') {
                    $badge = 'badge-error';
                } else if ($value['status'] == 'รอตรวจสอบ') {
                    $badge = 'badge-warning';
                } 
            ?>
                <tr>
                    <th><?php echo $count++; ?></th>
                    <td><?php echo htmlspecialchars($value['fname']); ?></td>
                    <td><?php echo htmlspecialchars($value['lname']); ?></td>
                    <td>
                        <button onclick='openSlipModal(<?= json_encode($value, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
                                class="btn btn-soft btn-primary btn-sm <?php echo empty($value['file_name']) ? 'btn-disabled' : ''; ?>">
                            สลิป
                        </button>
                    </td>
                    <td>
                        <div class="badge badge-soft badge-sm <?php echo $badge; ?>">
                            <?php echo empty($value['status']) ? 'ยังไม่ได้แนบสลิป' : htmlspecialchars($value['status']); ?>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button type="button" 
                                    class="btn btn-sm btn-outline btn-primary"
                                    onclick="openAdminUploadSlipModal(<?= $value['id'] ?>, '<?= htmlspecialchars($value['fname'] . ' ' .$value['lname'], ENT_QUOTES) ?>')">
                                แนบสลิปแทน
                            </button>
                            
                            <button onclick="openDeleteModal('<?php echo $value['id']; ?>','<?php echo$id; ?>')" 
                                    class="btn btn-soft btn-error btn-sm">
                                ลบ
                            </button>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="join mt-4">
            <?php 
            $count_btn = (int)ceil($count_max / 25);$range = 2;
            $start_page = max(1, $page -$range);
            $end_page   = min($count_btn, $page +$range);

            if ($page > 3) { ?>
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/detail.php?id='.$id.'&page=1'); ?>'">1</button>
                <?php if ($page > 4) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
            <?php }

            for ($i =$start_page; $i <=$end_page; $i++) {$active_class = ($i ==$page) ? 'btn-active' : ''; 
                ?>
                <button class="join-item btn <?php echo $active_class; ?>" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/detail.php?id='.$id.'&page='.$i); ?>'">
                    <?php echo $i; ?>
                </button>
            <?php }

            if ($page < $count_btn -$range) { ?>
                <?php if ($page < $count_btn -$range - 1) { ?>
                    <button class="join-item btn">...</button>
                <?php } ?>
                <button class="join-item btn" onclick="window.location.href = '<?php echo base_url('VIEW/ADMIN/detail.php?id='.$id.'&page='.$count_btn); ?>'">
                    <?php echo $count_btn; ?>
                </button>
            <?php } ?>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. ALL MODALS                              -->
<!-- ========================================== -->

<dialog id="Modal_link" class="modal">
    <div class="modal-box text-center">
        <h3 class="font-bold text-xl text-primary mb-2">ลิ้งค์เชิญ</h3>
        
        <?php 
        require_once '../../LIB/phpqrcode/qrlib.php';

        $tempDir = './temp/';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0775, true);
        }
        $qrText = base_url('API/addmemberToevent.php?id=' . $id);
        $filename = $tempDir . md5($qrText) . '.png';
        QRcode::png($qrText, $filename, QR_ECLEVEL_L, 4, 2);
        echo '<img src="' . $filename . '" alt="QR Code" class="mx-auto my-4">';
        ?>
        <p id="Link"><?php echo $qrText ?></p>
        <button class="btn btn-soft btn-primary btn-sm mt-4" type="button" onclick="copyToClipboard()" id = 'copyBtn'>คัดลอก</button>
        <div class="modal-action justify-center gap-4 mt-4">
            <!-- <button type="button" class="btn btn-ghost" onclick="document.getElementById('Modal_link').close()">ปิด</button> -->
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
<!-- Modal: Upload Slip Admin -->
<dialog id="Modal_uploadSlipAdmin" class="modal">
    <div class="modal-box max-w-lg">
        <button type="button" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" onclick="document.getElementById('Modal_uploadSlipAdmin').close()">✕</button>
        <h3 class="font-bold text-lg border-b pb-2 mb-4 text-primary">แนบสลิปโอนเงิน (สำหรับแอดมิน)</h3>

        <form method="POST" action="<?= base_url('/API/admin/process.php') ?>" enctype="multipart/form-data">
            <input type="hidden" name="id_event" id="admin_slip_id_event" value="<?= htmlspecialchars($id ?? '') ?>">
            <input type="hidden" name="add_by" id="admin_slip_add_by">
            <input type="hidden" name="action" value="upload_slip_by_admin">
            
            <div class="space-y-4 text-sm">
                <div class="bg-base-200 p-3 rounded-lg border">
                    <span class="text-gray-500 block text-xs">แนบสลิปแทนสมาชิก:</span>
                    <span id="admin_slip_member_name" class="font-bold text-base text-base-content">-</span>
                </div>

                <div class="form-control w-full">
                    <label class="label font-medium mb-1">
                        <span class="label-text">แนบไฟล์สลิป (รูปภาพ) <span class="text-error">*</span></span>
                    </label>
                    <input type="file" name="slip_file" accept="image/*" class="file-input file-input-bordered w-full" required onchange="previewImage(this)">
                    <div id="image_preview_wrapper" class="mt-2 hidden text-center">
                        <img id="image_preview" src="" alt="ตัวอย่างสลิป" class="max-h-48 mx-auto rounded border shadow-sm">
                    </div>
                </div>
                
                <div class="form-control w-full">
                    <label class="label font-medium mb-1">
                        <span class="label-text">กำหนดสถานะ</span>
                    </label>
                    <select name="status" class="select select-bordered w-full">
                        <option value="ผ่าน" selected>ผ่าน</option>
                        <option value="ไม่ผ่าน">ไม่ผ่าน</option>
                    </select>
                </div>
            </div>

            <div class="modal-action gap-2 mt-6">
                <button type="submit" class="btn btn-primary text-white px-6">บันทึกสลิป</button>
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('Modal_uploadSlipAdmin').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- Modal: Slip Detail -->
<dialog id="Modal_slipDetail" class="modal">
    <div class="modal-box max-w-lg">
        <button type="button" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" onclick="document.getElementById('Modal_slipDetail').close()">✕</button>
        <h3 class="font-bold text-lg border-b pb-2 mb-4">รายละเอียดสลิปและการตรวจสอบ</h3>

        <form method="POST" action="<?= base_url('/API/admin/process.php') ?>">
            <input type="hidden" name="slip_id" id="modal_slip_id">
            <input type="hidden" name="event_id" value="<?= htmlspecialchars($id ?? '') ?>">
            <input type="hidden" name="action" value="update_slip_status">

            <div class="space-y-4">
                <div class="flex flex-col items-center justify-center bg-base-200 p-3 rounded-lg min-h-[200px]">
                    <img id="modal_slip_img" src="" alt="สลิปโอนเงิน" class="max-h-72 object-contain rounded shadow hidden">
                    <p id="modal_no_img" class="text-gray-400 text-sm hidden">ไม่มีรูปภาพสลิป</p>
                </div>

                <div class="grid grid-cols-2 gap-2 text-sm bg-base-100 p-3 rounded-lg border">
                    <div>
                        <span class="text-gray-500 block">ชื่อ-นามสกุล:</span>
                        <span id="modal_fullname" class="font-semibold text-base-content">-</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">วันที่อัปโหลด:</span>
                        <span id="modal_upload_date" class="font-semibold text-base-content">-</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">จำนวนเงิน:</span>
                        <span id="modal_amount" class="font-semibold">-</span>
                    </div>
                </div>

                <div class="form-control w-full">
                    <label class="label font-medium mb-1" for="modal_status_select">
                        <span class="label-text">ปรับสถานะการตรวจสอบ</span>
                    </label>
                    <select name="status" id="modal_status_select" class="select select-bordered w-full">
                        <option value="ผ่าน">ผ่าน</option>
                        <option value="ไม่ผ่าน">ไม่ผ่าน</option>
                    </select>
                </div>
            </div>

            <div class="modal-action gap-2 mt-6">
                <button type="submit" class="btn btn-primary text-white px-6">บันทึกเปลี่ยนแปลง</button>
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('Modal_slipDetail').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- Modal: Add Member -->
<dialog id="Modal_add_member" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="font-bold text-lg text-primary mb-4">เพิ่มสมาชิกเข้าร่วมรายการ</h3>
        
        <!-- 1. เลือกกลุ่มสมาชิก -->
        <div class="form-control mb-3">
            <label class="label"><span class="label-text font-semibold">เลือกกลุ่มสมาชิก</span></label>
            <select class="select select-bordered w-full" id="select_group_id" onchange="fetchMembersByGroup(this.value)">
                <option value="" disabled selected>-- กรุณาเลือกกลุ่ม --</option>
                <?php foreach ($data_group as$value) { ?>
                    <option value="<?php echo $value['id'] ?>"><?php echo htmlspecialchars($value['title']) ?></option>
                <?php } ?>
            </select>
        </div>

        <!-- 2. ช่องค้นหา -->
        <div id="search_container" class="form-control w-full mb-3">
            <label class="label">
                <span class="label-text font-semibold">ค้นหาสมาชิก (Username, ชื่อ หรือ นามสกุล)</span>
            </label>
            <input type="text" 
                   id="search_member_input" 
                   class="input input-bordered w-full" 
                   placeholder="พิมพ์อย่างน้อย 1 ตัวอักษรเพื่อค้นหา..." 
                   oninput="onSearchInput()" 
                   autocomplete="off" />
        </div>

        <form id="addMembersForm" action="../../API/admin/process.php" method="POST">
            <input type="hidden" name="id_event" value="<?php echo $id; ?>">
            <input type="hidden" name="action" value="addMultipleMembersToEvent">

            <!-- 3. แถบเลือกทั้งหมด -->
            <div id="select_all_container" class="flex justify-between items-center mb-2 px-1 hidden">
                <label class="label cursor-pointer gap-2">
                    <input type="checkbox" id="check_all" class="checkbox checkbox-sm checkbox-primary" checked onchange="toggleSelectAll(this.checked)" />
                    <span class="label-text font-bold">เลือกทั้งหมด</span>
                </label>
                <span id="member_count" class="text-xs text-gray-500"></span>
            </div>

            <!-- 4. กล่องแสดงผลลัพธ์การค้นหา / รายชื่อสมาชิก -->
            <div id="member_list_container" class="max-h-60 overflow-y-auto border border-base-300 rounded-box p-3 mb-4 space-y-1">
                <div id="search_status" class="text-center py-4 text-gray-400">
                    เลือกกลุ่มสมาชิก หรือพิมพ์ค้นหาด้านบนเพื่อเริ่มต้น
                </div>
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

<!-- Modal: Delete Member -->
<dialog id="Modal_del" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error mb-4">ลบสมาชิก</h3>
        <p class="py-2">คุณแน่ใจหรือไม่ว่าต้องการลบสมาชิกคนนี้?</p>
        
        <form id="deleteForm" action="../../API/admin/process.php" method="POST">
            <input type="hidden" id="delete_id" name="id">
            <input type="hidden" id="delete_id_event" name="id_event">
            <input type="hidden" name="action" value="deletememberFromEvent">

            <div class="modal-action">
                <button type="button" class="btn btn-error" onclick="openConfirmModal('deleteForm')">ยืนยันการลบ</button>
                <button type="button" class="btn" onclick="document.getElementById('Modal_del').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- Modal: General Confirm -->
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

<!-- Modal: Event Success -->
<dialog id="Modal_eventSuccess" class="modal">
    <div class="modal-box text-center">
        <h3 class="font-bold text-xl text-warning mb-2">ยืนยันการทำรายการ</h3>
        <p class="py-2 text-gray-600">คุณต้องการดำเนินการตามรายการนี้ใช่หรือไม่?</p>
        
        <form method="POST" action="<?= base_url('/API/admin/process.php') ?>">
            <input type="hidden" name="event_id" id="event_success_id" value="<?php echo $id; ?>">
            <input type="hidden" name="action" value="eventSuccess">

            <div class="modal-action justify-center gap-4 mt-6">
                <button type="submit" class="btn btn-success px-6 text-white">ยืนยัน</button>
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('Modal_eventSuccess').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- ========================================== -->
<!-- 3. JAVASCRIPT SECTION                      -->
<!-- ========================================== -->
<script>
var searchTimeout = null;
var currentFormId = "";

// Helper ฟังก์ชันสำหรับ Safe HTML Encoding ใน JS
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ------------------------------------------
// Modal Show / Trigger Functions
// ------------------------------------------
function openAdminUploadSlipModal(memberId, memberName) {
    document.getElementById('admin_slip_add_by').value = memberId;
    document.getElementById('admin_slip_member_name').innerText = memberName;
    document.getElementById('image_preview_wrapper').classList.add('hidden');
    document.getElementById('image_preview').src = '';
    document.getElementById('Modal_uploadSlipAdmin').showModal();
}

function openSlipModal(data) {
    document.getElementById('modal_slip_id').value = data.slip_id;
    document.getElementById('modal_fullname').innerText = `${data.fname} ${data.lname}`;
    document.getElementById('modal_upload_date').innerText = data.date ? data.date : 'ยังไม่อัปโหลด';
    document.getElementById('modal_amount').innerText = data.amount ? parseFloat(data.amount).toLocaleString() + ' บาท' : '-';

    const imgElement = document.getElementById('modal_slip_img');
    const noImgElement = document.getElementById('modal_no_img');

    if (data.file_name) {
        imgElement.src = "<?php echo base_url('STORAGES/IMG/') ?>" + data.file_name;
        imgElement.classList.remove('hidden');
        noImgElement.classList.add('hidden');
    } else {
        imgElement.src = "";
        imgElement.classList.add('hidden');
        noImgElement.classList.remove('hidden');
    }

    const currentStatus = (data.status !== null && data.status !== undefined) ? data.status : 0;
    document.getElementById('modal_status_select').value = currentStatus;

    document.getElementById('Modal_slipDetail').showModal();
}

function openDeleteModal(id, id_event) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_id_event').value = id_event;
    document.getElementById('Modal_del').showModal();
}

function openConfirmModal(formId) {
    currentFormId = formId;
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

function previewImage(input) {
    const previewWrapper = document.getElementById('image_preview_wrapper');
    const previewImg = document.getElementById('image_preview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewWrapper.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        previewWrapper.classList.add('hidden');
    }
}

function toggleSelectAll(isChecked) {
    const checkboxes = document.querySelectorAll('.member-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = isChecked;
    });
}

// ------------------------------------------
// Member Search & Fetching Functions
// ------------------------------------------
function onSearchInput() {
    clearTimeout(searchTimeout);
    const keyword = document.getElementById('search_member_input').value.trim();
    const container = document.getElementById('member_list_container');

    if (keyword.length === 0) {
        container.innerHTML = `<div class="text-center py-4 text-gray-400">เลือกกลุ่มสมาชิก หรือพิมพ์ค้นหาด้านบนเพื่อเริ่มต้น</div>`;
        return;
    }

    container.innerHTML = `<div class="text-center py-4 text-gray-400"><span class="loading loading-spinner loading-md"></span> กำลังค้นหา...</div>`;

    searchTimeout = setTimeout(() => {
        fetchMembers(keyword);
    }, 300);
}

function fetchMembers(keyword) {
    const container = document.getElementById('member_list_container');
    const selectAllContainer = document.getElementById('select_all_container');
    const submitBtn = document.getElementById('btn_submit_members');
    const countSpan = document.getElementById('member_count');

    const url = `../../API/admin/process.php?q=${encodeURIComponent(keyword)}&group_id=${<?php echo json_encode($_SESSION['auth']['admin_group'] ?? ''); ?>}&action=searchMember`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data || data.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">ไม่พบรายชื่อที่ค้นหา</p>';
                selectAllContainer.classList.add('hidden');
                submitBtn.classList.add('hidden');
                return;
            }

            let html = '<div class="space-y-2">';
            data.forEach(member => {
                html += `
                    <label class="flex justify-between items-center p-2 bg-base-200 rounded-lg hover:bg-base-300 cursor-pointer">
                        <span class="text-sm">${escapeHtml(member.fname)} ${escapeHtml(member.lname)} (@${escapeHtml(member.user)})</span>
                        <input type="checkbox" name="member_ids[]" value="${member.id}" class="checkbox checkbox-primary member-checkbox" checked />
                    </label>
                `;
            });
            html += '</div>';

            container.innerHTML = html;
            selectAllContainer.classList.remove('hidden');
            submitBtn.classList.remove('hidden');
            
            const checkAll = document.getElementById('check_all');
            if (checkAll) checkAll.checked = true;
            if (countSpan) countSpan.innerText = `ทั้งหมด ${data.length} คน`;
        })
        .catch(err => {
            console.error('Error:', err);
            container.innerHTML = '<p class="text-sm text-error text-center py-4">เกิดข้อผิดพลาดในการดึงข้อมูล</p>';
        });
}

function fetchMembersByGroup(groupId) {
    const container = document.getElementById('member_list_container');
    const selectAllContainer = document.getElementById('select_all_container');
    const submitBtn = document.getElementById('btn_submit_members');
    const countSpan = document.getElementById('member_count');
    const id_event = <?php echo json_encode($id); ?>;

    if (!groupId) return;

    container.classList.remove('hidden');
    container.innerHTML = '<div class="text-center py-4 text-gray-400"><span class="loading loading-spinner loading-md"></span> กำลังโหลดข้อมูล...</div>';
    selectAllContainer.classList.add('hidden');
    submitBtn.classList.add('hidden');

    fetch(`<?php echo base_url();?>API/admin/process.php?group_id=${groupId}&id_event=${id_event}&action=getMembersByGroup`)
        .then(response => response.json())
        .then(data => {
            if (!data || data.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">ไม่พบสมาชิกในกลุ่มนี้</p>';
                return;
            }

            let html = '<div class="space-y-2">';
            data.forEach(member => {
                html += `
                    <label class="flex justify-between items-center p-2 bg-base-200 rounded-lg hover:bg-base-300 cursor-pointer">
                        <span class="text-sm">${escapeHtml(member.fname)} ${escapeHtml(member.lname)} (@${escapeHtml(member.user)})</span>
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
            container.innerHTML = '<p class="text-sm text-error text-center py-4">เกิดข้อผิดพลาดในการดึงข้อมูล</p>';
        });
}

function copyToClipboard() {
    // 1. ดึงข้อความจาก Input
    const copyText = document.getElementById("Link");
    const copyBtn = document.getElementById("copyBtn");

    // 2. ใช้ Clipboard API ในการคัดลอก
    navigator.clipboard.writeText(copyText.innerHTML).then(() => {
        // 3. เปลี่ยนข้อความบนปุ่มชั่วคราวเพื่อแจ้งเตือนผู้ใช้
        const originalText = copyBtn.innerHTML;
        copyBtn.innerHTML = "คัดลอกแล้ว!";
        copyBtn.classList.remove("btn-primary");
        copyBtn.classList.add("btn-success");

        // 4. คืนค่าปุ่มกลับเป็นเหมือนเดิมหลังจากผ่านไป 2 วินาที
        setTimeout(() => {
            copyBtn.innerHTML = originalText;
            copyBtn.classList.remove("btn-success");
            copyBtn.classList.add("btn-primary");
        }, 2000);
    }).catch(err => {
        console.error("ไม่สามารถคัดลอกข้อความได้: ", err);
    });
}
</script>