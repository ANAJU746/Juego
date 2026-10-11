<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

$consultaEstado = isset($_GET['api']) && $_GET['api'] === 'estado';

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_sala_activa'])) {
    if ($consultaEstado) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['estado' => 'sesion_expirada']);
        exit;
    }

    header("Location: ../index.php");
    exit;
}

$id_sala = $_SESSION['id_sala_activa'];

// Consultar el estado de la sala sin recargar la vista durante la espera
$stmt = $conexion->prepare("SELECT estado, id_categoria FROM salas WHERE id_sala = :id");
$stmt->execute([':id' => $id_sala]);
$sala = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sala) {
    unset($_SESSION['id_sala_activa']);
    if ($consultaEstado) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['estado' => 'finalizada']);
        exit;
    }

    header("Location: ../index.php");
    exit;
}

$esFiesta = $sala['estado'] === 'jugando' && (int) $sala['id_categoria'] === 6;
if ($esFiesta) {
    if (
        (int) ($_SESSION['id_sala_fiesta'] ?? 0) !== (int) $id_sala
        || !isset($_SESSION['paso_fiesta'])
    ) {
        $_SESSION['id_sala_fiesta'] = (int) $id_sala;
        $_SESSION['paso_fiesta'] = 1;
    }
} elseif ((int) ($_SESSION['id_sala_fiesta'] ?? 0) === (int) $id_sala) {
    unset($_SESSION['id_sala_fiesta'], $_SESSION['paso_fiesta']);
}

if ($consultaEstado) {
    if ($sala['estado'] === 'finalizada') {
        unset($_SESSION['id_sala_activa']);
    }

    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo json_encode([
        'estado' => $sala['estado'],
        'id_categoria' => $sala['id_categoria'] === null ? null : (int) $sala['id_categoria'],
        'modo_fiesta' => $esFiesta,
        'paso_fiesta' => $esFiesta ? (int) $_SESSION['paso_fiesta'] : null,
        'fiesta_completada' => $esFiesta && (int) $_SESSION['paso_fiesta'] > 5,
    ]);
    exit;
}

// Si el maestro presionó INICIAR, mandamos al alumno al juego
if ($sala['estado'] === 'jugando') {
    if ($esFiesta) {
        if ((int) $_SESSION['paso_fiesta'] > 5) {
            $fiestaCompletada = true;
        } else {
            $id_juego = (int) $_SESSION['paso_fiesta'];
            header("Location: juego{$id_juego}.php?fiesta=1&reiniciar=" . time());
            exit;
        }
    } else {
        $id_juego = (int) $sala['id_categoria'];
        if ($id_juego < 1 || $id_juego > 5) {
            $id_juego = 1;
        }

        header("Location: juego{$id_juego}.php?reiniciar=" . time());
        exit;
    }
}

// Si la sala se cerró, lo sacamos
if ($sala['estado'] === 'finalizada') {
    unset($_SESSION['id_sala_activa']);
    header("Location: ../index.php");
    exit;
}

require __DIR__ . '/../vistas/paneles/lobby_vista.php';
?>
