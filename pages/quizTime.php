<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, intial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css">
        <script src="../scripts/quiz.js" defer></script>
        <title>Quiz time! - Quizberry!</title>
    </head>

    <body>
        <header>
            <?php include '../layout/header.php' ?>
        </header>

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

        <footer>
            <?php include '../layout/header.php' ?>
        </footer>
    </body>
    
</html>