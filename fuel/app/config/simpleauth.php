<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.8.2
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2019 Fuel Development Team
 * @link       https://fuelphp.com
 */

/**
 * -----------------------------------------------------------------------------
 *  SimpleAuth Configuration
 * -----------------------------------------------------------------------------
 *
 *  Configuration for SimpleAuth driver.
 *
 */

return array(

	/**
	 * -------------------------------------------------------------------------
	 *  Database connection to use
	 * -------------------------------------------------------------------------
	 *
	 *  Leave null to use default database connection (tenant database).
	 *
	 */
	'db_connection' => null,

	/**
	 * -------------------------------------------------------------------------
	 *  Table name for users
	 * -------------------------------------------------------------------------
	 */
	'table_name' => 'users',

	/**
	 * -------------------------------------------------------------------------
	 *  Table columns
	 * -------------------------------------------------------------------------
	 *
	 *  Column names in the users table.
	 *
	 */
	'table_columns' => array(
		'id'             => 'id',
		'username'       => 'username',
		'password'       => 'password',
		'group'          => 'group_id',
		'email'          => 'email',
		'last_login'     => 'last_login',
		'previous_login' => 'previous_login',
		'login_hash'     => 'login_hash',
		'user_id'        => 'user_id',
		'created_at'     => 'created_at',
		'updated_at'     => 'updated_at',
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Groups definition
	 * -------------------------------------------------------------------------
	 *
	 *  Define user groups and their roles.
	 *  The key is the group_id stored in the users table.
	 *
	 */
	'groups' => array(
		/**
		 * Super Administrator - Full access to everything
		 */
		100 => array(
			'name'   => 'Super Admin',
			'roles'  => array('super_admin'),
		),

		/**
		 * Administrator - Full access to admin module
		 */
		50 => array(
			'name'   => 'Administrador',
			'roles'  => array('admin'),
		),

		/**
		 * Manager - Access to management functions
		 */
		40 => array(
			'name'   => 'Gerente',
			'roles'  => array('manager'),
		),

		/**
		 * Seller - Access to sales module
		 */
		30 => array(
			'name'   => 'Vendedor',
			'roles'  => array('seller'),
		),

		/**
		 * Provider - Access to provider portal
		 */
		25 => array(
			'name'   => 'Proveedor',
			'roles'  => array('provider'),
		),

		/**
		 * Partner - Access to partner portal
		 */
		20 => array(
			'name'   => 'Socio',
			'roles'  => array('partner'),
		),

		/**
		 * Client - Access to client portal
		 */
		10 => array(
			'name'   => 'Cliente',
			'roles'  => array('client'),
		),

		/**
		 * Guest - Minimal access (unauthenticated)
		 */
		0 => array(
			'name'   => 'Invitado',
			'roles'  => array('guest'),
		),
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Roles definition
	 * -------------------------------------------------------------------------
	 *
	 *  Define roles and their permissions.
	 *
	 */
	'roles' => array(
		/**
		 * Super Admin - All permissions
		 */
		'super_admin' => array(
			'#' => true, // Wildcard: all permissions
		),

		/**
		 * Admin - Administrative permissions
		 */
		'admin' => array(
			'admin'     => array('access', 'users', 'settings', 'reports'),
			'sellers'   => array('access', 'view', 'create', 'edit', 'delete'),
			'clients'   => array('access', 'view', 'create', 'edit', 'delete'),
			'providers' => array('access', 'view', 'create', 'edit', 'delete'),
			'partners'  => array('access', 'view', 'create', 'edit', 'delete'),
			'store'     => array('access', 'view', 'manage'),
		),

		/**
		 * Manager - Management permissions
		 */
		'manager' => array(
			'admin'     => array('access', 'reports'),
			'sellers'   => array('access', 'view', 'create', 'edit'),
			'clients'   => array('access', 'view', 'create', 'edit'),
			'providers' => array('access', 'view'),
			'partners'  => array('access', 'view'),
		),

		/**
		 * Seller - Sales permissions
		 */
		'seller' => array(
			'sellers'  => array('access', 'sales', 'customers', 'quotes', 'commissions'),
			'clients'  => array('access', 'view'),
			'store'    => array('access', 'view'),
		),

		/**
		 * Provider - Provider portal permissions
		 */
		'provider' => array(
			'providers' => array('access', 'products', 'inventory', 'orders'),
		),

		/**
		 * Partner - Partner portal permissions
		 */
		'partner' => array(
			'partners' => array('access', 'alliances', 'contracts', 'commissions'),
		),

		/**
		 * Client - Client portal permissions
		 */
		'client' => array(
			'clients' => array('access', 'orders', 'profile', 'support'),
			'store'   => array('access', 'view', 'checkout'),
		),

		/**
		 * Guest - Minimal permissions
		 */
		'guest' => array(
			'landing' => array('access'),
			'store'   => array('access', 'view'),
		),
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Login hash salt
	 * -------------------------------------------------------------------------
	 *
	 *  Salt used for generating login hashes.
	 *  IMPORTANT: Change this in production!
	 *
	 */
	'login_hash_salt' => 'ERP_LOGIN_HASH_SALT_CHANGE_ME',

	/**
	 * -------------------------------------------------------------------------
	 *  Remember me settings
	 * -------------------------------------------------------------------------
	 */
	'remember_me' => array(
		'enabled'       => true,
		'cookie_name'   => 'erp_remember_me',
		'expiration'    => 86400 * 31, // 31 days
	),

);
