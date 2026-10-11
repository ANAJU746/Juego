<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esperando al Maestro...</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        .contenedor-espera {
            position: fixed;
            top: 3vh;
            left: 50%;
            z-index: 10;
            isolation: isolate;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: min(92vw, 1250px);
            padding: clamp(22px, 4vw, 48px) clamp(20px, 5vw, 64px);
            text-align: center;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.78), rgba(232, 245, 255, 0.52));
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 2px solid rgba(255, 255, 255, 0.82);
            border-radius: 30px;
            box-shadow: 0 18px 55px rgba(26, 68, 105, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.9);
            transform: translateX(-50%);
            animation: lobby-tarjeta-entrada 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        .contenedor-espera::before {
            content: '';
            position: absolute;
            inset: -2px;
            z-index: -1;
            padding: 2px;
            border-radius: inherit;
            background: conic-gradient(
                from var(--lobby-borde-angle, 0deg),
                transparent 0deg 275deg,
                rgba(173, 232, 255, 0.45) 300deg,
                #a8e6ff 325deg,
                #42a5f5 342deg,
                transparent 360deg
            );
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            animation: lobby-borde-recorrido 4s linear infinite;
        }

        @property --lobby-borde-angle {
            syntax: '<angle>';
            inherits: false;
            initial-value: 0deg;
        }

        @keyframes lobby-borde-recorrido {
            to { --lobby-borde-angle: 360deg; }
        }

        #particulas-lobby {
            position: fixed;
            inset: 0;
            z-index: 2;
            overflow: hidden;
            pointer-events: none;
        }

        .particula-lobby {
            position: absolute;
            bottom: -30px;
            left: var(--particula-x);
            color: rgba(255, 255, 255, 0.82);
            font-size: var(--particula-size);
            opacity: 0;
            text-shadow: 0 0 12px rgba(168, 230, 255, 0.8);
            animation: lobby-particula-flotar var(--particula-duracion) var(--particula-retraso) linear infinite;
        }

        @keyframes lobby-particula-flotar {
            0% { opacity: 0; transform: translate3d(0, 0, 0) scale(0.7) rotate(0); }
            12% { opacity: 0.7; }
            80% { opacity: 0.45; }
            100% { opacity: 0; transform: translate3d(var(--particula-deriva), -110vh, 0) scale(1.15) rotate(180deg); }
        }

        @keyframes lobby-tarjeta-entrada {
            from { opacity: 0; translate: 0 -18px; }
            to { opacity: 1; translate: 0 0; }
        }

        .contenedor-espera h1 {
            max-width: 100%;
            font-size: clamp(3.5rem, 9vw, 8rem);
            line-height: 0.95;
            margin: 0;
            color: #2c3e50;
            font-weight: 900;
            text-wrap: balance;
            text-shadow: 0 3px 0 rgba(255, 255, 255, 0.75), 0 8px 22px rgba(46, 86, 121, 0.16);
        }

        .lobby-letra {
            display: inline-block;
            animation: lobby-letra-entrada 0.7s calc(var(--letra) * 45ms) cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        .contenedor-espera p {
            max-width: 900px;
            font-size: clamp(1.25rem, 2.8vw, 2.15rem);
            color: #34495e;
            font-weight: 600;
            margin: 18px 0 0;
            line-height: 1.35;
            animation: lobby-subtexto-entrada 1s 0.7s cubic-bezier(0.25, 1, 0.5, 1) both;
        }

        @keyframes lobby-letra-entrada {
            from { opacity: 0; transform: translateY(35px) scale(0.88); filter: blur(8px); }
            to { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); }
        }

        @keyframes lobby-subtexto-entrada {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #jaguar-lobby {
            position: fixed;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 1050px;
            max-width: 100vw;
            z-index: 5;
            pointer-events: none;
            background: transparent;
            mix-blend-mode: multiply;
            filter: contrast(1.3) brightness(1.1);
        }

        @media (max-width: 600px) {
            .contenedor-espera {
                top: 2vh;
                width: 94vw;
                padding: 22px 16px;
                border-radius: 22px;
            }
            .contenedor-espera h1 { font-size: clamp(3rem, 15vw, 5.5rem); }
            .contenedor-espera p { font-size: clamp(1.15rem, 5vw, 1.6rem); }
            #jaguar-lobby { bottom: 0; }
        }

        @media (prefers-reduced-motion: reduce) {
            .contenedor-espera {
                animation: none;
            }
            .contenedor-espera::before,
            .particula-lobby {
                animation: none;
            }
            .lobby-letra,
            .contenedor-espera p {
                animation: none;
            }
        }
    </style>
