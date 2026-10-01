<?php
session_start();
require_once 'config/conexion.php';

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario =$_SESSION['id_usuario'];
$nombre_usuario =$_SESSION['nombre_usuario']; 

// Lógica para UNIRSE A LA SALA
$error_sala = '';
if (isset($_POST['unirse_sala'])) {
    $codigo_ingresado = trim(strtoupper($_POST['codigo_sala']));
    
    $stmt_sala =$conexion->prepare("SELECT id_sala FROM salas WHERE codigo_sala = :codigo AND estado != 'finalizada'");
    $stmt_sala->execute([':codigo' =>$codigo_ingresado]);
    $sala =$stmt_sala->fetch(PDO::FETCH_ASSOC);
    
    if ($sala) {
        $_SESSION['id_sala_activa'] =$sala['id_sala'];
        header("Location: lobby.php");
        exit;
    } else {
        $error_sala = "El PIN no existe o la sala fue cerrada.";
    }
}

// 1. Obtener Estadísticas Generales
$juegos_jugados = 0; $errores_totales = 0; $dominio_porcentaje = 0;
try {
    $stmt =$conexion->prepare("SELECT COUNT(id_partida) as total_partidas, SUM(errores) as total_errores, SUM(puntuacion_final) as total_puntos FROM historial_partidas WHERE id_usuario = :id");
    $stmt->execute([':id' =>$id_usuario]);
    $stats =$stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($stats['total_partidas'] > 0) {
        $juegos_jugados =$stats['total_partidas'];
        $errores_totales =$stats['total_errores'] ?? 0;
        $total_puntos =$stats['total_puntos'] ?? 0;
        $total_preguntas = $total_puntos +$errores_totales;
        if ($total_preguntas > 0) {
            $dominio_porcentaje = round(($total_puntos / $total_preguntas) * 100);         }     } } catch (PDOException$e) {}

// 2. Obtener Historial Detallado
$historial_partidas = [];
try {
    $stmt_hist =$conexion->prepare("
        SELECT c.nombre_juego, h.puntuacion_final, h.errores, h.id_sala 
        FROM historial_partidas h
        JOIN categorias_juego c ON h.id_categoria = c.id_categoria
        WHERE h.id_usuario = :id
        ORDER BY h.fecha_jugada DESC LIMIT 6
    ");
    $stmt_hist->execute([':id' =>$id_usuario]);
    $historial_partidas =$stmt_hist->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

require_once 'vistas/paneles/index_vista.php';
?>
