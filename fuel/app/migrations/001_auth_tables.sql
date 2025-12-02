-- ============================================================================
-- SQL para Sistema de Autenticación y Permisos
-- FuelPHP ERP Multi-tenant
-- ============================================================================
-- 
-- INSTRUCCIONES:
-- 1. Ejecutar primero las tablas en la base de datos MASTER (erp_master)
--    - Solo la tabla 'tenants' va en la base master
-- 
-- 2. Ejecutar las tablas de usuarios en CADA base de datos de TENANT
--    - Tablas: users, users_metadata, user_permissions, password_resets
--
-- ============================================================================

-- ============================================================================
-- PARTE 1: TABLA MASTER (ejecutar en erp_master)
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Tabla: tenants
-- Almacena la información de cada tenant/cliente del sistema
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tenants` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `domain` VARCHAR(255) NOT NULL COMMENT 'Dominio o subdominio del tenant (ej: cliente1.example.com)',
    `db_name` VARCHAR(100) NOT NULL COMMENT 'Nombre de la base de datos del tenant',
    `active_modules` JSON DEFAULT NULL COMMENT 'Array JSON de módulos activos para el tenant',
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=activo, 0=inactivo',
    `company_name` VARCHAR(255) DEFAULT NULL COMMENT 'Nombre de la empresa del tenant',
    `plan_type` VARCHAR(50) DEFAULT 'basic' COMMENT 'Tipo de plan: basic, professional, enterprise',
    `max_users` INT(11) UNSIGNED DEFAULT 5 COMMENT 'Número máximo de usuarios permitidos',
    `expires_at` DATETIME DEFAULT NULL COMMENT 'Fecha de expiración del plan',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_domain` (`domain`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de tenants para multi-tenancy';

-- Insertar tenant de ejemplo (opcional)
-- INSERT INTO `tenants` (`domain`, `db_name`, `active_modules`, `is_active`, `company_name`) VALUES
-- ('localhost', 'erp_tenant_demo', '["admin", "sellers", "clients", "store", "landing"]', 1, 'Demo Company');


-- ============================================================================
-- PARTE 2: TABLAS DE TENANT (ejecutar en cada base de datos de tenant)
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Tabla: users
-- Almacena los usuarios del sistema
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL COMMENT 'Nombre de usuario único',
    `password` VARCHAR(255) NOT NULL COMMENT 'Contraseña hasheada',
    `group_id` INT(11) UNSIGNED NOT NULL DEFAULT 10 COMMENT 'ID del grupo/rol del usuario',
    `email` VARCHAR(255) NOT NULL COMMENT 'Email único del usuario',
    `first_name` VARCHAR(100) DEFAULT NULL,
    `last_name` VARCHAR(100) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL COMMENT 'Ruta a la imagen de perfil',
    `last_login` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Timestamp del último login',
    `previous_login` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Timestamp del login anterior',
    `login_hash` VARCHAR(255) DEFAULT NULL COMMENT 'Hash de sesión para verificación',
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=activo, 0=inactivo',
    `is_verified` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1=email verificado',
    `verification_token` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_username` (`username`),
    UNIQUE KEY `idx_email` (`email`),
    KEY `idx_group_id` (`group_id`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuarios del sistema';

-- -----------------------------------------------------------------------------
-- Tabla: users_metadata
-- Almacena información adicional del usuario en formato clave-valor
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users_metadata` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `meta_key` VARCHAR(100) NOT NULL,
    `meta_value` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_user_meta_key` (`user_id`, `meta_key`),
    KEY `idx_user_id` (`user_id`),
    CONSTRAINT `fk_users_metadata_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Metadata adicional de usuarios';

-- -----------------------------------------------------------------------------
-- Tabla: groups
-- Define los grupos/roles disponibles en el sistema
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `groups` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL COMMENT 'Nombre del grupo',
    `slug` VARCHAR(100) NOT NULL COMMENT 'Identificador único del grupo',
    `description` TEXT DEFAULT NULL,
    `level` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Nivel de jerarquía (mayor = más permisos)',
    `is_system` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1=grupo del sistema (no editable)',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Grupos/Roles del sistema';

-- Insertar grupos por defecto
INSERT INTO `groups` (`id`, `name`, `slug`, `description`, `level`, `is_system`) VALUES
(100, 'Super Admin', 'super_admin', 'Administrador supremo con acceso total al sistema', 100, 1),
(50, 'Administrador', 'admin', 'Administrador del tenant con acceso administrativo completo', 50, 1),
(40, 'Gerente', 'manager', 'Gerente con acceso a reportes y gestión', 40, 1),
(30, 'Vendedor', 'seller', 'Vendedor con acceso al módulo de ventas', 30, 1),
(25, 'Proveedor', 'provider', 'Proveedor con acceso al portal de proveedores', 25, 1),
(20, 'Socio', 'partner', 'Socio comercial con acceso al portal de partners', 20, 1),
(10, 'Cliente', 'client', 'Cliente con acceso al portal de clientes y tienda', 10, 1),
(0, 'Invitado', 'guest', 'Usuario sin autenticar con acceso mínimo', 0, 1);

-- -----------------------------------------------------------------------------
-- Tabla: permissions
-- Define los permisos disponibles en el sistema
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `area` VARCHAR(100) NOT NULL COMMENT 'Área o módulo (ej: admin, sellers, clients)',
    `permission` VARCHAR(100) NOT NULL COMMENT 'Permiso específico (ej: access, create, edit, delete)',
    `description` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_area_permission` (`area`, `permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permisos del sistema';

-- Insertar permisos por defecto
INSERT INTO `permissions` (`area`, `permission`, `description`) VALUES
-- Admin module
('admin', 'access', 'Acceso al módulo de administración'),
('admin', 'users', 'Gestión de usuarios'),
('admin', 'settings', 'Configuración del sistema'),
('admin', 'reports', 'Acceso a reportes'),
-- Sellers module
('sellers', 'access', 'Acceso al módulo de vendedores'),
('sellers', 'view', 'Ver información de ventas'),
('sellers', 'create', 'Crear ventas'),
('sellers', 'edit', 'Editar ventas'),
('sellers', 'delete', 'Eliminar ventas'),
('sellers', 'sales', 'Gestión de ventas'),
('sellers', 'customers', 'Gestión de clientes'),
('sellers', 'quotes', 'Gestión de cotizaciones'),
('sellers', 'commissions', 'Ver comisiones'),
-- Clients module
('clients', 'access', 'Acceso al portal de clientes'),
('clients', 'view', 'Ver información de clientes'),
('clients', 'create', 'Crear clientes'),
('clients', 'edit', 'Editar clientes'),
('clients', 'delete', 'Eliminar clientes'),
('clients', 'orders', 'Ver pedidos'),
('clients', 'profile', 'Editar perfil'),
('clients', 'support', 'Acceso a soporte'),
-- Providers module
('providers', 'access', 'Acceso al portal de proveedores'),
('providers', 'view', 'Ver información de proveedores'),
('providers', 'create', 'Crear proveedores'),
('providers', 'edit', 'Editar proveedores'),
('providers', 'delete', 'Eliminar proveedores'),
('providers', 'products', 'Gestión de productos'),
('providers', 'inventory', 'Gestión de inventario'),
('providers', 'orders', 'Gestión de pedidos'),
-- Partners module
('partners', 'access', 'Acceso al portal de socios'),
('partners', 'view', 'Ver información de socios'),
('partners', 'create', 'Crear socios'),
('partners', 'edit', 'Editar socios'),
('partners', 'delete', 'Eliminar socios'),
('partners', 'alliances', 'Gestión de alianzas'),
('partners', 'contracts', 'Gestión de contratos'),
('partners', 'commissions', 'Ver comisiones'),
-- Store module
('store', 'access', 'Acceso a la tienda'),
('store', 'view', 'Ver productos'),
('store', 'checkout', 'Realizar compras'),
('store', 'manage', 'Gestionar tienda'),
-- Landing
('landing', 'access', 'Acceso a landing page');

-- -----------------------------------------------------------------------------
-- Tabla: group_permissions
-- Relaciona grupos con permisos
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `group_permissions` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_id` INT(11) UNSIGNED NOT NULL,
    `permission_id` INT(11) UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_group_permission` (`group_id`, `permission_id`),
    KEY `idx_group_id` (`group_id`),
    KEY `idx_permission_id` (`permission_id`),
    CONSTRAINT `fk_group_permissions_group` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_group_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relación grupos-permisos';

-- -----------------------------------------------------------------------------
-- Tabla: user_permissions
-- Permisos específicos para usuarios (override de grupo)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_permissions` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `permission_id` INT(11) UNSIGNED NOT NULL,
    `granted` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=otorgado, 0=denegado',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_user_permission` (`user_id`, `permission_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_permission_id` (`permission_id`),
    CONSTRAINT `fk_user_permissions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_user_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permisos específicos de usuario';

-- -----------------------------------------------------------------------------
-- Tabla: password_resets
-- Tokens para recuperación de contraseña
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME NOT NULL,
    `used_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_email` (`email`),
    KEY `idx_token` (`token`),
    KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tokens de reset de contraseña';

-- -----------------------------------------------------------------------------
-- Tabla: user_sessions
-- Registro de sesiones de usuario
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `session_id` VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `payload` TEXT DEFAULT NULL,
    `last_activity` INT(11) UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_session_id` (`session_id`),
    KEY `idx_last_activity` (`last_activity`),
    CONSTRAINT `fk_user_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sesiones activas de usuarios';

-- -----------------------------------------------------------------------------
-- Tabla: activity_log
-- Registro de actividad del usuario (auditoría)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_log` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL COMMENT 'Acción realizada (login, logout, create, update, delete)',
    `module` VARCHAR(100) DEFAULT NULL COMMENT 'Módulo donde se realizó la acción',
    `entity_type` VARCHAR(100) DEFAULT NULL COMMENT 'Tipo de entidad afectada',
    `entity_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'ID de la entidad afectada',
    `description` TEXT DEFAULT NULL COMMENT 'Descripción de la acción',
    `old_values` JSON DEFAULT NULL COMMENT 'Valores anteriores (para updates)',
    `new_values` JSON DEFAULT NULL COMMENT 'Valores nuevos (para updates/creates)',
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_action` (`action`),
    KEY `idx_module` (`module`),
    KEY `idx_entity` (`entity_type`, `entity_id`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_activity_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de actividad para auditoría';


-- ============================================================================
-- DATOS INICIALES (ejecutar en base de datos de tenant)
-- ============================================================================

-- Crear usuario administrador por defecto
-- Contraseña: admin123 (CAMBIAR EN PRODUCCIÓN)
-- El hash se genera con: sha256(sha256('admin123') . 'ERP_MULTI_TENANT_SALT_CHANGE_ME_IN_PRODUCTION')
INSERT INTO `users` (`username`, `password`, `group_id`, `email`, `first_name`, `last_name`, `is_active`, `is_verified`) VALUES
('admin', 'GENERATE_HASH_WITH_PHP', 50, 'admin@example.com', 'Administrador', 'Sistema', 1, 1);

-- NOTA: Para generar el hash de contraseña correctamente, usar PHP:
-- <?php
-- $password = 'admin123';
-- $salt = 'ERP_MULTI_TENANT_SALT_CHANGE_ME_IN_PRODUCTION';
-- $hash = base64_encode(hash_pbkdf2('sha256', hash('sha256', $password), $salt, 10000, 32, true));
-- echo $hash;


-- ============================================================================
-- PROCEDIMIENTOS ALMACENADOS (opcional)
-- ============================================================================

-- Procedimiento para limpiar sesiones expiradas
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `clean_expired_sessions`()
BEGIN
    -- Eliminar sesiones inactivas por más de 24 horas
    DELETE FROM `user_sessions` WHERE `last_activity` < (UNIX_TIMESTAMP() - 86400);
    
    -- Eliminar tokens de password reset expirados
    DELETE FROM `password_resets` WHERE `expires_at` < NOW();
END //
DELIMITER ;


-- ============================================================================
-- VISTAS (opcional)
-- ============================================================================

-- Vista de usuarios con información de grupo
CREATE OR REPLACE VIEW `v_users_with_groups` AS
SELECT 
    u.id,
    u.username,
    u.email,
    u.first_name,
    u.last_name,
    u.group_id,
    g.name AS group_name,
    g.slug AS group_slug,
    g.level AS group_level,
    u.is_active,
    u.is_verified,
    u.last_login,
    u.created_at
FROM `users` u
LEFT JOIN `groups` g ON u.group_id = g.id
WHERE u.deleted_at IS NULL;

-- Vista de permisos por grupo
CREATE OR REPLACE VIEW `v_group_permissions_list` AS
SELECT 
    g.id AS group_id,
    g.name AS group_name,
    g.slug AS group_slug,
    p.area,
    p.permission,
    p.description AS permission_description
FROM `groups` g
JOIN `group_permissions` gp ON g.id = gp.group_id
JOIN `permissions` p ON gp.permission_id = p.id
ORDER BY g.level DESC, p.area, p.permission;
