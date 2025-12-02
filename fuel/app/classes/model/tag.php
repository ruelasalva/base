<?php

class Model_Tag extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"slug" => array(
			"label" => "Slug",
			"data_type" => "varchar",
		),
		"name" => array(
			"label" => "Name",
			"data_type" => "varchar",
		),
		"image" => array(
			"label" => "Image",
			"data_type" => "varchar",
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


	/* Functions */
	public static function get_all()
	{
		$tags = Model_Tag::query()
		->where('deleted', 0)
		->order_by('id', 'desc')
		->get();

		return $tags;
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

	protected static $_table_name = 'tags';

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
