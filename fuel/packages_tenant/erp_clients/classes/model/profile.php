<?php
/**
 * ERP Clients Module - Profile Model
 *
 * Client profile model.
 *
 * @package    ERP_Clients
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Clients;

/**
 * Profile Model
 *
 * Represents a client profile.
 */
class Model_Profile extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'user_profiles';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'user_id',
		'first_name',
		'last_name',
		'phone',
		'address',
		'city',
		'state',
		'postal_code',
		'country',
		'avatar',
		'preferences',
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
	 * Get profile for user
	 *
	 * @param int $user_id User ID
	 * @return Model_Profile|null
	 */
	public static function get_for_user($user_id)
	{
		return static::query()
			->where('user_id', $user_id)
			->get_one();
	}
}
