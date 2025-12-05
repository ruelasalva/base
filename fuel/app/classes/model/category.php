<?php

class Model_Category extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"parent_id" => array(
			"label" => "Parent ID",
			"data_type" => "int",
		),
		"name" => array(
			"label" => "Name",
			"data_type" => "varchar",
		),
		"slug" => array(
			"label" => "Slug",
			"data_type" => "varchar",
		),
		"description" => array(
			"label" => "Description",
			"data_type" => "text",
		),
		"image" => array(
			"label" => "Image",
			"data_type" => "varchar",
		),
		"sort_order" => array(
			"label" => "Sort Order",
			"data_type" => "int",
		),
		"is_active" => array(
			"label" => "Is Active",
			"data_type" => "int",
		),
		"created_at" => array(
			"label" => "Created at",
			"data_type" => "datetime",
		),
		"updated_at" => array(
			"label" => "Updated at",
			"data_type" => "datetime",
		),
		"deleted_at" => array(
			"label" => "Deleted at",
			"data_type" => "datetime",
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

	protected static $_table_name = 'categories';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'products' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Product',
			'key_to'         => 'category_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'children' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Category',
			'key_to'         => 'parent_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'parent' => array(
			'key_from'       => 'parent_id',
			'model_to'       => 'Model_Category',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	/* Functions */
	public static function get_menu()
	{
		$categories_info = array();

		$categories = Model_Category::query()
		->where('deleted_at', 'IS', null)
		->where('is_active', 1)
		->order_by('name', 'asc')
		->get();

		foreach($categories as $category)
		{
			$categories_info[] = array(
				'slug' => $category->slug,
				'name' => $category->name
			);
		}

		return $categories_info;
    }

	public static function get_opts()
	{
		$category_opts = array();

		$category_opts += array('todo' => 'Todo');

		$categories = Model_Category::query()
		->where('deleted_at', 'IS', null)
		->where('is_active', 1)
		->order_by('name', 'asc')
		->get();

		foreach($categories as $category)
		{
			$category_opts += array($category->slug => $category->name);
		}

		return $category_opts;
	}

}
