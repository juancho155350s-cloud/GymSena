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
        $dir = dirname(self::$fallbackFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!file_exists(self::$fallbackFile)) {
            // Inicializar con la semilla del sistema
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
                        'nombre_completo' => 'Coordinador Bienestar SENA',
                        'email' => 'admin@sena.edu.co',
                        'password_hash' => '$2y$10$54EKvUi5mybzz7/45W34sei2L/B1p0HD7Khe0UYDDyGHALb1KVwom', // admin123
                        'rol' => 'ADMINISTRADOR',
                        'estado' => 'ACTIVO'
                    ],
                    [
                        'id' => 2,
                        'sede_id' => 1,
                        'gimnasio_id' => 1,
                        'documento' => '1000000002',
                        'nombre_completo' => 'Monitor de Turno Gimnasio',
                        'email' => 'monitor@sena.edu.co',
                        'password_hash' => '$2y$10$pggjfg3hxCSREuRFqVHZAeSF/esnwxAdlobaivpH2edVagO2kW5uq', // monitor123
                        'rol' => 'MONITOR',
                        'estado' => 'ACTIVO'
                    ]
                ],
                'aprendices' => [
                    [
                        'id' => 1,
                        'sede_id' => 1,
                        'tipo_documento' => 'CC',
                        'documento' => '1020304050',
                        'nombres' => 'Carlos Andrés',
                        'apellidos' => 'Gómez Pérez',
                        'ficha' => '2694520',
                        'programa_formacion' => 'Análisis y Desarrollo de Software (ADSO)',
                        'correo' => 'carlos.gomez@soy.sena.edu.co',
                        'telefono' => '3001234567',
                        'contacto_emergencia_nombre' => 'Marta Pérez (Madre)',
                        'contacto_emergencia_telefono' => '3119876543',
                        'estado_formacion' => 'LECTIVA',
                        'activo' => 1,
                        'token_acceso' => 'tok_carlos_2694520',
                        'creado_en' => date('Y-m-d H:i:s')
                    ],
                    [
                        'id' => 2,
                        'sede_id' => 1,
                        'tipo_documento' => 'CC',
                        'documento' => '1030405060',
                        'nombres' => 'Laura Valentina',
                        'apellidos' => 'Restrepo Zapata',
                        'ficha' => '2694520',
                        'programa_formacion' => 'Análisis y Desarrollo de Software (ADSO)',
                        'correo' => 'laura.restrepo@soy.sena.edu.co',
                        'telefono' => '3104567890',
                        'contacto_emergencia_nombre' => 'Jorge Restrepo (Padre)',
                        'contacto_emergencia_telefono' => '3123456789',
                        'estado_formacion' => 'LECTIVA',
                        'activo' => 1,
                        'token_acceso' => 'tok_laura_2694520',
                        'creado_en' => date('Y-m-d H:i:s')
                    ],
                    [
                        'id' => 3,
                        'sede_id' => 1,
                        'tipo_documento' => 'TI',
                        'documento' => '1040506070',
                        'nombres' => 'Mateo',
                        'apellidos' => 'Henao Ramírez',
                        'ficha' => '2712398',
                        'programa_formacion' => 'Gestión Contable y de Información Financiera',
                        'correo' => 'mateo.henao@soy.sena.edu.co',
                        'telefono' => '3157891234',
                        'contacto_emergencia_nombre' => 'Gloria Ramírez (Tía)',
                        'contacto_emergencia_telefono' => '3161239874',
                        'estado_formacion' => 'LECTIVA',
                        'activo' => 1,
                        'token_acceso' => 'tok_mateo_2712398',
                        'creado_en' => date('Y-m-d H:i:s')
                    ]
                ],
                'consentimientos_ley1581' => [
                    ['id' => 1, 'aprendiz_id' => 1, 'acepto_politica' => 1, 'ip_registro' => '127.0.0.1', 'version_politica' => 'v1.0-2026', 'fecha_hora_consentimiento' => date('Y-m-d H:i:s')],
                    ['id' => 2, 'aprendiz_id' => 2, 'acepto_politica' => 1, 'ip_registro' => '127.0.0.1', 'version_politica' => 'v1.0-2026', 'fecha_hora_consentimiento' => date('Y-m-d H:i:s')],
                    ['id' => 3, 'aprendiz_id' => 3, 'acepto_politica' => 1, 'ip_registro' => '127.0.0.1', 'version_politica' => 'v1.0-2026', 'fecha_hora_consentimiento' => date('Y-m-d H:i:s')]
                ],
                'asistencias' => [
                    [
                        'id' => 1,
                        'gimnasio_id' => 1,
                        'aprendiz_id' => 1,
                        'fecha' => date('Y-m-d'),
                        'hora_entrada' => '07:15:00',
                        'hora_salida' => null,
                        'duracion_minutos' => null,
                        'metodo_ingreso' => 'QR_AUTONOMO',
                        'cierre_automatico' => 0,
                        'observaciones' => null
                    ],
                    [
                        'id' => 2,
                        'gimnasio_id' => 1,
                        'aprendiz_id' => 2,
                        'fecha' => date('Y-m-d'),
                        'hora_entrada' => '06:30:00',
                        'hora_salida' => '07:45:00',
                        'duracion_minutos' => 75,
                        'metodo_ingreso' => 'QR_AUTONOMO',
                        'cierre_automatico' => 0,
                        'observaciones' => null
                    ],
                    [
                        'id' => 3,
                        'gimnasio_id' => 1,
                        'aprendiz_id' => 3,
                        'fecha' => date('Y-m-d', strtotime('-1 day')),
                        'hora_entrada' => '08:00:00',
                        'hora_salida' => '09:15:00',
                        'duracion_minutos' => 75,
                        'metodo_ingreso' => 'QR_AUTONOMO',
                        'cierre_automatico' => 0,
                        'observaciones' => null
                    ]
                ],
                'novedades_sanciones' => []
            ];
            file_put_contents(self::$fallbackFile, json_encode($seedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            self::$fallbackData = $seedData;
        } else {
            self::$fallbackData = json_decode(file_get_contents(self::$fallbackFile), true) ?: [];
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
        file_put_contents(self::$fallbackFile, json_encode(self::$fallbackData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

