-- ============================================================================
-- SISTEMA DE NÓMINA COMPLETO
-- Sistema Multi-Tenant ERP
-- Versión: 1.0
-- Fecha: 6 de diciembre de 2025
-- ============================================================================

-- ============================================================================
-- 1. TABLA: payroll_periods (Períodos de Nómina)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `payroll_periods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `code` varchar(50) NOT NULL COMMENT 'Código del período (ej: 2025-01)',
  `name` varchar(100) NOT NULL COMMENT 'Nombre descriptivo',
  `period_type` enum('monthly','biweekly','weekly') NOT NULL DEFAULT 'monthly',
  `year` int(4) NOT NULL,
  `period_number` int(2) NOT NULL COMMENT 'Número de período en el año',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `payment_date` date NOT NULL,
  `status` enum('draft','in_progress','calculated','approved','paid','closed') NOT NULL DEFAULT 'draft',
  `total_employees` int(11) unsigned DEFAULT 0,
  `total_gross` decimal(15,2) DEFAULT 0.00 COMMENT 'Total percepciones',
  `total_deductions` decimal(15,2) DEFAULT 0.00 COMMENT 'Total deducciones',
  `total_net` decimal(15,2) DEFAULT 0.00 COMMENT 'Total neto a pagar',
  `notes` text DEFAULT NULL,
  `calculated_by` int(11) unsigned DEFAULT NULL,
  `calculated_at` datetime DEFAULT NULL,
  `approved_by` int(11) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `paid_by` int(11) unsigned DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `closed_by` int(11) unsigned DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_period_code` (`tenant_id`, `code`, `deleted_at`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_period_type` (`period_type`),
  KEY `idx_year_period` (`year`, `period_number`),
  KEY `idx_status` (`status`),
  KEY `idx_dates` (`start_date`, `end_date`),
  KEY `idx_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. TABLA: payroll_concepts (Conceptos de Nómina)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `payroll_concepts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('perception','deduction') NOT NULL,
  `calculation_type` enum('fixed','percentage','formula') NOT NULL DEFAULT 'fixed',
  `calculation_base` enum('base_salary','gross','net','other') DEFAULT 'base_salary',
  `percentage` decimal(5,2) DEFAULT NULL COMMENT 'Para cálculos porcentuales',
  `fixed_amount` decimal(15,2) DEFAULT NULL,
  `formula` text DEFAULT NULL COMMENT 'Fórmula de cálculo personalizada',
  `is_taxable` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Si aplica para ISR',
  `is_social_security` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Si aplica para IMSS',
  `affects_net` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Si afecta el neto',
  `sat_code` varchar(20) DEFAULT NULL COMMENT 'Código SAT para timbrado',
  `display_order` int(3) unsigned DEFAULT 0,
  `is_mandatory` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_concept_code` (`tenant_id`, `code`, `deleted_at`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_type` (`type`),
  KEY `idx_active` (`is_active`),
  KEY `idx_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. TABLA: payroll_employee_concepts (Conceptos Asignados a Empleados)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `payroll_employee_concepts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `employee_id` int(11) unsigned NOT NULL,
  `concept_id` int(11) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `percentage` decimal(5,2) DEFAULT NULL,
  `is_recurring` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Si se aplica cada período',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_employee` (`employee_id`),
  KEY `idx_concept` (`concept_id`),
  KEY `idx_recurring` (`is_recurring`),
  KEY `idx_active` (`is_active`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `fk_pec_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pec_concept` FOREIGN KEY (`concept_id`) REFERENCES `payroll_concepts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. TABLA: payroll_receipts (Recibos de Nómina)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `payroll_receipts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `period_id` int(11) unsigned NOT NULL,
  `employee_id` int(11) unsigned NOT NULL,
  `receipt_number` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  
  -- Información del Empleado (snapshot)
  `employee_code` varchar(50) DEFAULT NULL,
  `employee_name` varchar(300) NOT NULL,
  `department_name` varchar(100) DEFAULT NULL,
  `position_name` varchar(100) DEFAULT NULL,
  `rfc` varchar(13) DEFAULT NULL,
  `nss` varchar(11) DEFAULT NULL,
  `curp` varchar(18) DEFAULT NULL,
  
  -- Información Salarial
  `base_salary` decimal(15,2) NOT NULL DEFAULT 0.00,
  `daily_salary` decimal(15,2) DEFAULT 0.00,
  `worked_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `absence_days` decimal(5,2) DEFAULT 0.00,
  `overtime_hours` decimal(5,2) DEFAULT 0.00,
  
  -- Totales
  `total_perceptions` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_payment` decimal(15,2) NOT NULL DEFAULT 0.00,
  
  -- CFDI (Timbrado SAT)
  `is_stamped` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `cfdi_uuid` varchar(36) DEFAULT NULL,
  `cfdi_xml` longtext DEFAULT NULL,
  `cfdi_pdf` varchar(255) DEFAULT NULL,
  `stamped_at` datetime DEFAULT NULL,
  
  -- Estado y Pago
  `status` enum('pending','approved','paid','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `bank_reference` varchar(100) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_receipt_number` (`tenant_id`, `receipt_number`, `deleted_at`),
  UNIQUE KEY `unique_period_employee` (`period_id`, `employee_id`, `deleted_at`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_period` (`period_id`),
  KEY `idx_employee` (`employee_id`),
  KEY `idx_status` (`status`),
  KEY `idx_cfdi_uuid` (`cfdi_uuid`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `fk_pr_period` FOREIGN KEY (`period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pr_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. TABLA: payroll_receipt_details (Detalle de Recibos)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `payroll_receipt_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `receipt_id` int(11) unsigned NOT NULL,
  `concept_id` int(11) unsigned NOT NULL,
  `concept_code` varchar(50) NOT NULL,
  `concept_name` varchar(100) NOT NULL,
  `concept_type` enum('perception','deduction') NOT NULL,
  `calculation_type` enum('fixed','percentage','formula') NOT NULL DEFAULT 'fixed',
  `base_amount` decimal(15,2) DEFAULT 0.00,
  `percentage` decimal(5,2) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT 1.00,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_taxable` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `display_order` int(3) unsigned DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  
  PRIMARY KEY (`id`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_receipt` (`receipt_id`),
  KEY `idx_concept` (`concept_id`),
  KEY `idx_type` (`concept_type`),
  CONSTRAINT `fk_prd_receipt` FOREIGN KEY (`receipt_id`) REFERENCES `payroll_receipts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prd_concept` FOREIGN KEY (`concept_id`) REFERENCES `payroll_concepts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. TABLA: payroll_bank_dispersion (Dispersión Bancaria)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `payroll_bank_dispersion` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1,
  `period_id` int(11) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_type` enum('txt','excel','xml','layout_bancomer','layout_banamex','layout_santander') NOT NULL DEFAULT 'txt',
  `total_records` int(11) unsigned DEFAULT 0,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('generated','sent','processed','error') NOT NULL DEFAULT 'generated',
  `generated_by` int(11) unsigned DEFAULT NULL,
  `generated_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  
  PRIMARY KEY (`id`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_period` (`period_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_pbd_period` FOREIGN KEY (`period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DATOS INICIALES: Conceptos de Nómina Estándar
-- ============================================================================

INSERT INTO `payroll_concepts` 
(`tenant_id`, `code`, `name`, `description`, `type`, `calculation_type`, `is_taxable`, `is_social_security`, `affects_net`, `sat_code`, `display_order`, `is_mandatory`, `is_active`) 
VALUES
-- PERCEPCIONES
(1, 'P001', 'Sueldo Base', 'Salario base del empleado', 'perception', 'fixed', 1, 1, 1, '001', 1, 1, 1),
(1, 'P002', 'Horas Extra', 'Pago por tiempo extra trabajado', 'perception', 'fixed', 1, 1, 1, '019', 2, 0, 1),
(1, 'P003', 'Prima Vacacional', 'Prima vacacional anual', 'perception', 'percentage', 1, 0, 1, '021', 3, 0, 1),
(1, 'P004', 'Aguinaldo', 'Aguinaldo anual', 'perception', 'percentage', 1, 0, 1, '002', 4, 0, 1),
(1, 'P005', 'Bono de Productividad', 'Bono por cumplimiento de metas', 'perception', 'fixed', 1, 0, 1, '031', 5, 0, 1),
(1, 'P006', 'Vales de Despensa', 'Apoyo para despensa', 'perception', 'fixed', 0, 0, 1, '029', 6, 0, 1),
(1, 'P007', 'Comisiones', 'Comisiones por ventas', 'perception', 'percentage', 1, 1, 1, '003', 7, 0, 1),
(1, 'P008', 'Prima Dominical', 'Pago adicional por trabajo en domingo', 'perception', 'percentage', 1, 1, 1, '025', 8, 0, 1),

-- DEDUCCIONES
(1, 'D001', 'ISR', 'Impuesto Sobre la Renta', 'deduction', 'percentage', 0, 0, 1, '002', 10, 1, 1),
(1, 'D002', 'IMSS', 'Cuota obrera IMSS', 'deduction', 'percentage', 0, 1, 1, '001', 11, 1, 1),
(1, 'D003', 'Préstamo Personal', 'Descuento por préstamo personal', 'deduction', 'fixed', 0, 0, 1, '004', 12, 0, 1),
(1, 'D004', 'Infonavit', 'Descuento Infonavit', 'deduction', 'percentage', 0, 0, 1, '010', 13, 0, 1),
(1, 'D005', 'Fonacot', 'Descuento Fonacot', 'deduction', 'fixed', 0, 0, 1, '016', 14, 0, 1),
(1, 'D006', 'Pensión Alimenticia', 'Descuento por pensión alimenticia', 'deduction', 'percentage', 0, 0, 1, '018', 15, 0, 1),
(1, 'D007', 'Fondo de Ahorro', 'Aportación a fondo de ahorro', 'deduction', 'percentage', 0, 0, 1, '003', 16, 0, 1),
(1, 'D008', 'Caja de Ahorro', 'Descuento para caja de ahorro', 'deduction', 'fixed', 0, 0, 1, '003', 17, 0, 1),
(1, 'D009', 'Faltas', 'Descuento por inasistencias', 'deduction', 'fixed', 0, 0, 1, '107', 18, 0, 1),
(1, 'D010', 'Otros Descuentos', 'Otros descuentos diversos', 'deduction', 'fixed', 0, 0, 1, '999', 19, 0, 1);

-- ============================================================================
-- REGISTRAR MÓDULOS EN EL SISTEMA
-- ============================================================================

-- Módulo de Nómina
INSERT INTO `modules` 
(`name`, `display_name`, `description`, `icon`, `category`, `is_core`, `is_enabled`, `menu_order`) 
VALUES
('nomina', 'Nómina', 'Sistema de nómina y dispersión de pagos', 'fa-money-bill-wave', 'rrhh', 0, 1, 4)
ON DUPLICATE KEY UPDATE 
  `display_name` = VALUES(`display_name`),
  `description` = VALUES(`description`),
  `icon` = VALUES(`icon`),
  `menu_order` = VALUES(`menu_order`);

-- Módulo de Recursos Humanos (Dashboard)
INSERT INTO `modules` 
(`name`, `display_name`, `description`, `icon`, `category`, `is_core`, `is_enabled`, `menu_order`) 
VALUES
('rrhh', 'Recursos Humanos', 'Dashboard ejecutivo de RRHH con KPIs y reportes', 'fa-chart-line', 'rrhh', 0, 1, 5)
ON DUPLICATE KEY UPDATE 
  `display_name` = VALUES(`display_name`),
  `description` = VALUES(`description`),
  `icon` = VALUES(`icon`),
  `menu_order` = VALUES(`menu_order`);

-- ============================================================================
-- CREAR PERMISOS PARA LOS NUEVOS MÓDULOS
-- ============================================================================

-- Permisos para Nómina
INSERT IGNORE INTO `permissions` (`module`, `action`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('nomina', 'index', 'Ver Nómina', 'Permite ver períodos y recibos de nómina', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('nomina', 'create', 'Crear Nómina', 'Permite crear nuevos períodos de nómina', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('nomina', 'edit', 'Editar Nómina', 'Permite modificar períodos de nómina', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('nomina', 'delete', 'Eliminar Nómina', 'Permite eliminar períodos de nómina', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('nomina', 'calculate', 'Calcular Nómina', 'Permite calcular nóminas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('nomina', 'approve', 'Aprobar Nómina', 'Permite aprobar nóminas calculadas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('nomina', 'pay', 'Pagar Nómina', 'Permite marcar nóminas como pagadas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('nomina', 'export', 'Exportar Nómina', 'Permite exportar archivos de dispersión', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('nomina', 'concepts', 'Gestionar Conceptos', 'Permite administrar conceptos de nómina', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('nomina', 'reports', 'Reportes de Nómina', 'Permite ver reportes de nómina', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- Permisos para Recursos Humanos (Dashboard)
INSERT IGNORE INTO `permissions` (`module`, `action`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('rrhh', 'index', 'Ver Dashboard RRHH', 'Permite ver el dashboard de recursos humanos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('rrhh', 'analytics', 'Ver Analytics', 'Permite ver análisis y estadísticas detalladas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('rrhh', 'reports', 'Ver Reportes', 'Permite ver reportes ejecutivos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('rrhh', 'export', 'Exportar Datos', 'Permite exportar datos de RRHH', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- Asignar permisos al rol de administrador (id = 1)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM permissions WHERE module IN ('nomina', 'rrhh');

-- ============================================================================
-- FIN DEL SCRIPT
-- ============================================================================
