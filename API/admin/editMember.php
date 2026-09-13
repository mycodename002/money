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
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $user = $_POST['user'];
    $id = $_POST['id'];
    if(empty($fname) || empty($lname) || empty($user) || empty($id)){
        $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
    }
    update($conn,"UPDATE `members` SET `fname`='$fname',`lname`='$lname',`user`='$user' WHERE id = :id",["id"=>$id]);
    $_SESSION['notify'] = "แก้ไขข้อมูลสำเร็จ";
} catch (Throwable $th) {
    $_SESSION["alarm"] = "แก้ข้อผิดพลาด";
}
$conn = NULL;
backPage();
// 