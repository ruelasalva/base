-- Crear usuario administrador para el sistema
-- Usuario: admin@admin.com
-- Contraseña: admin123

USE base;

-- Eliminar usuario admin si existe
DELETE FROM users WHERE username = 'admin' OR email = 'admin@admin.com';

-- Insertar usuario administrador
-- Password hash para 'admin123' usando SimpleAuth de FuelPHP
INSERT INTO users (
    username,
    password,
    email,
    group_id,
    first_name,
    last_name,
    is_active,
    is_verified,
    created_at,
    updated_at
) VALUES (
    'admin',
    '8a684fd1eb8afd918fb6cbcd1efa87fd1f77f05f58086ecc99c96c7935ec4d37fffc05fffc9d77f0a3ce54683e1dd96dd4be21c13f0e0d7d33ee2ac0e1fc9d80MqwkdX8BTZxV7vZleSq6FpGHgaLzNhGE',
    'admin@admin.com',
    100,
    'Administrador',
    'Sistema',
    1,
    1,
    NOW(),
    NOW()
);

-- Mostrar el usuario creado
SELECT id, username, email, group_id, first_name, last_name, 'Password: admin123' as password_info FROM users WHERE username = 'admin';
