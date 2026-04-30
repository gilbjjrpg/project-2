<?php
// Connect to the SQLite database
include '../data/database.php';

// This will hold any login error message
$errorMessage = "";
$successMessage = "";

// Check whether the login form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  // Find out which form type is being submitted: login or signup
  $formType = $_POST["formType"] ?? "";

  //-------------------------------------------------------------
  // LOGIN FORM LOGIC
  //-------------------------------------------------------------
  if ($formType === "login") {

    // Get the entered username/email and password from the form
    $loginIdentifier = trim($_POST["loginIdentifier"] ?? "");
    $loginPassword = trim($_POST["loginPassword"] ?? "");

    // Only continue if both fields were filled in
    if ($loginIdentifier !== "" && $loginPassword !== "") {
      
        // Prepare a query to find a user whose username OR email matches
        $stmt = $db->prepare("
            SELECT id, username, name, email, password, is_guest
            FROM users
            WHERE username = ? OR email = ?
        ");

        // Run the query using the entered login identifier twice
        $stmt->execute([$loginIdentifier, $loginIdentifier]);

        // Fetch the matching user row
        $matchedUser = $stmt->fetch(PDO::FETCH_ASSOC);

        // If a user was found and the password matches, log them in
        $passwordMatches = false;

        if ($matchedUser) {
            $passwordMatches = password_verify($loginPassword, $matchedUser["password"]);

            // Upgrade older plain-text passwords to hashed passwords after login.
            if (!$passwordMatches && hash_equals($matchedUser["password"], $loginPassword)) {
                $passwordMatches = true;
                $newHashedPassword = password_hash($loginPassword, PASSWORD_DEFAULT);
                $updatePasswordStmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updatePasswordStmt->execute([$newHashedPassword, $matchedUser["id"]]);
            }
        }

        if ($matchedUser && $passwordMatches) {

            // Store the username in a cookie so other PHP pages can identify the user
            setcookie("username", $matchedUser["username"], time() + 86400, "/");

            // Redirect to the dashboard page
            header("Location: dashboard.php");
            exit;
        } else {
            // Show an error if login failed
            $errorMessage = "Invalid username/email or password.";
        }
    } else {
        // Show an error if fields were left blank
        $errorMessage = "Please enter both your username/email and password.";
    }
  }

  //-------------------------------------------------------------
  // SIGNUP FORM LOGIC
  //-------------------------------------------------------------
  if ($formType === "signup") {
    
    //Get the signup form values
    $signupName = trim($_POST["signupName"] ?? "");
    $signupUsername = trim($_POST["signupUsername"] ?? "");
    $signupEmail = trim($_POST["signupEmail"] ?? "");
    $signupPassword = trim($_POST["signupPassword"] ?? "");
    $confirmPassword = trim($_POST["confirmPassword"] ?? "");

    //Make sure all fields were filled in
    if (
      $signupName === "" ||
      $signupUsername === "" ||
      $signupEmail === "" ||
      $signupPassword === "" ||
      $confirmPassword === "" 
    ) {
      $errorMessage = "Please fill in all signup fields!";
    }

    //Make sure the passwords match 
    elseif ($signupPassword !== $confirmPassword) {
      $errorMessage = "Passwords do not match!";
    }

    //Make sure the username is not already taken 
    else {
      $usernameCheck = $db->prepare("SELECT id FROM users WHERE username = ?");
      $usernameCheck->execute([$signupUsername]);
      $existingUsername = $usernameCheck->fetch(PDO::FETCH_ASSOC);

      $emailCheck = $db->prepare("SELECT id FROM users WHERE email = ?");
      $emailCheck->execute([$signupEmail]);
      $existingEmail = $emailCheck->fetch(PDO::FETCH_ASSOC);

      if ($existingUsername) {
        $errorMessage = "That usernaeme is already taken!";
      } elseif ($existingEmail) {
        $errorMessage = "The email is already in use!";
      } else {
        //Insert the new user into the users table
        $insertStmt = $db->prepare("
          INSERT INTO users (username, name, email, password, is_guest)
          VALUES (?, ?, ?, ?, 0)
        ");

        $hashedPassword = password_hash($signupPassword, PASSWORD_DEFAULT);

        $insertStmt->execute([
          $signupUsername,
          $signupName,
          $signupEmail,
          $hashedPassword
        ]);


        //Log the new user in immediately after signup
        setcookie("username", $signupUsername, time() + 86400, "/");

        //Redirect to the Dashboard
        header("Location: dashboard.php");
        exit;
      }
    }
  }
}
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
            <p class="subtitle">Login, sign up, or continue as a guest.</p>

            <!-- Shared message area -->
            <div id="errorMessage" class="error-message <?php echo ($errorMessage || $successMessage) ? '' : 'hidden'; ?>">
              <?php
                if ($errorMessage) {
                  echo $errorMessage;
                } elseif ($successMessage) {
                  echo $successMessage;
                }
              ?>
            </div>

            <!-- Login form now submits to PHP -->
            <form id="loginForm" method="POST" action="">
                <input type="hidden" name="formType" value="login">

                <label for="loginIdentifier">Username or Email</label>
                <input type="text" id="loginIdentifier" name="loginIdentifier" placeholder="Enter username or email">

                <label for="loginPassword">Password</label>
                <input type="password" id="loginPassword" name="loginPassword" placeholder="Enter password">

                <button type="submit" class="primary-btn">Login</button>
                <button type="button" id="showSignupBtn" class="secondary-btn">Create Account</button>
                <button type="button" id="guestBtn" class="secondary-btn">Continue as Guest</button>
            </form>

            <!-- SIGNUP FORM -->
            <form id="signupForm" method="POST" action="" class="hidden">
                <input type="hidden" name="formType" value="signup">

                <label for="signupName">Name</label>
                <input type="text" id="signupName" name="signupName" placeholder="Enter a name!" >

                <label for="signupUsername">Username</label>
                <input type="text" id="signupUsername" name="signupUsername" placeholder="Choose a username!">

                <label for="email">Email</label>
                <input type="email" id="signupEmail" name="signupEmail" placeholder="Enter your email.">

                <label for="signupPassword">Password</label>
                <input type="password" id="signupPassword" name="signupPassword" placeholder="Create a password.">

                <label for="confirmPassword">Confrim Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter password.">

                <button type="submit" class="primary-btn">Sign Up</button>
                <button type="button" id="showLoginBtn" class="secondary-btn">Back to Login</button>
            </form>

        </div>
    </div>

  </body>

</html>
