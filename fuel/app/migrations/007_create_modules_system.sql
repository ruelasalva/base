-- =============================================
-- MIGRATION 007: Sistema de Módulos y Configuración
-- =============================================
-- Fecha: 2025-12-02
-- Descripción: Crea sistema de módulos habilitables,
--              preferencias de usuario (templates),
--              y configuración completa del sistema

-- =============================================
-- 1. TABLA: modules
-- Sistema de módulos habilitables dinámicamente
-- =============================================
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT 'Nombre interno del módulo (ej: accounting)',
  `display_name` varchar(100) NOT NULL COMMENT 'Nombre visible (ej: Contabilidad)',
  `description` text COMMENT 'Descripción del módulo',
  `icon` varchar(50) DEFAULT 'fa-cube' COMMENT 'Icono FontAwesome',
  `category` enum('core','business','sales','marketing','system') DEFAULT 'business',
  `is_core` tinyint(1) DEFAULT 0 COMMENT '1=No se puede desactivar',
  `is_enabled` tinyint(1) DEFAULT 0 COMMENT '1=Módulo activo',
  `has_migration` tinyint(1) DEFAULT 0 COMMENT '1=Tiene migración de BD',
  `migration_file` varchar(255) DEFAULT NULL COMMENT 'Archivo de migración',
  `version` varchar(20) DEFAULT '1.0.0',
  `requires_modules` text COMMENT 'JSON con módulos requeridos',
  `config_schema` text COMMENT 'JSON con esquema de configuración',
  `menu_order` int(11) DEFAULT 999,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_module_name` (`name`),
  KEY `idx_enabled` (`is_enabled`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Módulos del sistema';

-- =============================================
-- 2. TABLA: tenant_modules
-- Módulos activos por tenant (multi-tenant)
-- =============================================
CREATE TABLE IF NOT EXISTS `tenant_modules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL,
  `module_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `config` text COMMENT 'JSON con configuración específica del tenant',
  `activated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `activated_by` int(11) unsigned DEFAULT NULL COMMENT 'User ID que activó',
  `deactivated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tenant_module` (`tenant_id`,`module_id`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_module` (`module_id`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_tenant_modules_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tenant_modules_module` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Módulos activos por tenant';

-- =============================================
-- 3. TABLA: user_preferences
-- Preferencias de usuario (template, idioma, etc.)
-- =============================================
CREATE TABLE IF NOT EXISTS `user_preferences` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `tenant_id` int(11) unsigned DEFAULT NULL COMMENT 'NULL = global',
  `template_theme` enum('coreui','adminlte','argon') DEFAULT 'coreui',
  `sidebar_collapsed` tinyint(1) DEFAULT 0,
  `language` varchar(10) DEFAULT 'es',
  `timezone` varchar(50) DEFAULT 'America/Mexico_City',
  `items_per_page` int(11) DEFAULT 20,
  `dashboard_widgets` text COMMENT 'JSON con widgets activos',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_tenant_prefs` (`user_id`,`tenant_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_tenant` (`tenant_id`),
  CONSTRAINT `fk_user_prefs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Preferencias de usuario';

-- =============================================
-- 4. AMPLIAR TABLA: tenant_site_config
-- Configuración completa del sitio (ya existe, agregar campos)
-- =============================================
-- Verificar si las columnas ya existen antes de agregarlas
SET @dbname = DATABASE();
SET @tablename = 'tenant_site_config';

-- Logo y Branding
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'site_logo');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `site_logo` varchar(255) DEFAULT NULL COMMENT "URL del logo" AFTER `site_name`;',
    'SELECT "Column site_logo already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'site_favicon');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `site_favicon` varchar(255) DEFAULT NULL COMMENT "URL del favicon" AFTER `site_logo`;',
    'SELECT "Column site_favicon already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- SMTP Configuration
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'smtp_enabled');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `smtp_enabled` tinyint(1) DEFAULT 0 AFTER `fb_pixel_id`;',
    'SELECT "Column smtp_enabled already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'smtp_host');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `smtp_host` varchar(255) DEFAULT NULL AFTER `smtp_enabled`;',
    'SELECT "Column smtp_host already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'smtp_port');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `smtp_port` int(11) DEFAULT 587 AFTER `smtp_host`;',
    'SELECT "Column smtp_port already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'smtp_user');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `smtp_user` varchar(255) DEFAULT NULL AFTER `smtp_port`;',
    'SELECT "Column smtp_user already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'smtp_password');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `smtp_password` varchar(255) DEFAULT NULL AFTER `smtp_user`;',
    'SELECT "Column smtp_password already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'smtp_encryption');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `smtp_encryption` enum("tls","ssl","none") DEFAULT "tls" AFTER `smtp_password`;',
    'SELECT "Column smtp_encryption already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'smtp_from_email');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `smtp_from_email` varchar(255) DEFAULT NULL AFTER `smtp_encryption`;',
    'SELECT "Column smtp_from_email already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'smtp_from_name');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `smtp_from_name` varchar(255) DEFAULT NULL AFTER `smtp_from_email`;',
    'SELECT "Column smtp_from_name already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Social Media
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'social_facebook');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `social_facebook` varchar(255) DEFAULT NULL AFTER `smtp_from_name`;',
    'SELECT "Column social_facebook already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'social_twitter');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `social_twitter` varchar(255) DEFAULT NULL AFTER `social_facebook`;',
    'SELECT "Column social_twitter already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'social_instagram');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `social_instagram` varchar(255) DEFAULT NULL AFTER `social_twitter`;',
    'SELECT "Column social_instagram already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'social_linkedin');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `tenant_site_config` ADD COLUMN `social_linkedin` varchar(255) DEFAULT NULL AFTER `social_instagram`;',
    'SELECT "Column social_linkedin already exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- 5. INSERTAR MÓDULOS BASE
