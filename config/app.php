<?php
/**
 * GymSENA - Configuración General de la Aplicación
 */

return [
    'app_name' => 'GymSENA PWA',
    'institution' => 'Servicio Nacional de Aprendizaje - SENA',
    'version' => '1.0.0-MVP',
    'env' => 'development', // 'development' o 'production'
    'timezone' => 'America/Bogota',
    'base_url' => '', // Se detecta dinámicamente si está vacío
    
    // Configuración Operativa del Gimnasio por defecto
    'default_gym_id' => 1,
    'default_capacity' => 30,
    'schedule_open' => '06:00:00',
    'schedule_close' => '20:00:00',
    'days_enabled' => ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES'],
    
    // Parámetros Antifraude
    'security_pin_enabled' => false,
    'security_pin' => 'SENA26',
    'geofence_enabled' => false, // Activar en producción
    'gym_coords' => [
        'lat' => 6.25184,
        'lng' => -75.56359,
        'radius_meters' => 200
    ],
    
    // Ley 1581 de 2012 (Habeas Data)
    'data_protection_version' => 'v1.0-2026',
    'data_protection_text' => 'En cumplimiento de la Ley Estatutaria 1581 de 2012 y sus decretos reglamentarios, autorizo de manera libre, previa, expresa e informada al SERVICIO NACIONAL DE APRENDIZAJE - SENA, para el tratamiento de mis datos personales aquí suministrados, con la finalidad exclusiva de gestionar el control de acceso, aforo, seguridad y estadísticas de uso de las instalaciones del gimnasio institucional.'
];

