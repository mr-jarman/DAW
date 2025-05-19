<?php
session_start();
require_once 'game_scores.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['game']) || !isset($data['score'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required data']);
    exit;
}

$gameName = $data['game'];
$score = (int)$data['score'];
$level = isset($data['level']) ? (int)$data['level'] : 1;

// Save the score
if (saveGameScore($_SESSION['user_id'], $gameName, $score, $level)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save score']);
}
?> 