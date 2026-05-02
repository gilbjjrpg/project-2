<?php

//Connect to the configured database
//This file sets up the $db connection that this page will use.
include '../data/database.php';

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

//SQL query to build the leaderboard.
//The leaderboard view already includes only "10 Question" quiz scores.
$sql = "
    SELECT name, score, total_time, date_taken
    FROM leaderboard
    ORDER BY score DESC, date_taken ASC
    LIMIT 10
";

// Runs the query
$result = $db->query($sql);

// Fetch all returned rows into a normal PHP array
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css">
        <title>Leaderboards — Quizberry</title>
    </head>

    <body>
            <header>
                <?php include '../layout/header.php' ?>
            </header>

            <main class="leaderboard-container">
                <h1>Leaderboards</h1>
                <p>This is the leaderboard for 10-Question quizzes!</p>

                <table border="1">
                    <tr>
                        <th>Name</th>
                        <th>Score</th>
                        <th>Total Time</th>
                        <th>Date</th>
                    </tr>

                    <!-- If the leaderboard rows exist, display them-->
                    <?php if (count($rows) > 0): ?>
                        <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['score'], ENT_QUOTES, 'UTF-8'); ?>%</td>
                            <td><?php echo htmlspecialchars(formatTime($row['total_time'] ?? null), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['date_taken'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <?php endforeach; ?>

                    <!-- Otherwise, show a fallback message-->
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No leaderboard scores yet.</td>
                        </tr>
                    <?php endif; ?>

                </table>
            </main>

            <footer>
                <div class="footer-container">
                    <?php include '../layout/footer.php'; ?>
                </div>
            </footer>


    </body>

</html>
