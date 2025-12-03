UPDATE system_modules SET 
    display_name = CONVERT(CAST(CONVERT('Órdenes de Compra' USING latin1) AS BINARY) USING utf8mb4),
    description = CONVERT(CAST(CONVERT('Gestión de órdenes' USING latin1) AS BINARY) USING utf8mb4)
WHERE id = 14;

UPDATE system_modules SET 
    display_name = CONVERT(CAST(CONVERT('Recepciones' USING latin1) AS BINARY) USING utf8mb4),
    description = CONVERT(CAST(CONVERT('Recepción de mercancía' USING latin1) AS BINARY) USING utf8mb4)
WHERE id = 15;

SELECT id, name, display_name, description FROM system_modules WHERE id IN (14, 15);
