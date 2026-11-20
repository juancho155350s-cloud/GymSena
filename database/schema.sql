-- =============================================================================
-- PROYECTO FORMATIVO SENA: GymSENA
-- SISTEMA DE CONTROL DE ACCESO, AFORO Y SERVICIOS DEL GIMNASIO
-- Script DDL: Base de Datos Relacional Multi-Gimnasio
-- Motor recomendado: MariaDB 10.4+ / MySQL 8.0+
-- Cumplimiento: Ley 1581 de 2012 (Habeas Data) y Normalización en 3FN
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `gymsena_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `gymsena_db`;

-- -----------------------------------------------------------------------------
-- 1. TABLA: sedes (Centros de Formación SENA)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sedes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo_centro` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código único del Centro SENA',
    `nombre` VARCHAR(150) NOT NULL COMMENT 'Nombre del Centro de Formación',
    `regional` VARCHAR(100) NOT NULL COMMENT 'Regional SENA (ej. Antioquia, D.C., Valle)',
    `direccion` VARCHAR(200) NULL,
    `telefono` VARCHAR(30) NULL,
    `estado` ENUM('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. TABLA: gimnasios (Multi-Gimnasio por Sede)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gimnasios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sede_id` INT NOT NULL,
    `nombre` VARCHAR(100) NOT NULL COMMENT 'Ej: Gimnasio Principal, Sala de Cardio',
    `capacidad_maxima` INT NOT NULL DEFAULT 30 COMMENT 'Aforo máximo permitido simultáneo',
    `latitud` DECIMAL(10, 8) NULL COMMENT 'Para geolocalización perimetral antifraude',
    `longitud` DECIMAL(11, 8) NULL,
    `radio_metros_tolerancia` INT DEFAULT 150 COMMENT 'Radio permitido de escaneo',
    `pin_seguridad_diario` VARCHAR(6) NULL COMMENT 'PIN diario opcional de entrada',
    `estado` ENUM('ABIERTO', 'CERRADO', 'MANTENIMIENTO') NOT NULL DEFAULT 'ABIERTO',
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_gimnasio_sede` FOREIGN KEY (`sede_id`) 
        REFERENCES `sedes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. TABLA: horarios_operacion (Franjas de Lunes a Viernes)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `horarios_operacion` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gimnasio_id` INT NOT NULL,
    `dia_semana` ENUM('LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO') NOT NULL,
    `hora_apertura` TIME NOT NULL,
    `hora_cierre` TIME NOT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_horarios_gimnasio` FOREIGN KEY (`gimnasio_id`) 
        REFERENCES `gimnasios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY `uk_gimnasio_dia` (`gimnasio_id`, `dia_semana`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. TABLA: usuarios_sistema (Administradores y Monitores)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios_sistema` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sede_id` INT NOT NULL,
    `gimnasio_id` INT NULL COMMENT 'NULL si es admin general de la sede',
    `documento` VARCHAR(25) NOT NULL UNIQUE,
    `nombre_completo` VARCHAR(150) NOT NULL,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `rol` ENUM('ADMINISTRADOR', 'MONITOR') NOT NULL DEFAULT 'MONITOR',
    `estado` ENUM('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    `ultimo_login` DATETIME NULL,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_usuario_sede` FOREIGN KEY (`sede_id`) 
        REFERENCES `sedes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_usuario_gimnasio` FOREIGN KEY (`gimnasio_id`) 
        REFERENCES `gimnasios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. TABLA: aprendices
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `aprendices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sede_id` INT NOT NULL,
    `tipo_usuario` ENUM('APRENDIZ', 'INSTRUCTOR', 'FUNCIONARIO', 'INVITADO') NOT NULL DEFAULT 'APRENDIZ',
    `tipo_documento` ENUM('CC', 'TI', 'CE', 'PPT', 'OTRO') NOT NULL DEFAULT 'CC',
    `documento` VARCHAR(25) NOT NULL UNIQUE,
    `nombres` VARCHAR(100) NOT NULL,
    `apellidos` VARCHAR(100) NOT NULL,
    `ficha` VARCHAR(30) NOT NULL COMMENT 'Número de Ficha o identificador de vinculación',
    `programa_formacion` VARCHAR(150) NOT NULL COMMENT 'Programa, Área, Dependencia o Procedencia',
    `correo` VARCHAR(120) NOT NULL,
    `telefono` VARCHAR(30) NOT NULL,
    `contacto_emergencia_nombre` VARCHAR(120) NOT NULL,
    `contacto_emergencia_telefono` VARCHAR(30) NOT NULL,
    `estado_formacion` ENUM('LECTIVA', 'PRODUCTIVA', 'EGRESADO', 'SANCIONADO') NOT NULL DEFAULT 'LECTIVA',
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `token_acceso` VARCHAR(64) NULL COMMENT 'Token aleatorio persistente en el dispositivo',
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_aprendiz_sede` FOREIGN KEY (`sede_id`) 
        REFERENCES `sedes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_aprendiz_ficha` (`ficha`),
    INDEX `idx_aprendiz_documento` (`documento`),
    INDEX `idx_aprendiz_token` (`token_acceso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. TABLA: consentimientos_ley1581 (Trazabilidad Habeas Data Colombia)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `consentimientos_ley1581` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `aprendiz_id` INT NOT NULL,
    `acepto_politica` TINYINT(1) NOT NULL DEFAULT 1,
    `ip_registro` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `version_politica` VARCHAR(20) NOT NULL DEFAULT 'v1.0-2026',
    `fecha_hora_consentimiento` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_consentimiento_aprendiz` FOREIGN KEY (`aprendiz_id`) 
        REFERENCES `aprendices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. TABLA: asistencias (Check-in y Check-out)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `asistencias` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gimnasio_id` INT NOT NULL,
    `aprendiz_id` INT NOT NULL,
    `fecha` DATE NOT NULL COMMENT 'Fecha operativa de la visita',
    `hora_entrada` TIME NOT NULL,
    `hora_salida` TIME NULL,
    `duracion_minutos` INT NULL,
    `metodo_ingreso` ENUM('QR_AUTONOMO', 'MANUAL_MONITOR') NOT NULL DEFAULT 'QR_AUTONOMO',
    `cierre_automatico` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 si se cerró por cron a medianoche',
    `observaciones` VARCHAR(255) NULL,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_asistencia_gimnasio` FOREIGN KEY (`gimnasio_id`) 
        REFERENCES `gimnasios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_asistencia_aprendiz` FOREIGN KEY (`aprendiz_id`) 
        REFERENCES `aprendices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_asistencia_fecha` (`fecha`),
    INDEX `idx_asistencia_abierta` (`gimnasio_id`, `fecha`, `hora_salida`),
    INDEX `idx_asistencia_aprendiz_fecha` (`aprendiz_id`, `fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. TABLA: novedades_sanciones (Incidencias y Bloqueos)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `novedades_sanciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `aprendiz_id` INT NOT NULL,
    `gimnasio_id` INT NOT NULL,
    `usuario_sistema_id` INT NOT NULL COMMENT 'Monitor o Admin que reporta',
    `tipo` ENUM('LESION', 'COMPORTAMIENTO', 'SANCION_TEMPORAL', 'OTRO') NOT NULL,
    `descripcion` TEXT NOT NULL,
    `bloquea_ingreso` TINYINT(1) NOT NULL DEFAULT 0,
    `fecha_inicio` DATE NOT NULL,
    `fecha_fin` DATE NULL,
    `resuelto` TINYINT(1) NOT NULL DEFAULT 0,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_novedad_aprendiz` FOREIGN KEY (`aprendiz_id`) 
        REFERENCES `aprendices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_novedad_gimnasio` FOREIGN KEY (`gimnasio_id`) 
        REFERENCES `gimnasios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_novedad_usuario` FOREIGN KEY (`usuario_sistema_id`) 
        REFERENCES `usuarios_sistema` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- VISTA ANALÍTICA: v_aforo_actual (Conteo instantáneo de aprendices dentro)
-- -----------------------------------------------------------------------------
CREATE OR REPLACE VIEW `v_aforo_actual` AS
SELECT 
    g.`id` AS `gimnasio_id`,
    g.`nombre` AS `gimnasio_nombre`,
    g.`capacidad_maxima`,
    COUNT(a.`id`) AS `ocupacion_actual`,
    GREATEST(0, g.`capacidad_maxima` - COUNT(a.`id`)) AS `cupos_disponibles`
FROM `gimnasios` g
LEFT JOIN `asistencias` a ON g.`id` = a.`gimnasio_id` 
    AND a.`fecha` = CURDATE() 
    AND a.`hora_salida` IS NULL
GROUP BY g.`id`, g.`nombre`, g.`capacidad_maxima`;

