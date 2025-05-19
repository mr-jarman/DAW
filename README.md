Project Report: Mini Games Hub

by Students of University Batna2

. Developers Information

_____________________________________________
Leader:                                      

    Name: Direche Abderrahmane

    Group: L2 04

    Email: abdou6diabdou@gmail.com

    GitHub: github.com/mr-jarman
_____________________________________________
M:                                      

    Name: Oussama Slimani

    Group: L2 04
_____________________________________________
M:                                      

    Name: Boukhalfa yasser khalil arrahmane

    Group: L2 03
_____________________________________________
M:                                      

    Name: Châabna Mohammed moncef

    Group: L2 03
_____________________________________________

To: kamel.barka@univ-batna2.dz



1. Project Overview

Project Name: Mini Games Hub

Objective:

To develop a web-based platform that provides a collection of educational and entertaining mini-games. The system includes user authentication, progress tracking, and high score management for each game.

Core Technologies Used:

Frontend: HTML5, CSS3, JavaScript

Backend: PHP

Database: SQLite (via PDO)

Key Web APIs: Web Audio API (for sound generation)

2. System Architecture

The platform uses a client-server model with distinct frontend and backend components.

Frontend (Client-Side)

index.php (Main Portal)

- Modern, responsive grid layout for game cards
- Login/Registration system
- Dynamic content based on authentication status
- Attractive UI with hover effects and animations

Game Interfaces:
1. Morse Code Learner (morse_game.php)
   - Interactive Morse code learning
   - Audio feedback using Web Audio API
   - Progress tracking per character

2. Word Scramble (word_scramble.php)
   - Time-based word unscrambling
   - Score tracking
   - Multiple difficulty levels

3. Memory Match (memory_match.php)
   - Card matching game
   - Move counter
   - Timer functionality
   - Progressive difficulty

4. Typing Race (typing_race.php)
   - WPM (Words Per Minute) calculation
   - Accuracy tracking
   - Real-time feedback
   - Multiple text samples

5. Math Challenge (math_challenge.php)
   - Mental math exercises
   - Progressive difficulty levels
   - Score tracking
   - Time-based challenges

6. Color Match (color_match.php)
   - Color perception testing
   - Progressive difficulty
   - Score tracking
   - Time-based challenges

Backend (Server-Side in PHP)

Authentication System:
- db_connect.php: SQLite database connection and table initialization
- register.php: User registration with password hashing
- login.php: Secure login system
- logout.php: Session management

Game Management:
- game_scores.php: Score tracking and high score management
- save_score.php: Score saving functionality
- Session-based authentication for all games

Database (SQLite - morse_learner.sqlite)

Tables:
1. users
   - id, username, password_hash, created_at

2. progress
   - id, user_id, morse_char, level, last_practiced
   - UNIQUE(user_id, morse_char)

3. game_scores
   - id, user_id, game_name, score, level, created_at
   - UNIQUE(user_id, game_name)

3. Development Process & Methodology

Initial Setup:

- Created basic HTML structure and responsive CSS
- Implemented user authentication system
- Set up SQLite database with necessary tables

Game Development:

- Implemented each game with unique mechanics
- Added score tracking and high score system
- Integrated progressive difficulty levels
- Added time-based challenges where appropriate

UI/UX Design:

- Created consistent, modern design across all games
- Implemented responsive layouts
- Added visual feedback and animations
- Ensured accessibility and user-friendly interfaces

Testing & Refinement:

- Tested all games for functionality
- Verified score tracking system
- Ensured responsive design across devices
- Implemented error handling and user feedback

4. Key Features Implemented

✅ Secure Authentication System
- User registration and login
- Password hashing
- Session management

✅ Game Collection
- Six unique educational games
- Progressive difficulty levels
- Time-based challenges

✅ Score System
- High score tracking per game
- Level progression
- Performance metrics (WPM, accuracy, etc.)

✅ User Interface
- Modern, responsive design
- Interactive elements
- Visual feedback
- Consistent styling across games

✅ Security Features
- SQL injection prevention
- XSS protection
- Secure password storage
- Session management

5. Future Enhancements

Potential improvements for future development:
- Additional games
- Social features (leaderboards, friend challenges)
- Achievement system
- More detailed progress tracking
- Mobile app version

This project demonstrates the integration of modern web technologies to create an engaging educational gaming platform. It combines secure backend logic, responsive frontend design, and interactive game mechanics to provide a comprehensive learning experience.
