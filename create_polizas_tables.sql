-- =============================================
-- MÓDULO: PÓLIZAS CONTABLES
-- Sistema de registro contable con partida doble
-- Fecha: 5 de diciembre de 2025
-- =============================================

-- ========== TABLA: accounting_entries (Pólizas) ==========
CREATE TABLE IF NOT EXISTS `accounting_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(10) unsigned NOT NULL,
  `entry_number` varchar(50) NOT NULL COMMENT 'Folio único de póliza',
  `entry_type` enum('ingreso','egreso','diario','apertura','ajuste','cierre') NOT NULL DEFAULT 'diario',
  `entry_date` date NOT NULL COMMENT 'Fecha contable',
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `period` varchar(7) NOT NULL COMMENT 'Periodo: YYYY-MM',
  `fiscal_year` int(4) NOT NULL COMMENT 'Ejercicio fiscal',
  `concept` text NOT NULL COMMENT 'Concepto general de la póliza',
  `reference` varchar(100) DEFAULT NULL COMMENT 'Referencia externa (factura, pago, etc)',
  `total_debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('borrador','aplicada','cancelada','revisada') NOT NULL DEFAULT 'borrador',
  `is_balanced` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 si cargos = abonos',
  `created_by` int(10) unsigned DEFAULT NULL,
  `applied_by` int(10) unsigned DEFAULT NULL,
  `applied_at` datetime DEFAULT NULL,
  `cancelled_by` int(10) unsigned DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_entry_number` (`tenant_id`,`entry_number`),
  KEY `idx_tenant_date` (`tenant_id`,`entry_date`),
  KEY `idx_period` (`period`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`entry_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========== TABLA: accounting_entry_lines (Movimientos/Partidas) ==========
CREATE TABLE IF NOT EXISTS `accounting_entry_lines` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(10) unsigned NOT NULL,
  `line_number` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT 'Orden de la partida',
  `account_id` int(10) unsigned NOT NULL COMMENT 'FK a accounting_accounts',
  `description` varchar(255) NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reference` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_entry` (`entry_id`),
  KEY `idx_account` (`account_id`),
  CONSTRAINT `fk_entry_lines_entry` FOREIGN KEY (`entry_id`) REFERENCES `accounting_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_entry_lines_account` FOREIGN KEY (`account_id`) REFERENCES `accounting_accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========== ÍNDICES ADICIONALES ==========
ALTER TABLE `accounting_entries` ADD INDEX `idx_fiscal_year` (`fiscal_year`);
ALTER TABLE `accounting_entries` ADD INDEX `idx_reference` (`reference`);

-- ========== VERIFICACIÓN ==========
SELECT 'Tablas creadas exitosamente' as resultado;
SHOW TABLES LIKE 'accounting_entr%';

SELECT 'Estructura de accounting_entries:' as info;
DESCRIBE accounting_entries;

SELECT 'Estructura de accounting_entry_lines:' as info;
DESCRIBE accounting_entry_lines;
