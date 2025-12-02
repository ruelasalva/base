<?php

class Model_Wishlist extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"customer_id" => array(
			"label" => "Customer id",
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

	protected static $_table_name = 'wishlists';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'products' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Wishlists_Product',
            'key_to'         => 'wishlist_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        )
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'customer' => array(
            'key_from'       => 'customer_id',
            'model_to'       => 'Model_Customer',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        )
	);

}
