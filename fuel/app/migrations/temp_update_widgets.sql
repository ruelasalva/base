USE base;

-- Actualizar configuración de widgets del usuario admin
UPDATE user_preferences 
SET dashboard_widgets = '{"widgets":["active_users","recent_activity"],"refresh_interval":300}'
WHERE user_id = 3 AND tenant_id = 1;

-- Verificar
SELECT user_id, tenant_id, dashboard_widgets 
FROM user_preferences 
WHERE user_id = 3;
