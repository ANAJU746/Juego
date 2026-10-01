<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esperando al Maestro...</title>
   
    <meta http-equiv="refresh" content="2">
    <style>
        body { background: #1e88e5; color: white; font-family: 'Nunito', sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; text-align: center; }
        .spinner { width: 80px; height: 80px; border: 8px solid rgba(255,255,255,0.3); border-top: 8px solid white; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 30px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        h1 { font-size: 2.5rem; margin: 0; }
        p { font-size: 1.2rem; opacity: 0.8; }
    </style>
</head>
<body>
    <div class="spinner"></div>
    <h1>¡Estás dentro!</h1>
    <p>Mira la pantalla del profesor. El juego comenzará en breve...</p>
    <!-- MÚSICA DE FONDO -->
    <audio id="musica-juego" loop>
        <!-- Fíjate que usamos el nombre exacto de tu captura: muiscafondo.mp3 -->
        <source src="musica/muiscafondo.mp3" type="audio/mpeg">
    </audio>

    <!-- SCRIPT PARA CONTROLAR LA MÚSICA -->
    <script>
        const audioFondo = document.getElementById('musica-juego');
        audioFondo.volume = 0.3; // Volumen al 30% para que no tape los demás sonidos

        // Intentamos reproducir automáticamente
        audioFondo.play().catch(() => {
            // Si el navegador bloquea el autoplay, esperamos a que el usuario haga clic en cualquier parte
            document.body.addEventListener('click', () => {
                if (audioFondo.paused) {
                    audioFondo.play();
                }
            }, { once: true }); // El "once: true" hace que este evento solo se dispare la primera vez
        });
    </script>
</body>
</html>