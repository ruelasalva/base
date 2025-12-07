-- =============================================
-- PERMISOS PARA MÓDULO DE VENTAS (SALES)
-- =============================================
-- Fecha: 5 de Diciembre 2025
-- Descripción: Crea permisos CRUD para el módulo Sales
--              y los asigna a roles administrativos

USE base;

-- =============================================
-- 1. VERIFICAR QUE EXISTE EL MÓDULO SALES
-- =============================================
SELECT 'Verificando módulo sales...' as status;

SELECT id, name, display_name, is_enabled 
FROM modules 
WHERE name = 'sales';

-- =============================================
-- 2. CREAR PERMISOS PARA SALES
-- =============================================
-- Tabla: permissions
-- Formato: module.action

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at) VALUES
('sales', 'view', 'Ver Ventas', 'Permite ver el listado y detalles de ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sales', 'create', 'Crear Ventas', 'Permite crear nuevas ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sales', 'edit', 'Editar Ventas', 'Permite modificar ventas existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sales', 'delete', 'Cancelar Ventas', 'Permite cancelar ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sales', 'stats', 'Ver Estadísticas', 'Permite ver estadísticas de ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sales', 'export', 'Exportar Ventas', 'Permite exportar reportes de ventas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE 
    name = VALUES(name),
    description = VALUES(description),
    is_active = VALUES(is_active),
    updated_at = UNIX_TIMESTAMP();

-- =============================================
-- 3. ASIGNAR PERMISOS A ROLES
-- =============================================

-- SUPER ADMIN (group_id = 100) - Todos los permisos
INSERT INTO permissions_group (group_id, resource, can_view, can_edit, can_delete, can_create)
VALUES (100, 'sales', 1, 1, 1, 1)
ON DUPLICATE KEY UPDATE 
    can_view = 1, 
    can_edit = 1, 
    can_delete = 1, 
    can_create = 1;

-- ADMIN (group_id = 50) - Todos excepto delete
INSERT INTO permissions_group (group_id, resource, can_view, can_edit, can_delete, can_create)
VALUES (50, 'sales', 1, 1, 0, 1)
ON DUPLICATE KEY UPDATE 
    can_view = 1, 
    can_edit = 1, 
    can_delete = 0, 
    can_create = 1;

-- VENDEDOR (group_id = 25) - Solo view y create
INSERT INTO permissions_group (group_id, resource, can_view, can_edit, can_delete, can_create)
VALUES (25, 'sales', 1, 0, 0, 1)
ON DUPLICATE KEY UPDATE 
    can_view = 1, 
    can_edit = 0, 
    can_delete = 0, 
    can_create = 1;

-- USER (group_id = 1) - Solo view
INSERT INTO permissions_group (group_id, resource, can_view, can_edit, can_delete, can_create)
VALUES (1, 'sales', 1, 0, 0, 0)
ON DUPLICATE KEY UPDATE 
    can_view = 1, 
    can_edit = 0, 
    can_delete = 0, 
    can_create = 0;

-- =============================================
-- 4. VERIFICAR TABLA AUDIT_LOGS
-- =============================================
-- Asegurar que existe la tabla para logs de auditoría

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED DEFAULT 1,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `description` text,
  `old_data` text,
  `new_data` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_module` (`tenant_id`, `module`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_module_action` (`module`, `action`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Logs de auditoría del sistema';

-- =============================================
-- 5. RESUMEN FINAL
-- =============================================
SELECT '=== RESUMEN DE PERMISOS SALES ===' as '╔════════════════════════════╗';

SELECT 
    m.name as modulo,
    m.display_name,
    COUNT(DISTINCT p.id) as total_permisos,
    m.is_enabled as activo
FROM modules m
LEFT JOIN permissions p ON p.module = m.name
WHERE m.name = 'sales'
GROUP BY m.id;

SELECT '=== PERMISOS POR ROL ===' as '╔════════════════════════════╗';

SELECT 
    ug.name as rol,
    pg.resource as modulo,
    pg.can_view as ver,
    pg.can_create as crear,
    pg.can_edit as editar,
    pg.can_delete as eliminar
FROM permissions_group pg
INNER JOIN users_groups ug ON pg.group_id = ug.id
WHERE pg.resource = 'sales'
ORDER BY ug.id;

SELECT 'Permisos de Sales configurados correctamente ✓' as status;
