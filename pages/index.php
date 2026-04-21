<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quizberry! Login</title>
  <link rel="stylesheet" href="../style/style.css">
  <script src="../scripts/login.js" defer></script>

</head>

<body>
  <div class="page-wrapper">
    <div class="login-card">
      <h1>Quizberry!</h1>
      <p class="subtitle">Login, sign up later, or continue as a guest.</p>

      <form id="loginForm">
        <label for="loginIdentifier">Username or Email</label>
        <input type="text" id="loginIdentifier" name="loginIdentifier" placeholder="Enter username or email">

        <label for="loginPassword">Password</label>
        <input type="password" id="loginPassword" name="loginPassword" placeholder="Enter password">

        <div id="errorMessage" class="error-message hidden"></div>

        <button type="submit" class="primary-btn">Login</button>
        <button type="button" id="guestBtn" class="secondary-btn">Continue as Guest</button>
      </form>

      <div class="demo-accounts">
        <h2>Dummy Test Accounts</h2>
        <p><strong>Username:</strong> josh722 | <strong>Password:</strong> test123</p>
        <p><strong>Username:</strong> amy1287 | <strong>Password:</strong> hello456</p>
        <p><strong>Username:</strong> mike1202 | <strong>Password:</strong> quiz789</p>
      </div>
    </div>
  </div>
</body>
</html>
