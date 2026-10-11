<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiro al Blanco - Arcade</title>
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
    <script src="../js/arcade-cursor.js" defer></script>
</head>
<body class="juego-3">
    <video autoplay loop muted playsinline id="video-fondo-global">
        <source src="../videos/FondoAnimado1.webm" type="video/webm">
    </video>
<div id="flash-rojo"></div>

<div class="contenedor-juego3" id="app-juego">
    
    <div class="stats-bar" id="barra-estado">
        <div id="contenedor-vidas">
            <span class="corazon" id="vida-5">❤️</span>
            <span class="corazon" id="vida-4">❤️</span>
            <span class="corazon" id="vida-3">❤️</span>
            <span class="corazon" id="vida-2">❤️</span>
            <span class="corazon" id="vida-1">❤️</span>
        </div>
        <span style="color: #42a5f5;" id="txt-ronda">Ronda: 1/5</span>
        <span style="color: #4caf50;" id="txt-tiempo">⏱️ 0s</span>
    </div>

    <div id="zona-activa">
        <div class="pregunta-texto" id="txt-pregunta">Cargando...</div>
        
        <div id="instruccion">¡Memoriza las respuestas!</div>

        <div class="zona-cartas" id="tablero">
            <div class="carta-wrapper" id="c0" style="left: 0%;">
                <div class="carta-inner" id="inner0">
                    <div class="carta-frente" id="frente0"></div>
                    <div class="carta-dorso"></div>
                </div>
            </div>
            <div class="carta-wrapper" id="c1" style="left: 35%;">
                <div class="carta-inner" id="inner1">
                    <div class="carta-frente" id="frente1"></div>
                    <div class="carta-dorso"></div>
                </div>
            </div>
            <div class="carta-wrapper" id="c2" style="left: 70%;">
                <div class="carta-inner" id="inner2">
                    <div class="carta-frente" id="frente2"></div>
                    <div class="carta-dorso"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="pantalla-final">
        <h2 style="font-size: 2.5rem; color: #1e88e5; margin-bottom: 10px;">¡Misión Completada!</h2>
        <p id="mensaje-puntuacion" style="font-weight: 700; color: #6a8296; font-size: 1.2rem;"></p>
        <p id="mensaje-guardado" style="color: #4caf50; font-weight: 800; display: none;">✅ Puntaje guardado exitosamente</p>
        <a href="../index.php" class="boton-accion" style="margin-top: 20px;">Menú Principal</a>
        <button onclick="location.reload()" class="boton-accion verde" style="border:none; cursor:pointer; margin-top: 20px;">Volver a disparar</button>
    </div>

</div>

<!-- VIDEOS DE LA MASCOTA -->
<div style="position: fixed; bottom: -10px; left: 20px; width: 250px; z-index: 1000; pointer-events: none;">
    <video id="vid-espera" autoplay loop muted playsinline style="width: 100%; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); display: block;">
        <source src="../videos/Videoespera.webm" type="video/webm">
    </video>
    <video id="vid-felicitar" muted playsinline style="width: 100%; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); display: none;">
        <source src="../videos/VideoFelicitar.webm" type="video/webm">
    </video>
</div>

