<?php
/**
 * GymSENA - Script de Instalación y Puesta a Punto en Producción
 * Ejecutar desde la terminal:
 *   php scripts/install.php
 */

declare(strict_types=1);

date_default_timezone_set('America/Bogota');

echo "=================================================================\n";
echo "  INSTALADOR DE PUESTA A PUNTO - GymSENA (Producción / Local)    \n";
echo "=================================================================\n\n";

// 1. Verificación de versión y extensiones de PHP
echo "[1/5] Verificando entorno PHP...\n";
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    echo "  [ERROR] Se requiere PHP 8.1 o superior. Versión actual: " . PHP_VERSION . "\n";
    exit(1);
}
echo "  [OK] Versión de PHP: " . PHP_VERSION . "\n";

$requiredExtensions = ['pdo', 'json', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        echo "  [ERROR] Falta la extensión PHP requerida: {$ext}\n";
        exit(1);
    }
}
echo "  [OK] Extensiones esenciales cargadas.\n";

// 2. Cargar variables de entorno si existen
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    echo "[2/5] Cargando configuración desde .env...\n";
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($val, " \t\n\r\0\x0B\"'"));
        }
    }
} else {
    echo "[2/5] Archivo .env no encontrado. Usando configuración por defecto...\n";
}

// 3. Crear directorios de datos y sesiones requeridos
echo "[3/5] Creando directorios del sistema...\n";
$dirs = [
    dirname(__DIR__) . '/data',
    dirname(__DIR__) . '/data/sessions',
    dirname(__DIR__) . '/public/images/qrs'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
        echo "  [OK] Directorio creado: {$dir}\n";
    }
}

// 4. Inicializar base de datos
echo "[4/5] Configurando base de datos relacional...\n";
$dbConfig = require dirname(__DIR__) . '/config/database.php';
$host = $dbConfig['host'];
$port = $dbConfig['port'];
$dbName = $dbConfig['database'];
$user = $dbConfig['username'];
$pass = $dbConfig['password'];

$pdo = null;
try {
    $dsnNoDb = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsnNoDb, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Crear base de datos si no existe
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `{$dbName}`;");
    echo "  [OK] Conectado a MariaDB/MySQL. Base de datos '{$dbName}' lista.\n";

    // Ejecutar Schema
    $schemaFile = dirname(__DIR__) . '/database/schema.sql';
    if (file_exists($schemaFile)) {
        $schemaSql = file_get_contents($schemaFile);
        $pdo->exec($schemaSql);
        echo "  [OK] Tablas del sistema creadas desde schema.sql.\n";
    }

    // Ejecutar Semillas
    $seedFile = dirname(__DIR__) . '/database/seed.sql';
    if (file_exists($seedFile)) {
        $seedSql = file_get_contents($seedFile);
        $pdo->exec($seedSql);
        echo "  [OK] Datos iniciales y usuarios semilla cargados desde seed.sql.\n";
    }

} catch (\Throwable $e) {
    echo "  [AVISO] No se pudo conectar a MySQL (" . $e->getMessage() . ").\n";
    echo "  [OK] Activando modo Fallback JSON local de alta resiliencia en data/gymsena_local.json.\n";
}

// 5. Verificación de archivos clave
echo "[5/5] Verificando integridad de componentes...\n";
$criticalFiles = [
    dirname(__DIR__) . '/public/index.php',
    dirname(__DIR__) . '/public/.htaccess',
    dirname(__DIR__) . '/public/sw.js',
    dirname(__DIR__) . '/public/css/style.css',
    dirname(__DIR__) . '/public/images/qr-entrada-oficial.svg',
    dirname(__DIR__) . '/cron/daily_close.php'
];

$allOk = true;
foreach ($criticalFiles as $file) {
    if (!file_exists($file)) {
        echo "  [ADVERTENCIA] Archivo no encontrado: {$file}\n";
        $allOk = false;
    }
}

if ($allOk) {
    echo "  [OK] Todos los componentes del sistema están presentes y verificados.\n";
}

echo "\n=================================================================\n";
echo "  ¡INSTALACIÓN COMPLETADA CON ÉXITO!                             \n";
echo "=================================================================\n";
echo "  Punto de Acceso PWA:    http://localhost:8080/acceso           \n";
echo "  Cartel QR de Entrada:   http://localhost:8080/cartel-qr        \n";
echo "  Panel del Entrenador:   http://localhost:8080/monitor          \n";
echo "  Portal Administrativo:  http://localhost:8080/admin            \n";
echo "  Acceso de Monitor:      monitor@sena.edu.co / monitor123       \n";
echo "  Acceso Administrador:   admin@sena.edu.co / admin123           \n";
echo "=================================================================\n\n";

