<?php
try {
    //Connect to MySQL when DB_HOST or MYSQL_HOST is set. Otherwise, use local SQLite.
    $dbDriver = (getenv("DB_HOST") || getenv("MYSQL_HOST")) ? "mysql" : "sqlite";

    if ($dbDriver === "mysql") {
        //These values should be set in Render's Environment Variables.
        $dbHost = getenv("DB_HOST") ?: getenv("MYSQL_HOST");
        $dbPort = getenv("DB_PORT") ?: getenv("MYSQL_PORT") ?: "3306";
        $dbName = getenv("DB_NAME") ?: getenv("MYSQL_DATABASE") ?: "quizberry";
        $dbUser = getenv("DB_USER") ?: getenv("MYSQL_USER") ?: "quizberry";
        $dbPassword = getenv("DB_PASSWORD") ?: getenv("MYSQL_PASSWORD") ?: "";

        //Create a PDO connection to the MySQL database.
        $db = new PDO(
            "mysql:host=$dbHost;
            port=$dbPort;
            dbname=$dbName;
            charset=utf8mb4",
            $dbUser,
            $dbPassword
        );
    } else {
        //Build the full path to the local SQLite database file.
        $dbPath = __DIR__ . "/quizberry.db";

        //Create a PDO connection to the SQLite database file.
        //If the file does not exist yet, SQLite can create it.
        $db = new PDO("sqlite:" . $dbPath);
    }

    //Turn on exception-based error handling.
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Make fetched database rows use associative arrays by default.
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    //Run the database setup/checking function.
    initializeDatabase($db, $dbDriver);
} catch (PDOException $error) {
    //If the database cannot connect or initialize, stop and show the error.
    die("Database connection failed: " . $error->getMessage());
}

/** This function makes sure the database is ready to use.
 *  This function:
 * 1. Creates the users table if needed
 * 2. Creates the scores table if needed
 * 3. Creates the leaderboard view if needed
 * 4. Seeds starter data from users.json only if the users table is empty
 */
function initializeDatabase($db, $dbDriver) {
    createTables($db, $dbDriver);
    addMissingScoreColumns($db, $dbDriver);
    backfillExistingScoreData($db);
    createLeaderboardView($db);
    seedStarterDataIfNeeded($db);
}

//Creates the users and scores tables using the correct SQL for MySQL or SQLite.
function createTables($db, $dbDriver) {
    if ($dbDriver === "mysql") {
        //Create users table if it does not exist.
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                is_guest TINYINT(1) NOT NULL DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        //Create scores table if it does not exist.
        $db->exec("
            CREATE TABLE IF NOT EXISTS scores (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                quiz_type VARCHAR(100) NOT NULL,
                score INT NOT NULL,
                question_count INT NULL,
                total_time INT NULL,
                date_taken DATE NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } else {
        //Create users table if it does not exist.
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

        //Create scores table if it does not exist.
        $db->exec("
            CREATE TABLE IF NOT EXISTS scores (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                quiz_type TEXT NOT NULL,
                score INTEGER NOT NULL,
                question_count INTEGER,
                total_time INTEGER,
                date_taken TEXT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ");
    }
}

//Adds new score columns to older databases that were created before these fields existed.
function addMissingScoreColumns($db, $dbDriver) {
    $scoreColumns = getScoreColumns($db, $dbDriver);

    if (!in_array("question_count", $scoreColumns)) {
        $columnType = ($dbDriver === "mysql") ? "INT NULL" : "INTEGER";
        $db->exec("ALTER TABLE scores ADD COLUMN question_count $columnType");
    }

    if (!in_array("total_time", $scoreColumns)) {
        $columnType = ($dbDriver === "mysql") ? "INT NULL" : "INTEGER";
        $db->exec("ALTER TABLE scores ADD COLUMN total_time $columnType");
    }
}

//Gets the current column names from the scores table.
function getScoreColumns($db, $dbDriver) {
    $columns = [];

    if ($dbDriver === "mysql") {
        $columnResult = $db->query("SHOW COLUMNS FROM scores");

        foreach ($columnResult->fetchAll() as $column) {
            $columns[] = $column['Field'];
        }
    } else {
        $columnResult = $db->query("PRAGMA table_info(scores)");

        foreach ($columnResult->fetchAll() as $column) {
            $columns[] = $column['name'];
        }
    }

    return $columns;
}

//Fills older score rows with default question/time values if they were created before these fields existed.
function backfillExistingScoreData($db) {
    $db->exec("
        UPDATE scores
        SET question_count = 10
        WHERE question_count IS NULL
    ");

    $db->exec("
        UPDATE scores
        SET total_time = 600
        WHERE total_time IS NULL
    ");
}

//Creates the leaderboard view from the scores table.
//A view acts like a table, but its rows are generated from the latest score data.
function createLeaderboardView($db) {
    //Drop the view first so changes to the selected columns are applied.
    $db->exec("DROP VIEW IF EXISTS leaderboard");

    $db->exec("
        CREATE VIEW leaderboard AS
        SELECT
            users.name,
            scores.score,
            scores.total_time,
            scores.date_taken
        FROM scores
        JOIN users ON scores.user_id = users.id
        WHERE scores.quiz_type = '10 Question'
    ");
}

//Seeds starter/demo user data from users.json only when the users table is empty.
function seedStarterDataIfNeeded($db) {
    //Check whether users table already has data.
    $userCountResult = $db->query("SELECT COUNT(*) AS count FROM users");
    $userCountRow = $userCountResult->fetch();
    $userCount = (int)$userCountRow['count'];

    //If users already exist, stop here.
    if ($userCount > 0) {
        return;
    }

    seedStarterData($db);
}

//Loads starter/demo data from users.json and quizberry.db into the database.
function seedStarterData($db) {
    //Loads starter/demo user data from users.json.
    $usersFile = __DIR__ . "/users.json";
    $usersJson = file_get_contents($usersFile);
    $users = json_decode($usersJson, true);

    //Loop through each entry in users.json.
    foreach ($users as $user) {
        //Skip comment objects or anything that is not a real user.
        if (!isset($user['username'])) {
            continue;
        }

        //Hash the starter password before saving it to the database.
        $hashedPassword = password_hash($user['password'], PASSWORD_BCRYPT);

        //Insert the user into the users table.
        $stmt = $db->prepare("
            INSERT INTO users (id, username, name, email, password, is_guest)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $user['id'],
            $user['username'],
            $user['name'],
            $user['email'],
            $hashedPassword,
            $user['isGuest'] ? 1 : 0
        ]);

        //If the user has play history, move each score into the scores table.
        if (!empty($user['playHistory'])) {
            foreach ($user['playHistory'] as $quiz) {
                $questionCount = $quiz['questionCount'] ?? 10;
                $totalTime = $quiz['totalTime'] ?? 600;

                $scoreStmt = $db->prepare("
                    INSERT INTO scores (user_id, quiz_type, score, question_count, total_time, date_taken)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                $scoreStmt->execute([
                    $user['id'],
                    $quiz['quizType'],
                    $quiz['score'],
                    $questionCount,
                    $totalTime,
                    $quiz['date']
                ]);
            }
        }
    }
}
?>
