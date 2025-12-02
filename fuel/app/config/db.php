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
 *  Global database settings
 * -----------------------------------------------------------------------------
 *
 *  Set database configurations here to override environment specific
 *  configurations
 *
 */

return array(
	/**
	 * -------------------------------------------------------------------------
	 *  Master database connection for multi-tenant ERP
	 * -------------------------------------------------------------------------
	 *
	 *  This connection is used to query the tenants table and resolve
	 *  the tenant-specific database based on HTTP_HOST.
	 *
	 */
	'master' => array(
		'type'        => 'pdo',
		'connection'  => array(
			'dsn'      => 'mysql:host=localhost;dbname=base',
			'username' => 'root',
			'password' => '',
		),
		'identifier'  => '`',
		'table_prefix' => '',
		'charset'     => 'utf8',
		'collation'   => false,
		'enable_cache' => true,
		'profiling'   => false,
		'readonly'    => false,
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Default connection (will be reconfigured per-tenant)
	 * -------------------------------------------------------------------------
	 *
	 *  This connection is dynamically configured based on the tenant
	 *  resolved from HTTP_HOST in config_tenant.php
	 *
	 */
	'default' => array(
		'type'        => 'pdo',
		'connection'  => array(
			'dsn'      => 'mysql:host=localhost;dbname=base',
			'username' => 'root',
			'password' => '',
		),
		'identifier'  => '`',
		'table_prefix' => '',
		'charset'     => 'utf8',
		'collation'   => false,
		'enable_cache' => true,
		'profiling'   => false,
		'readonly'    => false,
	),
);
