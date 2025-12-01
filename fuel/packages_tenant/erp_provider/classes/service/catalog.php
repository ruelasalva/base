<?php
/**
 * ERP Provider Module - Catalog Service
 *
 * Product catalog management service.
 *
 * @package    ERP_Provider
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Provider;

/**
 * Catalog Service
 *
 * Handles product catalog operations for providers.
 */
class Service_Catalog
{
	/**
	 * Get products for provider
	 *
	 * @param int $provider_id Provider ID
	 * @param array $filters Filters
	 * @return array
	 */
	public static function get_products($provider_id, $filters = array())
	{
		$query = Model_Product::query()
			->where('provider_id', $provider_id);

		if ( ! empty($filters['category_id']))
		{
			$query->where('category_id', $filters['category_id']);
		}

		if ( ! empty($filters['is_active']))
		{
			$query->where('is_active', $filters['is_active']);
		}

		if ( ! empty($filters['search']))
		{
			$search = '%' . $filters['search'] . '%';
			$query->where_open()
				->where('name', 'LIKE', $search)
				->or_where('sku', 'LIKE', $search)
				->where_close();
		}

		return $query->order_by('name', 'asc')->get();
	}

	/**
	 * Get low stock products
	 *
	 * @param int $provider_id Provider ID
	 * @return array
	 */
	public static function get_low_stock_products($provider_id)
	{
		return Model_Product::query()
			->where('provider_id', $provider_id)
			->where('is_active', 1)
			->where(\DB::expr('stock'), '<=', \DB::expr('min_stock'))
			->get();
	}

	/**
	 * Update product stock
	 *
	 * @param int $product_id Product ID
	 * @param int $quantity Quantity change (positive or negative)
	 * @param string $type Movement type ('in', 'out', 'adjustment')
	 * @param string $reference Reference
	 * @return bool
	 */
	public static function update_stock($product_id, $quantity, $type = 'adjustment', $reference = '')
	{
		$product = Model_Product::find($product_id);

		if ( ! $product)
		{
			return false;
		}

		// Update product stock
		if ($type === 'in')
		{
			$product->stock += abs($quantity);
			Model_Inventory::record_entry($product_id, $quantity, $reference);
		}
		elseif ($type === 'out')
		{
			$product->stock -= abs($quantity);
			Model_Inventory::record_exit($product_id, $quantity, $reference);
		}
		else
		{
			$product->stock = $quantity;
		}

		$product->save();

		return true;
	}
}
