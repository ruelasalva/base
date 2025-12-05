<?php

namespace Fuel\Migrations;

class Create_product_attributes
{
	public function up()
	{
		// Tabla de atributos (color, talla, material, etc.)
		\DBUtil::create_table('attributes', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'primary_key' => true),
			'tenant_id' => array('type' => 'int', 'constraint' => 11, 'null' => false),
			'name' => array('type' => 'varchar', 'constraint' => 100, 'null' => false),
			'slug' => array('type' => 'varchar', 'constraint' => 100, 'null' => false),
			'type' => array('type' => 'enum', 'constraint' => ['text', 'select', 'multiselect', 'number', 'boolean'], 'default' => 'text'),
			'is_filterable' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1),
			'is_searchable' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 0),
			'sort_order' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'is_active' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'), true, 'InnoDB', 'utf8_general_ci');

		// Tabla de valores de atributos (rojo, azul, XL, etc.)
		\DBUtil::create_table('attribute_values', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'primary_key' => true),
			'attribute_id' => array('type' => 'int', 'constraint' => 11, 'null' => false),
			'value' => array('type' => 'varchar', 'constraint' => 255, 'null' => false),
			'slug' => array('type' => 'varchar', 'constraint' => 255, 'null' => false),
			'sort_order' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'is_active' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1),
			'created_at' => array('type' => 'datetime', 'null' => true),
		), array('id'), true, 'InnoDB', 'utf8_general_ci');

		// Tabla de relación producto-atributo-valor
		\DBUtil::create_table('product_attributes', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'primary_key' => true),
			'product_id' => array('type' => 'int', 'constraint' => 11, 'null' => false),
			'attribute_id' => array('type' => 'int', 'constraint' => 11, 'null' => false),
			'attribute_value_id' => array('type' => 'int', 'constraint' => 11, 'null' => true),
			'custom_value' => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
			'created_at' => array('type' => 'datetime', 'null' => true),
		), array('id'), true, 'InnoDB', 'utf8_general_ci');

		// Índices
		\DBUtil::create_index('attributes', array('tenant_id', 'slug'), 'idx_tenant_slug', 'UNIQUE');
		\DBUtil::create_index('attributes', 'is_filterable', 'idx_filterable');
		
		\DBUtil::create_index('attribute_values', 'attribute_id', 'idx_attribute');
		\DBUtil::create_index('attribute_values', 'slug', 'idx_slug');
		
		\DBUtil::create_index('product_attributes', array('product_id', 'attribute_id'), 'idx_product_attribute');
		\DBUtil::create_index('product_attributes', 'attribute_value_id', 'idx_value');

		// Insertar atributos predefinidos comunes
		$tenant_id = 1; // Ajustar según corresponda
		
		$attributes = array(
			array('name' => 'Color', 'slug' => 'color', 'type' => 'select', 'is_filterable' => 1, 'sort_order' => 1),
			array('name' => 'Talla', 'slug' => 'talla', 'type' => 'select', 'is_filterable' => 1, 'sort_order' => 2),
			array('name' => 'Material', 'slug' => 'material', 'type' => 'select', 'is_filterable' => 1, 'sort_order' => 3),
			array('name' => 'Género', 'slug' => 'genero', 'type' => 'select', 'is_filterable' => 1, 'sort_order' => 4),
			array('name' => 'Temporada', 'slug' => 'temporada', 'type' => 'select', 'is_filterable' => 1, 'sort_order' => 5),
		);

		foreach ($attributes as $attr) {
			\DB::insert('attributes')->set(array_merge($attr, array(
				'tenant_id' => $tenant_id,
				'is_active' => 1,
				'created_at' => date('Y-m-d H:i:s')
			)))->execute();
		}
	}

	public function down()
	{
		\DBUtil::drop_table('product_attributes');
		\DBUtil::drop_table('attribute_values');
		\DBUtil::drop_table('attributes');
	}
}
