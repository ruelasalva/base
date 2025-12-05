<?php

namespace Fuel\Migrations;

class Add_codes_and_tags_to_products
{
	public function up()
	{
		\DBUtil::add_fields('products', array(
			'codigo_venta' => array('type' => 'varchar', 'constraint' => 100, 'null' => true, 'after' => 'barcode'),
			'codigo_compra' => array('type' => 'varchar', 'constraint' => 100, 'null' => true, 'after' => 'codigo_venta'),
			'codigo_externo' => array('type' => 'varchar', 'constraint' => 100, 'null' => true, 'after' => 'codigo_compra'),
			'tags' => array('type' => 'text', 'null' => true, 'after' => 'description'),
		));

		// Agregar índices para búsquedas rápidas
		\DBUtil::create_index('products', 'codigo_venta', 'idx_codigo_venta');
		\DBUtil::create_index('products', 'codigo_compra', 'idx_codigo_compra');
		\DBUtil::create_index('products', 'codigo_externo', 'idx_codigo_externo');
	}

	public function down()
	{
		// Eliminar índices
		\DBUtil::drop_index('products', 'idx_codigo_venta');
		\DBUtil::drop_index('products', 'idx_codigo_compra');
		\DBUtil::drop_index('products', 'idx_codigo_externo');

		// Eliminar campos
		\DBUtil::drop_fields('products', array(
			'codigo_venta',
			'codigo_compra',
			'codigo_externo',
			'tags',
		));
	}
}
