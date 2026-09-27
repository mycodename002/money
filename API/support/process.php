<?php 
include_once './functionCode.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(checkAction('deleteMember')) { deleteMember(); exit; }
    if(checkAction('deleteGroup')){ deleteGroup(); exit;}
    if(checkAction('deleteEvent')){ deleteEvent(); exit;}
    if(checkAction('RestoreMember')){ RestoreMember(); exit;}
    if(checkAction('restoreEvent')){ restoreEvent(); exit;}
    if(checkAction('restoreGroup')){ restoreGroup(); exit;}
    if(checkAction('createAdmin')){ createAdmin(); exit;}
    

}
if($_SERVER['REQUEST_METHOD' ]== 'GET'){
    
}

// echo $_SERVER['REQUEST_METHOD'];