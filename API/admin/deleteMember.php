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
    $id = $_POST['id'];
    if(empty($id)){
        $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
         backPage();
        exit;
    }
   
    // ลบแบบไม่ลบจริงเลยใช้update
    update($conn,"UPDATE `members` SET `is_deleted` = 1 WHERE id = :id;",['id'=>$id]);
    $_SESSION['notify'] = "ลบข้อมูลสามชิกสำเร็จ";
} catch (Throwable $th) {
    $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
}
$conn = NULL;
backPage();
// 