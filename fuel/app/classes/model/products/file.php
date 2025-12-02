<?php

class Model_Products_File extends \Orm\Model
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
		"file_type_id" => array(
			"label" => "Type id",
			"data_type" => "int",
		),
		"file_name" => array(
			"label" => "File Name",
			"data_type" => "varchar",
		),
		"file_path" => array(
			"label" => "File Name",
			"data_type" => "varchar",
		),
		"order" => array(
			"label" => "Order",
			"data_type" => "int",
		),
		"downloads" => array(
			"label" => "Downloads",
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

	// En Model_Products_File.php
	public static function get_product_files($product_id)
	{
		// Trae los archivos relacionados a un producto, con su tipo
		return self::query()
			->related('file_type')
			->where('product_id', $product_id)
			->order_by('order', 'asc') // Si tienes campo 'order'
			->get();
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

	protected static $_table_name = 'products_files';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'files' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Products_File',
			'key_to'   => 'product_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'file_type' => array(
        'key_from' => 'file_type_id',                
        'model_to' => 'Model_Products_File_Type',    
        'key_to'   => 'id',                          
        'cascade_save' => false,
        'cascade_delete' => false,
    ),
    'product' => array(
        'key_from' => 'product_id',
        'model_to' => 'Model_Product',
        'key_to'   => 'id',
        'cascade_save' => false,
        'cascade_delete' => false,
    ),
	);

}
