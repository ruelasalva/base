<?php

namespace Fuel\Migrations;

class Create_purchase_orders_tables
{
	public function up()
	{
		// Tabla principal de órdenes de compra
		\DBUtil::create_table('purchase_orders', [
			'id' => ['type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'primary_key' => true],
			'code' => ['type' => 'varchar', 'constraint' => 50, 'null' => false],
			'provider_id' => ['type' => 'int', 'constraint' => 11, 'null' => false],
			'order_date' => ['type' => 'date', 'null' => false],
			'delivery_date' => ['type' => 'date', 'null' => true],
			'status' => ['type' => 'enum', 'constraint' => ['draft', 'pending', 'approved', 'rejected', 'received', 'cancelled'], 'default' => 'draft'],
			'type' => ['type' => 'enum', 'constraint' => ['inventory', 'usage', 'service'], 'default' => 'inventory'],
			'subtotal' => ['type' => 'decimal', 'constraint' => '15,2', 'default' => 0.00],
			'tax' => ['type' => 'decimal', 'constraint' => '15,2', 'default' => 0.00],
			'total' => ['type' => 'decimal', 'constraint' => '15,2', 'default' => 0.00],
			'notes' => ['type' => 'text', 'null' => true],
			'approved_by' => ['type' => 'int', 'constraint' => 11, 'null' => true],
			'approved_at' => ['type' => 'datetime', 'null' => true],
			'rejected_by' => ['type' => 'int', 'constraint' => 11, 'null' => true],
			'rejected_at' => ['type' => 'datetime', 'null' => true],
			'rejection_reason' => ['type' => 'text', 'null' => true],
			'received_by' => ['type' => 'int', 'constraint' => 11, 'null' => true],
			'received_at' => ['type' => 'datetime', 'null' => true],
			'created_by' => ['type' => 'int', 'constraint' => 11, 'null' => true],
			'created_at' => ['type' => 'datetime', 'null' => true],
			'updated_at' => ['type' => 'datetime', 'null' => true],
			'deleted_at' => ['type' => 'datetime', 'null' => true],
		], ['id'], true, 'InnoDB', 'utf8_general_ci');

		// Índices
		\DB::query("ALTER TABLE purchase_orders ADD INDEX idx_code (code)")->execute();
		\DB::query("ALTER TABLE purchase_orders ADD INDEX idx_provider_id (provider_id)")->execute();
		\DB::query("ALTER TABLE purchase_orders ADD INDEX idx_status (status)")->execute();
		\DB::query("ALTER TABLE purchase_orders ADD INDEX idx_type (type)")->execute();
		\DB::query("ALTER TABLE purchase_orders ADD INDEX idx_order_date (order_date)")->execute();
		\DB::query("ALTER TABLE purchase_orders ADD INDEX idx_deleted_at (deleted_at)")->execute();

		// Tabla de items/líneas de la orden
		\DBUtil::create_table('purchase_order_items', [
			'id' => ['type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'primary_key' => true],
			'purchase_order_id' => ['type' => 'int', 'constraint' => 11, 'null' => false],
			'product_id' => ['type' => 'int', 'constraint' => 11, 'null' => true],
			'item_type' => ['type' => 'enum', 'constraint' => ['product', 'service', 'custom'], 'default' => 'product'],
			'description' => ['type' => 'varchar', 'constraint' => 500, 'null' => false],
			'quantity' => ['type' => 'decimal', 'constraint' => '10,2', 'null' => false, 'default' => 1.00],
			'unit_price' => ['type' => 'decimal', 'constraint' => '15,2', 'null' => false, 'default' => 0.00],
			'tax_rate' => ['type' => 'decimal', 'constraint' => '5,2', 'null' => false, 'default' => 16.00],
			'subtotal' => ['type' => 'decimal', 'constraint' => '15,2', 'null' => false, 'default' => 0.00],
			'tax_amount' => ['type' => 'decimal', 'constraint' => '15,2', 'null' => false, 'default' => 0.00],
			'total' => ['type' => 'decimal', 'constraint' => '15,2', 'null' => false, 'default' => 0.00],
			'notes' => ['type' => 'varchar', 'constraint' => 500, 'null' => true],
			'created_at' => ['type' => 'datetime', 'null' => true],
			'updated_at' => ['type' => 'datetime', 'null' => true],
		], ['id'], true, 'InnoDB', 'utf8_general_ci');

		// Índices
		\DB::query("ALTER TABLE purchase_order_items ADD INDEX idx_purchase_order_id (purchase_order_id)")->execute();
		\DB::query("ALTER TABLE purchase_order_items ADD INDEX idx_product_id (product_id)")->execute();
		\DB::query("ALTER TABLE purchase_order_items ADD INDEX idx_item_type (item_type)")->execute();

		// Foreign keys
		\DB::query("
			ALTER TABLE purchase_order_items 
			ADD CONSTRAINT fk_poi_purchase_order 
			FOREIGN KEY (purchase_order_id) 
			REFERENCES purchase_orders(id) 
			ON DELETE CASCADE
		")->execute();

		\DB::query("
			ALTER TABLE purchase_order_items 
			ADD CONSTRAINT fk_poi_product 
			FOREIGN KEY (product_id) 
			REFERENCES products(id) 
			ON DELETE SET NULL
		")->execute();
	}

	public function down()
	{
		\DBUtil::drop_table('purchase_order_items');
		\DBUtil::drop_table('purchase_orders');
	}
}
