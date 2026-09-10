<?php 

function select($conn,$sql)  {
    $query = "$sql";
    return $conn->query($query)->execute();
}

function protectSelect($conn,$sql,$param,$one){
    $stmt = $conn->prepare($sql);
    $stmt->execute($param);
    if($one) $data = $stmt->fetchAll();
    else $data = $stmt->fetch();
    return $data;
}  
?>