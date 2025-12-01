<?php
/**
 * ERP Clients Module - Order Manager Service
 *
 * Order management service for clients.
 *
 * @package    ERP_Clients
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Clients;

/**
 * Order Manager Service
 *
 * Handles order operations for clients.
 */
class Service_OrderManager
{
	/**
	 * Get orders for client
	 *
	 * @param int $user_id User ID
	 * @param array $filters Filters
	 * @return array
	 */
	public static function get_orders($user_id, $filters = array())
	{
		$query = Model_Order::query()
			->where('user_id', $user_id);

		if ( ! empty($filters['status']))
		{
			$query->where('status', $filters['status']);
		}

		return $query->order_by('created_at', 'desc')->get();
	}

	/**
	 * Get order details
	 *
	 * @param int $order_id Order ID
	 * @param int $user_id User ID
	 * @return Model_Order|null
	 */
	public static function get_order($order_id, $user_id)
	{
		return Model_Order::query()
			->where('id', $order_id)
			->where('user_id', $user_id)
			->get_one();
	}

	/**
	 * Get client statistics
	 *
	 * @param int $user_id User ID
	 * @return array
	 */
	public static function get_statistics($user_id)
	{
		return array(
			'total_orders' => static::count_orders($user_id),
			'pending_orders' => static::count_orders($user_id, 'pending'),
			'processing_orders' => static::count_orders($user_id, 'processing'),
			'shipped_orders' => static::count_orders($user_id, 'shipped'),
			'total_spent' => static::sum_spent($user_id),
		);
	}

	/**
	 * Count orders
	 *
	 * @param int $user_id User ID
	 * @param string|null $status Status filter
	 * @return int
	 */
	protected static function count_orders($user_id, $status = null)
	{
		$query = Model_Order::query()
			->where('user_id', $user_id);

		if ($status !== null)
		{
			$query->where('status', $status);
		}

		return $query->count();
	}

	/**
	 * Sum total spent
	 *
	 * @param int $user_id User ID
	 * @return float
	 */
	protected static function sum_spent($user_id)
	{
		$result = Model_Order::query()
			->where('user_id', $user_id)
			->where('payment_status', 'paid')
			->sum('total');

		return $result ?: 0;
	}
}
