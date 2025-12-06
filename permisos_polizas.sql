-- Permisos para el módulo Pólizas Contables
-- Module ID: 65 (polizas)

-- Verificar que el módulo existe
SELECT id, name FROM modules WHERE id = 65 AND name = 'polizas';

-- 1. Permiso: Ver Pólizas
INSERT INTO permissions (module, action, description, created_at, updated_at) 
VALUES ('polizas', 'view', 'Ver listado y detalle de pólizas contables', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 2. Permiso: Crear Pólizas
INSERT INTO permissions (module, action, description, created_at, updated_at) 
VALUES ('polizas', 'create', 'Crear nuevas pólizas contables', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 3. Permiso: Editar/Aplicar Pólizas
INSERT INTO permissions (module, action, description, created_at, updated_at) 
VALUES ('polizas', 'edit', 'Aplicar pólizas borradores', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 4. Permiso: Cancelar/Eliminar Pólizas
INSERT INTO permissions (module, action, description, created_at, updated_at) 
VALUES ('polizas', 'delete', 'Cancelar o eliminar pólizas', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- Asignar todos los permisos al rol de Administrador (role_id = 1)
INSERT INTO role_permissions (role_id, module, action, created_at, updated_at)
SELECT 1, module, action, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM permissions 
WHERE module = 'polizas';

-- Verificar permisos creados
SELECT p.id, p.module, p.action, p.description 
FROM permissions p 
WHERE p.module = 'polizas'
ORDER BY p.action;

-- Verificar asignación a rol Admin
SELECT rp.*, r.name as role_name
FROM role_permissions rp
INNER JOIN roles r ON rp.role_id = r.id
WHERE rp.module = 'polizas'
ORDER BY rp.action;
