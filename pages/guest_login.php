<?php
//Start a PHP session so guest information can be saved on the server.
session_start();

//Save guest values in the session.
//This lets the rest of the PHP pages know the user is browsing as a guest.
$_SESSION["username"] = "Guest";
$_SESSION["isGuest"] = true;

//Send the guest user to the dashboard after the guest session is created.
header("Location: dashboard.php");
exit;
?>
