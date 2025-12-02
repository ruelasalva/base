<?php

class Model_Products_Price extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"type_id" => array(
			"label" => "Type id",
			"data_type" => "int",
		),
		"product_id" => array(
			"label" => "Product id",
			"data_type" => "int",
		),
		"price" => array(
			"label" => "Price",
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


	/* Functions */
    public static function get_price($product_id = 0, $type_id = 0)
    {
		# SE INICIALIZA LA VARIABLE
		$price = 0;

		# SE OBTIENE LA INFORMACION DEL MODELO
        $product_price = Model_Products_Price::query()
        ->where('type_id', $type_id)
        ->where('product_id', $product_id)
        ->get_one();

        # SI SE OBTUVO LA INFORMACION
        if(!empty($product_price))
        {
            # SE ALMACENA LA INFORMACION
			$price = $product_price->price;
        }

		# SE DEVUELVE LA VARIABLE
        return $price;
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

	protected static $_table_name = 'products_prices';

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
