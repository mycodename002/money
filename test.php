<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <style>
    body{
        background-color: green;
    }
</style>
<?php 

for($i=1;$i<=100;$i++){
    // echo md5($i);
    echo $i.'<br>';
    sleep(1);
}?>
</body>
</html>
