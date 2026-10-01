<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Control | Maestro</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&display=swap');
        
        * { box-sizing: border-box; }
        body { background-image: url('img/fondo.jpg'); background-size: cover; background-position: center; background-attachment: fixed; font-family: 'Nunito', sans-serif; margin: 0; display: flex; flex-direction: column; align-items: center; min-height: 100vh; padding: 20px; }

        .header-maestro { width: 100%; max-width: 1200px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(15px); border-radius: 20px; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 2px solid #fff; margin-bottom: 30px; }
        .header-maestro h1 { margin: 0; color: #1e88e5; font-size: 1.8rem; display: flex; align-items: center; gap: 15px; }
        
        .pin-box { background: linear-gradient(135deg, #f57f17, #ffb300); padding: 10px 30px; border-radius: 15px; color: white; text-align: center; box-shadow: 0 5px 15px rgba(245, 127, 23, 0.4); }
        .pin-box p { margin: 0; font-size: 0.9rem; font-weight: 800; text-transform: uppercase; opacity: 0.9; }
        .pin-box h2 { margin: 0; font-size: 2.5rem; letter-spacing: 5px; font-weight: 900; }

        .dashboard-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; width: 100%; max-width: 1200px; }

        /* Panel de Controles (Izquierda) */
        .panel-controles { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); border-radius: 20px; padding: 30px; border: 2px solid #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .panel-controles h3 { color: #6a8296; margin-top: 0; text-transform: uppercase; font-size: 1rem; border-bottom: 2px solid #e3f2fd; padding-bottom: 10px; }
        
        .selector-juego { width: 100%; padding: 15px; border-radius: 12px; border: 2px solid #90caf9; font-size: 1.1rem; font-family: 'Nunito', sans-serif; font-weight: 700; color: #1e88e5; outline: none; margin-bottom: 20px; cursor: pointer; }
        .selector-juego:focus { border-color: #1e88e5; }

        .btn { width: 100%; padding: 15px; border: none; border-radius: 12px; font-size: 1.1rem; font-weight: 800; cursor: pointer; transition: 0.3s; color: white; margin-bottom: 15px; text-transform: uppercase; }
        .btn-lanzar { background: #4caf50; box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4); }
        .btn-lanzar:hover { background: #388e3c; }
        .btn-pausar { background: #ffb300; box-shadow: 0 5px 15px rgba(255, 179, 0, 0.4); }
        .btn-cerrar { background: #ef5350; box-shadow: 0 5px 15px rgba(239, 83, 80, 0.4); }

        .estado-alerta { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 12px; font-weight: 800; text-align: center; border: 2px solid #81c784; margin-bottom: 20px; animation: latido 2s infinite; }
        @keyframes latido { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.02); } }

        /* Panel del Ranking en Vivo (Derecha) */
        .panel-ranking { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); border-radius: 20px; padding: 30px; border: 2px solid #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .panel-ranking h3 { display: flex; justify-content: space-between; color: #1e88e5; margin-top: 0; font-size: 1.5rem; font-weight: 900; border-bottom: 2px solid #e3f2fd; padding-bottom: 10px; }
        .indicador-vivo { display: flex; align-items: center; gap: 8px; color: #ef5350; font-size: 1rem; font-weight: 800; }
        .punto-rojo { width: 12px; height: 12px; background: #ef5350; border-radius: 50%; animation: parpadeo 1s infinite; }
        @keyframes parpadeo { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        .tabla-ranking { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .tabla-ranking th { background: #e3f2fd; color: #1e88e5; font-weight: 900; padding: 15px; text-transform: uppercase; font-size: 0.9rem; text-align: left; }
        .tabla-ranking th:first-child { border-radius: 12px 0 0 12px; }
        .tabla-ranking th:last-child { border-radius: 0 12px 12px 0; }
        .tabla-ranking td { padding: 15px; font-weight: 800; color: #2c3e50; border-bottom: 1px solid #f0f0f0; transition: background 0.3s; }
        
        .fila-alumno:hover td { background: #f8fbff; }
        .medalla { font-size: 1.5rem; }
        
        /* Celda vacía */
        .sin-datos { text-align: center; color: #6a8296; padding: 40px !important; font-weight: 700; font-size: 1.1rem; }

        @media (max-width: 900px) { .dashboard-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <header class="header-maestro">
        <h1>👨‍🏫 Panel Maestro: <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></h1>
        <div class="pin-box">
            <p>PIN para unirse</p>
            <h2><?php echo $sala_activa['codigo_sala']; ?></h2>
        </div>
    </header>

    <div class="dashboard-grid">
        
        <!-- CONTROLES DEL MAESTRO -->
        <div class="panel-controles">
            <h3>🎮 Controles de Clase</h3>
            
            <?php if ($sala_activa['estado'] === 'espera'): ?>
                <p style="color: #6a8296; font-weight: 700; margin-bottom: 20px;">Los alumnos están en el Lobby. Elige qué van a jugar y lánzales la actividad.</p>
                <form method="POST">
                    <select name="id_juego" class="selector-juego">
                        <option value="1">🎣 Pesca Técnica</option>
                        <option value="2">🔨 Caza Conceptos</option>
                        <option value="3">🎯 Tiro al Blanco</option>
                    </select>
                    <button type="submit" name="lanzar_juego" class="btn btn-lanzar">▶️ Lanzar Actividad</button>
                </form>

            <?php elseif ($sala_activa['estado'] === 'jugando'): ?>
                <div class="estado-alerta">
                    ▶️ Actividad en Curso
                </div>
                <p style="color: #6a8296; font-weight: 700; margin-bottom: 20px;">Los alumnos tienen las pantallas bloqueadas en el minijuego. Cuando termines, devuélvelos al Lobby.</p>
                <form method="POST">
                    <button type="submit" name="volver_lobby" class="btn btn-pausar">⏸️ Volver al Lobby</button>
                </form>
            <?php endif; ?>

            <hr style="border: 1px solid #f0f0f0; margin: 30px 0;">
            
            <form method="POST" onsubmit="return confirm('¿Seguro que quieres cerrar la sala por completo y expulsar a todos?');">
                <button type="submit" name="cerrar_sala" class="btn btn-cerrar">❌ Cerrar Sala Definitivamente</button>
            </form>
        </div>

        <!-- TABLA DE POSICIONES EN VIVO -->
        <div class="panel-ranking">
            <h3>
                🏆 Tabla de Posiciones
                <div class="indicador-vivo">
                    <div class="punto-rojo"></div> EN VIVO
                </div>
            </h3>
            
            <table class="tabla-ranking">
                <thead>
                    <tr>
                        <th style="width: 80px;">Top</th>
                        <th>Alumno</th>
                        <th>Puntuación</th>
                        <th>Errores</th>
                    </tr>
                </thead>
                <tbody id="cuerpo-ranking">
                    <tr><td colspan="4" class="sin-datos">Cargando datos en vivo...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SCRIPT DE ACTUALIZACIÓN EN TIEMPO REAL -->
    <script>
        function actualizarRankingEnVivo() {
            // Llamamos a nuestro controlador pasándole el parámetro api=ranking
            fetch('panel_maestro.php?api=ranking')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('cuerpo-ranking');
                tbody.innerHTML = ''; // Limpiamos la tabla
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="sin-datos">Aún no hay alumnos que hayan terminado la partida actual.</td></tr>';
                    return;
                }

                // Llenamos la tabla con los datos frescos
                data.forEach((fila, index) => {
                    let medalla = (index + 1) + "º";
                    if (index === 0) medalla = '🥇 1º';
                    if (index === 1) medalla = '🥈 2º';
                    if (index === 2) medalla = '🥉 3º';

                    let tr = document.createElement('tr');
                    tr.className = 'fila-alumno';
                    tr.innerHTML = `
                        <td style="font-size: 1.2rem;">${medalla}</td>
                        <td>${fila.nombre}</td>
                        <td style="color: #4caf50;">${fila.puntuacion_final} pts</td>
                        <td style="color: #ef5350;">${fila.errores} ❌</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => console.error("Error al actualizar tabla en vivo:", error));
        }

        // Ejecutar la actualización inmediatamente y luego cada 2 segundos (2000 ms)
        actualizarRankingEnVivo();
        setInterval(actualizarRankingEnVivo, 2000);
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