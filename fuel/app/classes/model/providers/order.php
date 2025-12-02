<?php

class Model_Providers_Order extends \Orm\Model
{
	protected static $_properties = array(
    'id',
    'provider_id',
	'origin', 
	'code_order', 
    'date_order',
    'payment_date',
    'subtotal',
    'iva',
    'retencion',
    'total',
    'currency_id', 
    'retention', 
    'tax_id', 
    'has_invoice', 
    'status',
    'notes',
    'uuid',
	'authorized_at',
    'authorized_by',
    'invoiced_total',
    'balance_total',
    'deleted',
    'created_at',
    'updated_at'
);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'providers_orders';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'bills' => array(
            'key_from' 		 => 'id',
            'model_to' 		 => 'Model_Providers_Bill',
            'key_to' 		 => 'order_id', 
            'cascade_save' 	 => false,
            'cascade_delete' => false,
        ),
		'details' => array (
			'key_from' 		 => 'id',
			'model_to' 		 => 'Model_Providers_Order_Detail',
			'key_to' 		 => 'order_id',
			'cascade_save' 	 => true,
			'cascade_delete' => false,
		),
	);

	protected static $_many_many = array(
		
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'provider' => array(
			'key_from' 		 => 'provider_id',
            'model_to' 		 => 'Model_Provider',
            'key_to'   		 => 'id',
            'cascade_save' 	 => false,
            'cascade_delete' => false,
        ),
		'currency' => array(
			'key_from' 			=> 'currency_id',
			'model_to' 			=> 'Model_Currency',
			'key_to' 			=> 'id',
			'cascade_save' 		=> false,
			'cascade_delete' 	=> false,
    	),
		'tax' => array(
			'key_from' 			=> 'tax_id',
			'model_to' 			=> 'Model_Tax',
			'key_to' 			=> 'id',
			'cascade_save' 		=> false,
			'cascade_delete' 	=> false,
		),
		
	);

	// LISTADO DE ÓRDENES POR PROVEEDOR
	public static function get_for_provider($provider_id)
	{
		$arr = [];
		$ordenes = static::query()
			->where('provider_id', $provider_id)
			->where('deleted', 0)
			->order_by('created_at', 'desc')
			->get();

		foreach ($ordenes as $o) {
			$arr[$o->id] = $o->code_order . ' ($' . number_format($o->total, 2) . ')';
		}
		return $arr;
	}

	 // GENERAR NÚMERO DE ORDEN AUTOMÁTICAMENTE
    public static function generate_code()
    {
        return Helper_Oc::next_code();
    }

}
