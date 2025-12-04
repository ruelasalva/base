-- Crear todas las tablas del sistema de inventario
-- Ejecutar por partes

USE base;

-- 1. CATEGORÍAS
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `parent_id` INT UNSIGNED NULL,
  `code` VARCHAR(20) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `icon` VARCHAR(50) DEFAULT 'fa-folder',
  `level` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `path` VARCHAR(255) NULL,
  `order_position` INT UNSIGNED DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` INT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_category_code` (`tenant_id`, `code`),
  KEY `idx_category_parent` (`parent_id`),
  KEY `idx_category_tenant` (`tenant_id`),
  CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. MARCAS
CREATE TABLE IF NOT EXISTS `product_brands` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `code` VARCHAR(20) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `logo_url` VARCHAR(255),
  `website` VARCHAR(255),
  `country` VARCHAR(50),
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` INT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_brand_code` (`tenant_id`, `code`),
  KEY `idx_brand_tenant` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. CUENTAS CONTABLES
CREATE TABLE IF NOT EXISTS `accounting_accounts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `parent_id` INT UNSIGNED NULL,
  `account_code` VARCHAR(20) NOT NULL,
  `sat_code` VARCHAR(20) NULL,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `account_type` ENUM('activo','pasivo','capital','ingresos','egresos','resultado') NOT NULL,
  `nature` ENUM('deudora','acreedora') NOT NULL,
  `level` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `allows_movement` TINYINT(1) NOT NULL DEFAULT 1,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` INT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_account_code` (`tenant_id`, `account_code`),
  KEY `idx_account_parent` (`parent_id`),
  KEY `idx_account_tenant` (`tenant_id`),
  CONSTRAINT `fk_account_parent` FOREIGN KEY (`parent_id`) REFERENCES `accounting_accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. LISTAS DE PRECIOS
