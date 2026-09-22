<?php 
require_once "../../API/functions.php";
require_once '../TEMPLATES/user/head_user.php';
require_once '../TEMPLATES/user/nav_user.php';
require_once "../../API/alarmAndNotify.php";
include_once "../../db.php";

if(!($_SESSION['auth']['rule'] == 'support')){
    header('location:' . base_url('index.php'));
    exit;
}