<?php
include_once "../../db.php";
require_once "../functions.php";
require_once "../auth.php";


function upload_slip(){
    global $conn;
    $id_event =$_POST['id_event'] ;
    $add_by   =$_POST['add_by'];  
    // $status   =$_POST['status'];

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
                'status_'   => "รอตรวจสอบ",
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
                'status_'   => "รอตรวจสอบ"
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

function changePass(){
    global $conn;
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    if(empty($old_password) || empty($new_password) || empty($confirm_password)){
        $_SESSION['alarm'] = "ข้อมูลไม่ครบถ้วน";
        backPage();
        exit;
    }

    $datacheckpass= protectSelect($conn,"SELECT pass FROM `members` WHERE id= :id;",['id'=>$_SESSION['auth']['id']],0);
    if(!password_verify($old_password, $datacheckpass['pass'])){
        $_SESSION['alarm'] = "รหัสเดิมไม่ถูกต้อง";
        backPage();
        exit;
    }

    if($new_password != $confirm_password){
        $_SESSION['alarm'] = "รหัสผ่านใหม่ไม่ไม่ตรงกัน";
        backPage();
        exit;
    }
    $new_password = password_hash($new_password,PASSWORD_BCRYPT);
    queryExecute($conn,"UPDATE `members` SET `pass` = :newpass WHERE id = :id",['id'=>$_SESSION['auth']['id'], 'newpass'=>$new_password]);
    $_SESSION['notify'] = "เปลี่ยนรหัสผ่านสำเร็จ";
    backPage();
    backPage();

}