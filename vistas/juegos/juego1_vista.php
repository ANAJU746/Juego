<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesca Técnica - Arcade</title>
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
    <script src="../js/arcade-cursor.js" defer></script>
</head>
<body class="juego-1">
    <video autoplay loop muted playsinline id="video-fondo-global">
        <source src="../videos/FondoAnimado1.webm" type="video/webm">
    </video>
<div id="flash-rojo"></div>

<div class="contenedor-juego1" id="app-juego">
    
    <div class="stats-bar" id="barra-estado">
        <div id="contenedor-vidas">
            <span class="corazon" id="vida-5">❤️</span>
            <span class="corazon" id="vida-4">❤️</span>
            <span class="corazon" id="vida-3">❤️</span>
            <span class="corazon" id="vida-2">❤️</span>
            <span class="corazon" id="vida-1">❤️</span>
        </div>
        <span style="color: #42a5f5;" id="txt-ronda">Ronda: 1/5</span>
        <span style="color: #4caf50;" id="txt-tiempo">⏱️ 20s</span>
    </div>

    <div id="zona-activa">
        <div id="instruccion">¡Lanza tu anzuelo a la respuesta correcta!</div>
        <div class="pregunta-cartel" id="txt-pregunta">Cargando pregunta...</div>
        <div class="zona-rio" id="rio-digital"></div>
    </div>

    <div id="pantalla-final">
        <h2 style="font-size: 2.5rem; color: #1e88e5; margin-bottom: 10px;">¡Pesca Terminada!</h2>
        <p id="mensaje-puntuacion" style="font-weight: 700; color: #6a8296; font-size: 1.2rem;"></p>
        <p id="mensaje-guardado" style="color: #4caf50; font-weight: 800; display: none;">✅ Puntaje guardado exitosamente</p>
        <a href="../index.php" class="boton-accion" style="margin-top: 20px;">Menú Principal</a>
        <button onclick="location.reload()" class="boton-accion verde" style="border:none; cursor:pointer; margin-top: 20px;">Volver a pescar</button>
    </div>
</div>

<div style="position: fixed; bottom: -10px; left: 20px; width: 250px; z-index: 1000; pointer-events: none;">
    <video id="vid-espera" autoplay loop muted playsinline style="width: 100%; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); display: block;">
        <source src="../videos/Videoespera.webm" type="video/webm">
    </video>
    <video id="vid-felicitar" muted playsinline style="width: 100%; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); display: none;">
        <source src="../videos/VideoFelicitar.webm" type="video/webm">
    </video>
</div>

