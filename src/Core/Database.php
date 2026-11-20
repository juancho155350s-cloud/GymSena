<?php
namespace GymSena\Core;

use PDO;
use PDOException;

/**
 * Clase Database: Patrón Singleton / Factory con soporte de conmutación por fallo.
 */
class Database
{
    private static ?PDO $pdo = null;
    private static bool $isFallback = false;
    private static ?string $fallbackFile = null;
    private static array $fallbackData = [];

    public static function getConnection(): ?PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $config = require dirname(__DIR__, 2) . '/config/database.php';

        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            self::$pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            self::$isFallback = false;
            return self::$pdo;
        } catch (PDOException $e) {
            if (!empty($config['allow_file_fallback'])) {
                self::$isFallback = true;
                self::$fallbackFile = $config['fallback_storage_path'];
                self::initFallbackStorage();
                return null;
            }
            throw $e;
        }
    }

    public static function isFallback(): bool
    {
        if (self::$pdo === null && !self::$isFallback) {
            self::getConnection();
        }
        return self::$isFallback;
    }

    private static function initFallbackStorage(): void
    {
        if (self::$fallbackFile === null) {
            $config = require dirname(__DIR__, 2) . '/config/database.php';
            self::$fallbackFile = $config['fallback_storage_path'];
        }

        $dir = dirname(self::$fallbackFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        if (!file_exists(self::$fallbackFile)) {
            $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
            $monitorHash = password_hash('monitor123', PASSWORD_DEFAULT);

            // Semilla limpia de producción
            $seedData = [
                'sedes' => [
                    [
                        'id' => 1,
                        'codigo_centro' => '9201',
                        'nombre' => 'Centro de Servicios y Gestión Empresarial',
                        'regional' => 'Antioquia',
                        'estado' => 'ACTIVO'
                    ]
                ],
                'gimnasios' => [
                    [
                        'id' => 1,
                        'sede_id' => 1,
                        'nombre' => 'Gimnasio Principal - Bienestar al Aprendiz',
                        'capacidad_maxima' => 30,
                        'latitud' => 6.25184,
                        'longitud' => -75.56359,
                        'radio_metros_tolerancia' => 150,
                        'pin_seguridad_diario' => 'SENA26',
                        'estado' => 'ABIERTO'
                    ]
                ],
                'horarios_operacion' => [
                    ['id' => 1, 'gimnasio_id' => 1, 'dia_semana' => 'LUNES', 'hora_apertura' => '06:00:00', 'hora_cierre' => '20:00:00', 'activo' => 1],
                    ['id' => 2, 'gimnasio_id' => 1, 'dia_semana' => 'MARTES', 'hora_apertura' => '06:00:00', 'hora_cierre' => '20:00:00', 'activo' => 1],
                    ['id' => 3, 'gimnasio_id' => 1, 'dia_semana' => 'MIERCOLES', 'hora_apertura' => '06:00:00', 'hora_cierre' => '20:00:00', 'activo' => 1],
                    ['id' => 4, 'gimnasio_id' => 1, 'dia_semana' => 'JUEVES', 'hora_apertura' => '06:00:00', 'hora_cierre' => '20:00:00', 'activo' => 1],
                    ['id' => 5, 'gimnasio_id' => 1, 'dia_semana' => 'VIERNES', 'hora_apertura' => '06:00:00', 'hora_cierre' => '20:00:00', 'activo' => 1],
                ],
                'usuarios_sistema' => [
                    [
                        'id' => 1,
                        'sede_id' => 1,
                        'gimnasio_id' => null,
                        'documento' => '1000000001',
                        'nombre_completo' => 'Instructor Encargado Gimnasio',
                        'email' => 'admin@sena.edu.co',
                        'password_hash' => $adminHash,
                        'rol' => 'ADMINISTRADOR',
                        'estado' => 'ACTIVO'
                    ],
                    [
                        'id' => 2,
                        'sede_id' => 1,
                        'gimnasio_id' => 1,
                        'documento' => '1000000002',
                        'nombre_completo' => 'Pasante Entrenador de Sala',
                        'email' => 'monitor@sena.edu.co',
                        'password_hash' => $monitorHash,
                        'rol' => 'MONITOR',
                        'estado' => 'ACTIVO'
                    ]
                ],
                'aprendices' => [],
                'consentimientos_ley1581' => [],
                'asistencias' => [],
                'novedades_sanciones' => []
            ];

            self::atomicWrite(self::$fallbackFile, json_encode($seedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            self::$fallbackData = $seedData;
        } elseif (empty(self::$fallbackData)) {
            $content = @file_get_contents(self::$fallbackFile);
            self::$fallbackData = $content ? (json_decode($content, true) ?: []) : [];
        }
    }

    public static function getFallbackData(string $table): array
    {
        self::initFallbackStorage();
        return self::$fallbackData[$table] ?? [];
    }

    public static function saveFallbackData(string $table, array $data): void
    {
        self::initFallbackStorage();
        self::$fallbackData[$table] = $data;
        self::atomicWrite(self::$fallbackFile, json_encode(self::$fallbackData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Escritura atómica con bloqueo exclusivo para prevenir condiciones de carrera y corrupción de datos.
     */
    private static function atomicWrite(string $filename, string $data): bool
    {
        $dir = dirname($filename);
        $tempFile = $dir . '/.tmp_' . bin2hex(random_bytes(8)) . '.json';
        
        $fp = @fopen($tempFile, 'w');
        if (!$fp) {
            return false;
        }

        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $data);
            fflush($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
            return @rename($tempFile, $filename);
        }

        fclose($fp);
        @unlink($tempFile);
        return false;
    }
}

