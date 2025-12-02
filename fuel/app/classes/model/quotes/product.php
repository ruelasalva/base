<?php

class Model_Quotes_Product extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"quote_id" => array(
			"label" => "Quote id",
			"data_type" => "int",
		),
		"product_id" => array(
			"label" => "Product id",
			"data_type" => "int",
		),
		"quantity" => array(
			"label" => "Quantity",
			"data_type" => "int",
		),
		"price" => array(
			"label" => "Price",
			"data_type" => "float",
		),
		"discount" => array(
			"label" => "Discount",
			"data_type" => "float",
		),
		"retention" => array(
			"label" => "Retention",
			"data_type" => "float",
		),
		"total" => array(
			"label" => "Total",
			"data_type" => "float",
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
	public static function get_one($request)
	{
		$response = Model_Quotes_Product::query();

		if(Arr::get($request, 'id_quote'))
		{
			$response = $response->where('quote_id', $request['id_quote']);
		}

		if(Arr::get($request, 'id_product'))
		{
			$response = $response->where('product_id', $request['id_product']);
		}

		$response = $response->get_one();

		return $response;
	}

	public static function get_all_products($quote_id)
	{
		$response = Model_Quotes_Product::query()
		->where('quote_id', $quote_id)
		->get();

		return $response;
	}

	public static function set_new_product($request)
	{
		$response = new Model_Quotes_Product($request);

		return $response->save();
	}

	public static function do_update($request, $id_quote_product)
	{
		$response = Model_Quotes_Product::find($id_quote_product);
		$response->set($request);

		return ($response->save()) ? $response : false;
	}

	public static function do_delete($id_quote_product)
	{
		$response = Model_Quotes_Product::find($id_quote_product);

		return $response->delete();
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

	protected static $_table_name = 'quotes_products';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'quote' => array(
			'key_from'       => 'quote_id',
			'model_to'       => 'Model_Quote',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'product' => array(
			'key_from'       => 'product_id',
			'model_to'       => 'Model_Product',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

}
