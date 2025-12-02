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
 *  Multi-tenant Configuration
 * -----------------------------------------------------------------------------
 *
 *  This file handles tenant resolution based on HTTP_HOST.
 *  It queries the master database to get tenant-specific database name
 *  and active modules, then reconfigures the default connection.
 *
 *  Expected tenants table structure:
 *  CREATE TABLE tenants (
 *      id INT AUTO_INCREMENT PRIMARY KEY,
 *      domain VARCHAR(255) NOT NULL UNIQUE,
 *      db_name VARCHAR(255) NOT NULL,
 *      active_modules JSON,
 *      is_active TINYINT(1) DEFAULT 1,
 *      created_at DATETIME,
 *      updated_at DATETIME
 *  );
 *
 */

/**
 * Resolve tenant from HTTP_HOST and configure database connection
 */
class Tenant_Resolver
{
	/**
	 * @var array|null Cached tenant data
	 */
	protected static $tenant = null;

	/**
	 * @var array Active modules for current tenant
	 */
	protected static $active_modules = array();

	/**
	 * Initialize tenant resolution and database configuration
	 *
	 * @return void
	 */
	public static function init()
	{
		// En modo DEVELOPMENT, cargar todos los módulos sin verificar BD
		if (\Fuel::$env === \Fuel::DEVELOPMENT)
		{
			// Cargar todos los módulos disponibles en development (admin deshabilitado por conflicto)
			$all_modules = array('partners', 'sellers', 'store', 'clients', 'providers', 'landing');
			static::$active_modules = $all_modules;
			
			// Define constant con todos los módulos
			if ( ! defined('TENANT_ACTIVE_MODULES'))
			{
				define('TENANT_ACTIVE_MODULES', serialize(static::$active_modules));
			}
			
			\Log::warning('Tenant Resolver: DEVELOPMENT mode - All modules loaded automatically');
			return;
		}
		
		// Get HTTP_HOST from server variables
		$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

		// Remove port if present
		$domain = preg_replace('/:\d+$/', '', $http_host);

		// Resolve tenant from master database
		static::$tenant = static::resolve_tenant($domain);

		if (static::$tenant)
		{
			// Reconfigure default database connection with tenant db_name
			static::configure_tenant_database(static::$tenant['db_name']);

			// Parse and store active modules
			static::$active_modules = static::parse_active_modules(static::$tenant['active_modules']);
		}

		// Define constant with active modules for use throughout the application
		if ( ! defined('TENANT_ACTIVE_MODULES'))
		{
			define('TENANT_ACTIVE_MODULES', serialize(static::$active_modules));
		}
	}

	/**
	 * Validate domain format
	 *
	 * @param string $domain The domain to validate
	 * @return bool True if valid, false otherwise
	 */
	protected static function validate_domain($domain)
	{
		// Check for empty or invalid type
		if (empty($domain) || ! is_string($domain))
		{
			return false;
		}

		// Check length (reasonable limit for domain names)
		if (strlen($domain) > 253)
		{
			return false;
		}

		// Allow localhost and standard domains
		// Domain pattern: letters, numbers, hyphens, and dots
		if ( ! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-\.]*[a-zA-Z0-9]$|^localhost$/', $domain))
		{
			return false;
		}

		return true;
	}

	/**
	 * Query master database to get tenant by domain
	 *
	 * @param string $domain The domain to lookup
	 * @return array|null Tenant data or null if not found
	 */
	protected static function resolve_tenant($domain)
	{
		// Validate domain format before querying
		if ( ! static::validate_domain($domain))
		{
			\Log::warning('Tenant Resolver: Invalid domain format: ' . substr($domain, 0, 50));
			return null;
		}

		try
		{
			// Get master database configuration
			$db_config = \Config::get('db.master');

			if ( ! $db_config)
			{
				\Log::error('Tenant Resolver: Master database configuration not found');
				return null;
			}

			// Create PDO connection to master database
			$pdo = new \PDO(
				$db_config['connection']['dsn'],
				$db_config['connection']['username'],
				$db_config['connection']['password'],
				array(
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
					\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
				)
			);

			// Query tenants table for the given domain
			$stmt = $pdo->prepare('SELECT id, domain, db_name, active_modules FROM tenants WHERE domain = :domain AND is_active = 1 LIMIT 1');
			$stmt->execute(array(':domain' => $domain));

			$tenant = $stmt->fetch();

			if ($tenant)
			{
				\Log::info('Tenant Resolver: Resolved tenant for domain: ' . $domain);
				return $tenant;
			}
			else
			{
				\Log::warning('Tenant Resolver: No active tenant found for domain: ' . $domain);
				return null;
			}
		}
		catch (\PDOException $e)
		{
			\Log::error('Tenant Resolver: Database error - ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Reconfigure default database connection with tenant database
	 *
	 * @param string $db_name The tenant database name
	 * @return void
	 */
	protected static function configure_tenant_database($db_name)
	{
		// Get current master configuration as base
		$master_config = \Config::get('db.master');

		if ( ! $master_config)
		{
			return;
		}

		// Parse the current DSN to modify database name
		$dsn = $master_config['connection']['dsn'];

		// Replace or add database name in DSN
		if (preg_match('/dbname=([^;]+)/', $dsn, $matches))
		{
			$new_dsn = preg_replace('/dbname=[^;]+/', 'dbname=' . $db_name, $dsn);
		}
		else
		{
			$new_dsn = $dsn . ';dbname=' . $db_name;
		}

		// Update default database configuration
		\Config::set('db.default.connection.dsn', $new_dsn);
		\Config::set('db.default.connection.username', $master_config['connection']['username']);
		\Config::set('db.default.connection.password', $master_config['connection']['password']);

		\Log::info('Tenant Resolver: Configured default database to: ' . $db_name);
	}

	/**
	 * Parse active modules from JSON string
	 *
	 * @param string|null $modules_json JSON string of active modules
	 * @return array Array of active module keys
	 */
	protected static function parse_active_modules($modules_json)
	{
		if (empty($modules_json))
		{
			return array();
		}

		$modules = json_decode($modules_json, true);

		if (json_last_error() !== JSON_ERROR_NONE)
		{
			\Log::error('Tenant Resolver: Failed to parse active_modules JSON');
			return array();
		}

		return is_array($modules) ? $modules : array();
	}

	/**
	 * Get current tenant data
	 *
	 * @return array|null
	 */
	public static function get_tenant()
	{
		return static::$tenant;
	}

	/**
	 * Get active modules for current tenant
	 *
	 * @return array
	 */
	public static function get_active_modules()
	{
		return static::$active_modules;
	}

	/**
	 * Check if a module is active for current tenant
	 *
	 * @param string $module_key The module key to check
	 * @return bool
	 */
	public static function is_module_active($module_key)
	{
		return in_array($module_key, static::$active_modules);
	}
}

return array(
	/**
	 * -------------------------------------------------------------------------
	 *  Tenant configuration options
	 * -------------------------------------------------------------------------
	 */
	'enabled' => true,

	/**
	 * Default tenant database to use when no tenant is resolved
	 */
	'default_db' => 'erp_master',

	/**
	 * Cache tenant resolution (in seconds, 0 = no cache)
	 */
	'cache_ttl' => 300,
);
