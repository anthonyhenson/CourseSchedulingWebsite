<?php
//session_unset();
//$_SESSION = [];
//unset($_SESSION['user']);
//$_SESSION['user'] = "";
session_destroy();
header('Location: login.php');
?>