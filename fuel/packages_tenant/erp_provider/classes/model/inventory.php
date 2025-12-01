<?php
/**
 * ERP Provider Module - Inventory Model
 *
 * Base model for inventory management.
 *
 * @package    ERP_Provider
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Provider;

/**
 * Inventory Model
 *
 * Represents inventory movements in the ERP system.
 */
class Model_Inventory extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'inventory_movements';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'product_id',
		'type', // 'in', 'out', 'adjustment'
		'quantity',
		'reference',
		'notes',
		'user_id',
		'created_at',
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
	);

	/**
	 * @var array Belongs to relationship
	 */
	protected static $_belongs_to = array(
		'product' => array(
			'key_from' => 'product_id',
			'model_to' => 'ERP_Provider\\Model_Product',
			'key_to' => 'id',
		),
	);

	/**
	 * Record stock entry
	 *
	 * @param int $product_id Product ID
	 * @param int $quantity Quantity
	 * @param string $reference Reference
	 * @param string $notes Notes
	 * @return Model_Inventory
	 */
	public static function record_entry($product_id, $quantity, $reference = '', $notes = '')
	{
		$movement = static::forge(array(
			'product_id' => $product_id,
			'type' => 'in',
			'quantity' => abs($quantity),
			'reference' => $reference,
			'notes' => $notes,
		));
		$movement->save();

		return $movement;
	}

	/**
	 * Record stock exit
	 *
	 * @param int $product_id Product ID
	 * @param int $quantity Quantity
	 * @param string $reference Reference
	 * @param string $notes Notes
	 * @return Model_Inventory
	 */
	public static function record_exit($product_id, $quantity, $reference = '', $notes = '')
	{
		$movement = static::forge(array(
			'product_id' => $product_id,
			'type' => 'out',
			'quantity' => -abs($quantity),
			'reference' => $reference,
			'notes' => $notes,
		));
		$movement->save();

		return $movement;
	}
}
