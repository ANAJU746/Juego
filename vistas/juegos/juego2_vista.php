<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caza Conceptos - Arcade</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&display=swap');

        body {
            background-image: url('img/fondo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Nunito', sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .contenedor-juego2 {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, 0.6);
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(30, 136, 229, 0.15);
            width: 95%;
            max-width: 750px;
            text-align: center;
            position: relative;
            z-index: 10;
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" style="font-size: 28px"><text y="28">🔨</text></svg>'), crosshair;
        }

        .stats-bar { font-weight: 800; color: #6a8296; font-size: 1.1rem; display: flex; justify-content: space-between; margin-bottom: 15px; }
        .pregunta-texto { font-size: 1.6rem; color: #1e88e5; font-weight: 900; margin-bottom: 25px; min-height: 50px; background: white; padding: 15px; border-radius: 15px; border: 3px solid #1e88e5; box-shadow: 0 10px 20px rgba(30, 136, 229, 0.2);}

        /* TABLERO DE LOS PUERTOS */
        .grid-topos { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-top: 10px; }
        .agujero-contenedor { position: relative; height: 150px; overflow: hidden; }

        /* Puerto holográfico */
        .agujero-base { position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); width: 80%; height: 35px; background: rgba(30, 136, 229, 0.05); border: 2px solid rgba(66, 165, 245, 0.4); border-radius: 50%; box-shadow: inset 0 5px 15px rgba(0,0,0,0.05), 0 0 15px rgba(66, 165, 245, 0.2); z-index: 3; pointer-events: none; }

        /* Pantalla digital flotante */
        .cartel { position: absolute; bottom: -100px; left: 50%; transform: translateX(-50%); width: 85%; background: #ffffff; border: 3px solid #e3f2fd; border-radius: 14px; padding: 15px 5px; text-align: center; font-weight: 800; color: #2c3e50; font-size: 1.2rem; transition: bottom 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 1; user-select: none; box-shadow: 0 10px 20px rgba(30, 136, 229, 0.15); }
        .cartel:hover { background-color: #f0f8ff; border-color: #90caf9; transform: translateX(-50%) scale(1.02); }
        .cartel::after { content: ''; position: absolute; bottom: -35px; left: 50%; transform: translateX(-50%); width: 8px; height: 35px; background: linear-gradient(to bottom, #90caf9, rgba(144, 202, 249, 0)); border-radius: 4px; }

        /* Activar para subir */
        .agujero-contenedor.activo .cartel { bottom: 40px; }

        /* Animaciones */
        .cartel.efecto-acierto { background: #f1f8e9; border-color: #66bb6a; color: #388e3c; box-shadow: 0 0 25px rgba(76, 175, 80, 0.4); }
        .cartel.efecto-acierto::after { background: linear-gradient(to bottom, #66bb6a, rgba(102, 187, 106, 0)); }
        
        .cartel.efecto-error { background: #ffebee; border-color: #ef5350; color: #d32f2f; animation: temblar 0.4s ease; box-shadow: 0 0 25px rgba(239, 83, 80, 0.4); }
        .cartel.efecto-error::after { background: linear-gradient(to bottom, #ef5350, rgba(239, 83, 80, 0)); }

        @keyframes temblar { 0%, 100% { transform: translateX(-50%) rotate(0deg); } 20%, 60% { transform: translateX(-50%) rotate(-8deg); } 40%, 80% { transform: translateX(-50%) rotate(8deg); } }

        #instruccion { font-weight: bold; color: #ef5350; font-size: 1.1rem; height: 30px; }
        #pantalla-final { display: none; margin-top: 20px; }
        .boton-accion { display: inline-block; padding: 12px 25px; background: #1e88e5; color: white; text-decoration: none; font-weight: bold; border-radius: 10px; font-size: 1.1rem; margin: 5px; }
        .boton-accion.verde { background: #4caf50; }
    </style>
</head>
<body>

<div class="contenedor-juego2" id="app-juego">
    
    <div class="stats-bar" id="barra-estado">
        <span style="color: #ef5350;" id="txt-errores">❌ Errores: 0</span>
        <span style="color: #42a5f5;" id="txt-ronda">Ronda: 1/5</span>
        <span style="color: #4caf50;" id="txt-tiempo">⏱️ 15s</span>
    </div>

    <div id="zona-activa">
        <div id="instruccion">¡Lee el concepto y martilla la respuesta correcta!</div>
        <div class="pregunta-texto" id="txt-pregunta">Cargando...</div>

        <div class="grid-topos" id="tablero">
            <?php for($i=0; $i<6; $i++): ?>
                <div class="agujero-contenedor" id="hoyo-<?php echo $i; ?>">
                    <div class="cartel" data-valor=""></div>
                    <div class="agujero-base"></div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div id="pantalla-final">
        <h2 style="font-size: 2.5rem; color: #1e88e5; margin-bottom: 10px;">¡Juego Terminado!</h2>
        <p id="mensaje-puntuacion" style="font-weight: 700; color: #6a8296; font-size: 1.2rem;"></p>
        <p id="mensaje-guardado" style="color: #4caf50; font-weight: 800; display: none;">✅ Puntaje guardado exitosamente</p>
        <a href="index.php" class="boton-accion" style="margin-top: 20px;">Menú Principal</a>
        <button onclick="location.reload()" class="boton-accion verde" style="border:none; cursor:pointer; margin-top: 20px;">Volver a jugar</button>
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
    const datosJuego = <?php echo json_encode($preguntas); ?>;
    
    let rondaActual = 0;
    let errores = 0;
    let tiempoRestante = 15;
    let timerPregunta;
    let timerMecanica; 
    let permitirmartillazo = false;
    let respuestaCorrecta = "";

    const elPregunta = document.getElementById('txt-pregunta');
    const elTiempo = document.getElementById('txt-tiempo');
    const txtErrores = document.getElementById('txt-errores');
    
    const todosLosHoyos = document.querySelectorAll('.agujero-contenedor');
    const todosLosCarteles = document.querySelectorAll('.cartel');

    function iniciarRonda() {
        if(rondaActual >= datosJuego.length) {
            terminarJuego();
            return;
        }

        let pregunta = datosJuego[rondaActual];
        elPregunta.innerText = pregunta.pregunta;
        respuestaCorrecta = pregunta.correcta;
        document.getElementById('txt-ronda').innerText = `Ronda: ${rondaActual + 1}/${datosJuego.length}`;

        tiempoRestante = 15;
        elTiempo.innerText = `⏱️ ${tiempoRestante}s`;
        permitirmartillazo = true;

        clearInterval(timerPregunta);
        timerPregunta = setInterval(() => {
            tiempoRestante--;
            elTiempo.innerText = `⏱️ ${tiempoRestante}s`;
            if(tiempoRestante <= 0) {
                registrarError();
                ocultarTodosLosCarteles();
                rondaActual++;
                setTimeout(iniciarRonda, 1000);
            }
        }, 1000);

        ocultarTodosLosCarteles();
        clearTimeout(timerMecanica);
        mecanicaAsomarse();
    }

    function mecanicaAsomarse() {
        if(!permitirmartillazo) return;

        ocultarTodosLosCarteles();

        timerMecanica = setTimeout(() => {
            if(!permitirmartillazo) return;

            let indices = [0, 1, 2, 3, 4, 5];
            indices.sort(() => Math.random() - 0.5);
            let hoyosElegidos = [indices[0], indices[1], indices[2]];

            let opciones = [respuestaCorrecta, datosJuego[rondaActual].falsas[0], datosJuego[rondaActual].falsas[1]];
            opciones.sort(() => Math.random() - 0.5);

            hoyosElegidos.forEach((indiceHoyo, i) => {
                let hoyo = todosLosHoyos[indiceHoyo];
                let cartel = hoyo.querySelector('.cartel');
                
                cartel.innerText = opciones[i];
                cartel.setAttribute('data-valor', opciones[i]);
                cartel.classList.remove('efecto-acierto', 'efecto-error');
                
                hoyo.classList.add('activo'); 
            });

            timerMecanica = setTimeout(mecanicaAsomarse, 3500);

        }, 500);
    }

    function ocultarTodosLosCarteles() {
        todosLosHoyos.forEach(hoyo => hoyo.classList.remove('activo'));
    }

    todosLosCarteles.forEach(cartel => {
        cartel.addEventListener('click', function(e) {
            if(!permitirmartillazo || !this.parentElement.classList.contains('activo')) return;

            let valorGolpeado = this.getAttribute('data-valor');

            if(valorGolpeado === respuestaCorrecta) {
                permitirmartillazo = false; 
                clearInterval(timerPregunta);
                clearTimeout(timerMecanica);

                this.classList.add('efecto-acierto'); 
                
                setTimeout(() => {
                    ocultarTodosLosCarteles();
                    rondaActual++;
                    iniciarRonda(); 
                }, 1000);

            } else {
                registrarError();
                this.classList.add('efecto-error'); 
                
                setTimeout(() => {
                    this.parentElement.classList.remove('activo');
                }, 400);
            }
        });
    });

    function registrarError() {
        errores++;
        txtErrores.innerText = `❌ Errores: ${errores}`;
    }

    function terminarJuego() {
        clearInterval(timerPregunta);
        clearTimeout(timerMecanica);
        permitirmartillazo = false;

        document.getElementById('zona-activa').style.display = 'none';
        document.getElementById('barra-estado').style.display = 'none';
        document.getElementById('pantalla-final').style.display = 'block';

        let msg = document.getElementById('mensaje-puntuacion');
        if (errores <= 2) {
            msg.innerText = "¡Excelentes reflejos! Solo fallaste " + errores + " veces.";
            let vidEspera = document.getElementById('vid-espera');
            if (vidEspera) vidEspera.style.display = 'none';
            let vidFeliz = document.getElementById('vid-felicitar');
            if (vidFeliz) { vidFeliz.style.display = 'block'; vidFeliz.play(); }
            lanzarConfeti();
        } else {
            msg.innerText = "¡Cuidado con el martillo! Tuviste " + errores + " errores. ¡Sigue practicando!";
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