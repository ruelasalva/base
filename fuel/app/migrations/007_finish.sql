INSERT INTO tenants (id, domain, db_name, company_name, is_active, plan_type, max_users)
SELECT 1, 'localhost', 'base', 'Empresa Principal', 1, 'enterprise', 999
WHERE NOT EXISTS (SELECT 1 FROM tenants WHERE id = 1);

INSERT INTO tenant_modules (tenant_id, module_id, is_active, activated_by)
SELECT 1, m.id, 1, 3 
FROM modules m
WHERE m.is_core = 1
AND NOT EXISTS (SELECT 1 FROM tenant_modules tm WHERE tm.tenant_id = 1 AND tm.module_id = m.id);

INSERT INTO user_preferences (user_id, tenant_id, template_theme, sidebar_collapsed, dashboard_widgets)
SELECT 3, 1, 'coreui', 0, '["stats","recent_sales","charts","quick_actions"]'
WHERE NOT EXISTS (SELECT 1 FROM user_preferences WHERE user_id = 3 AND tenant_id = 1);
