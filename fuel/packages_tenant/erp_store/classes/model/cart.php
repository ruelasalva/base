<?php
/**
 * ERP Store Module - Cart Model
 *
 * Shopping cart model.
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Cart Model
 *
 * Represents a shopping cart in the store.
 */
class Model_Cart extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'cart_items';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'session_id',
		'user_id',
		'product_id',
		'quantity',
		'price',
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
	 * @var array Belongs to relationship
	 */
	protected static $_belongs_to = array(
		'product' => array(
			'key_from' => 'product_id',
			'model_to' => 'ERP_Store\\Model_Product',
			'key_to' => 'id',
		),
	);

	/**
	 * Get subtotal for this item
	 *
	 * @return float
	 */
	public function get_subtotal()
	{
		return $this->price * $this->quantity;
	}

	/**
	 * Get cart items for session
	 *
	 * @param string $session_id Session ID
	 * @return array
	 */
	public static function get_for_session($session_id)
	{
		return static::query()
			->where('session_id', $session_id)
			->get();
	}

	/**
	 * Get cart total
	 *
	 * @param string $session_id Session ID
	 * @return float
	 */
	public static function get_total($session_id)
	{
		$items = static::get_for_session($session_id);
		$total = 0;

		foreach ($items as $item)
		{
			$total += $item->get_subtotal();
		}

		return $total;
	}
}
