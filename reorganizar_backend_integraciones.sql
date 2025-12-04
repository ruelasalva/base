USE base;

-- ===============================================
-- 1. REORGANIZAR PORTALES A CATEGORÍA BACKEND
-- ===============================================
-- Mover portal de proveedores de 'compras' a 'backend'
UPDATE modules SET 
    category = 'backend',
    menu_order = 70
WHERE name = 'supplier_portal';

-- Mover portal de empleados de 'rrhh' a 'backend'
UPDATE modules SET 
    category = 'backend',
    menu_order = 71
WHERE name = 'employee_portal';

-- Renombrar y reordenar portales existentes
UPDATE modules SET 
    display_name = 'Portal de Clientes',
    menu_order = 69
WHERE name = 'client_portal';

-- ===============================================
-- 2. AGREGAR PORTAL DE SOCIOS (si no existe)
-- ===============================================
INSERT INTO modules (name, display_name, description, icon, category, is_core, is_enabled, has_migration, menu_order, created_at, updated_at) 
VALUES ('partner_portal', 'Portal de Socios', 'Portal web para socios comerciales y distribuidores', 'fa-handshake', 'backend', 0, 1, 0, 72, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    category = 'backend',
    menu_order = 72,
    updated_at = NOW();

-- ===============================================
-- 3. REORDENAR APIs Y BACKEND MOBILE
-- ===============================================
UPDATE modules SET menu_order = 73 WHERE name = 'rest_api';
UPDATE modules SET menu_order = 74 WHERE name = 'graphql_api';
UPDATE modules SET menu_order = 75 WHERE name = 'webhooks';
UPDATE modules SET menu_order = 76 WHERE name = 'mobile_app_backend';

-- ===============================================
-- 4. AGREGAR NUEVA CATEGORÍA "INTEGRACIONES"
-- ===============================================
-- Primero necesitamos agregar 'integraciones' al ENUM
-- Nota: Esto requiere reconstruir el ENUM completo
ALTER TABLE modules 
MODIFY COLUMN category ENUM(
    'core',
    'contabilidad', 
    'finanzas',
    'compras',
    'inventario',
    'sales',
    'rrhh',
    'marketing',
    'backend',
    'integraciones',
    'system'
) NOT NULL DEFAULT 'core';

-- ===============================================
-- 5. INSERTAR MÓDULOS DE INTEGRACIONES
-- ===============================================
INSERT INTO modules (name, display_name, description, icon, category, is_core, is_enabled, has_migration, menu_order, created_at, updated_at) VALUES
-- Marketplaces mexicanos
('integracion_mercadolibre', 'Mercado Libre', 'Integración con Mercado Libre México', 'fa-shopping-bag', 'integraciones', 0, 1, 0, 80, NOW(), NOW()),
('integracion_amazon', 'Amazon México', 'Integración con Amazon Marketplace México', 'fa-amazon', 'integraciones', 0, 1, 0, 81, NOW(), NOW()),

-- Redes sociales y comercio
('integracion_tiktok', 'TikTok Shop', 'Integración con TikTok Shopping', 'fa-video', 'integraciones', 0, 1, 0, 82, NOW(), NOW()),
('integracion_facebook', 'Facebook Shop', 'Integración con Facebook Marketplace y Shops', 'fa-facebook', 'integraciones', 0, 1, 0, 83, NOW(), NOW()),
('integracion_instagram', 'Instagram Shopping', 'Integración con Instagram Shopping', 'fa-instagram', 'integraciones', 0, 1, 0, 84, NOW(), NOW()),

-- Plataformas de pago mexicanas
('integracion_clip', 'Clip', 'Integración con terminal Clip', 'fa-credit-card', 'integraciones', 0, 1, 0, 85, NOW(), NOW()),
('integracion_openpay', 'OpenPay', 'Integración con OpenPay (BBVA)', 'fa-money-check-alt', 'integraciones', 0, 1, 0, 86, NOW(), NOW()),
('integracion_conekta', 'Conekta', 'Integración con pasarela Conekta', 'fa-credit-card-alt', 'integraciones', 0, 1, 0, 87, NOW(), NOW()),

-- E-commerce y logística
('integracion_shopify', 'Shopify', 'Sincronización con tienda Shopify', 'fa-store', 'integraciones', 0, 1, 0, 88, NOW(), NOW()),
('integracion_woocommerce', 'WooCommerce', 'Integración con WooCommerce', 'fa-wordpress', 'integraciones', 0, 1, 0, 89, NOW(), NOW()),
('integracion_fedex', 'FedEx', 'Integración con servicios de envío FedEx', 'fa-shipping-fast', 'integraciones', 0, 1, 0, 90, NOW(), NOW()),
('integracion_dhl', 'DHL', 'Integración con servicios de envío DHL', 'fa-truck', 'integraciones', 0, 1, 0, 91, NOW(), NOW()),

-- Contabilidad mexicana
('integracion_contpaq', 'CONTPAQi', 'Integración con CONTPAQi Contabilidad', 'fa-file-invoice-dollar', 'integraciones', 0, 1, 0, 92, NOW(), NOW()),
('integracion_aspel', 'Aspel', 'Integración con sistemas Aspel (COI, SAE, NOI)', 'fa-calculator', 'integraciones', 0, 1, 0, 93, NOW(), NOW())

ON DUPLICATE KEY UPDATE updated_at = NOW();

-- ===============================================
-- 6. ACTIVAR NUEVOS MÓDULOS PARA TENANT
-- ===============================================
INSERT IGNORE INTO tenant_modules (tenant_id, module_id, is_active, activated_at)
SELECT 1, id, 1, NOW() 
FROM modules 
WHERE name IN (
    'partner_portal',
    'integracion_mercadolibre',
    'integracion_amazon',
    'integracion_tiktok',
    'integracion_facebook',
    'integracion_instagram',
    'integracion_clip',
    'integracion_openpay',
    'integracion_conekta',
    'integracion_shopify',
    'integracion_woocommerce',
    'integracion_fedex',
    'integracion_dhl',
    'integracion_contpaq',
    'integracion_aspel'
) AND is_enabled = 1;

-- ===============================================
-- 7. VERIFICACIÓN
-- ===============================================
SELECT 
    category as 'Categoría',
    COUNT(*) as 'Total',
    GROUP_CONCAT(display_name ORDER BY menu_order SEPARATOR ', ') as 'Módulos'
FROM modules 
WHERE category IN ('backend', 'integraciones')
GROUP BY category
ORDER BY FIELD(category, 'backend', 'integraciones');
