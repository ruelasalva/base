-- ============================================================================
-- MIGRACIÓN 006: Sistema RBAC (Role-Based Access Control)
-- ============================================================================
-- Descripción: Sistema completo de roles, permisos y control de acceso
-- Fecha: 2025-12-02
-- Autor: Sistema Multi-Tenant
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. TABLA: permissions (Permisos del sistema)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL COMMENT 'Módulo del sistema (sales, products, reports, etc)',
  `action` varchar(50) NOT NULL COMMENT 'Acción (view, create, edit, delete, export, etc)',
  `name` varchar(100) NOT NULL COMMENT 'Nombre descriptivo',
  `description` text DEFAULT NULL COMMENT 'Descripción del permiso',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_permission` (`module`, `action`),
  KEY `idx_module` (`module`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permisos del sistema';

-- ----------------------------------------------------------------------------
-- 2. TABLA: roles (Roles de usuario)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT 'Nombre del rol',
  `display_name` varchar(100) NOT NULL COMMENT 'Nombre para mostrar',
  `description` text DEFAULT NULL COMMENT 'Descripción del rol',
  `level` int(11) NOT NULL DEFAULT 1 COMMENT 'Nivel jerárquico (100=super, 50=admin, etc)',
  `is_system` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Rol del sistema (no editable)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`),
  KEY `idx_level` (`level`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Roles de usuario';

-- ----------------------------------------------------------------------------
-- 3. TABLA: role_permissions (Permisos asignados a roles)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned NOT NULL,
  `permission_id` int(11) unsigned NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role_id`, `permission_id`),
  KEY `idx_role` (`role_id`),
  KEY `idx_permission` (`permission_id`),
  CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_role_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permisos asignados a roles';

-- ----------------------------------------------------------------------------
-- 4. TABLA: user_roles (Roles asignados a usuarios)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  `tenant_id` int(11) unsigned DEFAULT NULL COMMENT 'NULL = todos los tenants',
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_role_tenant` (`user_id`, `role_id`, `tenant_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_role` (`role_id`),
  KEY `idx_tenant` (`tenant_id`),
  CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Roles asignados a usuarios por tenant';

-- ----------------------------------------------------------------------------
-- 5. TABLA: user_tenants (Acceso de usuarios a tenants)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_tenants` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `tenant_id` int(11) unsigned NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Tenant por defecto al login',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_tenant` (`user_id`, `tenant_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_tenant` (`tenant_id`),
  KEY `idx_default` (`is_default`),
  CONSTRAINT `fk_user_tenants_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Acceso de usuarios a múltiples tenants';

-- ============================================================================
-- DATOS INICIALES: Permisos del sistema
-- ============================================================================

INSERT INTO `permissions` (`module`, `action`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
-- Dashboard
('dashboard', 'view', 'Ver Dashboard', 'Acceso al panel principal', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('dashboard', 'stats', 'Ver Estadísticas', 'Ver estadísticas y gráficos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),

-- Configuración
('config', 'view', 'Ver Configuración', 'Ver configuración del sistema', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('config', 'edit', 'Editar Configuración', 'Modificar configuración del sistema', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),

-- Usuarios
('users', 'view', 'Ver Usuarios', 'Listar y ver usuarios', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('users', 'create', 'Crear Usuarios', 'Crear nuevos usuarios', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('users', 'edit', 'Editar Usuarios', 'Modificar usuarios existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('users', 'delete', 'Eliminar Usuarios', 'Eliminar usuarios', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('users', 'roles', 'Gestionar Roles', 'Asignar/quitar roles a usuarios', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),

-- Roles y Permisos
('roles', 'view', 'Ver Roles', 'Listar y ver roles', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('roles', 'create', 'Crear Roles', 'Crear nuevos roles', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('roles', 'edit', 'Editar Roles', 'Modificar roles existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('roles', 'delete', 'Eliminar Roles', 'Eliminar roles', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('roles', 'permissions', 'Gestionar Permisos', 'Asignar/quitar permisos a roles', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),

-- Productos
('products', 'view', 'Ver Productos', 'Listar y ver productos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('products', 'create', 'Crear Productos', 'Crear nuevos productos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('products', 'edit', 'Editar Productos', 'Modificar productos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('products', 'delete', 'Eliminar Productos', 'Eliminar productos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('products', 'import', 'Importar Productos', 'Importar desde Excel/CSV', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('products', 'export', 'Exportar Productos', 'Exportar a Excel/CSV', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),

-- Ventas
('sales', 'view', 'Ver Ventas', 'Listar y ver ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sales', 'create', 'Crear Ventas', 'Crear nuevas ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sales', 'edit', 'Editar Ventas', 'Modificar ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sales', 'delete', 'Eliminar Ventas', 'Eliminar ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sales', 'cancel', 'Cancelar Ventas', 'Cancelar ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sales', 'export', 'Exportar Ventas', 'Exportar ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),

-- Clientes
('customers', 'view', 'Ver Clientes', 'Listar y ver clientes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('customers', 'create', 'Crear Clientes', 'Crear nuevos clientes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('customers', 'edit', 'Editar Clientes', 'Modificar clientes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('customers', 'delete', 'Eliminar Clientes', 'Eliminar clientes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),

-- Reportes
('reports', 'view', 'Ver Reportes', 'Ver reportes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('reports', 'sales', 'Reporte de Ventas', 'Generar reporte de ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('reports', 'products', 'Reporte de Productos', 'Generar reporte de productos', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('reports', 'customers', 'Reporte de Clientes', 'Generar reporte de clientes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('reports', 'export', 'Exportar Reportes', 'Exportar reportes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- ============================================================================
-- DATOS INICIALES: Roles
-- ============================================================================

INSERT INTO `roles` (`name`, `display_name`, `description`, `level`, `is_system`, `is_active`, `created_at`, `updated_at`) VALUES
('super_admin', 'Super Administrador', 'Acceso total al sistema sin restricciones', 100, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('admin', 'Administrador', 'Administrador con acceso completo al tenant', 50, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('manager', 'Gerente', 'Gerente con acceso a reportes y configuración limitada', 40, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('seller', 'Vendedor', 'Vendedor con acceso a ventas y clientes', 30, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('viewer', 'Visualizador', 'Solo puede ver información, sin editar', 10, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- ============================================================================
-- PERMISOS POR ROL: Super Admin (TODOS LOS PERMISOS)
-- ============================================================================

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 
    (SELECT id FROM roles WHERE name = 'super_admin'),
    id,
    UNIX_TIMESTAMP()
FROM permissions;

-- ============================================================================
-- PERMISOS POR ROL: Admin (CASI TODOS excepto gestión de super admins)
-- ============================================================================

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 
    (SELECT id FROM roles WHERE name = 'admin'),
    p.id,
    UNIX_TIMESTAMP()
FROM permissions p
WHERE p.module IN ('dashboard', 'config', 'users', 'roles', 'products', 'sales', 'customers', 'reports');

-- ============================================================================
-- PERMISOS POR ROL: Manager (Reportes, ver usuarios, productos, ventas)
-- ============================================================================

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 
    (SELECT id FROM roles WHERE name = 'manager'),
    p.id,
    UNIX_TIMESTAMP()
FROM permissions p
WHERE 
    (p.module = 'dashboard' AND p.action IN ('view', 'stats'))
    OR (p.module = 'config' AND p.action = 'view')
    OR (p.module = 'users' AND p.action = 'view')
    OR (p.module = 'products')
    OR (p.module = 'sales')
    OR (p.module = 'customers')
    OR (p.module = 'reports');

-- ============================================================================
-- PERMISOS POR ROL: Seller (Ventas, clientes, productos - sin eliminar)
-- ============================================================================

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 
    (SELECT id FROM roles WHERE name = 'seller'),
    p.id,
    UNIX_TIMESTAMP()
FROM permissions p
WHERE 
    (p.module = 'dashboard' AND p.action = 'view')
    OR (p.module = 'products' AND p.action IN ('view', 'export'))
    OR (p.module = 'sales' AND p.action IN ('view', 'create', 'edit', 'export'))
    OR (p.module = 'customers' AND p.action IN ('view', 'create', 'edit'));

-- ============================================================================
-- PERMISOS POR ROL: Viewer (Solo lectura)
-- ============================================================================

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 
    (SELECT id FROM roles WHERE name = 'viewer'),
    p.id,
    UNIX_TIMESTAMP()
FROM permissions p
WHERE p.action = 'view';

-- ============================================================================
-- ASIGNAR ROL SUPER ADMIN AL USUARIO ADMIN
-- ============================================================================

-- Asignar rol super_admin al usuario admin (ID=3)
INSERT INTO `user_roles` (`user_id`, `role_id`, `tenant_id`, `created_at`) VALUES
(3, (SELECT id FROM roles WHERE name = 'super_admin'), NULL, UNIX_TIMESTAMP());

-- Dar acceso al tenant 1
INSERT INTO `user_tenants` (`user_id`, `tenant_id`, `is_default`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- ============================================================================
-- ÍNDICES Y OPTIMIZACIONES
-- ============================================================================

-- Ya están creados en las definiciones de las tablas

-- ============================================================================
-- FIN DE MIGRACIÓN 006
-- ============================================================================
