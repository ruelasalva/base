-- ============================================================================
-- Migración de tabla employees a estructura profesional
-- Sistema Multi-Tenant ERP
-- ============================================================================

-- Respaldar tabla antigua (por si acaso)
DROP TABLE IF EXISTS employees_old_backup;
CREATE TABLE employees_old_backup LIKE employees;

-- Eliminar tabla antigua
DROP TABLE IF EXISTS employees;

-- Crear tabla employees con estructura profesional completa
CREATE TABLE `employees` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'Usuario del sistema vinculado (opcional)',
  `code` varchar(50) DEFAULT NULL COMMENT 'Código único del empleado',
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `second_last_name` varchar(100) DEFAULT NULL,
  `gender` enum('M','F','O') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `curp` varchar(18) DEFAULT NULL,
  `rfc` varchar(13) DEFAULT NULL,
  `nss` varchar(11) DEFAULT NULL COMMENT 'Número de Seguro Social',
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `phone_emergency` varchar(20) DEFAULT NULL,
  `emergency_contact_name` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'México',
  
  -- Información Laboral
  `department_id` int(11) unsigned DEFAULT NULL,
  `position_id` int(11) unsigned DEFAULT NULL,
  `hire_date` date NOT NULL,
  `termination_date` date DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','intern','temporary') DEFAULT 'full_time',
  `employment_status` enum('active','inactive','suspended','on_leave','terminated') DEFAULT 'active',
  
  -- Información Financiera
  `salary` decimal(15,2) DEFAULT NULL,
  `salary_type` enum('monthly','biweekly','weekly','hourly','daily') DEFAULT 'monthly',
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `clabe` varchar(18) DEFAULT NULL,
  
  -- Metadata
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_employee_code` (`tenant_id`, `code`),
  UNIQUE KEY `unique_employee_email` (`tenant_id`, `email`, `deleted_at`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_department` (`department_id`),
  KEY `idx_position` (`position_id`),
  KEY `idx_status` (`employment_status`),
  KEY `idx_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Insertar empleados de prueba
-- ============================================================================

INSERT INTO `employees` 
(`tenant_id`, `code`, `first_name`, `last_name`, `second_last_name`, `gender`, `birthdate`, `curp`, `rfc`, `email`, `phone`, `department_id`, `position_id`, `hire_date`, `employment_type`, `employment_status`, `salary`, `salary_type`) 
VALUES
(1, 'EMP001', 'Juan', 'Pérez', 'García', 'M', '1985-03-15', 'PEGJ850315HDFRRN01', 'PEGJ850315ABC', 'juan.perez@empresa.com', '5551234567', 1, 1, '2020-01-15', 'full_time', 'active', 150000.00, 'monthly'),
(1, 'EMP002', 'María', 'López', 'Martínez', 'F', '1990-07-22', 'LOMM900722MDFPRS02', 'LOMM900722XYZ', 'maria.lopez@empresa.com', '5552345678', 2, 2, '2021-03-10', 'full_time', 'active', 35000.00, 'monthly'),
(1, 'EMP003', 'Carlos', 'Sánchez', 'Rodríguez', 'M', '1988-11-30', 'SARC881130HDFNDR03', 'SARC881130QWE', 'carlos.sanchez@empresa.com', '5553456789', 3, 3, '2019-06-01', 'full_time', 'active', 20000.00, 'monthly'),
(1, 'EMP004', 'Ana', 'Ramírez', 'Fernández', 'F', '1992-05-18', 'RAFA920518MDFMRN04', 'RAFA920518RTY', 'ana.ramirez@empresa.com', '5554567890', 3, 4, '2022-02-14', 'full_time', 'active', 15000.00, 'monthly');

-- ============================================================================
-- Registrar módulos de Departamentos y Puestos
-- ============================================================================

-- Departamentos
INSERT INTO `modules` 
(`name`, `display_name`, `description`, `icon`, `category`, `is_core`, `is_enabled`, `menu_order`) 
VALUES
('departamentos', 'Departamentos', 'Gestión de departamentos de la empresa', 'fa-sitemap', 'rrhh', 0, 1, 2)
ON DUPLICATE KEY UPDATE 
  `display_name` = VALUES(`display_name`),
  `description` = VALUES(`description`),
  `icon` = VALUES(`icon`);

-- Puestos
INSERT INTO `modules` 
(`name`, `display_name`, `description`, `icon`, `category`, `is_core`, `is_enabled`, `menu_order`) 
VALUES
('puestos', 'Puestos', 'Gestión de puestos y cargos', 'fa-user-tag', 'rrhh', 0, 1, 3)
ON DUPLICATE KEY UPDATE 
  `display_name` = VALUES(`display_name`),
  `description` = VALUES(`description`),
  `icon` = VALUES(`icon`);

-- ============================================================================
-- Permisos para los nuevos módulos
-- ============================================================================

-- Permisos para empleados
INSERT IGNORE INTO `permissions` (`module`, `action`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('empleados', 'view', 'Ver Empleados', 'Permite ver el listado y detalles de empleados', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('empleados', 'create', 'Crear Empleados', 'Permite crear nuevos empleados', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('empleados', 'edit', 'Editar Empleados', 'Permite modificar empleados existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('empleados', 'delete', 'Eliminar Empleados', 'Permite eliminar empleados', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- Permisos para departamentos
INSERT IGNORE INTO `permissions` (`module`, `action`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('departamentos', 'view', 'Ver Departamentos', 'Permite ver el listado y detalles de departamentos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('departamentos', 'create', 'Crear Departamentos', 'Permite crear nuevos departamentos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('departamentos', 'edit', 'Editar Departamentos', 'Permite modificar departamentos existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('departamentos', 'delete', 'Eliminar Departamentos', 'Permite eliminar departamentos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- Permisos para puestos
INSERT IGNORE INTO `permissions` (`module`, `action`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('puestos', 'view', 'Ver Puestos', 'Permite ver el listado y detalles de puestos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('puestos', 'create', 'Crear Puestos', 'Permite crear nuevos puestos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('puestos', 'edit', 'Editar Puestos', 'Permite modificar puestos existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('puestos', 'delete', 'Eliminar Puestos', 'Permite eliminar puestos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
