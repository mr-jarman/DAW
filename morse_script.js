document.addEventListener('DOMContentLoaded', function() {
    const currentCharDisplay = document.getElementById('current-char');
    const userMorseInputDisplay = document.getElementById('user-morse-input');
    const dotBtn = document.getElementById('dot-btn');
    const dashBtn = document.getElementById('dash-btn');
    const spaceBtn = document.getElementById('space-btn'); // For submitting the current letter's morse
    const nextCharBtn = document.getElementById('next-char-btn');
    const hearMorseBtn = document.getElementById('hear-morse-btn'); // New button
    const feedbackMessageDisplay = document.getElementById('feedback-message');
    const progressListDisplay = document.getElementById('progress-list');

    let aktuellenChar = '';
    let aktuellenMorse = '';
    let audioContext = null;
    const morseTiming = {
        dot: 100,   
        dash: 300,  
        symbolSpace: 100,
        letterSpace: 300,
        wordSpace: 700   
    };
    const morseFrequency = 600; 

    function initAudioContext() {
        if (!audioContext) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
    }

    function playTone(duration, frequency = morseFrequency) {
        if (!audioContext) return;
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(frequency, audioContext.currentTime);
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.00001, audioContext.currentTime + duration / 1000);

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + duration / 1000);
    }

    async function playMorseSequence(morseCodeString) {
        initAudioContext();
        if (!audioContext) {
            feedbackMessageDisplay.textContent = 'AudioContext not supported or enabled.';
            feedbackMessageDisplay.className = 'info';
            return;
        }
        
        if (audioContext.state === 'suspended') {
            await audioContext.resume();
        }

        for (const symbol of morseCodeString) {
            if (symbol === '.') {
                playTone(morseTiming.dot);
                await new Promise(resolve => setTimeout(resolve, morseTiming.dot + morseTiming.symbolSpace));
            } else if (symbol === '-') {
                playTone(morseTiming.dash);
                await new Promise(resolve => setTimeout(resolve, morseTiming.dash + morseTiming.symbolSpace));
            } else if (symbol === ' ') {
               // multi letters system
            }
        }
    }

    function fetchNewChar() {
        feedbackMessageDisplay.textContent = '';
        feedbackMessageDisplay.className = '';
        userMorseInputDisplay.textContent = '';
        fetch('game_api.php?action=get_new_char')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    aktuellenChar = data.char;
                    aktuellenMorse = data.morse; 
                    currentCharDisplay.textContent = aktuellenChar;
                } else {
                    feedbackMessageDisplay.textContent = data.message || 'Error fetching new character.';
                    feedbackMessageDisplay.className = 'incorrect';
                }
            })
            .catch(error => {
                console.error('Error fetching new char:', error);
                feedbackMessageDisplay.textContent = 'Network error. Please try again.';
                feedbackMessageDisplay.className = 'incorrect';
            });
    }

    function checkMorse() {
        const userAttempt = userMorseInputDisplay.textContent.trim();
        if (!aktuellenChar || userAttempt === '') {
            feedbackMessageDisplay.textContent = 'Please input Morse code first or get a new character.';
            feedbackMessageDisplay.className = 'info';
            return;
        }

        const formData = new FormData();
        formData.append('action', 'check_morse');
        formData.append('char', aktuellenChar);
        formData.append('morse_input', userAttempt);

        fetch('game_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                feedbackMessageDisplay.textContent = data.message;
                if (data.correct) {
                    feedbackMessageDisplay.className = 'correct';
                    fetchUserProgress();
                    playMorseSequence(aktuellenMorse); 
                } else {
                    feedbackMessageDisplay.className = 'incorrect';
                }
            } else {
                feedbackMessageDisplay.textContent = data.message || 'Error checking Morse code.';
                feedbackMessageDisplay.className = 'incorrect';
            }
        })
        .catch(error => {
            console.error('Error checking morse:', error);
            feedbackMessageDisplay.textContent = 'Network error. Please try again.';
            feedbackMessageDisplay.className = 'incorrect';
        });
    }

    function fetchUserProgress() {
        fetch('game_api.php?action=get_progress')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.progress) {
                    progressListDisplay.innerHTML = ''; 
                    if (data.progress.length === 0) {
                        progressListDisplay.innerHTML = '<li>No progress yet. Start learning!</li>';
                    }
                    data.progress.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = `Character: ${item.morse_char}, Level: ${item.level}`;
                        progressListDisplay.appendChild(li);
                    });
                } else {
                    
                    console.warn(data.message || 'Error fetching progress.');
                }
            })
            .catch(error => {
                console.error('Error fetching progress:', error);
            });
    }

    
    if(dotBtn) dotBtn.addEventListener('click', () => {
        initAudioContext();
        userMorseInputDisplay.textContent += '.';
        playTone(morseTiming.dot);
    });

    if(dashBtn) dashBtn.addEventListener('click', () => {
        initAudioContext();
        userMorseInputDisplay.textContent += '-';
        playTone(morseTiming.dash);
    });

    
    if(spaceBtn) spaceBtn.addEventListener('click', () => {
        checkMorse();
    });

    const deleteBtn = document.getElementById('delete-btn');
    if(deleteBtn) deleteBtn.addEventListener('click', () => {
        userMorseInputDisplay.textContent = '';
    });

    if(nextCharBtn) nextCharBtn.addEventListener('click', () => {
        fetchNewChar();
    });

    if(hearMorseBtn) hearMorseBtn.addEventListener('click', () => {
        if (aktuellenMorse) {
            playMorseSequence(aktuellenMorse);
        } else {
            feedbackMessageDisplay.textContent = 'No character loaded to play Morse for.';
            feedbackMessageDisplay.className = 'info';
        }
    });

    
    fetchNewChar();
    fetchUserProgress();
    
    document.body.addEventListener('click', initAudioContext, { once: true });
}); 