-- =====================================================
-- SISTEMA COMPLETO DE INVENTARIO Y COMPRAS
-- Incluye: Almacenes, Categorías, Marcas, Cuentas Contables,
-- Productos, Listas de Precios, Órdenes de Compra, Recepciones,
-- Facturas, Pólizas, Autorizaciones
-- =====================================================

USE base;

-- =====================================================
-- 1. CATEGORÍAS DE PRODUCTOS (Estructura jerárquica)
-- =====================================================
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `parent_id` INT UNSIGNED NULL,
  `code` VARCHAR(20) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `icon` VARCHAR(50) DEFAULT 'fa-folder',
  `level` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `path` VARCHAR(255) NULL COMMENT 'Ruta completa de IDs: 1/3/5',
  `order_position` INT UNSIGNED DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` INT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_category_code` (`tenant_id`, `code`),
  KEY `idx_category_parent` (`parent_id`),
  KEY `idx_category_tenant` (`tenant_id`),
  KEY `idx_category_active` (`is_active`),
  CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. MARCAS / FABRICANTES
-- =====================================================
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
  KEY `idx_brand_tenant` (`tenant_id`),
  KEY `idx_brand_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. CUENTAS CONTABLES (Catálogo SAT)
-- =====================================================
CREATE TABLE IF NOT EXISTS `accounting_accounts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `parent_id` INT UNSIGNED NULL,
  `account_code` VARCHAR(20) NOT NULL COMMENT 'Código contable: 1.1.1.001',
  `sat_code` VARCHAR(20) NULL COMMENT 'Código del catálogo SAT',
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `account_type` ENUM('activo','pasivo','capital','ingresos','egresos','resultado') NOT NULL,
  `account_subtype` VARCHAR(50) COMMENT 'circulante, fijo, diferido, etc',
  `nature` ENUM('deudora','acreedora') NOT NULL,
  `level` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `allows_movement` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Si permite captura o es de agrupación',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` INT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_account_code` (`tenant_id`, `account_code`),
  KEY `idx_account_parent` (`parent_id`),
  KEY `idx_account_tenant` (`tenant_id`),
  KEY `idx_account_type` (`account_type`),
  KEY `idx_account_sat` (`sat_code`),
  CONSTRAINT `fk_account_parent` FOREIGN KEY (`parent_id`) REFERENCES `accounting_accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. PRODUCTOS
-- =====================================================
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  `brand_id` INT UNSIGNED NULL,
  `sku` VARCHAR(50) NOT NULL,
  `barcode` VARCHAR(50) NULL,
  `name` VARCHAR(200) NOT NULL,
  `description` TEXT,
  `short_description` VARCHAR(500),
  `product_type` ENUM('producto','servicio','combo') NOT NULL DEFAULT 'producto',
  `unit_of_measure` VARCHAR(20) NOT NULL DEFAULT 'pieza' COMMENT 'pieza, kg, m, litro, etc',
  `sat_unit_code` VARCHAR(10) NULL COMMENT 'Clave de unidad SAT',
  `sat_product_code` VARCHAR(10) NULL COMMENT 'Clave de producto/servicio SAT',
  `cost_price` DECIMAL(15,4) DEFAULT 0.0000 COMMENT 'Precio de costo promedio',
  `standard_price` DECIMAL(15,4) DEFAULT 0.0000 COMMENT 'Precio estándar de venta',
  `min_price` DECIMAL(15,4) DEFAULT 0.0000 COMMENT 'Precio mínimo permitido',
  `tax_rate` DECIMAL(5,2) DEFAULT 16.00 COMMENT 'IVA u otro impuesto',
  `weight_kg` DECIMAL(10,3) NULL,
  `dimensions` VARCHAR(50) NULL COMMENT 'largo x ancho x alto',
  `image_url` VARCHAR(255),
  `images` TEXT COMMENT 'JSON con múltiples imágenes',
  `min_stock` INT DEFAULT 0 COMMENT 'Stock mínimo para alerta',
  `max_stock` INT DEFAULT 0 COMMENT 'Stock máximo',
  `reorder_point` INT DEFAULT 0 COMMENT 'Punto de reorden',
  `reorder_quantity` INT DEFAULT 0 COMMENT 'Cantidad a reordenar',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_for_sale` TINYINT(1) NOT NULL DEFAULT 1,
  `is_for_purchase` TINYINT(1) NOT NULL DEFAULT 1,
  `notes` TEXT,
  `account_inventory_id` INT UNSIGNED NULL COMMENT 'Cuenta de inventario',
  `account_cost_id` INT UNSIGNED NULL COMMENT 'Cuenta de costo de venta',
  `account_income_id` INT UNSIGNED NULL COMMENT 'Cuenta de ingreso',
  `created_by` INT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_sku` (`tenant_id`, `sku`),
  KEY `idx_product_tenant` (`tenant_id`),
  KEY `idx_product_category` (`category_id`),
  KEY `idx_product_brand` (`brand_id`),
  KEY `idx_product_barcode` (`barcode`),
  KEY `idx_product_active` (`is_active`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`),
  CONSTRAINT `fk_product_brand` FOREIGN KEY (`brand_id`) REFERENCES `product_brands` (`id`),
  CONSTRAINT `fk_product_account_inv` FOREIGN KEY (`account_inventory_id`) REFERENCES `accounting_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_product_account_cost` FOREIGN KEY (`account_cost_id`) REFERENCES `accounting_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_product_account_income` FOREIGN KEY (`account_income_id`) REFERENCES `accounting_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. LISTAS DE PRECIOS
-- =====================================================
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
  KEY `idx_pricelist_tenant` (`tenant_id`),
  KEY `idx_pricelist_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `price_list_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `price_list_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `price` DECIMAL(15,4) NOT NULL,
  `min_quantity` INT DEFAULT 1 COMMENT 'Cantidad mínima para este precio',
  `discount_percent` DECIMAL(5,2) DEFAULT 0.00,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pricelist_product` (`price_list_id`, `product_id`, `min_quantity`),
  KEY `idx_priceitem_product` (`product_id`),
  CONSTRAINT `fk_priceitem_list` FOREIGN KEY (`price_list_id`) REFERENCES `price_lists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_priceitem_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. INVENTARIO (Stock por almacén y ubicación)
