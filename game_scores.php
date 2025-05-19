<?php
require_once 'db_connect.php';

function saveGameScore($userId, $gameName, $score, $level = 1) {
    global $pdo;
    
    try {
        // Check if user already has a score for this game
        $stmt = $pdo->prepare("SELECT score FROM game_scores WHERE user_id = ? AND game_name = ?");
        $stmt->execute([$userId, $gameName]);
        $result = $stmt->fetch();
        
        if ($result) {
            // Only update if new score is higher
            if ($score > $result['score']) {
                $stmt = $pdo->prepare("UPDATE game_scores SET score = ?, level = ? WHERE user_id = ? AND game_name = ?");
                return $stmt->execute([$score, $level, $userId, $gameName]);
            }
            return true;
        } else {
            // Insert new score
            $stmt = $pdo->prepare("INSERT INTO game_scores (user_id, game_name, score, level) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$userId, $gameName, $score, $level]);
        }
    } catch (PDOException $e) {
        error_log("Error saving game score: " . $e->getMessage());
        return false;
    }
}

function getHighScore($userId, $gameName) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT score, level FROM game_scores WHERE user_id = ? AND game_name = ?");
        $stmt->execute([$userId, $gameName]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error getting high score: " . $e->getMessage());
        return null;
    }
}
?> 