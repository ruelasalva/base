<?php

class Model_Partner extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'code_sap',
		'name',
		'email',
		'rfc',
		'customer_id',
		'user_id',
		'employee_id',
		'type_id',
		'payment_terms_id',
		'deleted',
		'created_at',
		'updated_at'
	);

	/* Functions */
	public static function get_one($request)
	{
		$response = Model_Partner::query();

		if(Arr::get($request, 'id_user'))
		{
			$response = $response->where('user_id', $request['id_user']);
		}

		$response = $response->get_one();

		return $response;
    }

    public static function set_new_record($request)
	{
		$response = new Model_Partner($request);

		return ($response->save()) ? $response : false;
    }

    public static function do_update($request, $id)
	{
		$response = Model_Partner::find($id);
        $response->set($request);

		return ($response->save()) ? $response : false;
    }

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

	protected static $_table_name = 'partners';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'quotes' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Quote',
			'key_to'         => 'partner_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'addresses' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Partners_Address',
			'key_to'         => 'partner_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'delivery' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Partners_Delivery',
			'key_to'         => 'partner_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'tax_data' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Partners_Tax_Datum',
			'key_to'         => 'partner_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);


	protected static $_belongs_to = array(
		'user' => array(
			'key_from'       => 'user_id',
			'model_to'       => 'Model_User',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'customer' => array(
			'key_from'       => 'customer_id',
			'model_to'       => 'Model_Customer',
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
		),
		'type' => array(
			'key_from'       => 'type_id',
			'model_to'       => 'Model_Customers_Type',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_has_one = array(
	
	'partner_tax_datum' => array(
		'model_to' => 'Model_Partners_Tax_Datum',
		'key_from' => 'id',
		'key_to' => 'partner_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	),

	'partner_delivery' => array(
		'model_to' => 'Model_Partners_Delivery',
		'key_from' => 'id',
		'key_to' => 'partner_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	),

	'partner_purchase' => array(
		'model_to' => 'Model_Partners_Purchase',
		'key_from' => 'id',
		'key_to' => 'partner_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	),

	'partner_account' => array(
		'model_to' => 'Model_Partners_Account',
		'key_from' => 'id',
		'key_to' => 'partner_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	)
);

}
