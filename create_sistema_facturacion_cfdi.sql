-- Sistema de Facturación Electrónica CFDI
-- Tablas para emisión, timbrado y gestión de CFDIs

-- 1. Tabla principal de facturas CFDI
DROP TABLE IF EXISTS facturas_cfdi;
CREATE TABLE facturas_cfdi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL COMMENT 'ID del tenant',
    
    -- Información del folio
    serie VARCHAR(25) DEFAULT NULL COMMENT 'Serie del comprobante',
    folio VARCHAR(40) NOT NULL COMMENT 'Folio del comprobante',
    folio_fiscal VARCHAR(36) DEFAULT NULL COMMENT 'UUID del SAT (después de timbrar)',
    
    -- Tipo de comprobante
    tipo_comprobante VARCHAR(2) NOT NULL COMMENT 'I=Ingreso, E=Egreso, T=Traslado, N=Nómina, P=Pago',
    
    -- Fechas
    fecha_emision DATETIME NOT NULL COMMENT 'Fecha y hora de emisión',
    fecha_timbrado DATETIME DEFAULT NULL COMMENT 'Fecha de timbrado SAT',
    fecha_certificacion DATETIME DEFAULT NULL COMMENT 'Fecha de certificación',
    
    -- Información del emisor
    emisor_rfc VARCHAR(13) NOT NULL COMMENT 'RFC del emisor',
    emisor_nombre VARCHAR(255) NOT NULL COMMENT 'Nombre o razón social del emisor',
    emisor_regimen_fiscal VARCHAR(5) NOT NULL COMMENT 'Clave régimen fiscal SAT',
    
    -- Información del receptor
    receptor_rfc VARCHAR(13) NOT NULL COMMENT 'RFC del receptor',
    receptor_nombre VARCHAR(255) NOT NULL COMMENT 'Nombre del receptor',
    receptor_uso_cfdi VARCHAR(5) NOT NULL COMMENT 'Clave uso CFDI SAT',
    receptor_regimen_fiscal VARCHAR(5) DEFAULT NULL COMMENT 'Régimen fiscal del receptor',
    receptor_domicilio_fiscal VARCHAR(5) DEFAULT NULL COMMENT 'Código postal del receptor',
    
    -- Método y forma de pago
    metodo_pago VARCHAR(5) NOT NULL COMMENT 'PUE o PPD',
    forma_pago VARCHAR(3) DEFAULT NULL COMMENT 'Clave forma de pago SAT',
    condiciones_pago VARCHAR(255) DEFAULT NULL COMMENT 'Condiciones de pago',
    
    -- Moneda
    moneda VARCHAR(3) DEFAULT 'MXN' COMMENT 'Clave de moneda',
    tipo_cambio DECIMAL(10,6) DEFAULT 1.000000 COMMENT 'Tipo de cambio',
    
    -- Importes
    subtotal DECIMAL(19,2) NOT NULL DEFAULT 0.00 COMMENT 'Subtotal antes de impuestos',
    descuento DECIMAL(19,2) DEFAULT 0.00 COMMENT 'Descuento total',
    total DECIMAL(19,2) NOT NULL DEFAULT 0.00 COMMENT 'Total de la factura',
    
    -- Impuestos
    total_impuestos_trasladados DECIMAL(19,2) DEFAULT 0.00 COMMENT 'Total de impuestos trasladados',
    total_impuestos_retenidos DECIMAL(19,2) DEFAULT 0.00 COMMENT 'Total de impuestos retenidos',
    
    -- Certificación y timbrado
    no_certificado VARCHAR(20) DEFAULT NULL COMMENT 'Número de certificado del emisor',
    no_certificado_sat VARCHAR(20) DEFAULT NULL COMMENT 'Número de certificado del SAT',
    sello_digital TEXT DEFAULT NULL COMMENT 'Sello digital del emisor',
    sello_sat TEXT DEFAULT NULL COMMENT 'Sello digital del SAT',
    cadena_original_sat TEXT DEFAULT NULL COMMENT 'Cadena original del complemento de certificación',
    
    -- Archivos XML y PDF
    xml_path VARCHAR(255) DEFAULT NULL COMMENT 'Ruta del archivo XML',
    pdf_path VARCHAR(255) DEFAULT NULL COMMENT 'Ruta del archivo PDF',
    
    -- Relaciones
    relacionado_tipo VARCHAR(5) DEFAULT NULL COMMENT 'Tipo de relación SAT',
    relacionado_uuid VARCHAR(36) DEFAULT NULL COMMENT 'UUID del CFDI relacionado',
    
    -- Estado y control
    status ENUM('borrador', 'timbrado', 'cancelado', 'error') DEFAULT 'borrador' COMMENT 'Estado del CFDI',
    motivo_cancelacion VARCHAR(2) DEFAULT NULL COMMENT 'Clave de motivo de cancelación SAT',
    fecha_cancelacion DATETIME DEFAULT NULL COMMENT 'Fecha de cancelación',
    
    -- Notas y observaciones
    observaciones TEXT DEFAULT NULL COMMENT 'Notas internas',
    
    -- Auditoría
    created_by INT UNSIGNED DEFAULT NULL COMMENT 'Usuario que creó',
    updated_by INT UNSIGNED DEFAULT NULL COMMENT 'Usuario que actualizó',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_tenant (tenant_id),
    INDEX idx_folio (serie, folio),
    INDEX idx_uuid (folio_fiscal),
    INDEX idx_emisor (emisor_rfc),
    INDEX idx_receptor (receptor_rfc),
    INDEX idx_fecha (fecha_emision),
    INDEX idx_status (status),
    INDEX idx_tipo (tipo_comprobante),
    UNIQUE KEY unique_folio (tenant_id, serie, folio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Facturas CFDI (Comprobantes Fiscales Digitales)';

-- 2. Tabla de conceptos (líneas de la factura)
DROP TABLE IF EXISTS facturas_cfdi_conceptos;
CREATE TABLE facturas_cfdi_conceptos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    factura_id INT UNSIGNED NOT NULL COMMENT 'ID de la factura',
    
    -- Orden y agrupación
    numero_linea INT NOT NULL DEFAULT 1 COMMENT 'Número de línea',
    
    -- Información del producto/servicio
    clave_prod_serv VARCHAR(8) NOT NULL COMMENT 'Clave de producto/servicio SAT',
    clave_unidad VARCHAR(10) NOT NULL COMMENT 'Clave de unidad SAT',
    unidad VARCHAR(50) DEFAULT NULL COMMENT 'Descripción de la unidad',
    cantidad DECIMAL(19,6) NOT NULL DEFAULT 1.000000 COMMENT 'Cantidad',
    descripcion TEXT NOT NULL COMMENT 'Descripción del concepto',
    
    -- Importes
    valor_unitario DECIMAL(19,6) NOT NULL COMMENT 'Precio unitario',
    importe DECIMAL(19,2) NOT NULL COMMENT 'Importe (cantidad * valor_unitario)',
    descuento DECIMAL(19,2) DEFAULT 0.00 COMMENT 'Descuento del concepto',
    
    -- Identificación
    no_identificacion VARCHAR(100) DEFAULT NULL COMMENT 'Número de identificación (SKU)',
    
    -- Predicciones
    objeto_imp VARCHAR(2) DEFAULT '02' COMMENT '01=No, 02=Sí, 03=Sí y no obligado',
    
    -- Auditoría
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_factura (factura_id),
    INDEX idx_producto (clave_prod_serv),
    FOREIGN KEY (factura_id) REFERENCES facturas_cfdi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Conceptos de las facturas CFDI';

-- 3. Tabla de impuestos por concepto
DROP TABLE IF EXISTS facturas_cfdi_impuestos;
CREATE TABLE facturas_cfdi_impuestos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    concepto_id INT UNSIGNED NOT NULL COMMENT 'ID del concepto',
    
    -- Tipo de impuesto
    tipo ENUM('traslado', 'retencion') NOT NULL COMMENT 'Traslado o Retención',
    impuesto VARCHAR(3) NOT NULL COMMENT '001=ISR, 002=IVA, 003=IEPS',
    tipo_factor ENUM('Tasa', 'Cuota', 'Exento') NOT NULL COMMENT 'Tipo de factor',
    tasa_o_cuota DECIMAL(10,6) DEFAULT NULL COMMENT 'Tasa o cuota del impuesto (ej: 0.160000 para 16%)',
    base DECIMAL(19,2) NOT NULL COMMENT 'Base del impuesto',
    importe DECIMAL(19,2) DEFAULT 0.00 COMMENT 'Importe del impuesto',
    
    -- Auditoría
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_concepto (concepto_id),
    INDEX idx_tipo (tipo, impuesto),
    FOREIGN KEY (concepto_id) REFERENCES facturas_cfdi_conceptos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Impuestos de los conceptos de facturas CFDI';

-- 4. Tabla de complementos de pago (para PPD)
DROP TABLE IF EXISTS facturas_cfdi_pagos;
CREATE TABLE facturas_cfdi_pagos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL COMMENT 'ID del tenant',
    
    -- Información del complemento de pago
    folio_fiscal VARCHAR(36) NOT NULL COMMENT 'UUID del complemento de pago',
    fecha_pago DATETIME NOT NULL COMMENT 'Fecha del pago',
    
    -- Información del pago
    forma_pago VARCHAR(3) NOT NULL COMMENT 'Clave forma de pago SAT',
    moneda VARCHAR(3) DEFAULT 'MXN' COMMENT 'Moneda del pago',
    tipo_cambio DECIMAL(10,6) DEFAULT 1.000000 COMMENT 'Tipo de cambio',
    monto DECIMAL(19,2) NOT NULL COMMENT 'Monto del pago',
    
    -- Información bancaria (opcional)
    numero_operacion VARCHAR(100) DEFAULT NULL COMMENT 'Número de operación bancaria',
    rfc_emisor_cuenta_ordenante VARCHAR(13) DEFAULT NULL COMMENT 'RFC del banco ordenante',
    nombre_banco_ordenante VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del banco ordenante',
    cuenta_ordenante VARCHAR(50) DEFAULT NULL COMMENT 'Cuenta ordenante',
    rfc_emisor_cuenta_beneficiaria VARCHAR(13) DEFAULT NULL COMMENT 'RFC del banco beneficiario',
    cuenta_beneficiaria VARCHAR(50) DEFAULT NULL COMMENT 'Cuenta beneficiaria',
    
    -- Archivos
    xml_path VARCHAR(255) DEFAULT NULL COMMENT 'Ruta del XML del complemento',
    pdf_path VARCHAR(255) DEFAULT NULL COMMENT 'Ruta del PDF del complemento',
    
    -- Estado
    status ENUM('borrador', 'timbrado', 'cancelado') DEFAULT 'borrador',
    
    -- Auditoría
    created_by INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_tenant (tenant_id),
    INDEX idx_uuid (folio_fiscal),
    INDEX idx_fecha (fecha_pago),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Complementos de pago CFDI';

-- 5. Tabla de documentos relacionados en pagos
DROP TABLE IF EXISTS facturas_cfdi_pagos_documentos;
CREATE TABLE facturas_cfdi_pagos_documentos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pago_id INT UNSIGNED NOT NULL COMMENT 'ID del pago',
    factura_id INT UNSIGNED NOT NULL COMMENT 'ID de la factura que se paga',
    
    -- Información del documento relacionado
    uuid_documento VARCHAR(36) NOT NULL COMMENT 'UUID de la factura relacionada',
    serie VARCHAR(25) DEFAULT NULL,
    folio VARCHAR(40) NOT NULL,
    moneda VARCHAR(3) DEFAULT 'MXN',
    tipo_cambio DECIMAL(10,6) DEFAULT 1.000000,
    metodo_pago VARCHAR(5) NOT NULL COMMENT 'Método de pago de la factura',
    
    -- Importes
    numero_parcialidad INT NOT NULL DEFAULT 1 COMMENT 'Número de parcialidad',
    importe_saldo_anterior DECIMAL(19,2) NOT NULL COMMENT 'Saldo anterior',
    importe_pagado DECIMAL(19,2) NOT NULL COMMENT 'Importe pagado en esta parcialidad',
    importe_saldo_insoluto DECIMAL(19,2) NOT NULL COMMENT 'Saldo restante',
    
    -- Auditoría
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_pago (pago_id),
    INDEX idx_factura (factura_id),
    INDEX idx_uuid (uuid_documento),
    FOREIGN KEY (pago_id) REFERENCES facturas_cfdi_pagos(id) ON DELETE CASCADE,
    FOREIGN KEY (factura_id) REFERENCES facturas_cfdi(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Documentos relacionados en complementos de pago';

-- 6. Tabla de configuración de facturación (certificados, folios, etc.)
DROP TABLE IF EXISTS configuracion_facturacion;
CREATE TABLE configuracion_facturacion (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL COMMENT 'ID del tenant',
    
    -- Información del emisor
    rfc VARCHAR(13) NOT NULL COMMENT 'RFC del emisor',
    razon_social VARCHAR(255) NOT NULL COMMENT 'Razón social',
    regimen_fiscal VARCHAR(5) NOT NULL COMMENT 'Régimen fiscal SAT',
    
    -- Domicilio fiscal
    codigo_postal VARCHAR(5) NOT NULL COMMENT 'Código postal',
    
    -- Certificados
    certificado_cer TEXT DEFAULT NULL COMMENT 'Archivo .cer en base64',
    certificado_key TEXT DEFAULT NULL COMMENT 'Archivo .key en base64',
    certificado_password VARCHAR(255) DEFAULT NULL COMMENT 'Contraseña del certificado (encriptada)',
    certificado_numero VARCHAR(20) DEFAULT NULL COMMENT 'Número del certificado',
    certificado_vigencia_inicio DATE DEFAULT NULL COMMENT 'Inicio de vigencia',
    certificado_vigencia_fin DATE DEFAULT NULL COMMENT 'Fin de vigencia',
    
    -- Configuración de PAC (Proveedor Autorizado de Certificación)
    pac_nombre VARCHAR(50) DEFAULT NULL COMMENT 'Nombre del PAC',
    pac_usuario VARCHAR(255) DEFAULT NULL COMMENT 'Usuario del PAC',
    pac_password VARCHAR(255) DEFAULT NULL COMMENT 'Contraseña del PAC (encriptada)',
    pac_url_timbrado VARCHAR(255) DEFAULT NULL COMMENT 'URL de timbrado',
    pac_url_cancelacion VARCHAR(255) DEFAULT NULL COMMENT 'URL de cancelación',
    pac_produccion TINYINT(1) DEFAULT 0 COMMENT '0=Pruebas, 1=Producción',
    
    -- Configuración de folios
    serie_actual VARCHAR(25) DEFAULT NULL COMMENT 'Serie actual',
    folio_actual INT UNSIGNED DEFAULT 1 COMMENT 'Folio actual',
    folio_prefijo VARCHAR(10) DEFAULT NULL COMMENT 'Prefijo del folio',
    
    -- Configuración de logo y diseño
    logo_path VARCHAR(255) DEFAULT NULL COMMENT 'Ruta del logo',
    color_primario VARCHAR(7) DEFAULT '#007bff' COMMENT 'Color primario del PDF',
    
    -- Configuración adicional
    condiciones_pago_default VARCHAR(255) DEFAULT NULL COMMENT 'Condiciones de pago por defecto',
    observaciones_default TEXT DEFAULT NULL COMMENT 'Observaciones por defecto',
    
    -- Estado
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Configuración activa',
    
    -- Auditoría
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_tenant (tenant_id),
    INDEX idx_rfc (rfc),
    UNIQUE KEY unique_tenant_rfc (tenant_id, rfc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuración de facturación electrónica';

-- 7. Tabla de historial de timbrado
DROP TABLE IF EXISTS facturas_cfdi_log;
CREATE TABLE facturas_cfdi_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    factura_id INT UNSIGNED NOT NULL COMMENT 'ID de la factura',
    
    -- Información del evento
    evento ENUM('creacion', 'timbrado', 'cancelacion', 'error', 'modificacion') NOT NULL,
    descripcion TEXT DEFAULT NULL COMMENT 'Descripción del evento',
    
    -- Respuesta del PAC
    respuesta_pac TEXT DEFAULT NULL COMMENT 'Respuesta del PAC (JSON)',
    codigo_error VARCHAR(10) DEFAULT NULL COMMENT 'Código de error',
    mensaje_error TEXT DEFAULT NULL COMMENT 'Mensaje de error',
    
    -- Usuario y fecha
    user_id INT UNSIGNED DEFAULT NULL COMMENT 'Usuario que realizó la acción',
    ip_address VARCHAR(45) DEFAULT NULL COMMENT 'Dirección IP',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_factura (factura_id),
    INDEX idx_evento (evento),
    INDEX idx_fecha (created_at),
    FOREIGN KEY (factura_id) REFERENCES facturas_cfdi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de eventos de facturación';

-- Insertar configuración inicial de ejemplo
INSERT INTO configuracion_facturacion (tenant_id, rfc, razon_social, regimen_fiscal, codigo_postal, serie_actual, folio_actual, is_active)
VALUES (1, 'XAXX010101000', 'EMPRESA EJEMPLO SA DE CV', '601', '00000', 'A', 1, 1)
ON DUPLICATE KEY UPDATE rfc = VALUES(rfc);

-- Verificar tablas creadas
SELECT 
    'Tablas creadas' as mensaje,
    COUNT(*) as total
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_name IN (
    'facturas_cfdi',
    'facturas_cfdi_conceptos',
    'facturas_cfdi_impuestos',
    'facturas_cfdi_pagos',
    'facturas_cfdi_pagos_documentos',
    'configuracion_facturacion',
    'facturas_cfdi_log'
);
