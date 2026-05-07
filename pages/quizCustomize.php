<?php
// This page lets the user choose custom quiz settings.
// The user's choices will be saved in sessionStorage and then used by quizTime.php.
?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="../scripts/quiz.js?v=2" defer></script>
        <link rel="stylesheet" href="../style/style.css?v=3">
        <title>Quiz customization - Quizberry!</title>
        
    </head>

    <body>

    <!-- Shared site navigation. -->
    <?php include '../layout/header.php' ?>

    <main class="main-content customize-container">
        <h1>Customize your Quiz!</h1>
        <h2>Create a Quiz of your own!</h2>

        <!-- JavaScript reads this form and saves the custom quiz settings. -->
        <form id=customQuizForm>

            <!-- Controls how many questions will be selected for the quiz. -->
            <label for="questionCount">Number of Questions</label>
            <input type="number" id="questionCount" min="5" max="50" value="10">

            <!-- Controls how many seconds the user gets on each question. -->
            <label for="timePerQuestion">Time per question (in seconds)</label>
            <input type="number" id="timePerQuestion" min="30" max="300" value="60"> 

            <!-- Controls which section of questions.json the quiz pulls from. -->
            <label for="questionRange">Question Range</label>
            <select id="questionRange">
                <option value="all">All questions!</option>
                <option value="0-99">0 - 99 range.</option>
                <option value="100-199">100 - 199 range.</option>
                <option value="200-299">200 - 299 range.</option>
                <option value="300-399">300 - 399 range.</option>
                <option value="400-499">400 - 499 range.</option>
                <option value="500-599">500 - 599 range.</option>
            </select>

            <button type="submit" id="startCustomQuizBtn">Start Custom Quiz</button>
        </form>
    </main>

        <!-- Shared site footer. -->
        <?php include '../layout/footer.php' ?>

    </body>

</html>
