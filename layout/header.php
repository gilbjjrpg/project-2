<?php

// ----> THIS IS USED AS THE HEADER FOR ALL PAGES. CHANGE ONLY IF YOU NEED TO. <----
?>

<header>
  <div class="header-container">
    <!-- Site title shown on every page that includes this header. -->
    <h1>Quizberry!</h1>

    <!-- Main navigation links shared across the app. -->
    <nav>
      <ul>

        <!-- Sends users to the quiz menu page. -->
        <li class="button">
          <a href="../pages/quiz.php">Home</a>
        </li>

        <!-- Sends users to their profile/dashboard page. -->
        <li class="button">
          <a href="../pages/dashboard.php">Dashboard</a>
        </li>

        <!-- Shows the top 10-question quiz scores. -->
        <li class="button">
          <a href="../pages/leaderboard.php">Leaderboard</a>
        </li>

        <!-- Shows the current user's saved scores. -->
        <li class="button">
          <a href="../pages/scores.php">Scores</a>
        </li>

        <!-- Ends the current PHP session and returns to login. -->
        <li class="button">
          <a href="../pages/logout.php">Logout</a>
        </li>

      </ul>
    </nav>

  </div>
</header>
