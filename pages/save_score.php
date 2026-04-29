<?php

//Connect tot he SQLite database
include '../data/database.php';

// Tell JS that this file returns JSON
header('Content-Type: application/json');

//read the JSON daata sent from quiz.js
$data = json_decode(file_get_contents("php://input"), true);

//Only require quiz info from JS
if(
    !isset($data['quizType']) ||
    !isset($data['score']) ||
    !isset($data['dateTaken'])
) {
    echo json_encode ([
        "success" => false,
        "message" => "Missing required data."
    ]);
    exit;
}

// Get the logged-in username from the cookie inster of sessionStorage
$currentUsername = $_COOKIE['username'] ?? null;

//If no cookie exists, stop
if(!$currentUsername) {
    echo json_encode([
        "success" => false,
        "message" => "No logged-in user found."
    ]);
    exit;
}

// If the user is a guest, do not save the score
if($currentUsername === "Guest") {
    echo json_encode([
        "success" => false,
        "message" => "Guest scores are not saved!"
    ]);
    exit;
}

//Pull the submitted values into variables
$quizType = $data['quizType'];
$score = (int)$data['score'];
$dateTaken = $data['dateTaken'];

//Find the matching user id in the users table
$userStmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$userStmt->execute([$currentUsername]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);


//If no matching user was found, then stop
if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "User not found."
    ]);
    exit;
}

//Pull out the matched user id
$userId = $user['id'];

//Insert the new score into the scores table
$scoreStmt = $db->prepare("
    INSERT INTO scores (user_id, quiz_type, score, date_taken)
    VALUES (?, ?, ?, ?)
");

$scoreStmt->execute([$userId, $quizType, $score, $dateTaken]);

//Send a success responce back to JS
echo json_encode([
    "success" => true,
    "message" => "Score saved successfully."
]);
?>