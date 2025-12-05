<?php

class Model_Product extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"tenant_id" => array(
			"label" => "Tenant ID",
			"data_type" => "int",
		),
		"sku" => array(
			"label" => "SKU",
			"data_type" => "varchar",
		),
		"barcode" => array(
			"label" => "Barcode",
			"data_type" => "varchar",
		),
		"codigo_venta" => array(
			"label" => "Codigo Venta",
			"data_type" => "varchar",
		),
		"codigo_compra" => array(
			"label" => "Codigo Compra",
			"data_type" => "varchar",
		),
		"codigo_externo" => array(
			"label" => "Codigo Externo",
			"data_type" => "varchar",
		),
		"name" => array(
			"label" => "Name",
			"data_type" => "varchar",
		),
		"slug" => array(
			"label" => "Slug",
			"data_type" => "varchar",
		),
		"short_description" => array(
			"label" => "Short Description",
			"data_type" => "varchar",
		),
		"description" => array(
			"label" => "Description",
			"data_type" => "text",
		),
		"tags" => array(
			"label" => "Tags",
			"data_type" => "text",
		),
		"category_id" => array(
			"label" => "Category ID",
			"data_type" => "int",
		),
		"provider_id" => array(
			"label" => "Provider ID",
			"data_type" => "int",
		),
		"brand" => array(
			"label" => "Brand",
			"data_type" => "varchar",
		),
		"model" => array(
			"label" => "Model",
			"data_type" => "varchar",
		),
		"unit" => array(
			"label" => "Unit",
			"data_type" => "varchar",
		),
		"cost_price" => array(
			"label" => "Cost Price",
			"data_type" => "decimal",
		),
		"sale_price" => array(
			"label" => "Sale Price",
			"data_type" => "decimal",
		),
		"wholesale_price" => array(
			"label" => "Wholesale Price",
			"data_type" => "decimal",
		),
		"min_price" => array(
			"label" => "Min Price",
			"data_type" => "decimal",
		),
		"tax_rate" => array(
			"label" => "Tax Rate",
			"data_type" => "decimal",
		),
		"weight" => array(
			"label" => "Weight",
			"data_type" => "decimal",
		),
		"length" => array(
			"label" => "Length",
			"data_type" => "decimal",
		),
		"width" => array(
			"label" => "Width",
			"data_type" => "decimal",
		),
		"height" => array(
			"label" => "Height",
			"data_type" => "decimal",
		),
		"min_stock" => array(
			"label" => "Min Stock",
			"data_type" => "int",
		),
		"max_stock" => array(
			"label" => "Max Stock",
			"data_type" => "int",
		),
		"stock_quantity" => array(
			"label" => "Stock Quantity",
			"data_type" => "int",
		),
		"is_featured" => array(
			"label" => "Is Featured",
			"data_type" => "tinyint",
		),
		"is_active" => array(
			"label" => "Is Active",
			"data_type" => "tinyint",
		),
		"is_available" => array(
			"label" => "Is Available",
			"data_type" => "tinyint",
		),
		"sort_order" => array(
			"label" => "Sort Order",
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


    /* Functions */
	public static function get_highlighted()
	{
		$response = Model_Product::query()
		->where('is_active', 1)
		->where('is_featured', 1)
        ->where('deleted_at', 'IS', null)
        ->order_by('id', 'desc')
		->limit(8)
		->get();

		return $response;
	}

	public static function get_news()
	{
		$response = Model_Product::query()
		->where('is_active', 1)
        ->where('deleted_at', 'IS', null)
        ->where('is_featured', 1)
        ->order_by('id', 'desc')
		->limit(12)
		->get();

		return $response;
	}

	public static function get_valid($request = null)
	{
        $response = Model_Product::query();

        if(isset($request))
        {
            if(Arr::get($request, 'id_product'))
            {
                $response = $response->where('id', $request['id_product']);
            }
        }

        $response = $response->where('is_active', 1)
        ->where('stock_quantity', '>', 0)
		->where('deleted_at', 'IS', null)
		->get_one();

		if(empty($response))
		{
			$response = Model_Product::query();

	        if(isset($request))
	        {
	            if(Arr::get($request, 'id_product'))
	            {
	                $response = $response->where('id', $request['id_product']);
	            }
	        }

	        $response = $response->where('is_active', 1)
			->where('deleted_at', 'IS', null)
			->get_one();
		}

		return $response;
    }

	public static function get_product($request = null)
	{
        $response = Model_Product::query()
		->related('products_prices_wholesales')
		->related('products_price_amount');

        if(isset($request))
        {
            if(Arr::get($request, 'slug'))
            {
                $response = $response->where('slug', $request['slug']);
            }
        }

        $response = $response->where('is_active', 1)
        ->where('available', '>=', 0)
		->where('deleted_at', 'IS', null)
		->get_one();

		return $response;
    }

	public static function get_catalog($request, $pagination)
	{
		$response = array();

		$products = Model_Product::query();

		# SI REQUEST ESTA DEFINIDO
		if(isset($request))
		{
            if(Arr::get($request, 'id_category'))
            {
                $products = $products->where('category_id', $request['id_category']);
            }
        }

        $products = $products->where('category_id', '!=', 0)
        ->where('is_active', 1)
		->where('deleted_at', 'IS', null);

		# SI SE REQUIERE PAGINACION
		if(isset($pagination))
		{
			$config = array(
				'pagination_url' => $pagination['pagination_url'],
				'total_items'    => $products->count(),
				'per_page'       => $pagination['per_page'],
				'uri_segment'    => 'pagina',
				'num_links'      => 2,
			);

			$pagination_object = Pagination::forge('products', $config);
		}

		$products = $products->order_by('id', 'desc');

		# SI SE REQUIERE PAGINACION
		if(isset($pagination))
		{
			$products = $products->rows_offset($pagination_object->offset)
			->rows_limit($pagination_object->per_page);
		}

		$products = $products->get();

		# SI SE REQUIERE PAGINACION
		if(isset($pagination))
		{
			$response['data']       = $products;
			$response['pagination'] = $pagination_object;
		}
		else
		{
			$response = $products;
		}

		return $response;
	}

	public static function get_catalog_related_products($id_product, $id_category)
	{
		$related_products = Model_Product::query()
		->where('id', '!=', $id_product)
		->where('category_id', $id_category)
        ->where('is_active', 1)
		->where('deleted_at', 'IS', null)
		->order_by(DB::expr('RAND()'))
		->limit(4)
		->get();

		return $related_products;
    }

    public static function do_search($request)
    {
        $response = Model_Product::query()
        ->where('is_active', 1)
        ->where('deleted_at', 'IS', null)
        ->and_where_open();

        if (Arr::get($request, 'search'))
		{
			$search = $request['search'];

			$response = $response
				->where_open()
					->where('name', 'like', '%'.$search.'%')
					->or_where('sku', 'like', '%'.$search.'%')
					->or_where('barcode', 'like', '%'.$search.'%')
					->or_where('brand', 'like', '%'.$search.'%')
				->where_close();
		}
        $response = $response->and_where_close()
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

	protected static $_table_name = 'products';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'galleries' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Products_Image',
			'key_to'         => 'product_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'sales' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Sales_Product',
			'key_to'         => 'product_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'products_prices_wholesales' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Products_Prices_Wholesale',
			'key_to'         => 'product_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'products_files' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Products_File',
			'key_to' => 'product_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
		'price' => array(
	        'key_from'       => 'id',
	        'model_to'       => 'Model_Products_Price',
	        'key_to'         => 'product_id',
	        'cascade_save'   => false,
	        'cascade_delete' => false,
	    ),
		'products_price_amount' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Products_Prices_Amount',
			'key_to'         => 'product_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_belongs_to = array(
		'category' => array(
			'key_from'       => 'category_id',
			'model_to'       => 'Model_Category',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'provider' => array(
			'key_from'       => 'provider_id',
			'model_to'       => 'Model_Provider',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

}

