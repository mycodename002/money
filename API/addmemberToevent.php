<?php 
require_once './functions.php';
$id = $_GET['id'];
if(empty($id)){
    header('location:'.base_url('index.php'));
    exit;
}
$_SESSION['addByURL'] = $id;
require_once './auth.php';
?>