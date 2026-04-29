<?php
include '../data/database.php';

header('Content-Type: application/json');

//read JSON sent from JavaScript
$data = json_decode(file_get_contents("php://input"), true);

//make sure required values exist
if (
    !isset($data['username']) ||
    !isset($data['quizType']) ||
    !isset($data['score']) ||
    !isset($data['dateTaken'])
) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required data."
    ]);
    exit;
}

$username = $data['username'];
$quizType = $data['quizType'];
$score = (int)$data['score'];
$dateTaken = $data['dateTaken'];

//find the matching user id
$userStmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$userStmt->execute([$username]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "User not found."
    ]);
    exit;
}

$userId = $user['id'];

//insert new score into scores table
$scoreStmt = $db->prepare("
    INSERT INTO scores (user_id, quiz_type, score, date_taken)
    VALUES (?, ?, ?, ?)
");

$scoreStmt->execute([$userId, $quizType, $score, $dateTaken]);

echo json_encode([
    "success" => true,
    "message" => "Score saved successfully."
]);
?>