CREATE TABLE IF NOT EXISTS `price_lists` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `code` VARCHAR(20) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `list_type` ENUM('mayoreo','menudeo','distribuidor','especial') NOT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `valid_from` DATE NULL,
  `valid_to` DATE NULL,
  `created_by` INT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pricelist_code` (`tenant_id`, `code`),
  KEY `idx_pricelist_tenant` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `price_list_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `price_list_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `price` DECIMAL(15,4) NOT NULL,
  `min_quantity` INT DEFAULT 1,
  `discount_percent` DECIMAL(5,2) DEFAULT 0.00,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pricelist_product` (`price_list_id`, `product_id`, `min_quantity`),
  KEY `idx_priceitem_product` (`product_id`),
  CONSTRAINT `fk_priceitem_list` FOREIGN KEY (`price_list_id`) REFERENCES `price_lists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_priceitem_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. INVENTARIO
CREATE TABLE IF NOT EXISTS `inventory_stock` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `almacen_id` INT UNSIGNED NOT NULL,
  `location_id` INT UNSIGNED NULL,
  `quantity` DECIMAL(15,4) NOT NULL DEFAULT 0.0000,
  `reserved_quantity` DECIMAL(15,4) NOT NULL DEFAULT 0.0000,
  `cost_average` DECIMAL(15,4) DEFAULT 0.0000,
  `last_movement_date` DATETIME NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_stock_product_location` (`product_id`, `almacen_id`, `location_id`),
  KEY `idx_stock_tenant` (`tenant_id`),
  KEY `idx_stock_almacen` (`almacen_id`),
  KEY `idx_stock_location` (`location_id`),
  CONSTRAINT `fk_stock_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_stock_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_stock_location` FOREIGN KEY (`location_id`) REFERENCES `almacen_locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. MOVIMIENTOS
CREATE TABLE IF NOT EXISTS `inventory_movements` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `almacen_id` INT UNSIGNED NOT NULL,
  `location_id` INT UNSIGNED NULL,
  `movement_type` ENUM('entrada','salida','ajuste','transferencia','devolucion') NOT NULL,
  `movement_subtype` VARCHAR(50),
  `reference_type` VARCHAR(50),
  `reference_id` INT UNSIGNED NULL,
  `reference_number` VARCHAR(50) NULL,
  `quantity` DECIMAL(15,4) NOT NULL,
  `unit_cost` DECIMAL(15,4) DEFAULT 0.0000,
  `total_cost` DECIMAL(15,4) DEFAULT 0.0000,
  `balance_before` DECIMAL(15,4) DEFAULT 0.0000,
  `balance_after` DECIMAL(15,4) DEFAULT 0.0000,
  `notes` TEXT,
  `created_by` INT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_movement_tenant` (`tenant_id`),
  KEY `idx_movement_product` (`product_id`),
  KEY `idx_movement_almacen` (`almacen_id`),
  KEY `idx_movement_type` (`movement_type`),
  CONSTRAINT `fk_movement_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_movement_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. ÓRDENES DE COMPRA
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `order_number` VARCHAR(50) NOT NULL,
  `provider_id` INT UNSIGNED NOT NULL,
  `almacen_id` INT UNSIGNED NOT NULL,
  `order_date` DATE NOT NULL,
  `expected_date` DATE NULL,
  `currency` VARCHAR(3) DEFAULT 'MXN',
  `exchange_rate` DECIMAL(10,6) DEFAULT 1.000000,
  `status` ENUM('borrador','enviada','autorizada','parcial','recibida','cancelada') NOT NULL DEFAULT 'borrador',
  `subtotal` DECIMAL(15,4) DEFAULT 0.0000,
  `tax_amount` DECIMAL(15,4) DEFAULT 0.0000,
  `total` DECIMAL(15,4) DEFAULT 0.0000,
  `notes` TEXT,
  `internal_notes` TEXT,
  `created_by` INT UNSIGNED,
  `approved_by` INT UNSIGNED NULL,
  `approved_at` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_po_number` (`tenant_id`, `order_number`),
  KEY `idx_po_tenant` (`tenant_id`),
  KEY `idx_po_provider` (`provider_id`),
  KEY `idx_po_almacen` (`almacen_id`),
  KEY `idx_po_status` (`status`),
  CONSTRAINT `fk_po_provider` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`),
  CONSTRAINT `fk_po_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` DECIMAL(15,4) NOT NULL,
  `quantity_received` DECIMAL(15,4) DEFAULT 0.0000,
  `unit_price` DECIMAL(15,4) NOT NULL,
  `discount_percent` DECIMAL(5,2) DEFAULT 0.00,
  `discount_amount` DECIMAL(15,4) DEFAULT 0.0000,
  `tax_percent` DECIMAL(5,2) DEFAULT 16.00,
  `tax_amount` DECIMAL(15,4) DEFAULT 0.0000,
  `subtotal` DECIMAL(15,4) DEFAULT 0.0000,
  `total` DECIMAL(15,4) DEFAULT 0.0000,
  `notes` TEXT,
  PRIMARY KEY (`id`),
  KEY `idx_poitem_po` (`purchase_order_id`),
  KEY `idx_poitem_product` (`product_id`),
  CONSTRAINT `fk_poitem_po` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_poitem_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. RECEPCIONES
CREATE TABLE IF NOT EXISTS `purchase_receipts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `receipt_number` VARCHAR(50) NOT NULL,
  `purchase_order_id` INT UNSIGNED NOT NULL,
  `almacen_id` INT UNSIGNED NOT NULL,
  `receipt_date` DATETIME NOT NULL,
  `received_by` INT UNSIGNED,
  `status` ENUM('borrador','recibido','verificado','discrepancia','cancelado') NOT NULL DEFAULT 'borrador',
  `notes` TEXT,
  `discrepancy_notes` TEXT,
  `created_by` INT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_receipt_number` (`tenant_id`, `receipt_number`),
  KEY `idx_receipt_tenant` (`tenant_id`),
  KEY `idx_receipt_po` (`purchase_order_id`),
  KEY `idx_receipt_almacen` (`almacen_id`),
  CONSTRAINT `fk_receipt_po` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  CONSTRAINT `fk_receipt_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `purchase_receipt_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_receipt_id` INT UNSIGNED NOT NULL,
  `purchase_order_item_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `location_id` INT UNSIGNED NULL,
  `quantity_expected` DECIMAL(15,4) NOT NULL,
  `quantity_received` DECIMAL(15,4) NOT NULL,
  `unit_cost` DECIMAL(15,4) NOT NULL,
  `total_cost` DECIMAL(15,4) DEFAULT 0.0000,
  `condition` ENUM('bueno','dañado','defectuoso') DEFAULT 'bueno',
  `notes` TEXT,
  PRIMARY KEY (`id`),
  KEY `idx_receiptitem_receipt` (`purchase_receipt_id`),
  KEY `idx_receiptitem_poitem` (`purchase_order_item_id`),
  KEY `idx_receiptitem_product` (`product_id`),
  KEY `idx_receiptitem_location` (`location_id`),
  CONSTRAINT `fk_receiptitem_receipt` FOREIGN KEY (`purchase_receipt_id`) REFERENCES `purchase_receipts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_receiptitem_poitem` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`),
  CONSTRAINT `fk_receiptitem_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_receiptitem_location` FOREIGN KEY (`location_id`) REFERENCES `almacen_locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. PÓLIZAS CONTABLES
CREATE TABLE IF NOT EXISTS `accounting_entries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `entry_number` VARCHAR(50) NOT NULL,
  `entry_type` ENUM('ingreso','egreso','diario') NOT NULL,
  `entry_date` DATE NOT NULL,
  `reference_type` VARCHAR(50) NULL,
  `reference_id` INT UNSIGNED NULL,
  `reference_number` VARCHAR(50) NULL,
  `description` TEXT NOT NULL,
  `status` ENUM('borrador','autorizada','contabilizada','cancelada') NOT NULL DEFAULT 'borrador',
  `total_debit` DECIMAL(15,4) DEFAULT 0.0000,
  `total_credit` DECIMAL(15,4) DEFAULT 0.0000,
  `notes` TEXT,
  `created_by` INT UNSIGNED,
  `authorized_by` INT UNSIGNED NULL,
  `authorized_at` DATETIME NULL,
  `posted_by` INT UNSIGNED NULL,
  `posted_at` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_entry_number` (`tenant_id`, `entry_number`),
  KEY `idx_entry_tenant` (`tenant_id`),
  KEY `idx_entry_date` (`entry_date`),
  KEY `idx_entry_type` (`entry_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `accounting_entry_lines` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `accounting_entry_id` INT UNSIGNED NOT NULL,
  `account_id` INT UNSIGNED NOT NULL,
  `line_number` INT NOT NULL,
  `description` VARCHAR(255),
  `debit` DECIMAL(15,4) DEFAULT 0.0000,
  `credit` DECIMAL(15,4) DEFAULT 0.0000,
  `reference_type` VARCHAR(50) NULL,
  `reference_id` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entryline_entry` (`accounting_entry_id`),
  KEY `idx_entryline_account` (`account_id`),
  CONSTRAINT `fk_entryline_entry` FOREIGN KEY (`accounting_entry_id`) REFERENCES `accounting_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_entryline_account` FOREIGN KEY (`account_id`) REFERENCES `accounting_accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. AUTORIZACIONES