<script>
    const datosJuego = <?php echo json_encode($preguntas, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    
    let rondaActual = 0;
    let errores = 0;
    let vidas = 5;
    let juegoFinalizado = false;
    let sinVidas = false;
    let contextoAudioError;
    let timeoutFlashRojo;
    let permitirdisparo = false;
    let timerDisparo;
    let timerMemorizar;
    let tiempoRestante = 0;
    let respuestaCorrectaTexto = "";

    const elPregunta = document.getElementById('txt-pregunta');
    const elInstruccion = document.getElementById('instruccion');
    const elTiempo = document.getElementById('txt-tiempo');
    const innerCartas = [document.getElementById('inner0'), document.getElementById('inner1'), document.getElementById('inner2')];
    const frenteCartas = [document.getElementById('frente0'), document.getElementById('frente1'), document.getElementById('frente2')];
    const wrapperCartas = [document.getElementById('c0'), document.getElementById('c1'), document.getElementById('c2')];

    function iniciarRonda() {
        if(rondaActual >= datosJuego.length) {
            terminarJuego();
            return;
        }

        permitirdisparo = false;
        document.getElementById('txt-ronda').innerText = `Ronda: ${rondaActual + 1}/${datosJuego.length}`;
        elInstruccion.innerText = "¡Memoriza las respuestas!";
        elInstruccion.style.color = "#1e88e5";
        
        tiempoRestante = 8;
        elTiempo.innerText = `⏱️ ${tiempoRestante}s`;
        elTiempo.classList.remove('alerta-suave', 'alerta-dramatica');

        let pregunta = datosJuego[rondaActual];
        elPregunta.innerText = pregunta.pregunta;
        respuestaCorrectaTexto = pregunta.correcta;

        let opciones = [pregunta.correcta, pregunta.falsas[0], pregunta.falsas[1]];
        opciones.sort(() => Math.random() - 0.5);

        for(let i=0; i<3; i++) {
            innerCartas[i].classList.remove('volteada');
            frenteCartas[i].innerText = opciones[i];
            frenteCartas[i].classList.remove('efecto-acierto', 'efecto-error');
        }

        timerMemorizar = setInterval(() => {
            tiempoRestante--;
            elTiempo.innerText = `⏱️ ${tiempoRestante}s`;
            actualizarAlertaTiempo(tiempoRestante);
            
            if(tiempoRestante <= 0) {
                clearInterval(timerMemorizar);
                faseRevolver();
            }
        }, 1000);
    }

    function faseRevolver() {
        elInstruccion.innerText = "Revolviendo...";
        elInstruccion.style.color = "#ef5350";
        elTiempo.innerText = "⏱️ -";
        
        for(let i=0; i<3; i++) {
            innerCartas[i].classList.add('volteada');
        }

        setTimeout(() => {
            let movimientos = 0;
            let intervaloRevuelvo = setInterval(() => {
                let a = Math.floor(Math.random() * 3);
                let b = Math.floor(Math.random() * 3);
                
                let tempLeft = wrapperCartas[a].style.left;
                wrapperCartas[a].style.left = wrapperCartas[b].style.left;
                wrapperCartas[b].style.left = tempLeft;

                movimientos++;
                if(movimientos >= 6) {
                    clearInterval(intervaloRevuelvo);
                    setTimeout(faseDisparo, 500); 
                }
            }, 400);
        }, 600);
    }

    function faseDisparo() {
        elInstruccion.innerText = "¡DISPARA A LA CORRECTA!";
        elInstruccion.style.color = "#4caf50";
        permitirdisparo = true;
        
        tiempoRestante = 5; 
        elTiempo.innerText = `⏱️ ${tiempoRestante}s`;
        elTiempo.classList.remove('alerta-suave', 'alerta-dramatica');
        actualizarAlertaTiempo(tiempoRestante);

        timerDisparo = setInterval(() => {
            tiempoRestante--;
            elTiempo.innerText = `⏱️ ${tiempoRestante}s`;
            actualizarAlertaTiempo(tiempoRestante);
            if(tiempoRestante <= 0) {
                clearInterval(timerDisparo);
                procesarDisparo(null); 
            }
        }, 1000);
    }

    wrapperCartas.forEach((wrapper, index) => {
        wrapper.addEventListener('click', () => {
            if(!permitirdisparo) return;
            clearInterval(timerDisparo);
            procesarDisparo(index);
        });
    });

    function procesarDisparo(indiceDisparado) {
        permitirdisparo = false;
        elTiempo.innerText = "⏱️ -";

        for(let i=0; i<3; i++) {
            innerCartas[i].classList.remove('volteada');
        }

        let atino = false;
        if(indiceDisparado !== null && frenteCartas[indiceDisparado].innerText === respuestaCorrectaTexto) {
            atino = true;
        }

        if(atino) {
            lanzarAcierto();
            elInstruccion.innerText = "¡BLANCO PERFECTO!";
            elInstruccion.style.color = "#4caf50";
            frenteCartas[indiceDisparado].classList.add('efecto-acierto');
        } else {
            restarVida();
            elInstruccion.innerText = "¡FALLASTE!";
            elInstruccion.style.color = "#ef5350";
            
            if(indiceDisparado !== null) {
                frenteCartas[indiceDisparado].classList.add('efecto-error');
            }
            if (sinVidas) return;
        }

        rondaActual++;
        setTimeout(iniciarRonda, 2000); 
    }

    function reproducirSonidoError() {
        try {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (!AudioContextClass) throw new Error('Web Audio API no está disponible.');
            contextoAudioError = contextoAudioError || new AudioContextClass();
            if (contextoAudioError.state === 'suspended') contextoAudioError.resume();

            const oscilador = contextoAudioError.createOscillator();
            const ganancia = contextoAudioError.createGain();
            oscilador.connect(ganancia);
            ganancia.connect(contextoAudioError.destination);
            oscilador.type = 'sawtooth';
            oscilador.frequency.setValueAtTime(150, contextoAudioError.currentTime);
            oscilador.frequency.exponentialRampToValueAtTime(40, contextoAudioError.currentTime + 0.3);
            ganancia.gain.setValueAtTime(0.3, contextoAudioError.currentTime);
            ganancia.gain.exponentialRampToValueAtTime(0.01, contextoAudioError.currentTime + 0.3);
            oscilador.start();
            oscilador.stop(contextoAudioError.currentTime + 0.3);
        } catch (error) {
            console.error('No se pudo reproducir el sonido de error:', error);
        }
    }

    function restarVida() {
        if (vidas <= 0 || juegoFinalizado || sinVidas) return false;

        errores++;
        lanzarError();

        document.getElementById(`vida-${vidas}`).classList.add('corazon-perdido');
        vidas--;
        if (vidas === 0) {
            sinVidas = true;
            permitirdisparo = false;
            clearInterval(timerDisparo);
            clearInterval(timerMemorizar);
            setTimeout(terminarJuego, 600);
            return false;
        }
        return true;
    }

    function terminarJuego() {
        if (juegoFinalizado) return;
        juegoFinalizado = true;
        clearInterval(timerDisparo);
        clearInterval(timerMemorizar);
        document.getElementById('zona-activa').style.display = 'none';
        document.getElementById('barra-estado').style.display = 'none';
        document.getElementById('pantalla-final').style.display = 'block';

        let msg = document.getElementById('mensaje-puntuacion');
        
        if (sinVidas) {
            document.querySelector('#pantalla-final h2').innerText = '¡Juego Terminado!';
            msg.innerText = "¡Te quedaste sin vidas! Tuviste " + errores + " errores.";
            msg.style.color = '#ef5350';
        } else if (errores <= 1) {
            msg.innerText = "¡Eres un francotirador! Tuviste " + errores + " errores.";
            let vidEspera = document.getElementById('vid-espera');
            if (vidEspera) vidEspera.style.display = 'none';
            let vidFeliz = document.getElementById('vid-felicitar');
            if (vidFeliz) { vidFeliz.style.display = 'block'; vidFeliz.play(); }
            lanzarConfeti();
        } else {
            msg.innerText = "Terminaste, pero fallaste " + errores + " tiros. ¡Apunta mejor la próxima!";
        }

        // --- GUARDADO SILENCIOSO EN LA BASE DE DATOS ---
        let formData = new FormData();
        formData.append('guardar_puntaje', '1');
        formData.append('errores', errores);

        fetch(window.location.href, { 
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.proximo_juego) {
                    msg.innerText += " ¡Siguiente juego en 3 segundos!";
                    setTimeout(() => window.location.replace(`juego${data.proximo_juego}.php?fiesta=1`), 3000);
                    return;
                }
                if (data.fiesta_completada) {
                    msg.innerText += " ¡Completaste la Fiesta!";
                    setTimeout(() => window.location.replace('lobby.php'), 4000);
                    return;
                }
                document.getElementById('mensaje-guardado').style.display = 'block';
            } else {
                console.error("Error BD:", data.message);
            }
        })
        .catch(err => console.error("Fallo la conexión con PHP:", err));
    }

    function lanzarConfeti() {
        const colores = ['#42a5f5', '#4caf50', '#ffeb3b', '#ef5350', '#ffffff'];
        for (let i = 0; i < 100; i++) {
            let confeti = document.createElement('div');
            confeti.style.position = 'fixed'; confeti.style.top = '-30px';
            confeti.style.left = Math.random() * 100 + 'vw';
            confeti.style.zIndex = '9999'; confeti.style.pointerEvents = 'none';
            confeti.style.width = (Math.floor(Math.random() * 10) + 6) + 'px';
            confeti.style.height = (Math.floor(Math.random() * 15) + 8) + 'px';
            confeti.style.backgroundColor = colores[Math.floor(Math.random() * colores.length)];
            if (Math.random() > 0.5) confeti.style.borderRadius = '50%';
            document.body.appendChild(confeti);
            let duracion = (Math.random() * 3 + 2.5) * 1000;
            let oscilacion = Math.floor(Math.random() * 300) - 150;
            confeti.animate([
                { transform: 'translate3d(0, 0, 0) rotate(0deg)', opacity: 1 },
                { transform: `translate3d(${oscilacion}px, 110vh, 0) rotate(720deg)`, opacity: 0.8 }
            ], { duration: duracion, easing: 'ease-in', fill: 'forwards' });
            setTimeout(() => confeti.remove(), duracion);
        }
    }

    iniciarRonda();
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
    <button class="btn-salir-juego" onclick="mostrarModalPausa()">✖ Abandonar</button>
    <div id="modal-pausa" class="modal-pausa">
        <div class="modal-contenido-pausa">
            <h2 id="titulo-pausa">¿Seguro que deseas salir?</h2>
            <p id="texto-pausa" class="texto-advertencia">Si sales ahora, tu progreso en esta partida se perderá por completo.</p>
            <div class="modal-botones" id="botones-pausa">
                <button class="btn-no" onclick="iniciarConteoPausa()">No, seguir jugando</button>
                <button class="btn-si" onclick="window.location.href='../index.php'">Sí, salir al menú</button>
            </div>
            <div id="contador-pausa">3</div>
        </div>
    </div>
    <script>
        let velOriginalPausa = null;
        let generadorPausado = false;
        let intervaloConteoPausa = null;
        function mostrarModalPausa() {
            clearInterval(intervaloConteoPausa);
            intervaloConteoPausa = null;
            document.getElementById('contador-pausa').classList.remove('alerta-dramatica');
            document.getElementById('modal-pausa').style.display = 'flex';
            document.getElementById('botones-pausa').style.display = 'flex';
            document.getElementById('texto-pausa').style.display = 'block';
            document.getElementById('contador-pausa').style.display = 'none';
            document.getElementById('titulo-pausa').innerText = '¿Seguro que deseas salir?';
            document.getElementById('titulo-pausa').style.color = '#ef5350';
            if (typeof velocidad !== 'undefined' && velocidad > 0) { velOriginalPausa = velocidad; velocidad = 0; }
            if (typeof generadorGlobos !== 'undefined' && generadorGlobos) { clearInterval(generadorGlobos); generadorGlobos = null; generadorPausado = true; }
            document.getAnimations().forEach(animacion => animacion.pause());
            const musica = document.getElementById('musica-juego');
            if (musica) musica.pause();
        }
        function iniciarConteoPausa() {
            if (intervaloConteoPausa !== null) return;
            document.getElementById('botones-pausa').style.display = 'none';
            document.getElementById('texto-pausa').style.display = 'none';
            document.getElementById('titulo-pausa').innerText = 'Reanudando partida en...';
            document.getElementById('titulo-pausa').style.color = '#1e88e5';
            const contadorEl = document.getElementById('contador-pausa');
            contadorEl.style.display = 'block';
            let conteo = 3;
            contadorEl.innerText = conteo;
            contadorEl.style.color = '#66bb6a';
            contadorEl.classList.add('alerta-dramatica');
            try {
                if (typeof reproducirTick === 'function') reproducirTick();
            } catch (error) {
                console.error('No se pudo reproducir el tic del conteo:', error);
            }

            clearInterval(intervaloConteoPausa);
            intervaloConteoPausa = setInterval(() => {
                conteo--;
                if (conteo > 0) {
                    try {
                        if (typeof reproducirTick === 'function') reproducirTick();
                    } catch (error) {
                        console.error('No se pudo reproducir el tic del conteo:', error);
                    }
                    contadorEl.style.animation = 'none';
                    void contadorEl.offsetHeight;
                    contadorEl.style.animation = '';
                    contadorEl.innerText = conteo;
                    if (conteo === 2) contadorEl.style.color = '#ffca28';
                    if (conteo === 1) contadorEl.style.color = '#ef5350';
                    return;
                }

                clearInterval(intervaloConteoPausa);
                intervaloConteoPausa = null;
                contadorEl.classList.remove('alerta-dramatica');
                document.getElementById('modal-pausa').style.display = 'none';
                if (velOriginalPausa !== null) { velocidad = velOriginalPausa; velOriginalPausa = null; }
                if (typeof generadorGlobos !== 'undefined' && generadorPausado) {
                    generadorGlobos = setInterval(() => {
                        if (typeof opcionesActuales === 'undefined' || typeof indiceOpcion === 'undefined') return;
                        if (indiceOpcion >= opcionesActuales.length) { opcionesActuales.sort(() => Math.random() - 0.5); indiceOpcion = 0; }
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
    <!-- SISTEMA UNIVERSAL DE FEEDBACK -->
    <div id="flash-feedback"></div>
    <div id="contenedor-particulas"></div>
    <script>
        let timeoutFeedbackFlash;
        function reproducirSonidoAcierto() {
            try {
                const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                if (!AudioContextClass) throw new Error('Web Audio API no está disponible.');
                contextoAudioError = contextoAudioError || new AudioContextClass();
                if (contextoAudioError.state === 'suspended') contextoAudioError.resume();
                const oscilador = contextoAudioError.createOscillator();
                const ganancia = contextoAudioError.createGain();
                oscilador.connect(ganancia);
                ganancia.connect(contextoAudioError.destination);
                oscilador.type = 'sine';
                oscilador.frequency.setValueAtTime(600, contextoAudioError.currentTime);
                oscilador.frequency.exponentialRampToValueAtTime(1200, contextoAudioError.currentTime + 0.1);
                ganancia.gain.setValueAtTime(0, contextoAudioError.currentTime);
                ganancia.gain.linearRampToValueAtTime(0.3, contextoAudioError.currentTime + 0.05);
                ganancia.gain.exponentialRampToValueAtTime(0.01, contextoAudioError.currentTime + 0.4);
                oscilador.start();
                oscilador.stop(contextoAudioError.currentTime + 0.4);
            } catch (error) {
                console.error('No se pudo reproducir el sonido de acierto:', error);
            }
        }
        function reproducirTick() {
            try {
                const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                if (!AudioContextClass) throw new Error('Web Audio API no está disponible.');
                contextoAudioError = contextoAudioError || new AudioContextClass();
                if (contextoAudioError.state === 'suspended') contextoAudioError.resume();
                const oscilador = contextoAudioError.createOscillator();
                const ganancia = contextoAudioError.createGain();
                oscilador.connect(ganancia);
                ganancia.connect(contextoAudioError.destination);
                oscilador.type = 'triangle';
                oscilador.frequency.setValueAtTime(1000, contextoAudioError.currentTime);
                oscilador.frequency.exponentialRampToValueAtTime(100, contextoAudioError.currentTime + 0.1);
                ganancia.gain.setValueAtTime(0.6, contextoAudioError.currentTime);
                ganancia.gain.exponentialRampToValueAtTime(0.01, contextoAudioError.currentTime + 0.1);
                oscilador.start(contextoAudioError.currentTime);
                oscilador.stop(contextoAudioError.currentTime + 0.1);
            } catch (error) {
                console.error('No se pudo reproducir el tic del temporizador:', error);
            }
        }
        function actualizarAlertaTiempo(tiempo) {
                const temporizador = document.getElementById('txt-tiempo');
                if (tiempo <= 5 && tiempo > 0) {
                    temporizador.classList.remove('alerta-suave');
                    temporizador.classList.add('alerta-dramatica');
                    reproducirTick();
                } else if (tiempo <= 10 && tiempo > 5) {
                    temporizador.classList.add('alerta-suave');
                    temporizador.classList.remove('alerta-dramatica');
                } else {
                    temporizador.classList.remove('alerta-suave', 'alerta-dramatica');
                }
        }
        function mostrarFlashFeedback(colorRGBA) {
            const flash = document.getElementById('flash-feedback');
            flash.style.background = colorRGBA;
            flash.style.opacity = '1';
            clearTimeout(timeoutFeedbackFlash);
            timeoutFeedbackFlash = setTimeout(() => { flash.style.opacity = '0'; }, 300);
        }
        function generarParticulas(arraySimbolos, cantidad) {
            const contenedor = document.getElementById('contenedor-particulas');
            for (let i = 0; i < cantidad; i++) {
                const particula = document.createElement('div');
                particula.className = 'particula-feedback';
                particula.innerText = arraySimbolos[Math.floor(Math.random() * arraySimbolos.length)];
                particula.style.left = (Math.random() * 80 + 10) + 'vw';
                particula.style.top = (Math.random() * 80 + 10) + 'vh';
                const escalaAleatoria = Math.random() * 0.5 + 0.8;
                particula.style.fontSize = `${3.5 * escalaAleatoria}rem`;
                particula.style.animationDelay = (Math.random() * 0.2) + 's';
                contenedor.appendChild(particula);
                setTimeout(() => particula.remove(), 1500);
            }
        }
        function lanzarAcierto() {
            reproducirSonidoAcierto();
            mostrarFlashFeedback('rgba(102, 187, 106, 0.25)');
            generarParticulas(['✅', '✨', '⭐', '🎉'], 5);
        }
        function lanzarError() {
            reproducirSonidoError();
            mostrarFlashFeedback('rgba(239, 83, 80, 0.25)');
            generarParticulas(['❌', '⚠️', '❗', '💥'], 4);
            document.body.classList.remove('feedback-sacudida');
            void document.body.offsetWidth;
            document.body.classList.add('feedback-sacudida');
        }
    </script>
</body>
</html>