<?php
session_start();
$_SESSION['safecode']= $_POST['randomcode'];
echo $_SESSION['safecode'];
?>