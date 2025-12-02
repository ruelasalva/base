<?php

class Model_Products_Prices_Wholesale extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"product_id" => array(
			"label" => "Product id",
			"data_type" => "int",
		),
		"min_quantity" => array(
			"label" => "Min quantity",
			"data_type" => "int",
		),
		"max_quantity" => array(
			"label" => "Max quantity",
			"data_type" => "int",
		),
		"price" => array(
			"label" => "Price",
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

	protected static $_table_name = 'products_prices_wholesales';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'product' => array(
			'key_from'       => 'product_id',
			'model_to'       => 'Model_Product',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);
}
