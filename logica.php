<?php
session_start();
require_once 'config/conexion.php';

// 1. Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// 2. Reiniciar el juego de forma segura
if (isset($_GET['reiniciar'])) {
    unset($_SESSION['preguntas'], $_SESSION['pregunta_actual'], $_SESSION['puntuacion'], $_SESSION['estado'], $_SESSION['seleccion'], $_SESSION['puntaje_guardado'], $_SESSION['error_guardado']);
    
    // Si entró desde "Practicar Solo", desvinculamos la sala
    if (isset($_GET['solo'])) {
        unset($_SESSION['id_sala_activa']);
    }
    
    header("Location: juego1.php");
    exit;
}

// 3. AUTO-REPARACIÓN: Asegurar que exista la categoría 1 en la BD
try {
    $conexion->query("INSERT IGNORE INTO categorias_juego (id_categoria, nombre_juego) VALUES (1, 'Pesca Técnica')");
} catch (PDOException $e) {}

// 4. Cargar preguntas
if (!isset($_SESSION['preguntas'])) {
    try {
        $stmt = $conexion->prepare("SELECT texto_pregunta, respuesta_correcta, opcion_falsa_1, opcion_falsa_2 FROM preguntas ORDER BY RAND() LIMIT 5");
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($resultados) == 0) {
            die("<h2 style='text-align:center; margin-top:50px; color:red;'>Error: No hay preguntas en la base de datos.</h2>");
        }

        $preguntas_formateadas = [];
        foreach ($resultados as $fila) {
            $opciones = [$fila['respuesta_correcta'], $fila['opcion_falsa_1'], $fila['opcion_falsa_2']];
            shuffle($opciones); 
            $preguntas_formateadas[] = [
                "palabra" => $fila['texto_pregunta'],
                "opciones" => $opciones,
                "respuesta" => $fila['respuesta_correcta']
            ];
        }
        $_SESSION['preguntas'] = $preguntas_formateadas;
    } catch (PDOException $e) {
        die("Error al obtener preguntas: " . $e->getMessage());
    }
}

$preguntas = $_SESSION['preguntas'];

if (!isset($_SESSION['pregunta_actual'])) {
    $_SESSION['pregunta_actual'] = 0;
    $_SESSION['puntuacion'] = 0;
    $_SESSION['estado'] = null; 
    $_SESSION['seleccion'] = null;
}

$actual = $_SESSION['pregunta_actual'];
$total = count($preguntas);
$juego_terminado = $actual >= $total;

// 5. GUARDAR RESULTADOS EN LA BD (CON DIAGNÓSTICO)
if ($juego_terminado && !isset($_SESSION['puntaje_guardado'])) {
    try {
        $id_usuario = $_SESSION['id_usuario']; 
        $id_categoria = 1; 
        $puntuacion = $_SESSION['puntuacion'];
        $errores = $total - $puntuacion; 
        $tiempo_simulado = 60; 
        $id_sala = isset($_SESSION['id_sala_activa']) ? $_SESSION['id_sala_activa'] : null;
        
        $sql = "INSERT INTO historial_partidas (id_usuario, id_categoria, puntuacion_final, errores, tiempo_segundos, id_sala) 
                VALUES (:usuario, :categoria, :puntuacion, :errores, :tiempo, :sala)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':usuario' => $id_usuario,
            ':categoria' => $id_categoria,
            ':puntuacion' => $puntuacion,
            ':errores' => $errores,
            ':tiempo' => $tiempo_simulado,
            ':sala' => $id_sala
        ]);

        $_SESSION['puntaje_guardado'] = true;
        $_SESSION['error_guardado'] = null; // Guardado exitoso
        
    } catch (PDOException $e) {
        // Atrapamos el error exacto de SQL y lo guardamos para mostrarlo
        $_SESSION['error_guardado'] = $e->getMessage();
    }
}

// 6. Procesar Respuestas
if (isset($_GET['respuesta']) && $_SESSION['estado'] === null && !$juego_terminado) {
    $seleccion = $_GET['respuesta'];
    $_SESSION['seleccion'] = $seleccion;
    
    if ($seleccion === $preguntas[$actual]['respuesta']) {
        $_SESSION['puntuacion']++;
        $_SESSION['estado'] = 'correcto';
    } else {
        $_SESSION['estado'] = 'incorrecto';
    }
    header("Location: juego1.php");
    exit;
}

if (isset($_GET['siguiente']) && $_SESSION['estado'] !== null) {
    $_SESSION['pregunta_actual']++;
    $_SESSION['estado'] = null;
    $_SESSION['seleccion'] = null;
    header("Location: juego1.php");
    exit;
}
?>