-- =====================================================
-- MIGRATION 010: AGREGAR MÓDULOS DE BACKEND
-- =====================================================
-- Fecha: 2025-12-02
-- Descripción: Agrega módulos de Backend y APIs para
--              activar servicios como Portal de Clientes,
--              REST API, GraphQL, Webhooks, etc.
--              También actualiza el ENUM de category
-- =====================================================

USE base;

-- 1. Agregar 'backend' al ENUM de category
ALTER TABLE modules 
MODIFY COLUMN category ENUM('core','business','sales','marketing','backend','system') 
DEFAULT 'business';

-- 2. Actualizar módulos existentes a categoría backend
UPDATE modules 
SET 
    category = 'backend',
    icon = CASE name
        WHEN 'client_portal' THEN 'fa-user-circle'
        WHEN 'rest_api' THEN 'fa-code'
        WHEN 'graphql_api' THEN 'fa-project-diagram'
        WHEN 'webhooks' THEN 'fa-broadcast-tower'
        WHEN 'supplier_portal' THEN 'fa-truck'
        WHEN 'employee_portal' THEN 'fa-id-card'
        ELSE icon
    END,
    menu_order = CASE name
        WHEN 'client_portal' THEN 50
        WHEN 'rest_api' THEN 51
        WHEN 'graphql_api' THEN 52
        WHEN 'webhooks' THEN 53
        WHEN 'supplier_portal' THEN 55
        WHEN 'employee_portal' THEN 56
        ELSE menu_order
    END
WHERE name IN ('client_portal','rest_api','graphql_api','webhooks','supplier_portal','employee_portal');

-- 3. Verificar módulos backend creados
SELECT 
    id,
    name,
    display_name,
    category,
    icon,
    menu_order
FROM modules
WHERE category = 'backend'
ORDER BY menu_order;
