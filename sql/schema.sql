-- ==============================================================================
-- SISTEMA DE GESTIÓN CLÍNICA: PODOLOGÍA ANA RAMÍREZ (PERÚ)
-- Autenticación basada estrictamente en DNI peruano (8 dígitos, admite ceros iniciales)
-- Tipos de Documento en Perú: DNI, Carné de Extranjería, Pasaporte, Otro
-- Contraseña inicial = Número de DNI (con cambio obligatorio al primer ingreso)
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `podoana_db`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `podoana_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. TABLA: roles
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(30) NOT NULL UNIQUE COMMENT 'superadmin, admin, especialista, recepcionista',
  `nombre` VARCHAR(50) NOT NULL UNIQUE,
  `nivel_jerarquia` TINYINT UNSIGNED NOT NULL DEFAULT 3,
  `descripcion` VARCHAR(255) NULL,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. TABLA: usuarios
-- Autenticación exclusiva por DNI. Contraseña inicial = DNI.
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `rol_id` INT UNSIGNED NOT NULL,
  `dni` VARCHAR(20) NOT NULL UNIQUE COMMENT 'DNI peruano (8 dígitos, ej: 43285473 o 00252525)',
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `debe_cambiar_password` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1: Obliga a cambiar clave al iniciar por primera vez',
  `password_cambiado_en` DATETIME NULL,
  `telefono` VARCHAR(30) NULL,
  `especialidad` VARCHAR(100) NULL,
  `numero_colegiado` VARCHAR(50) NULL,
  `estado` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  `ultimo_acceso` DATETIME NULL,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_usuarios_rol`
    FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_usuarios_dni` (`dni`),
  INDEX `idx_usuarios_rol_estado` (`rol_id`, `estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. TABLA: pacientes
