<?php
session_start();

// Base de preguntas técnicas de Sistemas para este juego
$preguntas = [
    ["pregunta" => "¿Cuál es la IP local (localhost)?", "correcta" => "127.0.0.1", "falsas" => ["192.168.1.1", "255.255.255.0"]],
    ["pregunta" => "¿Qué estructura usa LIFO (Último en entrar, primero en salir)?", "correcta" => "Pila (Stack)", "falsas" => ["Cola (Queue)", "Árbol (Tree)"]],
    ["pregunta" => "¿Puerto por defecto para conexiones seguras HTTPS?", "correcta" => "443", "falsas" => ["80", "21"]],
    ["pregunta" => "¿Qué lenguaje se usa principalmente para consultas a Bases de Datos?", "correcta" => "SQL", "falsas" => ["Python", "C++"]],
    ["pregunta" => "¿Comando básico de Linux para listar archivos?", "correcta" => "ls", "falsas" => ["dir", "list"]]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiro al Blanco - Arcade</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <style>
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
        .pregunta-texto { font-size: 1.8rem; color: #1e88e5; font-weight: 900; margin-bottom: 20px; min-height: 60px;}

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
            transition: left 0.4s ease-in-out; /* Esta es la magia que hace que se deslicen suavemente */
            perspective: 1000px;
        }

        .carta-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.5s;
            transform-style: preserve-3d;
        }

        /* Clase que voltea la carta */
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
            /* Patrón de la parte de atrás de la carta (El "Blanco" a disparar) */
            background: radial-gradient(circle, #ef5350 20%, #fff 20%, #fff 40%, #ef5350 40%, #ef5350 60%, #fff 60%);
            border: 4px solid #b71c1c;
            transform: rotateY(180deg);
            color: white;
        }

        .carta-dorso::after { content: '🎯'; font-size: 3rem; }

        /* Efectos visuales de disparo */
        .efecto-acierto { box-shadow: 0 0 30px #4caf50; border-color: #4caf50; }
        .efecto-error { animation: temblar 0.4s ease; box-shadow: 0 0 30px #ef5350; border-color: #ef5350; }

        @keyframes temblar {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-10px); }
            40%, 80% { transform: translateX(10px); }
        }

        #instruccion { font-weight: bold; color: #ef5350; font-size: 1.2rem; height: 30px; }
        #pantalla-final { display: none; margin-top: 20px; }
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
        
        <div id="instruccion">¡Memoriza las cartas!</div>

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
        <a href="index.php" class="boton-accion" style="margin-top: 20px;">Menú Principal</a>
        <button onclick="location.reload()" class="boton-accion verde" style="border:none; cursor:pointer; margin-top: 20px;">Volver a disparar</button>
    </div>

</div>

<!-- VIDEOS DE LA MASCOTA (VERSIÓN WEBM) -->
<div style="position: fixed; bottom: -10px; left: 20px; width: 250px; z-index: 1000; pointer-events: none;">
    <!-- Video de Espera -->
    <video id="vid-espera" autoplay loop muted playsinline style="width: 100%; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); display: block;">
        <source src="videos/Videoespera.webm" type="video/webm">
    </video>
    <!-- Video de Felicitar -->
    <video id="vid-felicitar" muted playsinline style="width: 100%; filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4)); display: none;">
        <source src="videos/VideoFelicitar.webm" type="video/webm">
    </video>
</div>

<!-- LÓGICA DEL MOTOR DEL JUEGO -->
<script>
    // Importamos las preguntas de PHP a Javascript
    const datosJuego = <?php echo json_encode($preguntas); ?>;
    
    let rondaActual = 0;
    let errores = 0;
    let permitirdisparo = false;
    let timerDisparo;
    let timerMemorizar; // NUEVA VARIABLE PARA EL TIEMPO DE LECTURA
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
        
        // AQUÍ CONFIGURAMOS LOS 8 SEGUNDOS PARA LEER
        tiempoRestante = 8;
        elTiempo.innerText = `⏱️ ${tiempoRestante}s`;

        let pregunta = datosJuego[rondaActual];
        elPregunta.innerText = pregunta.pregunta;
        respuestaCorrectaTexto = pregunta.correcta;

        // Revolver respuestas
        let opciones = [pregunta.correcta, pregunta.falsas[0], pregunta.falsas[1]];
        opciones.sort(() => Math.random() - 0.5);

        // Limpiar estilos y voltear boca arriba
        for(let i=0; i<3; i++) {
            innerCartas[i].classList.remove('volteada');
            frenteCartas[i].innerText = opciones[i];
            frenteCartas[i].classList.remove('efecto-acierto', 'efecto-error');
        }

        // NUEVO: Temporizador visual para la memorización
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
        elTiempo.innerText = "⏱️ -"; // Pausamos el texto del reloj
        
        // Voltear las cartas boca abajo
        for(let i=0; i<3; i++) {
            innerCartas[i].classList.add('volteada');
        }

        // Esperar a que termine la animación de volteo para empezar a revolver
        setTimeout(() => {
            let movimientos = 0;
            let intervaloRevuelvo = setInterval(() => {
                // Elegir dos posiciones al azar para intercambiarlas
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
        
        // 5 segundos para disparar rápido
        tiempoRestante = 5; 
        elTiempo.innerText = `⏱️ ${tiempoRestante}s`;

        timerDisparo = setInterval(() => {
            tiempoRestante--;
            elTiempo.innerText = `⏱️ ${tiempoRestante}s`;
            if(tiempoRestante <= 0) {
                clearInterval(timerDisparo);
                procesarDisparo(null); // Disparo fallido por tiempo
            }
        }, 1000);
    }

    // Escuchar el clic ("Disparo")
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
        setTimeout(iniciarRonda, 2000); // Esperar 2 segundos para que vea su error/acierto
    }

    function terminarJuego() {
        document.getElementById('zona-activa').style.display = 'none';
        document.getElementById('barra-estado').style.display = 'none';
        document.getElementById('pantalla-final').style.display = 'block';

        let msg = document.getElementById('mensaje-puntuacion');
        
        if (errores <= 1) {
            msg.innerText = "¡Eres un francotirador! Tuviste " + errores + " errores.";
            document.getElementById('vid-espera').style.display = 'none';
            let vidFeliz = document.getElementById('vid-felicitar');
            vidFeliz.style.display = 'block';
            vidFeliz.play();
            lanzarConfeti();
        } else {
            msg.innerText = "Terminaste, pero fallaste " + errores + " tiros. ¡Apunta mejor la próxima!";
        }
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

    // Arrancar el juego
    iniciarRonda();
</script>

</body>
</html>