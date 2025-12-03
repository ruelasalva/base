-- =====================================================
-- CORRECCIÓN DE DATOS MAL CODIFICADOS
-- Para módulos con caracteres especiales
-- =====================================================

USE base;

-- Corregir display_name en system_modules
UPDATE system_modules SET display_name = 'Órdenes de Compra' WHERE name = 'ordenes_compra';
UPDATE system_modules SET display_name = 'Gestión de órdenes' WHERE name = 'ordenes_compra' AND description LIKE '%rdenes%';

-- Verificar otros módulos con problemas de encoding
UPDATE system_modules SET display_name = 'Facturación' WHERE display_name LIKE '%Facturaci%n%' OR display_name LIKE '%Facturaci?n%';
UPDATE system_modules SET display_name = 'Configuración' WHERE display_name LIKE '%Configuraci%n%' OR display_name LIKE '%Configuraci?n%';
UPDATE system_modules SET display_name = 'Almacén' WHERE display_name LIKE '%Almac%n%';
UPDATE system_modules SET display_name = 'Gestión' WHERE display_name LIKE '%Gesti%n%';
UPDATE system_modules SET display_name = 'Comunicación' WHERE display_name LIKE '%Comunicaci%n%';
UPDATE system_modules SET display_name = 'Autenticación' WHERE display_name LIKE '%Autenticaci%n%';
UPDATE system_modules SET display_name = 'Información' WHERE display_name LIKE '%Informaci%n%';

-- Corregir descriptions con problemas
UPDATE system_modules SET description = REPLACE(description, 'Ã©', 'é');
UPDATE system_modules SET description = REPLACE(description, 'Ã³', 'ó');
UPDATE system_modules SET description = REPLACE(description, 'Ã¡', 'á');
UPDATE system_modules SET description = REPLACE(description, 'Ã­', 'í');
UPDATE system_modules SET description = REPLACE(description, 'Ãº', 'ú');
UPDATE system_modules SET description = REPLACE(description, 'Ã±', 'ñ');
UPDATE system_modules SET description = REPLACE(description, 'Ã¼', 'ü');
UPDATE system_modules SET description = REPLACE(description, 'Ë', 'Ó');

-- Corregir display_name con problemas
UPDATE system_modules SET display_name = REPLACE(display_name, 'Ã©', 'é');
UPDATE system_modules SET display_name = REPLACE(display_name, 'Ã³', 'ó');
UPDATE system_modules SET display_name = REPLACE(display_name, 'Ã¡', 'á');
UPDATE system_modules SET display_name = REPLACE(display_name, 'Ã­', 'í');
UPDATE system_modules SET display_name = REPLACE(display_name, 'Ãº', 'ú');
UPDATE system_modules SET display_name = REPLACE(display_name, 'Ã±', 'ñ');
UPDATE system_modules SET display_name = REPLACE(display_name, 'Ã¼', 'ü');
UPDATE system_modules SET display_name = REPLACE(display_name, 'Ë', 'Ó');

-- Listar módulos actualizados
SELECT id, name, display_name, description 
FROM system_modules 
WHERE display_name LIKE '%orden%' OR display_name LIKE '%recep%'
ORDER BY id;

SELECT 'Datos corregidos. Los acentos ahora se muestran correctamente.' AS resultado;
