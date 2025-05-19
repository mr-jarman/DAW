<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    $_SESSION['login_error_message'] = "Please log in to access the games.";
    header("Location: index.php");
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Morse Code Game - Mini Games Hub</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="morse_style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <div class="header-left">
                <a href="index.php" class="back-btn">← Back to Games</a>
            </div>
            <div class="header-right">
                <span class="welcome-text">Welcome, <?php echo $username; ?>!</span>
                <a href="logout.php">Logout</a>
            </div>
        </header>

        <main>
            <div id="morse-game-area">
                <h2>Morse Code Learner</h2>
                <div id="char-display-area">
                    <p>Character to translate:</p>
                    <div id="current-char">A</div>
                    <button id="hear-morse-btn" title="Play Morse sound for this character">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-volume-2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                        Hear Morse
                    </button>
                </div>
                <div id="morse-input-area">
                    <p>Your Morse Input:</p>
                    <div id="user-morse-input" contenteditable="false"></div>
                    <button id="dot-btn">.</button>
                    <button id="dash-btn">-</button>
                    <button id="space-btn">Check</button>
                    <button id="delete-btn">Delete</button>
                    <button id="next-char-btn">Next Character</button>
                </div>
                <div id="feedback-area">
                    <p id="feedback-message"></p>
                </div>
                <div id="progress-area">
                    <h3>Your Progress</h3>
                    <ul id="progress-list">
                    </ul>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> Mini Games Hub</p>
        </footer>
    </div>

    <script src="morse_script.js"></script>
</body>
</html> 