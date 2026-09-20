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
//คิวรี่ผิดหมดเลย กำลังแก้
$page = 1;
if(isset($_GET['page'])){
    $page = (int)$_GET['page'];
}
if(!isset($_GET['idGroup'])){
    header('Location: ' . base_url('VIEW/ADMIN')); 
    exit;
}
$id = $_GET['idGroup'];
$admin_group = $_SESSION['auth']['admin_group'];
$count_max = protectSelect($conn,"SELECT  COUNT(bgm.id) AS C FROM `base_group_member` AS bgm WHERE bgm.id_name_group = :id ;",['id'=>$id],0);
$count_max = $count_max['C'];
$start = ($page-1)*25;

// เพิ่มการดึงฟิลด์ user มาด้วยเพื่อนำไปแสดงใน Modal แก้ไข
$sql = "SELECT  m.fname, m.lname,m.id,m.user FROM `base_group_member` AS bgm JOIN `members` AS m ON bgm.id_mem = m.id WHERE bgm.id_name_group = :id AND m.is_deleted = 0 LIMIT $start,25;";
$data = protectSelect($conn,$sql,['id'=>$id],1);
?>

<div class="container mx-auto px-4 mt-4  shadow-md">
    
    <div class="overflow-x-auto mt-8">
        <h2 class="text-xl font-bold mb-4">รายชื่อผู้ใช้ ในกลุ่ม</h2>
        <button class = "btn btn-soft btn-primary" onclick="Modal_add_memToGroup.showModal()">เพิ่มสมาชิก</button>
        <button class = "btn btn-soft btn-primary" onclick="Modal_link.showModal()">เชิญสมาชิก</button>
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
<dialog id="Modal_add_memToGroup" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="font-bold text-lg mb-4 text-primary">เพิ่มรายชื่อลงในกลุ่ม (เลือกได้หลายคน)</h3>
        
        <form id="addMemGroupForm" action="../../API/admin/process.php" method="POST">
            <input type="hidden" name="action" value="addMemberToGroup">
            <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($id); ?>">
            
            <!-- Hidden Input สำหรับเก็บ Array ของ member_id ที่ถูกเลือกทั้งหมด -->
            <input type="hidden" id="selected_member_ids" name="member_ids" required>

            <!-- ช่องค้นหา -->
            <div class="form-control w-full mb-3">
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

            <!-- รายชื่อสมาชิกที่ถูกเลือกทั้งหมด ( Badge/Chip Container ) -->
            <div id="selected_preview" class="mb-4 hidden">
                <label class="label">
                    <span class="label-text-alt font-bold text-primary">เลือกแล้ว (<span id="selected_count">0</span> คน):</span>
                </label>
                <div id="selected_badge_container" class="flex flex-wrap gap-2 max-h-28 overflow-y-auto p-2 bg-base-200 rounded-box border border-base-300">
                    <!-- รายการ Badge จะถูกสร้างขึ้นที่นี่ด้วย JS -->
                </div>
            </div>

            <!-- กล่องแสดงผลลัพธ์จาก AJAX -->
            <div class="form-control w-full mb-4">
                <label class="label">
                    <span class="label-text-alt text-gray-500">ผลการค้นหา (คลิกเพื่อเลือก/ยกเลิก):</span>
                </label>
                <div id="member_list_box" class="max-h-52 overflow-y-auto border border-base-300 rounded-box p-2 space-y-1">
                    <div id="search_status" class="text-center py-4 text-gray-400">
                        พิมพ์ข้อความด้านบนเพื่อเริ่มค้นหาสมาชิก
                    </div>
                </div>
            </div>

            <div class="modal-action">
                <button type="button" class="btn btn-soft btn-success" onclick="openConfirmModal('addMemGroupForm')">บันทึกเข้ากลุ่ม</button>
                <button type="button" class="btn" onclick="closeAddMemModal()">ยกเลิก</button>
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
        
        <form id="deleteForm" action="../../API/admin/process.php" method="POST">
            <input type="hidden" id="delete_id" name="id">
            <input type="hidden" id="" name="id_group" value="<?php echo $id; ?>">
            <input type="hidden" id="" name="action" value="deleteMemberformGroup">

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
var currentFormId = "";

function openEditModal(id, fname, lname, user) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_fname').value = fname;
    document.getElementById('edit_lname').value = lname;
    document.getElementById('edit_user').value = user;

    document.getElementById('Modal_edit').showModal();
}

function openDeleteModal(id) {
    console.warn(id);
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
const currentGroupId = "<?php echo $id; ?>";

let selectedMembers = new Map();

function onSearchInput() {
    clearTimeout(searchTimeout);
    const keyword = document.getElementById('search_member_input').value.trim();
    const listContainer = document.getElementById('member_list_box');

    if (keyword.length === 0) {
        listContainer.innerHTML = `<div class="text-center py-4 text-gray-400">พิมพ์ข้อความด้านบนเพื่อเริ่มค้นหาสมาชิก</div>`;
        return;
    }

    listContainer.innerHTML = `<div class="text-center py-4 text-gray-400">กำลังค้นหา...</div>`;

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
                listContainer.innerHTML = `<div class="text-center py-4 text-gray-400">ไม่พบรายชื่อที่ค้นหา</div>`;
                return;
            }

            data.forEach(mem => {
                const isSelected = selectedMembers.has(String(mem.id));
                const item = document.createElement('div');
                item.className = `member-item p-2 rounded-lg cursor-pointer transition-colors flex justify-between items-center ${isSelected ? 'bg-primary text-primary-content' : 'hover:bg-base-200'}`;
                item.setAttribute('data-id', mem.id);
                
                item.onclick = () => toggleSelectMember(item, mem);

                item.innerHTML = `
                    <div>
                        <span class="font-medium">${escapeHtml(mem.fname)} ${escapeHtml(mem.lname)}</span>
                        <span class="text-xs ${isSelected ? 'text-primary-content/80' : 'text-gray-500'} block">@${escapeHtml(mem.user)}</span>
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
            document.getElementById('member_list_box').innerHTML = `<div class="text-center py-4 text-error">เกิดข้อผิดพลาดในการดึงข้อมูล</div>`;
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
            badge.className = 'badge badge-primary gap-1 py-3 px-3';
            badge.innerHTML = `
                <span>${escapeHtml(val.name)} (@${escapeHtml(val.user)})</span>
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
    document.getElementById('member_list_box').innerHTML = `<div class="text-center py-4 text-gray-400">พิมพ์ข้อความด้านบนเพื่อเริ่มค้นหาสมาชิก</div>`;
    document.getElementById('Modal_add_memToGroup').close();
}

function escapeHtml(text) {
    return text ? text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
}
</script>

<?php require_once "./modal.php"; ?>