<script>
    // Las preguntas ahora vienen 100% de la Base de Datos
    const datosJuego = <?php echo json_encode($preguntas, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    
    let rondaActual = 0;
    let errores = 0;
    let vidas = 5;
    let juegoFinalizado = false;
    let sinVidas = false;
    let contextoAudioError;
    let timeoutFlashRojo;
    let tiempoRestante = 20;
    let timerPregunta;
    let permitirPesca = false;
    let respuestaCorrecta = "";

    const elPregunta = document.getElementById('txt-pregunta');
    const elTiempo = document.getElementById('txt-tiempo');
    const rio = document.getElementById('rio-digital');

    function iniciarRonda() {
        if(rondaActual >= datosJuego.length) {
            terminarJuego();
            return;
        }

        rio.innerHTML = ''; 
        let pregunta = datosJuego[rondaActual];
        elPregunta.innerText = pregunta.pregunta;
        respuestaCorrecta = pregunta.correcta;
        document.getElementById('txt-ronda').innerText = `Ronda: ${rondaActual + 1}/${datosJuego.length}`;

        tiempoRestante = 20;
        elTiempo.innerText = `⏱️ ${tiempoRestante}s`;
        elTiempo.classList.remove('alerta-suave', 'alerta-dramatica');
        permitirPesca = true;

        clearInterval(timerPregunta);
        timerPregunta = setInterval(() => {
            tiempoRestante--;
            elTiempo.innerText = `⏱ ${tiempoRestante}s`;
            actualizarAlertaTiempo(tiempoRestante);
            if(tiempoRestante <= 0) {
                clearInterval(timerPregunta);
                registrarError();
                if (sinVidas) return;
                rondaActual++;
                iniciarRonda();
            }
        }, 1000);

        let opciones = [pregunta.correcta, pregunta.falsas[0], pregunta.falsas[1]];
        opciones.sort(() => Math.random() - 0.5);

        opciones.forEach((texto, index) => {
            crearPez(texto, index);
        });
    }

    function crearPez(texto, index) {
        let pez = document.createElement('div');
        pez.classList.add('pez-concepto');
        pez.innerText = texto;
        pez.setAttribute('data-valor', texto);

        let direccion = Math.random() > 0.5 ? 'der' : 'izq';
        let velocidad = (Math.random() * 5 + 8) + 's'; 
        
        let alturas = [10, 40, 70]; 
        pez.style.top = alturas[index] + '%';
        pez.style.animation = `nadar-${direccion} ${velocidad} linear infinite`;

        pez.addEventListener('click', function() {
            if(!permitirPesca) return;

            let valorPescado = this.getAttribute('data-valor');

            if(valorPescado === respuestaCorrecta) {
                permitirPesca = false;
                clearInterval(timerPregunta);
                lanzarAcierto();
                
                this.style.animationPlayState = 'paused';
                this.classList.add('pez-acierto');

                document.querySelectorAll('.pez-concepto').forEach(p => {
                    if(p !== this) p.style.opacity = '0';
                });

                setTimeout(() => {
                    rondaActual++;
                    iniciarRonda();
                }, 1500);

            } else {
                registrarError();
                if (sinVidas) return;
                this.style.animationPlayState = 'paused';
                this.classList.add('pez-error');
                
                setTimeout(() => {
                    this.classList.remove('pez-error');
                    this.style.animationPlayState = 'running';
                }, 800);
            }
        });

        rio.appendChild(pez);
    }

    function registrarError() {
        restarVida();
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
            permitirPesca = false;
            clearInterval(timerPregunta);
            setTimeout(terminarJuego, 600);
            return false;
        }
        return true;
    }

    function terminarJuego() {
        if (juegoFinalizado) return;
        juegoFinalizado = true;
        clearInterval(timerPregunta);
        permitirPesca = false;

        document.getElementById('zona-activa').style.display = 'none';
        document.getElementById('barra-estado').style.display = 'none';
        document.getElementById('pantalla-final').style.display = 'block';

        let msg = document.getElementById('mensaje-puntuacion');
        if (sinVidas) {
            msg.innerText = "¡Te quedaste sin vidas! Tuviste " + errores + " errores.";
            msg.style.color = '#ef5350';
        } else if (errores <= 2) {
            msg.innerText = "¡Excelente pesca! Solo fallaste " + errores + " veces.";
            let vidEspera = document.getElementById('vid-espera');
            if(vidEspera) vidEspera.style.display = 'none';
            let vidFeliz = document.getElementById('vid-felicitar');
            if(vidFeliz) { vidFeliz.style.display = 'block'; vidFeliz.play(); }
            lanzarConfeti();
        } else {
            msg.innerText = "El anzuelo se trabó un poco. Tuviste " + errores + " errores. ¡Sigue practicando!";
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
<!-- ======================================================= -->
<!-- SISTEMA UNIVERSAL DE PAUSA Y SALIDA -->
<!-- ======================================================= -->

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
            if (velOriginalPausa !== null) {
                velocidad = velOriginalPausa;
                velOriginalPausa = null;
            }
            if (typeof generadorGlobos !== 'undefined' && generadorPausado) {
                generadorGlobos = setInterval(() => {
                    if (typeof opcionesActuales === 'undefined' || typeof indiceOpcion === 'undefined') return;
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