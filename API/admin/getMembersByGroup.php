<?php 
include_once "../../db.php";
require_once "../functions.php";
require_once "../auth.php";


if(!($_SESSION['auth']['rule'] == 'admin')){
    $_SESSION['alarm'] = "กลับไปเข้าสู่ระบบก่อน";
    header(base_url('index.php'));
    exit;
}

header('Content-Type: application/json');
// require_once '../../config/database.php'; // นำเข้าไฟล์เชื่อมต่อ DB ของคุณ

$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
$id_event = isset($_GET['id_event']) ? intval($_GET['id_event']) : 0;
if ($group_id > 0) {
    
    // ==========================================
    // *** ตรงนี้คือส่วนที่คุณเขียน SQL เพิ่ม ***
    // ==========================================
   $sql = "SELECT m.id, m.fname, m.lname, m.user 
FROM `members` AS m 
JOIN `base_group_member` AS b ON m.id = b.id_mem 
WHERE m.admin_group = :group_id
  AND m.is_deleted = 0
  AND NOT EXISTS (
      SELECT 1 
      FROM `mem_event` AS me 
      WHERE me.id_mem = m.id 
        AND me.id_event = :id_event
  );";

    $members = protectSelect($conn, $sql, [
        "group_id" => $group_id,
        "id_event" => $id_event
    ], 1);
    
    // ส่งผลลัพธ์ออกเป็น JSON ให้ JavaScript นำไปแสดงผล
    echo json_encode($members);
} else {
    echo json_encode([]);
}
?>