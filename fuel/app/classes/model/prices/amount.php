<?php

class Model_Prices_Amount extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"amount_id" => array(
			"label" => "amount Id",
			"data_type" => "int",
		),
		"min_amount" => array(
			"label" => "Min amount",
			"data_type" => "float",
		),
		"max_amount" => array(
			"label" => "Max amount",
			"data_type" => "float",
		),
		"percentage" => array(
			"label" => "Percentage",
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

	protected static $_table_name = 'prices_amounts';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'amount' => array(
			'key_from'       => 'amount_id',
			'model_to'       => 'Model_Amount',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);
}
