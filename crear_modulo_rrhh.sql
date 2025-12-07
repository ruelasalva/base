-- ============================================
-- MÓDULO DE RECURSOS HUMANOS (RRHH)
-- Sistema de gestión de empleados
-- ============================================

-- Tabla principal de empleados
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'Si tiene acceso al sistema',
  `code` varchar(50) DEFAULT NULL COMMENT 'Número de empleado',
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `phone_emergency` varchar(20) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `marital_status` enum('single','married','divorced','widowed') DEFAULT NULL,
  
  -- Identificación
  `tax_id` varchar(50) DEFAULT NULL COMMENT 'RFC',
  `curp` varchar(20) DEFAULT NULL,
  `nss` varchar(20) DEFAULT NULL COMMENT 'Número Seguro Social',
  
  -- Información laboral
  `department_id` int(11) unsigned DEFAULT NULL,
  `position_id` int(11) unsigned DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contractor','intern') DEFAULT 'full_time',
  `employment_status` enum('active','inactive','on_leave','terminated') DEFAULT 'active',
  
  -- Información salarial
  `salary` decimal(15,2) DEFAULT NULL,
  `salary_type` enum('monthly','biweekly','weekly','hourly') DEFAULT 'monthly',
  `bank_account` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `clabe` varchar(18) DEFAULT NULL,
  
  -- Dirección
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'México',
  
  -- Control
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_tenant` (`code`, `tenant_id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`),
  KEY `position_id` (`position_id`),
  KEY `employment_status` (`employment_status`),
  KEY `is_active` (`is_active`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de departamentos
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `parent_id` int(11) unsigned DEFAULT NULL COMMENT 'Para jerarquías',
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `manager_id` int(11) unsigned DEFAULT NULL COMMENT 'Jefe del departamento',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `parent_id` (`parent_id`),
  KEY `manager_id` (`manager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de puestos/cargos
CREATE TABLE IF NOT EXISTS `positions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `salary_min` decimal(15,2) DEFAULT NULL,
  `salary_max` decimal(15,2) DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de documentos de empleados
CREATE TABLE IF NOT EXISTS `employee_documents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `employee_id` int(11) unsigned NOT NULL,
  `document_type` varchar(50) NOT NULL COMMENT 'INE, CURP, comprobante, etc',
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `employee_id` (`employee_id`),
  KEY `document_type` (`document_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de asistencias (opcional, para control de horarios)
CREATE TABLE IF NOT EXISTS `employee_attendance` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `employee_id` int(11) unsigned NOT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` enum('present','absent','late','on_leave','holiday') DEFAULT 'present',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_date` (`employee_id`, `date`),
  KEY `tenant_id` (`tenant_id`),
  KEY `date` (`date`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar departamentos ejemplo
INSERT INTO `departments` (`tenant_id`, `name`, `code`, `description`, `is_active`) VALUES
(1, 'Dirección General', 'DIR', 'Dirección ejecutiva de la empresa', 1),
(1, 'Recursos Humanos', 'RRHH', 'Gestión del personal y nómina', 1),
(1, 'Ventas', 'SALES', 'Equipo comercial y ventas', 1),
(1, 'Compras', 'COMP', 'Adquisiciones y proveedores', 1),
(1, 'Contabilidad', 'CONT', 'Contabilidad y finanzas', 1),
(1, 'Almacén', 'ALM', 'Control de inventario', 1),
(1, 'Sistemas', 'IT', 'Tecnologías de la información', 1);

-- Insertar puestos ejemplo
INSERT INTO `positions` (`tenant_id`, `name`, `code`, `description`, `salary_min`, `salary_max`, `is_active`) VALUES
(1, 'Director General', 'DG', 'Máximo responsable ejecutivo', 50000.00, 150000.00, 1),
(1, 'Gerente', 'GER', 'Responsable de área', 25000.00, 50000.00, 1),
(1, 'Supervisor', 'SUP', 'Supervisión de equipo', 15000.00, 25000.00, 1),
(1, 'Vendedor', 'VEN', 'Ejecutivo de ventas', 10000.00, 20000.00, 1),
(1, 'Auxiliar Administrativo', 'AUX', 'Apoyo administrativo', 8000.00, 12000.00, 1),
(1, 'Contador', 'CONT', 'Contador general', 15000.00, 30000.00, 1),
(1, 'Almacenista', 'ALM', 'Control de almacén', 9000.00, 15000.00, 1);

-- Agregar módulo employees a la tabla modules
INSERT INTO `modules` (`name`, `display_name`, `description`, `icon`, `category`, `menu_order`, `is_core`, `is_enabled`, `requires_modules`, `created_at`) VALUES
('empleados', 'Empleados', 'Gestión de empleados y recursos humanos', 'fa-users-cog', 'rrhh', 1, 0, 1, NULL, NOW());

-- Agregar módulo departments
INSERT INTO `modules` (`name`, `display_name`, `description`, `icon`, `category`, `menu_order`, `is_core`, `is_enabled`, `requires_modules`, `created_at`) VALUES
('departamentos', 'Departamentos', 'Gestión de departamentos y áreas', 'fa-sitemap', 'rrhh', 2, 0, 1, '["empleados"]', NOW());

-- Agregar módulo positions
INSERT INTO `modules` (`name`, `display_name`, `description`, `icon`, `category`, `menu_order`, `is_core`, `is_enabled`, `requires_modules`, `created_at`) VALUES
('puestos', 'Puestos', 'Catálogo de puestos y cargos', 'fa-id-card', 'rrhh', 3, 0, 1, '["empleados"]', NOW());
