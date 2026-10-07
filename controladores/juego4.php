<?php
session_start();
require_once __DIR__ . '/../config/conexion.php'; 

// --- RECEPTOR OCULTO PARA GUARDAR EL PUNTAJE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_puntaje'])) {
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    $errores = isset($_POST['errores']) ? (int)$_POST['errores'] : 0;
    $aciertos = 5; 
    $id_sala = $_SESSION['id_sala_activa'] ?? null;

    if ($id_usuario) {
        try {
            $conexion->query("INSERT IGNORE INTO categorias_juego (id_categoria, nombre_juego) VALUES (4, 'Explotar Globos')");
            $sql = "INSERT INTO historial_partidas (id_usuario, id_categoria, puntuacion_final, errores, tiempo_segundos, id_sala) 
                    VALUES (:usuario, 4, :puntuacion, :errores, 90, :sala)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':usuario' => $id_usuario, ':puntuacion' => $aciertos, ':errores' => $errores, ':sala' => $id_sala]);
            echo json_encode(["status" => "success"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    exit;
}

// --- OBTENER PREGUNTAS DESDE LA BASE DE DATOS (Categoría 4) ---
$stmt = $conexion->prepare("SELECT texto_pregunta, respuesta_correcta, opcion_falsa_1, opcion_falsa_2 FROM preguntas WHERE id_categoria = 4 ORDER BY RAND() LIMIT 5");
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

// Validación por si aún no metes las preguntas de la cat 4 en la BD
if (empty($preguntas)) {
    die("<h2 style='text-align:center; padding:50px; font-family:sans-serif; color:#ef5350; background:white; border-radius:20px; margin:50px;'>Error: No hay preguntas de categoría 4 en la base de datos. Asegúrate de insertarlas en HeidiSQL.</h2>");
}

// LLAMAMOS AL DISEÑO DEL JUEGO
require_once __DIR__ . '/../vistas/juegos/juego4_vista.php';
?>