-- ===============================================
-- MÓDULO SAT - Tablas para gestión fiscal
-- ===============================================

-- Tabla para credenciales SAT (RFC, contraseñas, certificados)
CREATE TABLE IF NOT EXISTS sat_credentials (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    rfc VARCHAR(13) NOT NULL,
    password_encrypted TEXT NOT NULL COMMENT 'Contraseña del portal SAT encriptada',
    csd_cer LONGBLOB NULL COMMENT 'Certificado de Sello Digital (.cer)',
    csd_key LONGBLOB NULL COMMENT 'Llave privada CSD (.key)',
    csd_password_encrypted VARCHAR(255) NULL COMMENT 'Contraseña del CSD encriptada',
    fiel_cer LONGBLOB NULL COMMENT 'Certificado e.firma (.cer)',
    fiel_key LONGBLOB NULL COMMENT 'Llave privada e.firma (.key)',
    fiel_password_encrypted VARCHAR(255) NULL COMMENT 'Contraseña e.firma encriptada',
    is_active TINYINT(1) DEFAULT 1,
    last_connection DATETIME NULL,
    connection_status VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_tenant_rfc (tenant_id, rfc),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Credenciales SAT por tenant';

-- Tabla para log de descargas masivas
CREATE TABLE IF NOT EXISTS sat_downloads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    credential_id INT UNSIGNED NOT NULL,
    download_type ENUM('emitidos', 'recibidos', 'ambos') DEFAULT 'recibidos',
    date_from DATE NOT NULL,
    date_to DATE NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    total_downloaded INT DEFAULT 0,
    total_errors INT DEFAULT 0,
    error_message TEXT NULL,
    started_at DATETIME NULL,
    completed_at DATETIME NULL,
    created_by INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (credential_id) REFERENCES sat_credentials(id) ON DELETE CASCADE,
    INDEX idx_tenant_date (tenant_id, date_from, date_to),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de descargas masivas SAT';

-- Tabla principal de CFDIs descargados
CREATE TABLE IF NOT EXISTS sat_cfdis (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    download_id INT UNSIGNED NULL COMMENT 'ID de la descarga masiva',
    uuid VARCHAR(36) NOT NULL UNIQUE COMMENT 'Folio fiscal (UUID)',
    tipo_comprobante ENUM('I', 'E', 'T', 'N', 'P') NOT NULL COMMENT 'Ingreso/Egreso/Traslado/Nomina/Pago',
    version VARCHAR(10) NOT NULL,
    serie VARCHAR(25) NULL,
    folio VARCHAR(40) NULL,
    fecha_emision DATETIME NOT NULL,
    fecha_certificacion DATETIME NULL,
    rfc_emisor VARCHAR(13) NOT NULL,
    nombre_emisor VARCHAR(255) NULL,
    rfc_receptor VARCHAR(13) NOT NULL,
    nombre_receptor VARCHAR(255) NULL,
    uso_cfdi VARCHAR(5) NULL COMMENT 'Clave uso CFDI (G01, G02, etc)',
    forma_pago VARCHAR(5) NULL COMMENT '01-Efectivo, 02-Cheque, etc',
    metodo_pago VARCHAR(5) NULL COMMENT 'PUE, PPD',
    moneda VARCHAR(3) DEFAULT 'MXN',
    tipo_cambio DECIMAL(10, 6) DEFAULT 1.000000,
    subtotal DECIMAL(16, 2) NOT NULL,
    descuento DECIMAL(16, 2) DEFAULT 0.00,
    total DECIMAL(16, 2) NOT NULL,
    impuestos_trasladados DECIMAL(16, 2) DEFAULT 0.00,
    impuestos_retenidos DECIMAL(16, 2) DEFAULT 0.00,
    estado_sat ENUM('vigente', 'cancelado', 'no_encontrado') DEFAULT 'vigente',
    fecha_cancelacion DATETIME NULL,
    xml_content LONGTEXT NOT NULL COMMENT 'XML completo del CFDI',
    xml_hash VARCHAR(64) NOT NULL COMMENT 'SHA256 del XML para detección de duplicados',
    conceptos JSON NULL COMMENT 'Array de conceptos parseados',
    complementos JSON NULL COMMENT 'Complementos (pago, nomina, etc)',
    is_processed TINYINT(1) DEFAULT 0 COMMENT 'Ya procesado en contabilidad',
    processed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (download_id) REFERENCES sat_downloads(id) ON DELETE SET NULL,
    INDEX idx_tenant_uuid (tenant_id, uuid),
    INDEX idx_fecha_emision (fecha_emision),
    INDEX idx_rfc_emisor (rfc_emisor),
    INDEX idx_rfc_receptor (rfc_receptor),
    INDEX idx_estado (estado_sat),
    INDEX idx_tipo (tipo_comprobante),
    INDEX idx_processed (is_processed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='CFDIs descargados y parseados';

-- Tabla para validaciones de CFDIs
CREATE TABLE IF NOT EXISTS sat_validations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    cfdi_id INT UNSIGNED NULL,
    uuid VARCHAR(36) NOT NULL,
    validation_type ENUM('estructura', 'sello', 'estado_sat', 'lco') NOT NULL COMMENT 'estructura=XML válido, sello=firma válida, estado_sat=vigente/cancelado, lco=lista de complementos',
    is_valid TINYINT(1) DEFAULT 0,
    validation_message TEXT NULL,
    validated_at DATETIME NOT NULL,
    created_by INT UNSIGNED NOT NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (cfdi_id) REFERENCES sat_cfdis(id) ON DELETE CASCADE,
    INDEX idx_uuid (uuid),
    INDEX idx_validation_type (validation_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial de validaciones de CFDIs';

-- Insertar permisos para el módulo SAT
INSERT IGNORE INTO permissions (module, action, display_name, description) VALUES
('sat', 'view', 'Ver SAT', 'Ver el módulo SAT y dashboard'),
('sat', 'download', 'Descargar CFDIs', 'Descargar facturas desde el portal SAT'),
('sat', 'validate', 'Validar CFDIs', 'Validar la autenticidad de facturas'),
('sat', 'credentials', 'Gestionar credenciales', 'Configurar RFC y certificados'),
('sat', 'reports', 'Generar reportes', 'Exportar y generar reportes fiscales');

-- Asignar permisos al rol admin (id=1)
INSERT IGNORE INTO role_permissions (role_id, permission_id) 
SELECT 1, id FROM permissions WHERE module = 'sat';

-- Activar el módulo SAT para tenant 1
INSERT IGNORE INTO tenant_modules (tenant_id, module_id, is_active, activated_at, activated_by)
SELECT 1, id, 1, NOW(), 1 FROM system_modules WHERE name = 'sat';
