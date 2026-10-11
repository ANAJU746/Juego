<?php
session_start();
require_once __DIR__ . '/../config/conexion.php'; 
require_once __DIR__ . '/../config/preguntas_juego.php';
require_once __DIR__ . '/../config/fiesta.php';

// --- RECEPTOR OCULTO PARA GUARDAR EL PUNTAJE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_puntaje'])) {
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    $errores = isset($_POST['errores']) ? (int)$_POST['errores'] : 0;
    $aciertos = 5; 
    $id_sala = $_SESSION['id_sala_activa'] ?? null;

    if ($id_usuario) {
        try {
            $conexion->query("INSERT INTO categorias_juego (id_categoria, nombre_juego) VALUES (4, 'Atrapar Globos') ON DUPLICATE KEY UPDATE nombre_juego = VALUES(nombre_juego)");
            $sql = "INSERT INTO historial_partidas (id_usuario, id_categoria, puntuacion_final, errores, tiempo_segundos, id_sala) 
                    VALUES (:usuario, 4, :puntuacion, :errores, 90, :sala)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':usuario' => $id_usuario, ':puntuacion' => $aciertos, ':errores' => $errores, ':sala' => $id_sala]);
            echo json_encode(array_merge(["status" => "success"], avanzarFiestaJugador($conexion, 4)));
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    exit;
}

// Carga las preguntas propias de la sala si existen; la práctica usa el banco general.
$idSalaPreguntas = isset($_SESSION['id_sala_activa']) ? (int) $_SESSION['id_sala_activa'] : null;
$preguntas = obtenerPreguntasJuego($conexion, 4, $idSalaPreguntas);

// Validación por si aún no metes las preguntas de la cat 4 en la BD
if (empty($preguntas)) {
    die("<h2 style='text-align:center; padding:50px; font-family:sans-serif; color:#ef5350; background:white; border-radius:20px; margin:50px;'>Error: No hay preguntas de categoría 4 en la base de datos. Asegúrate de insertarlas en HeidiSQL.</h2>");
}

// LLAMAMOS AL DISEÑO DEL JUEGO
require_once __DIR__ . '/../vistas/juegos/juego4_vista.php';
?>