<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcade de Sistemas | Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&display=swap');
        * { box-sizing: border-box; }
        body { background-image: url('img/fondo.jpg'); background-size: cover; background-position: center; background-attachment: fixed; font-family: 'Nunito', sans-serif; margin: 0; display: flex; flex-direction: column; align-items: center; min-height: 100vh; overflow-x: hidden; padding-bottom: 50px; }

        /* Fondo Animado */
        #fondo-codigo { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; }
        .simbolo-codigo { position: absolute; font-family: monospace; font-weight: 900; animation: subir 10s linear infinite; opacity: 0.8; }
        @keyframes subir { 0% { transform: translateY(110vh) scale(0.5); opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { transform: translateY(-10vh) scale(1.5); opacity: 0; } }

        /* Barra Superior */
        .perfil-bar { width: 90%; max-width: 1100px; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); border: 2px solid rgba(255,255,255,0.9); border-radius: 20px; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; margin-top: 20px; box-shadow: 0 15px 30px rgba(30,136,229,0.15); flex-wrap: wrap; gap: 20px; z-index: 10; }
        .info-usuario { display: flex; align-items: center; gap: 15px; }
        .foto-perfil { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #42a5f5, #1e88e5); border: 3px solid #fff; box-shadow: 0 5px 15px rgba(30,136,229,0.3); display: flex; justify-content: center; align-items: center; font-size: 1.8rem; color: white; }
        .nombre-usuario { font-size: 1.2rem; font-weight: 900; color: #1e88e5; margin: 0; }
        .btn-logout { font-size: 0.8rem; color: #ef5350; text-decoration: none; font-weight: 800; text-transform: uppercase; }
        .stats-container { display: flex; gap: 30px; text-align: center; }
        .dominio-container { width: 220px; }
        .barra-fondo { width: 100%; height: 12px; background: rgba(66,165,245,0.2); border-radius: 10px; overflow: hidden; margin-top: 5px;}
        .barra-llena { height: 100%; background: linear-gradient(90deg, #42a5f5, #4caf50); border-radius: 10px; width: <?php echo $dominio_porcentaje; ?>%; }

        h1.titulo { font-size: 2.8rem; color: #1e88e5; background: rgba(255,255,255,0.9); padding: 10px 40px; border-radius: 20px; margin: 30px 0; border: 2px solid #fff; z-index: 10; }

        /* Estructura de Columnas */
        .dashboard-grid { display: flex; gap: 30px; width: 90%; max-width: 1100px; z-index: 10; flex-wrap: wrap; align-items: flex-start;}
        
        .panel-izquierdo { flex: 1; min-width: 300px; }
        .tarjeta-pin { background: rgba(255,255,255,0.9); backdrop-filter: blur(15px); border: 2px solid #ffb300; border-radius: 24px; padding: 40px 30px; text-align: center; box-shadow: 0 10px 30px rgba(245, 127, 23, 0.15); }
        .input-pin { width: 100%; padding: 15px; border: 2px solid #ffe0b2; border-radius: 12px; font-size: 1.5rem; text-align: center; font-weight: 900; text-transform: uppercase; color: #f57f17; margin: 20px 0; outline: none; background: #fff8e1; letter-spacing: 2px;}
        .input-pin:focus { border-color: #f57f17; background: #fff; }
        .btn-pin { width: 100%; padding: 16px; background: linear-gradient(135deg, #f57f17, #ffb300); color: white; border: none; border-radius: 12px; font-weight: 900; font-size: 1.1rem; cursor: pointer; box-shadow: 0 5px 15px rgba(245, 127, 23, 0.4); }
        .btn-pin:active { transform: translateY(3px); }

        .panel-derecho { flex: 2; min-width: 400px; background: rgba(255,255,255,0.9); backdrop-filter: blur(15px); border: 2px solid #fff; border-radius: 24px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .panel-derecho h3 { margin-top: 0; color: #1e88e5; font-size: 1.5rem; font-weight: 900; border-bottom: 2px solid #e3f2fd; padding-bottom: 10px;}
        .tabla-historial { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .tabla-historial th, .tabla-historial td { padding: 12px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        .tabla-historial th { color: #6a8296; font-weight: 800; text-transform: uppercase; font-size: 0.85rem; }
        .tabla-historial td { font-weight: 700; color: #2c3e50; }
        .tag-modo { padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 800; }
        .tag-solo { background: #e3f2fd; color: #1e88e5; }
        .tag-sala { background: #fff3e0; color: #f57f17; }

        /* Sección de Práctica Solo */
        .btn-practicar-solo { margin-top: 40px; padding: 15px 40px; background: white; color: #1e88e5; font-size: 1.2rem; font-weight: 900; border: 2px solid #1e88e5; border-radius: 15px; cursor: pointer; box-shadow: 0 5px 15px rgba(30,136,229,0.2); transition: 0.3s; z-index: 10;}
        .btn-practicar-solo:hover { background: #1e88e5; color: white; }

        #seccion-juegos { display: none; margin-top: 30px; width: 90%; max-width: 1100px; z-index: 10; position: relative; animation: deslizar 0.5s ease; }
        @keyframes deslizar { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        
        .btn-cerrar-juegos { position: absolute; top: -20px; right: 0; background: #ef5350; color: white; border: none; width: 40px; height: 40px; border-radius: 50%; font-weight: bold; font-size: 1.2rem; cursor: pointer; box-shadow: 0 4px 10px rgba(239,83,80,0.3); }

        .tarjetas-container { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; width: 100%; margin-top: 20px;}
        .tarjeta-juego { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); border: 2px solid #fff; border-radius: 20px; padding: 30px 20px; width: 250px; text-align: center; text-decoration: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: 0.3s; }
        .tarjeta-juego:hover { transform: translateY(-10px); border-color: #42a5f5; }
        .tarjeta-juego h2 { color: #1e88e5; margin: 10px 0; font-size: 1.4rem;}
        .tarjeta-juego p { color: #6a8296; font-size: 0.9rem; font-weight: 700; margin: 0;}

        /* MASCOTA FLOTANTE */
        .mascota-menu { position: fixed; bottom: -20px; right: 20px; width: 300px; pointer-events: none; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); animation: flotar 3s ease-in-out infinite; z-index: 20; }
        @keyframes flotar { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
        @media (max-width: 800px) { .mascota-menu { width: 150px; } }
    </style>
</head>
<body>
    <div id="fondo-codigo"></div>

    <header class="perfil-bar">
        <div class="info-usuario">
            <div class="foto-perfil">🧑‍💻</div>
            <div>
                <p class="nombre-usuario"><?php echo htmlspecialchars($nombre_usuario); ?></p>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>
        <div class="stats-container">
            <div><p style="margin:0; font-weight:800; color:#6a8296;">Partidas</p><h3 style="color:#1e88e5; margin:0;"><?php echo $juegos_jugados; ?></h3></div>
            <div><p style="margin:0; font-weight:800; color:#6a8296;">Errores</p><h3 style="color:#ef5350; margin:0;"><?php echo $errores_totales; ?></h3></div>
        </div>
        <div class="dominio-container">
            <div class="dominio-titulos">
                <span>Dominio del Tema</span>
                <span style="color: <?php echo ($dominio_porcentaje >= 60) ? '#4caf50' : '#ef5350'; ?>;"><?php echo $dominio_porcentaje; ?>%</span>
            </div>
            <div class="barra-fondo"><div class="barra-llena"></div></div>
        </div>
    </header>

    <h1 class="titulo">Panel de Estudiante</h1>

    <!-- DASHBOARD PRINCIPAL -->
    <div class="dashboard-grid">
        
        <!-- COLUMNA IZQ: PIN DE SALA -->
        <div class="panel-izquierdo">
            <div class="tarjeta-pin">
                <div style="font-size: 4rem; margin-bottom: 10px;">🎮</div>
                <h2 style="color: #f57f17; margin:0;">Unirse a Clase</h2>
                <p style="color: #6a8296; font-weight: 700; font-size: 0.95rem;">Ingresa el PIN de tu profesor.</p>
                <form method="POST">
                    <input type="text" name="codigo_sala" class="input-pin" required placeholder="Ej. X9K2M" autocomplete="off" maxlength="6">
                    <?php if ($error_sala): ?>
                        <div style="color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 8px; font-weight: bold; margin-bottom: 15px;"><?php echo $error_sala; ?></div>
                    <?php endif; ?>
                    <button type="submit" name="unirse_sala" class="btn-pin">ENTRAR A LA SALA</button>
                </form>
            </div>
        </div>

        <!-- COLUMNA DER: HISTORIAL -->
        <div class="panel-derecho">
            <h3>📊 Tus Últimas Partidas</h3>
            <?php if (count($historial_partidas) > 0): ?>
                <table class="tabla-historial">
                    <thead>
                        <tr>
                            <th>Juego</th>
                            <th>Puntos</th>
                            <th>Errores</th>
                            <th>Modalidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial_partidas as$partida): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($partida['nombre_juego']); ?></td>
                                <td style="color: #4caf50; font-weight: 900;"><?php echo $partida['puntuacion_final']; ?></td>
                                <td style="color: #ef5350; font-weight: 900;"><?php echo $partida['errores']; ?></td>
                                <td>
                                    <?php if ($partida['id_sala']): ?>
                                        <span class="tag-modo tag-sala">Sala 🎮</span>
                                    <?php else: ?>
                                        <span class="tag-modo tag-solo">Solo 👤</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #6a8296; font-weight: bold;">Aún no has jugado ninguna partida. ¡Anímate a empezar!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- BOTÓN PARA DESPLEGAR JUEGOS INDIVIDUALES -->
    <button class="btn-practicar-solo" id="btn-practicar">Practicar Solo 🕹️</button>

    <!-- SECCIÓN DE JUEGOS ACTIVOS -->
    <div id="seccion-juegos">
        <button class="btn-cerrar-juegos" id="btn-cerrar-practica">X</button>
        <div class="tarjetas-container">
            <a href="juego1.php?reiniciar=<?php echo time(); ?>&solo=1" class="tarjeta-juego">
                <div style="font-size: 4rem;">🎣</div>
                <h2>Pesca Técnica</h2>
                <p>Atrapa la respuesta correcta que nada en el río digital.</p>
            </a>
            <a href="juego2.php?reiniciar=<?php echo time(); ?>&solo=1" class="tarjeta-juego">
                <div style="font-size: 4rem;">🔨</div>
                <h2>Caza Conceptos</h2>
                <p>Martilla la pantalla holográfica antes de que se esconda.</p>
            </a>
            <a href="juego3.php?reiniciar=<?php echo time(); ?>&solo=1" class="tarjeta-juego">
                <div style="font-size: 4rem;">🎯</div>
                <h2>Tiro al Blanco</h2>
                <p>Memoriza las posiciones y dispárale a la correcta.</p>
            </a>
            <a href="juego4.php" class="tarjeta-juego">
                <div style="font-size: 4rem;">🎈</div>
                <h2>Atrapar Globos</h2>
                <p>Revienta el globo con la respuesta correcta antes de que escape.</p>
            </a>
            <a href="juego5.php" class="tarjeta-juego">
                <div style="font-size: 4rem;">🏢</div>
                <h2>Torre de Conceptos</h2>
                <p>Apila la respuesta correcta sin tirar la torre</p>
            </a>
        </div>
    </div>

    <img src="img/mascota1.png" alt="Guepardo Arcade" class="mascota-menu">

    <script>
        const btnPracticar = document.getElementById('btn-practicar');
        const seccionJuegos = document.getElementById('seccion-juegos');
        const btnCerrarPractica = document.getElementById('btn-cerrar-practica');

        btnPracticar.addEventListener('click', () => {
            seccionJuegos.style.display = 'block';
            btnPracticar.style.display = 'none';
            seccionJuegos.scrollIntoView({ behavior: 'smooth' });
        });

        btnCerrarPractica.addEventListener('click', () => {
            seccionJuegos.style.display = 'none';
            btnPracticar.style.display = 'block';
        });

        const contenedorFondo = document.getElementById('fondo-codigo');
        const simbolos = ['</>', '{...}', '=>', 'SELECT', 'if()', '&&', '||', '<h1>'];
        const coloresNeon = ['#42a5f5', '#4caf50', '#ffca28', '#ab47bc'];

        function crearSimbolo() {
            const el = document.createElement('div');
            el.classList.add('simbolo-codigo');
            el.innerText = simbolos[Math.floor(Math.random() * simbolos.length)];
            const color = coloresNeon[Math.floor(Math.random() * coloresNeon.length)];
            el.style.color = color; el.style.textShadow = `0 0 10px ${color}`;
            el.style.left = Math.random() * 100 + 'vw';
            el.style.fontSize = (Math.random() * 1.5 + 0.8) + 'rem';
            contenedorFondo.appendChild(el);
            setTimeout(() => { el.remove(); }, 10000);
        }
        setInterval(crearSimbolo, 1500);
    </script>
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