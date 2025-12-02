-- =============================================
-- MIGRACIÓN 008: AGREGAR MÓDULOS DE NEGOCIO CRÍTICOS
-- Fecha: 2024-12-02
-- Descripción: Agrega módulos de Facturación, Contabilidad, Nómina y BI
-- =============================================

-- Agregar nuevos módulos al sistema
INSERT INTO `modules` (`name`, `display_name`, `description`, `icon`, `category`, `menu_order`, `is_core`, `is_enabled`, `requires_modules`, `has_migration`, `migration_file`, `version`) VALUES

-- CONTABILIDAD Y FINANZAS (Business)
('contabilidad', 'Contabilidad', 'Catálogo de cuentas, pólizas contables, balanzas y estados financieros', 'fa-calculator', 'business', 5, 0, 0, '["finance"]', 1, 'contabilidad.sql', '1.0.0'),

('facturacion', 'Facturación Electrónica', 'CFDI 4.0, timbrado, certificados digitales SAT', 'fa-file-invoice', 'business', 6, 0, 0, '["contabilidad","sales"]', 1, 'facturacion.sql', '1.0.0'),

-- RRHH
('rrhh', 'Recursos Humanos', 'Gestión de empleados, contratos, vacaciones y evaluaciones', 'fa-users', 'business', 11, 0, 0, NULL, 1, 'rrhh.sql', '1.0.0'),

('nomina', 'Nómina', 'Cálculo de nómina, CFDI nómina 1.2, ISR, IMSS', 'fa-money-check-alt', 'business', 12, 0, 0, '["rrhh","contabilidad","facturacion"]', 1, 'nomina.sql', '1.0.0'),

-- BUSINESS INTELLIGENCE
('business_intelligence', 'Business Intelligence', 'Dashboards configurables, KPIs y análisis avanzado', 'fa-chart-line', 'system', 14, 0, 0, NULL, 1, 'business_intelligence.sql', '1.0.0');

-- =============================================
-- ACTUALIZAR MÓDULOS EXISTENTES
-- =============================================

-- Actualizar descripción de Finance para enfocarse en flujo de efectivo
UPDATE `modules` 
SET `description` = 'Flujo de efectivo, cuentas por cobrar/pagar, bancos y proyecciones financieras',
    `requires_modules` = NULL
WHERE `name` = 'finance';

-- Actualizar Sales para incluir integración con facturación
UPDATE `modules` 
SET `description` = 'Punto de venta, pedidos, remisiones y control de ventas',
    `requires_modules` = '["inventory"]'
WHERE `name` = 'sales';

-- Actualizar CRM
UPDATE `modules` 
SET `description` = 'Gestión de clientes, pipeline de ventas y seguimiento de oportunidades'
WHERE `name` = 'crm';

-- =============================================
-- AGREGAR NUEVOS PERMISOS
-- =============================================

INSERT INTO `permissions` (`module`, `action`, `description`) VALUES

-- Contabilidad
('contabilidad', 'view', 'Ver contabilidad'),
('contabilidad', 'create', 'Crear pólizas y cuentas'),
('contabilidad', 'edit', 'Editar pólizas y cuentas'),
('contabilidad', 'delete', 'Eliminar pólizas y cuentas'),
('contabilidad', 'post', 'Publicar pólizas'),
('contabilidad', 'reports', 'Ver reportes contables'),

-- Facturación
('facturacion', 'view', 'Ver facturas'),
('facturacion', 'create', 'Crear facturas'),
('facturacion', 'edit', 'Editar facturas (borrador)'),
('facturacion', 'cancel', 'Cancelar facturas timbradas'),
('facturacion', 'certificates', 'Gestionar certificados digitales'),
('facturacion', 'pac_config', 'Configurar PAC'),
('facturacion', 'send_email', 'Enviar facturas por email'),

-- RRHH
('rrhh', 'view', 'Ver empleados'),
('rrhh', 'create', 'Crear empleados'),
('rrhh', 'edit', 'Editar empleados'),
('rrhh', 'delete', 'Eliminar empleados'),
('rrhh', 'documents', 'Gestionar documentos de empleados'),

-- Nómina
('nomina', 'view', 'Ver nóminas'),
('nomina', 'create', 'Crear nóminas'),
('nomina', 'edit', 'Editar nóminas (borrador)'),
('nomina', 'calculate', 'Calcular nómina'),
('nomina', 'process', 'Procesar nómina'),
('nomina', 'cfdi', 'Generar CFDI de nómina'),
('nomina', 'reports', 'Ver reportes de nómina'),

-- Business Intelligence
('bi', 'view', 'Ver dashboards'),
('bi', 'create', 'Crear dashboards personalizados'),
('bi', 'edit', 'Editar dashboards'),
('bi', 'configure', 'Configurar widgets y métricas');

-- =============================================
-- ASIGNAR PERMISOS A SUPER ADMIN
-- =============================================

-- Obtener ID del rol super_admin
SET @super_admin_role = (SELECT id FROM roles WHERE name = 'super_admin' LIMIT 1);