</head>
<body>

    <!-- VIDEO FONDO GLOBAL ANIMADO -->
    <video autoplay loop muted playsinline id="video-fondo-global">
        <source src="../videos/FondoAnimado4.webm" type="video/webm">
    </video>

    <div id="particulas-lobby" aria-hidden="true"></div>

    <!-- TEXTO DE ESPERA -->
    <div class="contenedor-espera">
        <?php if (!empty($fiestaCompletada)): ?>
            <h1>¡Fiesta completada!</h1>
            <p>Terminaste los cinco juegos. Espera a que el maestro inicie otra actividad.</p>
        <?php else: ?>
            <h1>¡Estás dentro!</h1>
            <p>Mira la pantalla del profesor. El juego comenzará en breve...</p>
        <?php endif; ?>
    </div>

    <!-- EL JAGUAR CENTRADO SIN FONDO BLANCO -->
    <video autoplay loop muted playsinline id="jaguar-lobby">
        <source src="../videos/EsperaLobby.webm" type="video/webm">
    </video>

    <!-- MÚSICA DE FONDO OPCIONAL -->
    <audio id="musica-juego" loop autoplay>
        <source src="../musica/muiscafondo.mp3" type="audio/mpeg">
    </audio>

    <script>
        const particulasLobby = document.getElementById('particulas-lobby');
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            const simbolosFlotantes = ['✦', '·', '○', '✧'];
            for (let indice = 0; indice < 24; indice++) {
                const particula = document.createElement('span');
                particula.className = 'particula-lobby';
                particula.textContent = simbolosFlotantes[indice % simbolosFlotantes.length];
                particula.style.setProperty('--particula-x', `${Math.random() * 100}%`);
                particula.style.setProperty('--particula-size', `${8 + Math.random() * 13}px`);
                particula.style.setProperty('--particula-duracion', `${12 + Math.random() * 12}s`);
                particula.style.setProperty('--particula-retraso', `${-Math.random() * 24}s`);
                particula.style.setProperty('--particula-deriva', `${Math.random() * 100 - 50}px`);
                particulasLobby.appendChild(particula);
            }
        }

        const tituloEspera = document.querySelector('.contenedor-espera h1');
        if (tituloEspera && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            const titulo = tituloEspera.textContent;
            tituloEspera.setAttribute('aria-label', titulo);
            tituloEspera.replaceChildren(...Array.from(titulo, (letra, indice) => {
                if (letra === ' ') return document.createTextNode(' ');
                const span = document.createElement('span');
                span.className = 'lobby-letra';
                span.setAttribute('aria-hidden', 'true');
                span.style.setProperty('--letra', indice);
                span.textContent = letra;
                return span;
            }));
        }

        async function revisarEstadoSala() {
            try {
                const response = await fetch('lobby.php?api=estado', {
                    cache: 'no-store',
                    headers: { Accept: 'application/json' }
                });

                if (response.status === 401) {
                    window.location.replace('../index.php');
                    return;
                }
                if (!response.ok) {
                    throw new Error(`Error al consultar la sala: HTTP ${response.status}`);
                }

                const sala = await response.json();
                if (sala.estado === 'jugando') {
                    if (sala.modo_fiesta && sala.fiesta_completada) {
                        return;
                    }
                    if (sala.modo_fiesta) {
                        const paso = Number(sala.paso_fiesta || 1);
                        if (Number.isInteger(paso) && paso >= 1 && paso <= 5) {
                            window.location.replace(`juego${paso}.php?fiesta=1&reiniciar=${Date.now()}`);
                            return;
                        }
                    }
                    const idJuego = Number(sala.id_categoria);
                    const juego = Number.isInteger(idJuego) && idJuego >= 1 && idJuego <= 5 ? idJuego : 1;
                    window.location.replace(`juego${juego}.php?reiniciar=${Date.now()}`);
                    return;
                }
                if (sala.estado === 'finalizada') {
                    window.location.replace('../index.php');
                    return;
                }
                if (sala.estado !== 'espera') {
                    throw new Error(`Estado de sala inesperado: ${sala.estado}`);
                }
            } catch (error) {
                console.error('No se pudo comprobar el estado del lobby:', error);
            }

            window.setTimeout(revisarEstadoSala, 2000);
        }

        window.setTimeout(revisarEstadoSala, 2000);
    </script>
</body>
</html>