-- Documentos adaptados a la normativa peruana: DNI, Carné de Extranjería, Pasaporte
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `pacientes`;
CREATE TABLE `pacientes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `documento_identidad` VARCHAR(20) NOT NULL UNIQUE COMMENT 'DNI (8 dígitos) o CE, preservando ceros iniciales',
  `tipo_documento` ENUM('DNI', 'Carné de Extranjería', 'Pasaporte', 'Otro') NOT NULL DEFAULT 'DNI',
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NOT NULL,
  `fecha_nacimiento` DATE NOT NULL,
  `genero` ENUM('Femenino', 'Masculino', 'Otro') NOT NULL,
  `telefono` VARCHAR(30) NOT NULL COMMENT 'Celular (9 dígitos en Perú)',
  `telefono_emergencia` VARCHAR(30) NULL,
  `contacto_emergencia` VARCHAR(150) NULL,
  `email` VARCHAR(150) NULL,
  `direccion` VARCHAR(255) NULL,
  `ocupacion` VARCHAR(120) NULL,
  `es_diabetico` TINYINT(1) NOT NULL DEFAULT 0,
  `tipo_diabetes` VARCHAR(50) NULL,
  `tiene_hipertension` TINYINT(1) NOT NULL DEFAULT 0,
  `problemas_circulatorios` TINYINT(1) NOT NULL DEFAULT 0,
  `usa_anticoagulantes` TINYINT(1) NOT NULL DEFAULT 0,
  `tiene_marcapasos` TINYINT(1) NOT NULL DEFAULT 0,
  `enfermedades_renales` TINYINT(1) NOT NULL DEFAULT 0,
  `otras_patologias` TEXT NULL,
  `alergias_medicamentos` TEXT NULL,
  `alergias_contacto` TEXT NULL,
  `tipo_calzado_habitual` VARCHAR(100) NULL,
  `talla_calzado` VARCHAR(10) NULL,
  `usa_plantillas` TINYINT(1) NOT NULL DEFAULT 0,
  `actividad_fisica` VARCHAR(100) NULL,
  `creado_por_usuario_id` INT UNSIGNED NOT NULL,
  `fecha_creacion` DATE NOT NULL,
  `hora_creacion` TIME NOT NULL,
  `estado` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  `eliminado_en` DATETIME NULL,
  `eliminado_por_usuario_id` INT UNSIGNED NULL,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_pacientes_creador`
    FOREIGN KEY (`creado_por_usuario_id`) REFERENCES `usuarios` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pacientes_eliminador`
    FOREIGN KEY (`eliminado_por_usuario_id`) REFERENCES `usuarios` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_pacientes_documento` (`documento_identidad`),
  INDEX `idx_pacientes_apellidos_nombres` (`apellido`, `nombre`),
  INDEX `idx_pacientes_telefono` (`telefono`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. TABLA: historial_cambios_pacientes
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `historial_cambios_pacientes`;
CREATE TABLE `historial_cambios_pacientes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `paciente_id` INT UNSIGNED NOT NULL,
  `campo_modificado` VARCHAR(100) NOT NULL,
  `valor_anterior` TEXT NULL,
  `valor_nuevo` TEXT NULL,
  `modificado_por_usuario_id` INT UNSIGNED NOT NULL,
  `fecha_hora` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `motivo_cambio` VARCHAR(255) NULL,
  CONSTRAINT `fk_historial_paciente`
    FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_historial_usuario`
    FOREIGN KEY (`modificado_por_usuario_id`) REFERENCES `usuarios` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_historial_paciente_fecha` (`paciente_id`, `fecha_hora` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. TABLA: historias_clinicas
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `historias_clinicas`;
CREATE TABLE `historias_clinicas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `paciente_id` INT UNSIGNED NOT NULL,
  `especialista_id` INT UNSIGNED NOT NULL,
  `creado_por_usuario_id` INT UNSIGNED NOT NULL,
  `fecha_creacion` DATE NOT NULL,
  `hora_creacion` TIME NOT NULL,
  `fecha_atencion` DATETIME NOT NULL,
  `motivo_consulta` VARCHAR(255) NOT NULL,
  `diagnostico` TEXT NOT NULL,
  `procedimiento_realizado` TEXT NOT NULL,
  `evolucion_lesion` TEXT NULL,
  `prescripcion_recomendaciones` TEXT NULL,
  `observaciones_privadas` TEXT NULL,
  `proxima_visita_sugerida` DATE NULL,
  `estado` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  `eliminado_en` DATETIME NULL,
  `eliminado_por_usuario_id` INT UNSIGNED NULL,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_historia_paciente`
    FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_historia_especialista`
    FOREIGN KEY (`especialista_id`) REFERENCES `usuarios` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_historia_creador`
    FOREIGN KEY (`creado_por_usuario_id`) REFERENCES `usuarios` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_historia_paciente_fecha` (`paciente_id`, `fecha_atencion` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 6. TABLA: citas
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `citas`;
CREATE TABLE `citas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `paciente_id` INT UNSIGNED NOT NULL,
  `especialista_id` INT UNSIGNED NOT NULL,
  `creado_por_usuario_id` INT UNSIGNED NOT NULL,
  `fecha_creacion` DATE NOT NULL,
  `hora_creacion` TIME NOT NULL,
  `fecha_cita` DATE NOT NULL,
  `hora_inicio` TIME NOT NULL,
  `hora_fin` TIME NOT NULL,
  `motivo` VARCHAR(255) NOT NULL,
  `estado` ENUM('pendiente', 'en_atencion', 'finalizado', 'cancelado') NOT NULL DEFAULT 'pendiente',
  `notas` VARCHAR(255) NULL,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_citas_paciente`
    FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_citas_especialista`
    FOREIGN KEY (`especialista_id`) REFERENCES `usuarios` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_citas_creador`
    FOREIGN KEY (`creado_por_usuario_id`) REFERENCES `usuarios` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_citas_agenda_especialista` (`especialista_id`, `fecha_cita`, `hora_inicio`),
  INDEX `idx_citas_paciente` (`paciente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 7. TABLA: logs_sistema
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `logs_sistema`;
CREATE TABLE `logs_sistema` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT UNSIGNED NULL,
  `nivel` ENUM('INFO', 'WARNING', 'SECURITY', 'ERROR') NOT NULL DEFAULT 'INFO',
  `modulo` VARCHAR(50) NOT NULL,
  `accion` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `ip_origen` VARCHAR(45) NOT NULL,
  `user_agent` VARCHAR(255) NULL,
  `fecha_hora` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_logs_usuario`
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_logs_nivel_fecha` (`nivel`, `fecha_hora` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- DATOS SEMILLA: FORMATO DNI PERÚ (8 DÍGITOS)
-- ==============================================================================

-- 1. Roles
INSERT INTO `roles` (`id`, `slug`, `nombre`, `nivel_jerarquia`, `descripcion`) VALUES
  (1, 'superadmin', 'SuperAdmin / Creador Técnico', 1, 'Acceso exclusivo al panel de salud de BD, logs y reporte maestro'),
  (2, 'admin', 'Dueña / Administradora', 2, 'Gestión del negocio, edición autorizada de datos secundarios e impresión'),
  (3, 'especialista', 'Podólogo / Especialista', 3, 'Registro de atenciones clínicas y apertura de fichas'),
  (4, 'recepcionista', 'Recepcionista', 3, 'Gestión de turnos y registro de pacientes')
ON DUPLICATE KEY UPDATE `slug` = VALUES(`slug`);

-- 2. Usuarios del Centro Clínico
-- SuperAdmin Técnico: DNI 43285473 | Contraseña: 123456 (previamente definida)
-- Dueña / Admin:      DNI 10234567 | Contraseña inicial: 10234567
-- Podólogo:           DNI 20345678 | Contraseña inicial: 20345678
-- Recepcionista:      DNI 30456789 | Contraseña inicial: 30456789

INSERT INTO `usuarios` (`id`, `rol_id`, `dni`, `nombre`, `apellido`, `email`, `password_hash`, `debe_cambiar_password`, `telefono`, `especialidad`, `numero_colegiado`, `estado`) VALUES
  (1, 1, '43285473', 'Soporte', 'Técnico', 'soporte@podologiaanaramirez.com', '$2y$10$U.oEFupEuaV9kESwZeWX1.ESxQ5wXFScdDxMPQ5kG.f7xMB.i2KDG', 0, '999000001', 'Arquitectura & DBA', 'IT-001', 'activo'),
  (2, 2, '10234567', 'Ana', 'Ramírez', 'ana@podologiaanaramirez.com', '$2y$10$d0jeuQRUZQd.xmt9gg4Io.NqitW1rhhtEef3d7zowI..mHDU8yepi', 1, '991234567', 'Podología Clínica y Pie Diabético', 'COP-09823', 'activo'),
  (3, 3, '20345678', 'Carlos', 'Mendoza', 'carlos@podologiaanaramirez.com', '$2y$10$/ekDNnWG0aa5YFw2rUnmPOnQUOxYouIE/RvSqIOsTWIw1xX7eX5jW', 1, '987654321', 'Ortopodología y Biomecánica', 'COP-11452', 'activo'),
  (4, 4, '30456789', 'Lucía', 'Gómez', 'recepcion@podologiaanaramirez.com', '$2y$10$2F9D0cZw1.dt.MX3ib6ygO.IMQvYqRzGbTOMLoDXD5.LaFpxus93K', 1, '953332211', 'Atención al Paciente', NULL, 'activo')
ON DUPLICATE KEY UPDATE `dni` = VALUES(`dni`), `password_hash` = VALUES(`password_hash`);

-- 3. Paciente Ejemplo (Demostrando preservación de DNI peruano con cero inicial)
INSERT INTO `pacientes` (
  `id`, `documento_identidad`, `tipo_documento`, `nombre`, `apellido`,
  `fecha_nacimiento`, `genero`, `telefono`, `telefono_emergencia`, `contacto_emergencia`,
  `email`, `direccion`, `ocupacion`,
  `es_diabetico`, `tipo_diabetes`, `tiene_hipertension`, `problemas_circulatorios`,
  `usa_anticoagulantes`, `tiene_marcapasos`, `enfermedades_renales`, `otras_patologias`,
  `alergias_medicamentos`, `alergias_contacto`,
  `tipo_calzado_habitual`, `talla_calzado`, `usa_plantillas`, `actividad_fisica`,
  `creado_por_usuario_id`, `fecha_creacion`, `hora_creacion`, `estado`
) VALUES (
  1, '08123456', 'DNI', 'Roberto', 'Villacís',
  '1968-05-14', 'Masculino', '998765432', '981122334', 'Elena Villacís (Hija)',
  'roberto.villacis@example.com', 'Av. Javier Prado Este 1420, San Isidro, Lima', 'Contador',
  1, 'Tipo 2 (Metformina)', 1, 1,
  1, 0, 0, 'Hiperqueratosis plantar recurrente',
  'Alergia a la Penicilina', 'Sensibilidad a esparadrapos comunes',
  'Formal/Cuero', '42', 0, 'Sedentario (Camina 20 min al día)',
  3, '2026-10-15', '09:30:00', 'activo'
)
ON DUPLICATE KEY UPDATE `documento_identidad` = VALUES(`documento_identidad`);

-- 4. Registro de Auditoría Inicial
INSERT INTO `historial_cambios_pacientes` (
  `id`, `paciente_id`, `campo_modificado`, `valor_anterior`, `valor_nuevo`, `modificado_por_usuario_id`, `fecha_hora`, `motivo_cambio`
) VALUES
  (1, 1, 'telefono', '990001111', '998765432', 2, '2026-10-16 11:20:00', 'Actualización de celular solicitada por el paciente en recepción'),
  (2, 1, 'tipo_calzado_habitual', 'Deportivo', 'Formal/Cuero', 2, '2026-10-16 11:20:00', 'Corrección en ficha tras consulta presencial')
ON DUPLICATE KEY UPDATE `paciente_id` = VALUES(`paciente_id`);

-- 5. Historia Clínica Inicial
INSERT INTO `historias_clinicas` (
  `id`, `paciente_id`, `especialista_id`, `creado_por_usuario_id`, `fecha_creacion`, `hora_creacion`, `fecha_atencion`,
  `motivo_consulta`, `diagnostico`, `procedimiento_realizado`,
  `evolucion_lesion`, `prescripcion_recomendaciones`, `observaciones_privadas`, `proxima_visita_sugerida`
) VALUES (
  1, 1, 3, 3, '2026-10-15', '10:30:00', '2026-10-15 10:30:00',
  'Dolor y molestia en uña de primer dedo pie derecho al caminar',
  'Onicocriptosis en estadío I borde tibial derecho con heloma subungueal.',
  'Antisepsia estricta con clorhexidina al 2%. Deslaminado de hiperqueratosis periungueal y espiculotomía conservadora. Curación oclusiva no compresiva.',
  'Buena tolerancia al procedimiento sin sangrado profuso a pesar de terapia anticoagulante.',
  'Lavado diario con suero fisiológico, aplicación de povidona dérmica y cambio de calzado amplio.',
  'Paciente colaborador. Realizar seguimiento estricto por antecedente de Pie Diabético.',
  '2026-11-15'
)
ON DUPLICATE KEY UPDATE `paciente_id` = VALUES(`paciente_id`);