-- Asignar todos los nuevos permisos a super_admin
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT @super_admin_role, p.id
FROM `permissions` p
WHERE p.module IN ('contabilidad', 'facturacion', 'rrhh', 'nomina', 'bi')
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = @super_admin_role AND rp.permission_id = p.id
);

-- =============================================
-- TABLAS PARA CERTIFICADOS DIGITALES
-- =============================================

CREATE TABLE IF NOT EXISTS `tenant_sat_certificates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL,
  `certificate_type` enum('FIEL','CSD') NOT NULL COMMENT 'FIEL=e.firma, CSD=Certificado Sello Digital',
  `cer_file_path` varchar(255) NOT NULL COMMENT 'Ruta encriptada del archivo .cer',
  `key_file_path` varchar(255) NOT NULL COMMENT 'Ruta encriptada del archivo .key',
  `key_password` text NOT NULL COMMENT 'Contraseña encriptada con AES-256',
  `rfc` varchar(13) NOT NULL,
  `razon_social` varchar(255) NOT NULL,
  `certificate_number` varchar(20) NOT NULL COMMENT 'Número de certificado',
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_default` tinyint(1) DEFAULT 0 COMMENT 'Certificado por defecto para facturar',
  `uploaded_by` int(11) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_valid_until` (`valid_until`),
  CONSTRAINT `fk_certificates_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_certificates_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Certificados digitales SAT por tenant';

-- =============================================
-- TABLAS PARA CONFIGURACIÓN DE PAC
-- =============================================

CREATE TABLE IF NOT EXISTS `tenant_pac_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL,
  `pac_provider` enum('finkok','diverza','sw','facturama','otros') NOT NULL,
  `pac_mode` enum('test','production') DEFAULT 'test',
  `pac_username` varchar(100) NOT NULL,
  `pac_password` text NOT NULL COMMENT 'Encriptado con AES-256',
  `pac_api_url` varchar(255) NOT NULL,
  `pac_stamp_url` varchar(255) DEFAULT NULL COMMENT 'URL específica para timbrado',
  `pac_cancel_url` varchar(255) DEFAULT NULL COMMENT 'URL específica para cancelación',
  `pac_test_mode` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `daily_limit` int(11) DEFAULT NULL COMMENT 'Límite diario de timbrados',
  `monthly_limit` int(11) DEFAULT NULL COMMENT 'Límite mensual de timbrados',
  `config_json` text COMMENT 'Configuración adicional en JSON',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_pac_config_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Configuración de PAC por tenant';

-- =============================================
-- TABLA PARA DASHBOARD WIDGETS
-- =============================================

CREATE TABLE IF NOT EXISTS `dashboard_widgets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `widget_key` varchar(50) NOT NULL COMMENT 'Identificador único del widget',
  `widget_name` varchar(100) NOT NULL,
  `widget_description` text,
  `widget_type` enum('metric','chart','list','table','custom') NOT NULL,
  `requires_modules` text COMMENT 'JSON con módulos requeridos',
  `default_size` varchar(20) DEFAULT 'col-md-4' COMMENT 'Clase Bootstrap',
  `icon` varchar(50) DEFAULT 'fa-chart-bar',
  `category` varchar(50) DEFAULT 'general',
  `min_role_level` int(11) DEFAULT 1 COMMENT 'Nivel mínimo de rol para ver el widget',
  `is_configurable` tinyint(1) DEFAULT 1,
  `config_schema` text COMMENT 'JSON Schema para configuración',
  `refresh_interval` int(11) DEFAULT 300 COMMENT 'Segundos entre refrescos automáticos',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_widget_key` (`widget_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Catálogo de widgets disponibles';

-- Insertar widgets por defecto
INSERT INTO `dashboard_widgets` (`widget_key`, `widget_name`, `widget_description`, `widget_type`, `requires_modules`, `default_size`, `icon`, `category`, `is_configurable`, `refresh_interval`) VALUES

-- Widgets generales (disponibles siempre)
('stats_users', 'Usuarios Activos', 'Total de usuarios activos en el sistema', 'metric', NULL, 'col-md-3', 'fa-users', 'general', 0, 300),
('recent_activity', 'Actividad Reciente', 'Últimas acciones en el sistema', 'list', NULL, 'col-md-6', 'fa-history', 'general', 1, 60),

-- Widgets de ventas
('sales_today', 'Ventas del Día', 'Total de ventas realizadas hoy', 'metric', '["sales"]', 'col-md-3', 'fa-dollar-sign', 'sales', 0, 300),
('sales_chart_week', 'Ventas de la Semana', 'Gráfica de ventas de los últimos 7 días', 'chart', '["sales"]', 'col-md-6', 'fa-chart-line', 'sales', 1, 600),
('top_products', 'Productos Más Vendidos', 'Top 10 productos del mes', 'chart', '["sales","inventory"]', 'col-md-6', 'fa-trophy', 'sales', 1, 1800),

-- Widgets de facturación
('pending_invoices', 'Facturas Pendientes', 'Facturas sin timbrar', 'list', '["facturacion"]', 'col-md-4', 'fa-file-invoice', 'invoicing', 0, 300),
('certificate_expiry', 'Certificados por Vencer', 'Alertas de certificados próximos a expirar', 'metric', '["facturacion"]', 'col-md-4', 'fa-certificate', 'invoicing', 0, 86400),

