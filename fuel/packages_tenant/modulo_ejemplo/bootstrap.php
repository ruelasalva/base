<?php
/**
 * Modulo Ejemplo - Multi-tenant Module Bootstrap
 *
 * @package    Modulo_Ejemplo
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
 *  if 'modulo_ejemplo' key is present in TENANT_ACTIVE_MODULES.
 *
 */

// Define module key for this module
if ( ! defined('MODULO_EJEMPLO_KEY'))
{
	define('MODULO_EJEMPLO_KEY', 'modulo_ejemplo');
}

/**
 * Check if this module is active for the current tenant
 */
function modulo_ejemplo_is_active()
{
	// Check if TENANT_ACTIVE_MODULES constant is defined
	if ( ! defined('TENANT_ACTIVE_MODULES'))
	{
		return false;
	}

	// Unserialize the active modules array
	$active_modules = @unserialize(TENANT_ACTIVE_MODULES);

	if ( ! is_array($active_modules))
	{
		return false;
	}

	// Check if this module's key is in the active modules
	return in_array(MODULO_EJEMPLO_KEY, $active_modules);
}

/**
 * Initialize the module only if active for current tenant
 */
if (modulo_ejemplo_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'Modulo_Ejemplo\\Controller_Ejemplo' => __DIR__.'/classes/controller/ejemplo.php',
		'Modulo_Ejemplo\\Model_Ejemplo'      => __DIR__.'/classes/model/ejemplo.php',
		'Modulo_Ejemplo\\Service_Ejemplo'    => __DIR__.'/classes/service/ejemplo.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('Modulo_Ejemplo', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'ejemplo'          => 'modulo_ejemplo/ejemplo/index',
		'ejemplo/(:any)'   => 'modulo_ejemplo/ejemplo/$1',
	));

	// Log module activation
	\Log::info('Modulo Ejemplo: Module loaded and activated for tenant');
}
else
{
	// Log that module is not active
	\Log::debug('Modulo Ejemplo: Module not active for current tenant');
}
