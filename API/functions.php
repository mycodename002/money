<?php 
// require_once "./base.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// รันบนพอร์ตไหนก็มาแก้ด้วยเด้อ
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

function protectSelect($conn,$sql,$param,$multi){
    $stmt = $conn->prepare($sql);
    $stmt->execute($param);
    if($multi) $data = $stmt->fetchAll();
    else $data = $stmt->fetch();
    return $data;
}  

function delete($conn, $sql, $param){
    $tempSQL = $conn->prepare($sql);
    $tempSQL->execute($param);
    // $conn->exec($sql);
}

function update($conn, $sql, $param){
    $tempSQL = $conn->prepare($sql);
    $tempSQL->execute($param);
    // $conn->exec($sql);
}
function insert($conn, $sql) {
    $conn->exec($sql);
}

function backPage() {
    echo '<script type="text/javascript">
    if (document.referrer) {
        window.location.href = document.referrer;
    } else {
        window.history.back();
    }
    </script>';
}
?>