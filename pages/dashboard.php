<?php
session_start();

// connect to the SQLite database
include '../data/database.php';

// try to get the currently logged-in username from the session
$currentUsername = $_SESSION['username'] ?? null;

//Checks whether the visitor is a guest
$isGuest = !empty($_SESSION['isGuest']);

// this will hold the matched user's basic profile information
$currentUser = null;

// this will hold the user's quiz history from the scores table
$playHistory = [];

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

// only continue if a username session exists && user is not a guest
if ($currentUsername && !$isGuest) {

    // prepare a query to get the user's basic information
    $userStmt = $db->prepare("
        SELECT id, name, username, email
        FROM users
        WHERE username = ?
    ");

    // run the query using the username from the session
    $userStmt->execute([$currentUsername]);

    // fetch the matching user as an associative array
    $currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);

    // if a matching user was found, get that user's score history
    if ($currentUser) {

        // prepare a query to get all quiz scores for this user
        $scoreStmt = $db->prepare("
            SELECT quiz_type, score, question_count, total_time, date_taken
            FROM scores
            WHERE user_id = ?
            ORDER BY date_taken DESC, id DESC
        ");

        // run the score query using the user's id
        $scoreStmt->execute([$currentUser['id']]);

        // fetch all score rows into the playHistory array
        $playHistory = $scoreStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css?v=3">
        <title>Your dashboard — Quizberry!</title>
    </head>

    <body>
        <?php include '../layout/header.php'; ?>

        <main class="main-content">
            <!-- Checks to see if the user is a guest first -->
            <?php if ($isGuest): ?>
                <h1>Welcome, Guest!</h1>
                <p>You are playing as a guest, so your scores will not be saved!</p>


            <!-- If the user was found in the database, show their dashboard -->
            <?php elseif ($currentUser): ?>

                <!-- Show the user's name at the top -->
                <h1>Welcome, <?php echo htmlspecialchars($currentUser['name'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
                <h2>Your Dashboard</h2>

                <p>Username: <?php echo htmlspecialchars($currentUser['username'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Email: <?php echo htmlspecialchars($currentUser['email'], ENT_QUOTES, 'UTF-8'); ?></p>

                <h2>Play History</h2>

                <!-- If the user has any saved quiz scores, show them in a table -->
                <?php if (count($playHistory) > 0): ?>
                    <table border="1">
                        <tr>
                            <th>Quiz Type</th>
                            <th>Score</th>
                            <th>Questions</th>
                            <th>Total Time</th>
                            <th>Date</th>
                        </tr>

                        <!-- Loop through each saved quiz result -->
                        <?php foreach ($playHistory as $quiz): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($quiz['quiz_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($quiz['score'], ENT_QUOTES, 'UTF-8'); ?>%</td>
                                <td><?php echo htmlspecialchars($quiz['question_count'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars(formatTime($quiz['total_time'] ?? null), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($quiz['date_taken'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                <!-- If there are no saved quiz scores yet, show a fallback message -->
                <?php else: ?>
                    <p>No quiz history yet. Play a quiz or sign up to get your history started!</p>
                <?php endif; ?>

            <!-- If no matching logged-in user was found, show this message -->
            <?php else: ?>
                <p>No user is currently logged in.</p>
            <?php endif; ?>
        </main>

        <?php include '../layout/footer.php'; ?>
    </body>
</html>
