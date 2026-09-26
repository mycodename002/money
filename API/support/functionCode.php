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
            // print_r($_POST['id']);
            // echo $id;
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