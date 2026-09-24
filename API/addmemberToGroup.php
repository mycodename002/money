<?php 
require_once './functions.php';
$id = $_GET['id'];
if(empty($id)){
    header('location:'.base_url('index.php'));
    exit;
}
$_SESSION['addByGURL'] = $id;
header('location:'.base_url('/API/login.php'));
 
?>