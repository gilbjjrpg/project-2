<?php

/*
As the name of the file would imply, this page is used to save the user's score and enter it into a table. 
This page also isn't accessible via any links. Nothing happens when accessing it on browser.
*/
session_start();

//Connect to the configured database
include '../data/database.php';

// Tell JS that this file returns JSON
header('Content-Type: application/json');

//read the JSON data sent from quiz.js
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

// Get the logged-in username from the session
$currentUsername = $_SESSION['username'] ?? null;
$isGuest = !empty($_SESSION['isGuest']);

//If no session exists, stop
if(!$currentUsername) {
    echo json_encode([
        "success" => false,
        "message" => "No logged-in user found."
    ]);
    exit;
}

// If the user is a guest, do not save the score
if($isGuest) {
    echo json_encode([
        "success" => false,
        "message" => "Guest scores are not saved!"
    ]);
    exit;
}

//Pull the submitted values into variables
$quizType = $data['quizType'];
$score = (int)$data['score'];
$questionCount = isset($data['questionCount']) ? (int)$data['questionCount'] : null;
$totalTime = isset($data['totalTime']) ? (int)$data['totalTime'] : null;
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
    INSERT INTO scores (user_id, quiz_type, score, question_count, total_time, date_taken)
    VALUES (?, ?, ?, ?, ?, ?)
");

$scoreStmt->execute([$userId, $quizType, $score, $questionCount, $totalTime, $dateTaken]);

//Send a success response back to JS
echo json_encode([
    "success" => true,
    "message" => "Score saved successfully."
]);
?>
