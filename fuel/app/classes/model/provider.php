<?php

class Model_Provider extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'code',
		'company_name',
		'contact_name',
		'email',
		'phone',
		'phone_secondary',
		'address',
		'city',
		'state',
		'postal_code',
		'country',
		'tax_id',
		'website',
		'notes',
		'payment_terms',
		'credit_limit',
		'is_active',
		'created_at',
		'updated_at',
		'deleted_at',
		'is_suspended',
		'suspended_reason',
		'suspended_at',
		'activated_at',
		'activated_by'
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
    $proveedores = static::query()->order_by('company_name', 'asc')->get();
    foreach ($proveedores as $p) {
        $arr[$p->id] = $p->company_name . ' (' . $p->tax_id . ')';
    }
    return $arr;
}


	protected static $_primary_key = array('id');

	// Relaciones eliminadas: user_id y employee_id no existen en la tabla actual
	protected static $_belongs_to = array();

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
