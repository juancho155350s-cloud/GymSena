<?php
namespace GymSena\Controllers;

use GymSena\Core\Request;
use GymSena\Core\Response;
use GymSena\Core\Auth;
use GymSena\Models\Learner;
use GymSena\Models\Attendance;

class LearnerController
{
    public function history(Request $request): void
    {
        $learner = Auth::getLearner();
        $doc = $request->get('doc');

        if (!$learner && $doc) {
            $found = Learner::findByDocument($doc);
            if ($found) {
                Auth::loginLearner($found);
                $learner = Auth::getLearner();
            }
        }

        if (!$learner) {
            Response::redirect('/acceso?msg=debes_identificarte');
        }

        $history = Attendance::getHistoryByLearner($learner['id'], 50);

        // Métricas avanzadas para la vista ejecutiva
        $totalSesiones = count($history);
        $minutosTotales = 0;
        $visitasConSalida = 0;
        foreach ($history as $h) {
            if (!empty($h['duracion_minutos'])) {
                $minutosTotales += (int)$h['duracion_minutos'];
                $visitasConSalida++;
            }
        }
        $horasTotales = round($minutosTotales / 60, 1);
        $promedioMinutos = $visitasConSalida > 0 ? round($minutosTotales / $visitasConSalida) : 0;
        
        $nivelDisciplina = match(true) {
            $totalSesiones >= 12 => 'Atleta Constante',
            $totalSesiones >= 5 => 'Frecuente',
            $totalSesiones >= 1 => 'En Progreso',
            default => 'Nuevo Ingreso'
        };

        Response::view('learner/history', [
            'title' => 'Mi Historial de Asistencias - GymSENA',
            'learner' => $learner,
            'history' => $history,
            'stats' => [
                'total_sesiones' => $totalSesiones,
                'horas_totales' => $horasTotales,
                'minutos_totales' => $minutosTotales,
                'promedio_minutos' => $promedioMinutos,
                'nivel_disciplina' => $nivelDisciplina
            ]
        ], 'main');
    }

    public function digitalCard(Request $request): void
    {
        $learner = Auth::getLearner();
        if (!$learner) {
            Response::redirect('/acceso?msg=debes_identificarte');
        }

        $fullLearner = Learner::findById($learner['id']) ?? $learner;

        // Generar o asegurar QR SVG institucional offline sin CDNs externas
        $doc = preg_replace('/[^0-9A-Za-z]/', '', $fullLearner['documento']);
        $qrFilename = 'qr_learner_' . $doc . '.svg';
        $qrAbsolutePath = dirname(__DIR__, 2) . '/public/images/' . $qrFilename;

        if (!file_exists($qrAbsolutePath)) {
            $qrData = 'GYMSENA:' . $fullLearner['documento'];
            $pyScript = sprintf(
                'import qrcode, qrcode.image.svg; factory = qrcode.image.svg.SvgPathImage; img = qrcode.make(%s, image_factory=factory, box_size=10); img.save(%s)',
                escapeshellarg($qrData),
                escapeshellarg($qrAbsolutePath)
            );
            @shell_exec('python3 -c ' . escapeshellarg($pyScript));
        }

        Response::view('learner/card', [
            'title' => 'Mi Carné Digital - GymSENA',
            'learner' => $fullLearner,
            'qrUrl' => '/images/' . $qrFilename
        ], 'main');
    }

    public function logoutLearner(Request $request): void
    {
        setcookie('gymsena_token', '', time() - 3600, '/');
        Auth::logout();
        Response::redirect('/acceso?msg=sesion_cerrada');
    }
}

