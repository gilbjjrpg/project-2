<?php
include '../data/database.php';

$sql = "SELECT users.name, scores.score, scores.date_taken
        FROM scores
        JOIN users ON scores.user_id = users.id
        WHERE scores.quiz_type = '10 Question'
        ORDER BY scores.score DESC, scores.date_taken ASC
        LIMIT 10";

        $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, intial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css">
        <title>Leaderboards — Quizberry</title>
    </head>

    <body>
            <header>
                <?php include '../layout/header.php' ?>
            </header>

            <main class="leaderboard-container">
                <h1>Leaderboards</h1>
                <p>This is the leaderboard!</p>

                <table border="1">
                    <tr>
                        <th>Name</th>
                        <th>Score</th>
                        <th>Date</th>
                    </tr>

                    <?php if($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['score']; ?>%</td>
                                <td><?php echo $row['date_taken']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No leaderboard scores yet.</td>
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