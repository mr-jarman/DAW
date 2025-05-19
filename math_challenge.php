<?php
session_start();
require_once 'game_scores.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    $_SESSION['login_error_message'] = "Please log in to access the games.";
    header("Location: index.php");
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
$highScore = getHighScore($_SESSION['user_id'], 'math_challenge');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Challenge - Mini Games Hub</title>
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
        .problem-display {
            font-size: 2.5em;
            font-weight: bold;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 400px;
        }
        .answer-input {
            width: 200px;
            padding: 15px;
            font-size: 1.5em;
            text-align: center;
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
            <h2>Math Challenge</h2>
            <div class="game-info">
                <div class="score">Score: <span id="score">0</span></div>
                <div class="level">Level: <span id="level">1</span></div>
                <div class="timer">Time: <span id="time">30</span>s</div>
                <?php if ($highScore): ?>
                <div class="high-score">High Score: <?php echo $highScore['score']; ?> (Level <?php echo $highScore['level']; ?>)</div>
                <?php endif; ?>
            </div>
            <div class="problem-display" id="problem"></div>
            <input type="number" class="answer-input" id="answer" placeholder="?" disabled>
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
        let currentProblem = {};
        let correctAnswer = 0;

        const problemDisplay = document.getElementById('problem');
        const answerInput = document.getElementById('answer');
        const startBtn = document.getElementById('start-btn');
        const restartBtn = document.getElementById('restart-btn');
        const scoreDisplay = document.getElementById('score');
        const levelDisplay = document.getElementById('level');
        const timeDisplay = document.getElementById('time');
        const feedbackDisplay = document.getElementById('feedback');

        function generateProblem() {
            const operations = ['+', '-', '*'];
            const operation = operations[Math.floor(Math.random() * operations.length)];
            let num1, num2;

            switch(level) {
                case 1:
                    num1 = Math.floor(Math.random() * 10) + 1;
                    num2 = Math.floor(Math.random() * 10) + 1;
                    break;
                case 2:
                    num1 = Math.floor(Math.random() * 20) + 1;
                    num2 = Math.floor(Math.random() * 20) + 1;
                    break;
                case 3:
                    num1 = Math.floor(Math.random() * 50) + 1;
                    num2 = Math.floor(Math.random() * 50) + 1;
                    break;
                default:
                    num1 = Math.floor(Math.random() * 100) + 1;
                    num2 = Math.floor(Math.random() * 100) + 1;
            }

            switch(operation) {
                case '+':
                    correctAnswer = num1 + num2;
                    break;
                case '-':
                    correctAnswer = num1 - num2;
                    break;
                case '*':
                    correctAnswer = num1 * num2;
                    break;
            }

            return `${num1} ${operation} ${num2}`;
        }

        function startGame() {
            score = 0;
            level = 1;
            timeLeft = 30;
            isGameActive = true;
            
            scoreDisplay.textContent = score;
            levelDisplay.textContent = level;
            timeDisplay.textContent = timeLeft;
            
            answerInput.value = '';
            answerInput.disabled = false;
            answerInput.focus();
            
            startBtn.style.display = 'none';
            restartBtn.style.display = 'inline-block';
            
            generateNewProblem();
            timer = setInterval(updateTimer, 1000);
        }

        function generateNewProblem() {
            const problem = generateProblem();
            problemDisplay.textContent = problem;
            answerInput.value = '';
            answerInput.focus();
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
            answerInput.disabled = true;
            
            // Save score to server
            fetch('save_score.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    game: 'math_challenge',
                    score: score,
                    level: level
                })
            });
            
            feedbackDisplay.textContent = `Game Over! Final Score: ${score} (Level ${level})`;
            feedbackDisplay.className = 'feedback success';
        }

        function checkAnswer() {
            const userAnswer = parseInt(answerInput.value);
            
            if (userAnswer === correctAnswer) {
                score += level;
                feedbackDisplay.textContent = 'Correct!';
                feedbackDisplay.className = 'feedback success';
                
                if (score >= level * 10) {
                    level++;
                    levelDisplay.textContent = level;
                }
                
                generateNewProblem();
            } else {
                feedbackDisplay.textContent = 'Incorrect! Try again.';
                feedbackDisplay.className = 'feedback error';
            }
            
            scoreDisplay.textContent = score;
        }

        answerInput.addEventListener('keypress', (e) => {
            if (!isGameActive) return;
            
            if (e.key === 'Enter') {
                checkAnswer();
            }
        });

        startBtn.addEventListener('click', startGame);
        restartBtn.addEventListener('click', startGame);
    </script>
</body>
</html> 