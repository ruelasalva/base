-- ===============================================
-- MÓDULO PROVEEDORES V1.0 - Tablas Complementarias
-- ===============================================

-- Tabla para tokens de confirmación de email
CREATE TABLE IF NOT EXISTS providers_email_confirmations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL,
    is_confirmed TINYINT(1) DEFAULT 0,
    confirmed_at DATETIME NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tokens de confirmación de email de proveedores';

-- Tabla para control de intentos de login
CREATE TABLE IF NOT EXISTS providers_login_attempts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempts INT DEFAULT 1,
    is_blocked TINYINT(1) DEFAULT 0,
    blocked_until DATETIME NULL,
    last_attempt_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email_ip (email, ip_address),
    INDEX idx_blocked_until (blocked_until)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Control de intentos fallidos de login';

-- Tabla de configuración de facturación y pagos (centralizada por tenant)
CREATE TABLE IF NOT EXISTS providers_billing_config (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    invoice_receive_days VARCHAR(50) DEFAULT '1,2,3,4,5' COMMENT 'Días válidos para recibir facturas (1=Lun, 7=Dom)',
    invoice_receive_limit_time TIME DEFAULT '16:00:00' COMMENT 'Hora límite para recepción de facturas',
    payment_terms_days INT DEFAULT 30 COMMENT 'Días hábiles mínimos para pago',
    payment_days VARCHAR(50) DEFAULT '5' COMMENT 'Días de la semana para pagar (1=Lun, 5=Vie)',
    holidays JSON NULL COMMENT 'Array de fechas feriadas ["2025-01-01", "2025-12-25"]',
    auto_generate_receipt TINYINT(1) DEFAULT 1 COMMENT 'Generar contrarecibo automáticamente',
    require_po_match TINYINT(1) DEFAULT 1 COMMENT 'Requiere match con OC',
    max_amount_without_po DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Monto máximo sin OC',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_tenant (tenant_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuración de facturación y pagos por tenant';

-- Insertar configuración por defecto para tenant 1
INSERT IGNORE INTO providers_billing_config (tenant_id, invoice_receive_days, invoice_receive_limit_time, payment_terms_days, payment_days, holidays, auto_generate_receipt)
VALUES (1, '1,2,3,4,5', '16:00:00', 30, '5', '["2025-01-01","2025-02-03","2025-03-17","2025-05-01","2025-09-16","2025-11-17","2025-12-25"]', 1);

-- Agregar campos faltantes a providers_bills (si no existen)
ALTER TABLE providers_bills 
ADD COLUMN IF NOT EXISTS uuid VARCHAR(36) NULL COMMENT 'UUID del CFDI' AFTER id,
ADD COLUMN IF NOT EXISTS xml_content LONGTEXT NULL COMMENT 'XML completo del CFDI' AFTER total,
ADD COLUMN IF NOT EXISTS xml_hash VARCHAR(64) NULL COMMENT 'Hash SHA256 del XML' AFTER xml_content,
ADD COLUMN IF NOT EXISTS sat_status ENUM('vigente', 'cancelado', 'no_encontrado') DEFAULT 'vigente' AFTER status,
ADD COLUMN IF NOT EXISTS sat_validated_at DATETIME NULL COMMENT 'Fecha de última validación SAT' AFTER sat_status,
ADD COLUMN IF NOT EXISTS upload_ip VARCHAR(45) NULL COMMENT 'IP desde donde se subió' AFTER sat_validated_at,
ADD INDEX idx_uuid (uuid),
ADD INDEX idx_sat_status (sat_status);

-- Agregar campos faltantes a providers_receipts
ALTER TABLE providers_receipts
ADD COLUMN IF NOT EXISTS official_receipt_date DATE NULL COMMENT 'Fecha oficial de recepción calculada' AFTER bill_id,
ADD COLUMN IF NOT EXISTS programmed_payment_date DATE NULL COMMENT 'Fecha programada de pago calculada' AFTER official_receipt_date,
ADD COLUMN IF NOT EXISTS calculation_notes TEXT NULL COMMENT 'Notas del cálculo automático' AFTER programmed_payment_date;

-- Agregar campo de suspensión a providers
ALTER TABLE providers
ADD COLUMN IF NOT EXISTS is_suspended TINYINT(1) DEFAULT 1 COMMENT 'Cuenta suspendida hasta validación admin' AFTER status,
ADD COLUMN IF NOT EXISTS suspended_reason VARCHAR(255) NULL COMMENT 'Razón de suspensión' AFTER is_suspended,
ADD COLUMN IF NOT EXISTS suspended_at DATETIME NULL AFTER suspended_reason,
ADD COLUMN IF NOT EXISTS activated_at DATETIME NULL COMMENT 'Fecha de activación por admin' AFTER suspended_at,
ADD COLUMN IF NOT EXISTS activated_by INT UNSIGNED NULL COMMENT 'Admin que activó' AFTER activated_at;

-- Tabla de logs de proveedores (compatible con estructura existente)
CREATE TABLE IF NOT EXISTS providers_action_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    provider_id INT UNSIGNED NULL COMMENT 'ID del proveedor (null si es acción de sistema)',
    user_id INT UNSIGNED NULL COMMENT 'ID del admin (null si es proveedor)',
    entity VARCHAR(50) NOT NULL COMMENT 'Tabla afectada: providers, providers_bills, providers_receipts, etc',
    entity_id INT UNSIGNED NULL COMMENT 'ID del registro afectado',
    action VARCHAR(50) NOT NULL COMMENT 'Acción: create, update, delete, login, upload, validate, etc',
    description TEXT NULL COMMENT 'Descripción detallada de la acción',
    old_data JSON NULL COMMENT 'Datos anteriores',
    new_data JSON NULL COMMENT 'Datos nuevos',
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE SET NULL,
    INDEX idx_provider (provider_id),
    INDEX idx_entity (entity, entity_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de acciones de proveedores';

-- Permisos para módulo de proveedores
INSERT IGNORE INTO permissions (module, action, name, description) VALUES
('providers', 'view', 'Ver Proveedores', 'Acceso al módulo de proveedores'),
('providers', 'create', 'Crear Proveedor', 'Registrar nuevos proveedores'),
('providers', 'edit', 'Editar Proveedor', 'Modificar datos de proveedores'),
('providers', 'validate', 'Validar Proveedor', 'Activar/Suspender proveedores'),
('providers', 'view_bills', 'Ver Facturas', 'Ver facturas de proveedores'),
('providers', 'validate_bills', 'Validar Facturas', 'Aceptar/Rechazar facturas'),
('providers', 'manage_receipts', 'Gestionar Contrarecibos', 'Crear y editar contrarecibos'),
('providers', 'manage_payments', 'Gestionar Pagos', 'Registrar pagos a proveedores'),
('providers', 'config', 'Configurar Sistema', 'Acceso a configuración de facturación/pagos');

-- Asignar permisos al rol admin (id=1)
INSERT IGNORE INTO role_permissions (role_id, permission_id) 
SELECT 1, id FROM permissions WHERE module = 'providers';
