<!DOCTYPE html>

<?php 
// This page is used ONLY for navigation.
?>

<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, intial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css">
        <title>Quiz! — Quizberry</title>

        <script>

        </script>
    </head>

    <body>
        <header>
            <?php include '../layout/header.php' ?>
        </header>

        <main>
            <div>
                <h1>
                    Choose your Quiz!
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