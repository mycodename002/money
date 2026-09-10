<?php include_once "../db.php";
include_once "./functions.php";
$user = $_POST['user'];
$pass = $_POST['pass'];


// $_SESSION[''];
// $data = select($conn, ";");
// $data = $data->fetch();
// print_r($data);
$sql = "SELECT rule,id, fname, lname, pass FROM `members` WHERE user = :user and is_deleted = 0";
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
if($data['rule'] == 'admin'){
    header("Location:".base_url('/VIEW/ADMIN'));
    exit();
}

// echo "<pre>";
// print_r($data);
// print_r($_SESSION['auth']);