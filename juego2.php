<?php
session_start();
require_once 'config/conexion.php';

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
            echo json_encode(["status" => "success"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    exit; // Detenemos la página para que no devuelva HTML, solo JSON
}
// ------------------------------------------------------

// --- OBTENER PREGUNTAS DESDE LA BASE DE DATOS (Categoría 2) ---
$stmt =$conexion->prepare("
    SELECT texto_pregunta, respuesta_correcta, opcion_falsa_1, opcion_falsa_2 
    FROM preguntas 
    WHERE id_categoria = 2 
    ORDER BY RAND() 
    LIMIT 5
");
$stmt->execute();
$resultados_bd =$stmt->fetchAll(PDO::FETCH_ASSOC);

$preguntas = [];
foreach ($resultados_bd as $fila) {$preguntas[] = [
        "pregunta" => $fila['texto_pregunta'],
        "correcta" => $fila['respuesta_correcta'],
        "falsas" => [$fila['opcion_falsa_1'],$fila['opcion_falsa_2']]
    ];
}

// Validación de seguridad si la tabla no tiene preguntas
if (empty($preguntas)) {
    die("<h2 style='text-align:center; padding:50px; font-family:sans-serif; color:#ef5350; background:white; border-radius:20px; margin:50px;'>Error: No hay preguntas de categoría 2 en la base de datos. Asegúrate de insertarlas en HeidiSQL.</h2>");
}
// --------------------------------------------------------------

require_once 'vistas/juegos/juego2_vista.php';
?>
?>

