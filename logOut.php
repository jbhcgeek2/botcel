<?php

session_start();

if(!empty($_SESSION['usuarioPOS'])){
  //procedemos a limpoiar la sesion
  $_SESSION = array();

  session_destroy();
  header("Location: login.php");
  exit();
}
?>