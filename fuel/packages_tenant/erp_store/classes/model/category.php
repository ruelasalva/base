<?php
/**
 * ERP Store Module - Category Model
 *
 * Category model for product categorization.
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Category Model
 *
 * Represents a product category in the store.
 */
class Model_Category extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'categories';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'name',
		'slug',
		'description',
		'parent_id',
		'image',
		'sort_order',
		'is_active',
		'created_at',
		'updated_at',
	);

	/**
	 * @var array Belongs to relationship
	 */
	protected static $_belongs_to = array(
		'parent' => array(
			'key_from' => 'parent_id',
			'model_to' => 'ERP_Store\\Model_Category',
			'key_to' => 'id',
		),
	);

	/**
	 * @var array Has many relationship
	 */
	protected static $_has_many = array(
		'children' => array(
			'key_from' => 'id',
			'model_to' => 'ERP_Store\\Model_Category',
			'key_to' => 'parent_id',
		),
		'products' => array(
			'key_from' => 'id',
			'model_to' => 'ERP_Store\\Model_Product',
			'key_to' => 'category_id',
		),
	);

	/**
	 * Get main categories (no parent)
	 *
	 * @return array
	 */
	public static function get_main()
	{
		return static::query()
			->where('parent_id', null)
			->where('is_active', 1)
			->order_by('sort_order', 'asc')
			->get();
	}
}
