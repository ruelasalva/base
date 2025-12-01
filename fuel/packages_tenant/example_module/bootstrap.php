<?php
/**
 * Example Module - Multi-tenant Module Bootstrap
 *
 * @package    Example_Module
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
 *  The module will only load its routes and register its classes
 *  if 'example_module' key is present in TENANT_ACTIVE_MODULES.
 *
 */

// Define module key for this module
if ( ! defined('EXAMPLE_MODULE_KEY'))
{
	define('EXAMPLE_MODULE_KEY', 'example_module');
}

/**
 * Check if this module is active for the current tenant
 */
function example_module_is_active()
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
	return in_array(EXAMPLE_MODULE_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (example_module_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'Example_Module\\Controller_Example' => __DIR__.'/classes/controller/example.php',
		'Example_Module\\Model_Example'      => __DIR__.'/classes/model/example.php',
		'Example_Module\\Service_Example'    => __DIR__.'/classes/service/example.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('Example_Module', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'example'          => 'example_module/example/index',
		'example/(:any)'   => 'example_module/example/$1',
	));

	// Log module activation
	\Log::info('Example Module: Module loaded and activated for tenant');
}
else
{
	// Log that module is not active
	\Log::debug('Example Module: Module not active for current tenant');
}
