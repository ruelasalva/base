-- =============================================
-- MIGRACIÓN 008B: COMPLETAR MÓDULOS DE NEGOCIO
-- Solo las partes que faltan (permisos, tablas, etc.)
-- =============================================

-- =============================================
-- ACTUALIZAR MÓDULOS EXISTENTES
-- =============================================

-- Actualizar descripción de Finance
UPDATE `modules` 
SET `description` = 'Flujo de efectivo, cuentas por cobrar/pagar, bancos y proyecciones financieras',
    `requires_modules` = NULL
WHERE `name` = 'finance';

-- Actualizar Sales
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

INSERT IGNORE INTO `permissions` (`module`, `action`, `description`) VALUES

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

SET @super_admin_role = (SELECT id FROM roles WHERE name = 'super_admin' LIMIT 1);

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT @super_admin_role, p.id
FROM `permissions` p
WHERE p.module IN ('contabilidad', 'facturacion', 'rrhh', 'nomina', 'bi');

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
INSERT IGNORE INTO `dashboard_widgets` (`widget_key`, `widget_name`, `widget_description`, `widget_type`, `requires_modules`, `default_size`, `icon`, `category`, `is_configurable`, `refresh_interval`) VALUES

-- Widgets generales
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
-- VISTAS PARA REPORTES RÁPIDOS
-- =============================================

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

SELECT 'Migración 008B completada exitosamente!' as resultado;
