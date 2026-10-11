<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torre de Conceptos - Arcade</title>
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
    <script src="../js/arcade-cursor.js" defer></script>
</head>
<body class="juego-5">
    <video autoplay loop muted playsinline id="video-fondo-global">
        <source src="../videos/FondoAnimado1.webm" type="video/webm">
    </video>
<div id="flash-rojo"></div>

<div class="contenedor-juego">
    <div class="stats-bar" id="barra-estado">
        <div id="contenedor-vidas">
            <span class="corazon" id="vida-5">❤️</span>
            <span class="corazon" id="vida-4">❤️</span>
            <span class="corazon" id="vida-3">❤️</span>
            <span class="corazon" id="vida-2">❤️</span>
            <span class="corazon" id="vida-1">❤️</span>
        </div>
        <span style="color: #66bb6a;" id="txt-ronda">Bloques: 0/5</span>
    </div>
    
    <div id="zona-activa" style="display:flex; flex-direction:column; flex-grow:1;">
        <div class="pregunta-texto" id="txt-pregunta">Cargando...</div>
        <div class="instruccion">Haz CLIC directamente en el BLOQUE cuando tenga la respuesta correcta.</div>
        
        <div id="area-apilado">
            <!-- El evento onclick ahora está dentro del bloque -->
            <div id="bloque-movil" class="bloque" onclick="soltarBloque()"></div>
            <div id="contenedor-torre">
                <div class="bloque bloque-apilado" style="background: #78909c;">BASE DE LA TORRE</div>
            </div>
        </div>
    </div>

    <div id="pantalla-final">
        <h2 style="font-size: 2.5rem; color: #1e88e5;">¡Torre Completada! 🏢</h2>
        <p id="mensaje-puntuacion" style="font-weight: 700; color: #2c3e50; font-size: 1.2rem;"></p>
        <p id="mensaje-guardado" style="color: #66bb6a; font-weight: 800; display: none;">✅ Puntaje guardado exitosamente</p>
        <a href="../index.php" class="boton-accion">Volver al Menú</a>
    </div>
</div>

