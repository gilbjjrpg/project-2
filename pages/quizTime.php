<?php
// This is the main live quiz page
// JS fills the question text, answers, and score flows here.
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css?v=4">
        <script src="../scripts/quiz.js?v=2" defer></script>
        <title>Quiz time! - Quizberry!</title>
    </head>

    <body>

        <main class="quiz-page">
            <div id="quizContainer">
                <!-- JavaScript updates this with the current question number. -->
                <p id="quizProgress"></p>

                <!-- JavaScript updates this with the countdown timer. -->
                <p id="quizTimer"></p>

                <!-- JavaScript inserts the current question text here. -->
                <h2 id="questionText"></h2>

                <!-- JavaScript fills these buttons with answer choices A-D. -->
                <div id="answerContainer">
                    <button class="answer-btn"></button>
                    <button class="answer-btn"></button>
                    <button class="answer-btn"></button>
                    <button class="answer-btn"></button>
                </div>

                <!-- JavaScript checks the selected answer and moves to the next question. -->
                <button id="nextBtn">Next</button>

            </div>
        </main>

    </body>
    
</html>
