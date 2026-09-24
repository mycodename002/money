<?php 
require_once "../../API/functions.php";
require_once '../TEMPLATES/user/head_user.php';
require_once '../TEMPLATES/user/nav_user.php';
require_once "../../API/alarmAndNotify.php";
include_once "../../db.php";

// ตรวจสอบสิทธิ์ Admin
if(!isset($_SESSION['auth']['rule']) || $_SESSION['auth']['rule'] != 'admin') {
    header('Location: ' . base_url('index.php')); 
    exit();
}

// ตรวจสอบ Param idGroup
if(!isset($_GET['idGroup']) || empty($_GET['idGroup'])){
    header('Location: ' . base_url('VIEW/ADMIN')); 
    exit();
}

$id =$_GET['idGroup'];
$admin_group =$_SESSION['auth']['admin_group'] ?? null;

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 25;
$start = ($page - 1) *$limit;

// 1. คิวรี่นับจำนวนสมาชิกทั้งหมดในกลุ่มที่ยังไม่ถูกลบ (is_deleted = 0)
$sql_count = "SELECT COUNT(bgm.id) AS C 
              FROM `base_group_member` AS bgm 
              INNER JOIN `members` AS m ON bgm.id_mem = m.id 
              WHERE bgm.id_name_group = :id AND m.is_deleted = 0";
$count_max_data = protectSelect($conn, $sql_count, ['id' =>$id], 0);
$count_max =$count_max_data['C'] ?? 0;

// 2. คิวรี่ดึงรายชื่อสมาชิกตาม Pagination
$sql = "SELECT m.id, m.fname, m.lname, m.user 
        FROM `base_group_member` AS bgm 
        INNER JOIN `members` AS m ON bgm.id_mem = m.id 
        WHERE bgm.id_name_group = :id AND m.is_deleted = 0 
        ORDER BY m.id DESC 
        LIMIT $start,$limit;";
$data = protectSelect($conn, $sql, ['id' =>$id], 1) ?: [];
?>

