<?php

/**
 * Model_Inventory_Product_Category
 * 
 * Modelo para categorías de productos de inventario
 */
class Model_Inventory_Product_Category extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'tenant_id',
		'name',
		'description',
		'parent_id',
		'is_active',
		'created_at',
		'updated_at'
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'inventory_product_categories';

	protected static $_primary_key = array('id');

	protected static $_belongs_to = array(
		'parent' => array(
			'key_from' => 'parent_id',
			'model_to' => 'Model_Inventory_Product_Category',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	protected static $_has_many = array(
		'products' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Inventory_Product',
			'key_to' => 'category_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'subcategories' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Inventory_Product_Category',
			'key_to' => 'parent_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	/**
	 * Obtener categorías activas para select
	 * 
	 * @param  int  $tenant_id
	 * @return array
	 */
	public static function get_for_select($tenant_id = null)
	{
		$tenant_id = $tenant_id ?: Helper_User_Tenant::get_default_tenant_id();
		
		$categories = static::query()
			->where('tenant_id', $tenant_id)
			->where('is_active', 1)
			->order_by('name', 'asc')
			->get();

		$result = array('' => '-- Seleccionar categoría --');
		foreach ($categories as $cat)
		{
			$result[$cat->id] = $cat->name;
		}

		return $result;
	}
}
