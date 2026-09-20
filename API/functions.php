<?php 
// require_once "./base.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// รันบนพอร์ตไหนก็มาแก้ด้วยเด้อ
define('BASE_URL', 'http://localhost:8000/');

function base_url($path = '') {
    return BASE_URL . ltrim($path, '/');
}
function select($conn,$sql)  {
    $query = "$sql";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

function protectSelect($conn,$sql,$param,$multi){
    $stmt = $conn->prepare($sql);
    $stmt->execute($param);
    if($multi) $data = $stmt->fetchAll();
    else $data = $stmt->fetch();
    return $data;
}  

function delete($conn, $sql, $param){
    $tempSQL = $conn->prepare($sql);
    $tempSQL->execute($param);
}

function update($conn, $sql, $param){
    $tempSQL = $conn->prepare($sql);
    $tempSQL->execute($param);
}
function insert($conn, $sql) {
    $conn->exec($sql);
}

function protectInsert($conn,$sql,$param) {
    $tempSQL = $conn->prepare($sql);
    $tempSQL->execute($param);
}

function queryExecute($conn, $sql, $params) {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
}

function backPage() {
    echo '<script type="text/javascript">
    if (document.referrer) {
        window.location.href = document.referrer;
    } else {
        window.history.back();
    }
    </script>';
}

function checkAction($action,$post=true){
    if($post){
        if(!isset($_POST['action'])){
            return false;
        }
        if($_POST['action'] == $action){
            return true;
        }
        return false;
    }
    if(!isset($_GET['action'])){
        return false;
    }
    if($_GET['action'] == $action){
        return true;
    }
    return false;
    
}

function uploadSlipImage($fileArray, $id_event, $user_id) {
    // 1. เช็คว่ามีไฟล์ส่งมา และไม่มีข้อผิดพลาดเบื้องต้น
    if (!isset($fileArray) || $fileArray['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'filename' => null, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์'];
    }

    $fileTmpPath   = $fileArray['tmp_name'];
    $fileName      = $fileArray['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // 2. ตรวจสอบนามสกุลไฟล์
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        return ['status' => false, 'filename' => null, 'message' => 'รองรับเฉพาะไฟล์รูปภาพ (JPG, JPEG, PNG, WEBP) เท่านั้น'];
    }

    // 3. ตั้งชื่อไฟล์ตามหลักการ: idevent_date(YmdHis)_userid.extension
    // เช่น event_1_20260920163000_10.jpg
    $dateStr = date('YmdHis');
    $newFileName = "event_{$id_event}_{$dateStr}_{$user_id}.{$fileExtension}";

    // 4. กำหนดโฟลเดอร์จัดเก็บ
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/STORAGES/IMG/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $destPath = $uploadDir . $newFileName;

    // 5. ย้ายไฟล์ไปยังโฟลเดอร์เป้าหมาย
    if (move_uploaded_file($fileTmpPath, $destPath)) {
        return [
            'status'   => true,
            'filename' => $newFileName,
            'message'  => 'อัปโหลดรูปภาพสำเร็จ'
        ];
    } else {
        return [
            'status'   => false,
            'filename' => null,
            'message'  => 'ไม่สามารถย้ายไฟล์ไปยังโฟลเดอร์จัดเก็บได้'
        ];
    }
}
?>