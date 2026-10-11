<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcade de Sistemas | Dashboard</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <script src="js/arcade-cursor.js" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&display=swap');
        * { box-sizing: border-box; }
        body { font-family: 'Nunito', sans-serif; margin: 0; display: flex; flex-direction: column; align-items: center; min-height: 100vh; overflow-x: hidden; padding-bottom: 50px; }

        /* Fondo Animado */
        #fondo-codigo { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; }
        .simbolo-codigo { position: absolute; font-family: monospace; font-weight: 900; animation: subir 10s linear infinite; opacity: 0.8; }
        @keyframes subir { 0% { transform: translateY(110vh) scale(0.5); opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { transform: translateY(-10vh) scale(1.5); opacity: 0; } }

        /* Barra Superior */
        .perfil-bar { width: 90%; max-width: 1100px; display: flex; justify-content: space-between; align-items: center; margin-top: 20px; flex-wrap: wrap; gap: 20px; z-index: 10; }
        .info-usuario { display: flex; align-items: center; gap: 15px; }
        .foto-perfil { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #42a5f5, #1e88e5); border: 3px solid #fff; box-shadow: 0 5px 15px rgba(30,136,229,0.3); display: flex; justify-content: center; align-items: center; font-size: 1.8rem; color: white; }
        .nombre-usuario { font-size: 1.2rem; font-weight: 900; color: #1e88e5; margin: 0; }
        .stats-container { display: flex; gap: 30px; text-align: center; }
        .dominio-container { width: 220px; }
        .barra-fondo { background: rgba(0, 0, 0, 0.08); border-radius: 20px; padding: 4px; border: 1px solid rgba(66, 165, 245, 0.3); box-shadow: inset 0 2px 5px rgba(0,0,0,0.1); height: 25px; position: relative; margin-top: 5px; }
        @keyframes mover-rayas { 0% { background-position: 0 0; } 100% { background-position: 50px 50px; } }
        .barra-relleno { height: 100%; border-radius: 15px; background: repeating-linear-gradient(-45deg, #42a5f5, #42a5f5 10px, #1e88e5 10px, #1e88e5 20px); background-size: 50px 50px; animation: mover-rayas 2s linear infinite; box-shadow: 0 0 12px rgba(66, 165, 245, 0.6); transition: width 1s cubic-bezier(0.25, 1, 0.5, 1); }


        /* Estructura de Columnas */
        .dashboard-grid { display: flex; gap: 30px; width: 90%; max-width: 1100px; z-index: 10; flex-wrap: wrap; align-items: flex-start;}
        
        .panel-izquierdo { flex: 1; min-width: 300px; }
        .btn-pin { width: 100%; padding: 16px; background: linear-gradient(135deg, #f57f17, #ffb300); color: white; border: none; border-radius: 12px; font-weight: 900; font-size: 1.1rem; cursor: pointer; box-shadow: 0 5px 15px rgba(245, 127, 23, 0.4); }
        .btn-pin:active { transform: translateY(3px); }

        .panel-derecho { flex: 2; min-width: 400px; }
        .panel-derecho h3 { margin-top: 0; color: #1e88e5; font-size: 1.5rem; font-weight: 900; border-bottom: 2px solid #e3f2fd; padding-bottom: 10px;}
        .tabla-arcade { width: 100%; border-collapse: separate; border-spacing: 0; border: 2px solid rgba(66, 165, 245, 0.5); border-radius: 15px; overflow: hidden; background: rgba(255, 255, 255, 0.6); box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: 15px; }
        .tabla-arcade th { background: linear-gradient(90deg, rgba(30,136,229,0.1), rgba(66,165,245,0.2)); color: #1e88e5; padding: 15px; font-weight: 900; text-transform: uppercase; border-bottom: 2px solid rgba(66, 165, 245, 0.3); }
        .tabla-arcade td { padding: 12px 15px; color: #2c3e50; font-weight: 700; border-bottom: 1px solid rgba(0,0,0,0.05); transition: background 0.3s; }
        .tabla-arcade tr:last-child td { border-bottom: none; }
        .badge-acierto { background: rgba(102, 187, 106, 0.2); color: #2e7d32; padding: 5px 10px; border-radius: 8px; border: 1px solid #81c784; }
        .badge-error { background: rgba(239, 83, 80, 0.2); color: #c62828; padding: 5px 10px; border-radius: 8px; border: 1px solid #e57373; }
        .tag-modo { padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 800; }
        .tag-solo { background: #e3f2fd; color: #1e88e5; }
        .tag-sala { background: #fff3e0; color: #f57f17; }

        @keyframes aparecerDeslizando { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .tarjeta-animada { animation: aparecerDeslizando 0.6s cubic-bezier(0.25, 1, 0.5, 1) backwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.3s; }
        /* Sección de Práctica Solo */

        #contenedor-juegos { display: none; margin-top: 30px; width: 90%; max-width: 1100px; z-index: 10; position: relative; animation: deslizar 0.5s ease; }
        @keyframes deslizar { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        
        .contenedor-juegos-grid { display: flex; flex-wrap: wrap; gap: 25px; justify-content: center; width: 100%; margin-top: 30px; }
        .tarjeta-juego { background: rgba(255, 255, 255, 0.95); border: 2px solid rgba(66, 165, 245, 0.3); border-radius: 20px; padding: 20px; width: 220px; text-align: center; text-decoration: none; box-shadow: 0 10px 25px rgba(0,0,0,0.08); transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.3s; display: flex; flex-direction: column; align-items: center; }
        .tarjeta-juego:hover { transform: translateY(-8px) scale(1.05); box-shadow: 0 15px 35px rgba(66, 165, 245, 0.3); border-color: #42a5f5; }
        .tarjeta-juego h3 { color: #1e88e5 !important; font-size: 1.3rem; font-weight: 900; margin: 15px 0 10px; }
        .tarjeta-juego p { color: #546e7a !important; font-size: 0.95rem; font-weight: 700; line-height: 1.3; margin: 0; }
        .tarjeta-juego img, .tarjeta-juego span.icono { font-size: 3rem; width: 60px; height: 60px; }

        /* MASCOTA FLOTANTE */
        .mascota-menu { position: fixed; bottom: -20px; right: 20px; width: 300px; pointer-events: none; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); animation: flotar 3s ease-in-out infinite; z-index: 20; }
        @keyframes flotar { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
        @media (max-width: 800px) { .mascota-menu { width: 150px; } }
    </style>
</head>
<body>
    <video autoplay loop muted playsinline id="video-fondo-global">
        <source src="videos/FondoAnimado1.webm" type="video/webm">
    </video>
    <div id="fondo-codigo"></div>

    <header class="perfil-bar barra-perfil-glass">
        <div class="info-usuario">
            <div class="foto-perfil">🧑‍💻</div>
            <div>
                <p class="nombre-usuario"><?php echo htmlspecialchars($nombre_usuario); ?></p>
                <a href="controladores/logout.php" class="btn-cerrar-sesion">
                    Cerrar Sesión <span class="icono-salir">🚪</span>
                </a>
            </div>
        </div>
        <div class="stats-container">
            <div style="text-align: center;">
                <span class="stat-titulo">Partidas</span>
                <span class="stat-valor valor-azul"><?php echo (int) $juegos_jugados; ?></span>
            </div>
            <div style="text-align: center;">
                <span class="stat-titulo">Errores</span>
                <span class="stat-valor valor-rojo"><?php echo (int) $errores_totales; ?></span>
            </div>
        </div>
        <div class="dominio-container">
            <div class="dominio-titulos">
                <span>Dominio del Tema</span>
                <span style="color: <?php echo ($dominio_porcentaje >= 60) ? '#4caf50' : '#ef5350'; ?>;"><?php echo $dominio_porcentaje; ?>%</span>
            </div>
            <div class="barra-fondo"><div class="barra-relleno" style="width: <?php echo max(0, min(100, (int)$dominio_porcentaje)); ?>%;"></div></div>
        </div>
    </header>

    <div class="titulo-panel">
        <h2>Panel de Estudiante</h2>
    </div>

    <!-- DASHBOARD PRINCIPAL -->
    <div class="dashboard-grid">
        
        <!-- COLUMNA IZQ: PIN DE SALA -->
        <div class="panel-izquierdo">
            <form action="index.php" method="POST" class="tarjeta-unirse tarjeta-animada delay-1">
                <span style="font-size: 3rem;">🎮</span>
                <h3>Unirse a Clase</h3>
                <p>Ingresa el PIN de tu profesor.</p>
                <input type="text" name="codigo_sala" class="input-pin" placeholder="EJ. X9K2M" required autocomplete="off" maxlength="5">
                <?php if ($error_sala): ?>
                    <div style="color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 8px; font-weight: bold; margin-bottom: 15px;"><?php echo htmlspecialchars($error_sala); ?></div>
                <?php endif; ?>
                <button type="submit" name="unirse_sala" class="btn-entrar">ENTRAR A LA SALA</button>
            </form>
        </div>

        <!-- COLUMNA DER: HISTORIAL -->
        <div class="panel-derecho contenedor-tabla-glass tarjeta-animada delay-2">
            <h3>📊 Tus Últimas Partidas</h3>
            <?php if (count($historial_partidas) > 0): ?>
                <table class="tabla-historial tabla-arcade">
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
                                <td><span class="badge-acierto"><?php echo (int)$partida['puntuacion_final']; ?></span></td>
                                <td><span class="badge-error"><?php echo (int)$partida['errores']; ?></span></td>
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
    <button type="button" class="btn-practicar" id="btn-practicar">🎮 Practicar Solo 🕹️</button>

    <!-- SECCIÓN DE JUEGOS ACTIVOS -->
    <div id="contenedor-juegos">
        <button type="button" class="btn-cerrar-x" id="btn-cerrar-practica" onclick="cerrarPanelJuegos('contenedor-juegos')">✖</button>
        <div class="contenedor-juegos-grid">
            <a href="controladores/juego1.php?reiniciar=<?php echo time(); ?>&solo=1" class="tarjeta-juego tarjeta-animada delay-1">
                <span class="icono">🎣</span>
                <h3>Pesca Técnica</h3>
                <p>Atrapa la respuesta correcta que nada en el río digital.</p>
            </a>
            <a href="controladores/juego2.php?reiniciar=<?php echo time(); ?>&solo=1" class="tarjeta-juego tarjeta-animada delay-1">
                <span class="icono">🔨</span>
                <h3>Caza Conceptos</h3>
                <p>Martilla la pantalla holográfica antes de que se esconda.</p>
            </a>
            <a href="controladores/juego3.php?reiniciar=<?php echo time(); ?>&solo=1" class="tarjeta-juego tarjeta-animada delay-1">
                <span class="icono">🎯</span>
                <h3>Tiro al Blanco</h3>
                <p>Memoriza las posiciones y dispárale a la correcta.</p>
            </a>
            <a href="controladores/juego4.php" class="tarjeta-juego tarjeta-animada delay-2">
                <span class="icono">🎈</span>
                <h3>Atrapar Globos</h3>
                <p>Revienta el globo con la respuesta correcta antes de que escape.</p>
            </a>
            <a href="controladores/juego5.php" class="tarjeta-juego tarjeta-animada delay-1">
                <span class="icono">🏢</span>
                <h3>Torre de Conceptos</h3>
                <p>Apila la respuesta correcta sin tirar la torre</p>
            </a>
        </div>
    </div>

    <img src="img/mascota1.png" alt="Guepardo Arcade" class="mascota-menu">

    <script>
        const btnPracticar = document.getElementById('btn-practicar');
        const seccionJuegos = document.getElementById('contenedor-juegos');
        btnPracticar.addEventListener('click', () => {
            seccionJuegos.classList.remove('animacion-salida');
            seccionJuegos.style.display = 'block';
            btnPracticar.style.display = 'none';
            seccionJuegos.scrollIntoView({ behavior: 'smooth' });
        });

        function cerrarPanelJuegos(idContenedor) {
            const contenedor = document.getElementById(idContenedor);
            if (!contenedor || contenedor.classList.contains('animacion-salida')) return;

            contenedor.classList.add('animacion-salida');
            window.setTimeout(() => {
                contenedor.style.display = 'none';
                contenedor.classList.remove('animacion-salida');
                btnPracticar.style.display = 'block';
            }, 400);
        }

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