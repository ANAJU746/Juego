<?php
session_start();

// Datos simulados del alumno (¡Después conectaremos a la BD!)
$nombre_usuario = "Estudiante IT";
$juegos_jugados = $_SESSION['juegos_jugados'] ?? 12; 
$errores_totales = $_SESSION['errores_totales'] ?? 4;
$dominio_porcentaje = 75; // 75% de dominio

// Datos del Maestro / Clase
$nombre_maestro = "Prof. Roberto Carlos";
$codigo_clase = "845-902"; // Código estilo Kahoot
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcade de Sistemas</title>
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
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
            padding-bottom: 50px; /* Espacio extra para que no pegue abajo */
        }

        /* ========================================= */
        /* ANIMACIONES DE CÓDIGO EN EL FONDO (COLORES)*/
        /* ========================================= */
        #fondo-codigo {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .simbolo-codigo {
            position: absolute;
            font-family: monospace;
            font-weight: 900;
            animation: subirYDesvanecer linear infinite;
            opacity: 0.8; /* Un poco más visibles para que luzcan los colores */
        }

        @keyframes subirYDesvanecer {
            0% { transform: translateY(110vh) scale(0.5); opacity: 0; }
            10% { opacity: 1; }
            80% { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1.5); opacity: 0; }
        }

        /* ========================================= */
        /* BARRA DE PERFIL (TOP BAR)                 */
        /* ========================================= */
        .perfil-bar {
            width: 90%;
            max-width: 1100px;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            box-shadow: 0 15px 30px rgba(30, 136, 229, 0.15);
            z-index: 10;
            animation: bajarEntrada 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            flex-wrap: wrap; 
            gap: 20px;
        }

        @keyframes bajarEntrada {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .info-usuario { display: flex; align-items: center; gap: 15px; }
        
        .foto-perfil {
            width: 60px; height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #42a5f5, #1e88e5);
            border: 3px solid #fff;
            box-shadow: 0 5px 15px rgba(30, 136, 229, 0.3);
            display: flex; justify-content: center; align-items: center;
            font-size: 1.8rem; color: white;
        }

        .nombre-usuario { font-size: 1.2rem; font-weight: 800; color: #1e88e5; margin: 0; }
        .rango-usuario { font-size: 0.9rem; color: #6a8296; margin: 0; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;}

        .stats-container { display: flex; gap: 30px; text-align: center; }
        .stat-box p { margin: 0; font-size: 0.85rem; color: #6a8296; font-weight: 700; text-transform: uppercase; }
        .stat-box h3 { margin: 0; font-size: 1.6rem; color: #1e88e5; font-weight: 900; }

        .dominio-container { width: 220px; }
        .dominio-titulos { display: flex; justify-content: space-between; margin-bottom: 5px; font-weight: 800; font-size: 0.9rem; color: #6a8296; }
        .barra-fondo { width: 100%; height: 12px; background: rgba(66, 165, 245, 0.2); border-radius: 10px; overflow: hidden; }
        .barra-llena { 
            height: 100%; 
            background: linear-gradient(90deg, #42a5f5, #4caf50); 
            border-radius: 10px; 
            width: 0%; 
            transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ========================================= */
        /* TARJETAS DE JUEGOS Y TÍTULO               */
        /* ========================================= */
        .menu-titulo {
            font-size: 3.5rem;
            color: #1e88e5;
            background: rgba(255, 255, 255, 0.8);
            padding: 10px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            margin: 40px 0;
            text-align: center;
            border: 2px solid #fff;
            z-index: 10;
            animation: popIn 0.8s ease backwards;
            animation-delay: 0.2s;
        }

        @keyframes popIn {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .tarjetas-container {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 1000px;
            z-index: 10;
        }

        .tarjeta {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            border: 2px solid rgba(255,255,255,0.8);
            border-radius: 20px;
            padding: 40px 20px;
            width: 260px;
            text-align: center;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: subirTarjeta 0.6s ease backwards;
        }

        .tarjeta:nth-child(1) { animation-delay: 0.3s; }
        .tarjeta:nth-child(2) { animation-delay: 0.5s; }
        .tarjeta:nth-child(3) { animation-delay: 0.7s; }

        @keyframes subirTarjeta {
            from { transform: translateY(40px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .tarjeta:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(30, 136, 229, 0.2);
            border-color: #42a5f5;
        }

        .icono { font-size: 4.5rem; margin-bottom: 20px; }
        .tarjeta h2 { margin: 0 0 10px; color: #1e88e5; font-size: 1.8rem; }
        .tarjeta p { margin: 0; font-weight: 700; color: #6a8296; line-height: 1.4; }

        /* ========================================= */
        /* BARRA DEL MAESTRO ESTILO KAHOOT           */
        /* ========================================= */
        .maestro-bar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 10;
            animation: subirTarjeta 0.6s ease backwards;
            animation-delay: 0.9s;
        }

        .foto-maestro {
            width: 55px; height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ef5350, #ffca28); /* Colores cálidos para el maestro */
            border: 3px solid #fff;
            box-shadow: 0 5px 15px rgba(239, 83, 80, 0.3);
            display: flex; justify-content: center; align-items: center;
            font-size: 1.8rem;
        }

        .info-maestro { text-align: left; }
        .nombre-maestro { font-size: 1.1rem; font-weight: 800; color: #2c3e50; margin: 0 0 5px 0; }
        
        .codigo-kahoot { 
            font-size: 0.95rem; color: #6a8296; margin: 0; font-weight: 700; 
            background: #f4f7f6; padding: 5px 15px; border-radius: 10px;
            border: 1px solid #e0e0e0;
        }
        .codigo-kahoot strong { 
            font-size: 1.4rem; color: #1e88e5; letter-spacing: 3px; 
            font-family: monospace; vertical-align: middle;
        }

        /* ========================================= */
        /* MASCOTA FLOTANTE                          */
        /* ========================================= */
        .mascota-menu {
            position: fixed;
            bottom: -20px;
            right: 20px;
            width: 300px;
            pointer-events: none;
            filter: drop-shadow(0 15px 20px rgba(0,0,0,0.4));
            animation: flotar 3s ease-in-out infinite;
            z-index: 20;
        }

        @keyframes flotar {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        @media (max-width: 800px) {
            .perfil-bar { flex-direction: column; justify-content: center; }
            .stats-container { width: 100%; justify-content: space-around; }
            .mascota-menu { width: 150px; }
            .maestro-bar { flex-direction: column; text-align: center; }
            .info-maestro { text-align: center; }
        }
    </style>
</head>
<body>

    <div id="fondo-codigo"></div>

    <header class="perfil-bar">
        <div class="info-usuario">
            <div class="foto-perfil">🧑‍💻</div>
            <div>
                <p class="nombre-usuario"><?php echo $nombre_usuario; ?></p>
                <p class="rango-usuario">Nivel: Junior</p>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-box">
                <p>Partidas</p>
                <h3><?php echo $juegos_jugados; ?></h3>
            </div>
            <div class="stat-box">
                <p>Errores</p>
                <h3 style="color: #ef5350;"><?php echo $errores_totales; ?></h3>
            </div>
        </div>

        <div class="dominio-container">
            <div class="dominio-titulos">
                <span>Dominio del Tema</span>
                <span style="color: #4caf50;"><?php echo $dominio_porcentaje; ?>%</span>
            </div>
            <div class="barra-fondo">
                <div class="barra-llena" id="barra-magica" data-progreso="<?php echo $dominio_porcentaje; ?>"></div>
            </div>
        </div>
    </header>

    <h1 class="menu-titulo">Arcade de Sistemas</h1>

 <div class="tarjetas-container">
        
        <a href="juego1.php?reiniciar=<?php echo time(); ?>" class="tarjeta">
            <div class="icono">🎣</div>
            <h2>Pesca Técnica</h2>
            <p>Lanza tu anzuelo y atrapa la respuesta correcta que nada en el río digital.</p>
        </a>
        
        <a href="juego2.php" class="tarjeta">
            <div class="icono">🔨</div>
            <h2>Caza Conceptos</h2>
            <p>Lee el concepto y martilla la pantalla holográfica correcta antes de que se esconda.</p>
        </a>

        <a href="juego3.php" class="tarjeta">
            <div class="icono">🎯</div>
            <h2>Tiro al Blanco</h2>
            <p>Memoriza las posiciones de las tarjetas y dispárale a la correcta con tiempo límite.</p>
        </a>
        
    </div>

    <div class="maestro-bar">
        <div class="foto-maestro">👨‍🏫</div>
        <div class="info-maestro">
            <p class="nombre-maestro"><?php echo $nombre_maestro; ?></p>
            <p class="codigo-kahoot">CÓDIGO DE JUEGO: <strong><?php echo $codigo_clase; ?></strong></p>
        </div>
    </div>

    <img src="img/mascota1.png" alt="Mascota" class="mascota-menu">

    <script>
        window.addEventListener('load', () => {
            const barra = document.getElementById('barra-magica');
            const progreso = barra.getAttribute('data-progreso');
            setTimeout(() => {
                barra.style.width = progreso + '%';
            }, 300);
        });

        // Generador de Fondo de Código MULTICOLOR
        const contenedorFondo = document.getElementById('fondo-codigo');
        const simbolos = ['</>', '{...}', '=>', 'SELECT *', 'if()', '++i', '&&', '||', '<h1>', '127.0.0.1', 'sudo', 'npm install'];
        
        // Paleta de colores vibrantes (Azul, Verde, Amarillo, Rojo, Morado)
        const coloresNeon = ['#42a5f5', '#4caf50', '#ffca28', '#ef5350', '#ab47bc', '#26c6da'];

        function crearSimbolo() {
            const el = document.createElement('div');
            el.classList.add('simbolo-codigo');
            
            el.innerText = simbolos[Math.floor(Math.random() * simbolos.length)];
            
            // Asignar color aleatorio
            const colorElegido = coloresNeon[Math.floor(Math.random() * coloresNeon.length)];
            el.style.color = colorElegido;
            // Darle un brillo tenue del mismo color
            el.style.textShadow = `0 0 10px ${colorElegido}`;
            
            el.style.left = Math.random() * 100 + 'vw';
            const tamaño = Math.random() * 1.5 + 0.8;
            el.style.fontSize = tamaño + 'rem';
            
            const duracion = Math.random() * 10 + 10;
            el.style.animationDuration = duracion + 's';
            
            contenedorFondo.appendChild(el);
            setTimeout(() => { el.remove(); }, duracion * 1000);
        }

        for(let i=0; i<15; i++) {
            setTimeout(crearSimbolo, Math.random() * 5000);
        }
        setInterval(crearSimbolo, 1500);
    </script>

</body>
</html>