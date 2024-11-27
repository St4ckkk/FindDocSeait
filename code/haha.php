<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            background-color: #1a1a1a;
            color: #ff0000;
            font-family: 'Arial', sans-serif;
            margin: 0;
            overflow: hidden;
            height: 100vh;
        }
        
        .message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            text-align: center;
            z-index: 1000;
            animation: blink 1s infinite;
            background-color: rgba(26, 26, 26, 0.8);
            padding: 20px;
            border-radius: 10px;
        }
        
        .floating-media {
            position: absolute;
            pointer-events: none;
            animation: float 10s linear infinite;
        }
        
        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(100px, 100px) rotate(90deg); }
            50% { transform: translate(0, 200px) rotate(180deg); }
            75% { transform: translate(-100px, 100px) rotate(270deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        .audio-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .volume-control {
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="message"><h1>HAHAHAHAHAH<br>your IP address is blocked</div>
    
    <!-- Background Music -->
    <audio id="background-audio" loop>
        <source src="cats/meow.mp3" type="audio/mpeg">
    </audio>

    <!-- Timed Audio -->
    <audio id="timed-audio">
        <source src="cats/haha.mp3" type="audio/mpeg">
    </audio>

    <button onclick="toggleAudio()" class="audio-controls">Toggle Sound</button>

    <!-- Volume Control -->
    <label for="volume-slider" class="volume-control">Volume:</label>
    <input 
        type="range" 
        id="volume-slider" 
        class="volume-control" 
        min="0" 
        max="1" 
        step="0.1" 
        value="1" 
        oninput="adjustVolume(this.value)">
    <script>
        // Array of your image URLs
        const imageUrls = [
            'cats/cat1.gif',
            'cats/cat2.gif',
            'cats/cat3.gif',
            'cats/cat4.gif',
            'cats/cat5.gif',
            'cats/cat6.gif',
            // Add all your image URLs here
        ];

        // Function to create random floating media
        function createFloatingMedia() {
            const mediaElement = document.createElement('div');
            mediaElement.className = 'floating-media';
            
            // Random size between 50 and 150 pixels
            const size = Math.random() * 100 + 50;
            
            // Random position
            const startX = Math.random() * window.innerWidth;
            const startY = Math.random() * window.innerHeight;
            
            mediaElement.style.left = startX + 'px';
            mediaElement.style.top = startY + 'px';
            
            // Create image with random source from array
            const img = document.createElement('img');
            const randomImageUrl = imageUrls[Math.floor(Math.random() * imageUrls.length)];
            img.src = randomImageUrl;
            img.style.width = size + 'px';
            img.style.height = size + 'px';
            
            mediaElement.appendChild(img);
            document.body.appendChild(mediaElement);
            
            // Remove the element after animation
            setTimeout(() => {
                mediaElement.remove();
            }, 10000);
        }

        // Create new floating media every second
        setInterval(createFloatingMedia, 1000);

        // Audio control for background music
        function toggleAudio() {
            const audio = document.getElementById('background-audio');
            if (audio.paused) {
                audio.play();
            } else {
                audio.pause();
            }
        }

        // Play timed audio every 3 seconds
        setInterval(() => {
            const timedAudio = document.getElementById('timed-audio');
            timedAudio.currentTime = 0; // Reset to start
            timedAudio.play();
        }, 3000);

        const timedAudio = document.getElementById('timed-audio');

// Function to play the audio
function playTimedAudio() {
    timedAudio.currentTime = 0; // Reset to start
    timedAudio.play();
}

// Function to adjust volume
function adjustVolume(value) {
    timedAudio.volume = value; // Set audio volume
}
    </script>
</body>
</html>
