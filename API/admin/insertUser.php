<?php 
include_once "../../db.php";
require_once "../functions.php";
try {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $user = $_POST['user'];
    if(empty($fname) || empty($lname) || empty($user)){
        $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
    }
    $pass = password_hash("123",PASSWORD_BCRYPT);
    insert($conn,"INSERT INTO `members`( `fname`, `lname`, `user`, `pass`, `rule`) VALUES ('$fname','$lname','$user','$pass','user')");
    $_SESSION['notify'] = "เพิ่มสมาชิกสำเร็จ";
} catch (Throwable $th) {
    // //throw $th;
    // echo "fail0";
    $_SESSION["alarm"] = "มีชื่อผู้ใช้นี้อยู่แล้ว";
}
backPage();
// 