<!-- MASCOTA -->
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
    
    let opcionesActuales = [];
    let indiceOpcion = 0;
    let moviendo = false;
    let x = 0;
    let direccion = 1;
    let velocidad = 1.5;
    let animacionFrame;
    
    const bloqueMovil = document.getElementById('bloque-movil');
    const areaApilado = document.getElementById('area-apilado');
    const contenedorTorre = document.getElementById('contenedor-torre');
    const colores = ['#ef5350', '#ab47bc', '#42a5f5', '#26c6da', '#66bb6a', '#ffa726', '#ff7043'];

    function iniciarRonda() {
        if(rondaActual >= datosJuego.length) { terminarJuego(); return; }

        let pregunta = datosJuego[rondaActual];
        document.getElementById('txt-pregunta').innerText = pregunta.pregunta;
        document.getElementById('txt-ronda').innerText = `Bloques: ${rondaActual}/${datosJuego.length}`;

        opcionesActuales = [pregunta.correcta, pregunta.falsas[0], pregunta.falsas[1]];
        opcionesActuales.sort(() => Math.random() - 0.5);
        indiceOpcion = 0;

        prepararBloqueMovil();
    }

    function prepararBloqueMovil() {
        bloqueMovil.style.background = colores[Math.floor(Math.random() * colores.length)];
        bloqueMovil.innerText = opcionesActuales[indiceOpcion];
        x = 0;
        direccion = 1;
        bloqueMovil.style.display = 'flex';
        bloqueMovil.style.top = '20px'; // Reiniciar altura
        moviendo = true;
        moverBloque();
    }

    function moverBloque() {
        if(!moviendo || juegoFinalizado || sinVidas) return;
        
        let limiteMaximo = areaApilado.clientWidth - bloqueMovil.clientWidth;
        x += velocidad * direccion;

        if(x >= limiteMaximo || x <= 0) {
            direccion *= -1;
            indiceOpcion = (indiceOpcion + 1) % opcionesActuales.length;
            bloqueMovil.innerText = opcionesActuales[indiceOpcion];
            bloqueMovil.style.background = colores[Math.floor(Math.random() * colores.length)];
        }

        bloqueMovil.style.left = x + 'px';
        animacionFrame = requestAnimationFrame(moverBloque);
    }

    function soltarBloque() {
        if(!moviendo) return; 
        moviendo = false; // Bloquea clics adicionales
        cancelAnimationFrame(animacionFrame); // Detiene el movimiento lateral

        // CÁLCULO DE FÍSICA: Dónde debe caer (Centro X, y encima de la torre en Y)
        let targetX = (areaApilado.clientWidth / 2) - 110; // 110 es la mitad del ancho del bloque
        let targetY = areaApilado.clientHeight - contenedorTorre.clientHeight - 45; // 45 es la altura del bloque

        // ANIMACIÓN DE CAÍDA
        let caidaAnimacion = bloqueMovil.animate([
            { left: x + 'px', top: '20px' },
            { left: targetX + 'px', top: targetY + 'px' }
        ], { duration: 350, easing: 'ease-in' });

        // Qué pasa justo cuando el bloque "toca" la torre:
        caidaAnimacion.onfinish = () => {
            bloqueMovil.style.display = 'none'; // Escondemos el móvil
            
            let textoSeleccionado = opcionesActuales[indiceOpcion];
            let respuestaCorrecta = datosJuego[rondaActual].correcta;

            if (textoSeleccionado === respuestaCorrecta) {
                lanzarAcierto();
                // APILAR CORRECTAMENTE
                let nuevoBloque = document.createElement('div');
                nuevoBloque.className = 'bloque bloque-apilado';
                nuevoBloque.style.background = bloqueMovil.style.background;
                nuevoBloque.innerText = textoSeleccionado;
                contenedorTorre.appendChild(nuevoBloque); // Se une mágicamente a la torre
                
                rondaActual++;
                setTimeout(iniciarRonda, 300); // Pausa breve y lanza el siguiente
            } else {
                // ERROR: LA TORRE SE CAE
                restarVida();
                if (sinVidas) return;
                
                // Efecto visual a los bloques para que salgan volando
                let bloques = contenedorTorre.querySelectorAll('.bloque-apilado');
                for(let i = 1; i < bloques.length; i++) {
                    bloques[i].classList.add('derrumbe');
                    let direccionCaida = (Math.random() > 0.5) ? 200 : -200;
                    bloques[i].style.transform = `translate(${direccionCaida}px, 500px) rotate(${Math.random()*180}deg)`;
                }

                setTimeout(() => {
                    contenedorTorre.innerHTML = '<div class="bloque bloque-apilado" style="background: #78909c;">BASE DE LA TORRE</div>';
                    rondaActual = 0; 
                    iniciarRonda();
                }, 1000);
            }
        };
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
            moviendo = false;
            cancelAnimationFrame(animacionFrame);
            setTimeout(terminarJuego, 600);
            return false;
        }
        return true;
    }

    function terminarJuego() {
        if (juegoFinalizado) return;
        juegoFinalizado = true;
        document.getElementById('zona-activa').style.display = 'none';
        document.getElementById('barra-estado').style.display = 'none';
        document.getElementById('pantalla-final').style.display = 'block';

        const mensajePuntuacion = document.getElementById('mensaje-puntuacion');
        if (sinVidas) {
            document.querySelector('#pantalla-final h2').innerText = '¡Torre Derrumbada!';
            mensajePuntuacion.innerText = `¡Te quedaste sin vidas! Tuviste ${errores} errores.`;
            mensajePuntuacion.style.color = '#ef5350';
        } else {
            mensajePuntuacion.innerText = `Construiste la torre con ${errores} caídas.`;
        }
        if (!sinVidas) {
            document.getElementById('vid-espera').style.display = 'none';
            const vidFelicitar = document.getElementById('vid-felicitar');
            vidFelicitar.style.display = 'block';
            vidFelicitar.play().catch(error => console.error('No se pudo reproducir el video de felicitación:', error));
        }
        
        let formData = new FormData();
        formData.append('guardar_puntaje', '1');
        formData.append('errores', errores);
        fetch(window.location.href, { method: 'POST', body: formData })
        .then(r => r.json()).then(d => {
            if (d.status !== 'success') throw new Error(d.message || 'No se pudo guardar el puntaje.');
            if (d.proximo_juego) {
                mensajePuntuacion.innerText += ' ¡Siguiente juego en 3 segundos!';
                setTimeout(() => window.location.replace(`juego${d.proximo_juego}.php?fiesta=1`), 3000);
                return;
            }
            if (d.fiesta_completada) {
                mensajePuntuacion.innerText += ' ¡Completaste la Fiesta!';
                setTimeout(() => window.location.replace('lobby.php'), 4000);
                return;
            }
            document.getElementById('mensaje-guardado').style.display = 'block';
        }).catch(error => console.error('No se pudo guardar el puntaje:', error));
    }

    iniciarRonda();
</script>
<!-- MÚSICA DE FONDO -->
<audio id="musica-juego" loop>
    <source src="../musica/muiscafondo.mp3" type="audio/mpeg">
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