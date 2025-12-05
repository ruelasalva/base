-- =============================================
-- PERMISOS PARA MÓDULOS: CATEGORÍAS Y MARCAS
-- Fecha: 5 de diciembre de 2025
-- =============================================

-- Verificar que existan los módulos
SELECT 'Módulos encontrados:' as info;
SELECT id, name, display_name FROM modules WHERE name IN ('categorias', 'marcas');

-- ========== PERMISOS PARA CATEGORÍAS ==========
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at) VALUES
('categorias', 'view', 'Ver Categorías', 'Permite ver el listado y detalle de categorías', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('categorias', 'create', 'Crear Categorías', 'Permite crear nuevas categorías', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('categorias', 'edit', 'Editar Categorías', 'Permite editar categorías existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('categorias', 'delete', 'Eliminar Categorías', 'Permite eliminar categorías', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE updated_at = UNIX_TIMESTAMP();

-- ========== PERMISOS PARA MARCAS ==========
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at) VALUES
('marcas', 'view', 'Ver Marcas', 'Permite ver el listado y detalle de marcas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('marcas', 'create', 'Crear Marcas', 'Permite crear nuevas marcas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('marcas', 'edit', 'Editar Marcas', 'Permite editar marcas existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('marcas', 'delete', 'Eliminar Marcas', 'Permite eliminar marcas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE updated_at = UNIX_TIMESTAMP();

-- Verificar permisos creados
SELECT 'Permisos de Categorías creados:' as info;
SELECT p.id, p.module, p.action, p.name 
FROM permissions p 
WHERE p.module = 'categorias'
ORDER BY p.id;

SELECT 'Permisos de Marcas creados:' as info;
SELECT p.id, p.module, p.action, p.name 
FROM permissions p 
WHERE p.module = 'marcas'
ORDER BY p.id;

-- ========== ASIGNAR PERMISOS AL ROL ADMIN (role_id = 1) ==========

-- Obtener ID del rol Admin
SET @admin_role_id = 1;

-- Asignar permisos de categorías al Admin
INSERT INTO role_permissions (role_id, permission_id, created_at, updated_at)
SELECT @admin_role_id, p.id, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM permissions p
WHERE p.module = 'categorias'
ON DUPLICATE KEY UPDATE updated_at = UNIX_TIMESTAMP();

-- Asignar permisos de marcas al Admin
INSERT INTO role_permissions (role_id, permission_id, created_at, updated_at)
SELECT @admin_role_id, p.id, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM permissions p
WHERE p.module = 'marcas'
ON DUPLICATE KEY UPDATE updated_at = UNIX_TIMESTAMP();

-- ========== VERIFICACIÓN FINAL ==========
SELECT 'Verificación de asignación a Admin:' as info;

SELECT 
    'Categorías' as modulo,
    p.action as permiso,
    p.name,
    CASE WHEN rp.role_id IS NOT NULL THEN 'Asignado a Admin' ELSE 'NO Asignado' END as estado
FROM permissions p
LEFT JOIN role_permissions rp ON p.id = rp.permission_id AND rp.role_id = @admin_role_id
WHERE p.module = 'categorias'
ORDER BY p.action;

SELECT 
    'Marcas' as modulo,
    p.action as permiso,
    p.name,
    CASE WHEN rp.role_id IS NOT NULL THEN 'Asignado a Admin' ELSE 'NO Asignado' END as estado
FROM permissions p
LEFT JOIN role_permissions rp ON p.id = rp.permission_id AND rp.role_id = @admin_role_id
WHERE p.module = 'marcas'
ORDER BY p.action;

-- ========== RESUMEN ==========
SELECT 
    'RESUMEN' as tipo,
    (SELECT COUNT(*) FROM permissions WHERE module = 'categorias') as permisos_categorias,
    (SELECT COUNT(*) FROM permissions WHERE module = 'marcas') as permisos_marcas,
    (SELECT COUNT(*) FROM role_permissions WHERE role_id = @admin_role_id AND permission_id IN (
        SELECT id FROM permissions WHERE module IN ('categorias', 'marcas')
    )) as total_asignados_admin;
