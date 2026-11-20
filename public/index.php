<?php
/**
 * GymSENA - Punto de Entrada Único (Front Controller)
 */

declare(strict_types=1);

// 0. Soporte para servidor integrado de PHP (servir archivos estáticos directamente)
if (php_sapi_name() === 'cli-server') {
    $requestUriPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $staticFilePath = __DIR__ . $requestUriPath;
    if ($requestUriPath !== '/' && file_exists($staticFilePath) && is_file($staticFilePath)) {
        return false; // Permite al servidor web interno de PHP servir el archivo estático con su MIME type nativo
    }
}

// 1. Configuración de zona horaria y errores
date_default_timezone_set('America/Bogota');
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Carga opcional de variables de entorno (.env) para producción
$envFilePath = dirname(__DIR__) . '/.env';
if (file_exists($envFilePath) && is_readable($envFilePath)) {
    $envLines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            putenv("{$k}={$v}");
            $_ENV[$k] = $v;
            $_SERVER[$k] = $v;
        }
    }
}

// Cabeceras HTTP de Seguridad
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// 2. Autocargador PSR-4 para el espacio de nombres GymSena
spl_autoload_register(function ($class) {
    $prefix = 'GymSena\\';
    $baseDir = dirname(__DIR__) . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use GymSena\Core\Request;
use GymSena\Core\Response;
use GymSena\Core\Router;
use GymSena\Core\Auth;
use GymSena\Controllers\AccessController;
use GymSena\Controllers\LearnerController;
use GymSena\Controllers\MonitorController;
use GymSena\Controllers\AdminController;
use GymSena\Controllers\AuthController;

// 3. Iniciar sesión de usuario
Auth::startSession();

// 4. Instanciar enrutador y definir rutas del sistema
$router = new Router();
$request = new Request();

// Rutas de Archivos Estáticos con MIME type estricto (Garantía de Carga de CSS/JS/SVG/Imágenes)
$router->get('/css/{file}', function ($req, $params) {
    $file = basename($params['file'] ?? '');
    $path = __DIR__ . '/css/' . $file;
    if (file_exists($path) && is_file($path)) {
        header('Content-Type: text/css; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        readfile($path);
        exit;
    }
    http_response_code(404);
    echo 'Stylesheet not found';
    exit;
});
$router->get('/js/app.js', function ($req) {
    header('Content-Type: application/javascript; charset=utf-8');
    readfile(__DIR__ . '/js/app.js');
    exit;
});
$router->get('/icons/{file}', function ($req, $params) {
    $file = basename($params['file'] ?? '');
    $path = __DIR__ . '/icons/' . $file;
    if (file_exists($path) && is_file($path)) {
        header('Content-Type: image/svg+xml; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        readfile($path);
        exit;
    }
    http_response_code(404);
    echo 'Icon not found';
    exit;
});
$router->get('/images/{file}', function ($req, $params) {
    $file = basename($params['file'] ?? '');
    $path = __DIR__ . '/images/' . $file;
    if (file_exists($path) && is_file($path)) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = ($ext === 'png') ? 'image/png' : (($ext === 'svg') ? 'image/svg+xml' : 'image/jpeg');
        header('Content-Type: ' . $mime);
        header('Cache-Control: public, max-age=86400');
        readfile($path);
        exit;
    }
    http_response_code(404);
    echo 'Image not found';
    exit;
});
$router->get('/manifest.json', function ($req) {
    header('Content-Type: application/manifest+json; charset=utf-8');
    readfile(__DIR__ . '/manifest.json');
    exit;
});

// Redirección de la raíz al acceso del gimnasio
$router->get('/', function ($req) {
    Response::redirect('/acceso');
});

// Rutas de Acceso y Aprendiz (PWA)
$router->get('/acceso', [AccessController::class, 'scan']);
$router->post('/procesar-acceso', [AccessController::class, 'process']);
$router->post('/registrar-aprendiz', [AccessController::class, 'register']);
$router->get('/api/check-user', [AccessController::class, 'checkUser']);
$router->get('/api/aforo', [AccessController::class, 'aforoJson']);
$router->get('/cartel-qr', [AccessController::class, 'poster']);

$router->get('/historial', [LearnerController::class, 'history']);
$router->get('/carne', [LearnerController::class, 'digitalCard']);
$router->get('/logout-aprendiz', [LearnerController::class, 'logoutLearner']);

// Rutas Operativas del Monitor
$router->get('/monitor', [MonitorController::class, 'dashboard']);
$router->get('/api/monitor/live-check', [MonitorController::class, 'liveCheck']);
$router->post('/monitor/asistencia-manual', [MonitorController::class, 'manualAccess']);
$router->post('/monitor/marcar-salida/{id}', [MonitorController::class, 'forceCheckout']);

// Rutas del Instructor Encargado / Administrador (Bienestar al Aprendiz)
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->post('/admin/actualizar-config', [AdminController::class, 'updateSettings']);
$router->get('/admin/exportar-csv', [AdminController::class, 'exportCsv']);
$router->post('/admin/carga-masiva', [AdminController::class, 'bulkUpload']);
$router->post('/admin/crear-pasante', [AdminController::class, 'createTrainer']);
$router->post('/admin/toggle-pasante/{id}', [AdminController::class, 'toggleTrainer']);
$router->post('/admin/eliminar-pasante/{id}', [AdminController::class, 'deleteTrainer']);

// Rutas de Autenticación
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// 5. Despachar la petición
$router->dispatch($request);