CREATE TABLE IF NOT EXISTS `authorization_workflows` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `description` TEXT,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_workflow_tenant` (`tenant_id`),
  KEY `idx_workflow_entity` (`entity_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `authorization_workflow_levels` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `workflow_id` INT UNSIGNED NOT NULL,
  `level_number` TINYINT UNSIGNED NOT NULL,
  `level_name` VARCHAR(50) NOT NULL,
  `role_id` INT UNSIGNED NULL,
  `user_id` INT UNSIGNED NULL,
  `amount_from` DECIMAL(15,4) NULL,
  `amount_to` DECIMAL(15,4) NULL,
  `is_required` TINYINT(1) NOT NULL DEFAULT 1,
  `notification_email` VARCHAR(255),
  PRIMARY KEY (`id`),
  KEY `idx_level_workflow` (`workflow_id`),
  KEY `idx_level_role` (`role_id`),
  KEY `idx_level_user` (`user_id`),
  CONSTRAINT `fk_level_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `authorization_workflows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `authorization_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `workflow_id` INT UNSIGNED NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `request_number` VARCHAR(50) NOT NULL,
  `amount` DECIMAL(15,4) NULL,
  `description` TEXT,
  `status` ENUM('pendiente','aprobada','rechazada','cancelada') NOT NULL DEFAULT 'pendiente',
  `current_level` TINYINT UNSIGNED DEFAULT 1,
  `requested_by` INT UNSIGNED,
  `requested_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `completed_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_auth_request` (`tenant_id`, `request_number`),
  KEY `idx_auth_tenant` (`tenant_id`),
  KEY `idx_auth_workflow` (`workflow_id`),
  KEY `idx_auth_entity` (`entity_type`, `entity_id`),
  CONSTRAINT `fk_auth_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `authorization_workflows` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `authorization_approvals` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `authorization_request_id` INT UNSIGNED NOT NULL,
  `level_id` INT UNSIGNED NOT NULL,
  `level_number` TINYINT UNSIGNED NOT NULL,
  `status` ENUM('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `approved_by` INT UNSIGNED NULL,
  `approved_at` DATETIME NULL,
  `comments` TEXT,
  PRIMARY KEY (`id`),
  KEY `idx_approval_request` (`authorization_request_id`),
  KEY `idx_approval_level` (`level_id`),
  CONSTRAINT `fk_approval_request` FOREIGN KEY (`authorization_request_id`) REFERENCES `authorization_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_approval_level` FOREIGN KEY (`level_id`) REFERENCES `authorization_workflow_levels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DATOS DE EJEMPLO