-- =====================================================
CREATE TABLE IF NOT EXISTS `inventory_stock` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `almacen_id` INT UNSIGNED NOT NULL,
  `location_id` INT UNSIGNED NULL,
  `quantity` DECIMAL(15,4) NOT NULL DEFAULT 0.0000,
  `reserved_quantity` DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'Cantidad reservada en pedidos',
  `available_quantity` DECIMAL(15,4) GENERATED ALWAYS AS (`quantity` - `reserved_quantity`) STORED,
  `cost_average` DECIMAL(15,4) DEFAULT 0.0000 COMMENT 'Costo promedio',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. MOVIMIENTOS DE INVENTARIO (Kardex)
-- =====================================================
CREATE TABLE IF NOT EXISTS `inventory_movements` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `almacen_id` INT UNSIGNED NOT NULL,
  `location_id` INT UNSIGNED NULL,
  `movement_type` ENUM('entrada','salida','ajuste','transferencia','devolucion') NOT NULL,
  `movement_subtype` VARCHAR(50) COMMENT 'compra, venta, merma, robo, recepcion, etc',
  `reference_type` VARCHAR(50) COMMENT 'orden_compra, factura, ajuste, etc',
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
  KEY `idx_movement_reference` (`reference_type`, `reference_id`),
  KEY `idx_movement_date` (`created_at`),
  CONSTRAINT `fk_movement_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_movement_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. ÓRDENES DE COMPRA
-- =====================================================
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `order_number` VARCHAR(50) NOT NULL,
  `provider_id` INT UNSIGNED NOT NULL,
  `almacen_id` INT UNSIGNED NOT NULL COMMENT 'Almacén destino',
  `order_date` DATE NOT NULL,
  `expected_date` DATE NULL,
  `currency` VARCHAR(3) DEFAULT 'MXN',
  `exchange_rate` DECIMAL(10,6) DEFAULT 1.000000,
  `status` ENUM('borrador','enviada','autorizada','parcial','recibida','cancelada') NOT NULL DEFAULT 'borrador',
  `subtotal` DECIMAL(15,4) DEFAULT 0.0000,
  `tax_amount` DECIMAL(15,4) DEFAULT 0.0000,
  `total` DECIMAL(15,4) DEFAULT 0.0000,
  `notes` TEXT,
  `internal_notes` TEXT COMMENT 'Notas internas no visibles al proveedor',
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
  KEY `idx_po_date` (`order_date`),
  CONSTRAINT `fk_po_provider` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`),
  CONSTRAINT `fk_po_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` DECIMAL(15,4) NOT NULL,
  `quantity_received` DECIMAL(15,4) DEFAULT 0.0000,
  `quantity_pending` DECIMAL(15,4) GENERATED ALWAYS AS (`quantity` - `quantity_received`) STORED,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. RECEPCIONES DE COMPRA