-- =============================================
INSERT INTO `modules` (`name`, `display_name`, `description`, `icon`, `category`, `is_core`, `is_enabled`, `has_migration`, `menu_order`) VALUES
-- CORE (No se pueden desactivar)
('dashboard', 'Dashboard', 'Panel principal con gráficas y estadísticas', 'fa-tachometer-alt', 'core', 1, 1, 0, 1),
('users', 'Usuarios y Roles', 'Gestión de usuarios, roles y permisos RBAC', 'fa-users', 'core', 1, 1, 0, 2),
('config', 'Configuración', 'Configuración del sistema, SEO, Analytics, SMTP', 'fa-cog', 'core', 1, 1, 0, 999),
('tenants', 'Multi-Tenant', 'Gestión de tenants y configuración', 'fa-building', 'core', 1, 1, 0, 998),

-- BUSINESS MODULES (Opcionales)
('accounting', 'Contabilidad', 'Cuentas contables, transacciones, balance general', 'fa-calculator', 'business', 0, 0, 1, 10),
('finance', 'Finanzas', 'Presupuestos, flujo de caja, proyecciones financieras', 'fa-chart-line', 'business', 0, 0, 1, 11),
('inventory', 'Inventario', 'Productos, stock, movimientos de inventario', 'fa-boxes', 'business', 0, 0, 1, 12),
('purchases', 'Compras', 'Órdenes de compra, proveedores, recepciones', 'fa-shopping-basket', 'business', 0, 0, 1, 13),

-- SALES MODULES
('sales', 'Ventas', 'Órdenes de venta, cotizaciones, facturación', 'fa-shopping-cart', 'sales', 0, 0, 1, 20),
('crm', 'CRM', 'Gestión de clientes, seguimiento, oportunidades', 'fa-user-friends', 'sales', 0, 0, 1, 21),
('quotes', 'Cotizaciones', 'Generación y seguimiento de cotizaciones', 'fa-file-invoice', 'sales', 0, 0, 1, 22),

-- MARKETING MODULES
('ecommerce', 'Tienda Online', 'Catálogo público, carrito de compras, checkout', 'fa-store', 'marketing', 0, 0, 1, 30),
('landing', 'Landing Pages', 'Páginas públicas, blog, optimización SEO', 'fa-globe', 'marketing', 0, 0, 1, 31),
('email_marketing', 'Email Marketing', 'Campañas de email, newsletters, automatización', 'fa-envelope', 'marketing', 0, 0, 1, 32),

-- SYSTEM MODULES
('reports', 'Reportes Avanzados', 'Business Intelligence, exportación, dashboards', 'fa-chart-bar', 'system', 0, 0, 1, 40),
('documents', 'Documentos', 'Gestión documental, archivos, versiones', 'fa-file-alt', 'system', 0, 0, 1, 41),
('notifications', 'Notificaciones', 'Sistema de notificaciones push, email, SMS', 'fa-bell', 'system', 0, 0, 1, 42);

-- =============================================
-- 6. CREAR TENANT DEFAULT SI NO EXISTE
-- =============================================
INSERT INTO `tenants` (`id`, `domain`, `db_name`, `company_name`, `is_active`, `plan_type`, `max_users`)
SELECT 1, 'localhost', 'base', 'Empresa Principal', 1, 'enterprise', 999
WHERE NOT EXISTS (SELECT 1 FROM `tenants` WHERE id = 1);

-- =============================================
-- 7. ACTIVAR MÓDULOS CORE PARA TENANT 1
-- =============================================
INSERT INTO `tenant_modules` (`tenant_id`, `module_id`, `is_active`, `activated_by`)
SELECT 1, m.id, 1, 3 
FROM `modules` m
WHERE m.`is_core` = 1
AND NOT EXISTS (SELECT 1 FROM `tenant_modules` tm WHERE tm.tenant_id = 1 AND tm.module_id = m.id);

-- =============================================
-- 8. CREAR PREFERENCIAS DEFAULT PARA ADMIN
-- =============================================
-- =============================================
-- 8. CREAR PREFERENCIAS DEFAULT PARA ADMIN
-- =============================================
INSERT INTO `user_preferences` (`user_id`, `tenant_id`, `template_theme`, `sidebar_collapsed`, `dashboard_widgets`)
SELECT 3, 1, 'coreui', 0, '["stats","recent_sales","charts","quick_actions"]'
WHERE NOT EXISTS (SELECT 1 FROM `user_preferences` WHERE user_id = 3 AND tenant_id = 1);

-- =============================================
-- 9. PERMISOS PARA MÓDULOS
-- =============================================
-- Agregar permisos para gestión de módulos
INSERT INTO `permissions` (`module`, `action`, `name`, `description`) VALUES
('modules', 'view', 'Ver Módulos', 'Ver lista de módulos disponibles'),
('modules', 'enable', 'Habilitar Módulos', 'Activar/desactivar módulos del sistema'),
('modules', 'configure', 'Configurar Módulos', 'Configurar parámetros de módulos');

-- Asignar permisos de módulos solo a super_admin
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id 
FROM `roles` r
CROSS JOIN `permissions` p
WHERE r.name = 'super_admin' 
AND p.module = 'modules'
AND NOT EXISTS (
    SELECT 1 FROM `role_permissions` rp 
    WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

-- =============================================
-- FIN MIGRATION 007
-- =============================================
