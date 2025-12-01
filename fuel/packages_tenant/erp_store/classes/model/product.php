<?php
/**
 * ERP Store Module - Product Model
 *
 * Product model for the frontend store.
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Product Model
 *
 * Represents a product for the store frontend.
 */
class Model_Product extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'products';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'sku',
		'name',
		'slug',
		'description',
		'short_description',
		'price',
		'sale_price',
		'category_id',
		'stock',
		'is_active',
		'is_featured',
		'image',
		'images',
		'created_at',
		'updated_at',
	);

	/**
	 * Get featured products
	 *
	 * @param int $limit Number of products to return
	 * @return array
	 */
	public static function get_featured($limit = 8)
	{
		return static::query()
			->where('is_active', 1)
			->where('is_featured', 1)
			->where('stock', '>', 0)
			->order_by('created_at', 'desc')
			->limit($limit)
			->get();
	}

	/**
	 * Check if product is on sale
	 *
	 * @return bool
	 */
	public function is_on_sale()
	{
		return $this->sale_price > 0 && $this->sale_price < $this->price;
	}

	/**
	 * Get current price
	 *
	 * @return float
	 */
	public function get_current_price()
	{
		return $this->is_on_sale() ? $this->sale_price : $this->price;
	}

	/**
	 * Check if product is in stock
	 *
	 * @return bool
	 */
	public function is_in_stock()
	{
		return $this->stock > 0;
	}
}
