<?php

class Model_Subcategory extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		/*"category_id" => array(
			"label" => "Category Id",
			"data_type" => "int",
		),*/
		"slug" => array(
			"label" => "Slug",
			"data_type" => "varchar",
		),
		"name" => array(
			"label" => "Name",
			"data_type" => "varchar",
		),
		"deleted" => array(
			"label" => "Deleted",
			"data_type" => "int",
		),
		"status" => array(
			"label" => "Status",
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

	protected static $_table_name = 'subcategories';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'products' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Product',
			'key_to'         => 'subcategory_id',
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

	/* Functions */
	public static function get_menu()
	{
		$subcategories_info = array();

		$subcategories = Model_Subcategory::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		foreach($subcategories as $subcategory)
		{
			$categories_info[] = array(
				'slug' => $subcategory->slug,
				'name' => $subcategory->name
			);
		}

		return $subcategories_info;
    }

	public static function get_opts()
	{
		$category_opts = array();

		$category_opts += array('todo' => 'Todo');

		$categories = Model_Subcategory::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		foreach($subcategories as $subcategory)
		{
			$subcategory_opts += array($subcategory->slug => $subcategory->name);
		}

		return $subcategory_opts;
	}

}
