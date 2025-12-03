-- Corrección de encoding en system_modules
USE base;

SET NAMES utf8mb4;

-- Módulo 14: Órdenes de Compra
UPDATE system_modules 
SET display_name = 'Órdenes de Compra',
    description = 'Gestión de órdenes'
WHERE id = 14;

-- Módulo 15: Recepciones  
UPDATE system_modules
SET display_name = 'Recepciones',
    description = 'Recepción de mercancía'
WHERE id = 15;

-- Verificar
SELECT id, name, display_name, description 
FROM system_modules 
WHERE id IN (14, 15);
