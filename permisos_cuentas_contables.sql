-- =============================================
-- PERMISOS PARA MÓDULO: CUENTAS CONTABLES
-- Fecha: 5 de diciembre de 2025
-- =============================================

-- Verificar módulo
SELECT 'Módulo encontrado:' as info;
SELECT id, name, display_name FROM modules WHERE name = 'cuentas_contables';

-- ========== PERMISOS PARA CUENTAS CONTABLES ==========
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at) VALUES
('cuentas_contables', 'view', 'Ver Cuentas Contables', 'Permite ver el catálogo de cuentas contables', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('cuentas_contables', 'create', 'Crear Cuentas Contables', 'Permite crear nuevas cuentas en el catálogo', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('cuentas_contables', 'edit', 'Editar Cuentas Contables', 'Permite modificar cuentas existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('cuentas_contables', 'delete', 'Eliminar Cuentas Contables', 'Permite eliminar cuentas (sin subcuentas)', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE updated_at = UNIX_TIMESTAMP();

-- Verificar permisos creados
SELECT 'Permisos creados:' as info;
SELECT p.id, p.module, p.action, p.name 
FROM permissions p 
WHERE p.module = 'cuentas_contables'
ORDER BY p.id;

-- ========== ASIGNAR PERMISOS AL ROL ADMIN ==========
SET @admin_role_id = 1;

INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT @admin_role_id, p.id, UNIX_TIMESTAMP()
FROM permissions p
WHERE p.module = 'cuentas_contables'
ON DUPLICATE KEY UPDATE created_at = UNIX_TIMESTAMP();

-- ========== VERIFICACIÓN ==========
SELECT 'Asignación a Admin:' as info;
SELECT 
    p.action as permiso,
    p.name,
    CASE WHEN rp.role_id IS NOT NULL THEN 'Asignado' ELSE 'NO Asignado' END as estado
FROM permissions p
LEFT JOIN role_permissions rp ON p.id = rp.permission_id AND rp.role_id = @admin_role_id
WHERE p.module = 'cuentas_contables'
ORDER BY p.action;

-- ========== RESUMEN ==========
SELECT 
    'RESUMEN' as tipo,
    (SELECT COUNT(*) FROM permissions WHERE module = 'cuentas_contables') as permisos_creados,
    (SELECT COUNT(*) FROM role_permissions WHERE role_id = @admin_role_id AND permission_id IN (
        SELECT id FROM permissions WHERE module = 'cuentas_contables'
    )) as permisos_asignados_admin;
