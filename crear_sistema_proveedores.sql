USE base;

-- =====================================================
-- MIGRACIÓN: Mejorar Sistema de Proveedores
-- Fecha: 2025-12-04
-- =====================================================

SET FOREIGN_KEY_CHECKS=0;

-- =====================================================
-- 1. MEJORAR TABLA PROVIDERS
-- =====================================================
-- Verificar y agregar tenant_id si no existe
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'base' AND TABLE_NAME = 'providers' AND COLUMN_NAME = 'tenant_id');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE providers ADD COLUMN tenant_id INT(11) UNSIGNED NOT NULL DEFAULT 1 AFTER id, ADD INDEX idx_providers_tenant (tenant_id)',
    'SELECT "tenant_id ya existe" as info');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;

-- Verificar y agregar currency si no existe
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'base' AND TABLE_NAME = 'providers' AND COLUMN_NAME = 'currency');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE providers ADD COLUMN currency VARCHAR(3) NOT NULL DEFAULT "MXN" AFTER payment_terms',
    'SELECT "currency ya existe" as info');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;

-- Verificar y agregar created_by si no existe
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'base' AND TABLE_NAME = 'providers' AND COLUMN_NAME = 'created_by');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE providers ADD COLUMN created_by INT(11) UNSIGNED NULL AFTER is_active',
    'SELECT "created_by ya existe" as info');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;

-- Verificar y agregar updated_by si no existe
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'base' AND TABLE_NAME = 'providers' AND COLUMN_NAME = 'updated_by');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE providers ADD COLUMN updated_by INT(11) UNSIGNED NULL AFTER created_by',
    'SELECT "updated_by ya existe" as info');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;

