<?php
/**
 * ERP Sellers Module - Sales Manager Service
 *
 * Sales management service.
 *
 * @package    ERP_Sellers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Sellers;

/**
 * Sales Manager Service
 *
 * Handles sales operations for sellers.
 */
class Service_SalesManager
{
	/**
	 * Get sales for seller
	 *
	 * @param int $seller_id Seller ID
	 * @param array $filters Filters
	 * @return array
	 */
	public static function get_sales($seller_id, $filters = array())
	{
		$query = Model_Sale::query()
			->where('seller_id', $seller_id);

		if ( ! empty($filters['status']))
		{
			$query->where('status', $filters['status']);
		}

		if ( ! empty($filters['date_from']))
		{
			$query->where('created_at', '>=', $filters['date_from']);
		}

		if ( ! empty($filters['date_to']))
		{
			$query->where('created_at', '<=', $filters['date_to']);
		}

		return $query->order_by('created_at', 'desc')->get();
	}

	/**
	 * Get seller statistics
	 *
	 * @param int $seller_id Seller ID
	 * @return array
	 */
	public static function get_statistics($seller_id)
	{
		$today = date('Y-m-d');
		$month_start = date('Y-m-01');
		$month_end = date('Y-m-t');

		return array(
			'sales_today' => static::count_sales($seller_id, $today, $today),
			'sales_month' => static::count_sales($seller_id, $month_start, $month_end),
			'revenue_today' => static::sum_revenue($seller_id, $today, $today),
			'revenue_month' => static::sum_revenue($seller_id, $month_start, $month_end),
			'customers_total' => static::count_customers($seller_id),
			'commission_month' => Model_Commission::get_total_for_period($seller_id, $month_start, $month_end),
		);
	}

	/**
	 * Count sales for period
	 *
	 * @param int $seller_id Seller ID
	 * @param string $start_date Start date
	 * @param string $end_date End date
	 * @return int
	 */
	protected static function count_sales($seller_id, $start_date, $end_date)
	{
		return Model_Sale::query()
			->where('seller_id', $seller_id)
			->where('created_at', '>=', $start_date . ' 00:00:00')
			->where('created_at', '<=', $end_date . ' 23:59:59')
			->count();
	}

	/**
	 * Sum revenue for period
	 *
	 * @param int $seller_id Seller ID
	 * @param string $start_date Start date
	 * @param string $end_date End date
	 * @return float
	 */
	protected static function sum_revenue($seller_id, $start_date, $end_date)
	{
		$result = Model_Sale::query()
			->where('seller_id', $seller_id)
			->where('payment_status', 'paid')
			->where('created_at', '>=', $start_date . ' 00:00:00')
			->where('created_at', '<=', $end_date . ' 23:59:59')
			->sum('total');

		return $result ?: 0;
	}

	/**
	 * Count customers for seller
	 *
	 * @param int $seller_id Seller ID
	 * @return int
	 */
	protected static function count_customers($seller_id)
	{
		return Model_Customer::query()
			->where('seller_id', $seller_id)
			->where('is_active', 1)
			->count();
	}
}
