<?php 
include_once "../../db.php";
require_once "../functions.php";
require_once "../auth.php";

if(!($_SESSION['auth']['rule'] == 'admin')){
    $_SESSION['alarm'] = "กลับไปเข้าสู่ระบบก่อน";
    header(base_url('index.php'));
    exit;
}

try {
    if(!isset($_POST['id']) || !isset($_POST['id_event'])){ $_SESSION['alarm'] = 'ข้อมูลไม่ครบถ้วน'; backPage(); exit;}
    $id = $_POST['id'];
    $id_event = $_POST['id_event'];

    delete($conn,"DELETE FROM `mem_event` WHERE id_mem = :id_mem AND id_event = :id_event;",["id_mem"=>$id, "id_event"=>$id_event]);
    $_SESSION['notify'] = "ลบสมาชิกออกจากรายการสำเร็จ";
} catch (Throwable $th) {
    $_SESSION['alarm'] = 'เกิดข้อผิดพลาดในการลบสมาชิก';
}
$conn = NULL;
backPage();