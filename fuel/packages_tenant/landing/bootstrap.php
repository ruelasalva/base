<?php
/**
 * Landing Module - Multi-tenant Module Bootstrap
 *
 * Landing page y páginas públicas del ERP.
 * Incluye página principal, información de la empresa,
 * contacto, y otras páginas informativas.
 *
 * @package    Landing
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

// Define module key for this module
if ( ! defined('LANDING_KEY'))
{
	define('LANDING_KEY', 'landing');
}

/**
 * Check if this module is active for the current tenant
 */
function landing_is_active()
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

	return in_array(LANDING_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (landing_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'Landing\\Controller_Home'     => __DIR__.'/classes/controller/home.php',
		'Landing\\Controller_About'    => __DIR__.'/classes/controller/about.php',
		'Landing\\Controller_Contact'  => __DIR__.'/classes/controller/contact.php',
		'Landing\\Controller_Pages'    => __DIR__.'/classes/controller/pages.php',
		'Landing\\Model_Page'          => __DIR__.'/classes/model/page.php',
		'Landing\\Model_Contact'       => __DIR__.'/classes/model/contact.php',
		'Landing\\Service_Content'     => __DIR__.'/classes/service/content.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('Landing', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'landing'             => 'landing/home/index',
		'nosotros'            => 'landing/about/index',
		'contacto'            => 'landing/contact/index',
		'contacto/(:any)'     => 'landing/contact/$1',
		'pagina/(:any)'       => 'landing/pages/view/$1',
	));

	\Log::info('Landing Module: Module loaded and activated for tenant');
}
else
{
	\Log::debug('Landing Module: Module not active for current tenant');
}
