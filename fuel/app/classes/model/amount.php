<?php

class Model_Amount extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"name" => array(
			"label" => "Name",
			"data_type" => "varchar",
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

	protected static $_table_name = 'amounts';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'products_prices_amounts' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Products_Prices_Amount',
			'key_to'         => 'amount_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'prices_amounts' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Prices_Amount',
			'key_to'         => 'amount_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
	);
}
