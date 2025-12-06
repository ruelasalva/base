-- Script para crear permisos de los nuevos módulos en el menú

-- Permisos para Facturación
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at, updated_at)
VALUES (1, 'facturacion', 1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE can_view = 1, can_create = 1, can_edit = 1, can_delete = 1;

-- Permisos para Catálogos SAT
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at, updated_at)
VALUES (1, 'sat', 1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE can_view = 1, can_create = 1, can_edit = 1, can_delete = 1;

-- Permisos para Contabilidad - Cuentas
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at, updated_at)
VALUES (1, 'cuentas', 1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE can_view = 1, can_create = 1, can_edit = 1, can_delete = 1;

-- Permisos para Pólizas
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at, updated_at)
VALUES (1, 'polizas', 1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE can_view = 1, can_create = 1, can_edit = 1, can_delete = 1;

-- Permisos para Libro Mayor
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at, updated_at)
VALUES (1, 'libromayor', 1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE can_view = 1, can_create = 1, can_edit = 1, can_delete = 1;

-- Permisos para Reportes
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at, updated_at)
VALUES (1, 'reportes', 1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE can_view = 1, can_create = 1, can_edit = 1, can_delete = 1;

-- Permisos para Almacenes
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at, updated_at)
VALUES (1, 'almacenes', 1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE can_view = 1, can_create = 1, can_edit = 1, can_delete = 1;

-- Permisos para Proveedores
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at, updated_at)
VALUES (1, 'proveedores', 1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE can_view = 1, can_create = 1, can_edit = 1, can_delete = 1;

-- Permisos para Marcas
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at, updated_at)
VALUES (1, 'marcas', 1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE can_view = 1, can_create = 1, can_edit = 1, can_delete = 1;

-- Permisos para Categorías
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at, updated_at)
VALUES (1, 'categorias', 1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE can_view = 1, can_create = 1, can_edit = 1, can_delete = 1;

SELECT 'Permisos configurados correctamente' AS Resultado;

-- Verificar todos los permisos creados
SELECT 
    id, user_id, resource, can_view, can_create, can_edit, can_delete
FROM permissions 
WHERE resource IN ('facturacion','sat','cuentas','polizas','libromayor','reportes','almacenes','proveedores','marcas','categorias')
ORDER BY resource;
