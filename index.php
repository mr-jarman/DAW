<?php 
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Games Hub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        .game-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .game-card:hover {
            transform: translateY(-3px);
        }
        .game-card img {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }
        .game-card h3 {
            color: #007bff;
            margin: 8px 0;
            font-size: 1.1em;
        }
        .game-card p {
            color: #666;
            font-size: 0.85em;
            margin: 0;
        }
        .login-section {
            text-align: right;
            margin-bottom: 15px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            margin: 10px 0;
            font-size: 2em;
        }
        p {
            margin: 10px 0;
        }
        .login-prompt {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-top: 40px;
        }
        .login-prompt h2 {
            color: #007bff;
            margin-bottom: 20px;
        }
        .login-prompt p {
            color: #666;
            margin-bottom: 20px;
        }
        .login-prompt .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        .login-prompt .buttons a {
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .login-prompt .buttons .login-btn {
            background: #007bff;
            color: white;
        }
        .login-prompt .buttons .register-btn {
            background: #e9ecef;
            color: #007bff;
        }
        .login-prompt .buttons a:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-section">
            <?php if ($isLoggedIn): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a> | <a href="register.php">Register</a>
            <?php endif; ?>
        </div>

        <h1>Mini Games Hub</h1>
        <p>Welcome to our collection of fun and educational mini games!</p>

        <?php if ($isLoggedIn): ?>
            <div class="games-grid">
                <div class="game-card" onclick="window.location.href='morse_game.php'">
                    <img src="morse_icon.png" alt="Morse Code Game" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwN2JmZiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxwYXRoIGQ9Ik0xMiAyMHYtMTZtLTggOGgxNm0tOC04djE2Ii8+PC9zdmc+'" />
                    <h3>Morse Code Learner</h3>
                    <p>Learn and practice Morse code in a fun, interactive way!</p>
                </div>
                <div class="game-card" onclick="window.location.href='word_scramble.php'">
                    <img src="word_scramble_icon.png" alt="Word Scramble Game" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwN2JmZiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxwYXRoIGQ9Ik0xMiAyMHYtMTZtLTggOGgxNm0tOC04djE2Ii8+PC9zdmc+'" />
                    <h3>Word Scramble</h3>
                    <p>Test your vocabulary by unscrambling words against the clock!</p>
                </div>
                <div class="game-card" onclick="window.location.href='memory_match.php'">
                    <img src="memory_icon.png" alt="Memory Match Game" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwN2JmZiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxwYXRoIGQ9Ik0xMiAyMHYtMTZtLTggOGgxNm0tOC04djE2Ii8+PC9zdmc+'" />
                    <h3>Memory Match</h3>
                    <p>Test your memory by matching pairs of cards!</p>
                </div>
                <div class="game-card" onclick="window.location.href='typing_race.php'">
                    <img src="typing_icon.png" alt="Typing Race Game" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwN2JmZiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxwYXRoIGQ9Ik0xMiAyMHYtMTZtLTggOGgxNm0tOC04djE2Ii8+PC9zdmc+'" />
                    <h3>Typing Race</h3>
                    <p>Improve your typing speed and accuracy!</p>
                </div>
                <div class="game-card" onclick="window.location.href='math_challenge.php'">
                    <img src="math_icon.png" alt="Math Challenge Game" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwN2JmZiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxwYXRoIGQ9Ik0xMiAyMHYtMTZtLTggOGgxNm0tOC04djE2Ii8+PC9zdmc+'" />
                    <h3>Math Challenge</h3>
                    <p>Test your mental math skills with timed challenges!</p>
                </div>
                <div class="game-card" onclick="window.location.href='color_match.php'">
                    <img src="color_icon.png" alt="Color Match Game" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwN2JmZiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxwYXRoIGQ9Ik0xMiAyMHYtMTZtLTggOGgxNm0tOC04djE2Ii8+PC9zdmc+'" />
                    <h3>Color Match</h3>
                    <p>Test your color perception and matching skills!</p>
                </div>
            </div>
        <?php else: ?>
            <div class="login-prompt">
                <h2>Login Required</h2>
                <p>Please log in or create an account to access our collection of games.</p>
                <div class="buttons">
                    <a href="login.php" class="login-btn">Login</a>
                    <a href="register.php" class="register-btn">Register</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 