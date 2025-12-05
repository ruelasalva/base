<?php

/**
 * Modelo de Precio por Producto
 * Relación entre productos y listas de precios
 */
class Model_Product_Price extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'tenant_id',
		'product_id',
		'price_list_id',
		'price',
		'min_quantity',
		'max_quantity',
		'is_active',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'product_prices';

	protected static $_belongs_to = array(
		'product' => array(
			'key_from' => 'product_id',
			'model_to' => 'Model_Product',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'price_list' => array(
			'key_from' => 'price_list_id',
			'model_to' => 'Model_Price_List',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	/**
	 * Obtener precio para un producto en una lista específica
	 */
	public static function get_price_for_list($product_id, $price_list_id, $quantity = 1)
	{
		return static::query()
			->where('product_id', $product_id)
			->where('price_list_id', $price_list_id)
			->where('is_active', 1)
			->where('min_quantity', '<=', $quantity)
			->where_open()
				->where('max_quantity', '>=', $quantity)
				->or_where('max_quantity', 'IS', null)
			->where_close()
			->order_by('min_quantity', 'desc')
			->get_one();
	}

	/**
	 * Obtener todos los precios de un producto
	 */
	public static function get_product_prices($product_id, $active_only = true)
	{
		$query = static::query()
			->where('product_id', $product_id)
			->related('price_list');
		
		if ($active_only) {
			$query->where('is_active', 1);
		}

		return $query->get();
	}
}