-- =====================================================
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
  `discrepancy_notes` TEXT COMMENT 'Notas sobre diferencias encontradas',
  `created_by` INT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_receipt_number` (`tenant_id`, `receipt_number`),
  KEY `idx_receipt_tenant` (`tenant_id`),
  KEY `idx_receipt_po` (`purchase_order_id`),
  KEY `idx_receipt_almacen` (`almacen_id`),
  KEY `idx_receipt_date` (`receipt_date`),
  CONSTRAINT `fk_receipt_po` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  CONSTRAINT `fk_receipt_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `purchase_receipt_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_receipt_id` INT UNSIGNED NOT NULL,
  `purchase_order_item_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `location_id` INT UNSIGNED NULL,
  `quantity_expected` DECIMAL(15,4) NOT NULL,
  `quantity_received` DECIMAL(15,4) NOT NULL,
  `quantity_difference` DECIMAL(15,4) GENERATED ALWAYS AS (`quantity_received` - `quantity_expected`) STORED,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. PÓLIZAS CONTABLES
-- =====================================================
CREATE TABLE IF NOT EXISTS `accounting_entries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `entry_number` VARCHAR(50) NOT NULL,
  `entry_type` ENUM('ingreso','egreso','diario') NOT NULL,
  `entry_date` DATE NOT NULL,
  `reference_type` VARCHAR(50) NULL COMMENT 'orden_compra, factura, pago, etc',
  `reference_id` INT UNSIGNED NULL,
  `reference_number` VARCHAR(50) NULL,
  `description` TEXT NOT NULL,
  `status` ENUM('borrador','autorizada','contabilizada','cancelada') NOT NULL DEFAULT 'borrador',
  `total_debit` DECIMAL(15,4) DEFAULT 0.0000,
  `total_credit` DECIMAL(15,4) DEFAULT 0.0000,
  `is_balanced` TINYINT(1) GENERATED ALWAYS AS (`total_debit` = `total_credit`) STORED,
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
  KEY `idx_entry_type` (`entry_type`),
  KEY `idx_entry_status` (`status`),
  KEY `idx_entry_reference` (`reference_type`, `reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. SISTEMA DE AUTORIZACIONES MULTINIVEL
-- =====================================================
CREATE TABLE IF NOT EXISTS `authorization_workflows` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL COMMENT 'orden_compra, factura, pago, ajuste, etc',
  `description` TEXT,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_workflow_tenant` (`tenant_id`),
  KEY `idx_workflow_entity` (`entity_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `authorization_workflow_levels` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `workflow_id` INT UNSIGNED NOT NULL,
  `level_number` TINYINT UNSIGNED NOT NULL,
  `level_name` VARCHAR(50) NOT NULL,
  `role_id` INT UNSIGNED NULL COMMENT 'Rol requerido para autorizar',
  `user_id` INT UNSIGNED NULL COMMENT 'Usuario específico (opcional)',
  `amount_from` DECIMAL(15,4) NULL COMMENT 'Monto mínimo para este nivel',
  `amount_to` DECIMAL(15,4) NULL COMMENT 'Monto máximo para este nivel',
  `is_required` TINYINT(1) NOT NULL DEFAULT 1,
  `notification_email` VARCHAR(255),
  PRIMARY KEY (`id`),
  KEY `idx_level_workflow` (`workflow_id`),
  KEY `idx_level_role` (`role_id`),
  KEY `idx_level_user` (`user_id`),
  CONSTRAINT `fk_level_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `authorization_workflows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_auth_status` (`status`),
  CONSTRAINT `fk_auth_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `authorization_workflows` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_approval_status` (`status`),
  CONSTRAINT `fk_approval_request` FOREIGN KEY (`authorization_request_id`) REFERENCES `authorization_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_approval_level` FOREIGN KEY (`level_id`) REFERENCES `authorization_workflow_levels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS DE EJEMPLO
-- =====================================================

-- Categorías
INSERT INTO `product_categories` (`tenant_id`, `code`, `name`, `description`, `level`, `is_active`, `created_at`) VALUES
(1, 'CAT-001', 'Electrónica', 'Productos electrónicos y tecnología', 0, 1, NOW()),
(1, 'CAT-002', 'Alimentos', 'Productos alimenticios', 0, 1, NOW()),
(1, 'CAT-003', 'Papelería', 'Artículos de oficina y papelería', 0, 1, NOW());

-- Marcas
INSERT INTO `product_brands` (`tenant_id`, `code`, `name`, `country`, `is_active`, `created_at`) VALUES
(1, 'MRC-001', 'Samsung', 'Corea del Sur', 1, NOW()),
(1, 'MRC-002', 'LG', 'Corea del Sur', 1, NOW()),
(1, 'MRC-003', 'Sony', 'Japón', 1, NOW());

-- Cuentas Contables (básicas)
INSERT INTO `accounting_accounts` (`tenant_id`, `account_code`, `name`, `account_type`, `nature`, `level`, `allows_movement`, `is_active`) VALUES
(1, '1.1.1.001', 'Inventarios', 'activo', 'deudora', 3, 1, 1),
(1, '2.1.1.001', 'Proveedores', 'pasivo', 'acreedora', 3, 1, 1),
(1, '5.1.1.001', 'Costo de Ventas', 'egresos', 'deudora', 3, 1, 1),
(1, '4.1.1.001', 'Ventas', 'ingresos', 'acreedora', 3, 1, 1);

-- Listas de Precios
INSERT INTO `price_lists` (`tenant_id`, `code`, `name`, `list_type`, `is_default`, `is_active`, `created_at`) VALUES
(1, 'LP-001', 'Precio Público', 'menudeo', 1, 1, NOW()),
(1, 'LP-002', 'Precio Mayoreo', 'mayoreo', 0, 1, NOW()),
(1, 'LP-003', 'Precio Distribuidor', 'distribuidor', 0, 1, NOW());

-- Workflow de Autorización para Órdenes de Compra
INSERT INTO `authorization_workflows` (`tenant_id`, `name`, `entity_type`, `description`, `is_active`) VALUES
(1, 'Autorización de Órdenes de Compra', 'orden_compra', 'Flujo de autorización para órdenes de compra según monto', 1);

INSERT INTO `authorization_workflow_levels` (`workflow_id`, `level_number`, `level_name`, `amount_from`, `amount_to`, `is_required`) VALUES
(1, 1, 'Gerente de Compras', 0.00, 50000.00, 1),
(1, 2, 'Director de Operaciones', 50000.01, 200000.00, 1),
(1, 3, 'Director General', 200000.01, NULL, 1);

-- =====================================================
-- VISTAS ÚTILES
-- =====================================================

-- Vista de stock disponible por producto
CREATE OR REPLACE VIEW `v_product_stock` AS
SELECT 
    p.id AS product_id,
    p.tenant_id,
    p.sku,
    p.name AS product_name,
    p.unit_of_measure,
    a.id AS almacen_id,
    a.name AS almacen_name,
    COALESCE(SUM(s.quantity), 0) AS total_quantity,
    COALESCE(SUM(s.reserved_quantity), 0) AS total_reserved,
    COALESCE(SUM(s.available_quantity), 0) AS total_available,
    COALESCE(AVG(s.cost_average), 0) AS avg_cost
FROM products p
CROSS JOIN almacenes a ON p.tenant_id = a.tenant_id
LEFT JOIN inventory_stock s ON s.product_id = p.id AND s.almacen_id = a.id
WHERE p.is_active = 1 AND a.is_active = 1
GROUP BY p.id, p.tenant_id, p.sku, p.name, p.unit_of_measure, a.id, a.name;

-- Vista de órdenes de compra pendientes
CREATE OR REPLACE VIEW `v_pending_purchase_orders` AS
SELECT 
    po.id,
    po.tenant_id,
    po.order_number,
    po.order_date,
    po.expected_date,
    po.status,
    po.total,
    prov.name AS provider_name,
    a.name AS almacen_name,
    COUNT(poi.id) AS total_items,
    SUM(poi.quantity_pending) AS total_pending_quantity
FROM purchase_orders po
JOIN providers prov ON po.provider_id = prov.id
JOIN almacenes a ON po.almacen_id = a.id
LEFT JOIN purchase_order_items poi ON poi.purchase_order_id = po.id
WHERE po.status IN ('enviada', 'autorizada', 'parcial')
GROUP BY po.id;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
