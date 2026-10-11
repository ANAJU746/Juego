(() => {
    const contextClass = window.AudioContext || window.webkitAudioContext;
    let audioContext = null;

    function playBloop() {
        if (!contextClass) {
            console.warn('Web Audio API no está disponible; no se reproducirá el sonido del cursor.');
            return;
        }

        try {
            audioContext = audioContext || new contextClass();
            if (audioContext.state === 'suspended') {
                audioContext.resume().catch(error => {
                    console.error('No se pudo reanudar el audio del cursor:', error);
                });
            }

            const oscillator = audioContext.createOscillator();
            const gain = audioContext.createGain();
            const startTime = audioContext.currentTime;

            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(620, startTime);
            oscillator.frequency.exponentialRampToValueAtTime(260, startTime + 0.12);
            gain.gain.setValueAtTime(0.16, startTime);
            gain.gain.exponentialRampToValueAtTime(0.01, startTime + 0.12);
            oscillator.connect(gain);
            gain.connect(audioContext.destination);
            oscillator.start(startTime);
            oscillator.stop(startTime + 0.12);
        } catch (error) {
            console.error('No se pudo reproducir el sonido del cursor:', error);
        }
    }

    function createClickSparks(event) {
        if (event.detail === 0) return;

        const symbols = ['✨', '⭐', '🫧', '💫'];
        const particleCount = 5;

        for (let index = 0; index < particleCount; index++) {
            const particle = document.createElement('span');
            const angle = (Math.PI * 2 * index) / particleCount;
            const distance = 28 + Math.random() * 36;

            particle.className = 'chispa-cursor';
            particle.textContent = symbols[Math.floor(Math.random() * symbols.length)];
            particle.style.left = `${event.clientX}px`;
            particle.style.top = `${event.clientY}px`;
            particle.style.setProperty('--tx', `${Math.cos(angle) * distance}px`);
            particle.style.setProperty('--ty', `${Math.sin(angle) * distance}px`);
            particle.style.setProperty('--rot', `${Math.random() * 180 - 90}deg`);
            document.body.appendChild(particle);
            particle.addEventListener('animationend', () => particle.remove(), { once: true });
        }

        playBloop();
    }

    document.addEventListener('click', createClickSparks);
})();
