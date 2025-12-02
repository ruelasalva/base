<?php

class Model_Coupon extends \Orm\Model
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
		"discount" => array(
			"label" => "Discount",
			"data_type" => "float",
		),
		"quantity" => array(
			"label" => "Quantity",
			"data_type" => "int",
		),
		"available" => array(
			"label" => "Available",
			"data_type" => "int",
		),
		"minimum" => array(
			"label" => "Minimum",
			"data_type" => "int",
		),
		"total_minimum" => array(
			"label" => "Total minimum",
			"data_type" => "float",
		),
		"start_date" => array(
			"label" => "Start date",
			"data_type" => "int",
		),
		"end_date" => array(
			"label" => "End date",
			"data_type" => "int",
		),
		"deleted" => array(
			"label" => "Deleted",
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

	protected static $_table_name = 'coupons';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'codes' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Coupons_Code',
            'key_to'         => 'coupon_id',
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
