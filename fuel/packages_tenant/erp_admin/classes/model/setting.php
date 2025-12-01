<?php
/**
 * ERP Admin Module - Setting Model
 *
 * Base model for system settings in the ERP.
 *
 * @package    ERP_Admin
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Admin;

/**
 * Setting Model
 *
 * Represents a system setting in the ERP.
 */
class Model_Setting extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'settings';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'key',
		'value',
		'group',
		'type',
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
	 * Get setting value by key
	 *
	 * @param string $key Setting key
	 * @param mixed $default Default value if not found
	 * @return mixed
	 */
	public static function get_value($key, $default = null)
	{
		$setting = static::query()->where('key', $key)->get_one();

		return $setting ? $setting->value : $default;
	}

	/**
	 * Set setting value by key
	 *
	 * @param string $key Setting key
	 * @param mixed $value Setting value
	 * @param string $group Setting group
	 * @return Model_Setting
	 */
	public static function set_value($key, $value, $group = 'general')
	{
		$setting = static::query()->where('key', $key)->get_one();

		if ( ! $setting)
		{
			$setting = static::forge(array(
				'key' => $key,
				'group' => $group,
			));
		}

		$setting->value = $value;
		$setting->save();

		return $setting;
	}
}
