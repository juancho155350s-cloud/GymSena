<?php
/**
 * GymSENA - Configuración de Conexión a Base de Datos
 * Compatible con XAMPP, WampServer, Docker, VPS y Entornos Educativos SENA.
 */

return [
    'driver' => 'mysql',
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_NAME') ?: 'gymsena_db',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') ?: '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
    // Modo de persistencia resiliente: Si MySQL no está disponible, conmuta a almacenamiento local JSON
    'allow_file_fallback' => true,
    'fallback_storage_path' => dirname(__DIR__) . '/data/gymsena_local.json'
];

