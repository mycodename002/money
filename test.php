<?php 
require_once "db.php";
require_once "./API/functions.php";

$_SESSION['auth']['id'] = $_POST['id'];
$_SESSION['auth']['rule'] = $_POST['rule'];
$_SESSION['auth']['admin_group'] = $_POST['admin'];

backPage();
