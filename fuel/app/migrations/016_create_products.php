<?php

namespace Fuel\Migrations;

/**
 * Migración: Crear Sistema de Productos
 * 
 * Crea las tablas necesarias para el módulo de productos:
 * - product_categories: Categorías de productos
 * - products: Productos del inventario
 * - product_logs: Registro de cambios en productos
 */
class Create_products
{
    public function up()
    {
        // =====================================================
        // 1. TABLA: INVENTORY_PRODUCT_CATEGORIES (Categorías)
        // =====================================================
        \DBUtil::create_table('inventory_product_categories', array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'tenant_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => true
            ),
            'parent_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Categoría padre para subcategorías'
            ),
            'is_active' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'default' => 1
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true
            )
        ), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

        \DBUtil::create_index('inventory_product_categories', 'tenant_id', 'idx_inv_product_categories_tenant');
        \DBUtil::create_index('inventory_product_categories', 'parent_id', 'idx_inv_product_categories_parent');

        // =====================================================
        // 2. TABLA: INVENTORY_PRODUCTS (Productos)
        // =====================================================
        \DBUtil::create_table('products', array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'tenant_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
            'code' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'Código único del producto (SKU)'
            ),
            'barcode' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Código de barras'
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'comment' => 'Nombre del producto'
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Descripción detallada'
            ),
            'category_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Categoría del producto'
            ),
            'unit_of_measure' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'PZA',
                'comment' => 'Unidad de medida (PZA, KG, LT, etc.)'
            ),
            'unit_price' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => 0.00,
                'comment' => 'Precio de venta unitario'
            ),
            'cost' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => 0.00,
                'comment' => 'Costo unitario'
            ),
            'stock' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => 0.00,
                'comment' => 'Cantidad en stock'
            ),
            'min_stock' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'comment' => 'Stock mínimo (alerta de reorden)'
            ),
            'max_stock' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'comment' => 'Stock máximo'
            ),
            'tax_rate' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => false,
                'default' => 16.00,
                'comment' => 'Tasa de IVA (%)'
            ),
            'image' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Ruta de imagen del producto'
            ),
            'is_active' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'default' => 1
            ),
            'is_service' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'default' => 0,
                'comment' => '1 = Servicio, 0 = Producto físico'
            ),
            'created_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ),
            'updated_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'deleted_at' => array(
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Soft delete'
            )
        ), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

        // Índices para optimizar consultas
        \DBUtil::create_index('inventory_products', 'tenant_id', 'idx_inv_products_tenant');
        \DBUtil::create_index('inventory_products', 'code', 'idx_inv_products_code');
        \DBUtil::create_index('inventory_products', 'barcode', 'idx_inv_products_barcode');
        \DBUtil::create_index('inventory_products', 'category_id', 'idx_inv_products_category');
        \DBUtil::create_index('inventory_products', 'is_active', 'idx_inv_products_active');
        \DBUtil::create_index('inventory_products', array('tenant_id', 'code'), 'idx_inv_products_tenant_code', 'unique');

        // =====================================================
        // 3. TABLA: INVENTORY_PRODUCT_LOGS (Logs de productos)
        // =====================================================
        \DBUtil::create_table('inventory_product_logs', array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'tenant_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
            'product_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ),
            'action' => array(
                'type' => 'ENUM',
                'constraint' => array('created', 'updated', 'deleted', 'stock_adjusted', 'price_changed'),
                'null' => false
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Descripción del cambio'
            ),
            'old_values' => array(
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Valores anteriores (JSON)'
            ),
            'new_values' => array(
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Valores nuevos (JSON)'
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false
            )
        ), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

        \DBUtil::create_index('inventory_product_logs', 'product_id', 'idx_inv_product_logs_product');
        \DBUtil::create_index('inventory_product_logs', 'user_id', 'idx_inv_product_logs_user');
        \DBUtil::create_index('inventory_product_logs', 'created_at', 'idx_inv_product_logs_date');
    }

    public function down()
    {
        // Eliminar tablas en orden inverso
        \DBUtil::drop_table('inventory_product_logs');
        \DBUtil::drop_table('inventory_products');
        \DBUtil::drop_table('inventory_product_categories');
    }
}
