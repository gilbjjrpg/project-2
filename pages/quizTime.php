<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, intial-scale=1.0">
        <title>Quiz time! - Quizberry!</title>
        <link rel="stylesheet" href="../style/style.css">

        <script>
            window.addEventListener("DOMContentLoaded", function() {
                setupQuizTimePage();
            });

            let allQuestions = [];
            let quizQuestions = [];
            let currentQuestionIndex = 0;
            let totalCorrect = 0;
            let selecterAnswer = null;
            let quizConfig = null;

        </script>

    </head>

    <main>
        <div id="quizContainer">
            <p id="quizProgress"></p>
            <p id="quizTimer"></p>

            <h2 id="questionText"></h2>

            <div id="answerContainer">
                <button class="answer-btn"></button>
                <button class="answer-btn"></button>
                <button class="answer-btn"></button>
                <button class="answer-btn"></button>
            </div>

            <button id="nextBtn">Next</button>

        </div>
    </main>
    
</html>