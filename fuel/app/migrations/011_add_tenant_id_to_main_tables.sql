-- =====================================================
-- MIGRATION 011: AGREGAR tenant_id A TABLAS PRINCIPALES
-- =====================================================
-- Fecha: 2025-12-02
-- Descripción: Agrega columna tenant_id a las tablas
--              products, sales, customers para soporte
--              multi-tenant
-- =====================================================

USE base;

-- 1. Agregar tenant_id a tabla PRODUCTS
ALTER TABLE products 
ADD COLUMN tenant_id INT(11) UNSIGNED DEFAULT 1 AFTER id,
ADD INDEX idx_tenant_products (tenant_id);

-- 2. Agregar tenant_id a tabla SALES
ALTER TABLE sales 
ADD COLUMN tenant_id INT(11) UNSIGNED DEFAULT 1 AFTER id,
ADD INDEX idx_tenant_sales (tenant_id);

-- 3. Agregar tenant_id a tabla CUSTOMERS
ALTER TABLE customers 
ADD COLUMN tenant_id INT(11) UNSIGNED DEFAULT 1 AFTER id,
ADD INDEX idx_tenant_customers (tenant_id);

-- 4. Actualizar registros existentes con tenant_id = 1 (por si hay NULL)
UPDATE products SET tenant_id = 1 WHERE tenant_id IS NULL;
UPDATE sales SET tenant_id = 1 WHERE tenant_id IS NULL;
UPDATE customers SET tenant_id = 1 WHERE tenant_id IS NULL;

-- Verificar cambios
SELECT 'PRODUCTS' as Tabla, COUNT(*) as Total FROM products WHERE tenant_id = 1
UNION ALL
SELECT 'SALES', COUNT(*) FROM sales WHERE tenant_id = 1
UNION ALL
SELECT 'CUSTOMERS', COUNT(*) FROM customers WHERE tenant_id = 1;
