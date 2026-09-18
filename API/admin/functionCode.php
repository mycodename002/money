<?php
include_once "../../db.php";
require_once "../functions.php";
require_once "../auth.php";

if(!($_SESSION['auth']['rule'] == 'admin')){
    $_SESSION['alarm'] = "กลับไปเข้าสู่ระบบก่อน";
    header(base_url('index.php'));
    exit;
}

function deleteMember(){
    try {
        global $conn;
        $id = $_POST['id'];
        if(empty($id)){
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

function editMember(){
    global $conn;
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
    backPage();
}

function insertUser(){
    global $conn;
    try {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $user = $_POST['user'];
        if(empty($fname) || empty($lname) || empty($user)){
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
        }
        $admin_g = $_SESSION['auth']['admin_group'];
        $pass = password_hash("123",PASSWORD_BCRYPT);
        insert($conn,"INSERT INTO `members`( `fname`, `lname`, `user`, `pass`, `rule`, `admin_group`) VALUES ('$fname','$lname','$user','$pass','user', '$admin_g')");
        $_SESSION['notify'] = "เพิ่มสมาชิกสำเร็จ";
    } catch (Throwable $th) {
        $_SESSION["alarm"] = "มีชื่อผู้ใช้นี้อยู่แล้ว";
        // print_r($th);
    }
    backPage();
}

function deletememberFromEvent() {
    global $conn;
    try {
        if(!isset($_POST['id']) || !isset($_POST['id_event'])){ $_SESSION['alarm'] = 'ข้อมูลไม่ครบถ้วน'; backPage(); exit;}
        $id = $_POST['id'];
        $id_event = $_POST['id_event'];

        delete($conn,"DELETE FROM `mem_event` WHERE id_mem = :id_mem AND id_event = :id_event;",["id_mem"=>$id, "id_event"=>$id_event]);
        $_SESSION['notify'] = "ลบสมาชิกออกจากรายการสำเร็จ";
    } catch (Throwable $th) {
        $_SESSION['alarm'] = 'เกิดข้อผิดพลาดในการลบสมาชิก';
    }
    backPage();
}

function addMultipleMembersToEvent(){
    global $conn;
    try {
        if(!isset($_POST['member_ids']) || !isset($_POST['id_event'])){
            $_SESSION['alarm'] = "ข้อมูลไม่ครบถ้วน";
            backPage();
            exit;
        }
        $mem_ids = $_POST['member_ids'];
        $event = $_POST['id_event'];
        foreach($mem_ids as $mem_id){
            insert($conn,"INSERT INTO `mem_event`( `id_mem`, `id_event`) VALUES ($mem_id,$event)");
        }
        $_SESSION['notify'] = "เพิ่มสมาชิกสำเร็จ";
    } catch (Throwable $th) {
        $_SESSION['alarm'] = "เกิดข้อผิดพลาดในการเพิ่มสมาชิก";
    }
    backPage();
}

function getMembersByGroup (){
    global $conn;
    header('Content-Type: application/json');

    $group_id = isset($_GET['group_id']) ? $_GET['group_id'] : 0;
    $id_event = isset($_GET['id_event']) ? $_GET['id_event'] : 0;
    if ($group_id > 0) {
        
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
        
        echo json_encode($members);
    } else {
        echo json_encode([]);
    }
}