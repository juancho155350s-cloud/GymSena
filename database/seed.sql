-- =============================================================================
-- PROYECTO FORMATIVO SENA: GymSENA
-- Script DML: Datos Semilla Institucionales para Demostración y Pruebas
-- =============================================================================

USE `gymsena_db`;

-- 1. Insertar Sede Principal
INSERT INTO `sedes` (`id`, `codigo_centro`, `nombre`, `regional`, `direccion`, `telefono`, `estado`)
VALUES 
(1, '9201', 'Centro de Servicios y Gestión Empresarial', 'Antioquia', 'Calle 51 No. 57-70, Medellín', '(604) 5760000', 'ACTIVO')
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- 2. Insertar Gimnasio Principal
INSERT INTO `gimnasios` (`id`, `sede_id`, `nombre`, `capacidad_maxima`, `latitud`, `longitud`, `radio_metros_tolerancia`, `pin_seguridad_diario`, `estado`)
VALUES 
(1, 1, 'Gimnasio Principal - Bienestar al Aprendiz', 30, 6.25184, -75.56359, 150, 'SENA26', 'ABIERTO')
ON DUPLICATE KEY UPDATE `capacidad_maxima` = VALUES(`capacidad_maxima`);

-- 3. Horarios de Operación (Lunes a Viernes de 06:00 a 20:00)
INSERT INTO `horarios_operacion` (`gimnasio_id`, `dia_semana`, `hora_apertura`, `hora_cierre`, `activo`)
VALUES
(1, 'LUNES', '06:00:00', '20:00:00', 1),
(1, 'MARTES', '06:00:00', '20:00:00', 1),
(1, 'MIERCOLES', '06:00:00', '20:00:00', 1),
(1, 'JUEVES', '06:00:00', '20:00:00', 1),
(1, 'VIERNES', '06:00:00', '20:00:00', 1)
ON DUPLICATE KEY UPDATE `hora_apertura` = VALUES(`hora_apertura`), `hora_cierre` = VALUES(`hora_cierre`);

-- 4. Usuarios Administrativos del Sistema
-- Clave admin: admin123  | Clave monitor: monitor123
INSERT INTO `usuarios_sistema` (`id`, `sede_id`, `gimnasio_id`, `documento`, `nombre_completo`, `email`, `password_hash`, `rol`, `estado`)
VALUES
(1, 1, NULL, '1000000001', 'Instructor Encargado Gimnasio', 'admin@sena.edu.co', '$2y$10$54EKvUi5mybzz7/45W34sei2L/B1p0HD7Khe0UYDDyGHALb1KVwom', 'ADMINISTRADOR', 'ACTIVO'),
(2, 1, 1, '1000000002', 'Pasante Entrenador de Sala', 'monitor@sena.edu.co', '$2y$10$pggjfg3hxCSREuRFqVHZAeSF/esnwxAdlobaivpH2edVagO2kW5uq', 'MONITOR', 'ACTIVO')
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);


