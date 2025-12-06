-- Catálogos SAT para Facturación Electrónica
-- Tablas maestras con información fiscal del SAT

-- 1. Catálogo de Productos y Servicios SAT (c_ClaveProdServ)
CREATE TABLE IF NOT EXISTS sat_productos_servicios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(8) NOT NULL UNIQUE COMMENT 'Clave del producto/servicio',
    descripcion TEXT NOT NULL COMMENT 'Descripción del producto/servicio',
    palabras_similares TEXT COMMENT 'Palabras clave para búsqueda',
    incluye_iva ENUM('Sí','No','Opcional') DEFAULT 'Opcional',
    incluye_ieps ENUM('Sí','No','Opcional') DEFAULT 'No',
    complemento VARCHAR(100) COMMENT 'Complemento que debe usarse',
    fecha_inicio DATE COMMENT 'Fecha de vigencia inicio',
    fecha_fin DATE COMMENT 'Fecha de vigencia fin',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo SAT de Productos y Servicios';

-- 2. Catálogo de Claves de Unidad (c_ClaveUnidad)
CREATE TABLE IF NOT EXISTS sat_unidades (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(10) NOT NULL UNIQUE COMMENT 'Clave de unidad',
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre de la unidad',
    descripcion TEXT COMMENT 'Descripción detallada',
    nota TEXT COMMENT 'Notas adicionales',
    simbolo VARCHAR(20) COMMENT 'Símbolo de la unidad',
    fecha_inicio DATE COMMENT 'Fecha de vigencia inicio',
    fecha_fin DATE COMMENT 'Fecha de vigencia fin',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo SAT de Unidades de Medida';

-- 3. Catálogo de Uso de CFDI (c_UsoCFDI)
CREATE TABLE IF NOT EXISTS sat_uso_cfdi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(5) NOT NULL UNIQUE COMMENT 'Clave de uso CFDI',
    descripcion VARCHAR(255) NOT NULL COMMENT 'Descripción del uso',
    aplica_fisica TINYINT(1) DEFAULT 1 COMMENT 'Aplica para persona física',
    aplica_moral TINYINT(1) DEFAULT 1 COMMENT 'Aplica para persona moral',
    regimen_fiscal VARCHAR(255) COMMENT 'Regímenes aplicables',
    fecha_inicio DATE COMMENT 'Fecha de vigencia inicio',
    fecha_fin DATE COMMENT 'Fecha de vigencia fin',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo SAT de Uso de CFDI';

-- 4. Catálogo de Formas de Pago (c_FormaPago)
CREATE TABLE IF NOT EXISTS sat_formas_pago (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(3) NOT NULL UNIQUE COMMENT 'Clave de forma de pago',
    descripcion VARCHAR(255) NOT NULL COMMENT 'Descripción de la forma de pago',
    bancarizado TINYINT(1) DEFAULT 0 COMMENT 'Requiere número de operación bancaria',
    numero_operacion TINYINT(1) DEFAULT 0 COMMENT 'Permite captura de número de operación',
    rfc_emisor_cuenta_ordenante VARCHAR(100) COMMENT 'RFC del emisor cuenta ordenante',
    fecha_inicio DATE COMMENT 'Fecha de vigencia inicio',
    fecha_fin DATE COMMENT 'Fecha de vigencia fin',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo SAT de Formas de Pago';

-- 5. Catálogo de Métodos de Pago (c_MetodoPago)
CREATE TABLE IF NOT EXISTS sat_metodos_pago (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(5) NOT NULL UNIQUE COMMENT 'Clave de método de pago',
    descripcion VARCHAR(255) NOT NULL COMMENT 'Descripción del método',
    fecha_inicio DATE COMMENT 'Fecha de vigencia inicio',
    fecha_fin DATE COMMENT 'Fecha de vigencia fin',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo SAT de Métodos de Pago';

-- 6. Catálogo de Tipos de Comprobante (c_TipoDeComprobante)
CREATE TABLE IF NOT EXISTS sat_tipos_comprobante (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(2) NOT NULL UNIQUE COMMENT 'Clave de tipo de comprobante',
    descripcion VARCHAR(100) NOT NULL COMMENT 'Descripción del tipo',
    valor_maximo DECIMAL(19,2) COMMENT 'Valor máximo permitido',
    fecha_inicio DATE COMMENT 'Fecha de vigencia inicio',
    fecha_fin DATE COMMENT 'Fecha de vigencia fin',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo SAT de Tipos de Comprobante';

-- 7. Catálogo de Regímenes Fiscales (c_RegimenFiscal)
CREATE TABLE IF NOT EXISTS sat_regimenes_fiscales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(5) NOT NULL UNIQUE COMMENT 'Clave de régimen fiscal',
    descripcion VARCHAR(255) NOT NULL COMMENT 'Descripción del régimen',
    aplica_fisica TINYINT(1) DEFAULT 1 COMMENT 'Aplica para persona física',
    aplica_moral TINYINT(1) DEFAULT 1 COMMENT 'Aplica para persona moral',
    fecha_inicio DATE COMMENT 'Fecha de vigencia inicio',
    fecha_fin DATE COMMENT 'Fecha de vigencia fin',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo SAT de Regímenes Fiscales';

-- 8. Insertar datos básicos más comunes

-- Tipos de Comprobante más usados
INSERT INTO sat_tipos_comprobante (clave, descripcion, is_active) VALUES
('I', 'Ingreso', 1),
('E', 'Egreso', 1),
('T', 'Traslado', 1),
('N', 'Nómina', 1),
('P', 'Pago', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Métodos de Pago más comunes
INSERT INTO sat_metodos_pago (clave, descripcion, is_active) VALUES
('PUE', 'Pago en una sola exhibición', 1),
('PPD', 'Pago en parcialidades o diferido', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Formas de Pago más comunes
INSERT INTO sat_formas_pago (clave, descripcion, bancarizado, is_active) VALUES
('01', 'Efectivo', 0, 1),
('02', 'Cheque nominativo', 1, 1),
('03', 'Transferencia electrónica de fondos', 1, 1),
('04', 'Tarjeta de crédito', 1, 1),
('28', 'Tarjeta de débito', 1, 1),
('99', 'Por definir', 0, 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Usos de CFDI más comunes
INSERT INTO sat_uso_cfdi (clave, descripcion, aplica_fisica, aplica_moral, is_active) VALUES
('G01', 'Adquisición de mercancías', 1, 1, 1),
('G02', 'Devoluciones, descuentos o bonificaciones', 1, 1, 1),
('G03', 'Gastos en general', 1, 1, 1),
('I01', 'Construcciones', 1, 1, 1),
('I02', 'Mobiliario y equipo de oficina por inversiones', 1, 1, 1),
('D01', 'Honorarios médicos, dentales y gastos hospitalarios', 1, 0, 1),
('D02', 'Gastos médicos por incapacidad o discapacidad', 1, 0, 1),
('D04', 'Donativos', 1, 0, 1),
('P01', 'Por definir', 1, 1, 1),
('S01', 'Sin efectos fiscales', 1, 1, 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Unidades de medida más comunes
INSERT INTO sat_unidades (clave, nombre, simbolo, is_active) VALUES
('H87', 'Pieza', 'Pza', 1),
('E48', 'Unidad de servicio', 'Servicio', 1),
('ACT', 'Actividad', 'Actividad', 1),
('KGM', 'Kilogramo', 'kg', 1),
('MTR', 'Metro', 'm', 1),
('LTR', 'Litro', 'L', 1),
('XBX', 'Caja', 'Caja', 1),
('XPK', 'Paquete', 'Paquete', 1),
('DAY', 'Día', 'Día', 1),
('MON', 'Mes', 'Mes', 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- Regímenes Fiscales más comunes
INSERT INTO sat_regimenes_fiscales (clave, descripcion, aplica_fisica, aplica_moral, is_active) VALUES
('601', 'General de Ley Personas Morales', 0, 1, 1),
('603', 'Personas Morales con Fines no Lucrativos', 0, 1, 1),
('605', 'Sueldos y Salarios e Ingresos Asimilados a Salarios', 1, 0, 1),
('606', 'Arrendamiento', 1, 0, 1),
('608', 'Demás ingresos', 1, 0, 1),
('610', 'Residentes en el Extranjero sin Establecimiento Permanente en México', 1, 1, 1),
('611', 'Ingresos por Dividendos (socios y accionistas)', 1, 0, 1),
('612', 'Personas Físicas con Actividades Empresariales y Profesionales', 1, 0, 1),
('614', 'Ingresos por intereses', 1, 0, 1),
('616', 'Sin obligaciones fiscales', 1, 0, 1),
('620', 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos', 0, 1, 1),
('621', 'Incorporación Fiscal', 1, 0, 1),
('622', 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras', 0, 1, 1),
('623', 'Opcional para Grupos de Sociedades', 0, 1, 1),
('624', 'Coordinados', 0, 1, 1),
('625', 'Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas', 1, 0, 1),
('626', 'Régimen Simplificado de Confianza', 1, 1, 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Verificar registros insertados
SELECT 'Tipos de Comprobante' as tabla, COUNT(*) as registros FROM sat_tipos_comprobante
UNION ALL
SELECT 'Métodos de Pago', COUNT(*) FROM sat_metodos_pago
UNION ALL
SELECT 'Formas de Pago', COUNT(*) FROM sat_formas_pago
UNION ALL
SELECT 'Usos de CFDI', COUNT(*) FROM sat_uso_cfdi
UNION ALL
SELECT 'Unidades', COUNT(*) FROM sat_unidades
UNION ALL
SELECT 'Regímenes Fiscales', COUNT(*) FROM sat_regimenes_fiscales;
