-- Corregir encoding de nombres de módulos
USE base;

UPDATE system_modules SET display_name = 'Auditoría' WHERE id = 4;
UPDATE system_modules SET display_name = 'Configuración' WHERE id = 6;
UPDATE system_modules SET display_name = 'Almacén' WHERE id = 18;
UPDATE system_modules SET display_name = 'Órdenes de Compra' WHERE id = 14;

-- Verificar cambios
SELECT id, name, display_name, icon, category, is_active 
FROM system_modules 
WHERE id IN (4, 6, 14, 18)
ORDER BY id;
