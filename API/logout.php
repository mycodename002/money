<?php 
require_once './functions.php';
session_destroy();
header('location:'.base_url('index.php'));