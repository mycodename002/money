<?php 
include_once './functionCode.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(checkAction('deleteMember',true)) { deleteMember(); exit; }
    if(checkAction('editMember',true))   { editMember(); exit; }
    if(checkAction('insertUser',true))   { insertUser(); exit; }
    if(checkAction('deletememberFromEvent',true))   { deletememberFromEvent(); exit; }
    if(checkAction('addMultipleMembersToEvent',true))   { addMultipleMembersToEvent(); exit; }
    
}
if($_SERVER['REQUEST_METHOD' ]== 'GET'){
    // echo intval(checkAction('getMembersByGroup',false));
    if(checkAction('getMembersByGroup',false)){ getMembersByGroup(); exit;}
    // echo 77;
}

// echo $_SERVER['REQUEST_METHOD'];