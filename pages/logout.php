<?php
session_start();

//Remove all saved session values for the current user.
$_SESSION = [];

//Remove the PHP session cookie from the browser if it exists.
if (ini_get("session.use_cookies")) {
    $cookieParams = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $cookieParams["path"],
        $cookieParams["domain"],
        $cookieParams["secure"],
        $cookieParams["httponly"]
    );
}

//Destroy the server-side session data.
session_destroy();

//Send the user back to the login page.
header("Location: index.php");
exit;
?>
