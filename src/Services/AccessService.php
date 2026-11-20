<?php
namespace GymSena\Services;

use GymSena\Models\Gym;
use GymSena\Models\Learner;
use GymSena\Models\Attendance;
use GymSena\Core\Database;

class AccessService
{
    private static array $dayMap = [
        1 => 'LUNES',
        2 => 'MARTES',
        3 => 'MIERCOLES',
        4 => 'JUEVES',
        5 => 'VIERNES',
        6 => 'SABADO',
        7 => 'DOMINGO'
    ];

    public static function isGymOpen(int $gymId, ?string $simulatedDay = null, ?string $simulatedTime = null): array
    {
        $appConfig = require dirname(__DIR__, 2) . '/config/app.php';
        $dayOfWeekNum = (int)date('N'); // 1 = Lunes, 7 = Domingo
        $currentDayName = $simulatedDay ?? (self::$dayMap[$dayOfWeekNum] ?? 'LUNES');
        $currentTime = $simulatedTime ?? date('H:i:s');

        // Validar si el día está permitido (Lunes a Viernes)
        if (!in_array($currentDayName, $appConfig['days_enabled'])) {
            return [
                'open' => false,
                'reason' => "El gimnasio solo presta servicio de Lunes a Viernes. Hoy ({$currentDayName}) está cerrado."
            ];
        }

        $openTime = $appConfig['schedule_open'];
        $closeTime = $appConfig['schedule_close'];

        if ($currentTime < $openTime || $currentTime > $closeTime) {
            return [
                'open' => false,
                'reason' => "Gimnasio fuera de horario operativo. Horario de atención: {$openTime} a {$closeTime}."
            ];
        }

        $aforo = Gym::getAforo($gymId);
        if ($aforo['estado'] !== 'ABIERTO') {
            return [
                'open' => false,
                'reason' => "El gimnasio se encuentra temporalmente en estado: {$aforo['estado']}."
            ];
        }

        return ['open' => true, 'aforo' => $aforo];
    }

    public static function processAccess(int $gymId, ?string $docOrToken, array $options = []): array
    {
        // 1. Validar horario operativo (salvo que sea bypass explícito de testing/desarrollo)
        if (empty($options['bypass_schedule'])) {
            $simDay = $options['simulated_day'] ?? null;
            $simTime = $options['simulated_time'] ?? null;
            $openCheck = self::isGymOpen($gymId, $simDay, $simTime);
            if (!$openCheck['open']) {
                return [
                    'success' => false,
                    'type' => 'SCHEDULE_BLOCKED',
                    'message' => $openCheck['reason']
                ];
            }
        }

        if (empty($docOrToken)) {
            return [
                'success' => false,
                'type' => 'INPUT_REQUIRED',
                'message' => 'Por favor ingresa tu número de documento.'
            ];
        }

        // 2. Buscar aprendiz por token de dispositivo o por número de documento
        $learner = null;
        if (str_starts_with($docOrToken, 'tok_')) {
            $learner = Learner::findByToken($docOrToken);
        }
        if (!$learner) {
            $learner = Learner::findByDocument($docOrToken);
        }

        // 3. Si no existe, requiere formulario de inscripción
        if (!$learner) {
            return [
                'success' => true,
                'type' => 'NEEDS_REGISTRATION',
                'documento_sugerido' => is_numeric($docOrToken) ? $docOrToken : '',
                'message' => 'No estás registrado en GymSENA. Diligencia tu inscripción por única vez para ingresar.'
            ];
        }

        // 4. Validar sanciones activas
        $sanction = Learner::hasActiveSanction($learner['id']);
        if ($sanction) {
            return [
                'success' => false,
                'type' => 'SANCTIONED',
                'learner' => $learner,
                'message' => "Acceso restringido por novedad registrada: {$sanction['descripcion']}. Dirígete a Bienestar al Aprendiz."
            ];
        }

        $hoy = date('Y-m-d');
        $activeVisit = Attendance::getActiveVisit($gymId, $learner['id'], $hoy);

        // 5. Flujo de SALIDA (Check-out) si ya tenía entrada abierta
        if ($activeVisit) {
            $checkoutResult = Attendance::checkOut($activeVisit['id']);
            $aforoActualizado = Gym::getAforo($gymId);

            return [
                'success' => true,
                'type' => 'CHECKOUT',
                'action' => 'SALIDA',
                'learner' => $learner,
                'attendance' => $checkoutResult,
                'aforo' => $aforoActualizado,
                'message' => "¡Buen entrenamiento, {$learner['nombres']}! Has registrado tu salida exitosamente."
            ];
        }

        // 6. Flujo de ENTRADA (Check-in)
        $aforo = Gym::getAforo($gymId);
        if ($aforo['cupos_disponibles'] <= 0) {
            return [
                'success' => false,
                'type' => 'GYM_FULL',
                'learner' => $learner,
                'aforo' => $aforo,
                'message' => "El gimnasio ha alcanzado su capacidad máxima permitida ({$aforo['capacidad_maxima']}/{$aforo['capacidad_maxima']}). Espera a que un compañero registre su salida."
            ];
        }

        $method = $options['method'] ?? 'QR_AUTONOMO';
        $newVisit = Attendance::checkIn($gymId, $learner['id'], $method);
        $aforoActualizado = Gym::getAforo($gymId);

        return [
            'success' => true,
            'type' => 'CHECKIN',
            'action' => 'ENTRADA',
            'learner' => $learner,
            'attendance' => $newVisit,
            'aforo' => $aforoActualizado,
            'message' => "¡Bienvenido a entrenar, {$learner['nombres']}! Entrada registrada con éxito."
        ];
    }

    public static function registerAndCheckIn(int $gymId, array $data, string $ip, string $userAgent, array $options = []): array
    {
        // 1. Validar horario operativo
        if (empty($options['bypass_schedule'])) {
            $openCheck = self::isGymOpen($gymId);
            if (!$openCheck['open']) {
                return [
                    'success' => false,
                    'type' => 'SCHEDULE_BLOCKED',
                    'message' => $openCheck['reason']
                ];
            }
        }

        // 2. Validar consentimiento Ley 1581
        if (empty($data['acepto_politica'])) {
            return [
                'success' => false,
                'message' => 'Es obligatorio autorizar el tratamiento de datos personales para usar el gimnasio del SENA.'
            ];
        }

        // 3. Verificar que no exista ya el documento
        $existente = Learner::findByDocument($data['documento']);
        if ($existente) {
            // Si ya existe, procesar directamente el acceso con el existente
            return self::processAccess($gymId, $existente['documento'], $options);
        }

        // 3. Crear aprendiz
        $learner = Learner::create($data);

        // 4. Guardar consentimiento legal de datos
        $appConfig = require dirname(__DIR__, 2) . '/config/app.php';
        Learner::recordConsent($learner['id'], $ip, $userAgent, $appConfig['data_protection_version']);

        // 5. Marcar primera entrada de inmediato
        $checkInVisit = Attendance::checkIn($gymId, $learner['id'], 'QR_AUTONOMO', 'Primera visita con auto-inscripción');
        $aforo = Gym::getAforo($gymId);

        return [
            'success' => true,
            'type' => 'REGISTERED_AND_CHECKIN',
            'action' => 'ENTRADA',
            'learner' => $learner,
            'attendance' => $checkInVisit,
            'aforo' => $aforo,
            'token' => $learner['token_acceso'],
            'message' => "¡Registro completado! Bienvenido al Gimnasio SENA, {$learner['nombres']}."
        ];
    }
}
