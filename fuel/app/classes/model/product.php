<?php

class Model_Product extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"category_id" => array(
			"label" => "Category id",
			"data_type" => "int",
		),
		"subcategory_id" => array(
			"label" => "Subcategory id",
			"data_type" => "int",
		),
		"brand_id" => array(
			"label" => "Brand id",
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
		"name_order" => array(
			"label" => "Name Order",
			"data_type" => "varchar",
		),
		"code" => array(
			"label" => "Code",
			"data_type" => "varchar",
		),
		"code_order" => array(
			"label" => "Code_order",
			"data_type" => "varchar",
		),
		"sku" => array(
			"label" => "Sku",
			"data_type" => "varchar",
		),
		"factor" => array(
			"label" => "Facrtor",
			"data_type" => "int",
		),
		"purchase_unit_id" => array(
			"label" => "Unidad compra",
			"data_type" => "int",
		),
		"sale_unit_id" => array(
			"label" => "Unidad venta",
			"data_type" => "int",
		),
		"claveprodserv" => array(
			"label" => "Clave Sat",
			"data_type" => "int",
		),
		"claveunidad" => array(
			"label" => "Clave Sat",
			"data_type" => "varchar",
		),
		"codebar" => array(
			"label" => "CodeBar",
			"data_type" => "int",
		),
		"image" => array(
			"label" => "Image",
			"data_type" => "varchar",
		),
		"description" => array(
			"label" => "Description",
			"data_type" => "mediumtext",
		),
		"original_price" => array(
			"label" => "Original price",
			"data_type" => "float",
		),
		"available" => array(
			"label" => "Available",
			"data_type" => "int",
		),
		"minimum_sale" => array(
			"label" => "Minimum Sale",
			"data_type" => "int",
		),
		"minimum_order" => array(
			"label" => "Minimum Order",
			"data_type" => "int",
		),
		"weight" => array(
			"label" => "Peso",
			"data_type" => "float",
		),
		"price_per" => array(
			"label" => "Price per",
			"data_type" => "varchar",
		),
		"status" => array(
			"label" => "Status",
			"data_type" => "int",
		),
		"status_index" => array(
			"label" => "Status index",
			"data_type" => "int",
		),
		"soon" => array(
			"label" => "Soon",
			"data_type" => "int",
		),
		"newproduct" => array(
			"label" => "New Product",
			"data_type" => "int",
		),
		"temporarily_sold_out" => array(
			"label" => "Agotado temporalmente",
			"data_type" => "int",
		),
		"deleted" => array(
			"label" => "Deteled",
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
	public static function get_highlighted()
	{
		$response = Model_Product::query()
		->where('status', 1)
		->where('status_index', 1)
        ->where('deleted', 0)
        ->order_by('id', 'desc')
		->limit(8)
		->get();

		return $response;
	}

	public static function get_news()
	{
		$response = Model_Product::query()
		->where('status', 1)
        ->where('deleted', 0)
        ->where('newproduct', 1)
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

        $response = $response->where('status', 1)
        ->where('available', '>', 0)
		->where('deleted', 0)
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

	        $response = $response->where('status', 1)
			->where('deleted', 0)
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

        $response = $response->where('status', 1)
        ->where('available', '>=', 0)
		->where('deleted', 0)
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
        ->where('status', 1)
		->where('deleted', 0);

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
        ->where('status', 1)
		->where('deleted', 0)
		->order_by(DB::expr('RAND()'))
		->limit(4)
		->get();

		return $related_products;
    }

    public static function do_search($request)
    {
        $response = Model_Product::query()
        ->where('status', 1)
        ->where('deleted', 0)
        ->and_where_open();

        if (Arr::get($request, 'search'))
			{
				$search = $request['search'];

				$response = $response
					->where_open()
						->where_open()
							->where('name', 'like', '%'.$search.'%')
							->or_where('code', 'like', '%'.$search.'%')
							->or_where('sku', 'like', '%'.$search.'%')
						->where_close()
						->and_where('category.deleted', 0)
						->and_where('brand.deleted', 0)
						->or_where_open()
							->related('category')
							->where('category.name', 'like', '%'.$search.'%')
							->where('category.deleted', 0)
							->related('brand')
							->where('brand.name', 'like', '%'.$search.'%')
							->where('brand.deleted', 0)
						->or_where_close()
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
		'subcategory' => array(
			'key_from'       => 'subcategory_id',
			'model_to'       => 'Model_Subcategory',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'brand' => array(
			'key_from'       => 'brand_id',
			'model_to'       => 'Model_Brand',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'sale_unit' => array(
			'key_from'       => 'sale_unit_id',
			'model_to'       => 'Model_Sat_Unit',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),

		'purchase_unit' => array(
			'key_from'       => 'purchase_unit_id',
			'model_to'       => 'Model_Sat_Unit',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),

		'claveunidad_sat' => array(
			'key_from'       => 'claveunidad',
			'model_to'       => 'Model_Sat_Unit',
			'key_to'         => 'code', 
			'cascade_save'   => false,
			'cascade_delete' => false,
		),

	);

}
