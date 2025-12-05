<?php

/**
 * Modelo de Lista de Precios
 * Catálogo de listas de precios personalizadas
 */
class Model_Price_List extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'tenant_id',
		'name',
		'code',
		'description',
		'type', // percentage o fixed
		'discount_value',
		'is_active',
		'priority',
		'created_at',
		'updated_at',
		'deleted_at',
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

	protected static $_table_name = 'price_lists';

	protected static $_has_many = array(
		'product_prices' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Product_Price',
			'key_to' => 'price_list_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);

	/**
	 * Obtener listas activas de un tenant
	 */
	public static function get_active_lists($tenant_id = null)
	{
		$tenant_id = $tenant_id ?: Session::get('tenant_id', 1);
		
		return static::query()
			->where('tenant_id', $tenant_id)
			->where('is_active', 1)
			->where('deleted_at', 'IS', null)
			->order_by('priority', 'asc')
			->order_by('name', 'asc')
			->get();
	}

	/**
	 * Calcular precio aplicando la lista
	 */
	public function calculate_price($base_price)
	{
		if ($this->type == 'percentage') {
			// Descuento porcentual
			return $base_price * (1 - ($this->discount_value / 100));
		} else {
			// Descuento fijo
			return max(0, $base_price - $this->discount_value);
		}
	}
}
