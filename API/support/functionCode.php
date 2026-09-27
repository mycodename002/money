<?php
include_once "../../db.php";
require_once "../functions.php";
require_once "../auth.php";

if(!($_SESSION['auth']['rule'] == 'support')){
    $_SESSION['alarm'] = "กลับไปเข้าสู่ระบบก่อน";
    header(base_url('index.php'));
    exit;


}
function deleteMember(){
    try {
        global $conn;
        $id = $_POST['id'];
        if(empty($id)){
            // print_r($_POST['id']);
            // echo $id;
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        update($conn,"UPDATE `members` SET `is_deleted` = 1 WHERE id = :id;",['id'=>$id]);
        $_SESSION['notify'] = "ลบข้อมูลสามชิกสำเร็จ";
    } catch (Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
    }
    backPage();
}
function RestoreMember(){
    try {
        global $conn;
        $id = $_POST['id'];
        if(empty($id)){
            // print_r($_POST['id']);
            // echo $id;
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        update($conn,"UPDATE `members` SET `is_deleted` = 0 WHERE id = :id;",['id'=>$id]);
        $_SESSION['notify'] = "คืนข้อมูลสามชิกสำเร็จ";
    } catch (Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
    }
    backPage();
}

function RestoreEvent(){
    try {
        global $conn;
        $id = $_POST['id'];
        if(empty($id)){
            // print_r($_POST['id']);
            // echo $id;
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        update($conn,"UPDATE `events` SET `is_deleted` = 0 WHERE id = :id;",['id'=>$id]);
        $_SESSION['notify'] = "คืนข้อมูลกิจกรรมสำเร็จ";
    } catch (Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
    }
    backPage();
}

function deleteEvent(){
    try {
        global $conn;
        $id = $_POST['id'];
        if(empty($id)){
            // print_r($_POST['id']);
            // echo $id;
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        update($conn,"UPDATE `events` SET `is_deleted` = 1 WHERE id = :id;",['id'=>$id]);
        $_SESSION['notify'] = "ลบข้อมูลกิจกรรมสำเร็จ";
    } catch (Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
    }
    backPage();
}

function restoreGroup(){
    try {
        global $conn;
        $id = $_POST['id'];
        if(empty($id)){
            // print_r($_POST['id']);
            // echo $id;
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        update($conn,"UPDATE `group_mem` SET `is_deleted` = 0 WHERE id = :id;",['id'=>$id]);
        $_SESSION['notify'] = "คืนข้อมูลกลุ่มสำเร็จ";
    } catch (Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
    }
    backPage();
}

function deleteGroup(){
    try {
        global $conn;
        $id = $_POST['id'];
        if(empty($id)){
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        update($conn,"UPDATE `group_mem` SET `is_deleted` = 1 WHERE id = :id;",['id'=>$id]);
        $_SESSION['notify'] = "ลบข้อมูลกลุ่มสำเร็จ";
    } catch (Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
    }
    backPage();
}

function createAdmin(){
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $user = $_POST['user'];

    if(!isset($fname) || !isset($lname) || !isset ($user)){
        $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
        backPage();
        exit;
    }

    try {
        global $conn;
        $conn->beginTransaction();

        $admin_g = select($conn,"SELECT MAX(id_group) AS mx FROM `group_admins`;");
        queryExecute($conn,"INSERT INTO `members`( `fname`, `lname`, `user`, `pass`, `rule`, `admin_group`, `is_deleted`) VALUES (:fname, :lname, :user, :pass,:rule , :admin_group, 0)",
        ["fname"=> $fname,
        "lname"=> $lname,
        'user'=>$user,
        'rule'=>"admin",
        "pass" => password_hash("123",PASSWORD_BCRYPT),
        "admin_group"=>$admin_g[0]['mx']+1]);
        $id = protectSelect($conn,"SELECT id FROM `members` WHERE `user`=:user ;",['user'=>$user],0);
        // echo $admin_g['mx'];
        queryExecute($conn,"INSERT INTO `group_admins`(`id_admin`, `id_group`) VALUES (:id,:admin_group);",["id"=>$id['id'], "admin_group"=>$admin_g[0]['mx']+1]);
        $conn->commit();

        $_SESSION['notify'] = "เพิ่มผู้ดูแลสำเร็จ";
        
        
    } catch (\Throwable $th) {
        // throw $th;
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
    }
    backPage();
}