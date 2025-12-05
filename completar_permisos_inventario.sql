-- ========================================
-- COMPLETAR PERMISOS DEL MÓDULO INVENTARIO
-- Fecha: 5 de Diciembre 2025
-- ========================================

USE base;

-- Agregar permisos faltantes al módulo inventario
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at) VALUES
('inventario', 'create', 'Crear Movimientos', 'Crear nuevos movimientos de inventario', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('inventario', 'delete', 'Eliminar Movimientos', 'Eliminar movimientos en borrador', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('inventario', 'approve', 'Aprobar Movimientos', 'Aprobar movimientos pendientes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('inventario', 'apply', 'Aplicar Movimientos', 'Aplicar movimientos aprobados al inventario', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    updated_at = UNIX_TIMESTAMP();

-- Obtener IDs de permisos
SET @perm_view = (SELECT id FROM permissions WHERE module = 'inventario' AND action = 'view');
SET @perm_create = (SELECT id FROM permissions WHERE module = 'inventario' AND action = 'create');
SET @perm_edit = (SELECT id FROM permissions WHERE module = 'inventario' AND action = 'edit');
SET @perm_delete = (SELECT id FROM permissions WHERE module = 'inventario' AND action = 'delete');
SET @perm_approve = (SELECT id FROM permissions WHERE module = 'inventario' AND action = 'approve');
SET @perm_apply = (SELECT id FROM permissions WHERE module = 'inventario' AND action = 'apply');

-- Asignar permisos al rol Admin (role_id = 1)
INSERT IGNORE INTO role_permissions (role_id, permission_id, created_at) VALUES
(1, @perm_view, UNIX_TIMESTAMP()),
(1, @perm_create, UNIX_TIMESTAMP()),
(1, @perm_edit, UNIX_TIMESTAMP()),
(1, @perm_delete, UNIX_TIMESTAMP()),
(1, @perm_approve, UNIX_TIMESTAMP()),
(1, @perm_apply, UNIX_TIMESTAMP());

-- Verificar permisos creados
SELECT 
    p.id,
    p.module,
    p.action,
    p.name,
    p.description,
    CASE WHEN rp.role_id IS NOT NULL THEN 'Asignado a Admin' ELSE 'No asignado' END as admin_access
FROM permissions p
LEFT JOIN role_permissions rp ON p.id = rp.permission_id AND rp.role_id = 1
WHERE p.module = 'inventario'
ORDER BY p.id;

-- Resumen
SELECT 
    'Permisos del módulo inventario' as descripcion,
    COUNT(*) as total_permisos
FROM permissions 
WHERE module = 'inventario';

SELECT 
    'Permisos asignados al rol Admin' as descripcion,
    COUNT(*) as total_asignados
FROM role_permissions rp
INNER JOIN permissions p ON rp.permission_id = p.id
WHERE rp.role_id = 1 AND p.module = 'inventario';

-- ✅ COMPLETADO
