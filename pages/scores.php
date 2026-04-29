<?php
// Connect to the SQLite database
include '../data/database.php';

// Try to get the currently logged-in username from the cookie
$currentUsername = $_COOKIE['username'] ?? null;

//Checks whether the visitor is a guest
$isGuest = ($currentUsername === "Guest");

// This will hold the matched user's basic info
$currentUser = null;

// This will hold all score rows for the current user
$userScores = [];

// Only continue if a username cookie exists & user is NOT a guest
if ($currentUsername && !$isGuest) {

    // Find the logged-in user in the users table
    $userStmt = $db->prepare("
        SELECT id, name, username, email
        FROM users
        WHERE username = ?
    ");

    // Run the query with the username from the cookie
    $userStmt->execute([$currentUsername]);

    // Fetch the matching user row
    $currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);

    // If the user exists, load all their scores
    if ($currentUser) {
        $scoreStmt = $db->prepare("
            SELECT quiz_type, score, date_taken
            FROM scores
            WHERE user_id = ?
            ORDER BY date_taken DESC, id DESC
        ");

        // Run the query using the user's id
        $scoreStmt->execute([$currentUser['id']]);

        // Store all returned score rows
        $userScores = $scoreStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css">
        <title>Scores — Quizberry!</title>
    </head>

    <body>
        <header>
            <?php include '../layout/header.php'; ?>
        </header>

        <main class="scores-container">
            <h1>Scores</h1>
            <p>Your scores!</p>

            <?php if ($isGuest): ?>
                <p>You are playing as a guest, so your scores will not be saved!</p>
                <p>Consider signing up to keep track of your scores!</p>

            <!-- If a logged-in user exists, show their saved score history -->
            <?php elseif ($currentUser): ?>

                <?php if (count($userScores) > 0): ?>
                    <table border="1">
                        <tr>
                            <th>Quiz Type</th>
                            <th>Score</th>
                            <th>Date</th>
                        </tr>

                        <!-- Loop through each saved score row -->
                        <?php foreach ($userScores as $score): ?>
                            <tr>
                                <td><?php echo $score['quiz_type']; ?></td>
                                <td><?php echo $score['score']; ?>%</td>
                                <td><?php echo $score['date_taken']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <!-- Fallback if the user has no saved scores -->
                    <p>No scores saved yet.</p>
                <?php endif; ?>

            <?php else: ?>
                <!-- Fallback if nobody is logged in -->
                <p>No user is currently logged in.</p>
            <?php endif; ?>
        </main>

        <footer>
            <?php include '../layout/footer.php'; ?>
        </footer>

    </body>
</html>