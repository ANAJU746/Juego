<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiro al Blanco - Arcade</title>
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

        .contenedor-juego3 {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, 0.6);
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(30, 136, 229, 0.15);
            width: 95%;
            max-width: 600px;
            text-align: center;
            position: relative;
            z-index: 10;
            cursor: crosshair; /* ¡Cursor de mira de disparo! */
        }

        .stats-bar { font-weight: 800; color: #6a8296; font-size: 1.1rem; display: flex; justify-content: space-between; margin-bottom: 15px; }
        .pregunta-texto { font-size: 1.8rem; color: #1e88e5; font-weight: 900; margin-bottom: 20px; min-height: 60px; background: white; padding: 15px; border-radius: 15px; border: 3px solid #1e88e5; box-shadow: 0 10px 20px rgba(30, 136, 229, 0.2);}

        /* Zona donde las cartas se moverán mágicamente */
        .zona-cartas {
            position: relative;
            height: 140px;
            width: 100%;
            margin: 30px 0;
        }

        .carta-wrapper {
            position: absolute;
            width: 30%;
            height: 100%;
            top: 0;
            transition: left 0.4s ease-in-out;
            perspective: 1000px;
        }

        .carta-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.5s;
            transform-style: preserve-3d;
        }

        .volteada { transform: rotateY(180deg); }

        .carta-frente, .carta-dorso {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            padding: 10px;
            box-sizing: border-box;
            text-align: center;
        }

        .carta-frente { background: #fff; border: 3px solid #e3f2fd; color: #2c3e50; font-size: 1rem; }
        
        .carta-dorso {
            background: radial-gradient(circle, #ef5350 20%, #fff 20%, #fff 40%, #ef5350 40%, #ef5350 60%, #fff 60%);
            border: 4px solid #b71c1c;
            transform: rotateY(180deg);
            color: white;
        }

        .carta-dorso::after { content: '🎯'; font-size: 3rem; }

        /* Efectos visuales de disparo */
        .efecto-acierto { box-shadow: 0 0 30px #4caf50; border-color: #4caf50; background: #e8f5e9; color: #2e7d32;}
        .efecto-error { animation: temblar 0.4s ease; box-shadow: 0 0 30px #ef5350; border-color: #ef5350; background: #ffebee; color: #c62828;}

        @keyframes temblar {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-10px); }
            40%, 80% { transform: translateX(10px); }
        }

        #instruccion { font-weight: bold; color: #ef5350; font-size: 1.2rem; height: 30px; }
        #pantalla-final { display: none; margin-top: 20px; }
        .boton-accion { display: inline-block; padding: 12px 25px; background: #1e88e5; color: white; text-decoration: none; font-weight: bold; border-radius: 10px; font-size: 1.1rem; margin: 5px; }
        .boton-accion.verde { background: #4caf50; }
    </style>
</head>
<body>

<div class="contenedor-juego3" id="app-juego">
    
    <div class="stats-bar" id="barra-estado">
        <span style="color: #ef5350;" id="txt-errores">❌ Errores: 0</span>
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
        <a href="index.php" class="boton-accion" style="margin-top: 20px;">Menú Principal</a>
        <button onclick="location.reload()" class="boton-accion verde" style="border:none; cursor:pointer; margin-top: 20px;">Volver a disparar</button>
    </div>

</div>

<!-- VIDEOS DE LA MASCOTA -->
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

        timerDisparo = setInterval(() => {
            tiempoRestante--;
            elTiempo.innerText = `⏱️ ${tiempoRestante}s`;
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
            elInstruccion.innerText = "¡BLANCO PERFECTO!";
            elInstruccion.style.color = "#4caf50";
            frenteCartas[indiceDisparado].classList.add('efecto-acierto');
        } else {
            errores++;
            document.getElementById('txt-errores').innerText = `❌ Errores: ${errores}`;
            elInstruccion.innerText = "¡FALLASTE!";
            elInstruccion.style.color = "#ef5350";
            
            if(indiceDisparado !== null) {
                frenteCartas[indiceDisparado].classList.add('efecto-error');
            }
        }

        rondaActual++;
        setTimeout(iniciarRonda, 2000); 
    }

    function terminarJuego() {
        document.getElementById('zona-activa').style.display = 'none';
        document.getElementById('barra-estado').style.display = 'none';
        document.getElementById('pantalla-final').style.display = 'block';

        let msg = document.getElementById('mensaje-puntuacion');
        
        if (errores <= 1) {
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