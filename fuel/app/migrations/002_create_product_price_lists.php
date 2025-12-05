<?php

namespace Fuel\Migrations;

class Create_product_price_lists
{
	public function up()
	{
		// Tabla de listas de precios (catálogo de listas)
		\DBUtil::create_table('price_lists', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'primary_key' => true),
			'tenant_id' => array('type' => 'int', 'constraint' => 11, 'null' => false),
			'name' => array('type' => 'varchar', 'constraint' => 100, 'null' => false),
			'code' => array('type' => 'varchar', 'constraint' => 50, 'null' => false),
			'description' => array('type' => 'text', 'null' => true),
			'type' => array('type' => 'enum', 'constraint' => ['percentage', 'fixed'], 'default' => 'percentage'),
			'discount_value' => array('type' => 'decimal', 'constraint' => '10,2', 'default' => 0.00),
			'is_active' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1),
			'priority' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'), true, 'InnoDB', 'utf8_general_ci');

		// Tabla de precios por producto y lista
		\DBUtil::create_table('product_prices', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'primary_key' => true),
			'tenant_id' => array('type' => 'int', 'constraint' => 11, 'null' => false),
			'product_id' => array('type' => 'int', 'constraint' => 11, 'null' => false),
			'price_list_id' => array('type' => 'int', 'constraint' => 11, 'null' => false),
			'price' => array('type' => 'decimal', 'constraint' => '10,2', 'null' => false),
			'min_quantity' => array('type' => 'int', 'constraint' => 11, 'default' => 1),
			'max_quantity' => array('type' => 'int', 'constraint' => 11, 'null' => true),
			'is_active' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
		), array('id'), true, 'InnoDB', 'utf8_general_ci');

		// Índices para optimización
		\DBUtil::create_index('price_lists', array('tenant_id', 'code'), 'idx_tenant_code', 'UNIQUE');
		\DBUtil::create_index('price_lists', 'is_active', 'idx_is_active');
		
		\DBUtil::create_index('product_prices', array('product_id', 'price_list_id'), 'idx_product_pricelist', 'UNIQUE');
		\DBUtil::create_index('product_prices', 'tenant_id', 'idx_tenant');
		\DBUtil::create_index('product_prices', 'is_active', 'idx_is_active');
	}

	public function down()
	{
		\DBUtil::drop_table('product_prices');
		\DBUtil::drop_table('price_lists');
	}
}
