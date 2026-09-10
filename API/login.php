<?php include_once "../db.php";
include_once "./functions.php";
$user = $_POST['user'];
$pass = $_POST['pass'];


// $_SESSION[''];
// $data = select($conn, ";");
// $data = $data->fetch();
// print_r($data);
$sql = "SELECT rule,id, fname, lname FROM `members` WHERE user = :user AND pass = :pass and is_deleted = 0";
$data = protectSelect($conn, $sql,['user'=>$user, 'pass'=> $pass] ,!true);
$_SESSION['auth']['id'] = $data['id'];
$_SESSION['auth']['fname'] = $data['fname'];

$_SESSION['auth']['lname'] = $data['lname'];
$_SESSION['auth']['rule'] = $data['rule'];
echo "<pre>";
print_r($data);
print_r($_SESSION['auth']);