<?php
$usersFile = "../data/users.json";
$usersJson = file_get_contents($usersFile);
$users = json_decode($usersJson, true);

//Array to hold every leaderboard entry
$leaderboardEntries = [];

//Loops through each user
foreach($users as $user) {
    //Skips guests just in case
    if (!empty($user['isGuest'])) {
        continue;
    }

    //If the user has a play history, check each quiz result
    if(!empty($user['playHistory'])) {
        foreach($user['playHistory'] as $quiz) {

        //only include 10 Question quizzes on the leaderboard!
            if($quiz['quizType'] === "10 Question") {
                $leaderboardEntries[] = [
                    "name" => $user['name'],
                    "score" => $quiz['score'],
                    "date" => $quiz['date']
                ];
            }
        }
    }
}

//sorting function (highest score first)
usort($leaderboardEntries, function($a, $b) {
    return $b['score'] <=> $a['score'];
});

//keep ONLY the top 10
$leaderboardEntries = array_slice($leaderboardEntries, 0, 10);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, intial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css">
        <title>Your dashboard — Quizberry</title>
    </head>

    <body>
            <header>
                <?php include '../layout/header.php' ?>
            </header>

            <main style="leaderboard-container">
                <h1>Leaderboards</h1>
                <p>This is the leaderboard!</p>

                <table>
                    <tr>
                        <th>Name</th>
                        <th>Score</th>
                        <th>Date</th>
                    </tr>

                    <?php if(count($leaderboardEntries) > 0): ?>
                        <?php foreach($leaderboardEntries as $entry): ?>
                            <tr>
                                <td><?php echo $entry['name']; ?></td>
                                <td><?php echo $entry['score']; ?>%</td>
                                <td><?php echo $entry['date']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No leaderboard scores yet.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </main>

            <footer>
                <div class="footer-container">
                    <?php include '../layout/footer.php'; ?>
                </div>
            </footer>


    </body>

</html>