INSERT IGNORE INTO product_categories (tenant_id, code, name, description, level, is_active) VALUES
(1, 'CAT-001', 'Electrónica', 'Productos electrónicos', 0, 1),
(1, 'CAT-002', 'Alimentos', 'Productos alimenticios', 0, 1),
(1, 'CAT-003', 'Papelería', 'Artículos de oficina', 0, 1);

INSERT IGNORE INTO product_brands (tenant_id, code, name, country, is_active) VALUES
(1, 'MRC-001', 'Samsung', 'Corea del Sur', 1),
(1, 'MRC-002', 'LG', 'Corea del Sur', 1),
(1, 'MRC-003', 'Sony', 'Japón', 1);

INSERT IGNORE INTO accounting_accounts (tenant_id, account_code, name, account_type, nature, level, allows_movement, is_active) VALUES
(1, '1.1.1.001', 'Inventarios', 'activo', 'deudora', 3, 1, 1),
(1, '2.1.1.001', 'Proveedores', 'pasivo', 'acreedora', 3, 1, 1),
(1, '5.1.1.001', 'Costo de Ventas', 'egresos', 'deudora', 3, 1, 1),
(1, '4.1.1.001', 'Ventas', 'ingresos', 'acreedora', 3, 1, 1);

INSERT IGNORE INTO price_lists (tenant_id, code, name, list_type, is_default, is_active) VALUES
(1, 'LP-001', 'Precio Público', 'menudeo', 1, 1),
(1, 'LP-002', 'Precio Mayoreo', 'mayoreo', 0, 1),
(1, 'LP-003', 'Precio Distribuidor', 'distribuidor', 0, 1);

INSERT IGNORE INTO authorization_workflows (tenant_id, name, entity_type, description, is_active) VALUES
(1, 'Autorización de Órdenes de Compra', 'orden_compra', 'Flujo de autorización para OC según monto', 1);

INSERT IGNORE INTO authorization_workflow_levels (workflow_id, level_number, level_name, amount_from, amount_to, is_required) VALUES
(1, 1, 'Gerente de Compras', 0.00, 50000.00, 1),
(1, 2, 'Director de Operaciones', 50000.01, 200000.00, 1),
(1, 3, 'Director General', 200000.01, 999999999.99, 1);
