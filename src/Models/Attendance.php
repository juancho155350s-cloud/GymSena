<?php
namespace GymSena\Models;

use GymSena\Core\Database;
use PDO;

class Attendance
{
    public static function getActiveVisit(int $gymId, int $learnerId, string $date): ?array
    {
        if (Database::isFallback()) {
            $asistencias = Database::getFallbackData('asistencias');
            foreach ($asistencias as $a) {
                if ($a['gimnasio_id'] == $gymId && $a['aprendiz_id'] == $learnerId && $a['fecha'] === $date && empty($a['hora_salida'])) {
                    return $a;
                }
            }
            return null;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM asistencias 
            WHERE gimnasio_id = ? AND aprendiz_id = ? AND fecha = ? AND hora_salida IS NULL 
            LIMIT 1
        ");
        $stmt->execute([$gymId, $learnerId, $date]);
        return $stmt->fetch() ?: null;
    }

    public static function checkIn(int $gymId, int $learnerId, string $method = 'QR_AUTONOMO', ?string $observations = null): array
    {
        $fecha = date('Y-m-d');
        $horaEntrada = date('H:i:s');

        $record = [
            'gimnasio_id' => $gymId,
            'aprendiz_id' => $learnerId,
            'fecha' => $fecha,
            'hora_entrada' => $horaEntrada,
            'hora_salida' => null,
            'duracion_minutos' => null,
            'metodo_ingreso' => $method,
            'cierre_automatico' => 0,
            'observaciones' => $observations,
            'creado_en' => date('Y-m-d H:i:s')
        ];

        if (Database::isFallback()) {
            $asistencias = Database::getFallbackData('asistencias');
            $newId = count($asistencias) > 0 ? max(array_column($asistencias, 'id')) + 1 : 1;
            $record['id'] = $newId;
            $asistencias[] = $record;
            Database::saveFallbackData('asistencias', $asistencias);
            return $record;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO asistencias 
            (gimnasio_id, aprendiz_id, fecha, hora_entrada, metodo_ingreso, cierre_automatico, observaciones)
            VALUES (?, ?, ?, ?, ?, 0, ?)
        ");
        $stmt->execute([$gymId, $learnerId, $fecha, $horaEntrada, $method, $observations]);
        $record['id'] = (int)$pdo->lastInsertId();
        return $record;
    }

    public static function checkOut(int $attendanceId, ?string $observations = null): array
    {
        $horaSalida = date('H:i:s');

        if (Database::isFallback()) {
            $asistencias = Database::getFallbackData('asistencias');
            $updated = null;
            foreach ($asistencias as &$a) {
                if ($a['id'] == $attendanceId) {
                    $a['hora_salida'] = $horaSalida;
                    // Calcular duración en minutos
                    $tsEntrada = strtotime("{$a['fecha']} {$a['hora_entrada']}");
                    $tsSalida = strtotime("{$a['fecha']} {$horaSalida}");
                    $duracion = max(1, round(($tsSalida - $tsEntrada) / 60));
                    $a['duracion_minutos'] = $duracion;
                    if ($observations) {
                        $a['observaciones'] = trim(($a['observaciones'] ?? '') . ' ' . $observations);
                    }
                    $updated = $a;
                    break;
                }
            }
            Database::saveFallbackData('asistencias', $asistencias);
            return $updated ?: [];
        }

        $pdo = Database::getConnection();
        // Obtener la entrada previa para calcular duración
        $stmt = $pdo->prepare("SELECT fecha, hora_entrada FROM asistencias WHERE id = ?");
        $stmt->execute([$attendanceId]);
        $row = $stmt->fetch();

        $duracion = 0;
        if ($row) {
            $tsEntrada = strtotime("{$row['fecha']} {$row['hora_entrada']}");
            $tsSalida = strtotime("{$row['fecha']} {$horaSalida}");
            $duracion = max(1, round(($tsSalida - $tsEntrada) / 60));
        }

        $stmtUpdate = $pdo->prepare("
            UPDATE asistencias 
            SET hora_salida = ?, duracion_minutos = ?, observaciones = CONCAT(COALESCE(observaciones, ''), ' ', COALESCE(?, ''))
            WHERE id = ?
        ");
        $stmtUpdate->execute([$horaSalida, $duracion, $observations, $attendanceId]);

        return [
            'id' => $attendanceId,
            'hora_salida' => $horaSalida,
            'duracion_minutos' => $duracion
        ];
    }

    public static function getCurrentlyInside(int $gymId): array
    {
        $hoy = date('Y-m-d');

        if (Database::isFallback()) {
            $asistencias = Database::getFallbackData('asistencias');
            $aprendices = Database::getFallbackData('aprendices');
            $learnersById = [];
            foreach ($aprendices as $l) {
                $learnersById[$l['id']] = $l;
            }

            $inside = [];
            foreach ($asistencias as $a) {
                if ($a['gimnasio_id'] == $gymId && $a['fecha'] === $hoy && empty($a['hora_salida'])) {
                    $l = $learnersById[$a['aprendiz_id']] ?? [];
                    $inside[] = array_merge($a, [
                        'nombres' => $l['nombres'] ?? 'Aprendiz',
                        'apellidos' => $l['apellidos'] ?? '',
                        'documento' => $l['documento'] ?? '',
                        'ficha' => $l['ficha'] ?? '',
                        'programa_formacion' => $l['programa_formacion'] ?? ''
                    ]);
                }
            }
            return array_reverse($inside);
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT a.*, l.nombres, l.apellidos, l.documento, l.ficha, l.programa_formacion
            FROM asistencias a
            JOIN aprendices l ON a.aprendiz_id = l.id
            WHERE a.gimnasio_id = ? AND a.fecha = ? AND a.hora_salida IS NULL
            ORDER BY a.hora_entrada DESC
        ");
        $stmt->execute([$gymId, $hoy]);
        return $stmt->fetchAll() ?: [];
    }

    public static function getHistoryByLearner(int $learnerId, int $limit = 30): array
    {
        if (Database::isFallback()) {
            $asistencias = Database::getFallbackData('asistencias');
            $history = [];
            foreach ($asistencias as $a) {
                if ($a['aprendiz_id'] == $learnerId) {
                    $history[] = $a;
                }
            }
            usort($history, fn($x, $y) => strcmp($y['fecha'] . $y['hora_entrada'], $x['fecha'] . $x['hora_entrada']));
            return array_slice($history, 0, $limit);
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT a.*, g.nombre AS gimnasio_nombre
            FROM asistencias a
            JOIN gimnasios g ON a.gimnasio_id = g.id
            WHERE a.aprendiz_id = ?
            ORDER BY a.fecha DESC, a.hora_entrada DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $learnerId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    public static function getStatsSummary(int $gymId, string $startDate, string $endDate): array
    {
        if (Database::isFallback()) {
            $asistencias = Database::getFallbackData('asistencias');
            $totalVisitas = 0;
            $uniqueAprendices = [];
            $totalMinutos = 0;
            $minutosCount = 0;

            foreach ($asistencias as $a) {
                if ($a['gimnasio_id'] == $gymId && $a['fecha'] >= $startDate && $a['fecha'] <= $endDate) {
                    $totalVisitas++;
                    $uniqueAprendices[$a['aprendiz_id']] = true;
                    if (!empty($a['duracion_minutos'])) {
                        $totalMinutos += (int)$a['duracion_minutos'];
                        $minutosCount++;
                    }
                }
            }

            return [
                'total_visitas' => $totalVisitas,
                'aprendices_unicos' => count($uniqueAprendices),
                'promedio_minutos' => $minutosCount > 0 ? round($totalMinutos / $minutosCount) : 0
            ];
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(id) AS total_visitas,
                COUNT(DISTINCT aprendiz_id) AS aprendices_unicos,
                ROUND(AVG(duracion_minutos)) AS promedio_minutos
            FROM asistencias
            WHERE gimnasio_id = ? AND fecha BETWEEN ? AND ?
        ");
        $stmt->execute([$gymId, $startDate, $endDate]);
        return $stmt->fetch() ?: ['total_visitas' => 0, 'aprendices_unicos' => 0, 'promedio_minutos' => 0];
    }

    public static function getPeakHours(int $gymId, string $startDate, string $endDate): array
    {
        if (Database::isFallback()) {
            $asistencias = Database::getFallbackData('asistencias');
            $hours = [];
            for ($h = 6; $h <= 20; $h++) {
                $hours[sprintf('%02d:00', $h)] = 0;
            }
            foreach ($asistencias as $a) {
                if ($a['gimnasio_id'] == $gymId && $a['fecha'] >= $startDate && $a['fecha'] <= $endDate) {
                    $hour = substr($a['hora_entrada'], 0, 2) . ':00';
                    if (isset($hours[$hour])) {
                        $hours[$hour]++;
                    }
                }
            }
            return $hours;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT HOUR(hora_entrada) as hora, COUNT(id) as total
            FROM asistencias
            WHERE gimnasio_id = ? AND fecha BETWEEN ? AND ?
            GROUP BY HOUR(hora_entrada)
            ORDER BY hora ASC
        ");
        $stmt->execute([$gymId, $startDate, $endDate]);
        $rows = $stmt->fetchAll();

        $result = [];
        for ($h = 6; $h <= 20; $h++) {
            $result[sprintf('%02d:00', $h)] = 0;
        }
        foreach ($rows as $row) {
            $key = sprintf('%02d:00', $row['hora']);
            if (isset($result[$key])) {
                $result[$key] = (int)$row['total'];
            }
        }
        return $result;
    }

    public static function getTopPrograms(int $gymId, string $startDate, string $endDate, int $limit = 5): array
    {
        if (Database::isFallback()) {
            $asistencias = Database::getFallbackData('asistencias');
            $aprendices = Database::getFallbackData('aprendices');
            $map = [];
            foreach ($aprendices as $l) {
                $map[$l['id']] = $l['programa_formacion'];
            }

            $counts = [];
            foreach ($asistencias as $a) {
                if ($a['gimnasio_id'] == $gymId && $a['fecha'] >= $startDate && $a['fecha'] <= $endDate) {
                    $prog = $map[$a['aprendiz_id']] ?? 'Otro Programa';
                    $counts[$prog] = ($counts[$prog] ?? 0) + 1;
                }
            }
            arsort($counts);
            return array_slice($counts, 0, $limit, true);
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT l.programa_formacion, COUNT(a.id) as total
            FROM asistencias a
            JOIN aprendices l ON a.aprendiz_id = l.id
            WHERE a.gimnasio_id = ? AND a.fecha BETWEEN ? AND ?
            GROUP BY l.programa_formacion
            ORDER BY total DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $gymId, PDO::PARAM_INT);
        $stmt->bindValue(2, $startDate);
        $stmt->bindValue(3, $endDate);
        $stmt->bindValue(4, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $res = [];
        foreach ($stmt->fetchAll() as $r) {
            $res[$r['programa_formacion']] = (int)$r['total'];
        }
        return $res;
    }

    public static function getAllForExport(int $gymId, string $startDate, string $endDate): array
    {
        if (Database::isFallback()) {
            $asistencias = Database::getFallbackData('asistencias');
            $aprendices = Database::getFallbackData('aprendices');
            $learnersById = [];
            foreach ($aprendices as $l) {
                $learnersById[$l['id']] = $l;
            }

            $export = [];
            foreach ($asistencias as $a) {
                if ($a['gimnasio_id'] == $gymId && $a['fecha'] >= $startDate && $a['fecha'] <= $endDate) {
                    $l = $learnersById[$a['aprendiz_id']] ?? [];
                    $export[] = [
                        'fecha' => $a['fecha'],
                        'documento' => $l['documento'] ?? '',
                        'aprendiz' => ($l['nombres'] ?? '') . ' ' . ($l['apellidos'] ?? ''),
                        'ficha' => $l['ficha'] ?? '',
                        'programa' => $l['programa_formacion'] ?? '',
                        'hora_entrada' => $a['hora_entrada'],
                        'hora_salida' => $a['hora_salida'] ?? 'EN CURSO',
                        'duracion_minutos' => $a['duracion_minutos'] ?? '--',
                        'metodo' => $a['metodo_ingreso'],
                        'cierre_automatico' => !empty($a['cierre_automatico']) ? 'SÍ' : 'NO'
                    ];
                }
            }
            return $export;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT 
                a.fecha,
                l.documento,
                CONCAT(l.nombres, ' ', l.apellidos) AS aprendiz,
                l.ficha,
                l.programa_formacion AS programa,
                a.hora_entrada,
                COALESCE(a.hora_salida, 'EN CURSO') AS hora_salida,
                COALESCE(a.duracion_minutos, '--') AS duracion_minutos,
                a.metodo_ingreso AS metodo,
                IF(a.cierre_automatico = 1, 'SÍ', 'NO') AS cierre_automatico
            FROM asistencias a
            JOIN aprendices l ON a.aprendiz_id = l.id
            WHERE a.gimnasio_id = ? AND a.fecha BETWEEN ? AND ?
            ORDER BY a.fecha DESC, a.hora_entrada DESC
        ");
        $stmt->execute([$gymId, $startDate, $endDate]);
        return $stmt->fetchAll() ?: [];
    }

    public static function closeAllOpenVisitsForDate(string $date, string $closeTime = '20:00:00'): int
    {
        $count = 0;
        if (Database::isFallback()) {
            $asistencias = Database::getFallbackData('asistencias');
            foreach ($asistencias as &$a) {
                if ($a['fecha'] === $date && empty($a['hora_salida'])) {
                    $a['hora_salida'] = $closeTime;
                    $a['cierre_automatico'] = 1;
                    $a['observaciones'] = 'Cierre automático nocturno por fin de jornada';
                    $tsEntrada = strtotime("{$a['fecha']} {$a['hora_entrada']}");
                    $tsSalida = strtotime("{$a['fecha']} {$closeTime}");
                    $a['duracion_minutos'] = max(1, round(($tsSalida - $tsEntrada) / 60));
                    $count++;
                }
            }
            Database::saveFallbackData('asistencias', $asistencias);
            return $count;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE asistencias
            SET hora_salida = ?, 
                cierre_automatico = 1, 
                observaciones = CONCAT(COALESCE(observaciones, ''), ' [Cierre automático nocturno]'),
                duracion_minutos = GREATEST(1, ROUND(TIME_TO_SEC(TIMEDIFF(?, hora_entrada)) / 60))
            WHERE fecha = ? AND hora_salida IS NULL
        ");
        $stmt->execute([$closeTime, $closeTime, $date]);
        return $stmt->rowCount();
    }
}

