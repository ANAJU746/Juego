<?php
session_start();

// Base de datos de preguntas (Área de Sistemas)
$preguntas = [
    [
        "palabra" => "¿Qué componente es considerado el 'cerebro' de una computadora?", 
        "opciones" => ["Memoria RAM", "Disco Duro", "Procesador (CPU)"], 
        "respuesta" => "Procesador (CPU)"
    ],
    [
        "palabra" => "¿Qué lenguaje estándar se usa para gestionar bases de datos?", 
        "opciones" => ["Python", "SQL", "HTML"], 
        "respuesta" => "SQL"
    ],
    [
        "palabra" => "¿Qué significa la palabra 'Bug' en el desarrollo de software?", 
        "opciones" => ["Un error en el código", "Una actualización", "Un tipo de virus"], 
        "respuesta" => "Un error en el código"
    ],
    [
        "palabra" => "¿Cuál es el protocolo utilizado para páginas web seguras?", 
        "opciones" => ["FTP", "HTTP", "HTTPS"], 
        "respuesta" => "HTTPS"
    ],
    [
        "palabra" => "En programación, ¿qué es un algoritmo?", 
        "opciones" => ["Un lenguaje web", "Una serie de pasos para resolver un problema", "Un componente físico del servidor"], 
        "respuesta" => "Una serie de pasos para resolver un problema"
    ]
];

// Reiniciar juego de forma definitiva y sin caché
if (isset($_GET['reiniciar'])) {
    session_unset();   // Limpia todas las variables
    session_destroy(); // Destruye la sesión en el servidor
    
    // Le pasamos un número aleatorio (time) a la redirección 
    // para que el navegador jamás use la caché
    header("Location: juego1.php?rand=" . time());
    exit;
}

// Inicializar variables de sesión
if (!isset($_SESSION['pregunta_actual'])) {
    $_SESSION['pregunta_actual'] = 0;
    $_SESSION['puntuacion'] = 0;
    $_SESSION['errores'] = 0;
    $_SESSION['estado'] = null; // null, 'correcto', 'incorrecto'
    $_SESSION['seleccion'] = null;
}

$actual = $_SESSION['pregunta_actual'];
$total = count($preguntas);
$juego_terminado = $actual >= $total;

// Procesar respuesta
if (isset($_GET['respuesta']) && $_SESSION['estado'] === null && !$juego_terminado) {
    $seleccion = $_GET['respuesta'];
    $_SESSION['seleccion'] = $seleccion;
    
    if ($seleccion === $preguntas[$actual]['respuesta']) {
        $_SESSION['puntuacion']++;
        $_SESSION['estado'] = 'correcto';
    } else {
        $_SESSION['errores']++; 
        $_SESSION['estado'] = 'incorrecto';
    }
    // AQUÍ TAMBIÉN CAMBIAMOS A juego1.php
    header("Location: juego1.php");
    exit;
}

// Avanzar a la siguiente pregunta
if (isset($_GET['siguiente']) && $_SESSION['estado'] !== null) {
    $_SESSION['pregunta_actual']++;
    $_SESSION['estado'] = null;
    $_SESSION['seleccion'] = null;
    // AQUÍ TAMBIÉN CAMBIAMOS A juego1.php
    header("Location: juego1.php");
    exit;
}
?>