USE base;

SET FOREIGN_KEY_CHECKS=0;

-- =====================================================
-- AMPLIACIÓN: Datos Detallados de Proveedores
-- =====================================================

-- =====================================================
-- 1. AGREGAR category_id A PROVIDERS
-- =====================================================
ALTER TABLE providers 
ADD COLUMN category_id INT(11) UNSIGNED NULL AFTER tenant_id,
ADD INDEX idx_category (category_id);

-- =====================================================
-- 2. TABLA: PROVIDER_CONTACTS (Contactos Detallados)
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_contacts_detailed (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT(11) UNSIGNED NOT NULL,
    contact_type ENUM('general','purchasing','accounting','technical','legal','administrative') NOT NULL DEFAULT 'general',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NULL COMMENT 'Puesto/Cargo',
    department VARCHAR(100) NULL COMMENT 'Departamento',
    email VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    mobile VARCHAR(20) NULL,
    extension VARCHAR(10) NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_provider (provider_id),
    INDEX idx_type (contact_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. TABLA: PROVIDER_ADDRESSES_DETAILED
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_addresses_detailed (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT(11) UNSIGNED NOT NULL,
    address_type ENUM('fiscal','shipping','pickup','billing','administrative') NOT NULL DEFAULT 'fiscal',
    street VARCHAR(255) NOT NULL,
    exterior_number VARCHAR(20) NULL,
    interior_number VARCHAR(20) NULL,
    neighborhood VARCHAR(100) NULL COMMENT 'Colonia',
    city VARCHAR(100) NOT NULL,
    municipality VARCHAR(100) NULL COMMENT 'Municipio/Delegación',
    state VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'México',
    reference TEXT NULL COMMENT 'Referencias para ubicación',
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_provider (provider_id),
    INDEX idx_type (address_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. TABLA: PROVIDER_FINANCIAL_INFO (Información Financiera)
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_financial_info (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT(11) UNSIGNED NOT NULL UNIQUE,
    credit_days INT(11) NOT NULL DEFAULT 30 COMMENT 'Días de crédito',
    credit_limit DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Límite de crédito',
    credit_used DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Crédito utilizado',
    discount_percentage DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Descuento general %',
    payment_terms TEXT NULL COMMENT 'Términos de pago detallados',
    minimum_order DECIMAL(15,2) NULL COMMENT 'Pedido mínimo',
    currency VARCHAR(3) NOT NULL DEFAULT 'MXN',
    bank_name VARCHAR(100) NULL,
    account_holder VARCHAR(255) NULL,
    account_number VARCHAR(50) NULL,
    clabe VARCHAR(18) NULL,
    swift_code VARCHAR(11) NULL,
    routing_number VARCHAR(50) NULL,
    credit_approved_by INT(11) UNSIGNED NULL,
    credit_approved_at DATETIME NULL,
    last_credit_review DATETIME NULL,
    next_credit_review DATETIME NULL,
    financial_status ENUM('approved','pending','suspended','cancelled') NOT NULL DEFAULT 'pending',
    risk_level ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_provider (provider_id),
    INDEX idx_status (financial_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. TABLA: PROVIDER_DOCUMENTS (Constancias, Opiniones, Contratos)
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_documents (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT(11) UNSIGNED NOT NULL,
    document_type ENUM(
        'constancia_fiscal',
        'opinion_cumplimiento',
        'contrato',
        'cedula_fiscal',
        'acta_constitutiva',
        'poder_legal',
        'comprobante_domicilio',
        'certificado_calidad',
        'poliza_seguro',
        'carta_responsiva',
        'otro'
    ) NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    document_number VARCHAR(100) NULL COMMENT 'Número de documento/folio',
    file_path VARCHAR(500) NULL COMMENT 'Ruta del archivo',
    file_name VARCHAR(255) NULL,
    file_size INT(11) NULL COMMENT 'Tamaño en bytes',
    issue_date DATE NULL COMMENT 'Fecha de emisión',
    expiration_date DATE NULL COMMENT 'Fecha de vencimiento',
    issuing_authority VARCHAR(255) NULL COMMENT 'Autoridad emisora',
    status ENUM('active','expired','cancelled','pending') NOT NULL DEFAULT 'active',
    verified_by INT(11) UNSIGNED NULL,
    verified_at DATETIME NULL,
    notes TEXT NULL,
    created_by INT(11) UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_provider (provider_id),
    INDEX idx_type (document_type),
    INDEX idx_expiration (expiration_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. TABLA: PROVIDER_CONTRACTS (Contratos Detallados)
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_contracts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT(11) UNSIGNED NOT NULL,
    contract_number VARCHAR(100) NOT NULL,
    contract_type ENUM('compra','servicio','marco','suministro','distribucion','otro') NOT NULL,
    contract_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    renewal_date DATE NULL,
    contract_value DECIMAL(15,2) NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'MXN',
    status ENUM('draft','active','expired','cancelled','suspended') NOT NULL DEFAULT 'draft',
    file_path VARCHAR(500) NULL,
    payment_terms TEXT NULL,
    delivery_terms TEXT NULL,
    special_conditions TEXT NULL,
    auto_renewal TINYINT(1) NOT NULL DEFAULT 0,
    renewal_days_notice INT(11) NULL COMMENT 'Días de aviso antes de renovación',
    created_by INT(11) UNSIGNED NOT NULL,
    approved_by INT(11) UNSIGNED NULL,
    approved_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_provider (provider_id),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. TABLA: PROVIDER_EVALUATIONS (Evaluación de Proveedores)
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_evaluations (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT(11) UNSIGNED NOT NULL,
    evaluation_date DATE NOT NULL,
    evaluated_by INT(11) UNSIGNED NOT NULL,
    period_from DATE NOT NULL,
    period_to DATE NOT NULL,
    
    -- Criterios de evaluación (1-10)
    quality_score DECIMAL(3,1) NOT NULL DEFAULT 0.0 COMMENT 'Calidad de productos/servicios',
    delivery_score DECIMAL(3,1) NOT NULL DEFAULT 0.0 COMMENT 'Cumplimiento de entregas',
    price_score DECIMAL(3,1) NOT NULL DEFAULT 0.0 COMMENT 'Competitividad de precios',
    service_score DECIMAL(3,1) NOT NULL DEFAULT 0.0 COMMENT 'Atención y servicio',
    documentation_score DECIMAL(3,1) NOT NULL DEFAULT 0.0 COMMENT 'Documentación y facturación',
    
    -- Puntaje general
    total_score DECIMAL(4,2) NOT NULL DEFAULT 0.00,
    rating ENUM('excelente','bueno','regular','malo','pesimo') NOT NULL,
    
    comments TEXT NULL,
    recommendations TEXT NULL,
    action_required TINYINT(1) NOT NULL DEFAULT 0,
    action_description TEXT NULL,
    
    status ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_provider (provider_id),
    INDEX idx_date (evaluation_date),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. TABLA: PROVIDER_CERTIFICATIONS (Certificaciones)
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_certifications (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT(11) UNSIGNED NOT NULL,
    certification_type ENUM('iso','haccp','fsc','fair_trade','organic','otro') NOT NULL,
    certification_name VARCHAR(255) NOT NULL,
    certification_number VARCHAR(100) NULL,
    certifying_body VARCHAR(255) NULL COMMENT 'Organismo certificador',
    issue_date DATE NOT NULL,
    expiration_date DATE NOT NULL,
    status ENUM('active','expired','suspended') NOT NULL DEFAULT 'active',
    file_path VARCHAR(500) NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_provider (provider_id),
    INDEX idx_expiration (expiration_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. TABLA: PROVIDER_COMPLIANCE (Cumplimiento Fiscal/Legal)
-- =====================================================
CREATE TABLE IF NOT EXISTS provider_compliance (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT(11) UNSIGNED NOT NULL,
    compliance_type ENUM(
        'opinion_cumplimiento_sat',
        'constancia_situacion_fiscal',
        'seguro_social',
        'infonavit',
        'registro_patron',
        'otro'
    ) NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    document_number VARCHAR(100) NULL,
    issue_date DATE NOT NULL,
    expiration_date DATE NOT NULL,
    status ENUM('vigente','por_vencer','vencido','no_aplica') NOT NULL DEFAULT 'vigente',
    authority VARCHAR(255) NULL COMMENT 'SAT, IMSS, INFONAVIT, etc',
    file_path VARCHAR(500) NULL,
    alert_days INT(11) NOT NULL DEFAULT 30 COMMENT 'Días antes de vencer para alertar',
    last_check_date DATE NULL,
    next_check_date DATE NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_provider (provider_id),
    INDEX idx_expiration (expiration_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. VISTA: Resumen de Proveedores
-- =====================================================
CREATE OR REPLACE VIEW v_providers_summary AS
SELECT 
    p.id,
    p.code,
    p.company_name,
    p.email,
    p.phone,
    p.tax_id,
    p.is_active,
    p.is_suspended,
    pc.name as category_name,
    pfi.credit_limit,
    pfi.credit_used,
    pfi.credit_days,
    pfi.financial_status,
    pfi.risk_level,
    (pfi.credit_limit - pfi.credit_used) as credit_available,
    (SELECT COUNT(*) FROM provider_inventory_receipts WHERE provider_id = p.id AND deleted_at IS NULL) as total_receipts,
    (SELECT COUNT(*) FROM provider_payments WHERE provider_id = p.id AND deleted_at IS NULL) as total_payments,
    (SELECT SUM(total) FROM providers_orders WHERE provider_id = p.id AND deleted = 0) as total_orders_amount,
    (SELECT MAX(date_order) FROM providers_orders WHERE provider_id = p.id AND deleted = 0) as last_order_date,
    p.created_at,
    p.updated_at
FROM providers p
LEFT JOIN provider_categories pc ON p.category_id = pc.id
LEFT JOIN provider_financial_info pfi ON p.id = pfi.provider_id
WHERE p.deleted_at IS NULL;

SET FOREIGN_KEY_CHECKS=1;

-- =====================================================
-- DATOS INICIALES
-- =====================================================

-- Ejemplo de proveedor completo
INSERT INTO providers (tenant_id, code, company_name, email, phone, tax_id, is_active, is_suspended, created_at) VALUES
(1, 'PRO-000001', 'Distribuidora de Insumos SA de CV', 'contacto@distribuidora.com', '5512345678', 'DIS950101ABC', 1, 0, NOW());

SET @provider_id = LAST_INSERT_ID();

-- Información financiera
INSERT INTO provider_financial_info (provider_id, credit_days, credit_limit, discount_percentage, currency, financial_status, risk_level) VALUES
(@provider_id, 30, 100000.00, 5.00, 'MXN', 'approved', 'low');

-- Contacto
INSERT INTO provider_contacts_detailed (provider_id, contact_type, first_name, last_name, position, email, phone, is_primary) VALUES
(@provider_id, 'purchasing', 'Juan', 'Pérez', 'Gerente de Ventas', 'jperez@distribuidora.com', '5512345678', 1);

-- Dirección fiscal
INSERT INTO provider_addresses_detailed (provider_id, address_type, street, exterior_number, neighborhood, city, state, postal_code, is_default) VALUES
(@provider_id, 'fiscal', 'Av. Insurgentes Sur', '1234', 'Del Valle', 'Ciudad de México', 'CDMX', '03100', 1);

SELECT '✅ Sistema de proveedores ampliado correctamente' as status;

-- Ver resumen
SELECT * FROM v_providers_summary;
