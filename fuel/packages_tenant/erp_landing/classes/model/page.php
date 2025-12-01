<?php
/**
 * ERP Landing Module - Page Model
 *
 * CMS page model.
 *
 * @package    ERP_Landing
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Landing;

/**
 * Page Model
 *
 * Represents a CMS page.
 */
class Model_Page extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'pages';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'title',
		'slug',
		'content',
		'meta_title',
		'meta_description',
		'is_active',
		'sort_order',
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
	 * Get page by slug
	 *
	 * @param string $slug Page slug
	 * @return Model_Page|null
	 */
	public static function get_by_slug($slug)
	{
		return static::query()
			->where('slug', $slug)
			->where('is_active', 1)
			->get_one();
	}
}
