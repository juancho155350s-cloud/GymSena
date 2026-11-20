<?php
namespace GymSena\Controllers;

use GymSena\Core\Request;
use GymSena\Core\Response;
use GymSena\Core\Auth;
use GymSena\Models\Gym;
use GymSena\Models\Learner;
use GymSena\Models\Attendance;
use GymSena\Services\AccessService;

class AccessController
{
    public function scan(Request $request): void
    {
        $appConfig = require dirname(__DIR__, 2) . '/config/app.php';
        $gymId = (int)($request->get('gym') ?: $appConfig['default_gym_id']);
        
        $aforo = Gym::getAforo($gymId);
        $isOpenCheck = AccessService::isGymOpen($gymId);

        // Verificar si el usuario ya tiene sesión activa o token persistente
        $learner = Auth::getLearner();
        $savedToken = $request->get('token') ?: ($_COOKIE['gymsena_token'] ?? null);

        if (!$learner && $savedToken) {
            $found = Learner::findByToken($savedToken);
            if ($found) {
                Auth::loginLearner($found);
                $learner = Auth::getLearner();
            }
        }

        // Determinar si el usuario recordado tiene una visita activa en el gimnasio
        $activeVisit = null;
        if ($learner && !empty($learner['id'])) {
            $activeVisit = Attendance::getActiveVisit($gymId, (int)$learner['id'], date('Y-m-d'));
        }

        $suggestedDoc = trim($request->get('doc') ?: '');
        $showRegistration = !empty($request->get('registro'));
        $isNewUserDetected = !empty($request->get('nuevo'));

        Response::view('access/scan', [
            'title' => 'Acceso al Gimnasio - GymSENA',
            'gymId' => $gymId,
            'aforo' => $aforo,
            'isOpen' => $isOpenCheck['open'],
            'closedReason' => $isOpenCheck['reason'] ?? '',
            'learner' => $learner,
            'activeVisit' => $activeVisit,
            'suggestedDoc' => $suggestedDoc,
            'showRegistration' => $showRegistration,
            'isNewUserDetected' => $isNewUserDetected,
            'dataProtectionText' => $appConfig['data_protection_text'],
            'pinEnabled' => $appConfig['security_pin_enabled']
        ], 'main');
    }

    public function checkUser(Request $request): void
    {
        $doc = trim($request->get('doc') ?: '');
        if (empty($doc)) {
            Response::json(['exists' => false, 'message' => 'Documento requerido'], 400);
            return;
        }

        $learner = Learner::findByDocument($doc);
        if (!$learner) {
            Response::json([
                'exists' => false,
                'is_new' => true,
                'doc' => $doc,
                'message' => 'Usuario nuevo detectado. Requiere inscripción.'
            ]);
            return;
        }

        $gymId = (int)($request->get('gym') ?: 1);
        $activeVisit = Attendance::getActiveVisit($gymId, (int)$learner['id'], date('Y-m-d'));

        Response::json([
            'exists' => true,
            'is_new' => false,
            'id' => $learner['id'],
            'documento' => $learner['documento'],
            'nombres' => $learner['nombres'],
            'apellidos' => $learner['apellidos'],
            'tipo_usuario' => $learner['tipo_usuario'] ?? 'APRENDIZ',
            'programa' => $learner['programa_formacion'] ?? '',
            'ficha' => $learner['ficha'] ?? '',
            'is_inside' => !empty($activeVisit),
            'visit_id' => $activeVisit['id'] ?? null,
            'hora_entrada' => !empty($activeVisit['hora_entrada']) ? substr($activeVisit['hora_entrada'], 0, 5) : null
        ]);
    }

    public function process(Request $request): void
    {
        $gymId = (int)($request->post('gym_id') ?: 1);
        $docOrToken = trim($request->post('documento_o_token') ?: '');

        // Si no vino en el POST pero el aprendiz tiene sesión
        if (empty($docOrToken)) {
            $learner = Auth::getLearner();
            if ($learner) {
                $docOrToken = $learner['documento'];
            }
        }

        $result = AccessService::processAccess($gymId, $docOrToken, [
            'method' => 'QR_AUTONOMO'
        ]);

        // Si el usuario no existe, redirigir automáticamente al registro con el documento pre-diligenciado
        if (!empty($result['type']) && $result['type'] === 'NEEDS_REGISTRATION') {
            if ($request->isAjax()) {
                Response::json([
                    'success' => false,
                    'is_new' => true,
                    'type' => 'NEEDS_REGISTRATION',
                    'doc' => $result['documento_sugerido'] ?? $docOrToken,
                    'message' => 'Usuario nuevo detectado. Por favor completa tu inscripción.'
                ]);
                return;
            }
            $targetDoc = urlencode($result['documento_sugerido'] ?? $docOrToken);
            Response::redirect("/acceso?gym={$gymId}&registro=1&doc={$targetDoc}&nuevo=1");
            return;
        }

        if (!empty($result['learner'])) {
            Auth::loginLearner($result['learner']);
            if (!empty($result['learner']['token_acceso'])) {
                setcookie('gymsena_token', $result['learner']['token_acceso'], time() + (86400 * 365), '/');
            }
        }

        if ($request->isAjax()) {
            Response::json($result);
            return;
        }

        // Renderizar vista de resultado
        Response::view('access/result', [
            'title' => 'Resultado de Acceso - GymSENA',
            'result' => $result,
            'gymId' => $gymId
        ], 'main');
    }

