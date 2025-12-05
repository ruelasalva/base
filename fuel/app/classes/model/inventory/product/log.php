<?php

/**
 * Model_Inventory_Product_Log
 * 
 * Modelo para logs de productos de inventario
 */
class Model_Inventory_Product_Log extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'tenant_id',
		'product_id',
		'user_id',
		'action',
		'description',
		'old_values',
		'new_values',
		'ip_address',
		'created_at'
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'inventory_product_logs';

	protected static $_primary_key = array('id');

	protected static $_belongs_to = array(
		'product' => array(
			'key_from' => 'product_id',
			'model_to' => 'Model_Inventory_Product',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'user' => array(
			'key_from' => 'user_id',
			'model_to' => 'Model_User',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	/**
	 * Registrar acción en log
	 * 
	 * @param  int     $product_id
	 * @param  string  $action
	 * @param  string  $description
	 * @param  array   $old_values
	 * @param  array   $new_values
	 * @return bool
	 */
	public static function log_action($product_id, $action, $description = null, $old_values = null, $new_values = null)
	{
		try
		{
			$log = static::forge(array(
				'tenant_id' => Helper_User_Tenant::get_default_tenant_id(),
				'product_id' => $product_id,
				'user_id' => Auth::check() ? Auth::get('id') : null,
				'action' => $action,
				'description' => $description,
				'old_values' => $old_values ? json_encode($old_values) : null,
				'new_values' => $new_values ? json_encode($new_values) : null,
				'ip_address' => Input::real_ip(),
			));

			return $log->save();
		}
		catch (\Exception $e)
		{
			\Log::error('Error al registrar log de producto: ' . $e->getMessage());
			return false;
		}
	}
}