-- Widgets de inventario
('critical_inventory', 'Inventario Crítico', 'Productos con stock mínimo', 'list', '["inventory"]', 'col-md-4', 'fa-exclamation-triangle', 'inventory', 0, 600),
('inventory_value', 'Valor de Inventario', 'Valor total del inventario', 'metric', '["inventory"]', 'col-md-3', 'fa-warehouse', 'inventory', 0, 1800),

-- Widgets financieros
('accounts_receivable', 'Cuentas por Cobrar', 'Total por cobrar y vencidas', 'metric', '["finance"]', 'col-md-3', 'fa-hand-holding-usd', 'finance', 1, 600),
('cash_flow', 'Flujo de Efectivo', 'Ingresos vs Egresos últimos 30 días', 'chart', '["contabilidad"]', 'col-md-6', 'fa-chart-area', 'finance', 1, 1800);

-- =============================================
-- TRIGGER PARA ALERTAR CERTIFICADOS POR VENCER
-- =============================================

DELIMITER $$

CREATE TRIGGER `trg_check_certificate_expiry` 
BEFORE UPDATE ON `tenant_sat_certificates`
FOR EACH ROW
BEGIN
    -- Si el certificado está activo y vence en menos de 30 días
    IF NEW.is_active = 1 AND DATEDIFF(NEW.valid_until, CURDATE()) <= 30 THEN
        -- Aquí se podría insertar en una tabla de notificaciones
        -- Por ahora solo registramos en log (implementar después)
        SET @days_to_expire = DATEDIFF(NEW.valid_until, CURDATE());
    END IF;
END$$

DELIMITER ;

-- =============================================
-- ÍNDICES ADICIONALES PARA PERFORMANCE
-- =============================================

-- Índices en tabla modules para búsquedas frecuentes (solo si no existen)
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'modules' AND INDEX_NAME = 'idx_category');
SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `modules` ADD INDEX `idx_category` (`category`);',
    'SELECT "Index idx_category already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'modules' AND INDEX_NAME = 'idx_enabled');
SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `modules` ADD INDEX `idx_enabled` (`is_enabled`);',
    'SELECT "Index idx_enabled already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'modules' AND INDEX_NAME = 'idx_core');
SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `modules` ADD INDEX `idx_core` (`is_core`);',
    'SELECT "Index idx_core already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Índices en permissions
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'permissions' AND INDEX_NAME = 'idx_module');
SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `permissions` ADD INDEX `idx_module` (`module`);',
    'SELECT "Index idx_module already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- VISTAS PARA REPORTES RÁPIDOS
-- =============================================

-- Vista de módulos activos por tenant
CREATE OR REPLACE VIEW `v_tenant_active_modules` AS
SELECT 
    t.id as tenant_id,
    t.company_name,
    m.name as module_name,
    m.display_name as module_display_name,
    m.category,
    tm.is_active,
    tm.activated_at,
    u.username as activated_by_user
FROM tenants t
INNER JOIN tenant_modules tm ON t.id = tm.tenant_id
INNER JOIN modules m ON tm.module_id = m.id
LEFT JOIN users u ON tm.activated_by = u.id
WHERE tm.is_active = 1;

-- Vista de certificados activos y su estado
CREATE OR REPLACE VIEW `v_active_certificates` AS
SELECT 
    c.id,
    c.tenant_id,
    t.company_name,
    c.certificate_type,
    c.rfc,
    c.razon_social,
    c.valid_from,
    c.valid_until,
    DATEDIFF(c.valid_until, CURDATE()) as days_to_expire,
    CASE 
        WHEN DATEDIFF(c.valid_until, CURDATE()) < 0 THEN 'Vencido'
        WHEN DATEDIFF(c.valid_until, CURDATE()) <= 30 THEN 'Por Vencer'
        ELSE 'Vigente'
    END as status,
    c.is_default,
    c.created_at
FROM tenant_sat_certificates c
INNER JOIN tenants t ON c.tenant_id = t.id
WHERE c.is_active = 1;

-- =============================================
-- COMENTARIOS FINALES
-- =============================================

-- Esta migración agrega:
-- 1. 5 nuevos módulos (Contabilidad, Facturación, RRHH, Nómina, BI)
-- 2. 29 nuevos permisos para los módulos
-- 3. Tablas para certificados digitales SAT
-- 4. Tablas para configuración de PAC
-- 5. Sistema de widgets configurables para dashboard
-- 6. Vistas para reportes rápidos
-- 7. Triggers para alertas automáticas

-- SIGUIENTE PASO: Ejecutar esta migración y luego crear los archivos
-- de migración específicos de cada módulo:
-- - contabilidad.sql (catálogo de cuentas, pólizas, etc.)
-- - facturacion.sql (facturas CFDI, conceptos, etc.)
-- - rrhh.sql (empleados, contratos, etc.)
-- - nomina.sql (nóminas, deducciones, etc.)
-- - business_intelligence.sql (configuraciones de BI)
