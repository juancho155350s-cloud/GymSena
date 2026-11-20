<?php
/**
 * GymSENA - Tarea Programada de Cierre Automático Nocturno
 * Ejecutar de Lunes a Viernes a las 23:59:00 vía cron
 * Comando ejemplo en crontab:
 * 59 23 * * 1-5 /usr/bin/php /ruta/a/GymSena/cron/daily_close.php
 */

require_once dirname(__DIR__) . '/src/Core/Database.php';
require_once dirname(__DIR__) . '/src/Models/Attendance.php';

use GymSena\Models\Attendance;

date_default_timezone_set('America/Bogota');
$hoy = date('Y-m-d');
$horaCierreOficial = '20:00:00';

echo "[" . date('Y-m-d H:i:s') . "] Iniciando tarea de cierre automático de asistencias...\n";

try {
    $cerradas = Attendance::closeAllOpenVisitsForDate($hoy, $horaCierreOficial);
    echo "[" . date('Y-m-d H:i:s') . "] Proceso completado exitosamente. Visitas auto-cerradas: {$cerradas}.\n";
} catch (\Throwable $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR al ejecutar el cierre automático: " . $e->getMessage() . "\n";
    exit(1);
}

