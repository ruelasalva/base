<?php

class Model_Purchaseorderitem extends \Orm\Model
{
	protected static $_properties = [
		'id',
		'purchase_order_id',
		'product_id',
		'item_type',
		'description',
		'quantity',
		'unit_price',
		'tax_rate',
		'subtotal',
		'tax_amount',
		'total',
		'notes',
		'created_at',
		'updated_at',
	];

	protected static $_observers = [
		'Orm\Observer_CreatedAt' => [
			'events' => ['before_insert'],
			'mysql_timestamp' => false,
		],
		'Orm\Observer_UpdatedAt' => [
			'events' => ['before_save'],
			'mysql_timestamp' => false,
		],
	];

	protected static $_table_name = 'purchase_order_items';

	protected static $_belongs_to = [
		'purchase_order' => [
			'key_from' => 'purchase_order_id',
			'model_to' => 'Model_Purchaseorder',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		],
		'product' => [
			'key_from' => 'product_id',
			'model_to' => 'Model_Product',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		]
	];

	/**
	 * Calcular totales del item antes de guardar
	 */
	public function _event_before_save()
	{
		$this->calculate_totals();
	}

	/**
	 * Calcular subtotal, impuestos y total
	 */
	public function calculate_totals()
	{
		// Subtotal = cantidad * precio unitario
		$this->subtotal = $this->quantity * $this->unit_price;
		
		// Impuesto = subtotal * tasa de impuesto / 100
		$this->tax_amount = $this->subtotal * ($this->tax_rate / 100);
		
		// Total = subtotal + impuesto
		$this->total = $this->subtotal + $this->tax_amount;

		return $this;
	}

	/**
	 * Crear item desde producto
	 */
	public static function create_from_product($product, $quantity = 1)
	{
		$item = new static();
		$item->product_id = $product->id;
		$item->item_type = 'product';
		$item->description = $product->name;
		$item->quantity = $quantity;
		$item->unit_price = $product->cost_price ?? $product->price ?? 0;
		$item->tax_rate = 16.00; // IVA por defecto
		
		return $item;
	}

	/**
	 * Crear item personalizado (servicio o uso)
	 */
	public static function create_custom($description, $quantity, $unit_price, $type = 'service')
	{
		$item = new static();
		$item->product_id = null;
		$item->item_type = $type;
		$item->description = $description;
		$item->quantity = $quantity;
		$item->unit_price = $unit_price;
		$item->tax_rate = 16.00;
		
		return $item;
	}
}
