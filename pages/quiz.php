<!DOCTYPE html>

<?php 
// This page is the quiz menu page that doubles as the homepage.
// The quiz is not actually run here.
// User can select to do a 10-question quiz or a custom quiz.
?>

<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css">
        <script src="../scripts/quiz.js" defer></script>
        <title>Quiz! — Quizberry</title>

    </head>

    <body>
        <header>
            <!-- Shared site navigation. -->
            <?php include '../layout/header.php' ?>
        </header>

        <main class="main-content">
            <div>
                <!-- Main page heading and short instructions for picking a quiz mode. -->
                <h1>
                    Welcome to the homepage. Choose your Quiz!
                </h1>

                <h2>
                    Select to do either a 10-Question or your own custom Quiz.
                </h2>
            </div>

            <!-- JavaScript uses this id to create the standard 10-question quizConfig. -->
            <a href="quizTime.php" id="startTenQuestion" class="primary-btn">
                <button>10-Question</button>
            </a>

            <!-- Custom quizzes are configured on a separate form page. -->
            <a href="quizCustomize.php" class="primary-btn">
                <button>Custom</button>
            </a>

        </main>

        <footer>
            <!-- Shared site footer. -->
            <?php include '../layout/footer.php' ?>
        </footer>

    </body>

</html>
