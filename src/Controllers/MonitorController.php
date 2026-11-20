<?php
namespace GymSena\Controllers;

use GymSena\Core\Request;
use GymSena\Core\Response;
use GymSena\Core\Auth;
use GymSena\Models\Gym;
use GymSena\Models\Attendance;
use GymSena\Models\Learner;
use GymSena\Services\AccessService;

class MonitorController
{
    public function dashboard(Request $request): void
    {
        Auth::requireRole('MONITOR');

        $user = Auth::getUser();
        $gymId = (int)($user['gimnasio_id'] ?: 1);

        $aforo = Gym::getAforo($gymId);
        $inside = Attendance::getCurrentlyInside($gymId);

        Response::view('monitor/dashboard', [
            'title' => 'Panel del Entrenador - GymSENA',
            'user' => $user,
            'gymId' => $gymId,
            'aforo' => $aforo,
            'inside' => $inside
        ], 'dashboard');
    }

    public function manualAccess(Request $request): void
    {
        Auth::requireRole('MONITOR');

        $gymId = (int)($request->post('gym_id') ?: 1);
        $document = trim($request->post('documento') ?: '');
        $observaciones = trim($request->post('observaciones') ?: 'Registro asistido por monitor');

        if (empty($document)) {
            Response::redirect('/monitor?error=documento_vacio');
        }

        $result = AccessService::processAccess($gymId, $document, [
            'method' => 'MANUAL_MONITOR'
        ]);

        if ($request->isAjax()) {
            Response::json($result);
        }

        $statusMsg = $result['success'] ? 'success_op' : 'error_op';
        Response::redirect("/monitor?status={$statusMsg}&msg=" . urlencode($result['message']));
    }

    public function forceCheckout(Request $request, array $params): void
    {
        Auth::requireRole('MONITOR');
        $attendanceId = (int)($params['id'] ?? 0);

        if ($attendanceId > 0) {
            Attendance::checkOut($attendanceId, 'Salida forzada por monitor de sala');
        }

        Response::redirect('/monitor?msg=' . urlencode('Salida marcada manualmente por el monitor'));
    }

    public function liveCheck(Request $request): void
    {
        Auth::requireRole('MONITOR');
        $user = Auth::getUser();
        $gymId = (int)($user['gimnasio_id'] ?: 1);

        $aforo = Gym::getAforo($gymId);
        $inside = Attendance::getCurrentlyInside($gymId);
        $lastId = (int)($request->get('last_id') ?: 0);

        $newEntry = null;
        if (!empty($inside)) {
            $latest = $inside[0];
            $latestId = (int)($latest['id'] ?? 0);
            if ($lastId > 0 && $latestId > $lastId) {
                $newEntry = [
                    'id' => $latestId,
                    'nombre' => $latest['nombres'] . ' ' . $latest['apellidos'],
                    'hora' => substr($latest['hora_entrada'], 0, 5)
                ];
            }
        }

        Response::json([
            'aforo' => $aforo,
            'inside_count' => count($inside),
            'latest_id' => !empty($inside[0]['id']) ? (int)$inside[0]['id'] : 0,
            'new_entry' => $newEntry,
            'inside' => $inside
        ]);
    }
}

