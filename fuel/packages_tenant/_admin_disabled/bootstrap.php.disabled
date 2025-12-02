<?php
/**
 * Admin Module - Multi-tenant Module Bootstrap
 *
 * Backend de administración del ERP.
 * Incluye gestión de usuarios, roles, configuración del sistema,
 * reportes y dashboard administrativo.
 *
 * @package    Admin
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

// Define module key for this module
if ( ! defined('ADMIN_KEY'))
{
	define('ADMIN_KEY', 'admin');
}

// Prevent loading the bootstrap multiple times
if (defined('ADMIN_MODULE_LOADED'))
{
	return;
}

define('ADMIN_MODULE_LOADED', true);

/**
 * Check if this module is active for the current tenant
 */
if ( ! function_exists('admin_is_active'))
{
	function admin_is_active()
	{
		if ( ! defined('TENANT_ACTIVE_MODULES'))
		{
			return false;
		}

		$serialized = TENANT_ACTIVE_MODULES;

		if (empty($serialized) || ! is_string($serialized))
		{
			return false;
		}

		$active_modules = unserialize($serialized, array('allowed_classes' => false));

		if ($active_modules === false || ! is_array($active_modules))
		{
			return false;
		}

		return in_array(ADMIN_KEY, $active_modules, true);
	}
}

/**
 * Initialize the module only if active for current tenant
 */
if (admin_is_active() || \Fuel::$env === \Fuel::DEVELOPMENT)
{
	// Add module path to package paths
	\Package::load('admin', TENANT_PKGPATH.'admin'.DIRECTORY_SEPARATOR);
	
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'Admin\\Controller_Dashboard'  => __DIR__.'/classes/controller/dashboard.php',
		'Admin\\Controller_Users'      => __DIR__.'/classes/controller/users.php',
		'Admin\\Controller_Settings'   => __DIR__.'/classes/controller/settings.php',
		'Admin\\Controller_Reports'    => __DIR__.'/classes/controller/reports.php',
		'Admin\\Model_User'            => __DIR__.'/classes/model/user.php',
		'Admin\\Model_Role'            => __DIR__.'/classes/model/role.php',
		'Admin\\Model_Setting'         => __DIR__.'/classes/model/setting.php',
		'Admin\\Service_Auth'          => __DIR__.'/classes/service/auth.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('Admin', __DIR__.'/classes/', true);
	
	// Add views path
	\Finder::instance()->add_path(__DIR__.'/views/', -1);

	// Register module routes - Usar formato correcto sin "controller_"
	\Router::add(array(
		'admin'                 => 'admin/dashboard/index',
		'admin/dashboard'       => 'admin/dashboard/index',
		'admin/users'           => 'admin/users/index',
		'admin/users/(:any)'    => 'admin/users/$1',
		'admin/settings'        => 'admin/settings/index',
		'admin/settings/(:any)' => 'admin/settings/$1',
		'admin/reports'         => 'admin/reports/index',
		'admin/reports/(:any)'  => 'admin/reports/$1',
	), null, true); // true = prepend (higher priority)

	\Log::info('Admin Module: Module loaded successfully');
}
else
{
	\Log::debug('Admin Module: Module not active for current tenant');
}
