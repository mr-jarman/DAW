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
    <title>Word Scramble - Mini Games Hub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .game-container {
            text-align: center;
            background-color: #f0f9ff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
        }
        .scrambled-word {
            font-size: 3em;
            font-weight: bold;
            color: #ff6f61;
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            letter-spacing: 5px;
        }
        .timer {
            font-size: 1.5em;
            color: #007bff;
            margin: 10px 0;
        }
        .score {
            font-size: 1.2em;
            color: #28a745;
            margin: 10px 0;
        }
        .input-area {
            margin: 20px 0;
        }
        .input-area input {
            font-size: 1.2em;
            padding: 10px;
            width: 200px;
            text-align: center;
            border: 2px solid #007bff;
            border-radius: 10px;
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
        .feedback.correct {
            background-color: #d4edda;
            color: #155724;
        }
        .feedback.incorrect {
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
            <h2>Word Scramble</h2>
            <div class="timer">Time: <span id="time">60</span>s</div>
            <div class="score">Score: <span id="score">0</span></div>
            <div class="scrambled-word" id="scrambled-word"></div>
            <div class="input-area">
                <input type="text" id="answer" placeholder="Type your answer" autocomplete="off">
            </div>
            <div class="controls">
                <button id="check-btn">Check Answer</button>
                <button id="skip-btn">Skip Word</button>
            </div>
            <div class="feedback" id="feedback"></div>
        </div>
    </div>

    <script>
        const words = [
            'JAVASCRIPT', 'PYTHON', 'PROGRAMMING', 'COMPUTER', 'DEVELOPER',
            'INTERNET', 'WEBSITE', 'DATABASE', 'ALGORITHM', 'FUNCTION',
            'VARIABLE', 'KEYBOARD', 'MONITOR', 'NETWORK', 'SOFTWARE',
            'HARDWARE', 'SYSTEM', 'SERVER', 'CLIENT', 'BROWSER'
        ];

        let currentWord = '';
        let scrambledWord = '';
        let score = 0;
        let timeLeft = 60;
        let timer;
        let isGameActive = false;

        const scrambledWordDisplay = document.getElementById('scrambled-word');
        const answerInput = document.getElementById('answer');
        const checkBtn = document.getElementById('check-btn');
        const skipBtn = document.getElementById('skip-btn');
        const feedbackDisplay = document.getElementById('feedback');
        const scoreDisplay = document.getElementById('score');
        const timeDisplay = document.getElementById('time');

        function scrambleWord(word) {
            const array = word.split('');
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array.join('');
        }

        function getNewWord() {
            currentWord = words[Math.floor(Math.random() * words.length)];
            scrambledWord = scrambleWord(currentWord);
            while (scrambledWord === currentWord) {
                scrambledWord = scrambleWord(currentWord);
            }
            scrambledWordDisplay.textContent = scrambledWord;
            answerInput.value = '';
            feedbackDisplay.textContent = '';
            feedbackDisplay.className = 'feedback';
        }

        function startTimer() {
            timer = setInterval(() => {
                timeLeft--;
                timeDisplay.textContent = timeLeft;
                if (timeLeft <= 0) {
                    endGame();
                }
            }, 1000);
        }

        function endGame() {
            clearInterval(timer);
            isGameActive = false;
            feedbackDisplay.textContent = `Game Over! Final Score: ${score}`;
            feedbackDisplay.className = 'feedback';
            checkBtn.disabled = true;
            skipBtn.disabled = true;
            answerInput.disabled = true;
        }

        function checkAnswer() {
            if (!isGameActive) return;
            
            const userAnswer = answerInput.value.toUpperCase();
            if (userAnswer === currentWord) {
                score += 10;
                scoreDisplay.textContent = score;
                feedbackDisplay.textContent = 'Correct! +10 points';
                feedbackDisplay.className = 'feedback correct';
                getNewWord();
            } else {
                feedbackDisplay.textContent = 'Try again!';
                feedbackDisplay.className = 'feedback incorrect';
            }
        }

        function skipWord() {
            if (!isGameActive) return;
            getNewWord();
        }

        function startGame() {
            score = 0;
            timeLeft = 60;
            isGameActive = true;
            scoreDisplay.textContent = score;
            timeDisplay.textContent = timeLeft;
            checkBtn.disabled = false;
            skipBtn.disabled = false;
            answerInput.disabled = false;
            getNewWord();
            startTimer();
        }

        checkBtn.addEventListener('click', checkAnswer);
        skipBtn.addEventListener('click', skipWord);
        answerInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                checkAnswer();
            }
        });

        // Start the game when the page loads
        startGame();
    </script>
</body>
</html> 