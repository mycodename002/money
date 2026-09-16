<?php 

require_once '../db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="./test.php" method="post">
        <input type="text" name="id" value="<?php echo $_SESSION['auth']['id'] ?>" id="">
        <input type="text" name="rule" value="<?php echo $_SESSION['auth']['rule'] ?>" id="">
        <input type="text" name="admin" value="<?php echo $_SESSION['auth']['admin_group'] ?>" id="">
        <input type="submit" value="">
    </form>
    <?php echo "<pre>";
        print_r($_SESSION);
    ?>
    <script>
        setTimeout(()=>{
            window.location.reload()
        },5000);
    </script>
</body>
</html>