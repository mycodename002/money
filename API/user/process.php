<?php 
include_once './functionCode.php';

if(!($_SESSION['auth']['rule'] == 'user')){
    $_SESSION['alarm'] = "กลับไปเข้าสู่ระบบก่อน";
    header(base_url('index.php'));
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // if(checkAction('deleteMember')) { deleteMember(); exit; }
    if(checkAction('upload_slip')) { upload_slip(); exit; }
    if(checkAction('changePass')) { changePass(); exit; }
    

}
if($_SERVER['REQUEST_METHOD' ]== 'GET'){
    // if(checkAction('searchMember',false)){ searchMember(); exit;}
}
