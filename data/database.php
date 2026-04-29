<?php
try {
    $dbPath = __DIR__ . "/quizberry.db";
    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    initializeDatabase($db);
} catch (PDOException $error) {
    die("Database connection failed: " . $error->getMessage());
}

function initializeDatabase($db) {
    // Create users table if it does not exist
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

    // Create scores table if it does not exist
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

    // Check whether users table already has data
    $userCountResult = $db->query("SELECT COUNT(*) AS count FROM users");
    $userCountRow = $userCountResult->fetch(PDO::FETCH_ASSOC);
    $userCount = (int)$userCountRow['count'];

    // If users already exist, stop here
    if ($userCount > 0) {
        return;
    }

    // Otherwise, load starter data from users.json
    $usersFile = __DIR__ . "/users.json";
    $usersJson = file_get_contents($usersFile);
    $users = json_decode($usersJson, true);

    foreach ($users as $user) {
        // Skip comment objects or anything that is not a real user
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
}

?>