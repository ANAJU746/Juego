<?php
session_start();
require_once 'config/conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_puntaje'])) {
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    $errores = isset($_POST['errores']) ? (int)$_POST['errores'] : 0;
    $aciertos = 5; 
    $id_sala = $_SESSION['id_sala_activa'] ?? null;

    if ($id_usuario) {
        try {
            $conexion->query("INSERT IGNORE INTO categorias_juego (id_categoria, nombre_juego) VALUES (5, 'Torre de Conceptos')");
            $sql = "INSERT INTO historial_partidas (id_usuario, id_categoria, puntuacion_final, errores, tiempo_segundos, id_sala) 
                    VALUES (:usuario, 5, :puntuacion, :errores, 120, :sala)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':usuario' => $id_usuario, ':puntuacion' => $aciertos, ':errores' => $errores, ':sala' => $id_sala]);
            echo json_encode(["status" => "success"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    exit;
}

// Obtener preguntas de la Categoría 5
$stmt = $conexion->prepare("SELECT texto_pregunta, respuesta_correcta, opcion_falsa_1, opcion_falsa_2 FROM preguntas WHERE id_categoria = 5 ORDER BY RAND() LIMIT 5");
$stmt->execute();
$resultados_bd = $stmt->fetchAll(PDO::FETCH_ASSOC);

$preguntas = [];
foreach ($resultados_bd as $fila) {
    $preguntas[] = [
        "pregunta" => $fila['texto_pregunta'],
        "correcta" => $fila['respuesta_correcta'],
        "falsas" => [$fila['opcion_falsa_1'], $fila['opcion_falsa_2']]
    ];
}

if (empty($preguntas)) {
    die("<h2 style='text-align:center; padding:50px; font-family:sans-serif; color:#ef5350;'>Error: Faltan preguntas de categoría 5 en la Base de Datos.</h2>");
}

require_once 'vistas/juegos/juego5_vista.php';
?>