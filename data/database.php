<?php
try {
    //Build the full path to the SQLite database file/
    // __DIR__ = "the fold this file is in"
    $dbPath = __DIR__ . "/quizberry.db";

    //Create a PDO connection to the SQLite database file.
    // If the file does not exist yet, SQLite can create it.
    $db = new PDO("sqlite:" . $dbPath);

    //Turn on exception-based error handling
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Run the database setup/checking function
    initializeDatabase($db);


} catch (PDOException $error) {

    // If the database cannot connect or initialize, stop and show the error
    die("Database connection failed: " . $error->getMessage());
}

/** This block underneathe makes sure the database is ready to use, so there's a lot going on. 
 *  This function:
 * 1. Creates the users table if needed
 * 2. Creates the scores table if needed
 * 3. Seeds starter data from users.json only if the users table is empty
 * 
 */

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

    // Loads starter/demo user data from users.json
    $usersFile = __DIR__ . "/users.json";
    $usersJson = file_get_contents($usersFile);
    $users = json_decode($usersJson, true);
    
    //Loop through each entry in users.json
    foreach ($users as $user) {

        // Skip comment objects or anything that is not a real user
        if (!isset($user['username'])) {
            continue;
        }

        //Insert the user into the users table
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

        //If the user has a play history, move each score into the scores table
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