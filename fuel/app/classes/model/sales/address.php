<?php

class Model_Sales_Address extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"state_id" => array(
			"label" => "State id",
			"data_type" => "int",
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
		"street" => array(
			"label" => "Street",
			"data_type" => "varchar",
		),
		"number" => array(
			"label" => "Number",
			"data_type" => "varchar",
		),
		"internal_number" => array(
			"label" => "Internal number",
			"data_type" => "varchar",
		),
		"colony" => array(
			"label" => "Colony",
			"data_type" => "varchar",
		),
		"zipcode" => array(
			"label" => "Zipcode",
			"data_type" => "varchar",
		),
		"city" => array(
			"label" => "City",
			"data_type" => "varchar",
		),
		"details" => array(
			"label" => "Details",
			"data_type" => "mediumtext",
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
    public static function set_new_record($request)
	{
		$response = new Model_Sales_Address($request);

		return ($response->save()) ? $response : false;
    }

    public static function do_update($request, $id)
	{
		$response = Model_Sales_Address::find($id);
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

	protected static $_table_name = 'sales_addresses';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
		'sale' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Sale',
			'key_to'         => 'address_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_belongs_to = array(
		'state' => array(
			'key_from'       => 'state_id',
			'model_to'       => 'Model_State',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

}
