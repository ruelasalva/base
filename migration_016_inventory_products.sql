-- Migración 016: Crear Sistema de Productos de Inventario
-- Ejecutar en la base de datos del tenant actual

-- =====================================================
-- 1. TABLA: INVENTORY_PRODUCT_CATEGORIES (Categorías)
-- =====================================================
CREATE TABLE IF NOT EXISTS `inventory_product_categories` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` INT(11) UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `parent_id` INT(11) UNSIGNED NULL COMMENT 'Categoría padre para subcategorías',
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_inv_product_categories_tenant` (`tenant_id`),
    INDEX `idx_inv_product_categories_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. TABLA: INVENTORY_PRODUCTS (Productos)
-- =====================================================
CREATE TABLE IF NOT EXISTS `inventory_products` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` INT(11) UNSIGNED NOT NULL,
    `code` VARCHAR(50) NOT NULL COMMENT 'Código único del producto (SKU)',
    `barcode` VARCHAR(50) NULL COMMENT 'Código de barras',
    `name` VARCHAR(255) NOT NULL COMMENT 'Nombre del producto',
    `description` TEXT NULL COMMENT 'Descripción detallada',
    `category_id` INT(11) UNSIGNED NULL COMMENT 'Categoría del producto',
    `unit_of_measure` VARCHAR(20) NOT NULL DEFAULT 'PZA' COMMENT 'Unidad de medida (PZA, KG, LT, etc.)',
    `unit_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio de venta unitario',
    `cost` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Costo unitario',
    `stock` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Cantidad en stock',
    `min_stock` DECIMAL(15,2) NULL COMMENT 'Stock mínimo (alerta de reorden)',
    `max_stock` DECIMAL(15,2) NULL COMMENT 'Stock máximo',
    `tax_rate` DECIMAL(5,2) NOT NULL DEFAULT 16.00 COMMENT 'Tasa de IVA (%)',
    `image` VARCHAR(255) NULL COMMENT 'Ruta de imagen del producto',
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `is_service` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1 = Servicio, 0 = Producto físico',
    `created_by` INT(11) UNSIGNED NULL,
    `updated_by` INT(11) UNSIGNED NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NULL,
    `deleted_at` DATETIME NULL COMMENT 'Soft delete',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_inv_products_tenant_code` (`tenant_id`, `code`),
    INDEX `idx_inv_products_tenant` (`tenant_id`),
    INDEX `idx_inv_products_code` (`code`),
    INDEX `idx_inv_products_barcode` (`barcode`),
    INDEX `idx_inv_products_category` (`category_id`),
    INDEX `idx_inv_products_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. TABLA: INVENTORY_PRODUCT_LOGS (Logs de productos)
-- =====================================================
CREATE TABLE IF NOT EXISTS `inventory_product_logs` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` INT(11) UNSIGNED NOT NULL,
    `product_id` INT(11) UNSIGNED NOT NULL,
    `user_id` INT(11) UNSIGNED NULL,
    `action` ENUM('created','updated','deleted','stock_adjusted','price_changed') NOT NULL,
    `description` TEXT NULL COMMENT 'Descripción del cambio',
    `old_values` JSON NULL COMMENT 'Valores anteriores (JSON)',
    `new_values` JSON NULL COMMENT 'Valores nuevos (JSON)',
    `ip_address` VARCHAR(45) NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_inv_product_logs_product` (`product_id`),
    INDEX `idx_inv_product_logs_user` (`user_id`),
    INDEX `idx_inv_product_logs_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. DATOS INICIALES: Categorías de ejemplo
-- =====================================================
INSERT INTO `inventory_product_categories` (`tenant_id`, `name`, `description`, `is_active`, `created_at`) VALUES
(1, 'General', 'Categoría general de productos', 1, NOW()),
(1, 'Materia Prima', 'Materias primas para producción', 1, NOW()),
(1, 'Producto Terminado', 'Productos terminados listos para venta', 1, NOW()),
(1, 'Servicios', 'Servicios prestados', 1, NOW()),
(1, 'Consumibles', 'Productos de consumo regular', 1, NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- =====================================================
-- 5. PERMISOS: Insertar permisos para el módulo
-- =====================================================

-- Verificar si existe la tabla permissions
-- Si no existe, estos INSERT fallarán pero el resto de la migración continuará

INSERT IGNORE INTO `permissions` (`module`, `action`, `description`, `is_active`) VALUES
('inventory_products', 'view', 'Ver productos de inventario', 1),
('inventory_products', 'create', 'Crear productos de inventario', 1),
('inventory_products', 'edit', 'Editar productos de inventario', 1),
('inventory_products', 'delete', 'Eliminar productos de inventario', 1);

-- =====================================================
-- COMPLETADO
-- =====================================================
-- Tablas creadas:
-- - inventory_product_categories
-- - inventory_products  
-- - inventory_product_logs
-- 
-- Permisos agregados:
-- - inventory_products.view
-- - inventory_products.create
-- - inventory_products.edit
-- - inventory_products.delete
