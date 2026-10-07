<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atrapar Globos - Arcade</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&display=swap');

        body { background-image: url('img/fondo.jpg'); background-size: cover; background-position: center; font-family: 'Nunito', sans-serif; margin: 0; overflow: hidden; display: flex; align-items: center; justify-content: center; height: 100vh; }

        .contenedor-juego { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border: 2px solid #fff; padding: 30px; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.15); width: 95%; max-width: 700px; text-align: center; z-index: 10; position: relative; }

        .stats-bar { font-weight: 800; font-size: 1.1rem; display: flex; justify-content: space-between; margin-bottom: 15px; }
        .pregunta-texto { font-size: 1.8rem; color: #9c27b0; font-weight: 900; margin-bottom: 20px; background: white; padding: 15px; border-radius: 15px; border: 3px solid #ce93d8; }

        /* ZONA DE CIELO PARA GLOBOS */
        #cielo-globos { position: fixed; top: 0; left: 0; width: 100%; height: 100vh; pointer-events: none; z-index: 5; overflow: hidden; }
        
        .globo {
            position: absolute; bottom: -180px; width: 110px; height: 140px; border-radius: 50% 50% 50% 50% / 40% 40% 60% 60%;
            display: flex; justify-content: center; align-items: center; text-align: center; padding: 15px; font-weight: 800; font-size: 0.9rem; color: white; cursor: crosshair; pointer-events: auto;
            box-shadow: inset -10px -10px 20px rgba(0,0,0,0.2); transition: transform 0.1s;
        }
        
        /* Hilo del globo */
        .globo::after { content: ''; position: absolute; bottom: -40px; left: 50%; width: 2px; height: 40px; background: rgba(255,255,255,0.7); transform: translateX(-50%); }
        /* Efecto al explotar */
        .explotado { transform: scale(1.5); opacity: 0; pointer-events: none; transition: 0.2s ease-out; }

        #pantalla-final { display: none; margin-top: 20px; }
        .boton-accion { display: inline-block; padding: 12px 25px; background: #9c27b0; color: white; text-decoration: none; font-weight: bold; border-radius: 10px; font-size: 1.1rem; border:none; cursor:pointer; }
    </style>
</head>
<body>

<div id="cielo-globos"></div>

<div class="contenedor-juego" id="app-juego">
    <div class="stats-bar" id="barra-estado">
        <span style="color: #ef5350;" id="txt-errores">❌ Errores: 0</span>
        <span style="color: #ab47bc;" id="txt-ronda">Ronda: 1/5</span>
    </div>
    <div id="zona-activa">
        <div class="pregunta-texto" id="txt-pregunta">Cargando...</div>
        <h3 style="color: #6a8296;">¡Revienta el globo correcto antes de que escape! 🎈</h3>
    </div>

    <div id="pantalla-final">
        <h2 style="font-size: 2.5rem; color: #9c27b0;">¡Misión Completada!</h2>
        <p id="mensaje-puntuacion" style="font-weight: 700; color: #6a8296; font-size: 1.2rem;"></p>
        <p id="mensaje-guardado" style="color: #4caf50; font-weight: 800; display: none;">✅ Puntaje guardado exitosamente</p>
        <a href="index.php" class="boton-accion" style="margin-top: 20px;">Menú Principal</a>
    </div>
</div>

<!-- MASCOTAS -->
<div style="position: fixed; bottom: -10px; left: 20px; width: 250px; z-index: 1000; pointer-events: none;">
    <video id="vid-espera" autoplay loop muted playsinline style="width: 100%; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); display: block;">
        <source src="videos/Videoespera.webm" type="video/webm">
    </video>
    <video id="vid-felicitar" muted playsinline style="width: 100%; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); display: none;">
        <source src="videos/VideoFelicitar.webm" type="video/webm">
    </video>
</div>

<script>
    const datosJuego = <?php echo json_encode($preguntas, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    let rondaActual = 0;
    let errores = 0;
    let respuestaCorrectaTexto = "";
    let opcionesActuales = [];
    let indiceOpcion = 0;
    const cielo = document.getElementById('cielo-globos');
    const colores = ['#ef5350', '#42a5f5', '#66bb6a', '#ffa726', '#ab47bc'];
    let generadorGlobos;

    function iniciarRonda() {
        cielo.innerHTML = '';
        if (generadorGlobos) {
            clearInterval(generadorGlobos);
            generadorGlobos = null;
        }

        if(rondaActual >= datosJuego.length) { terminarJuego(); return; }

        document.getElementById('txt-ronda').innerText = `Ronda: ${rondaActual + 1}/${datosJuego.length}`;
        let pregunta = datosJuego[rondaActual];
        document.getElementById('txt-pregunta').innerText = pregunta.pregunta;
        respuestaCorrectaTexto = pregunta.correcta;

        opcionesActuales = [pregunta.correcta, pregunta.falsas[0], pregunta.falsas[1]];
        opcionesActuales.sort(() => Math.random() - 0.5);

        indiceOpcion = 0;
        generadorGlobos = setInterval(() => {
            if (indiceOpcion >= opcionesActuales.length) {
                opcionesActuales.sort(() => Math.random() - 0.5);
                indiceOpcion = 0;
            }

            crearGlobo(opcionesActuales[indiceOpcion]);
            indiceOpcion++;
        }, 1000);
    }

    function crearGlobo(texto) {
        let globo = document.createElement('div');
        globo.className = 'globo';
        globo.innerText = texto;
        globo.style.background = colores[Math.floor(Math.random() * colores.length)];
        
        // Posición aleatoria horizontal (entre 10% y 80% de la pantalla)
        globo.style.left = (Math.random() * 70 + 10) + 'vw';
        
        // Animación de subida
        let duracion = Math.random() * 3 + 4; // Entre 4 y 7 segundos en subir
        globo.animate([
            { transform: 'translateY(0) rotate(0deg)' },
            { transform: `translateY(-120vh) rotate(${Math.random()*20-10}deg)` }
        ], { duration: duracion * 1000, fill: 'forwards' });

        setTimeout(() => {
            if(globo.parentNode) globo.remove();
        }, duracion * 1000);

        globo.addEventListener('click', () => {
            globo.classList.add('explotado');
            setTimeout(() => {
                if(globo.parentNode) globo.remove();
            }, 200);

            if (texto === respuestaCorrectaTexto) {
                clearInterval(generadorGlobos);
                generadorGlobos = null;
                cielo.innerHTML = '';
                rondaActual++;
                setTimeout(iniciarRonda, 1000);
            } else {
                errores++;
                document.getElementById('txt-errores').innerText = `❌ Errores: ${errores}`;
            }
        });

        cielo.appendChild(globo);
    }

    function terminarJuego() {
        if (generadorGlobos) {
            clearInterval(generadorGlobos);
            generadorGlobos = null;
        }
        document.getElementById('zona-activa').style.display = 'none';
        document.getElementById('barra-estado').style.display = 'none';
        document.getElementById('pantalla-final').style.display = 'block';

        let msg = document.getElementById('mensaje-puntuacion');
        msg.innerText = "Explotaste los globos correctos. Tuviste " + errores + " errores.";
        
        document.getElementById('vid-espera').style.display = 'none';
        let vidFeliz = document.getElementById('vid-felicitar');
        vidFeliz.style.display = 'block'; vidFeliz.play();

        let formData = new FormData();
        formData.append('guardar_puntaje', '1');
        formData.append('errores', errores);
        fetch(window.location.href, { method: 'POST', body: formData })
            .then(r => {
                if (!r.ok) throw new Error(`Error al guardar el puntaje: HTTP ${r.status}`);
                return r.json();
            })
            .then(d => {
                if (d.status !== 'success') throw new Error(d.message || 'No se pudo guardar el puntaje.');
                document.getElementById('mensaje-guardado').style.display = 'block';
            })
            .catch(error => {
                const mensajeGuardado = document.getElementById('mensaje-guardado');
                mensajeGuardado.innerText = 'No se pudo guardar el puntaje. Inténtalo de nuevo.';
                mensajeGuardado.style.color = '#ef5350';
                mensajeGuardado.style.display = 'block';
                console.error(error);
            });
    }

    iniciarRonda();
</script>
<!-- MÚSICA DE FONDO -->
<audio id="musica-juego" loop>
    <source src="musica/muiscafondo.mp3" type="audio/mpeg">
</audio>
<script>
    const audioFondo = document.getElementById('musica-juego');
    audioFondo.volume = 0.3;
    audioFondo.play().catch(() => {
        document.body.addEventListener('click', () => {
            if (audioFondo.paused && document.getElementById('modal-pausa').style.display !== 'flex') {
                audioFondo.play().catch(error => console.error('No se pudo reproducir la música de fondo:', error));
            }
        }, { once: true });
    });
</script>
<style>
    .btn-salir-juego { position: fixed; top: 20px; right: 20px; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(5px); color: #ef5350; border: 2px solid #ef5350; padding: 10px 20px; border-radius: 15px; font-weight: 900; font-family: 'Nunito', sans-serif; font-size: 1.1rem; cursor: pointer; z-index: 9000; box-shadow: 0 5px 15px rgba(239, 83, 80, 0.15); transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .btn-salir-juego:hover { background: #ef5350; color: white; transform: scale(1.05); }
    .modal-pausa { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(200, 220, 240, 0.5); backdrop-filter: blur(10px); display: none; justify-content: center; align-items: center; z-index: 10000; font-family: 'Nunito', sans-serif; text-align: center; }
    .modal-contenido-pausa { background: rgba(255, 255, 255, 0.95); padding: 40px 50px; border-radius: 24px; border: 2px solid #fff; box-shadow: 0 15px 35px rgba(0,0,0,0.15); max-width: 450px; width: 90%; }
    .modal-contenido-pausa h2 { font-size: 2rem; color: #ef5350; margin-top: 0; font-weight: 900; }
    .texto-advertencia { font-size: 1.1rem; color: #6a8296; font-weight: 700; }
    .modal-botones { margin-top: 30px; display: flex; justify-content: center; gap: 15px; }
    .modal-botones button { padding: 12px 25px; border: none; border-radius: 12px; font-weight: 900; font-size: 1.1rem; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; font-family: 'Nunito', sans-serif; }
    .modal-botones button:hover { transform: translateY(-3px); }
    .btn-si { background: #ffebee; color: #d32f2f; border: 2px solid #ffcdd2; box-shadow: 0 5px 15px rgba(211, 47, 47, 0.1); }
    .btn-no { background: linear-gradient(135deg, #42a5f5, #1e88e5); color: white; box-shadow: 0 5px 15px rgba(30, 136, 229, 0.3); }
    #contador-pausa { font-size: 6rem; font-weight: 900; color: #66bb6a; display: none; margin: 20px 0; text-shadow: 2px 2px 10px rgba(0,0,0,0.1); }
</style>
<button class="btn-salir-juego" onclick="mostrarModalPausa()">✖ Abandonar</button>
<div id="modal-pausa" class="modal-pausa">
    <div class="modal-contenido-pausa">
        <h2 id="titulo-pausa">¿Seguro que deseas salir?</h2>
        <p id="texto-pausa" class="texto-advertencia">Si sales ahora, tu progreso en esta partida se perderá por completo.</p>
        <div class="modal-botones" id="botones-pausa">
            <button class="btn-no" onclick="iniciarConteoPausa()">No, seguir jugando</button>
            <button class="btn-si" onclick="window.location.href='index.php'">Sí, salir al menú</button>
        </div>
        <div id="contador-pausa">3</div>
    </div>
</div>
<script>
    let velOriginalPausa = null;
    let generadorPausado = false;
    let intervaloConteoPausa;

    function mostrarModalPausa() {
        clearInterval(intervaloConteoPausa);
        document.getElementById('modal-pausa').style.display = 'flex';
        document.getElementById('botones-pausa').style.display = 'flex';
        document.getElementById('texto-pausa').style.display = 'block';
        document.getElementById('contador-pausa').style.display = 'none';
        document.getElementById('titulo-pausa').innerText = '¿Seguro que deseas salir?';
        document.getElementById('titulo-pausa').style.color = '#ef5350';

        if (typeof velocidad !== 'undefined' && velocidad > 0) {
            velOriginalPausa = velocidad;
            velocidad = 0;
        }
        if (typeof generadorGlobos !== 'undefined' && generadorGlobos) {
            clearInterval(generadorGlobos);
            generadorGlobos = null;
            generadorPausado = true;
        }
        document.getAnimations().forEach(animacion => animacion.pause());
        const musica = document.getElementById('musica-juego');
        if (musica) musica.pause();
    }

    function iniciarConteoPausa() {
        document.getElementById('botones-pausa').style.display = 'none';
        document.getElementById('texto-pausa').style.display = 'none';
        document.getElementById('titulo-pausa').innerText = 'Reanudando partida en...';
        document.getElementById('titulo-pausa').style.color = '#1e88e5';
        const contadorEl = document.getElementById('contador-pausa');
        contadorEl.style.display = 'block';
        let conteo = 3;
        contadorEl.innerText = conteo;
        contadorEl.style.color = '#66bb6a';

        clearInterval(intervaloConteoPausa);
        intervaloConteoPausa = setInterval(() => {
            conteo--;
            if (conteo > 0) {
                contadorEl.innerText = conteo;
                if (conteo === 2) contadorEl.style.color = '#ffca28';
                if (conteo === 1) contadorEl.style.color = '#ef5350';
                return;
            }

            clearInterval(intervaloConteoPausa);
            document.getElementById('modal-pausa').style.display = 'none';
            if (velOriginalPausa !== null) {
                velocidad = velOriginalPausa;
                velOriginalPausa = null;
            }
            if (typeof generadorGlobos !== 'undefined' && generadorPausado) {
                generadorGlobos = setInterval(() => {
                    if (indiceOpcion >= opcionesActuales.length) {
                        opcionesActuales.sort(() => Math.random() - 0.5);
                        indiceOpcion = 0;
                    }
                    crearGlobo(opcionesActuales[indiceOpcion]);
                    indiceOpcion++;
                }, 1000);
                generadorPausado = false;
            }
            document.getAnimations().forEach(animacion => animacion.play());
            const musica = document.getElementById('musica-juego');
            if (musica) musica.play().catch(error => console.error('No se pudo reanudar la música:', error));
        }, 1000);
    }
</script>
</body>
</html>