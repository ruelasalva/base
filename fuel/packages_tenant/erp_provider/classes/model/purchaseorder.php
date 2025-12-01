<?php
/**
 * ERP Provider Module - Purchase Order Model
 *
 * Base model for purchase order management.
 *
 * @package    ERP_Provider
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Provider;

/**
 * PurchaseOrder Model
 *
 * Represents a purchase order in the ERP system.
 */
class Model_PurchaseOrder extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'purchase_orders';

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
		'provider_id',
		'status', // 'pending', 'confirmed', 'shipped', 'received', 'cancelled'
		'subtotal',
		'tax',
		'total',
		'notes',
		'expected_date',
		'received_date',
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
			'received' => 'Recibido',
			'cancelled' => 'Cancelado',
		);

		return isset($labels[$this->status]) ? $labels[$this->status] : $this->status;
	}
}
