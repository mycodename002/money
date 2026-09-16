<?php 
include_once "../../db.php";
require_once "../functions.php";
require_once "../auth.php";

if(!($_SESSION['auth']['rule'] == 'admin')){
    $_SESSION['alarm'] = "กลับไปเข้าสู่ระบบก่อน";
    header(base_url('index.php'));
    exit;
}

// print_r($_POST['member_ids']);
// $_POST['id_event'];

if(!isset($_POST['member_ids']) || !isset($_POST['id_event'])){
    $_SESSION['alarm'] = "ข้อมูลไม่ครบถ้วน";
    backPage();
    exit;
}
$mem_ids = $_POST['member_ids'];
$event = $_POST['id_event'];
try {
    foreach($mem_ids as $mem_id){
        insert($conn,"INSERT INTO `mem_event`( `id_mem`, `id_event`) VALUES ($mem_id,$event)");
    }
    $_SESSION['notify'] = "เพิ่มสมาชิกสำเร็จ";
} catch (Throwable $th) {
    $_SESSION['alarm'] = "เกิดข้อผิดพลาดในการเพิ่มสมาชิก";
}
backPage();