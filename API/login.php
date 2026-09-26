<?php include_once "../db.php";
include_once "./functions.php";


$user = $_POST['user'];
$pass = $_POST['pass'];

if(empty($user) || empty($pass)){
    header('location:'.base_url('index.php'));
}
$sql = "SELECT rule,id, fname, lname, pass, admin_group FROM `members` WHERE user = :user and is_deleted = 0";
$data = protectSelect($conn, $sql,['user'=>$user] ,!true);
if(empty($data)){
    $_SESSION["alarm"] = "ชื่อผู้ใช้งานไม่ถูกต้อง";
    header("Location:".base_url('index.php'));
    exit();
}
if (!password_verify($pass, $data['pass'])) {
    $_SESSION["alarm"] = "รหัสผ่านไม่ถูกต้อง";
    header("Location:".base_url('index.php'));
    exit();
} 
$_SESSION['auth']['id'] = $data['id'];
$_SESSION['auth']['fname'] = $data['fname'];

$_SESSION['auth']['lname'] = $data['lname'];
$_SESSION['auth']['rule'] = $data['rule'];


if(isset($_SESSION['addByURL'])){
    $check = protectSelect($conn,"SELECT id FROM `mem_event` WHERE `id_mem` = :mem_id AND `id_event` = :event_",['mem_id'=>$_SESSION['auth']['id'],'event_'=>$_SESSION['addByURL']],0);
    if(empty($check)){
        queryExecute($conn,"INSERT INTO `mem_event`( `id_mem`, `id_event`) VALUES (:mem_id,:event_)",['mem_id'=>$_SESSION['auth']['id'],'event_'=>$_SESSION['addByURL']]);
    }
    unset($_SESSION['addByURL']);
}
if(isset($_SESSION['addByGURL'])){
    $check = protectSelect($conn,"SELECT id FROM `base_group_member` WHERE `id_mem` = :mem_id AND `id_name_group` = :id_name_group",['mem_id'=>$_SESSION['auth']['id'],'id_name_group'=>$_SESSION['addByGURL']],0);
    if(empty($check)){
        queryExecute($conn,"INSERT INTO `base_group_member`( `id_mem`, `id_name_group`) VALUES (:mem_id,:id_name_group)",['mem_id'=>$_SESSION['auth']['id'],'id_name_group'=>$_SESSION['addByGURL']]);
    }
    unset($_SESSION['addByGURL']);
}


if($data['rule'] == 'admin'){
    $_SESSION['auth']['admin_group'] = $data['admin_group'];
    header("Location:".base_url('/VIEW/ADMIN'));
    exit();
}

if($data['rule'] == 'user'){
    header("Location:".base_url('/VIEW/USER'));
    exit();
}

if($data['rule'] == 'support'){
    header("Location:".base_url('/VIEW/SUPPORT'));
    exit();
}

