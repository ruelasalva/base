<?php
/**
 * ERP Admin Module - User Model
 *
 * Base model for user management in the ERP system.
 *
 * @package    ERP_Admin
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Admin;

/**
 * User Model
 *
 * Represents a user in the ERP system with role-based access control.
 */
class Model_User extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'users';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'username',
		'email',
		'password',
		'first_name',
		'last_name',
		'role_id',
		'is_active',
		'last_login',
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
		'role' => array(
			'key_from' => 'role_id',
			'model_to' => 'ERP_Admin\\Model_Role',
			'key_to' => 'id',
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
	 * Check if user has a specific role
	 *
	 * @param string $role_name Role name to check
	 * @return bool
	 */
	public function has_role($role_name)
	{
		return $this->role && $this->role->name === $role_name;
	}
}
