<?php
include 'Cors.php';
session_start();
setcookie('jwt', '', time() - 3600, '/'); 
session_destroy(); 
header('Location: /login.php'); 
exit();
?>
