<?php
session_start();

$usersFile = "../data/users.json";
$usersJson = file_get_contents($usersFile);
$users = json_decode($usersJson, true);

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $loginIdentifier = $_POST["loginIdentifier"] ?? "";
    $loginPassword = $_POST["loginPassword"] ?? "";

    $matchedUser = null;

    foreach ($users as $user) {
        if (
            ($user["username"] === $loginIdentifier || $user["email"] === $loginIdentifier) &&
            $user["password"] === $loginPassword
        ) {
            $matchedUser = $user;
            break;
        }
    }

    if ($matchedUser) {
        $_SESSION["username"] = $matchedUser["username"];
        header("Location: dashboard.php");
        exit;
    } else {
        $errorMessage = "Invalid username/email or password.";
    }
}

// ----> THIS IS THE LOGIN/REGISTER PAGE. DO NOT CHANGE. <----

/*
      Test accounts you could use:
       Username: josh722 | Password: test123
       Username: amy1287 | Password: hello456
       Username: mike1202 | Password: quiz789
*/
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quizberry! Login</title>
  <link rel="stylesheet" href="../style/style.css">
  <script src="../scripts/login.js" defer></script>

</head>

<body>
  <div class="login-page-wrapper">
    <div class="login-card">
      <h1>Quizberry!</h1>
      <p class="subtitle">Login, sign up later, or continue as a guest.</p>

      <form id="loginForm" method="POST" action="">
        <label for="loginIdentifier">Username or Email</label>
        <input type="text" id="loginIdentifier" name="loginIdentifier" placeholder="Enter username or email">

        <label for="loginPassword">Password</label>
        <input type="password" id="loginPassword" name="loginPassword" placeholder="Enter password">

        <div id="errorMessage" class="error-message hidden"></div>

        <button type="submit" class="primary-btn">Login</button>
        <button type="button" id="guestBtn" class="secondary-btn">Continue as Guest</button>
      </form>
      
    </div>
  </div>
</body>
</html>
