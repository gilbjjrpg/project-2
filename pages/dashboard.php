<?php
// connect to the SQLite database
include '../data/database.php';

// try to get the currently logged-in username from the cookie
$currentUsername = $_COOKIE['username'] ?? null;
$isGuest = ($currentUsername === "Guest");

// this will hold the matched user's basic profile information
$currentUser = null;

// this will hold the user's quiz history from the scores table
$playHistory = [];

// only continue if a username cookie exists
if ($currentUsername) {

    // prepare a query to get the user's basic information
    $userStmt = $db->prepare("
        SELECT id, name, username, email
        FROM users
        WHERE username = ?
    ");

    // run the query using the username from the cookie
    $userStmt->execute([$currentUsername]);

    // fetch the matching user as an associative array
    $currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);

    // if a matching user was found, get that user's score history
    if ($currentUser) {
        // prepare a query to get all quiz scores for this user
        $scoreStmt = $db->prepare("
            SELECT quiz_type, score, date_taken
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
        <link rel="stylesheet" href="../style/style.css">
        <title>Your dashboard — Quizberry!</title>
    </head>

    <body>
        <header>
            
            <?php include '../layout/header.php'; ?>
        </header>

        <main>
            <!-- Checks to see if the user is a guest first -->
            <?php if ($isGuest): ?>
                <h1>Welcome, Guest!</h1>
                <p>You are playing as a guest, so your scores will not be saved!</p>


            <!-- If the user was found in the database, show their dashboard -->
            <?php elseif ($currentUser): ?>

                <!-- Show the user's name at the top -->
                <h1>Welcome, <?php echo $currentUser['name']; ?>!</h1>

                <h2>Your Dashboard</h2>

                <p>Username: <?php echo $currentUser['username']; ?></p>
                <p>Email: <?php echo $currentUser['email']; ?></p>

                <h2>Play History</h2>

                <!-- If the user has any saved quiz scores, show them in a table -->
                <?php if (count($playHistory) > 0): ?>
                    <table border="1">
                        <tr>
                            <th>Quiz Type</th>
                            <th>Score</th>
                            <th>Date</th>
                        </tr>

                        <!-- Loop through each saved quiz result -->
                        <?php foreach ($playHistory as $quiz): ?>
                            <tr>
                                <td><?php echo $quiz['quiz_type']; ?></td>
                                <td><?php echo $quiz['score']; ?>%</td>
                                <td><?php echo $quiz['date_taken']; ?></td>
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

        <footer>
            <?php include '../layout/footer.php'; ?>
        </footer>
    </body>
</html>