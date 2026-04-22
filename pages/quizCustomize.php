<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="UTF-8" name="viewport" content="width=device-width, intial-scale=1.0">
        <title>Quiz customization - Quizberry!</title>
        <link rel="stylesheet" href="../style/style.css">
        
        <script>
            const quizConfig = {
                mode: "custom",
                questionCount: 15,
                timePerQuestion: 60,
                range: "0-99"
            };

            sessionStorage.setItem("quizConfig", JSON.stringify(quizConfig));
            window.location.href = "quizTime.html"
        </script>

    </head>

</html>