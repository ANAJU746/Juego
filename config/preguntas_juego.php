<?php

function obtenerPreguntasJuego(PDO $conexion, int $idCategoria, ?int $idSala): array
{
    if ($idSala !== null) {
        $stmtActividad = $conexion->prepare("
            SELECT COUNT(*)
            FROM salas
            WHERE id_sala = :id_sala
                AND estado = 'jugando'
                AND (
                    id_categoria = :id_categoria
                    OR (
                        id_categoria = 6
                        AND :es_fiesta = 1
                    )
                )
        ");
        $stmtActividad->execute([
            ':id_sala' => $idSala,
            ':id_categoria' => $idCategoria,
            ':es_fiesta' => (int) (
                isset($_SESSION['id_sala_fiesta'], $_SESSION['paso_fiesta'])
                && (int) $_SESSION['id_sala_fiesta'] === $idSala
                && (int) $_SESSION['paso_fiesta'] === $idCategoria
            ),
        ]);
        $actividadDeSala = (int) $stmtActividad->fetchColumn() > 0;

        if ($actividadDeSala) {
            $tablaSalaDisponible = (int) $conexion->query("
                SELECT COUNT(*)
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'preguntas_sala'
            ")->fetchColumn() > 0;
        } else {
            $tablaSalaDisponible = false;
        }

        if ($tablaSalaDisponible) {
            $stmtSala = $conexion->prepare("
                SELECT texto_pregunta, respuesta_correcta, opcion_falsa_1, opcion_falsa_2
                FROM preguntas_sala
                WHERE id_sala = :id_sala
                ORDER BY RAND()
                LIMIT 5
            ");
            $stmtSala->execute([':id_sala' => $idSala]);
            $preguntasSala = $stmtSala->fetchAll(PDO::FETCH_ASSOC);

            if (count($preguntasSala) === 5) {
                return formatearPreguntasJuego($preguntasSala);
            }
        }
    }

    $stmt = $conexion->prepare("
        SELECT texto_pregunta, respuesta_correcta, opcion_falsa_1, opcion_falsa_2
        FROM preguntas
        WHERE id_categoria = :id_categoria
        ORDER BY RAND()
        LIMIT 5
    ");
    $stmt->execute([':id_categoria' => $idCategoria]);

    return formatearPreguntasJuego($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function formatearPreguntasJuego(array $filas): array
{
    $preguntas = [];
    foreach ($filas as $fila) {
        $preguntas[] = [
            'pregunta' => $fila['texto_pregunta'],
            'correcta' => $fila['respuesta_correcta'],
            'falsas' => [$fila['opcion_falsa_1'], $fila['opcion_falsa_2']],
        ];
    }

    return $preguntas;
}
