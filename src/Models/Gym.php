<?php
namespace GymSena\Models;

use GymSena\Core\Database;
use PDO;

class Gym
{
    public static function getById(int $id): ?array
    {
        if (Database::isFallback()) {
            $gims = Database::getFallbackData('gimnasios');
            foreach ($gims as $g) {
                if ($g['id'] == $id) {
                    return $g;
                }
            }
            return null;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM gimnasios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function getAforo(int $gymId): array
    {
        $gym = self::getById($gymId);
        $capacidad = $gym ? (int)$gym['capacidad_maxima'] : 30;
        $hoy = date('Y-m-d');

        if (Database::isFallback()) {
            $asistencias = Database::getFallbackData('asistencias');
            $ocupacion = 0;
            foreach ($asistencias as $a) {
                if ($a['gimnasio_id'] == $gymId && $a['fecha'] === $hoy && empty($a['hora_salida'])) {
                    $ocupacion++;
                }
            }
            $disponibles = max(0, $capacidad - $ocupacion);
            return [
                'gimnasio_id' => $gymId,
                'nombre' => $gym['nombre'] ?? 'Gimnasio SENA',
                'capacidad_maxima' => $capacidad,
                'ocupacion_actual' => $ocupacion,
                'cupos_disponibles' => $disponibles,
                'porcentaje_ocupacion' => $capacidad > 0 ? round(($ocupacion / $capacidad) * 100) : 0,
                'estado' => $gym['estado'] ?? 'ABIERTO'
            ];
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT COUNT(id) as ocupacion 
            FROM asistencias 
            WHERE gimnasio_id = ? AND fecha = ? AND hora_salida IS NULL
        ");
        $stmt->execute([$gymId, $hoy]);
        $res = $stmt->fetch();
        $ocupacion = (int)($res['ocupacion'] ?? 0);
        $disponibles = max(0, $capacidad - $ocupacion);

        return [
            'gimnasio_id' => $gymId,
            'nombre' => $gym['nombre'] ?? 'Gimnasio SENA',
            'capacidad_maxima' => $capacidad,
            'ocupacion_actual' => $ocupacion,
            'cupos_disponibles' => $disponibles,
            'porcentaje_ocupacion' => $capacidad > 0 ? round(($ocupacion / $capacidad) * 100) : 0,
            'estado' => $gym['estado'] ?? 'ABIERTO'
        ];
    }

    public static function updateCapacity(int $gymId, int $newCapacity): bool
    {
        if (Database::isFallback()) {
            $gims = Database::getFallbackData('gimnasios');
            foreach ($gims as &$g) {
                if ($g['id'] == $gymId) {
                    $g['capacidad_maxima'] = $newCapacity;
                    break;
                }
            }
            Database::saveFallbackData('gimnasios', $gims);
            return true;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE gimnasios SET capacidad_maxima = ? WHERE id = ?");
        return $stmt->execute([$newCapacity, $gymId]);
    }
}

