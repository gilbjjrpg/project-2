<?php
session_start();

$usersFile = "../data/users.json";
$usersJson = file_get_contents($usersFile);
$users = json_decode($usersJson, true);

//Gets the logged-in name from the session
$currentUsername = $_SESSION['username'] ?? null;

//Start with no user found
$currentUser = null;

//Look through users.json for the matching user
if($currentUsername) {
    foreach($users as $user) {
        if($user['username'] === $currentUsername) {
            $currentUser = $user;
            break;
        }
    }
}

//Only assifn IF a current user was found
if($currentUser) {
    $name = $currentUser['name'];
    $username = $currentUser['username'];
    $email = $currentUser['email'];
    $playHistory = $currentUser['playHistory'];
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
            <meta charset="UTF-8" name="viewport" content="width=device-width, intial-scale=1.0">
            <link rel="stylesheet" href="../style/style.css">
            <title>Your dashboard — Quizberry!</title>
    </head>

    <body>
        <header>
            <?php include '../layout/header.php'; ?>
        </header>

        <main>

            <?php if ($currentUser): ?>
                <h1>Welcome, <?php echo $name; ?>!</h1>
                <h2>Your Dashboard</h2>
                <p>Username: <?php echo $username; ?></p>
                <p>Email: <?php echo $email; ?></p>

                <h2>Play History</h2>

                <?php if (count($playHistory) > 0): ?>
                
                    <table border="1">

                        <tr>
                            <th>Quiz Type</th>
                            <th>Score</th>
                            <th>Date</th>
                        </tr>

                        <?php foreach ($playHistory as $quiz): ?>

                            <tr>
                                <td><?php echo $quiz['quizType']; ?></td>
                                <td><?php echo $quiz['score']; ?>%</td>
                                <td><?php echo $quiz['date']; ?></td>
                            </tr>

                        <?php endforeach; ?>

                    </table>

                <?php else: ?>
                    <p>No quiz history yet. Play a quiz or sign up to get your history started!"</p>
                <?php endif; ?>

            <?php else: ?>
                <p>No user is currently logged in.</p>
            <?php endif; ?>

        </main>

        <footer>
            <?php include '../layout/footer.php'; ?>
        </footer>
        
    </body>

</html>