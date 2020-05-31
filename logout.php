<?php
  session_start();
  session_destroy();
  $_SESSION['identifiant']= [];
  header("location: index.php");
  exit();
?>
