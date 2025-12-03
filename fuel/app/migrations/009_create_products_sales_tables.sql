-- Migration 009: Crear tablas para cuentas por cobrar y actividad de usuarios
-- Fecha: 2025-12-02
-- Propósito: Crear tablas necesarias para widgets del dashboard
-- NOTA: products, sales, sales_items ya existen en la base de datos

-- ============================================
-- Tabla: accounts_receivable (Cuentas por cobrar)
-- ============================================
CREATE TABLE IF NOT EXISTS `accounts_receivable` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) UNSIGNED NULL,
  `sale_id` INT(11) UNSIGNED NULL,
  `invoice_number` VARCHAR(50) NULL,
  `description` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `due_date` DATE NOT NULL,
  `payment_date` DATE NULL,
  `status` ENUM('pending', 'partial', 'paid', 'overdue', 'cancelled') NOT NULL DEFAULT 'pending',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sale` (`sale_id`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_due_date` (`due_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cuentas por cobrar';

-- ============================================
-- Tabla: user_activity (Actividad de usuarios)
-- ============================================
CREATE TABLE IF NOT EXISTS `user_activity` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `entity_type` VARCHAR(50) NULL COMMENT 'Tipo de entidad afectada (product, sale, invoice, etc)',
  `entity_id` INT(11) UNSIGNED NULL COMMENT 'ID de la entidad afectada',
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_entity` (`entity_type`, `entity_id`),
  CONSTRAINT `fk_user_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de actividad de usuarios';

-- ============================================
-- Datos de prueba para cuentas por cobrar
-- ============================================
INSERT INTO `accounts_receivable` (`sale_id`, `invoice_number`, `description`, `amount`, `paid_amount`, `balance`, `due_date`, `status`) VALUES
(NULL, NULL, 'Servicio de consultoría web', 8500.00, 5000.00, 3500.00, CURDATE() - INTERVAL 5 DAY, 'overdue'),
(NULL, NULL, 'Desarrollo de sistema a medida', 25000.00, 10000.00, 15000.00, CURDATE() + INTERVAL 30 DAY, 'partial'),
(NULL, NULL, 'Mantenimiento mensual', 3500.00, 3500.00, 0.00, CURDATE() - INTERVAL 10 DAY, 'paid'),
(NULL, NULL, 'Hosting y dominio anual', 5000.00, 0.00, 5000.00, CURDATE() + INTERVAL 15 DAY, 'pending');

-- ============================================
-- Datos de prueba para actividad de usuarios
-- ============================================
INSERT INTO `user_activity` (`user_id`, `action`, `description`, `entity_type`, `entity_id`, `ip_address`) VALUES
(2, 'login', 'Inició sesión en el sistema', NULL, NULL, '127.0.0.1'),
(2, 'create', 'Creó un nuevo producto', 'product', 1, '127.0.0.1'),
(2, 'create', 'Registró una venta', 'sale', 1, '127.0.0.1'),
(2, 'update', 'Actualizó inventario del producto', 'product', 1, '127.0.0.1'),
(2, 'create', 'Generó factura', 'sale', 2, '127.0.0.1'),
(2, 'update', 'Modificó precios de productos', 'product', NULL, '127.0.0.1'),
(2, 'view', 'Consultó reportes de ventas', NULL, NULL, '127.0.0.1'),
(2, 'create', 'Registró nuevo cliente', 'customer', NULL, '127.0.0.1'),
(2, 'update', 'Actualizó configuración del sistema', 'config', NULL, '127.0.0.1'),
(2, 'create', 'Registró cuenta por cobrar', 'account_receivable', 2, '127.0.0.1');

-- ============================================
-- Fin de migración 009
-- ============================================
