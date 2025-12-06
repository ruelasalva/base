-- Script simplificado para permisos de Facturación Electrónica

-- Agregar permisos para el usuario administrador (user_id = 1)
INSERT INTO permissions (user_id, resource, can_view, can_create, can_edit, can_delete, created_at, updated_at)
VALUES 
    (1, 'facturacion', 1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE
    can_view = 1,
    can_create = 1,
    can_edit = 1,
    can_delete = 1,
    updated_at = UNIX_TIMESTAMP();

SELECT 'Permisos configurados correctamente' AS Resultado;

-- Verificar
SELECT * FROM permissions WHERE resource = 'facturacion';
