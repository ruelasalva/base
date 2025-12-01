<?php
/**
 * ERP Admin Module - Multi-tenant Module Bootstrap
 *
 * Backend de administración del ERP.
 * Incluye gestión de usuarios, roles, configuración del sistema,
 * reportes y dashboard administrativo.
 *
 * @package    ERP_Admin
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

/**
 * -----------------------------------------------------------------------------
 *  Module Bootstrap
 * -----------------------------------------------------------------------------
 *
 *  This file handles the conditional loading of routes and classes
 *  based on whether the module is active for the current tenant.
 *
 */

// Define module key for this module
if ( ! defined('ERP_ADMIN_KEY'))
{
	define('ERP_ADMIN_KEY', 'erp_admin');
}

/**
 * Check if this module is active for the current tenant
 */
function erp_admin_is_active()
{
	// Check if TENANT_ACTIVE_MODULES constant is defined
	if ( ! defined('TENANT_ACTIVE_MODULES'))
	{
		return false;
	}

	// Unserialize the active modules array with proper error handling
	$serialized = TENANT_ACTIVE_MODULES;

	// Validate serialized format before unserializing
	if (empty($serialized) || ! is_string($serialized))
	{
		return false;
	}

	$active_modules = unserialize($serialized, array('allowed_classes' => false));

	if ($active_modules === false || ! is_array($active_modules))
	{
		return false;
	}

	// Check if this module's key is in the active modules
	return in_array(ERP_ADMIN_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (erp_admin_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'ERP_Admin\\Controller_Dashboard'  => __DIR__.'/classes/controller/dashboard.php',
		'ERP_Admin\\Controller_Users'      => __DIR__.'/classes/controller/users.php',
		'ERP_Admin\\Controller_Settings'   => __DIR__.'/classes/controller/settings.php',
		'ERP_Admin\\Controller_Reports'    => __DIR__.'/classes/controller/reports.php',
		'ERP_Admin\\Model_User'            => __DIR__.'/classes/model/user.php',
		'ERP_Admin\\Model_Role'            => __DIR__.'/classes/model/role.php',
		'ERP_Admin\\Model_Setting'         => __DIR__.'/classes/model/setting.php',
		'ERP_Admin\\Service_Auth'          => __DIR__.'/classes/service/auth.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('ERP_Admin', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'admin'                => 'erp_admin/dashboard/index',
		'admin/dashboard'      => 'erp_admin/dashboard/index',
		'admin/users'          => 'erp_admin/users/index',
		'admin/users/(:any)'   => 'erp_admin/users/$1',
		'admin/settings'       => 'erp_admin/settings/index',
		'admin/settings/(:any)' => 'erp_admin/settings/$1',
		'admin/reports'        => 'erp_admin/reports/index',
		'admin/reports/(:any)' => 'erp_admin/reports/$1',
	));

	// Log module activation
	\Log::info('ERP Admin Module: Module loaded and activated for tenant');
}
else
{
	// Log that module is not active
	\Log::debug('ERP Admin Module: Module not active for current tenant');
}
