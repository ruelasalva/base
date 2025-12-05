-- =============================================
-- PRODUCTOS DE PRUEBA PARA INVENTARIO
-- =============================================

-- Verificar si existe la tabla products o inventory_products
-- Usar la tabla products que es la tabla principal del sistema

INSERT INTO products (sku, name, slug, description, unit, sale_price, cost_price, stock_quantity, min_stock, is_active, created_at) VALUES
('PROD-001', 'Laptop Dell Inspiron 15', 'laptop-dell-inspiron-15', 'Laptop para oficina, 8GB RAM, 256GB SSD', 'PZA', 12500.00, 10000.00, 0, 5, 1, NOW()),
('PROD-002', 'Mouse Logitech M185', 'mouse-logitech-m185', 'Mouse inalámbrico', 'PZA', 150.00, 100.00, 0, 10, 1, NOW()),
('PROD-003', 'Teclado Microsoft 600', 'teclado-microsoft-600', 'Teclado USB estándar', 'PZA', 250.00, 180.00, 0, 10, 1, NOW()),
('PROD-004', 'Monitor Samsung 24"', 'monitor-samsung-24', 'Monitor LED Full HD', 'PZA', 3500.00, 2800.00, 0, 3, 1, NOW()),
('PROD-005', 'Cable HDMI 2m', 'cable-hdmi-2m', 'Cable HDMI alta velocidad', 'PZA', 120.00, 80.00, 0, 20, 1, NOW()),
('PROD-006', 'Disco Duro Externo 1TB', 'disco-duro-externo-1tb', 'USB 3.0, portátil', 'PZA', 850.00, 650.00, 0, 5, 1, NOW()),
('PROD-007', 'Memoria USB 32GB', 'memoria-usb-32gb', 'USB 3.0, Kingston', 'PZA', 180.00, 120.00, 0, 25, 1, NOW()),
('PROD-008', 'Silla Ergonómica', 'silla-ergonomica', 'Silla de oficina con soporte lumbar', 'PZA', 2500.00, 1800.00, 0, 3, 1, NOW()),
('PROD-009', 'Escritorio 120x60cm', 'escritorio-120x60cm', 'Escritorio para oficina', 'PZA', 1800.00, 1200.00, 0, 2, 1, NOW()),
('PROD-010', 'Lámpara LED Escritorio', 'lampara-led-escritorio', 'Lámpara ajustable con USB', 'PZA', 350.00, 220.00, 0, 5, 1, NOW());

-- Verificar productos creados
SELECT 'Productos creados:' as info;
SELECT id, sku, name, unit, sale_price, stock_quantity, min_stock FROM products WHERE is_active=1 ORDER BY id DESC LIMIT 10;
