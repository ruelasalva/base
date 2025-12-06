-- Script para agregar permisos del módulo de Facturación Electrónica
-- Ejecutar después de instalar el sistema de facturación

-- Verificar si el módulo existe en la tabla modules
INSERT INTO modules (name, slug, is_active, created_at, updated_at)
VALUES ('Facturación Electrónica', 'facturacion', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    name = 'Facturación Electrónica',
    is_active = 1,
    updated_at = NOW();

-- Obtener el ID del módulo recién creado o existente
SET @module_id = (SELECT id FROM modules WHERE slug = 'facturacion' LIMIT 1);

-- Si existe tabla permissions (sistema de permisos granulares)
-- Agregar permisos para el usuario administrador (user_id = 1)
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at)
VALUES 
    (1, 'facturacion', 1, 1, 1, 1, NOW())
ON DUPLICATE KEY UPDATE
    can_view = 1,
    can_create = 1,
    can_edit = 1,
    can_delete = 1;

-- Agregar acción especial para timbrado
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at)
VALUES 
    (1, 'facturacion_timbrar', 1, 0, 0, 0, NOW())
ON DUPLICATE KEY UPDATE
    can_view = 1;

-- Agregar acción para cancelación
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at)
VALUES 
    (1, 'facturacion_cancelar', 1, 0, 0, 0, NOW())
ON DUPLICATE KEY UPDATE
    can_view = 1;

-- Agregar acción para configuración
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at)
VALUES 
    (1, 'facturacion_configuracion', 1, 0, 1, 0, NOW())
ON DUPLICATE KEY UPDATE
    can_view = 1,
    can_edit = 1;

-- Crear entrada en el menú de administración (si existe tabla admin_menu)
-- Agregar al menú principal
INSERT INTO admin_menu (parent_id, title, url, icon, order_num, is_active, created_at)
VALUES 
    (NULL, 'Facturación', '#', 'fa-file-invoice', 30, 1, NOW())
ON DUPLICATE KEY UPDATE
    title = 'Facturación',
    icon = 'fa-file-invoice',
    is_active = 1;

SET @menu_facturacion_id = LAST_INSERT_ID();

-- Submenús
INSERT INTO admin_menu (parent_id, title, url, icon, order_num, is_active, created_at)
VALUES 
    (@menu_facturacion_id, 'Facturas', '/admin/facturacion', 'fa-list', 1, 1, NOW()),
    (@menu_facturacion_id, 'Nueva Factura', '/admin/facturacion/create', 'fa-plus', 2, 1, NOW()),
    (@menu_facturacion_id, 'Configuración', '/admin/facturacion/configuracion', 'fa-cog', 3, 1, NOW())
ON DUPLICATE KEY UPDATE
    is_active = 1;

-- Mensaje de confirmación
SELECT 'Permisos de Facturación configurados correctamente' AS Resultado;

-- Verificar permisos creados
SELECT 
    p.id,
    p.user_id,
    p.resource,
    p.can_view,
    p.can_create,
    p.can_edit,
    p.can_delete
FROM permissions p
WHERE p.resource LIKE 'facturacion%'
ORDER BY p.resource;
