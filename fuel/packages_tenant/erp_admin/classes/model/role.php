<?php
/**
 * ERP Admin Module - Role Model
 *
 * Base model for role management in the ERP system.
 *
 * @package    ERP_Admin
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Admin;

/**
 * Role Model
 *
 * Represents a role in the ERP system for access control.
 */
class Model_Role extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'roles';

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
		'description',
		'permissions',
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
	 * @var array Has many relationship
	 */
	protected static $_has_many = array(
		'users' => array(
			'key_from' => 'id',
			'model_to' => 'ERP_Admin\\Model_User',
			'key_to' => 'role_id',
		),
	);

	/**
	 * Get permissions as array
	 *
	 * @return array
	 */
	public function get_permissions_array()
	{
		if (empty($this->permissions))
		{
			return array();
		}

		$decoded = json_decode($this->permissions, true);

		return is_array($decoded) ? $decoded : array();
	}

	/**
	 * Check if role has a specific permission
	 *
	 * @param string $permission Permission to check
	 * @return bool
	 */
	public function has_permission($permission)
	{
		$permissions = $this->get_permissions_array();

		return in_array($permission, $permissions, true);
	}
}
