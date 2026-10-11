<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/preguntas_juego.php';
require_once __DIR__ . '/../config/fiesta.php';

// --- RECEPTOR OCULTO PARA GUARDAR EL PUNTAJE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_puntaje'])) {
    $id_usuario =$_SESSION['id_usuario'] ?? null;
    $errores = isset($_POST['errores']) ? (int)$_POST['errores'] : 0;
    $aciertos = 5; // El juego exige 5 aciertos para terminar
    $id_sala =$_SESSION['id_sala_activa'] ?? null;

    if ($id_usuario) {
        try {
            // Auto-reparación por si la categoría no existe en la BD
            $conexion->query("INSERT IGNORE INTO categorias_juego (id_categoria, nombre_juego) VALUES (2, 'Caza Conceptos')");

            // Guardamos en la categoría 2
            $sql = "INSERT INTO historial_partidas (id_usuario, id_categoria, puntuacion_final, errores, tiempo_segundos, id_sala) 
                    VALUES (:usuario, 2, :puntuacion, :errores, 75, :sala)";
            $stmt =$conexion->prepare($sql);$stmt->execute([
                ':usuario' => $id_usuario,
                ':puntuacion' => $aciertos,
                ':errores' => $errores,
                ':sala' => $id_sala
            ]);
            echo json_encode(array_merge(["status" => "success"], avanzarFiestaJugador($conexion, 2)));
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    exit; // Detenemos la página para que no devuelva HTML, solo JSON
}
// ------------------------------------------------------

// Carga las preguntas propias de la sala si existen; la práctica usa el banco general.
$idSalaPreguntas = isset($_SESSION['id_sala_activa']) ? (int) $_SESSION['id_sala_activa'] : null;
$preguntas = obtenerPreguntasJuego($conexion, 2, $idSalaPreguntas);

// Validación de seguridad si la tabla no tiene preguntas
if (empty($preguntas)) {
    die("<h2 style='text-align:center; padding:50px; font-family:sans-serif; color:#ef5350; background:white; border-radius:20px; margin:50px;'>Error: No hay preguntas de categoría 2 en la base de datos. Asegúrate de insertarlas en HeidiSQL.</h2>");
}
// --------------------------------------------------------------

require_once __DIR__ . '/../vistas/juegos/juego2_vista.php';
?>
?>
