-- Permisos para el módulo Libro Mayor
-- Module ID: 66 (libro_mayor)

-- Verificar que el módulo existe
SELECT id, name FROM modules WHERE name = 'libro_mayor';

-- 1. Permiso: Ver Libro Mayor
INSERT INTO permissions (module, action, description, created_at, updated_at) 
VALUES ('libro_mayor', 'view', 'Consultar libro mayor y saldos', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 2. Permiso: Exportar Reportes
INSERT INTO permissions (module, action, description, created_at, updated_at) 
VALUES ('libro_mayor', 'export', 'Exportar libro mayor a CSV/Excel', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- Asignar permisos al rol de Administrador (role_id = 1)
INSERT INTO role_permissions (role_id, permission_id, created_at)
SELECT 1, p.id, UNIX_TIMESTAMP()
FROM permissions p 
WHERE p.module = 'libro_mayor'
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = 1 AND rp.permission_id = p.id
);

-- Verificar permisos creados
SELECT p.id, p.module, p.action, p.description 
FROM permissions p 
WHERE p.module = 'libro_mayor'
ORDER BY p.action;

-- Verificar asignación a rol Admin
SELECT rp.id, r.name as role, p.module, p.action
FROM role_permissions rp
INNER JOIN roles r ON rp.role_id = r.id
INNER JOIN permissions p ON rp.permission_id = p.id
WHERE p.module = 'libro_mayor'
ORDER BY p.action;
