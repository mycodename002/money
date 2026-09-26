<?php
include_once "../../db.php";
require_once "../functions.php";
require_once "../auth.php";


function deleteMember(){
    try {
        global $conn;
        $id = $_POST['id'];
        echo $id;
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
        
        $sql = "SELECT DISTINCT m.id, m.fname, m.lname, m.user 
        FROM `members` AS m 
        JOIN `base_group_member` AS b ON m.id = b.id_mem 
        WHERE  m.is_deleted = 0 AND b.id_name_group = :group_id
        AND m.id NOT IN(
        	SELECT id_mem FROM `mem_event` WHERE id_event = :id_event
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

function addGroupMember(){
    global $conn;
    try {
        $name_group = $_POST['name_group'];
        $details = $_POST['details'];
        if(empty($name_group)){
            $_SESSION['alarm'] = "กรุณากรอกชื่อกลุ่ม";
            backPage();
            exit;
        }
        $admin_g = $_SESSION["auth"]["admin_group"];
        insert($conn,"INSERT INTO `group_mem`(`title`, `details`, `id_group_admin`, `is_deleted`) VALUES ('$name_group','$details',$admin_g,0)");
        $_SESSION['notify'] = "เพิ่มกลุ่มสำเร็จ";
    } catch (Throwable $th) {
        $_SESSION['alarm'] = "เกิดข้อผิดพลาด";
        // print_r($th);
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
        update($conn,"UPDATE `group_mem` SET `is_deleted`= 1 WHERE id=:id",['id'=>$id]);
        $_SESSION['notify'] = "ลบข้อมูลกลุ่ม";
    } catch (Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
    }
    backPage();
}


function searchMember(){
    global $conn;
    header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['auth']['admin_group'])) {
    echo json_encode([]);
    exit();
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$group_id = isset($_GET['group_id']) ?$_GET['group_id'] : '';

if (empty($q) || empty($group_id)) {
    echo json_encode([]);
    exit();
}


$sql = "SELECT id, fname, lname, user 
        FROM `members` 
        WHERE is_deleted = 0 
          AND admin_group = :ad_id 
          AND (fname LIKE :q OR lname LIKE :q OR user LIKE :q)
          AND id NOT IN (
              SELECT id_mem 
              FROM `base_group_member` 
              WHERE id_name_group = :id_name_group
          )
        LIMIT 15;";

$params = [
    "ad_id" => $_SESSION['auth']['admin_group'],
    "id_name_group" => $group_id,
    "q" => "%" . $q . "%"
];

$result = protectSelect($conn, $sql,$params, 1);

echo json_encode($result ?: []);
}


function addMemberToGroup(){
    global $conn;
    
    try {
        if(!isset($_POST['member_ids']) || !isset($_POST['group_id'])){
            header("Location: ".base_url('/VIEW/ADMIN'));
            $_SESSION['alarm'] = "กรุณากรอกข้อมูลให้ถูกต้อง";
            exit;
        }
        $id_group = $_POST['group_id'];
        $mem_ids = json_decode($_POST['member_ids'], true) ;
        $conn->beginTransaction();
        foreach($mem_ids as $mem_id){
            queryExecute($conn,'INSERT INTO `base_group_member` (id_mem, id_name_group) VALUES (:id_mem, :id_group)',['id_mem'=>$mem_id,'id_group'=>$id_group]);
        }
        $conn->commit();
        $_SESSION['notify'] = "เพิ่มสมาชิกสำเร็จ";

    } catch (\Throwable $th) {
        $conn->rollBack(); //ถ้ามันเกิดข้อผิพลาดอะไรสักอย่าง ข้อมูลที่บันทึกไปจะถูกยกเลิกทั้งหมด 
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
    }
    backPage();
}

function deleteMemberformGroup(){
    try {
        global $conn;
        $id_mem = $_POST['id'];
        $id_group = $_POST['id_group'];
        if(empty($id_mem)){
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        queryExecute($conn,"DELETE FROM `base_group_member` WHERE id_mem = :id_mem AND id_name_group = :id_group;",['id_mem'=>$id_mem,'id_group'=>$id_group]);
        $_SESSION['notify'] = "ลบข้อมูลสามชิกสำเร็จ";
    } catch (Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
    }
    backPage();
}

function deleteEvent(){
    try {
        global $conn;
        $id_event = $_POST['id'];
        // echo $id_event;
        if(empty($id_event)){
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        queryExecute($conn,"UPDATE `events` SET `is_deleted` = 1 WHERE id_group_admin = :id_group AND id = :id_event;",['id_group'=>$_SESSION['auth']['admin_group'],'id_event'=>$id_event]);
        $_SESSION['notify'] = "ลบข้อมูลรายการสำเร็จ";

    } catch (\Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";

    }
    backPage();

}

function editEvent(){
    try {
        global $conn;
        $title = $_POST['title'];
        $details = $_POST['details'];
        $id_event = $_POST['id'];
        if(empty($title) || empty($id_event)){
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        queryExecute($conn,"UPDATE `events` SET `title` = :title, `details` = :details WHERE id_group_admin = :id_group AND id = :id_event;",['title'=>$title,'details'=>$details,'id_group'=>$_SESSION['auth']['admin_group'],'id_event'=>$id_event]);
        $_SESSION['notify'] = "แก้ไขรายการสำเร็จ";

    } catch (\Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
        
    }
    backPage();
}

function addEvent(){
    // echo $_POST['title'];
    try {
        global $conn;
        $title = $_POST['title'];
        $details = $_POST['details'];
        if(empty($title) ){
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        queryExecute($conn,"INSERT INTO `events` (`title`, `details`, `id_group_admin`, `is_deleted`, `is_success`) VALUES (:title, :details, :id_admin_group, 0,0 );",[
            'title'=> $title,
            'details' => $details,
            'id_admin_group' => $_SESSION['auth']['admin_group']
        ]); 
        $_SESSION['notify'] = "เพิ่มรายการสำเร็จ";

    } catch (Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
        
    }
    backPage();
}

function eventSuccess(){
    try {
        global $conn;
        $event_id = $_POST['event_id'];
        if(empty($event_id) ){
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        $checkData = protectSelect($conn, "
SELECT 
    COUNT(DISTINCT m.id) AS total_members,
    COUNT(DISTINCT CASE WHEN s.status = 'ผ่าน' THEN m.id END) AS passed_members,
    ROUND(
        (COUNT(DISTINCT CASE WHEN s.status = 'ผ่าน' THEN m.id END) * 100.0) 
        / NULLIF(COUNT(DISTINCT m.id), 0), 
        2
    ) AS pass_percentage
FROM `mem_event` AS e 
JOIN `members` AS m ON e.id_mem = m.id 
LEFT JOIN `slips` AS s ON m.id = s.add_by AND s.id_event = e.id_event
WHERE e.id_event = :event_id AND m.is_deleted = 0;",['event_id'=> $event_id],0);
        if($checkData['pass_percentage'] != 100){
            $_SESSION["alarm"] = "ยังมีบางคนยังไม่แนบสลิป";
            backPage();
            exit;
        }
        queryExecute($conn,"UPDATE `events` SET  `is_success` = 1 WHERE id_group_admin = :id_group AND id = :event_id;",[
            'event_id'=> $event_id,
            'id_group' => $_SESSION['auth']['admin_group']
        ]); 
        $_SESSION['notify'] = "จัดการให้รายการนี้เสร็จสิ้นเรียบร้อย";

    } catch (Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
        
    }
    header('location:'.base_url('/VIEW/ADMIN'));
}


function update_slip_status() {
    try {
        global $conn;
        $slip_id = $_POST['slip_id'];
        $status = $_POST['status'];

        if(empty($slip_id) ){
            $_SESSION["alarm"] = "กรุณากรอกข้อมูลใหม่ให้ครบถ้วน";
            backPage();
            exit;
        }
        queryExecute($conn,"UPDATE `slips` SET  `status` = :status_s WHERE id = :id;",[
            'id'=> $slip_id,
            'status_s' => $status
        ]); 
        $_SESSION['notify'] = "เปลี่ยนสถานะเรียบร้อยแล้ว";

    } catch (Throwable $th) {
        $_SESSION["alarm"] = "เกิดข้อผิดพลาดผิดพลาด";
        
    }
    backPage();
}

function upload_slip_by_admin(){
    global $conn;
    $id_event =$_POST['id_event'] ;
    $add_by   =$_POST['add_by'];  
    $status   =$_POST['status'];

    if (empty($id_event) || empty($add_by)) {$_SESSION['alarm'] = "ข้อมูลไม่ถูกต้อง";
        backPage();
        exit;
    }

    $uploadResult = uploadSlipImage($_FILES['slip_file'] ?? null, $id_event,$add_by);

    if ($uploadResult['status'] === false) {
        $_SESSION['alarm'] =$uploadResult['message'];
        backPage();
        exit;
    }

    $newFileName =$uploadResult['filename'];

    try {
        $checkSql = "SELECT `id`, `file_name` FROM `slips` WHERE `id_event` = :id_event AND `add_by` = :add_by LIMIT 1";
        // $existingSlip = queryExecute($conn,$checkSql, ['id_event' => $id_event, 'add_by' =>$add_by])->fetch();
        $existingSlip = protectSelect($conn,$checkSql, ['id_event' => $id_event, 'add_by' =>$add_by],0);
        $uploadDir = '/../../STORAGES/IMG/';

        if (!empty($existingSlip)) {
            if (!empty($existingSlip['file_name'])) {$oldFilePath = $uploadDir .$existingSlip['file_name'];
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }
            $updateSql = "UPDATE `slips` 
                          SET `file_name` = :file_name, 
                              `status`    = :status_ 
                          WHERE `id`      = :id";
            
            $params = [
                'file_name' => $newFileName,
                'status_'   => $status,
                'id'        => $existingSlip['id']
            ];

            queryExecute($conn,$updateSql, $params);$_SESSION['notify'] = "อัปเดตสลิปและสถานะสำเร็จ";

        } else {
            $insertSql = "INSERT INTO `slips` (
                            `file_name`, 
                            `id_event`, 
                            `add_by`, 
                            `type`, 
                            `status`
                        ) VALUES (
                            :file_name, 
                            :id_event, 
                            :add_by, 
                            'รายรับ', 
                            :status_
                        )";

            $params = [
                'file_name' => $newFileName,
                'id_event'  => $id_event,
                'add_by'    => $add_by,
                'status_'   => $status
            ];

            queryExecute($conn,$insertSql, $params);$_SESSION['notify'] = "อัปโหลดและแนบสลิปแทนสมาชิกสำเร็จ";
        }

    } catch (\Throwable $th) {
        // หากระบบฐานข้อมูลขัดข้อง ให้ลบไฟล์ใหม่ที่เพิ่งอัปโหลดไปทันที
        $newFilePath = __DIR__ . '/../../STORAGES/IMG/' .$newFileName;
        if (file_exists($newFilePath)) {
            @unlink($newFilePath);
        }
        $_SESSION['alarm'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูลลงฐานข้อมูล";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}