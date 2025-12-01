<?php
/**
 * ERP Sellers Module - Commission Model
 *
 * Base model for commission tracking.
 *
 * @package    ERP_Sellers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Sellers;

/**
 * Commission Model
 *
 * Represents a seller commission in the ERP system.
 */
class Model_Commission extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'commissions';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'seller_id',
		'sale_id',
		'amount',
		'rate',
		'status', // 'pending', 'approved', 'paid'
		'paid_at',
		'created_at',
		'updated_at',
	);

	/**
	 * @var array Observer configuration
	 */
	protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => true,
		),
		'Orm\\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => true,
		),
	);

	/**
	 * Get status label
	 *
	 * @return string
	 */
	public function get_status_label()
	{
		$labels = array(
			'pending' => 'Pendiente',
			'approved' => 'Aprobada',
			'paid' => 'Pagada',
		);

		return isset($labels[$this->status]) ? $labels[$this->status] : $this->status;
	}

	/**
	 * Get total commissions for seller in period
	 *
	 * @param int $seller_id Seller ID
	 * @param string $start_date Start date
	 * @param string $end_date End date
	 * @return float
	 */
	public static function get_total_for_period($seller_id, $start_date, $end_date)
	{
		$result = static::query()
			->where('seller_id', $seller_id)
			->where('created_at', '>=', $start_date)
			->where('created_at', '<=', $end_date)
			->sum('amount');

		return $result ?: 0;
	}
}
