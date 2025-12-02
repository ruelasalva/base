<?php

class Model_Products_Image extends \Orm\Model
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
		"image" => array(
			"label" => "Image",
			"data_type" => "varchar",
		),
		"order" => array(
			"label" => "Order",
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
	public static function get_one($request)
	{
		$response = Model_Products_Image::query();

		if(Arr::get($request, 'id_product'))
		{
			$response = $response->where('product_id', $request['id_product']);
		}

		if(Arr::get($request, 'id_color'))
		{
			$response = $response->where('color_id', $request['id_color']);
        }

        if(Arr::get($request, 'id_model'))
		{
			$response = $response->where('model_id', $request['id_model']);
		}

		$response = $response->get_one();

		return $response;
	}

	public static function get_product_gallery($id_product)
	{
		$response = Model_Products_Image::query()
		->where('product_id', $id_product)
		->order_by('order', 'asc')
		->get();

		return $response;
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

	protected static $_table_name = 'products_images';

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
