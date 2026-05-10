<!DOCTYPE html>

<?php 
// This page is the quiz menu page that doubles as the homepage.
// The quiz is not actually ran here.
// User can select to do a 10-question quiz or a custom quiz.
?>

<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css?v=5">
        <script src="../scripts/quiz.js?v=2" defer></script>
        <title>Quiz! — Quizberry</title>

    </head>

    <body>
        <!-- Shared site navigation. -->
        <?php include '../layout/header.php' ?>

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

            <div class="quiz-option-buttons">
                <!-- JavaScript uses this id to create the standard 10-question quizConfig. -->
                <a href="quizTime.php" id="startTenQuestion" class="quiz-option-btn">10-Question</a>

                <!-- Custom quizzes are configured on a separate form page. -->
                <a href="quizCustomize.php" class="quiz-option-btn">Custom</a>
            </div>

        </main>

        <!-- Shared site footer. -->
        <?php include '../layout/footer.php' ?>

    </body>

</html>
