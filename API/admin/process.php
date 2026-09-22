<?php 
include_once './functionCode.php';

if(!($_SESSION['auth']['rule'] == 'admin')){
    $_SESSION['alarm'] = "กลับไปเข้าสู่ระบบก่อน";
    header(base_url('index.php'));
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(checkAction('deleteMember')) { deleteMember(); exit; }
    if(checkAction('editMember'))   { editMember(); exit; }
    if(checkAction('insertUser'))   { insertUser(); exit; }
    if(checkAction('deletememberFromEvent'))   { deletememberFromEvent(); exit; }
    if(checkAction('addMultipleMembersToEvent'))   { addMultipleMembersToEvent(); exit; }
    if(checkAction('addGroupMember')){ addGroupMember(); exit;}
    if(checkAction('deleteGroup')){ deleteGroup(); exit;}
    if(checkAction('addMemberToGroup')){ addMemberToGroup(); exit;}
    if(checkAction('deleteMemberformGroup')){ deleteMemberformGroup(); exit;}
    if(checkAction('deleteEvent')){ deleteEvent(); exit;}
    if(checkAction('editEvent')){ editEvent(); exit;}
    if(checkAction('addEvent')){ addEvent(); exit;}
    if(checkAction('eventSuccess')){ eventSuccess(); exit;}
    if(checkAction('update_slip_status')){ update_slip_status(); exit;}
    if(checkAction('upload_slip_by_admin')){ upload_slip_by_admin(); exit;}

}
if($_SERVER['REQUEST_METHOD' ]== 'GET'){
    // echo intval(checkAction('getMembersByGroup',false));
    if(checkAction('getMembersByGroup',false)){ getMembersByGroup(); exit;}
    if(checkAction('searchMember',false)){ searchMember(); exit;}
}

// echo $_SERVER['REQUEST_METHOD'];