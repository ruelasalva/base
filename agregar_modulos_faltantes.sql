-- Agregar módulos faltantes
USE base;

INSERT INTO modules (name, display_name, description, icon, category, is_core, is_enabled, has_migration, menu_order, created_at, updated_at) VALUES
('sat', 'Catálogos SAT', 'Catálogos del SAT (productos, servicios, unidades, uso CFDI)', 'fa-file-invoice', 'contabilidad', 0, 1, 0, 26, NOW(), NOW()),
('proveedores', 'Proveedores', 'Gestión de proveedores y contactos', 'fa-truck-field', 'compras', 0, 1, 0, 27, NOW(), NOW()),
('precotizacion', 'Precotizaciones', 'Precotizaciones y propuestas', 'fa-file-lines', 'sales', 0, 1, 0, 28, NOW(), NOW()),
('socios', 'Socios', 'Gestión de socios de negocio', 'fa-handshake', 'sales', 0, 1, 0, 29, NOW(), NOW()),
('cupones', 'Cupones y Descuentos', 'Códigos de descuento y promociones', 'fa-ticket', 'marketing', 0, 1, 0, 30, NOW(), NOW()),
('banners', 'Banners', 'Gestión de banners publicitarios', 'fa-image', 'marketing', 0, 1, 0, 31, NOW(), NOW()),
('slides', 'Slides', 'Carrusel de imágenes principal', 'fa-images', 'marketing', 0, 1, 0, 32, NOW(), NOW()),
('editordiseno', 'Editor de Diseño', 'Editor visual de páginas', 'fa-paint-brush', 'marketing', 0, 1, 0, 33, NOW(), NOW()),
('contrarecibos', 'Contrarecibos', 'Gestión de contrarecibos', 'fa-receipt', 'compras', 0, 1, 0, 34, NOW(), NOW()),
('abandonados', 'Carritos Abandonados', 'Seguimiento de carritos abandonados', 'fa-cart-arrow-down', 'sales', 0, 1, 0, 35, NOW(), NOW()),
('deseados', 'Lista de Deseados', 'Productos en lista de deseos', 'fa-heart', 'sales', 0, 1, 0, 36, NOW(), NOW()),
('empleados', 'Empleados', 'Gestión de empleados', 'fa-id-card', 'rrhh', 0, 1, 0, 37, NOW(), NOW()),
('administradores', 'Administradores', 'Gestión de usuarios admin', 'fa-user-shield', 'core', 0, 1, 0, 38, NOW(), NOW()),
('bbva', 'Integración BBVA', 'Conexión con servicios BBVA', 'fa-building-columns', 'finanzas', 0, 1, 0, 39, NOW(), NOW()),
('perfil', 'Perfil de Usuario', 'Gestión de perfil personal', 'fa-user-circle', 'core', 0, 1, 0, 40, NOW(), NOW()),
('roles_permisos', 'Roles y Permisos', 'Gestión de roles y permisos', 'fa-shield-halved', 'core', 0, 1, 0, 41, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at=NOW();

-- Activar para el tenant
INSERT IGNORE INTO tenant_modules (tenant_id, module_id, is_active, activated_at) 
SELECT 1, id, 1, NOW() FROM modules 
WHERE name IN ('sat','proveedores','precotizacion','socios','cupones','banners','slides','editordiseno','contrarecibos','abandonados','deseados','empleados','administradores','bbva','perfil','roles_permisos')
AND is_enabled=1;

-- Ver resumen
SELECT category, COUNT(*) as total FROM modules WHERE is_enabled=1 GROUP BY category ORDER BY category;
