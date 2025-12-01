<?php
/**
 * ERP Store Module - Cart Manager Service
 *
 * Shopping cart management service.
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Cart Manager Service
 *
 * Handles shopping cart operations.
 */
class Service_CartManager
{
	/**
	 * Get session ID
	 *
	 * @return string
	 */
	protected static function get_session_id()
	{
		$session_id = \Session::get('cart_session_id');

		if (empty($session_id))
		{
			$session_id = uniqid('cart_', true);
			\Session::set('cart_session_id', $session_id);
		}

		return $session_id;
	}

	/**
	 * Get cart items
	 *
	 * @return array
	 */
	public static function get_items()
	{
		return Model_Cart::get_for_session(static::get_session_id());
	}

	/**
	 * Add item to cart
	 *
	 * @param int $product_id Product ID
	 * @param int $quantity Quantity
	 * @return bool
	 */
	public static function add_item($product_id, $quantity = 1)
	{
		$product = Model_Product::find($product_id);

		if ( ! $product || ! $product->is_in_stock())
		{
			return false;
		}

		$session_id = static::get_session_id();

		// Check if product already in cart
		$existing = Model_Cart::query()
			->where('session_id', $session_id)
			->where('product_id', $product_id)
			->get_one();

		if ($existing)
		{
			$existing->quantity += $quantity;
			$existing->save();
		}
		else
		{
			$item = Model_Cart::forge(array(
				'session_id' => $session_id,
				'product_id' => $product_id,
				'quantity' => $quantity,
				'price' => $product->get_current_price(),
			));
			$item->save();
		}

		return true;
	}

	/**
	 * Remove item from cart
	 *
	 * @param int $item_id Cart item ID
	 * @return bool
	 */
	public static function remove_item($item_id)
	{
		$item = Model_Cart::find($item_id);

		if ($item && $item->session_id === static::get_session_id())
		{
			$item->delete();
			return true;
		}

		return false;
	}

	/**
	 * Update item quantity
	 *
	 * @param int $item_id Cart item ID
	 * @param int $quantity New quantity
	 * @return bool
	 */
	public static function update_quantity($item_id, $quantity)
	{
		$item = Model_Cart::find($item_id);

		if ($item && $item->session_id === static::get_session_id())
		{
			if ($quantity <= 0)
			{
				return static::remove_item($item_id);
			}

			$item->quantity = $quantity;
			$item->save();

			return true;
		}

		return false;
	}

	/**
	 * Get cart total
	 *
	 * @return float
	 */
	public static function get_total()
	{
		return Model_Cart::get_total(static::get_session_id());
	}

	/**
	 * Get cart item count
	 *
	 * @return int
	 */
	public static function get_item_count()
	{
		$items = static::get_items();
		$count = 0;

		foreach ($items as $item)
		{
			$count += $item->quantity;
		}

		return $count;
	}

	/**
	 * Clear cart
	 *
	 * @return void
	 */
	public static function clear()
	{
		$items = static::get_items();

		foreach ($items as $item)
		{
			$item->delete();
		}
	}
}
