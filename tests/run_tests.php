<?php
/**
 * GymSENA - Suite de Pruebas Automatizadas del MVP
 * Ejecuta validaciones de lógica de negocio, aforo, inscripción y check-in/check-out.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Core/Database.php';
require_once dirname(__DIR__) . '/src/Models/Gym.php';
require_once dirname(__DIR__) . '/src/Models/Learner.php';
require_once dirname(__DIR__) . '/src/Models/Attendance.php';
require_once dirname(__DIR__) . '/src/Models/User.php';
require_once dirname(__DIR__) . '/src/Services/AccessService.php';

use GymSena\Models\Gym;
use GymSena\Models\Learner;
use GymSena\Models\Attendance;
use GymSena\Models\User;
use GymSena\Services\AccessService;

date_default_timezone_set('America/Bogota');

echo "========================================================\n";
echo "🧪 INICIANDO SUITE DE PRUEBAS DE SOFTWARE - GymSENA\n";
echo "========================================================\n\n";

$testsPassed = 0;
$totalTests = 0;

function assertTest(string $description, bool $condition): void {
    global $testsPassed, $totalTests;
    $totalTests++;
    if ($condition) {
        $testsPassed++;
        echo "  [PASS] {$description}\n";
    } else {
        echo "  [FAIL] ❌ {$description}\n";
    }
}

// 1. Prueba de Aforo Inicial
echo "--- 1. Pruebas de Aforo y Gimnasio ---\n";
$aforo = Gym::getAforo(1);
assertTest("El aforo inicial tiene capacidad configurada", $aforo['capacidad_maxima'] > 0);
assertTest("El aforo reporta cupos disponibles calculados", $aforo['cupos_disponibles'] >= 0);

// 2. Prueba de Validación Horaria (Sábado bloqueado)
echo "\n--- 2. Pruebas de Horario y Restricción de Fin de Semana ---\n";
$bloqueoSabado = AccessService::processAccess(1, '1020304050', ['simulated_day' => 'SABADO']);
assertTest("Bloqueo de acceso en fin de semana (Sábado) funciona correctamente", $bloqueoSabado['success'] === false && $bloqueoSabado['type'] === 'SCHEDULE_BLOCKED');

// 3. Prueba de Inscripción de Nuevo Aprendiz con Ley 1581 (con bypass para test)
echo "\n--- 3. Pruebas de Inscripción y Ley 1581 ---\n";
$docPrueba = '999' . time() . rand(10, 99);
$nuevoAprendizData = [
    'sede_id' => 1,
    'tipo_documento' => 'CC',
    'documento' => $docPrueba,
    'nombres' => 'Juan Diego',
    'apellidos' => 'Morales',
    'ficha' => '2800123',
    'programa_formacion' => 'ADSO',
    'correo' => 'juan.morales@soy.sena.edu.co',
    'telefono' => '3123456789',
    'contacto_emergencia_nombre' => 'Ana Morales',
    'contacto_emergencia_telefono' => '3109876543',
    'acepto_politica' => 1
];

$registroResult = AccessService::registerAndCheckIn(1, $nuevoAprendizData, '127.0.0.1', 'PHPUnit/TestRunner', ['bypass_schedule' => true]);
assertTest("Inscripción exitosa genera Check-in inmediato", $registroResult['success'] === true && $registroResult['action'] === 'ENTRADA');
assertTest("Se generó token único persistente para el dispositivo", !empty($registroResult['token']));

// 3.1 Prueba de Auto-Detección de Usuario No Registrado
$docNoExiste = '000' . time();
$checkNoExiste = AccessService::processAccess(1, $docNoExiste, ['bypass_schedule' => true]);
assertTest("Auto-detección identifica correctamente usuario nuevo para registro", $checkNoExiste['type'] === 'NEEDS_REGISTRATION');

// 4. Prueba de Check-Out Automático al Volver a Escanear
echo "\n--- 4. Pruebas de Flujo QR (Check-in -> Check-out) ---\n";
$segundoEscaneo = AccessService::processAccess(1, $docPrueba, ['bypass_schedule' => true]);
assertTest("Segundo escaneo del mismo aprendiz marca SALIDA (Check-out)", $segundoEscaneo['success'] === true && $segundoEscaneo['action'] === 'SALIDA');
assertTest("Se calculó tiempo de entrenamiento en minutos", isset($segundoEscaneo['attendance']['duracion_minutos']));

// 5. Prueba de Autenticación de Usuarios del Sistema (Admin y Monitor)
echo "\n--- 5. Pruebas de Seguridad y Roles ---\n";
$admin = User::findByEmail('admin@sena.edu.co');
assertTest("Existe usuario administrador semilla", $admin !== null && $admin['rol'] === 'ADMINISTRADOR');
assertTest("Contraseña de administrador encriptada valida correctamente", User::verifyPassword($admin, 'admin123'));
assertTest("Contraseña incorrecta es rechazada", !User::verifyPassword($admin, 'clave_erronea'));

$monitor = User::findByEmail('monitor@sena.edu.co');
assertTest("Existe usuario monitor de turno", $monitor !== null && $monitor['rol'] === 'MONITOR');
assertTest("Contraseña de monitor valida correctamente", User::verifyPassword($monitor, 'monitor123'));

// 5. Prueba de Tarea Nocturna de Cierre Automático
echo "\n--- 5. Pruebas de Cron y Resiliencia ---\n";
$cerradas = Attendance::closeAllOpenVisitsForDate(date('Y-m-d'));
assertTest("Cierre automático procesa registros abiertos sin error", $cerradas >= 0);

// 6. Prueba de Acceso Multi-Rol (Instructores, Trabajadores, Invitados)
echo "\n--- 6. Pruebas de Usuarios Inclusivos (Sin Cuellos de Botella) ---\n";
$docInstructor = '987654321';
$regInstructor = AccessService::registerAndCheckIn(1, [
    'sede_id' => 1,
    'tipo_documento' => 'CC',
    'documento' => $docInstructor,
    'nombres' => 'Profesor Roberto',
    'apellidos' => 'Giraldo',
    'tipo_usuario' => 'INSTRUCTOR',
    'ficha' => 'INSTRUCTOR',
    'programa_formacion' => 'Red de Teleinformática',
    'correo' => 'rgiraldo@sena.edu.co',
    'telefono' => '3157778899',
    'contacto_emergencia_nombre' => 'Contacto Sena',
    'contacto_emergencia_telefono' => '3157778899',
    'acepto_politica' => 1
], '127.0.0.1', 'PHP-Testing-Harness', ['bypass_schedule' => true]);

assertTest("Instructor se registra e ingresa sin requerir ficha de aprendiz", $regInstructor['success'] === true);
assertTest("Instructor queda registrado con tipo_usuario INSTRUCTOR", ($regInstructor['learner']['tipo_usuario'] ?? '') === 'INSTRUCTOR');

$docInvitado = '888999111';
$regInvitado = AccessService::registerAndCheckIn(1, [
    'sede_id' => 1,
    'tipo_documento' => 'CC',
    'documento' => $docInvitado,
    'nombres' => 'María Camila',
    'apellidos' => 'Suárez',
    'tipo_usuario' => 'INVITADO',
    'ficha' => 'INVITADO',
    'programa_formacion' => 'Universidad de Antioquia (Convenio)',
    'correo' => 'maria.suarez@udea.edu.co',
    'telefono' => '3009990011',
    'contacto_emergencia_nombre' => 'Familia Suárez',
    'contacto_emergencia_telefono' => '3009990011',
    'acepto_politica' => 1
], '127.0.0.1', 'PHP-Testing-Harness', ['bypass_schedule' => true]);

assertTest("Invitado externo se registra e ingresa con trazabilidad", $regInvitado['success'] === true);

// 7. Prueba del Cartel Oficial de Entrada
echo "\n--- 7. Pruebas del Cartel QR Único de Entrada ---\n";
assertTest("Existe archivo vectorial SVG del QR de entrada", file_exists(__DIR__ . '/../public/images/qr-entrada-oficial.svg'));
assertTest("Existe plantilla del cartel oficial de entrada", file_exists(__DIR__ . '/../src/Views/access/poster.php'));

// 8. Pruebas de Jerarquía y Gestión de Pasantes (Instructor Encargado)
echo "\n--- 8. Pruebas de Jerarquía y Pasantes (Instructor -> Entrenadores) ---\n";
$emailPasante = 'pasante.test.' . time() . '@misena.edu.co';
$nuevoPasante = User::create([
    'documento' => '1099887766',
    'nombre_completo' => 'Carlos Pasante Sena',
    'email' => $emailPasante,
    'password' => 'Pasante2026*',
    'rol' => 'MONITOR',
    'sede_id' => 1,
    'estado' => 'ACTIVO'
]);
assertTest("Instructor crea cuenta para pasante / entrenador de sala", !empty($nuevoPasante['id']));

$nuevoPasanteId = (int)$nuevoPasante['id'];
$pasanteCreado = User::findById($nuevoPasanteId);
assertTest("Pasante creado tiene rol MONITOR asignado correctamente", ($pasanteCreado['rol'] ?? '') === 'MONITOR');

$toggled = User::toggleStatus($nuevoPasanteId);
assertTest("Instructor puede pausar/activar cuenta de pasante", $toggled === true);

$pasantePausado = User::findById($nuevoPasanteId);
assertTest("Estado del pasante cambió a INACTIVO", ($pasantePausado['estado'] ?? '') === 'INACTIVO');

$eliminado = User::delete($nuevoPasanteId);
assertTest("Instructor puede dar de baja pasante al culminar etapa", $eliminado === true);

echo "\n========================================================\n";
echo "📊 RESULTADO: {$testsPassed} de {$totalTests} pruebas superadas con éxito (" . round(($testsPassed/$totalTests)*100) . "%)\n";
echo "========================================================\n";
