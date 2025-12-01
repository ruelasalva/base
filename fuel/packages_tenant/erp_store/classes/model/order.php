<?php
/**
 * ERP Store Module - Order Model
 *
 * Customer order model.
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Order Model
 *
 * Represents a customer order in the store.
 */
class Model_Order extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'orders';

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
		'user_id',
		'status', // 'pending', 'processing', 'shipped', 'delivered', 'cancelled'
		'subtotal',
		'shipping',
		'tax',
		'discount',
		'total',
		'payment_method',
		'payment_status', // 'pending', 'paid', 'failed', 'refunded'
		'shipping_name',
		'shipping_address',
		'shipping_city',
		'shipping_state',
		'shipping_postal_code',
		'shipping_country',
		'shipping_phone',
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
			'processing' => 'En Proceso',
			'shipped' => 'Enviado',
			'delivered' => 'Entregado',
			'cancelled' => 'Cancelado',
		);

		return isset($labels[$this->status]) ? $labels[$this->status] : $this->status;
	}

	/**
	 * Generate order number
	 *
	 * @return string
	 */
	public static function generate_order_number()
	{
		return 'ORD-' . strtoupper(uniqid());
	}

	/**
	 * Get orders for user
	 *
	 * @param int $user_id User ID
	 * @return array
	 */
	public static function get_for_user($user_id)
	{
		return static::query()
			->where('user_id', $user_id)
			->order_by('created_at', 'desc')
			->get();
	}
}
