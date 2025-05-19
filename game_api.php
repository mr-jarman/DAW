<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated', 'success' => false]);
    exit;
}
$user_id = $_SESSION['user_id'];

$morse_code_map = [
    'A' => '.- ', 'B' => '-... ', 'C' => '-.-. ', 'D' => '-.. ', 'E' => '. ', 
    'F' => '..-. ', 'G' => '--. ', 'H' => '.... ', 'I' => '.. ', 'J' => '.--- ',
    'K' => '-.- ', 'L' => '.-.. ', 'M' => '-- ', 'N' => '-. ', 'O' => '--- ',
    'P' => '.--. ', 'Q' => '--.- ', 'R' => '.-. ', 'S' => '... ', 'T' => '- ',
    'U' => '..- ', 'V' => '...- ', 'W' => '.-- ', 'X' => '-..- ', 'Y' => '-.-- ',
    'Z' => '--.. ',
    '0' => '----- ', '1' => '.---- ', '2' => '..--- ', '3' => '...-- ', '4' => '....- ',
    '5' => '..... ', '6' => '-.... ', '7' => '--... ', '8' => '---.. ', '9' => '----. '
];
$learnable_chars = array_keys($morse_code_map);

// --- API Actions Router ---
$action = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {
    case 'get_new_char':
        $char_to_learn = $learnable_chars[array_rand($learnable_chars)];
        echo json_encode(['success' => true, 'char' => $char_to_learn, 'morse' => trim($morse_code_map[$char_to_learn])]);
        break;

    case 'check_morse':
        $submitted_char = strtoupper(trim($_POST['char'] ?? ''));
        $user_morse_input = trim($_POST['morse_input'] ?? '');

        if (empty($submitted_char) || !isset($morse_code_map[$submitted_char])) {
            echo json_encode(['success' => false, 'message' => 'Invalid character provided.']);
            break;
        }

        $correct_morse = trim($morse_code_map[$submitted_char]);
        $is_correct = ($user_morse_input === $correct_morse);

        if ($is_correct) {
            try {
                $stmt_check = $pdo->prepare("SELECT level FROM progress WHERE user_id = ? AND morse_char = ?");
                $stmt_check->execute([$user_id, $submitted_char]);
                $progress = $stmt_check->fetch();

                if ($progress) {
                    $new_level = min($progress['level'] + 1, 5); 
                    $stmt_update = $pdo->prepare("UPDATE progress SET level = ?, last_practiced = CURRENT_TIMESTAMP WHERE user_id = ? AND morse_char = ?");
                    $stmt_update->execute([$new_level, $user_id, $submitted_char]);
                } else {
                    $stmt_insert = $pdo->prepare("INSERT INTO progress (user_id, morse_char, level) VALUES (?, ?, 1)");
                    $stmt_insert->execute([$user_id, $submitted_char]);
                }
                 echo json_encode(['success' => true, 'correct' => true, 'message' => 'Correct! '.$submitted_char . ' is ' . $correct_morse, 'char' => $submitted_char]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Error saving progress: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => true, 'correct' => false, 'message' => 'Incorrect. Try again! The correct Morse for ' . $submitted_char . ' is ' . $correct_morse, 'char' => $submitted_char]);
        }
        break;

    case 'get_progress':
        try {
            $stmt = $pdo->prepare("SELECT morse_char, level FROM progress WHERE user_id = ? ORDER BY morse_char");
            $stmt->execute([$user_id]);
            $user_progress = $stmt->fetchAll();
            echo json_encode(['success' => true, 'progress' => $user_progress]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error fetching progress: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
        break;
}

?> 