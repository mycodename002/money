<?php 
// require_once "./base.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://localhost:8000/');

function base_url($path = '') {
    return BASE_URL . ltrim($path, '/');
}
function select($conn,$sql)  {
    $query = "$sql";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

function protectSelect($conn,$sql,$param,$one){
    $stmt = $conn->prepare($sql);
    $stmt->execute($param);
    if($one) $data = $stmt->fetchAll();
    else $data = $stmt->fetch();
    return $data;
}  
?>