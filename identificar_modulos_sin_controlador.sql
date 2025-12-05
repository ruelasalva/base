USE base;

-- ===============================================
-- SCRIPT: Redirigir módulos sin implementación al controlador genérico
-- Módulos que apuntarán a /admin/endesarrollo
-- ===============================================

-- Verificar módulos que NO tienen controlador implementado
-- Controladores encontrados: 46
-- Módulos totales: 71
-- Módulos sin controlador: ~25

-- Crear tabla temporal con módulos que necesitan redirección
CREATE TEMPORARY TABLE IF NOT EXISTS modules_sin_controlador AS
SELECT 
    m.id,
    m.name,
    m.display_name,
    m.icon,
    m.category
FROM modules m
WHERE m.name NOT IN (
    -- Módulos con controlador completo
    'dashboard', 'users', 'administradores', 'perfil', 'config', 'tenants',
    'contabilidad', 'facturacion', 'accounting', 'cuentas_contables', 'sat', 'polizas',
    'finance', 'bbva',
    'purchases', 'compras', 'ordenes_compra', 'recepciones', 'proveedores', 'contrarecibos',
    'inventory', 'inventario', 'categorias', 'marcas', 'productos', 'listas_precios', 'almacenes',
    'sales', 'crm', 'quotes', 'cotizaciones', 'precotizacion', 'socios', 'abandonados', 'deseados',
    'rrhh', 'nomina', 'empleados',
    'ecommerce', 'cupones', 'banners', 'landing', 'slides', 'email_marketing', 'editordiseno',
    'business_intelligence', 'autorizaciones', 'reports', 'reportes', 'documents', 'notifications', 'notificaciones',
    'roles_permisos', 'permissions', 'roles',
    'modules', 'system_modules', 'ajax', 'pdf', 'logs', 'ventas', 'usuarios',
    'endesarrollo'
)
AND m.is_enabled = 1;

-- Ver módulos que serán redirigidos
SELECT 
    'MÓDULOS QUE APUNTARÁN AL CONTROLADOR GENÉRICO:' as '';
    
SELECT 
    name as 'Módulo',
    display_name as 'Nombre',
    category as 'Categoría',
    icon as 'Icono'
FROM modules_sin_controlador
ORDER BY category, name;

-- Contar por categoría
SELECT 
    category as 'Categoría',
    COUNT(*) as 'Sin Controlador'
FROM modules_sin_controlador
GROUP BY category
ORDER BY FIELD(category, 'core', 'contabilidad', 'finanzas', 'compras', 'inventario', 'sales', 'rrhh', 'marketing', 'backend', 'integraciones', 'system');

-- NOTA: No actualizamos rutas en base de datos
-- La redirección se maneja mejor en el Helper_Module o en las rutas de FuelPHP
-- Este script es solo informativo para saber qué módulos necesitan el controlador genérico
