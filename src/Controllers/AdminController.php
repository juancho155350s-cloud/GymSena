<?php
namespace GymSena\Controllers;

use GymSena\Core\Request;
use GymSena\Core\Response;
use GymSena\Core\Auth;
use GymSena\Models\Gym;
use GymSena\Models\Attendance;
use GymSena\Models\Learner;
use GymSena\Models\User;

class AdminController
{
    public function dashboard(Request $request): void
    {
        Auth::requireRole('ADMINISTRADOR');

        $user = Auth::getUser();
        $gymId = (int)($request->get('gym') ?: 1);

        $startDate = $request->get('inicio') ?: date('Y-m-01');
        $endDate = $request->get('fin') ?: date('Y-m-d');

        $aforo = Gym::getAforo($gymId);
        $summary = Attendance::getStatsSummary($gymId, $startDate, $endDate);
        $peakHours = Attendance::getPeakHours($gymId, $startDate, $endDate);
        $topPrograms = Attendance::getTopPrograms($gymId, $startDate, $endDate, 5);
        $recentLearners = Learner::getAll(15);
        $trainers = User::getAllStaff();

        Response::view('admin/dashboard', [
            'title' => 'Portal del Instructor Encargado - GymSENA',
            'user' => $user,
            'gymId' => $gymId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'aforo' => $aforo,
            'summary' => $summary,
            'peakHours' => $peakHours,
            'topPrograms' => $topPrograms,
            'recentLearners' => $recentLearners,
            'trainers' => $trainers
        ], 'dashboard');
    }

    public function updateSettings(Request $request): void
    {
        Auth::requireRole('ADMINISTRADOR');

        if (!Auth::validateCsrfToken($request->post('csrf_token'))) {
            Response::redirect('/admin?error=' . urlencode('Sesión expirada o solicitud no válida'));
            return;
        }

        $gymId = (int)($request->post('gym_id') ?: 1);
        $capacity = (int)($request->post('capacidad_maxima') ?: 30);

        if ($capacity > 0) {
            Gym::updateCapacity($gymId, $capacity);
        }

        Response::redirect('/admin?msg=' . urlencode('Configuración actualizada correctamente'));
    }

