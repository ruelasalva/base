-- ============================================
-- ESTRUCTURA DE TABLAS PARA MÓDULO ALMACENES
-- ============================================

USE base;

-- Tabla principal de almacenes
CREATE TABLE IF NOT EXISTS `almacenes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` INT(11) NOT NULL DEFAULT 1,
  `code` VARCHAR(50) NOT NULL COMMENT 'Código único del almacén (ej: ALM-001)',
  `name` VARCHAR(255) NOT NULL COMMENT 'Nombre del almacén',
  `description` TEXT NULL COMMENT 'Descripción del almacén',
  `type` ENUM('principal', 'secundario', 'transito', 'virtual') NOT NULL DEFAULT 'principal' COMMENT 'Tipo de almacén',
  `address` VARCHAR(500) NULL COMMENT 'Dirección física',
  `city` VARCHAR(100) NULL,
  `state` VARCHAR(100) NULL,
  `country` VARCHAR(100) NULL DEFAULT 'México',
  `postal_code` VARCHAR(20) NULL,
  `phone` VARCHAR(50) NULL,
  `manager_user_id` INT(11) NULL COMMENT 'ID del usuario responsable',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `capacity_m2` DECIMAL(10,2) NULL COMMENT 'Capacidad en metros cuadrados',
  `capacity_units` INT(11) NULL COMMENT 'Capacidad estimada en unidades',
  `notes` TEXT NULL,
  `created_by` INT(11) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_almacen_code` (`tenant_id`, `code`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de almacenes';

-- Tabla de ubicaciones dentro del almacén
CREATE TABLE IF NOT EXISTS `almacen_locations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `almacen_id` INT(11) NOT NULL,
  `code` VARCHAR(50) NOT NULL COMMENT 'Código de ubicación (ej: A-01-01)',
  `name` VARCHAR(255) NOT NULL COMMENT 'Nombre de la ubicación',
  `type` ENUM('pasillo', 'estante', 'rack', 'zona', 'area') NOT NULL DEFAULT 'estante',
  `aisle` VARCHAR(10) NULL COMMENT 'Pasillo',
  `section` VARCHAR(10) NULL COMMENT 'Sección',
  `level` VARCHAR(10) NULL COMMENT 'Nivel/Altura',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `capacity_units` INT(11) NULL COMMENT 'Capacidad en unidades',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_location_code` (`almacen_id`, `code`),
  KEY `idx_almacen` (`almacen_id`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_location_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ubicaciones dentro de los almacenes';

-- Insertar almacén por defecto
INSERT INTO `almacenes` (`tenant_id`, `code`, `name`, `description`, `type`, `is_active`, `created_by`) 
VALUES 
(1, 'ALM-001', 'Almacén Principal', 'Almacén principal de la empresa', 'principal', 1, 1),
(1, 'ALM-002', 'Almacén Secundario', 'Almacén de respaldo', 'secundario', 1, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insertar ubicaciones de ejemplo
INSERT INTO `almacen_locations` (`almacen_id`, `code`, `name`, `type`, `aisle`, `section`, `level`, `is_active`) 
VALUES 
(1, 'A-01-01', 'Pasillo A - Sección 1 - Nivel 1', 'estante', 'A', '01', '01', 1),
(1, 'A-01-02', 'Pasillo A - Sección 1 - Nivel 2', 'estante', 'A', '01', '02', 1),
(1, 'A-02-01', 'Pasillo A - Sección 2 - Nivel 1', 'estante', 'A', '02', '01', 1),
(1, 'B-01-01', 'Pasillo B - Sección 1 - Nivel 1', 'estante', 'B', '01', '01', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

SELECT 'Tablas de almacenes creadas exitosamente' AS resultado;