    public function register(Request $request): void
    {
        $gymId = (int)($request->post('gym_id') ?: 1);
        $tipoUsuario = strtoupper(trim($request->post('tipo_usuario') ?: 'APRENDIZ'));
        if (!in_array($tipoUsuario, ['APRENDIZ', 'INSTRUCTOR', 'FUNCIONARIO', 'INVITADO'])) {
            $tipoUsuario = 'APRENDIZ';
        }

        $doc = trim($request->post('documento') ?: '');
        $nombres = trim($request->post('nombres') ?: '');
        $apellidos = trim($request->post('apellidos') ?: '');
        
        $ficha = trim($request->post('ficha') ?: '');
        if (empty($ficha)) {
            $ficha = match($tipoUsuario) {
                'INSTRUCTOR' => 'INSTRUCTOR',
                'FUNCIONARIO' => 'FUNCIONARIO',
                'INVITADO' => 'INVITADO',
                default => 'SENA'
            };
        }

        $programa = trim($request->post('programa_formacion') ?: '');
        if (empty($programa)) {
            $programa = trim($request->post('area_o_dependencia') ?: ($tipoUsuario . ' SENA'));
        }

        $correo = trim($request->post('correo') ?: '');
        if (empty($correo)) {
            $correo = $doc . '@sena.user';
        }

        $telefono = trim($request->post('telefono') ?: 'No registra');
        $emergenciaNombre = trim($request->post('contacto_emergencia_nombre') ?: 'Contacto Institucional');
        $emergenciaTel = trim($request->post('contacto_emergencia_telefono') ?: $telefono);

        $formData = [
            'sede_id' => 1,
            'tipo_documento' => $request->post('tipo_documento') ?: 'CC',
            'documento' => $doc,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'tipo_usuario' => $tipoUsuario,
            'ficha' => $ficha,
            'programa_formacion' => $programa,
            'correo' => $correo,
            'telefono' => $telefono,
            'contacto_emergencia_nombre' => $emergenciaNombre,
            'contacto_emergencia_telefono' => $emergenciaTel,
            'acepto_politica' => !empty($request->post('acepto_politica')) ? 1 : 0
        ];

        // Validaciones básicas de integridad
        if (empty($formData['documento']) || empty($formData['nombres'])) {
            if ($request->isAjax()) {
                Response::json(['success' => false, 'message' => 'Por favor completa documento y nombres.'], 400);
            }
            Response::redirect("/acceso?gym={$gymId}&error=incomplete_fields");
        }

        $ip = $request->getIp();
        $ua = $request->getUserAgent();

        $result = AccessService::registerAndCheckIn($gymId, $formData, $ip, $ua);

        if (!empty($result['learner'])) {
            Auth::loginLearner($result['learner']);
            if (!empty($result['token'])) {
                setcookie('gymsena_token', $result['token'], time() + (86400 * 365), '/');
            }
        }

        if ($request->isAjax()) {
            Response::json($result);
        }

        Response::view('access/result', [
            'title' => 'Inscripción y Acceso - GymSENA',
            'result' => $result,
            'gymId' => $gymId
        ], 'main');
    }

    public function poster(Request $request): void
    {
        $appConfig = require dirname(__DIR__, 2) . '/config/app.php';
        $gymId = (int)($request->get('gym') ?: $appConfig['default_gym_id']);
        $gym = Gym::getAforo($gymId);
        
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $accessUrl = "{$proto}://{$host}/acceso?gym={$gymId}&qr=entrada";

        // Cargar vista dedicada de cartel oficial
        require dirname(__DIR__) . '/Views/access/poster.php';
        exit;
    }

    public function aforoJson(Request $request): void
    {
        $gymId = (int)($request->get('gym') ?: 1);
        $aforo = Gym::getAforo($gymId);
        Response::json($aforo);
    }
}

