<?php
/**
 * ERP Landing Module - Multi-tenant Module Bootstrap
 *
 * Landing page y páginas públicas del ERP.
 * Incluye página principal, información de la empresa,
 * contacto, y otras páginas informativas.
 *
 * @package    ERP_Landing
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
if ( ! defined('ERP_LANDING_KEY'))
{
	define('ERP_LANDING_KEY', 'erp_landing');
}

/**
 * Check if this module is active for the current tenant
 */
function erp_landing_is_active()
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
	return in_array(ERP_LANDING_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (erp_landing_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'ERP_Landing\\Controller_Home'     => __DIR__.'/classes/controller/home.php',
		'ERP_Landing\\Controller_About'    => __DIR__.'/classes/controller/about.php',
		'ERP_Landing\\Controller_Contact'  => __DIR__.'/classes/controller/contact.php',
		'ERP_Landing\\Controller_Pages'    => __DIR__.'/classes/controller/pages.php',
		'ERP_Landing\\Model_Page'          => __DIR__.'/classes/model/page.php',
		'ERP_Landing\\Model_Contact'       => __DIR__.'/classes/model/contact.php',
		'ERP_Landing\\Service_Content'     => __DIR__.'/classes/service/content.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('ERP_Landing', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'landing'             => 'erp_landing/home/index',
		'nosotros'            => 'erp_landing/about/index',
		'contacto'            => 'erp_landing/contact/index',
		'contacto/(:any)'     => 'erp_landing/contact/$1',
		'pagina/(:any)'       => 'erp_landing/pages/view/$1',
	));

	// Log module activation
	\Log::info('ERP Landing Module: Module loaded and activated for tenant');
}
else
{
	// Log that module is not active
	\Log::debug('ERP Landing Module: Module not active for current tenant');
}
