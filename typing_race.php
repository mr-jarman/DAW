<?php
session_start();
require_once 'game_scores.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    $_SESSION['login_error_message'] = "Please log in to access the games.";
    header("Location: index.php");
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
$highScore = getHighScore($_SESSION['user_id'], 'typing_race');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Race - Mini Games Hub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .game-container {
            text-align: center;
            background-color: #f0f9ff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
        }
        .game-info {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .wpm, .accuracy, .timer {
            font-size: 1.2em;
            color: #007bff;
            padding: 10px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .text-display {
            font-size: 1.5em;
            line-height: 1.6;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 800px;
            min-height: 150px;
        }
        .text-input {
            width: 100%;
            max-width: 800px;
            padding: 15px;
            font-size: 1.2em;
            border: 2px solid #007bff;
            border-radius: 10px;
            margin: 20px auto;
            display: block;
        }
        .controls {
            margin-top: 20px;
        }
        .controls button {
            margin: 5px;
            padding: 10px 20px;
            font-size: 1.1em;
        }
        .feedback {
            margin: 15px 0;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
        }
        .feedback.success {
            background-color: #d4edda;
            color: #155724;
        }
        .current-word {
            background-color: #e3f2fd;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .correct {
            color: #28a745;
        }
        .incorrect {
            color: #dc3545;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-left">
                <a href="index.php" class="back-btn">← Back to Games</a>
            </div>
            <div class="header-right">
                <span class="welcome-text">Welcome, <?php echo $username; ?>!</span>
                <a href="logout.php">Logout</a>
            </div>
        </header>

        <div class="game-container">
            <h2>Typing Race</h2>
            <div class="game-info">
                <div class="wpm">WPM: <span id="wpm">0</span></div>
                <div class="accuracy">Accuracy: <span id="accuracy">100</span>%</div>
                <div class="timer">Time: <span id="time">60</span>s</div>
                <?php if ($highScore): ?>
                <div class="high-score">High Score: <?php echo $highScore['score']; ?> WPM</div>
                <?php endif; ?>
            </div>
            <div class="text-display" id="text-display"></div>
            <input type="text" class="text-input" id="text-input" placeholder="Start typing..." disabled>
            <div class="controls">
                <button id="start-btn">Start Game</button>
                <button id="restart-btn" style="display: none;">Restart Game</button>
            </div>
            <div class="feedback" id="feedback"></div>
        </div>
    </div>

    <script>
        const texts = [
            "The quick brown fox jumps over the lazy dog. Pack my box with five dozen liquor jugs. How vexingly quick daft zebras jump!",
            "Programming is the process of creating a set of instructions that tell a computer how to perform a task. Programming can be done using a variety of computer programming languages.",
            "The Internet is a global network of billions of computers and other electronic devices. With the Internet, it's possible to access almost any information, communicate with anyone else in the world, and do much more.",
            "Artificial intelligence is the simulation of human intelligence processes by machines, especially computer systems. These processes include learning, reasoning, and self-correction.",
            "Cloud computing is the delivery of computing services—including servers, storage, databases, networking, software, analytics, and intelligence—over the Internet to offer faster innovation, flexible resources, and economies of scale."
        ];

        let currentText = '';
        let currentWord = '';
        let wordIndex = 0;
        let charIndex = 0;
        let startTime;
        let timer;
        let isGameActive = false;
        let correctChars = 0;
        let totalChars = 0;

        const textDisplay = document.getElementById('text-display');
        const textInput = document.getElementById('text-input');
        const startBtn = document.getElementById('start-btn');
        const restartBtn = document.getElementById('restart-btn');
        const wpmDisplay = document.getElementById('wpm');
        const accuracyDisplay = document.getElementById('accuracy');
        const timeDisplay = document.getElementById('time');
        const feedbackDisplay = document.getElementById('feedback');

        function startGame() {
            currentText = texts[Math.floor(Math.random() * texts.length)];
            wordIndex = 0;
            charIndex = 0;
            correctChars = 0;
            totalChars = 0;
            isGameActive = true;
            startTime = Date.now();
            
            displayText();
            textInput.value = '';
            textInput.disabled = false;
            textInput.focus();
            
            startBtn.style.display = 'none';
            restartBtn.style.display = 'inline-block';
            
            timer = setInterval(updateTimer, 1000);
            updateStats();
        }

        function displayText() {
            const words = currentText.split(' ');
            textDisplay.innerHTML = words.map((word, index) => {
                if (index === wordIndex) {
                    return `<span class="current-word">${word}</span>`;
                }
                return word;
            }).join(' ');
        }

        function updateTimer() {
            const timeLeft = Math.max(0, 60 - Math.floor((Date.now() - startTime) / 1000));
            timeDisplay.textContent = timeLeft;
            
            if (timeLeft === 0) {
                endGame();
            }
        }

        function updateStats() {
            const timeElapsed = (Date.now() - startTime) / 1000 / 60; // in minutes
            const wpm = Math.round((correctChars / 5) / timeElapsed);
            const accuracy = totalChars > 0 ? Math.round((correctChars / totalChars) * 100) : 100;
            
            wpmDisplay.textContent = wpm;
            accuracyDisplay.textContent = accuracy;
        }

        function endGame() {
            isGameActive = false;
            clearInterval(timer);
            textInput.disabled = true;
            
            const timeElapsed = (Date.now() - startTime) / 1000 / 60; // in minutes
            const wpm = Math.round((correctChars / 5) / timeElapsed);
            const accuracy = Math.round((correctChars / totalChars) * 100);
            
            // Save score to server
            fetch('save_score.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    game: 'typing_race',
                    score: wpm,
                    level: 1
                })
            });
            
            feedbackDisplay.textContent = `Game Over! Your final score: ${wpm} WPM with ${accuracy}% accuracy`;
            feedbackDisplay.className = 'feedback success';
        }

        textInput.addEventListener('input', (e) => {
            if (!isGameActive) return;
            
            const words = currentText.split(' ');
            const currentWord = words[wordIndex];
            const input = e.target.value;
            
            if (input.endsWith(' ')) {
                if (input.trim() === currentWord) {
                    correctChars += currentWord.length;
                }
                totalChars += currentWord.length;
                
                wordIndex++;
                charIndex = 0;
                e.target.value = '';
                
                if (wordIndex >= words.length) {
                    endGame();
                    return;
                }
                
                displayText();
            }
            
            updateStats();
        });

        startBtn.addEventListener('click', startGame);
        restartBtn.addEventListener('click', startGame);
    </script>
</body>
</html> 