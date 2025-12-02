<?php

class Model_Payment extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"type_id" => array(
			"label" => "Type id",
			"data_type" => "int",
		),
		"token" => array(
			"label" => "Token",
			"data_type" => "varchar",
		),
		"total" => array(
			"label" => "Total",
			"data_type" => "float",
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
		$response = new Model_Payment($request);

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

	protected static $_table_name = 'payments';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
		'sale' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Sale',
			'key_to'         => 'payment_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_belongs_to = array(
		'type' => array(
			'key_from'       => 'type_id',
			'model_to'       => 'Model_Payments_Type',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

}
