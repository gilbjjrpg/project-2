<?php
$dbPath = __DIR__ . "/../data/quizberry.db";
$db = new PDO("sqlite:" . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        is_guest INTEGER NOT NULL DEFAULT 0
    )
");

$db->exec("
    CREATE TABLE IF NOT EXISTS scores (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        quiz_type TEXT NOT NULL,
        score INTEGER NOT NULL,
        date_taken TEXT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )
");

$db->exec("DELETE FROM scores");
$db->exec("DELETE FROM users");

$usersFile = __DIR__ . "/../data/users.json";
$usersJson = file_get_contents($usersFile);
$users = json_decode($usersJson, true);

foreach ($users as $user) {
    if (!isset($user['username'])) {
        continue;
    }

    $stmt = $db->prepare("
        INSERT INTO users (id, username, name, email, password, is_guest)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user['id'],
        $user['username'],
        $user['name'],
        $user['email'],
        $user['password'],
        $user['isGuest'] ? 1 : 0
    ]);

    if (!empty($user['playHistory'])) {
        foreach ($user['playHistory'] as $quiz) {
            $scoreStmt = $db->prepare("
                INSERT INTO scores (user_id, quiz_type, score, date_taken)
                VALUES (?, ?, ?, ?)
            ");

            $scoreStmt->execute([
                $user['id'],
                $quiz['quizType'],
                $quiz['score'],
                $quiz['date']
            ]);
        }
    }
}

echo "Database setup complete.";

?>