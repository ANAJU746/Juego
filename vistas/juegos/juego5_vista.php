<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torre de Conceptos - Arcade</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&display=swap');

        body { background-image: url('../img/fondo.jpg'); background-size: cover; background-position: center; font-family: 'Nunito', sans-serif; margin: 0; overflow: hidden; display: flex; align-items: center; justify-content: center; height: 100vh; }

        .contenedor-juego { 
            background: rgba(255, 255, 255, 0.90); 
            backdrop-filter: blur(12px); 
            border: 2px solid #fff; 
            padding: 20px; 
            border-radius: 24px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.15); 
            width: 95%; max-width: 600px; text-align: center; 
            color: #2c3e50; 
            z-index: 10; position: relative; display: flex; flex-direction: column; height: 85vh; 
        }

        .stats-bar { font-weight: 800; font-size: 1.1rem; display: flex; justify-content: space-between; margin-bottom: 10px; }
        
        .pregunta-texto { font-size: 1.4rem; color: #1e88e5; font-weight: 900; margin-bottom: 10px; min-height: 60px; }
        .instruccion { font-size: 0.95rem; color: #6a8296; margin-bottom: 15px; font-weight: bold; }

        /* AREA DE LA TORRE (Ya no tiene cursor pointer) */
        #area-apilado { 
            flex-grow: 1; position: relative; border-radius: 15px; 
            background: rgba(0,0,0,0.03); 
            border: 2px dashed #b0bec5;
            border-bottom: 5px solid #66bb6a; 
            overflow: hidden; 
        }
        
        /* El bloque móvil ahora tiene el cursor de mano */
        .bloque { position: absolute; width: 220px; height: 45px; display: flex; justify-content: center; align-items: center; font-weight: 800; font-size: 0.95rem; border-radius: 8px; box-shadow: inset 0 -4px 0 rgba(0,0,0,0.2); color: white; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); padding: 0 10px; text-align: center; line-height: 1.1; user-select: none; }
        
        #bloque-movil { top: 20px; left: 0; z-index: 50; cursor: pointer; transition: background 0.3s; }
        #bloque-movil:hover { filter: brightness(1.1); transform: scale(1.02); }
        
        #contenedor-torre { position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 220px; display: flex; flex-direction: column-reverse; align-items: center; transition: bottom 0.3s; }
        
        .bloque-apilado { position: relative; width: 100%; height: 45px; margin-bottom: 2px; }
        
        .derrumbe { transform: translateY(500px) rotate(45deg); opacity: 0; transition: transform 0.5s ease-in, opacity 0.5s; }

        #pantalla-final { display: none; margin-top: auto; margin-bottom: auto; }
        .boton-accion { display: inline-block; padding: 12px 25px; background: linear-gradient(135deg, #42a5f5, #1e88e5); color: white; text-decoration: none; font-weight: bold; border-radius: 10px; font-size: 1.1rem; border:none; cursor:pointer; margin-top: 20px; box-shadow: 0 5px 15px rgba(30,136,229,0.3); }
    </style>
</head>
<body>

<div class="contenedor-juego">
    <div class="stats-bar" id="barra-estado">
        <span style="color: #ef5350;" id="txt-errores">❌ Errores: 0</span>
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
    const datosJuego = <?php echo json_encode($preguntas); ?>;
    let rondaActual = 0;
    let errores = 0;
    
    let opcionesActuales = [];
    let indiceOpcion = 0;
    let moviendo = false;
    let x = 0;
    let direccion = 1;
    let velocidad = 0.6; 
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
        if(!moviendo) return;
        
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
                errores++;
                document.getElementById('txt-errores').innerText = `❌ Errores: ${errores}`;
                
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

    function terminarJuego() {
        document.getElementById('zona-activa').style.display = 'none';
        document.getElementById('barra-estado').style.display = 'none';
        document.getElementById('pantalla-final').style.display = 'block';

        document.getElementById('mensaje-puntuacion').innerText = `Construiste la torre con ${errores} caídas.`;
        document.getElementById('vid-espera').style.display = 'none';
        const vidFelicitar = document.getElementById('vid-felicitar');
        vidFelicitar.style.display = 'block';
        vidFelicitar.play().catch(error => console.error('No se pudo reproducir el video de felicitación:', error));
        
        let formData = new FormData();
        formData.append('guardar_puntaje', '1');
        formData.append('errores', errores);
        fetch(window.location.href, { method: 'POST', body: formData })
        .then(r => r.json()).then(d => { if(d.status==='success') document.getElementById('mensaje-guardado').style.display = 'block'; });
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
            <button class="btn-si" onclick="window.location.href='../index.php'">Sí, salir al menú</button>
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
        if (typeof velocidad !== 'undefined' && velocidad > 0) { velOriginalPausa = velocidad; velocidad = 0; }
        if (typeof generadorGlobos !== 'undefined' && generadorGlobos) { clearInterval(generadorGlobos); generadorGlobos = null; generadorPausado = true; }
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
</body>
</html>