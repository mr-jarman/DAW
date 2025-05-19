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
    <title>Memory Match - Mini Games Hub</title>
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
        .moves, .timer {
            font-size: 1.2em;
            color: #007bff;
            padding: 10px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .memory-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px auto;
            max-width: 600px;
        }
        .card {
            aspect-ratio: 1;
            background: #007bff;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s ease;
            transform-style: preserve-3d;
            position: relative;
        }
        .card.flipped {
            transform: rotateY(180deg);
        }
        .card-front, .card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 2em;
            font-weight: bold;
        }
        .card-front {
            background: #007bff;
            color: white;
        }
        .card-back {
            background: white;
            transform: rotateY(180deg);
            border: 2px solid #007bff;
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
            <h2>Memory Match</h2>
            <div class="game-info">
                <div class="moves">Moves: <span id="moves">0</span></div>
                <div class="timer">Time: <span id="time">0</span>s</div>
            </div>
            <div class="memory-grid" id="memory-grid"></div>
            <div class="controls">
                <button id="restart-btn">Restart Game</button>
            </div>
            <div class="feedback" id="feedback"></div>
        </div>
    </div>

    <script>
        const emojis = ['🎮', '🎲', '🎯', '🎨', '🎭', '🎪', '🎫', '🎬'];
        let cards = [...emojis, ...emojis];
        let moves = 0;
        let time = 0;
        let timer;
        let flippedCards = [];
        let matchedPairs = 0;
        let isLocked = false;

        const memoryGrid = document.getElementById('memory-grid');
        const movesDisplay = document.getElementById('moves');
        const timeDisplay = document.getElementById('time');
        const restartBtn = document.getElementById('restart-btn');
        const feedbackDisplay = document.getElementById('feedback');

        function shuffleCards() {
            for (let i = cards.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [cards[i], cards[j]] = [cards[j], cards[i]];
            }
        }

        function createCard(emoji, index) {
            const card = document.createElement('div');
            card.className = 'card';
            card.innerHTML = `
                <div class="card-front">?</div>
                <div class="card-back">${emoji}</div>
            `;
            card.dataset.index = index;
            card.addEventListener('click', flipCard);
            return card;
        }

        function startTimer() {
            timer = setInterval(() => {
                time++;
                timeDisplay.textContent = time;
            }, 1000);
        }

        function stopTimer() {
            clearInterval(timer);
        }

        function flipCard() {
            if (isLocked || this.classList.contains('flipped') || flippedCards.length >= 2) return;

            this.classList.add('flipped');
            flippedCards.push(this);

            if (flippedCards.length === 2) {
                moves++;
                movesDisplay.textContent = moves;
                checkMatch();
            }
        }

        function checkMatch() {
            const [card1, card2] = flippedCards;
            const match = card1.querySelector('.card-back').textContent === card2.querySelector('.card-back').textContent;

            if (match) {
                matchedPairs++;
                if (matchedPairs === emojis.length) {
                    endGame();
                }
            } else {
                isLocked = true;
                setTimeout(() => {
                    card1.classList.remove('flipped');
                    card2.classList.remove('flipped');
                    isLocked = false;
                }, 1000);
            }
            flippedCards = [];
        }

        function endGame() {
            stopTimer();
            feedbackDisplay.textContent = `Congratulations! You won in ${moves} moves and ${time} seconds!`;
            feedbackDisplay.className = 'feedback success';
        }

        function startGame() {
            // Reset game state
            moves = 0;
            time = 0;
            matchedPairs = 0;
            flippedCards = [];
            isLocked = false;
            movesDisplay.textContent = moves;
            timeDisplay.textContent = time;
            feedbackDisplay.textContent = '';
            feedbackDisplay.className = 'feedback';

            // Clear and shuffle cards
            memoryGrid.innerHTML = '';
            shuffleCards();
            cards.forEach((emoji, index) => {
                memoryGrid.appendChild(createCard(emoji, index));
            });

            // Start timer
            stopTimer();
            startTimer();
        }

        restartBtn.addEventListener('click', startGame);

        // Start the game when the page loads
        startGame();
    </script>
</body>
</html> 