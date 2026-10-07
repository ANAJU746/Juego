<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Validar que sea maestro
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'maestro') {
    header("Location: login.php");
    exit;
}

$id_maestro = $_SESSION['id_usuario'];

// 1. Buscar si el maestro ya tiene una sala activa
$stmt = $conexion->prepare("SELECT * FROM salas WHERE id_maestro = :id AND estado != 'finalizada'");
$stmt->execute([':id' => $id_maestro]);
$sala_activa = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Si no tiene sala, le creamos una automáticamente al entrar
if (!$sala_activa) {
    $codigo = strtoupper(substr(md5(uniqid()), 0, 5));
    $stmt_insert = $conexion->prepare("INSERT INTO salas (id_maestro, codigo_sala, estado) VALUES (:id, :codigo, 'espera')");
    $stmt_insert->execute([':id' => $id_maestro, ':codigo' => $codigo]);
    
    $stmt->execute([':id' => $id_maestro]);
    $sala_activa = $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- ACCIONES DEL MAESTRO ---

// A) Lanzar un juego específico a las pantallas de los alumnos
if (isset($_POST['lanzar_juego'])) {
    $id_juego = $_POST['id_juego'];
    $stmt_update = $conexion->prepare("UPDATE salas SET estado = 'jugando', id_categoria = :juego WHERE id_sala = :sala");
    $stmt_update->execute([':juego' => $id_juego, ':sala' => $sala_activa['id_sala']]);
    header("Location: panel_maestro.php");
    exit;
}

// B) Pausar y regresar a todos los alumnos al Lobby
if (isset($_POST['volver_lobby'])) {
    $stmt_update = $conexion->prepare("UPDATE salas SET estado = 'espera', id_categoria = NULL WHERE id_sala = :sala");
    $stmt_update->execute([':sala' => $sala_activa['id_sala']]);
    header("Location: panel_maestro.php");
    exit;
}

// C) Cerrar sala definitivamente (expulsar a todos)
if (isset($_POST['cerrar_sala'])) {
    $stmt_update = $conexion->prepare("UPDATE salas SET estado = 'finalizada' WHERE id_sala = :sala");
    $stmt_update->execute([':sala' => $sala_activa['id_sala']]);
    header("Location: panel_maestro.php");
    exit;
}

// --- API EN TIEMPO REAL (Para el JavaScript de la vista) ---
// Si la vista nos pide el ranking por AJAX, le mandamos puro JSON y detenemos el código.
if (isset($_GET['api']) && $_GET['api'] == 'ranking') {
    header('Content-Type: application/json');
    $stmt_ranking = $conexion->prepare("
        SELECT u.nombre, h.puntuacion_final, h.errores 
        FROM historial_partidas h
        JOIN usuarios u ON h.id_usuario = u.id_usuario
        WHERE h.id_sala = :id_sala
        ORDER BY h.puntuacion_final DESC, h.errores ASC
    ");
    $stmt_ranking->execute([':id_sala' => $sala_activa['id_sala']]);
    $ranking = $stmt_ranking->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($ranking);
    exit;
}

// Si no es petición API, cargamos la Vista normal
require_once __DIR__ . '/../vistas/paneles/panel_maestro_vista.php';
?>