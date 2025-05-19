<?php
session_start();
require_once 'game_scores.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    $_SESSION['login_error_message'] = "Please log in to access the games.";
    header("Location: index.php");
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
$highScore = getHighScore($_SESSION['user_id'], 'color_match');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Match - Mini Games Hub</title>
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
        .score, .level, .timer {
            font-size: 1.2em;
            color: #007bff;
            padding: 10px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .color-display {
            width: 200px;
            height: 200px;
            margin: 30px auto;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .color-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            max-width: 400px;
            margin: 20px auto;
        }
        .color-option {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s;
            border: 2px solid transparent;
        }
        .color-option:hover {
            transform: scale(1.05);
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
        .feedback.error {
            background-color: #f8d7da;
            color: #721c24;
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
            <h2>Color Match</h2>
            <div class="game-info">
                <div class="score">Score: <span id="score">0</span></div>
                <div class="level">Level: <span id="level">1</span></div>
                <div class="timer">Time: <span id="time">30</span>s</div>
                <?php if ($highScore): ?>
                <div class="high-score">High Score: <?php echo $highScore['score']; ?> (Level <?php echo $highScore['level']; ?>)</div>
                <?php endif; ?>
            </div>
            <div class="color-display" id="target-color"></div>
            <div class="color-options" id="color-options"></div>
            <div class="controls">
                <button id="start-btn">Start Game</button>
                <button id="restart-btn" style="display: none;">Restart Game</button>
            </div>
            <div class="feedback" id="feedback"></div>
        </div>
    </div>

    <script>
        let score = 0;
        let level = 1;
        let timeLeft = 30;
        let timer;
        let isGameActive = false;
        let targetColor = '';
        let colorOptions = [];

        const targetColorDisplay = document.getElementById('target-color');
        const colorOptionsContainer = document.getElementById('color-options');
        const startBtn = document.getElementById('start-btn');
        const restartBtn = document.getElementById('restart-btn');
        const scoreDisplay = document.getElementById('score');
        const levelDisplay = document.getElementById('level');
        const timeDisplay = document.getElementById('time');
        const feedbackDisplay = document.getElementById('feedback');

        function generateColor() {
            const r = Math.floor(Math.random() * 256);
            const g = Math.floor(Math.random() * 256);
            const b = Math.floor(Math.random() * 256);
            return `rgb(${r}, ${g}, ${b})`;
        }

        function generateSimilarColor(baseColor) {
            const [r, g, b] = baseColor.match(/\d+/g).map(Number);
            const variation = Math.max(5, 30 - level * 5);
            
            const newR = Math.max(0, Math.min(255, r + Math.floor(Math.random() * variation * 2) - variation));
            const newG = Math.max(0, Math.min(255, g + Math.floor(Math.random() * variation * 2) - variation));
            const newB = Math.max(0, Math.min(255, b + Math.floor(Math.random() * variation * 2) - variation));
            
            return `rgb(${newR}, ${newG}, ${newB})`;
        }

        function generateColorOptions() {
            const options = [targetColor];
            const numOptions = Math.min(9, 3 + level);
            
            while (options.length < numOptions) {
                const similarColor = generateSimilarColor(targetColor);
                if (!options.includes(similarColor)) {
                    options.push(similarColor);
                }
            }
            
            return options.sort(() => Math.random() - 0.5);
        }

        function startGame() {
            score = 0;
            level = 1;
            timeLeft = 30;
            isGameActive = true;
            
            scoreDisplay.textContent = score;
            levelDisplay.textContent = level;
            timeDisplay.textContent = timeLeft;
            
            startBtn.style.display = 'none';
            restartBtn.style.display = 'inline-block';
            
            generateNewRound();
            timer = setInterval(updateTimer, 1000);
        }

        function generateNewRound() {
            targetColor = generateColor();
            targetColorDisplay.style.backgroundColor = targetColor;
            
            colorOptions = generateColorOptions();
            colorOptionsContainer.innerHTML = '';
            
            colorOptions.forEach(color => {
                const option = document.createElement('div');
                option.className = 'color-option';
                option.style.backgroundColor = color;
                option.addEventListener('click', () => checkAnswer(color));
                colorOptionsContainer.appendChild(option);
            });
        }

        function updateTimer() {
            timeLeft--;
            timeDisplay.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                endGame();
            }
        }

        function endGame() {
            isGameActive = false;
            clearInterval(timer);
            
            // Save score to server
            fetch('save_score.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    game: 'color_match',
                    score: score,
                    level: level
                })
            });
            
            feedbackDisplay.textContent = `Game Over! Final Score: ${score} (Level ${level})`;
            feedbackDisplay.className = 'feedback success';
        }

        function checkAnswer(selectedColor) {
            if (!isGameActive) return;
            
            if (selectedColor === targetColor) {
                score += level;
                feedbackDisplay.textContent = 'Correct!';
                feedbackDisplay.className = 'feedback success';
                
                if (score >= level * 10) {
                    level++;
                    levelDisplay.textContent = level;
                }
                
                generateNewRound();
            } else {
                feedbackDisplay.textContent = 'Incorrect! Try again.';
                feedbackDisplay.className = 'feedback error';
            }
            
            scoreDisplay.textContent = score;
        }

        startBtn.addEventListener('click', startGame);
        restartBtn.addEventListener('click', startGame);
    </script>
</body>
</html> 