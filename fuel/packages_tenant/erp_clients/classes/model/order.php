<?php
/**
 * ERP Clients Module - Order Model
 *
 * Order model for client portal.
 *
 * @package    ERP_Clients
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Clients;

/**
 * Order Model
 *
 * Represents a client's order view.
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
		'status',
		'subtotal',
		'shipping',
		'tax',
		'discount',
		'total',
		'payment_status',
		'shipping_name',
		'shipping_address',
		'tracking_number',
		'created_at',
		'updated_at',
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
	 * Get payment status label
	 *
	 * @return string
	 */
	public function get_payment_status_label()
	{
		$labels = array(
			'pending' => 'Pendiente',
			'paid' => 'Pagado',
			'failed' => 'Fallido',
			'refunded' => 'Reembolsado',
		);

		return isset($labels[$this->payment_status]) ? $labels[$this->payment_status] : $this->payment_status;
	}
}
