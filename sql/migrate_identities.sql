-- =========================================================
-- MIGRACIÓN DE DATOS A SISTEMA DE IDENTIDADES
-- =========================================================
-- Este script migra los datos existentes al nuevo sistema
-- de identidades unificado
-- =========================================================

-- 1. Migrar empleados que tienen user_id
INSERT INTO user_identities (user_id, identity_type, identity_id, is_primary, can_login, access_level, created_at)
SELECT 
    user_id,
    'employee' as identity_type,
    id as identity_id,
    1 as is_primary,
    1 as can_login,
    'full' as access_level,
    NOW() as created_at
FROM employees
WHERE user_id IS NOT NULL 
  AND deleted = 0
  AND user_id NOT IN (
      SELECT user_id FROM user_identities WHERE identity_type = 'employee'
  );

-- Verificar resultados
SELECT 
    'Empleados migrados' as descripcion,
    COUNT(*) as total
FROM user_identities 
WHERE identity_type = 'employee';

-- =========================================================
-- OPCIONAL: Si tienes proveedores que ya tienen acceso
-- (por ahora no aplica, pero lo dejamos preparado)
-- =========================================================
-- INSERT INTO user_identities (user_id, identity_type, identity_id, is_primary, can_login, access_level, created_at)
-- SELECT 
--     user_id,
--     'provider' as identity_type,
--     id as identity_id,
--     1 as is_primary,
--     1 as can_login,
--     'readonly' as access_level,
--     NOW() as created_at
-- FROM providers
-- WHERE user_id IS NOT NULL  -- si tuvieras este campo
--   AND is_active = 1;

-- =========================================================
-- VERIFICACIONES
-- =========================================================

-- Ver todas las identidades creadas
SELECT 
    ui.id,
    ui.identity_type,
    ui.identity_id,
    u.username,
    u.email,
    CASE 
        WHEN ui.identity_type = 'employee' THEN (SELECT CONCAT(name, ' ', last_name) FROM employees WHERE id = ui.identity_id)
        WHEN ui.identity_type = 'provider' THEN (SELECT company_name FROM providers WHERE id = ui.identity_id)
        ELSE 'N/A'
    END as entity_name,
    ui.is_primary,
    ui.can_login,
    ui.access_level
FROM user_identities ui
INNER JOIN users u ON ui.user_id = u.id
ORDER BY ui.identity_type, ui.identity_id;

-- Contar por tipo
SELECT 
    identity_type,
    COUNT(*) as total,
    SUM(is_primary) as primarias,
    SUM(can_login) as con_acceso
FROM user_identities
GROUP BY identity_type;
