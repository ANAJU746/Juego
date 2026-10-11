<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Arcade de Sistemas</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&display=swap');
        
        * { box-sizing: border-box; }
        body {
            background-color: #85c1e9;
            font-family: 'Nunito', sans-serif; 
            margin: 0; 
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh; 
            overflow-x: hidden;
            isolation: isolate;
        }
        #video-fondo-global {
            position: fixed;
            inset: 0;
            z-index: -1;
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
        }

        /* --- FONDO ANIMADO DE CÓDIGO --- */
        #fondo-codigo { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; overflow: hidden; }
        .simbolo-codigo { position: absolute; font-family: monospace; font-weight: 900; animation: subir 10s linear infinite; opacity: 0.8; }
        @keyframes subir { 0% { transform: translateY(110vh) scale(0.5); opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { transform: translateY(-10vh) scale(1.5); opacity: 0; } }

        /* --- CONTENEDOR CENTRAL --- */
        .contenedor-login {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            border: 2px solid #fff;
            border-radius: 24px;
            padding: 40px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            z-index: 10;
            position: relative;
        }

        .contenedor-login h2 { color: #1e88e5; font-size: 2rem; font-weight: 900; margin-top: 0; margin-bottom: 20px; }

        .bienvenida-arcade {
            width: 100%;
            min-height: 92px;
            padding: 14px 24px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.9);
            background: linear-gradient(135deg, rgba(227, 242, 253, 0.94), rgba(243, 229, 245, 0.94));
            box-shadow: 0 8px 20px rgba(30, 136, 229, 0.16), inset 0 1px 0 rgba(255, 255, 255, 0.9);
            text-align: center;
            position: relative;
            z-index: 10;
        }
        .bienvenida-arcade h1 {
            margin: 0;
            color: #1565c0;
            font-size: 1.55rem;
            font-weight: 900;
            letter-spacing: 0.4px;
        }
        .bienvenida-arcade p {
            margin: 0;
            color: #6a1b9a;
            font-size: 1rem;
            font-weight: 800;
        }

        @media (max-width: 480px) {
            .bienvenida-arcade { min-height: 78px; padding: 12px 16px; }
            .bienvenida-arcade h1 { font-size: 1.3rem; }
        }
        
        .grupo-input { margin-bottom: 20px; text-align: left; }
        .grupo-input label { display: block; font-weight: 800; color: #6a8296; margin-bottom: 8px; font-size: 0.95rem; }
        .grupo-input input {
            width: 100%; padding: 15px; border: 2px solid #e3f2fd; border-radius: 12px; font-size: 1rem;
            font-family: 'Nunito', sans-serif; font-weight: 700; color: #2c3e50; outline: none; transition: border-color 0.3s;
            background: #f8fbff;
        }
        .grupo-input input:focus { border-color: #42a5f5; background: #fff; }

        .btn-submit {
            width: 100%; padding: 15px; background: linear-gradient(135deg, #42a5f5, #1e88e5); color: white;
            border: none; border-radius: 12px; font-weight: 900; font-size: 1.1rem; cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 5px 15px rgba(30,136,229,0.3);
            margin-top: 10px;
        }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(30,136,229,0.4); }

        .links-login { margin-top: 25px; font-weight: 700; color: #6a8296; font-size: 0.95rem; }
        .links-login a { color: #1e88e5; text-decoration: none; font-weight: 900; transition: color 0.3s; }
        .links-login a:hover { color: #1565c0; text-decoration: underline; }

        .mensaje-error { background: #ffebee; color: #d32f2f; padding: 12px; border-radius: 10px; border: 1px solid #ffcdd2; font-weight: 800; margin-bottom: 20px; font-size: 0.9rem; }
        .mensaje-exito { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 10px; border: 1px solid #c8e6c9; font-weight: 800; margin-bottom: 20px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <video autoplay loop muted playsinline id="video-fondo-global" aria-hidden="true">
        <source src="../videos/FondoAnimado3.webm" type="video/webm">
    </video>
    <div id="fondo-codigo"></div>

    <header class="bienvenida-arcade">
        <h1>Arcade Jaguar</h1>
        <p>¡Bienvenido!</p>
    </header>

    <div class="contenedor-login" style="margin: auto;">
        <h2>Iniciar Sesión 🕹️</h2>
        
        <?php if (!empty($error)): ?>
            <div class="mensaje-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($mensaje_exito)): ?>
            <div class="mensaje-exito"><?php echo htmlspecialchars($mensaje_exito); ?></div>
        <?php endif; ?>

        <!-- El formulario apunta a tu controlador en la raíz -->
        <form method="POST" action="login.php">
            <div class="grupo-input">
                <label>Correo Electrónico</label>
                <input type="email" name="correo" required placeholder="tu@correo.com">
            </div>
            
            <div class="grupo-input">
                <label>Contraseña</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn-submit">Entrar al Arcade</button>
        </form>

        <div class="links-login">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>

    <!-- VIDEO DE LA MASCOTA ESPERANDO (Lado Izquierdo) -->
    <!-- Se agregó "transform: scaleX(-1);" para que el jaguar mire hacia el formulario de login -->
    <div style="position: fixed; bottom: -10px; left: 20px; width: 280px; z-index: 1000; pointer-events: none;">
        <video autoplay loop muted playsinline style="width: 100%; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.5)); display: block; transform: scaleX(-1);">
            <source src="../videos/Videoespera.webm" type="video/webm">
        </video>
    </div>

    <!-- SCRIPT DE ANIMACIÓN -->
    <script>
        const contenedorFondo = document.getElementById('fondo-codigo');
        const simbolos = ['</>', '{...}', '=>', 'SELECT', 'if()', '++i', '&&', '||', '<h1>', 'localhost', 'root', 'INSERT'];
        const coloresNeon = ['#42a5f5', '#4caf50', '#ffca28', '#ef5350', '#ab47bc', '#26c6da'];

        function crearSimbolo() {
            const el = document.createElement('div');
            el.classList.add('simbolo-codigo');
            el.innerText = simbolos[Math.floor(Math.random() * simbolos.length)];
            const colorElegido = coloresNeon[Math.floor(Math.random() * coloresNeon.length)];
            el.style.color = colorElegido;
            el.style.textShadow = `0 0 10px ${colorElegido}`;
            el.style.left = Math.random() * 100 + 'vw';
            el.style.fontSize = (Math.random() * 1.5 + 0.8) + 'rem';
            
            const duracion = Math.random() * 7 + 8;
            el.style.animationDuration = duracion + 's';
            
            contenedorFondo.appendChild(el);
            setTimeout(() => { el.remove(); }, duracion * 1000);
        }
        
        for(let i = 0; i < 15; i++) { setTimeout(crearSimbolo, Math.random() * 5000); }
        setInterval(crearSimbolo, 1500);
    </script>
    <!-- MÚSICA DE FONDO -->
    <audio id="musica-juego" loop>
        <!-- Fíjate que usamos el nombre exacto de tu captura: muiscafondo.mp3 -->
        <source src="../musica/muiscafondo.mp3" type="audio/mpeg">
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