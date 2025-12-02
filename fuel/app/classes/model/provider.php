<?php

class Model_Provider extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'name',
		'code_sap',
		'rfc',
		'user_id',
		'employee_id',
		'require_purchase',
		'payment_terms_id',
		'csf',
		'origin',
		'provider_type',
		'created_at',
		'updated_at'
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events'          => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events'          => array('before_save'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'providers';

	public static function get_for_input()
{
    $arr = [];
    $proveedores = static::query()->order_by('name', 'asc')->get();
    foreach ($proveedores as $p) {
        $arr[$p->id] = $p->name . ' (' . $p->rfc . ')';
    }
    return $arr;
}


	protected static $_primary_key = array('id');

	protected static $_belongs_to = array(
		'user' => array(
			'key_from'       => 'user_id',
			'model_to'       => 'Model_User',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'employee' => array(
			'key_from'       => 'employee_id',
			'model_to'       => 'Model_Employee',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_has_one = array(
	
	'provider_tax_datum' => array(
		'model_to' => 'Model_Providers_Tax_Datum',
		'key_from' => 'id',
		'key_to' => 'provider_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	),

	'provider_delivery' => array(
		'model_to' => 'Model_Providers_Delivery',
		'key_from' => 'id',
		'key_to' => 'provider_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	),

	'provider_purchase' => array(
		'model_to' => 'Model_Providers_Purchase',
		'key_from' => 'id',
		'key_to' => 'provider_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	),

	'provider_account' => array(
		'model_to' => 'Model_Providers_Account',
		'key_from' => 'id',
		'key_to' => 'provider_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	),
	'payment_term' => array(
		'model_to' => 'Model_Payments_Term',
		'key_from' => 'payment_terms_id',
		'key_to' => 'id',
		'cascade_save' => true,
		'cascade_delete' => false,
	)
	
);

protected static $_has_many = [
    'departments' => [
        'key_from' => 'id',
        'model_to' => 'Model_Providers_Department',
        'key_to' => 'provider_id',
        'cascade_save' => true,
        'cascade_delete' => false,
    ],
];

}
