<?php
/**
 * ERP Sellers Module - Quote Model
 *
 * Base model for quote management.
 *
 * @package    ERP_Sellers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Sellers;

/**
 * Quote Model
 *
 * Represents a sales quote in the ERP system.
 */
class Model_Quote extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'quotes';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'quote_number',
		'customer_id',
		'seller_id',
		'status', // 'draft', 'sent', 'accepted', 'rejected', 'expired'
		'subtotal',
		'discount',
		'tax',
		'total',
		'valid_until',
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
			'draft' => 'Borrador',
			'sent' => 'Enviada',
			'accepted' => 'Aceptada',
			'rejected' => 'Rechazada',
			'expired' => 'Expirada',
		);

		return isset($labels[$this->status]) ? $labels[$this->status] : $this->status;
	}

	/**
	 * Check if quote is expired
	 *
	 * @return bool
	 */
	public function is_expired()
	{
		if (empty($this->valid_until))
		{
			return false;
		}

		return strtotime($this->valid_until) < time();
	}
}
