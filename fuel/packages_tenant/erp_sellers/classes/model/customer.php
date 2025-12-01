<?php
/**
 * ERP Sellers Module - Customer Model
 *
 * Base model for customer management.
 *
 * @package    ERP_Sellers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Sellers;

/**
 * Customer Model
 *
 * Represents a customer in the ERP system.
 */
class Model_Customer extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'customers';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'code',
		'first_name',
		'last_name',
		'email',
		'phone',
		'address',
		'city',
		'state',
		'country',
		'postal_code',
		'seller_id',
		'is_active',
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
	 * Get full name
	 *
	 * @return string
	 */
	public function get_full_name()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

	/**
	 * Get full address
	 *
	 * @return string
	 */
	public function get_full_address()
	{
		$parts = array_filter(array(
			$this->address,
			$this->city,
			$this->state,
			$this->postal_code,
			$this->country,
		));

		return implode(', ', $parts);
	}
}
