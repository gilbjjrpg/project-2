<?php
$db = new PDO("sqlite:../data/quizberry.db");
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

echo "Database setup complete.";
?>