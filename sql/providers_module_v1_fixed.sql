-- ================================================
-- MÓDULO DE PROVEEDORES V1.0 - INFRAESTRUCTURA
-- Base de datos para seguridad, configuración y trazabilidad
-- ================================================

USE base;

-- ================================================
-- 1. TABLA DE CONFIRMACIONES DE EMAIL
-- ================================================
CREATE TABLE IF NOT EXISTS providers_email_confirmations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT UNSIGNED NOT NULL COMMENT 'FK a providers',
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE COMMENT 'Token único para confirmar email',
    expires_at DATETIME NOT NULL COMMENT 'Fecha de expiración del token (24h)',
    confirmed_at DATETIME NULL,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_provider (provider_id),
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Tokens de confirmación de email para proveedores';

-- ================================================
-- 2. TABLA DE INTENTOS DE LOGIN
-- ================================================
CREATE TABLE IF NOT EXISTS providers_login_attempts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempt_count INT UNSIGNED DEFAULT 1,
    blocked_until DATETIME NULL COMMENT 'Bloqueado hasta esta fecha (NULL = no bloqueado)',
    last_attempt_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email_ip (email, ip_address),
    INDEX idx_email (email),
    INDEX idx_blocked (blocked_until)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Control de intentos fallidos de login con bloqueo temporal';

-- ================================================
-- 3. TABLA DE CONFIGURACIÓN DE FACTURACIÓN/PAGO
-- ================================================
CREATE TABLE IF NOT EXISTS providers_billing_config (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Multi-tenant support',
    invoice_receive_days VARCHAR(20) DEFAULT '1,2,3,4,5' COMMENT 'Días válidos para recibir facturas (CSV: 1=Lun, 7=Dom)',
    invoice_receive_limit_time TIME DEFAULT '14:00:00' COMMENT 'Hora límite para recibir facturas el mismo día',
    payment_terms_days INT UNSIGNED DEFAULT 30 COMMENT 'Días de crédito (plazo de pago)',
    payment_days VARCHAR(20) DEFAULT '5' COMMENT 'Días de pago permitidos (CSV: 5=Viernes)',
    holidays JSON NULL COMMENT 'Array de fechas festivas ["2024-12-25","2025-01-01"]',
    auto_generate_receipt TINYINT(1) DEFAULT 1 COMMENT 'Generar contrarecibo automáticamente',
    require_purchase_order TINYINT(1) DEFAULT 0 COMMENT 'Requerir OC para validar factura',
    max_amount_without_po DECIMAL(12,2) DEFAULT 5000.00 COMMENT 'Monto máximo sin OC',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_tenant (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Configuración de parámetros de facturación y pago por tenant';

-- Insertar configuración por defecto para tenant 1
INSERT INTO providers_billing_config (tenant_id, holidays) VALUES 
(1, JSON_ARRAY('2024-12-25', '2025-01-01', '2025-05-01', '2025-09-16', '2025-12-25'))
ON DUPLICATE KEY UPDATE tenant_id=tenant_id;

-- ================================================
-- 4. MODIFICACIONES A TABLAS EXISTENTES
-- ================================================

-- providers_bills ya tiene uuid, xml_content, xml_hash, sat_status, sat_validated_at, upload_ip ✅
-- Solo comentamos para referencia:
-- ALTER TABLE providers_bills ADD COLUMN uuid VARCHAR(250) NOT NULL;
-- ALTER TABLE providers_bills ADD COLUMN xml_content LONGTEXT;
-- ALTER TABLE providers_bills ADD COLUMN xml_hash VARCHAR(64);
-- ALTER TABLE providers_bills ADD COLUMN sat_status ENUM('vigente','cancelado','no_encontrado') DEFAULT 'vigente';
-- ALTER TABLE providers_bills ADD COLUMN sat_validated_at DATETIME;
-- ALTER TABLE providers_bills ADD COLUMN upload_ip VARCHAR(45);

-- Agregar campos de cálculo automático a providers_receipts (usar INT(11) para timestamps como en tabla original)
ALTER TABLE providers_receipts
ADD COLUMN IF NOT EXISTS official_receipt_date INT(11) NULL COMMENT 'Fecha oficial de recepción calculada (timestamp)' AFTER receipt_date,
ADD COLUMN IF NOT EXISTS programmed_payment_date INT(11) NULL COMMENT 'Fecha programada de pago calculada (timestamp)' AFTER payment_date,
ADD COLUMN IF NOT EXISTS calculation_notes TEXT NULL COMMENT 'Notas del cálculo automático (días hábiles, festivos aplicados)';

-- Agregar campos de suspensión/activación a providers
ALTER TABLE providers
ADD COLUMN IF NOT EXISTS is_suspended TINYINT(1) DEFAULT 1 COMMENT 'Cuenta suspendida hasta validación admin (1=suspendida, 0=activa)',
ADD COLUMN IF NOT EXISTS suspended_reason VARCHAR(255) NULL COMMENT 'Razón de suspensión',
ADD COLUMN IF NOT EXISTS suspended_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS activated_at DATETIME NULL COMMENT 'Fecha de activación por admin',
ADD COLUMN IF NOT EXISTS activated_by INT UNSIGNED NULL COMMENT 'ID del usuario admin que activó';

-- ================================================
-- 5. TABLA DE LOGS DE ACCIONES (TRAZABILIDAD)
-- ================================================
CREATE TABLE IF NOT EXISTS providers_action_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL DEFAULT 1,
    provider_id INT UNSIGNED NULL COMMENT 'FK a providers (NULL para acciones de sistema)',
    user_id INT UNSIGNED NULL COMMENT 'Usuario admin que realizó la acción',
    entity VARCHAR(50) NOT NULL COMMENT 'Entidad afectada (providers, providers_bills, providers_receipts)',
    entity_id INT UNSIGNED NULL COMMENT 'ID del registro afectado',
    action VARCHAR(50) NOT NULL COMMENT 'Acción realizada (register, login, upload, validate, approve, suspend, etc.)',
    description TEXT NULL COMMENT 'Descripción legible de la acción',
    old_data JSON NULL COMMENT 'Datos anteriores (para auditoría)',
    new_data JSON NULL COMMENT 'Datos nuevos',
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_provider (provider_id),
    INDEX idx_entity (entity, entity_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Registro completo de acciones para trazabilidad y auditoría';

-- ================================================
-- 6. PERMISOS DEL MÓDULO
-- ================================================
INSERT IGNORE INTO permissions (module, action, name, description, is_active, created_at, updated_at) VALUES
('providers', 'view', 'Ver Proveedores', 'Ver listado de proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('providers', 'create', 'Crear Proveedores', 'Crear nuevos proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('providers', 'edit', 'Editar Proveedores', 'Editar proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('providers', 'delete', 'Eliminar Proveedores', 'Eliminar proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('providers', 'activate', 'Activar/Suspender', 'Activar/suspender cuentas de proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('providers', 'bills_validate', 'Validar Facturas', 'Validar facturas de proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('providers', 'receipts_manage', 'Gestionar Contrarecibos', 'Gestionar contrarecibos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('providers', 'config', 'Configuración', 'Configurar parámetros de facturación/pago', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('providers', 'logs', 'Ver Logs', 'Ver logs de auditoría de proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- Asignar permisos a rol admin (role_id = 1)
INSERT IGNORE INTO role_permissions (role_id, permission_id) 
SELECT 1, id FROM permissions WHERE module = 'providers';

-- ================================================
-- FIN DE SCRIPT
-- ================================================
