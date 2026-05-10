<?php
session_start();

// Connect to the SQLite database
include '../data/database.php';

// Try to get the currently logged-in username from the session
$currentUsername = $_SESSION['username'] ?? null;

//Checks whether the visitor is a guest
$isGuest = !empty($_SESSION['isGuest']);

// This will hold the matched user's basic info
$currentUser = null;

// This will hold all score rows for the current user
$userScores = [];

//Formats seconds into a readable time, such as "2m 14s".
function formatTime($seconds) {
    if ($seconds === null || $seconds === "") {
        return "N/A";
    }

    $seconds = (int)$seconds;
    $minutes = intdiv($seconds, 60);
    $remainingSeconds = $seconds % 60;

    if ($minutes > 0) {
        return $minutes . "m " . $remainingSeconds . "s";
    }

    return $remainingSeconds . "s";
}

// Only continue if a username session exists & user is NOT a guest
if ($currentUsername && !$isGuest) {

    // Find the logged-in user in the users table
    $userStmt = $db->prepare("
        SELECT id, name, username, email
        FROM users
        WHERE username = ?
    ");

    // Run the query with the username from the session
    $userStmt->execute([$currentUsername]);

    // Fetch the matching user row
    $currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);

    // If the user exists, load all their scores
    if ($currentUser) {
        $scoreStmt = $db->prepare("
            SELECT quiz_type, score, question_count, total_time, date_taken
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
        <link rel="stylesheet" href="../style/style.css?v=5">
        <title>Scores — Quizberry!</title>
    </head>

    <body>
        <?php include '../layout/header.php'; ?>

        <main class="main-content scores-container">
            <h1>Scores</h1>
            <h2>Your scores!</h2>

            <?php if ($isGuest): ?>
                <p>You are playing as a guest, so your scores will not be saved!</p>
                <p>Consider signing up to keep track of your scores!</p>

            <!-- If a logged-in user exists, show their saved score history -->
            <?php elseif ($currentUser): ?>

                <?php if (count($userScores) > 0): ?>
                    <table>
                        <tr>
                            <th>Quiz Type</th>
                            <th>Score</th>
                            <th>Questions</th>
                            <th>Total Time</th>
                            <th>Date</th>
                        </tr>

                        <!-- Loop through each saved score row -->
                        <?php foreach ($userScores as $score): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($score['quiz_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($score['score'], ENT_QUOTES, 'UTF-8'); ?>%</td>
                                <td><?php echo htmlspecialchars($score['question_count'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars(formatTime($score['total_time'] ?? null), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($score['date_taken'], ENT_QUOTES, 'UTF-8'); ?></td>
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

        <?php include '../layout/footer.php'; ?>

    </body>
</html>
