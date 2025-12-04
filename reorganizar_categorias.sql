-- Reorganizar categorías de módulos
USE base;

-- Contabilidad
UPDATE modules SET category='contabilidad' WHERE id IN (5,35,36,59,65);

-- Finanzas  
UPDATE modules SET category='finanzas' WHERE id=6;

-- Compras
UPDATE modules SET category='compras' WHERE id IN (8,50,63,64);

-- Inventario
UPDATE modules SET category='inventario' WHERE id IN (7,56,57,58,60,61,62);

-- RRHH
UPDATE modules SET category='rrhh' WHERE id IN (37,38,51);

-- Verificar
SELECT category, COUNT(*) as total, GROUP_CONCAT(display_name SEPARATOR ', ') as modulos 
FROM modules 
WHERE is_enabled=1 
GROUP BY category 
ORDER BY FIELD(category, 'core', 'contabilidad', 'finanzas', 'compras', 'inventario', 'sales', 'rrhh', 'marketing', 'backend', 'system');
