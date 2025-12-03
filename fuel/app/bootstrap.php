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

// Bootstrap the framework - THIS LINE NEEDS TO BE FIRST!
require COREPATH.'bootstrap.php';

// Configurar encoding UTF-8 para todos los módulos
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_regex_encoding('UTF-8');

// Add framework overload classes here
\Autoloader::add_classes(array(
	// Example: 'View' => APPPATH.'classes/myview.php',
));

// Register the autoloader
\Autoloader::register();

/**
 * Your environment.  Can be set to any of the following:
 *
 * Fuel::DEVELOPMENT
 * Fuel::TEST
 * Fuel::STAGING
 * Fuel::PRODUCTION
 */
Fuel::$env = Arr::get($_SERVER, 'FUEL_ENV', Arr::get($_ENV, 'FUEL_ENV', getenv('FUEL_ENV') ?: Fuel::DEVELOPMENT));

// Initialize the framework with the config file.
\Fuel::init('config.php');

/**
 * -----------------------------------------------------------------------------
 *  Multi-tenant ERP Configuration
 * -----------------------------------------------------------------------------
 *
 *  Initialize tenant resolution and configure tenant-specific database.
 *  This must run after Fuel::init() to ensure Config class is available.
 *
 */

// Load tenant configuration
\Config::load('config_tenant', 'tenant');

// Initialize tenant resolver if multi-tenancy is enabled
if (\Config::get('tenant.enabled', true))
{
	// Include and initialize the Tenant_Resolver class
	require_once APPPATH.'config/config_tenant.php';
	\Tenant_Resolver::init();
}

/**
 * -----------------------------------------------------------------------------
 *  Load Tenant-specific Packages
 * -----------------------------------------------------------------------------
 *
 *  Load modules from packages_tenant directory if they are active
 *  for the current tenant.
 *
 */

// Define tenant packages path constant
if ( ! defined('TENANT_PKGPATH'))
{
	$base_path = realpath(APPPATH.'..');

	// Only define path if base path exists
	if ($base_path !== false)
	{
		define('TENANT_PKGPATH', $base_path.DIRECTORY_SEPARATOR.'packages_tenant'.DIRECTORY_SEPARATOR);
	}
	else
	{
		define('TENANT_PKGPATH', false);
	}
}

// Add tenant packages path to Config BEFORE loading bootstraps
if (TENANT_PKGPATH !== false && is_dir(TENANT_PKGPATH))
{
	// Get current package paths
	$package_paths = \Config::get('package_paths', array());
	
	// Add tenant packages path if not already added
	if ( ! in_array(TENANT_PKGPATH, $package_paths))
	{
		$package_paths[] = TENANT_PKGPATH;
		\Config::set('package_paths', $package_paths);
	}
}

// Load tenant packages if directory exists
if (TENANT_PKGPATH !== false && is_dir(TENANT_PKGPATH))
{
	// Get list of tenant packages
	$tenant_packages = glob(TENANT_PKGPATH.'*', GLOB_ONLYDIR);

	if ($tenant_packages !== false)
	{
		foreach ($tenant_packages as $package_path)
		{
			$bootstrap_file = $package_path.DIRECTORY_SEPARATOR.'bootstrap.php';

			// Load package bootstrap if it exists
			if (file_exists($bootstrap_file))
			{
				require_once $bootstrap_file;
				
				// Log package loading
				\Log::info('Tenant Package Loaded: ' . basename($package_path));
			}
		}
	}
}
