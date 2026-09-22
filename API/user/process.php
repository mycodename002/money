<?php 
// include_once './functionCode.php';

if(!($_SESSION['auth']['rule'] == 'admin')){
    $_SESSION['alarm'] = "กลับไปเข้าสู่ระบบก่อน";
    header(base_url('index.php'));
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // if(checkAction('deleteMember')) { deleteMember(); exit; }
    

}
if($_SERVER['REQUEST_METHOD' ]== 'GET'){
    // if(checkAction('searchMember',false)){ searchMember(); exit;}
}