<div class="container mx-auto px-4 my-8">
    <div class="bg-base-100 p-6 rounded-2xl shadow-xl border border-base-200">
        <!-- Header & Top Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 mb-6 border-b border-base-200">
            <div>
                <a href="<?php echo base_url('VIEW/ADMIN'); ?>" class="btn btn-sm btn-ghost gap-1 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    ย้อนกลับ
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-base-content">รายชื่อผู้ใช้ในกลุ่ม</h2>
                <p class="text-sm text-base-content/60">จัดการสมาชิกและคำเชิญเข้าร่วมกลุ่ม</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button class="btn btn-primary shadow-md gap-2" onclick="Modal_add_memToGroup.showModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    เพิ่มสมาชิก
                </button>
                <button class="btn btn-outline btn-primary gap-2" onclick="Modal_link.showModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                    เชิญสมาชิก
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-base-200">
            <table class="table table-zebra w-full">
                <thead class="bg-base-200/60 text-base-content/80">
                    <tr>
                        <th class="w-16 text-center">ลำดับ</th>
                        <th>ชื่อ</th>
                        <th>นามสกุล</th>
                        <th>Username</th>
                        <th class="text-center w-32">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($data)) {
                        $count =$start + 1;
                        foreach ($data as$value) { ?>
                        <tr class="hover">
                            <th class="text-center font-normal text-base-content/70"><?php echo $count++; ?></th>
                            <td class="font-medium"><?php echo htmlspecialchars($value['fname']); ?></td>
                            <td><?php echo htmlspecialchars($value['lname']); ?></td>
                            <td><span class="badge badge-ghost badge-sm font-mono">@<?php echo htmlspecialchars($value['user']); ?></span></td>
                            <td class="text-center">
                                <button onclick="openDeleteModal('<?php echo $value['id']; ?>')" class="btn btn-error btn-soft btn-sm">
                                    ลบ
                                </button>
                            </td>
                        </tr>
                        <?php } 
                    } else { ?>
                        <tr>
                            <td colspan="5" class="text-center py-8 text-base-content/50">ไม่พบรายชื่อสมาชิกในกลุ่มนี้</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php 
        $count_btn = (int)ceil($count_max / $limit); 
        if ($count_btn > 1) {$range = 2;
            $start_page = max(1, $page -$range);
            $end_page   = min($count_btn, $page +$range);
        ?>
        <div class="flex justify-between items-center mt-6 flex-col sm:flex-row gap-4">
            <span class="text-xs text-base-content/60">แสดง <?php echo min($start + 1,$count_max); ?> ถึง <?php echo min($start + $limit,$count_max); ?> จากทั้งหมด <?php echo $count_max; ?> รายการ</span>
            <div class="join">
                <?php if ($page > 1) { ?>
                    <a class="join-item btn btn-sm" href="<?php echo base_url('VIEW/ADMIN/showMember.php?idGroup='.$id.'&page=1'); ?>">«</a>
                <?php } ?>

                <?php if ($start_page > 1) { ?>
                    <a class="join-item btn btn-sm" href="<?php echo base_url('VIEW/ADMIN/showMember.php?idGroup='.$id.'&page=1'); ?>">1</a>
                    <?php if ($start_page > 2) { ?><button class="join-item btn btn-sm btn-disabled">...</button><?php } ?>
                <?php } ?>

                <?php for ($i =$start_page; $i <=$end_page; $i++) {$active_class = ($i ==$page) ? 'btn-primary' : ''; 
                ?>
                    <a class="join-item btn btn-sm <?php echo $active_class; ?>" href="<?php echo base_url('VIEW/ADMIN/showMember.php?idGroup='.$id.'&page='.$i); ?>">
                        <?php echo $i; ?>
                    </a>
                <?php } ?>

                <?php if ($end_page <$count_btn) { ?>
                    <?php if ($end_page <$count_btn - 1) { ?><button class="join-item btn btn-sm btn-disabled">...</button><?php } ?>
                    <a class="join-item btn btn-sm" href="<?php echo base_url('VIEW/ADMIN/showMember.php?idGroup='.$id.'&page='.$count_btn); ?>"><?php echo $count_btn; ?></a>
                <?php } ?>

                <?php if ($page <$count_btn) { ?>
                    <a class="join-item btn btn-sm" href="<?php echo base_url('VIEW/ADMIN/showMember.php?idGroup='.$id.'&page='.($page+1)); ?>">»</a>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Modal เพิ่มสมาชิก -->
<dialog id="Modal_add_memToGroup" class="modal">
    <div class="modal-box w-11/12 max-w-2xl rounded-2xl shadow-2xl">
        <h3 class="font-bold text-xl mb-1 text-primary">เพิ่มสมาชิกเข้ากลุ่ม</h3>
        <p class="text-xs text-base-content/60 mb-4">ค้นหาและเลือกรายชื่อผู้ใช้ที่ต้องการเพิ่ม (เลือกได้มากกว่า 1 คน)</p>
        
        <form id="addMemGroupForm" action="../../API/admin/process.php" method="POST">
            <input type="hidden" name="action" value="addMemberToGroup">
            <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($id); ?>">
            <input type="hidden" id="selected_member_ids" name="member_ids" required>

            <!-- ช่องค้นหา -->
            <div class="form-control w-full mb-3">
                <label class="label py-1">
                    <span class="label-text font-semibold">ค้นหาสมาชิก</span>
                </label>
                <input type="text" 
                       id="search_member_input" 
                       class="input input-bordered w-full rounded-xl focus:input-primary" 
                       placeholder="พิมพ์ ชื่อ, นามสกุล หรือ Username..." 
                       oninput="onSearchInput()" 
                       autocomplete="off" />
            </div>

            <!-- รายชื่อสมาชิกที่ถูกเลือกทั้งหมด ( Badge Container ) -->
            <div id="selected_preview" class="mb-4 hidden">
                <label class="label py-1">
                    <span class="label-text-alt font-bold text-primary">เลือกแล้ว (<span id="selected_count">0</span> คน):</span>
                </label>
                <div id="selected_badge_container" class="flex flex-wrap gap-2 max-h-28 overflow-y-auto p-3 bg-base-200/50 rounded-xl border border-base-300">
                </div>
            </div>

            <!-- ผลลัพธ์ AJAX -->
            <div class="form-control w-full mb-4">
                <label class="label py-1">
                    <span class="label-text-alt text-base-content/60">ผลการค้นหา:</span>
                </label>
                <div id="member_list_box" class="max-h-56 overflow-y-auto border border-base-300 rounded-xl p-2 space-y-1 bg-base-100">
                    <div id="search_status" class="text-center py-6 text-base-content/40 text-sm">
                        พิมพ์ข้อความด้านบนเพื่อเริ่มค้นหาสมาชิก
                    </div>
                </div>
            </div>

            <div class="modal-action">
                <button type="button" class="btn btn-primary" onclick="openConfirmModal('addMemGroupForm')">บันทึกเข้ากลุ่ม</button>
                <button type="button" class="btn btn-ghost" onclick="closeAddMemModal()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button onclick="closeAddMemModal()">close</button>
    </form>
</dialog>

<!-- Modal ลบสมาชิก -->
<dialog id="Modal_del" class="modal">
    <div class="modal-box rounded-2xl">
        <h3 class="font-bold text-lg text-error mb-2">ยืนยันการลบสมาชิก</h3>
        <p class="py-2 text-sm text-base-content/70">คุณแน่ใจหรือไม่ว่าต้องการลบสมาชิกคนนี้ออกจากกลุ่ม?</p>
        
        <form id="deleteForm" action="../../API/admin/process.php" method="POST">
            <input type="hidden" id="delete_id" name="id">
            <input type="hidden" name="id_group" value="<?php echo htmlspecialchars($id); ?>">
            <input type="hidden" name="action" value="deleteMemberformGroup">

            <div class="modal-action">
                <button type="button" class="btn btn-error" onclick="openConfirmModal('deleteForm')">ยืนยันการลบ</button>
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('Modal_del').close()">ยกเลิก</button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
<dialog id="Modal_link" class="modal">
    <div class="modal-box text-center">
        <h3 class="font-bold text-xl text-primary mb-2">ลิ้งค์เชิญ</h3>
        
        <?php 
        $qrText = base_url('API/addmemberToGroup.php?id=' . $id);
        echo '<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . $qrText . '" alt="QR Code" class="mx-auto my-4">';
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
<!-- Modal Confirm -->
<dialog id="Modal_confirm" class="modal">
    <div class="modal-box text-center rounded-2xl max-w-sm">
        <div class="w-12 h-12 rounded-full bg-warning/20 text-warning flex items-center justify-center mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        </div>
        <h3 class="font-bold text-xl text-base-content mb-1">ยืนยันการทำรายการ</h3>
        <p class="text-sm text-base-content/70 mb-4">คุณต้องการดำเนินการตามรายการนี้ใช่หรือไม่?</p>
        <div class="modal-action justify-center gap-2">
            <button type="button" class="btn btn-primary px-6" onclick="submitEditForm()">ยืนยัน</button>
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('Modal_confirm').close()">ยกเลิก</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
var currentFormId = "";

function openDeleteModal(id) {
    document.getElementById('delete_id').value = id;
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
</script>

<script>
let searchTimeout = null;
const currentGroupId = "<?php echo htmlspecialchars($id); ?>";
let selectedMembers = new Map();

function onSearchInput() {
    clearTimeout(searchTimeout);
    const keyword = document.getElementById('search_member_input').value.trim();
    const listContainer = document.getElementById('member_list_box');

    if (keyword.length === 0) {
        listContainer.innerHTML = `<div class="text-center py-6 text-base-content/40 text-sm">พิมพ์ข้อความด้านบนเพื่อเริ่มค้นหาสมาชิก</div>`;
        return;
    }

    listContainer.innerHTML = `<div class="text-center py-6 text-base-content/40 text-sm">กำลังค้นหา...</div>`;

    searchTimeout = setTimeout(() => {
        fetchMembers(keyword);
    }, 300);
}

function fetchMembers(keyword) {
    const url = `../../API/admin/process.php?q=${encodeURIComponent(keyword)}&group_id=${currentGroupId}&action=searchMember`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const listContainer = document.getElementById('member_list_box');
            listContainer.innerHTML = '';

            if (!data || data.length === 0) {
                listContainer.innerHTML = `<div class="text-center py-6 text-base-content/40 text-sm">ไม่พบรายชื่อที่ค้นหา</div>`;
                return;
            }

            data.forEach(mem => {
                const isSelected = selectedMembers.has(String(mem.id));
                const item = document.createElement('div');
                item.className = `member-item p-2.5 rounded-lg cursor-pointer transition-all flex justify-between items-center ${isSelected ? 'bg-primary text-primary-content' : 'hover:bg-base-200'}`;
                item.setAttribute('data-id', mem.id);
                
                item.onclick = () => toggleSelectMember(item, mem);

                item.innerHTML = `
                    <div>
                        <span class="font-medium text-sm block">${escapeHtml(mem.fname)} ${escapeHtml(mem.lname)}</span>
                        <span class="text-xs opacity-75 block">@${escapeHtml(mem.user)}</span>
                    </div>
                    <span class="badge ${isSelected ? 'badge-warning' : 'badge-ghost'} badge-sm select-badge">
                        ${isSelected ? 'เลือกแล้ว' : 'เลือก'}
                    </span>
                `;
                listContainer.appendChild(item);
            });
        })
        .catch(err => {
            console.error(err);
            document.getElementById('member_list_box').innerHTML = `<div class="text-center py-6 text-error text-sm">เกิดข้อผิดพลาดในการดึงข้อมูล</div>`;
        });
}

function toggleSelectMember(element, mem) {
    const memId = String(mem.id);

    if (selectedMembers.has(memId)) {
        selectedMembers.delete(memId);
        element.classList.remove('bg-primary', 'text-primary-content');
        element.classList.add('hover:bg-base-200');
        
        const badge = element.querySelector('.select-badge');
        if(badge) {
            badge.innerText = 'เลือก';
            badge.className = 'badge badge-ghost badge-sm select-badge';
        }
    } else {
        selectedMembers.set(memId, {
            name: `${mem.fname} ${mem.lname}`,
            user: mem.user
        });
        element.classList.add('bg-primary', 'text-primary-content');
        element.classList.remove('hover:bg-base-200');

        const badge = element.querySelector('.select-badge');
        if(badge) {
            badge.innerText = 'เลือกแล้ว';
            badge.className = 'badge badge-warning badge-sm select-badge';
        }
    }

    updateSelectedUI();
}

function updateSelectedUI() {
    const container = document.getElementById('selected_badge_container');
    const previewBox = document.getElementById('selected_preview');
    const hiddenInput = document.getElementById('selected_member_ids');
    const countSpan = document.getElementById('selected_count');

    container.innerHTML = '';
    const ids = Array.from(selectedMembers.keys());

    if (ids.length > 0) {
        previewBox.classList.remove('hidden');
        countSpan.innerText = ids.length;

        selectedMembers.forEach((val, id) => {
            const badge = document.createElement('div');
            badge.className = 'badge badge-primary gap-1 py-3 px-3 shadow-sm';
            badge.innerHTML = `
                <span class="text-xs">${escapeHtml(val.name)} (@${escapeHtml(val.user)})</span>
                <button type="button" onclick="removeSelectedMember('${id}')" class="btn btn-ghost btn-xs btn-circle text-xs">✕</button>
            `;
            container.appendChild(badge);
        });

        hiddenInput.value = JSON.stringify(ids);
    } else {
        previewBox.classList.add('hidden');
        hiddenInput.value = '';
    }
}

function removeSelectedMember(id) {
    selectedMembers.delete(String(id));
    
    const activeItem = document.querySelector(`.member-item[data-id="${id}"]`);
    if (activeItem) {
        activeItem.classList.remove('bg-primary', 'text-primary-content');
        activeItem.classList.add('hover:bg-base-200');
        const badge = activeItem.querySelector('.select-badge');
        if(badge) {
            badge.innerText = 'เลือก';
            badge.className = 'badge badge-ghost badge-sm select-badge';
        }
    }

    updateSelectedUI();
}

function closeAddMemModal() {
    document.getElementById('search_member_input').value = '';
    selectedMembers.clear();
    updateSelectedUI();
    document.getElementById('member_list_box').innerHTML = `<div class="text-center py-6 text-base-content/40 text-sm">พิมพ์ข้อความด้านบนเพื่อเริ่มค้นหาสมาชิก</div>`;
    document.getElementById('Modal_add_memToGroup').close();
}

function escapeHtml(text) {
    return text ? text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
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

<?php require_once "./modal.php"; ?>