-- =====================================================
-- 2. TABLA: PROVIDER_CATEGORIES
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_categories (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT(11) UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tenant (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. TABLA: PROVIDER_BANK_ACCOUNTS
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_bank_accounts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT(11) UNSIGNED NOT NULL,
    bank_name VARCHAR(100) NOT NULL,
    account_number VARCHAR(50) NOT NULL,
    clabe VARCHAR(18) NULL,
    swift_code VARCHAR(11) NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'MXN',
    is_default TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_provider (provider_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. TABLA: PROVIDER_INVENTORY_RECEIPTS
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_inventory_receipts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT(11) UNSIGNED NOT NULL,
    provider_id INT(11) UNSIGNED NOT NULL,
    purchase_order_id INT(11) UNSIGNED NULL COMMENT 'Relación con providers_orders',
    receipt_number VARCHAR(50) NOT NULL UNIQUE,
    receipt_date DATE NOT NULL,
    warehouse_id INT(11) UNSIGNED NOT NULL,
    received_by INT(11) UNSIGNED NOT NULL,
    invoice_number VARCHAR(50) NULL,
    invoice_date DATE NULL,
    status ENUM('draft','received','verified','posted','cancelled') NOT NULL DEFAULT 'draft',
    notes TEXT NULL,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    verified_by INT(11) UNSIGNED NULL,
    verified_at DATETIME NULL,
    posted_by INT(11) UNSIGNED NULL,
    posted_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    INDEX idx_tenant (tenant_id),
    INDEX idx_provider (provider_id),
    INDEX idx_order (purchase_order_id),
    INDEX idx_receipt_number (receipt_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. TABLA: PROVIDER_INVENTORY_RECEIPT_DETAILS
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_inventory_receipt_details (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    receipt_id INT(11) UNSIGNED NOT NULL,
    product_id INT(11) UNSIGNED NOT NULL,
    quantity_ordered DECIMAL(15,4) NOT NULL DEFAULT 0.0000,
    quantity_received DECIMAL(15,4) NOT NULL DEFAULT 0.0000,
    unit_cost DECIMAL(15,4) NOT NULL DEFAULT 0.0000,
    subtotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    lot_number VARCHAR(50) NULL,
    expiration_date DATE NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_receipt (receipt_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. TABLA: PROVIDER_PAYMENTS
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_payments (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT(11) UNSIGNED NOT NULL,
    provider_id INT(11) UNSIGNED NOT NULL,
    payment_number VARCHAR(50) NOT NULL UNIQUE,
    payment_date DATE NOT NULL,
    payment_method ENUM('efectivo','transferencia','cheque','tarjeta','otro') NOT NULL DEFAULT 'transferencia',
    reference_number VARCHAR(100) NULL,
    amount DECIMAL(15,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'MXN',
    exchange_rate DECIMAL(10,4) NOT NULL DEFAULT 1.0000,
    bank_account_id INT(11) UNSIGNED NULL,
    notes TEXT NULL,
    status ENUM('draft','completed','cancelled') NOT NULL DEFAULT 'draft',
    created_by INT(11) UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    INDEX idx_tenant (tenant_id),
    INDEX idx_provider (provider_id),
    INDEX idx_payment_number (payment_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. TABLA: PROVIDER_PAYMENT_ALLOCATIONS
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_payment_allocations (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id INT(11) UNSIGNED NOT NULL,
    invoice_id INT(11) UNSIGNED NULL COMMENT 'providers_bills.id',
    order_id INT(11) UNSIGNED NULL COMMENT 'providers_orders.id',
    amount_allocated DECIMAL(15,2) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_payment (payment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. TABLA: PROVIDER_LOGS
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_logs (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT(11) UNSIGNED NOT NULL,
    provider_id INT(11) UNSIGNED NOT NULL,
    user_id INT(11) UNSIGNED NULL,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NULL COMMENT 'provider, order, payment, receipt, etc',
    entity_id INT(11) UNSIGNED NULL,
    description TEXT NULL,
    old_values TEXT NULL,
    new_values TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tenant (tenant_id),
    INDEX idx_provider (provider_id),
    INDEX idx_user (user_id),
    INDEX idx_entity (entity_type, entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. INSERTAR PERMISOS
-- =====================================================
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at) VALUES
('proveedores', 'view', 'Ver Proveedores', 'Permite ver el listado y detalles de proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'create', 'Crear Proveedores', 'Permite crear nuevos proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'edit', 'Editar Proveedores', 'Permite editar información de proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'delete', 'Eliminar Proveedores', 'Permite eliminar proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'orders_view', 'Ver Órdenes de Compra', 'Permite ver órdenes de compra', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'orders_create', 'Crear Órdenes de Compra', 'Permite crear órdenes de compra', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'orders_authorize', 'Autorizar Órdenes', 'Permite autorizar órdenes de compra', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'receipts_view', 'Ver Recepciones', 'Permite ver recepciones de mercancía', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'receipts_create', 'Crear Recepciones', 'Permite crear recepciones de mercancía', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'receipts_verify', 'Verificar Recepciones', 'Permite verificar recepciones de mercancía', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'payments_view', 'Ver Pagos', 'Permite ver pagos a proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'payments_create', 'Crear Pagos', 'Permite crear pagos a proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('proveedores', 'reports', 'Reportes', 'Permite ver reportes de proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE updated_at = UNIX_TIMESTAMP();

-- =====================================================
-- 10. DATOS INICIALES - CATEGORÍAS
-- =====================================================
INSERT INTO provider_categories (tenant_id, name, description, is_active, created_at) VALUES
(1, 'Materias Primas', 'Proveedores de materias primas e insumos', 1, NOW()),
(1, 'Servicios', 'Proveedores de servicios', 1, NOW()),
(1, 'Mercancías', 'Proveedores de productos terminados', 1, NOW()),
(1, 'Equipos y Tecnología', 'Proveedores de equipos y tecnología', 1, NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

SET FOREIGN_KEY_CHECKS=1;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
SELECT 
    'Tablas creadas:' as '';

SELECT 
    TABLE_NAME as 'Tabla',
    TABLE_ROWS as 'Registros'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'base' 
    AND TABLE_NAME LIKE 'provider%'
ORDER BY TABLE_NAME;

SELECT 
    'Permisos creados:' as '';

SELECT 
    module as 'Módulo',
    action as 'Acción',
    name as 'Nombre'
FROM permissions 
WHERE module = 'proveedores'
ORDER BY action;
