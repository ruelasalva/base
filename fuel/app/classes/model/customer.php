<?php

class Model_Customer extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"user_id" => array(
			"label" => "User id",
			"data_type" => "int",
		),
		"employee_id" => array(
			"label" => "Employer id",
			"data_type" => "int",
		),
		"parent_id" => array(
			"label" => "Parent id",
			"data_type" => "int",
		),
		"type_id" => array(
			"label" => "Type id",
			"data_type" => "int",
		),
		"sap_code" => array(
			"label" => "Codigo Sap",
			"data_type" => "varchar",
		),
		"name" => array(
			"label" => "Name",
			"data_type" => "varchar",
		),
		"last_name" => array(
			"label" => "Last name",
			"data_type" => "varchar",
		),
		"phone" => array(
			"label" => "Phone",
			"data_type" => "varchar",
		),
		"require_bill" => array(
			"label" => "Require bill",
			"data_type" => "int",
		),
		"created_at" => array(
			"label" => "Created at",
			"data_type" => "int",
		),
		"updated_at" => array(
			"label" => "Updated at",
			"data_type" => "int",
		),
    );

    /* Functions */
	public static function get_one($request)
	{
		$response = Model_Customer::query();

		if(Arr::get($request, 'id_user'))
		{
			$response = $response->where('user_id', $request['id_user']);
		}

		$response = $response->get_one();

		return $response;
    }

    public static function set_new_record($request)
	{
		$response = new Model_Customer($request);

		return ($response->save()) ? $response : false;
    }

    public static function do_update($request, $id)
	{
		$response = Model_Customer::find($id);
        $response->set($request);

		return ($response->save()) ? $response : false;
    }

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

	protected static $_table_name = 'customers';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'sales' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Sale',
			'key_to'         => 'customer_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'addresses' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Customers_Address',
			'key_to'         => 'customer_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'tax_data' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Customers_Tax_Datum',
			'key_to'         => 'customer_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
		'wishlist' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Wishlist',
			'key_to'         => 'customer_id',
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

}