    public function exportCsv(Request $request): void
    {
        Auth::requireRole('ADMINISTRADOR');

        $gymId = (int)($request->get('gym') ?: 1);
        $startDate = $request->get('inicio') ?: date('Y-m-01');
        $endDate = $request->get('fin') ?: date('Y-m-d');

        $rows = Attendance::getAllForExport($gymId, $startDate, $endDate);

        $filename = "GymSENA_Reporte_Asistencias_{$startDate}_al_{$endDate}.csv";

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $output = fopen('php://output', 'w');
        // BOM UTF-8 para apertura correcta en Microsoft Excel
        fputs($output, "\xEF\xBB\xBF");

        // Cabeceras
        fputcsv($output, [
            'FECHA',
            'DOCUMENTO',
            'APRENDIZ',
            'FICHA',
            'PROGRAMA DE FORMACION',
            'HORA ENTRADA',
            'HORA SALIDA',
            'DURACION (MIN)',
            'METODO INGRESO',
            'CIERRE AUTOMATICO'
        ]);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['fecha'],
                $row['documento'],
                $row['aprendiz'],
                $row['ficha'],
                $row['programa'],
                $row['hora_entrada'],
                $row['hora_salida'],
                $row['duracion_minutos'],
                $row['metodo'],
                $row['cierre_automatico']
            ]);
        }

        fclose($output);
        exit;
    }

    public function bulkUpload(Request $request): void
    {
        Auth::requireRole('ADMINISTRADOR');

        if (!Auth::validateCsrfToken($request->post('csrf_token'))) {
            Response::redirect('/admin?error=' . urlencode('Sesión expirada o solicitud no válida'));
            return;
        }

        if (empty($_FILES['archivo_excel']['tmp_name'])) {
            Response::redirect('/admin?error=' . urlencode('No se subió ningún archivo'));
        }

        $tmpFile = $_FILES['archivo_excel']['tmp_name'];
        $handle = fopen($tmpFile, 'r');
        if (!$handle) {
            Response::redirect('/admin?error=' . urlencode('Error al leer el archivo CSV'));
        }

        $headers = fgetcsv($handle);
        $importados = 0;

        while (($row = fgetcsv($handle)) !== false) {
            // Formato esperado: Documento, Nombres, Apellidos, Ficha, Programa, Correo, Telefono
            if (count($row) >= 5) {
                $doc = trim($row[0]);
                if (!empty($doc) && !Learner::findByDocument($doc)) {
                    Learner::create([
                        'sede_id' => 1,
                        'tipo_documento' => 'CC',
                        'documento' => $doc,
                        'nombres' => trim($row[1] ?? 'Aprendiz'),
                        'apellidos' => trim($row[2] ?? 'SENA'),
                        'ficha' => trim($row[3] ?? '000000'),
                        'programa_formacion' => trim($row[4] ?? 'Formación SENA'),
                        'correo' => trim($row[5] ?? "{$doc}@soy.sena.edu.co"),
                        'telefono' => trim($row[6] ?? '3000000000'),
                        'contacto_emergencia_nombre' => 'Contacto Institucional',
                        'contacto_emergencia_telefono' => '3000000000'
                    ]);
                    $importados++;
                }
            }
        }
        fclose($handle);

        Response::redirect("/admin?msg=" . urlencode("Se importaron {$importados} aprendices exitosamente"));
    }

    public function createTrainer(Request $request): void
    {
        Auth::requireRole('ADMINISTRADOR');

        if (!Auth::validateCsrfToken($request->post('csrf_token'))) {
            Response::redirect('/admin?error=' . urlencode('Sesión expirada o solicitud no válida'));
            return;
        }

        $doc = trim($request->post('documento') ?: '');
        $nombre = trim($request->post('nombre_completo') ?: '');
        $email = strtolower(trim($request->post('email') ?: ''));
        $password = trim($request->post('password') ?: '');

        if (empty($doc) || empty($nombre) || empty($email) || empty($password)) {
            Response::redirect('/admin?error=' . urlencode('Todos los campos son obligatorios para crear el pasante'));
            return;
        }

        // Verificar si ya existe usuario con ese correo
        if (User::findByEmail($email)) {
            Response::redirect('/admin?error=' . urlencode('Ya existe un usuario registrado con este correo electrónico'));
            return;
        }

        User::create([
            'sede_id' => 1,
            'gimnasio_id' => 1,
            'documento' => $doc,
            'nombre_completo' => $nombre,
            'email' => $email,
            'password' => $password,
            'rol' => 'MONITOR',
            'estado' => 'ACTIVO'
        ]);

        Response::redirect('/admin?msg=' . urlencode("Pasante {$nombre} registrado exitosamente como entrenador de sala"));
    }

    public function toggleTrainer(Request $request, array $params): void
    {
        Auth::requireRole('ADMINISTRADOR');

        if (!Auth::validateCsrfToken($request->post('csrf_token'))) {
            Response::redirect('/admin?error=' . urlencode('Sesión expirada o solicitud no válida'));
            return;
        }

        $id = (int)($params['id'] ?? 0);
        if ($id > 0) {
            User::toggleStatus($id);
        }
        Response::redirect('/admin?msg=' . urlencode('Estado del pasante actualizado correctamente'));
    }

    public function deleteTrainer(Request $request, array $params): void
    {
        Auth::requireRole('ADMINISTRADOR');

        if (!Auth::validateCsrfToken($request->post('csrf_token'))) {
            Response::redirect('/admin?error=' . urlencode('Sesión expirada o solicitud no válida'));
            return;
        }

        $id = (int)($params['id'] ?? 0);
        if ($id > 0) {
            User::delete($id);
        }
        Response::redirect('/admin?msg=' . urlencode('Pasante eliminado del sistema'));
    }
}

