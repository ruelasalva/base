<?php

class Model_Slide extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"image" => array(
			"label" => "Image",
			"data_type" => "varchar",
		),
		"url" => array(
			"label" => "Url",
			"data_type" => "varchar",
		),
		"order" => array(
			"label" => "Order",
			"data_type" => "int",
		),
		"status" => array(
			"label" => "Estatus",
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
	public static function get_carousel()
	{
		$slides = Model_Slide::query()
		->order_by('order', 'asc')
		->where('status', 1)
		->get();

		return $slides;
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

	protected static $_table_name = 'slides';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
	);

}
