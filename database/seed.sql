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
(1, 1, NULL, '1000000001', 'Coordinador Bienestar SENA', 'admin@sena.edu.co', '$2y$10$54EKvUi5mybzz7/45W34sei2L/B1p0HD7Khe0UYDDyGHALb1KVwom', 'ADMINISTRADOR', 'ACTIVO'),
(2, 1, 1, '1000000002', 'Monitor de Turno Gimnasio', 'monitor@sena.edu.co', '$2y$10$pggjfg3hxCSREuRFqVHZAeSF/esnwxAdlobaivpH2edVagO2kW5uq', 'MONITOR', 'ACTIVO')
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- 5. Aprendices de Muestra
INSERT INTO `aprendices` (`id`, `sede_id`, `tipo_documento`, `documento`, `nombres`, `apellidos`, `ficha`, `programa_formacion`, `correo`, `telefono`, `contacto_emergencia_nombre`, `contacto_emergencia_telefono`, `estado_formacion`, `activo`, `token_acceso`)
VALUES
(1, 1, 'CC', '1020304050', 'Carlos Andrés', 'Gómez Pérez', '2694520', 'Análisis y Desarrollo de Software (ADSO)', 'carlos.gomez@soy.sena.edu.co', '3001234567', 'Marta Pérez (Madre)', '3119876543', 'LECTIVA', 1, 'tok_carlos_2694520'),
(2, 1, 'CC', '1030405060', 'Laura Valentina', 'Restrepo Zapata', '2694520', 'Análisis y Desarrollo de Software (ADSO)', 'laura.restrepo@soy.sena.edu.co', '3104567890', 'Jorge Restrepo (Padre)', '3123456789', 'LECTIVA', 1, 'tok_laura_2694520'),
(3, 1, 'TI', '1040506070', 'Mateo', 'Henao Ramírez', '2712398', 'Gestión Contable y de Información Financiera', 'mateo.henao@soy.sena.edu.co', '3157891234', 'Gloria Ramírez (Tía)', '3161239874', 'LECTIVA', 1, 'tok_mateo_2712398'),
(4, 1, 'CC', '1050607080', 'Sofía', 'Montoya Castaño', '2654120', 'Mantenimiento Mecatrónico Automotriz', 'sofia.montoya@soy.sena.edu.co', '3206549871', 'Pedro Montoya (Hermano)', '3187654321', 'LECTIVA', 1, 'tok_sofia_2654120')
ON DUPLICATE KEY UPDATE `documento` = VALUES(`documento`);

-- 6. Consentimiento Ley 1581 de 2012
INSERT INTO `consentimientos_ley1581` (`aprendiz_id`, `acepto_politica`, `ip_registro`, `version_politica`)
VALUES
(1, 1, '127.0.0.1', 'v1.0-2026'),
(2, 1, '127.0.0.1', 'v1.0-2026'),
(3, 1, '127.0.0.1', 'v1.0-2026'),
(4, 1, '127.0.0.1', 'v1.0-2026');

-- 7. Asistencias de Muestra (Una activa para ver aforo, y visitas pasadas para reportes)
INSERT INTO `asistencias` (`gimnasio_id`, `aprendiz_id`, `fecha`, `hora_entrada`, `hora_salida`, `duracion_minutos`, `metodo_ingreso`, `cierre_automatico`)
VALUES
-- Visita en curso hoy (Entrada activa sin salida)
(1, 1, CURDATE(), '07:15:00', NULL, NULL, 'QR_AUTONOMO', 0),
-- Visitas cerradas hoy
(1, 2, CURDATE(), '06:30:00', '07:45:00', 75, 'QR_AUTONOMO', 0),
-- Visitas históricas
(1, 3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '08:00:00', '09:15:00', 75, 'QR_AUTONOMO', 0),
(1, 4, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '16:00:00', '17:20:00', 80, 'MANUAL_MONITOR', 0),
(1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '07:00:00', '08:30:00', 90, 'QR_AUTONOMO', 0),
(1, 2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '07:10:00', '08:15:00', 65, 'QR_AUTONOMO', 0);

