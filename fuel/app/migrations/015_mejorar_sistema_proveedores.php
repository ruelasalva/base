<?php

namespace Fuel\Migrations;

/**
 * Migración: Mejorar Sistema de Proveedores
 * 
 * Mejoras al sistema de proveedores incluyendo:
 * - Relación con inventario (entradas de mercancía)
 * - Registro de recepciones
 * - Sistema de contabilidad integrado
 * - Logs detallados
 * - Permisos granulares
 */
class Mejorar_sistema_proveedores
{
    public function up()
    {
        // =====================================================
        // 1. MEJORAR TABLA PROVIDERS (agregar tenant_id)
        // =====================================================
        \DBUtil::add_fields('providers', array(
            'tenant_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'default' => 1,
                'after' => 'id'
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => false,
                'default' => 'MXN',
                'after' => 'payment_terms'
            ),
            'created_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'is_active'
            ),
            'updated_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'created_by'
            )
        ));

        // Agregar índices
        \DBUtil::create_index('providers', 'tenant_id', 'idx_providers_tenant');
        
        // =====================================================
        // 2. TABLA: PROVIDER_CATEGORIES (Categorías)
        // =====================================================
        \DBUtil::create_table('provider_categories', array(
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

        \DBUtil::create_index('provider_categories', 'tenant_id', 'idx_provider_categories_tenant');

        // =====================================================
        // 3. TABLA: PROVIDER_BANK_ACCOUNTS (Cuentas Bancarias)
        // =====================================================
        \DBUtil::create_table('provider_bank_accounts', array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'provider_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
            'bank_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ),
            'account_number' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ),
            'clabe' => array(
                'type' => 'VARCHAR',
                'constraint' => 18,
                'null' => true
            ),
            'swift_code' => array(
                'type' => 'VARCHAR',
                'constraint' => 11,
                'null' => true
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => false,
                'default' => 'MXN'
            ),
            'is_default' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'default' => 0
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

        \DBUtil::create_index('provider_bank_accounts', 'provider_id', 'idx_provider_bank_accounts_provider');

        // =====================================================
        // 4. TABLA: PROVIDER_INVENTORY_RECEIPTS (Recepciones)
        // =====================================================
        \DBUtil::create_table('provider_inventory_receipts', array(
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
            'provider_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
            'purchase_order_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Relación con providers_orders'
            ),
            'receipt_number' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ),
            'receipt_date' => array(
                'type' => 'DATE',
                'null' => false
            ),
            'warehouse_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
            'received_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
            'invoice_number' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ),
            'invoice_date' => array(
                'type' => 'DATE',
                'null' => true
            ),
            'status' => array(
                'type' => 'ENUM',
                'constraint' => "'draft','received','verified','posted','cancelled'",
                'null' => false,
                'default' => 'draft'
            ),
            'notes' => array(
                'type' => 'TEXT',
                'null' => true
            ),
            'total_amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00'
            ),
            'verified_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ),
            'verified_at' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'posted_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ),
            'posted_at' => array(
                'type' => 'DATETIME',
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
                'null' => true
            )
        ), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

        \DBUtil::create_index('provider_inventory_receipts', 'tenant_id', 'idx_receipts_tenant');
        \DBUtil::create_index('provider_inventory_receipts', 'provider_id', 'idx_receipts_provider');
        \DBUtil::create_index('provider_inventory_receipts', 'purchase_order_id', 'idx_receipts_order');
        \DBUtil::create_index('provider_inventory_receipts', 'receipt_number', 'idx_receipts_number', 'UNIQUE');

        // =====================================================
        // 5. TABLA: PROVIDER_INVENTORY_RECEIPT_DETAILS
        // =====================================================
        \DBUtil::create_table('provider_inventory_receipt_details', array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'receipt_id' => array(
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
            'quantity_ordered' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,4',
                'null' => false,
                'default' => '0.0000'
            ),
            'quantity_received' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,4',
                'null' => false,
                'default' => '0.0000'
            ),
            'unit_cost' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,4',
                'null' => false,
                'default' => '0.0000'
            ),
            'subtotal' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00'
            ),
            'tax_amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00'
            ),
            'total' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00'
            ),
            'lot_number' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ),
            'expiration_date' => array(
                'type' => 'DATE',
                'null' => true
            ),
            'notes' => array(
                'type' => 'TEXT',
                'null' => true
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

        \DBUtil::create_index('provider_inventory_receipt_details', 'receipt_id', 'idx_receipt_details_receipt');
        \DBUtil::create_index('provider_inventory_receipt_details', 'product_id', 'idx_receipt_details_product');

        // =====================================================
        // 6. TABLA: PROVIDER_PAYMENTS (Pagos a Proveedores)
        // =====================================================
        \DBUtil::create_table('provider_payments', array(
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
            'provider_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
            'payment_number' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ),
            'payment_date' => array(
                'type' => 'DATE',
                'null' => false
            ),
            'payment_method' => array(
                'type' => 'ENUM',
                'constraint' => "'efectivo','transferencia','cheque','tarjeta','otro'",
                'null' => false,
                'default' => 'transferencia'
            ),
            'reference_number' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ),
            'amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => false,
                'default' => 'MXN'
            ),
            'exchange_rate' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,4',
                'null' => false,
                'default' => '1.0000'
            ),
            'bank_account_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ),
            'notes' => array(
                'type' => 'TEXT',
                'null' => true
            ),
            'status' => array(
                'type' => 'ENUM',
                'constraint' => "'draft','completed','cancelled'",
                'null' => false,
                'default' => 'draft'
            ),
            'created_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
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
                'null' => true
            )
        ), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

        \DBUtil::create_index('provider_payments', 'tenant_id', 'idx_payments_tenant');
        \DBUtil::create_index('provider_payments', 'provider_id', 'idx_payments_provider');
        \DBUtil::create_index('provider_payments', 'payment_number', 'idx_payments_number', 'UNIQUE');

        // =====================================================
        // 7. TABLA: PROVIDER_PAYMENT_ALLOCATIONS
        // =====================================================
        \DBUtil::create_table('provider_payment_allocations', array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'payment_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
            'invoice_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'providers_bills.id'
            ),
            'order_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'providers_orders.id'
            ),
            'amount_allocated' => array(
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false
            )
        ), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

        \DBUtil::create_index('provider_payment_allocations', 'payment_id', 'idx_allocations_payment');

        // =====================================================
        // 8. MEJORAR TABLA PROVIDER_LOGS
        // =====================================================
        if (!\DBUtil::table_exists('provider_logs')) {
            \DBUtil::create_table('provider_logs', array(
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
                'provider_id' => array(
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
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => false
                ),
                'entity_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => true,
                    'comment' => 'provider, order, payment, receipt, etc'
                ),
                'entity_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true
                ),
                'description' => array(
                    'type' => 'TEXT',
                    'null' => true
                ),
                'old_values' => array(
                    'type' => 'TEXT',
                    'null' => true
                ),
                'new_values' => array(
                    'type' => 'TEXT',
                    'null' => true
                ),
                'ip_address' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 45,
                    'null' => true
                ),
                'user_agent' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true
                ),
                'created_at' => array(
                    'type' => 'DATETIME',
                    'null' => false
                )
            ), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

            \DBUtil::create_index('provider_logs', 'tenant_id', 'idx_logs_tenant');
            \DBUtil::create_index('provider_logs', 'provider_id', 'idx_logs_provider');
            \DBUtil::create_index('provider_logs', 'user_id', 'idx_logs_user');
            \DBUtil::create_index('provider_logs', array('entity_type', 'entity_id'), 'idx_logs_entity');
        }

        // =====================================================
        // 9. INSERTAR PERMISOS DEL MÓDULO
        // =====================================================
        \DB::insert('permissions')->set(array(
            array(
                'name' => 'proveedores.view',
                'display_name' => 'Ver Proveedores',
                'description' => 'Permite ver el listado y detalles de proveedores',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.create',
                'display_name' => 'Crear Proveedores',
                'description' => 'Permite crear nuevos proveedores',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.edit',
                'display_name' => 'Editar Proveedores',
                'description' => 'Permite editar información de proveedores',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.delete',
                'display_name' => 'Eliminar Proveedores',
                'description' => 'Permite eliminar proveedores',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.orders.view',
                'display_name' => 'Ver Órdenes de Compra',
                'description' => 'Permite ver órdenes de compra',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.orders.create',
                'display_name' => 'Crear Órdenes de Compra',
                'description' => 'Permite crear órdenes de compra',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.orders.authorize',
                'display_name' => 'Autorizar Órdenes de Compra',
                'description' => 'Permite autorizar órdenes de compra',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.receipts.view',
                'display_name' => 'Ver Recepciones',
                'description' => 'Permite ver recepciones de mercancía',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.receipts.create',
                'display_name' => 'Crear Recepciones',
                'description' => 'Permite crear recepciones de mercancía',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.receipts.verify',
                'display_name' => 'Verificar Recepciones',
                'description' => 'Permite verificar recepciones de mercancía',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.payments.view',
                'display_name' => 'Ver Pagos',
                'description' => 'Permite ver pagos a proveedores',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.payments.create',
                'display_name' => 'Crear Pagos',
                'description' => 'Permite crear pagos a proveedores',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'name' => 'proveedores.reports',
                'display_name' => 'Reportes de Proveedores',
                'description' => 'Permite ver reportes de proveedores',
                'module' => 'proveedores',
                'created_at' => date('Y-m-d H:i:s')
            )
        ))->execute();

        // =====================================================
        // 10. DATOS INICIALES
        // =====================================================
        
        // Categorías por defecto
        \DB::insert('provider_categories')->set(array(
            array(
                'tenant_id' => 1,
                'name' => 'Materias Primas',
                'description' => 'Proveedores de materias primas e insumos',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'tenant_id' => 1,
                'name' => 'Servicios',
                'description' => 'Proveedores de servicios',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'tenant_id' => 1,
                'name' => 'Mercancías',
                'description' => 'Proveedores de productos terminados',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ),
            array(
                'tenant_id' => 1,
                'name' => 'Equipos y Tecnología',
                'description' => 'Proveedores de equipos y tecnología',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            )
        ))->execute();
    }

    public function down()
    {
        // Eliminar tablas en orden inverso
        \DBUtil::drop_table('provider_payment_allocations');
        \DBUtil::drop_table('provider_payments');
        \DBUtil::drop_table('provider_inventory_receipt_details');
        \DBUtil::drop_table('provider_inventory_receipts');
        \DBUtil::drop_table('provider_bank_accounts');
        \DBUtil::drop_table('provider_categories');
        \DBUtil::drop_table('provider_logs');
        
        // Eliminar permisos
        \DB::delete('permissions')->where('module', 'proveedores')->execute();
        
        // Eliminar campos agregados a providers
        if (\DBUtil::field_exists('providers', 'tenant_id')) {
            \DBUtil::drop_fields('providers', array('tenant_id', 'currency', 'created_by', 'updated_by'));
        }
    }
}
