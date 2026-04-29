<!DOCTYPE html>

<?php 
// This page is the quiz menu page that doubles as the homepage.
// The quiz is not actually ran here
// User can select to do a 10-question quiz or a custom quiz
?>

<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, intial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css">
        <script src="../scripts/quiz.js" defer></script>
        <title>Quiz! — Quizberry</title>

    </head>

    <body>
        <header>
            <?php include '../layout/header.php' ?>
        </header>

        <main>
            <div>
                <h1>
                    Welcome to the homepage. Choose your Quiz!
                </h1>

                <p>
                    Select to do either a 10-Question or your own custom Quiz.
                </p>
            </div>

            <a href="quizTime.php" id="startTenQuestion">10-Question</a>
            <a href="quizCustomize.php">Custom</a>

        </main>

        <footer>
            <?php include '../layout/footer.php' ?>
        </footer>

    </body>

</html>