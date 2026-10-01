<?php
session_start();
require_once 'config/conexion.php';

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_sala_activa'])) {
    header("Location: index.php");
    exit;
}

$id_sala = $_SESSION['id_sala_activa'];

// Consultar el estado de la sala
$stmt = $conexion->prepare("SELECT estado FROM salas WHERE id_sala = :id");
$stmt->execute([':id' => $id_sala]);
$sala = $stmt->fetch(PDO::FETCH_ASSOC);

// Si el maestro presionó INICIAR, mandamos al alumno al juego
if ($sala['estado'] === 'jugando') {
    header("Location: juego1.php?reiniciar=" . time());
    exit;
}

// Si la sala se cerró, lo sacamos
if ($sala['estado'] === 'finalizada') {
    unset($_SESSION['id_sala_activa']);
    header("Location: index.php");
    exit;
}
?>
