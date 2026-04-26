<?php

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, intial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css">
        <title>Your dashboard — Quizberry</title>
    </head>

    <body>
            <header>
                <?php include '../layout/header.php' ?>
            </header>

            <main style="leaderboard-container">
                <h1>Leaderboards</h1>
                <p>This is the leaderboard!</p>

                <table>
                    <tr>
                        <th>Name</th>
                        <th>Score</th>
                        <th>Date</th>
                    </tr>
                </table>
                
            </main>

            <footer>
                <div class="footer-container">
                    <?php include '../layout/footer.php'; ?>
            </footer>


    </body>

</html>