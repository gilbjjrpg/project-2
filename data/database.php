<?php
try {
    $dbPath = __DIR__ . "/quizberry.db";
    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    die("Database connection failed: " . $error->getMessage());
}


?>