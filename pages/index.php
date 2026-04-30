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
        if ($matchedUser && $matchedUser["password"] === $loginPassword) {

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
            <p class="subtitle">Login, sign up later, or continue as a guest.</p>

            <!-- Login form now submits to PHP -->
            <form id="loginForm" method="POST" action="">
                <label for="loginIdentifier">Username or Email</label>
                <input type="text" id="loginIdentifier" name="loginIdentifier" placeholder="Enter username or email">

                <label for="loginPassword">Password</label>
                <input type="password" id="loginPassword" name="loginPassword" placeholder="Enter password">

                <!-- Show PHP login errors here -->
                <div id="errorMessage" class="error-message <?php echo $errorMessage ? '' : 'hidden'; ?>">
                    <?php echo $errorMessage; ?>
                </div>

                <button type="submit" class="primary-btn">Login</button>
                <button type="button" id="guestBtn" class="secondary-btn">Continue as Guest</button>
            </form>
        </div>
    </div>

  </body>

</html>