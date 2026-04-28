<?php
try {
    $db = new PDO("sqlite:../data/quizberry.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    die("Database connection failed: " . $error->getMessage());
}


?>
