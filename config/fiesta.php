<?php

function avanzarFiestaJugador(PDO $conexion, int $idJuego): array
{
    if (!isset($_SESSION['id_sala_activa'], $_SESSION['id_sala_fiesta'], $_SESSION['paso_fiesta'])) {
        return [];
    }

    $idSala = (int) $_SESSION['id_sala_activa'];
    if ($idSala !== (int) $_SESSION['id_sala_fiesta'] || (int) $_SESSION['paso_fiesta'] !== $idJuego) {
        return [];
    }

    $stmt = $conexion->prepare("
        SELECT COUNT(*)
        FROM salas
        WHERE id_sala = :id_sala AND estado = 'jugando' AND id_categoria = 6
    ");
    $stmt->execute([':id_sala' => $idSala]);
    if ((int) $stmt->fetchColumn() !== 1) {
        return [];
    }

    if ($idJuego < 5) {
        $proximoJuego = $idJuego + 1;
        $_SESSION['paso_fiesta'] = $proximoJuego;
        return ['proximo_juego' => $proximoJuego];
    }

    $_SESSION['paso_fiesta'] = 6;
    return ['fiesta_completada' => true];
}
