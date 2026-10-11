<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Control | Maestro</title>
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
    <script src="../js/arcade-cursor.js" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&display=swap');
        
        * { box-sizing: border-box; }
        body { font-family: 'Nunito', sans-serif; margin: 0; display: flex; flex-direction: column; align-items: center; min-height: 100vh; padding: 20px; }

        .header-maestro { width: 100%; max-width: 1200px; display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 30px; }
        .header-maestro h1 { margin: 0; color: #1e88e5; font-size: 1.8rem; display: flex; align-items: center; gap: 15px; }
        
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
        .boton-archivo-temario:hover { transform: translateY(-2px); border-color: #7b1fa2 !important; box-shadow: 0 9px 20px rgba(123, 31, 162, 0.3) !important; }
        .selector-archivo-temario:focus-within .boton-archivo-temario { outline: 3px solid rgba(171, 71, 188, 0.35); outline-offset: 3px; }
        .selector-archivo-temario input[type="file"] { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
        .selector-archivo-temario.arrastrando .boton-archivo-temario { background: #f3e5f5 !important; border-color: #7b1fa2 !important; box-shadow: 0 9px 20px rgba(123, 31, 162, 0.3) !important; }

        .estado-alerta { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 12px; font-weight: 800; text-align: center; border: 2px solid #81c784; margin-bottom: 20px; animation: latido 2s infinite; }
        @keyframes latido { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.02); } }

        /* Panel del Ranking en Vivo (Derecha) */
        .panel-ranking { min-width: 0; }
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
    <video autoplay loop muted playsinline id="video-fondo-global">
        <source src="../videos/FondoAnimado1.webm" type="video/webm">
    </video>

    <header class="header-maestro barra-perfil-glass">
        <h1>👨‍🏫 Panel Maestro: <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></h1>
        <a href="../controladores/logout.php" class="btn-cerrar-sesion">
            Cerrar Sesión <span class="icono-salir">🚪</span>
        </a>
    </header>

    <section class="panel-pin-glass">
        <h3 style="color: #546e7a; font-weight: 800; text-transform: uppercase;">PIN de la Sala</h3>
        <div class="texto-pin-neon"><?php echo htmlspecialchars($sala_activa['codigo_sala']); ?></div>
        <p style="color: #78909c;">Pide a tus alumnos que ingresen este código.</p>
    </section>

    <div class="dashboard-grid">
        
        <!-- CONTROLES DE CLASE REORGANIZADOS -->
        <div class="panel-controles" style="text-align: left; padding: 25px;">
            <h3 style="color: #4a148c; font-weight: 900; font-size: 1.3rem; margin-bottom: 15px; border-bottom: 2px solid #e1bee7; padding-bottom: 10px;">
                ⚙️ Configuración de la Sala
            </h3>
            
<?php if (($mensaje_panel['tipo'] ?? '') === 'exito' && ($_GET['exito'] ?? '') === '1'): ?>
                <div role="status" style="background: #e8f5e9; border: 2px solid #66bb6a; color: #2e7d32; padding: 15px; border-radius: 12px; font-weight: 800; text-align: center; margin-bottom: 20px;">
                    ✅ ¡Tema cargado con éxito!<br>
                    <span style="font-size: 0.9rem; font-weight: 600; color: #388e3c;"><?php echo htmlspecialchars($mensaje_panel['texto'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php elseif ($mensaje_panel): ?>
                <div role="alert" style="padding: 12px; margin-bottom: 15px; border-radius: 10px; font-weight: 800; color: #b71c1c; background: #ffebee;">
                    <?php echo htmlspecialchars($mensaje_panel['texto'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>


            <div style="background: rgba(243, 229, 245, 0.5); padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #ce93d8;">
                <h4 style="margin-top: 0; color: #6a1b9a; font-size: 1rem;">1. Fuente de las Preguntas (IA)</h4>
                <form id="form-ia" action="actualizar_tema_ia.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <label style="flex: 1; text-align: center; background: white; padding: 10px; border-radius: 8px; border: 2px solid #ab47bc; cursor: pointer; font-weight: 800; color: #4a148c;">
                            <input type="radio" name="modo_ia" value="texto" checked>
                            ✍️ Escribir Tema
                        </label>
                        <label style="flex: 1; text-align: center; background: white; padding: 10px; border-radius: 8px; border: 2px solid #ab47bc; cursor: pointer; font-weight: 800; color: #4a148c;">
                            <input type="radio" name="modo_ia" value="archivo">
                            📄 Subir Archivo
                        </label>
                    </div>

                    <div id="caja-texto" style="margin-bottom: 15px;">
                        <textarea name="temario_ia" id="temario_ia" rows="3" maxlength="20000" required placeholder="Ej. Escribe aquí: La Revolución Mexicana, Redes EIGRP, etc..."
                            style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ab47bc; font-family: 'Nunito', sans-serif; outline: none; box-sizing: border-box;"><?php echo htmlspecialchars($temario_ia_ultimo, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div id="caja-archivo" style="margin-bottom: 15px; display: none;">
                        <div class="selector-archivo-temario" id="selector-archivo-temario" style="position: relative;">
                            <input type="file" name="archivo_temario" id="archivo_temario" accept=".pdf,.txt,.docx" aria-label="Seleccionar archivo PDF, TXT o DOCX" disabled>
                            <label for="archivo_temario" class="boton-archivo-temario" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 25px 15px; background: rgba(255,255,255,0.9); border: 2px dashed #ab47bc; border-radius: 10px; cursor: pointer; transition: all 0.3s;">
                                <span style="font-size: 2.5rem; margin-bottom: 5px;">📥</span>
                                <span style="color: #6a1b9a; font-weight: 900; font-size: 1.05rem;">Haz clic o arrastra tu archivo aquí</span>
                                <span style="color: #78909c; font-size: 0.85rem; font-weight: 600; margin-top: 5px;">PDF, TXT o DOCX · Máximo 2 MB</span>
                            </label>
                            <small id="nombre-archivo-temario" aria-live="polite" style="color: #4a148c; display: none; margin-top: 10px; font-weight: 900; text-align: center;"></small>
                        </div>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label for="enfoque_tema" style="font-size: 0.85rem; color: #6a8296; font-weight: 800;">Filtro Específico (Opcional):</label>
                        <input type="text" name="enfoque_tema" id="enfoque_tema" maxlength="300" placeholder="Ej. Solo Tema 1, o Solo valores éticos..."
                            style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #b0bec5; font-family: 'Nunito', sans-serif; outline: none; margin-top: 5px; box-sizing: border-box;">
                    </div>

                    <button type="submit" id="btn-generar-ia" style="width: 100%; background: linear-gradient(135deg, #ab47bc, #7b1fa2); color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 900; cursor: pointer; font-size: 1rem; box-shadow: 0 4px 10px rgba(171,71,188,0.3); transition: all 0.3s;">
                        ✨ Generar Preguntas
                    </button>
                </form>
                <small style="display: block; margin-top: 8px; color: #78909c; font-weight: 600;">
                    Las preguntas son exclusivas de esta sala y se usarán al iniciar la siguiente actividad.
                </small>
            </div>

            <div style="background: rgba(227, 242, 253, 0.5); padding: 15px; border-radius: 12px; border: 1px solid #90caf9;">
                <h4 style="margin-top: 0; color: #1565c0; font-size: 1rem;">2. Lanzar Actividad</h4>
                <?php if ($sala_activa['estado'] === 'espera'): ?>
                    <form method="POST">
                        <select name="id_juego" class="selector-juego" style="margin-bottom: 10px; padding: 10px; font-size: 0.9rem;">
                            <option value="1">🎣 Pesca Técnica</option>
                            <option value="2">🔨 Caza Conceptos</option>
                            <option value="3">🎯 Tiro al Blanco</option>
                            <option value="4">🎈 Atrapar Globos</option>
                            <option value="5">🏢 Torre de Conceptos</option>
                            <option value="fiesta" style="color: #d81b60; font-weight: 900;">🎉 ¡Fiesta! (Todos)</option>
                        </select>
                        <button type="submit" name="lanzar_juego" class="btn btn-lanzar" style="padding: 12px; font-size: 1rem;">▶️ Iniciar Juego</button>
                    </form>
                <?php else: ?>
                    <div style="text-align: center;">
                        <div class="estado-alerta" style="margin-bottom: 10px; padding: 10px;">▶️ Juego en Curso</div>
                        <form method="POST">
                            <button type="submit" name="volver_lobby" class="btn btn-pausar" style="padding: 10px; font-size: 0.9rem;">⏸️ Volver al Lobby</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 20px 0;">

            <form method="POST" onsubmit="return confirm('¿Cerrar sala por completo?');">
                <button type="submit" name="cerrar_sala" class="btn btn-cerrar" style="padding: 10px; font-size: 0.9rem;">❌ Cerrar Sala Definitivamente</button>
            </form>
        </div>

        <!-- TABLA DE POSICIONES EN VIVO -->
        <div class="panel-ranking contenedor-tabla-glass">
            <h3>
                🏆 Tabla de Posiciones
                <div class="indicador-vivo">
                    <div class="punto-rojo"></div> EN VIVO
                </div>
            </h3>
            
            <table class="tabla-ranking tabla-arcade">
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
        <source src="../musica/muiscafondo.mp3" type="audio/mpeg">
    </audio>

    <!-- SCRIPT PARA CONTROLAR LA MÚSICA -->
    <script>
        const cajaTextoTemario = document.getElementById('caja-texto');
        const cajaArchivoTemario = document.getElementById('caja-archivo');
        const entradaTemario = document.getElementById('temario_ia');
        const entradaArchivoTemario = document.getElementById('archivo_temario');
        const modosTemario = document.querySelectorAll('input[name="modo_ia"]');
        const selectorArchivoTemario = document.getElementById('selector-archivo-temario');
        const nombreArchivoTemario = document.getElementById('nombre-archivo-temario');

        function actualizarModoTemario() {
            const usarArchivo = document.querySelector('input[name="modo_ia"]:checked').value === 'archivo';
            cajaTextoTemario.style.display = usarArchivo ? 'none' : 'block';
            cajaArchivoTemario.style.display = usarArchivo ? 'block' : 'none';
            entradaTemario.disabled = usarArchivo;
            entradaTemario.required = !usarArchivo;
            entradaArchivoTemario.disabled = !usarArchivo;
            entradaArchivoTemario.required = usarArchivo;
        }

        modosTemario.forEach(modo => modo.addEventListener('change', actualizarModoTemario));
        actualizarModoTemario();

        entradaArchivoTemario.addEventListener('change', function () {
            const archivo = this.files[0];
            nombreArchivoTemario.textContent = archivo ? `📄 Archivo seleccionado: ${archivo.name}` : '';
            nombreArchivoTemario.style.display = archivo ? 'block' : 'none';
        });

        selectorArchivoTemario.addEventListener('dragover', function (evento) {
            evento.preventDefault();
            if (!entradaArchivoTemario.disabled) {
                selectorArchivoTemario.classList.add('arrastrando');
            }
        });
        selectorArchivoTemario.addEventListener('dragleave', function (evento) {
            if (!selectorArchivoTemario.contains(evento.relatedTarget)) {
                selectorArchivoTemario.classList.remove('arrastrando');
            }
        });
        selectorArchivoTemario.addEventListener('drop', function (evento) {
            evento.preventDefault();
            selectorArchivoTemario.classList.remove('arrastrando');
            if (entradaArchivoTemario.disabled || !evento.dataTransfer.files.length) {
                return;
            }

            const transferencia = new DataTransfer();
            transferencia.items.add(evento.dataTransfer.files[0]);
            entradaArchivoTemario.files = transferencia.files;
            entradaArchivoTemario.dispatchEvent(new Event('change', { bubbles: true }));
        });



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