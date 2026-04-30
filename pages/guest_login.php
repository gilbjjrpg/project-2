<?php
session_start();

$_SESSION["username"] = "Guest";
$_SESSION["isGuest"] = true;

header("Location: dashboard.php");
exit;
?>
