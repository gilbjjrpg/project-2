<?php
// This page lets the user choose custome quiz settings.
// The user's choices will be saved and then used by quizTime.php
?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="UTF-8" name="viewport" content="width=device-width, intial-scale=1.0">
        <script src="../scripts/quiz.js" defer></script>
        <link rel="stylesheet" href="../style/style.css">
        <title>Quiz customization - Quizberry!</title>
        
    </head>

    <body>

    <header>
    <?php include '../layout/header.php' ?>
    </header>

    <main>
        <h1>Customize your Quiz!</h1>

        <form id=customQuizForm>
            <label for="questionCount">Number of Questions</label>
            <input type="number" id="questionCount" min="5" max="50" value="10">

            <label for="timePerQuestion">Time per questions (in seconds)</label>
            <input type="number" id="timePerQuestion" min="30" max="300" value="60"> 

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

            <button type="submit">Start Custom Quiz</button>
        </form>
    </main>

    <footer>
        <?php include '../layout/footer.php' ?>
    </footer>

    </body>

</html>