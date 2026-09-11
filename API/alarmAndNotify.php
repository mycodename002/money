<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['alarm'])){
  if(!empty($_SESSION['alarm'])){ ?>

<script> Swal.fire({
  icon: "error",
  title: "<?php echo $_SESSION['alarm'] ?>",
  showConfirmButton: false,
  timer: 1500
  });
</script>

<?php }


unset($_SESSION['alarm']);
} 

if(isset($_SESSION['notify'])){
  if(!empty($_SESSION['notify'])){ ?>

<script> Swal.fire({
  icon: "success",
  title: "<?php echo $_SESSION['notify'] ?>",
  showConfirmButton: false,
  timer: 1500
  });
</script>

<?php }


unset($_SESSION['notify']);
}
?>

