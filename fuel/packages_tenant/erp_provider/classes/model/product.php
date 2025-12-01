<?php
/**
 * ERP Provider Module - Product Model
 *
 * Base model for product management.
 *
 * @package    ERP_Provider
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Provider;

/**
 * Product Model
 *
 * Represents a product in the ERP system.
 */
class Model_Product extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'products';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'sku',
		'name',
		'description',
		'price',
		'cost',
		'category_id',
		'provider_id',
		'stock',
		'min_stock',
		'is_active',
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
	 * Check if product is low on stock
	 *
	 * @return bool
	 */
	public function is_low_stock()
	{
		return $this->stock <= $this->min_stock;
	}

	/**
	 * Get profit margin
	 *
	 * @return float
	 */
	public function get_margin()
	{
		if ($this->cost <= 0)
		{
			return 0;
		}

		return (($this->price - $this->cost) / $this->cost) * 100;
	}
}
