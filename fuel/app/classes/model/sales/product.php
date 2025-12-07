<?php

class Model_Sales_Product extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"sale_id" => array(
			"label" => "Sale id",
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
		"total" => array(
			"label" => "Total",
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
	public static function get_one($request)
	{
		$response = Model_Sales_Product::query();

		if(Arr::get($request, 'id_sale'))
		{
			$response = $response->where('sale_id', $request['id_sale']);
		}

		if(Arr::get($request, 'id_product'))
		{
			$response = $response->where('product_id', $request['id_product']);
		}

		$response = $response->get_one();

		return $response;
	}

	public static function get_all_products($sale_id)
	{
		$response = Model_Sales_Product::query()
		->where('sale_id', $sale_id)
		->get();

		return $response;
	}

	public static function set_new_product($request)
	{
		$response = new Model_Sales_Product($request);

		return $response->save();
	}

	public static function do_update($request, $id_sale_product)
	{
		$response = Model_Sales_Product::find($id_sale_product);
		$response->set($request);

		return ($response->save()) ? $response : false;
	}

	public static function do_delete($id_sale_product)
	{
		$response = Model_Sales_Product::find($id_sale_product);

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

	protected static $_table_name = 'sales_products';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'sale' => array(
			'key_from'       => 'sale_id',
			'model_to'       => 'Model_Sale',
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

	/**
	 * MÉTODOS HELPER MODERNOS
	 */

	/**
	 * Calcula el subtotal del item (precio unitario * cantidad)
	 * 
	 * @return float
	 */
	public function get_subtotal()
	{
		return $this->price * $this->quantity;
	}

	/**
	 * Calcula el descuento si aplica
	 * 
	 * @param float $discount_percentage Porcentaje de descuento
	 * @return float
	 */
	public function get_discount($discount_percentage = 0)
	{
		if ($discount_percentage > 0) {
			return $this->get_subtotal() * ($discount_percentage / 100);
		}
		return 0;
	}

	/**
	 * Obtiene información completa del producto
	 * 
	 * @return string HTML con información del producto
	 */
	public function get_product_info()
	{
		if ($this->product) {
			return '<div class="product-info">' .
				   '<strong>' . htmlspecialchars($this->product->name, ENT_QUOTES, 'UTF-8') . '</strong><br>' .
				   '<small>SKU: ' . htmlspecialchars($this->product->sku, ENT_QUOTES, 'UTF-8') . '</small>' .
				   '</div>';
		}
		return '<span class="text-muted">Producto no disponible</span>';
	}

	/**
	 * Verifica si hay stock suficiente
	 * 
	 * @return bool
	 */
	public function has_stock()
	{
		if ($this->product) {
			return $this->product->available >= $this->quantity;
		}
		return false;
	}

	/**
	 * Obtiene el stock disponible del producto
	 * 
	 * @return int
	 */
	public function get_available_stock()
	{
		if ($this->product) {
			return $this->product->available;
		}
		return 0;
	}

	/**
	 * Formatea el precio para display
	 * 
	 * @param bool $with_currency Incluir símbolo de moneda
	 * @return string
	 */
	public function get_formatted_price($with_currency = true)
	{
		$formatted = number_format($this->price, 2, '.', ',');
		return $with_currency ? '$' . $formatted : $formatted;
	}

	/**
	 * Formatea el total para display
	 * 
	 * @param bool $with_currency Incluir símbolo de moneda
	 * @return string
	 */
	public function get_formatted_total($with_currency = true)
	{
		$formatted = number_format($this->total, 2, '.', ',');
		return $with_currency ? '$' . $formatted : $formatted;
	}

	/**
	 * Valida el item antes de guardar
	 * 
	 * @return array Array con errores (vacío si es válido)
	 */
	public function validate_item()
	{
		$errors = array();

		if (!$this->product_id || $this->product_id <= 0) {
			$errors[] = 'Producto inválido';
		}

		if (!$this->quantity || $this->quantity <= 0) {
			$errors[] = 'Cantidad debe ser mayor a 0';
		}

		if (!$this->price || $this->price < 0) {
			$errors[] = 'Precio inválido';
		}

		if (!$this->has_stock()) {
			$errors[] = 'Stock insuficiente. Disponible: ' . $this->get_available_stock();
		}

		return $errors;
	}

	/**
	 * Recalcula el total basado en cantidad y precio
	 * 
	 * @return float
	 */
	public function recalculate_total()
	{
		$this->total = $this->price * $this->quantity;
		return $this->total;
	}

}
