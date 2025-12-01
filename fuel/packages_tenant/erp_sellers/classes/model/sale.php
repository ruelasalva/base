<?php
/**
 * ERP Sellers Module - Sale Model
 *
 * Base model for sales management.
 *
 * @package    ERP_Sellers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Sellers;

/**
 * Sale Model
 *
 * Represents a sale in the ERP system.
 */
class Model_Sale extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'sales';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'order_number',
		'customer_id',
		'seller_id',
		'status', // 'pending', 'confirmed', 'shipped', 'delivered', 'cancelled'
		'subtotal',
		'discount',
		'tax',
		'total',
		'payment_method',
		'payment_status', // 'pending', 'paid', 'refunded'
		'notes',
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
			'confirmed' => 'Confirmado',
			'shipped' => 'Enviado',
			'delivered' => 'Entregado',
			'cancelled' => 'Cancelado',
		);

		return isset($labels[$this->status]) ? $labels[$this->status] : $this->status;
	}

	/**
	 * Check if sale is paid
	 *
	 * @return bool
	 */
	public function is_paid()
	{
		return $this->payment_status === 'paid';
	}
}
