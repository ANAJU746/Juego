<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesca Técnica - Arcade</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <style>
        .contenedor-juego1 {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, 0.6);
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(30, 136, 229, 0.15);
            width: 95%;
            max-width: 800px;
            text-align: center;
            position: relative;
            z-index: 10;
            margin: 40px auto;
            font-family: 'Nunito', sans-serif;
        }

        .stats-bar { font-weight: 800; color: #6a8296; font-size: 1.1rem; display: flex; justify-content: space-between; margin-bottom: 15px; }
        
        .pregunta-cartel {
            background: #ffffff;
            border: 3px solid #1e88e5;
            border-radius: 15px;
            padding: 15px 20px;
            font-size: 1.4rem;
            color: #1e88e5;
            font-weight: 900;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(30, 136, 229, 0.2);
            position: relative;
            z-index: 2;
        }

        /* EL RÍO DIGITAL */
        .zona-rio {
            position: relative;
            height: 350px;
            background: linear-gradient(180deg, rgba(227, 242, 253, 0.4) 0%, rgba(66, 165, 245, 0.3) 100%);
            border: 3px solid #90caf9;
            border-radius: 20px;
            overflow: hidden;
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" style="font-size: 28px"><text y="28">🪝</text></svg>'), crosshair;
            box-shadow: inset 0 10px 20px rgba(0,0,0,0.05);
        }

        .zona-rio::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 100%;
            background-image: radial-gradient(circle at 50% 100%, rgba(255,255,255,0.2) 0%, transparent 60%);
            pointer-events: none;
        }

        .pez-concepto {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            border: 3px solid #64b5f6;
            border-radius: 30px;
            padding: 12px 25px;
            font-weight: 800;
            color: #1565c0;
            font-size: 1.1rem;
            white-space: nowrap;
            box-shadow: 0 8px 15px rgba(30, 136, 229, 0.2);
            user-select: none;
            transition: transform 0.2s, background 0.3s, border-color 0.3s;
        }

        .pez-concepto:hover { transform: scale(1.05); background: #e3f2fd; border-color: #2196f3; }

        @keyframes nadar-der { from { left: -50%; } to { left: 120%; } }
        @keyframes nadar-izq { from { left: 120%; } to { left: -50%; } }

        .pez-acierto { background: #4caf50 !important; color: white !important; border-color: #388e3c !important; z-index: 10; transform: scale(1.2) !important; box-shadow: 0 0 30px rgba(76, 175, 80, 0.6) !important;}
        .pez-error { background: #ef5350 !important; color: white !important; border-color: #c62828 !important; animation: temblar 0.4s ease; }

        @keyframes temblar {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-10px); }
            40%, 80% { transform: translateX(10px); }
        }

        #instruccion { font-weight: bold; color: #ef5350; font-size: 1.1rem; height: 30px; }
        #pantalla-final { display: none; margin-top: 20px; }
        .boton-accion { display: inline-block; padding: 12px 25px; background: #1e88e5; color: white; text-decoration: none; font-weight: bold; border-radius: 10px; font-size: 1.1rem; margin: 5px; }
        .boton-accion.verde { background: #4caf50; }
    </style>
</head>
<body style="background-image: url('img/fondo.jpg'); background-size: cover; background-attachment: fixed;">

<div class="contenedor-juego1" id="app-juego">
    
    <div class="stats-bar" id="barra-estado">
        <span style="color: #ef5350;" id="txt-errores">❌ Errores: 0</span>
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
        <a href="index.php" class="boton-accion" style="margin-top: 20px;">Menú Principal</a>
        <button onclick="location.reload()" class="boton-accion verde" style="border:none; cursor:pointer; margin-top: 20px;">Volver a pescar</button>
    </div>
</div>

<div style="position: fixed; bottom: -10px; left: 20px; width: 250px; z-index: 1000; pointer-events: none;">
    <video id="vid-espera" autoplay loop muted playsinline style="width: 100%; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); display: block;">
        <source src="videos/Videoespera.webm" type="video/webm">
    </video>
    <video id="vid-felicitar" muted playsinline style="width: 100%; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); display: none;">
        <source src="videos/VideoFelicitar.webm" type="video/webm">
    </video>
</div>

<script>
    // Las preguntas ahora vienen 100% de la Base de Datos
    const datosJuego = <?php echo json_encode($preguntas); ?>;
    
    let rondaActual = 0;
    let errores = 0;
    let tiempoRestante = 20;
    let timerPregunta;
    let permitirPesca = false;
    let respuestaCorrecta = "";

    const elPregunta = document.getElementById('txt-pregunta');
    const elTiempo = document.getElementById('txt-tiempo');
    const txtErrores = document.getElementById('txt-errores');
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
        permitirPesca = true;

        clearInterval(timerPregunta);
        timerPregunta = setInterval(() => {
            tiempoRestante--;
            elTiempo.innerText = `⏱ ${tiempoRestante}s`;
            if(tiempoRestante <= 0) {
                registrarError();
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
        errores++;
        txtErrores.innerText = `❌ Errores: ${errores}`;
    }

    function terminarJuego() {
        clearInterval(timerPregunta);
        permitirPesca = false;

        document.getElementById('zona-activa').style.display = 'none';
        document.getElementById('barra-estado').style.display = 'none';
        document.getElementById('pantalla-final').style.display = 'block';

        let msg = document.getElementById('mensaje-puntuacion');
        if (errores <= 2) {
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