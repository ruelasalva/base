<?php
/**
 * Sellers Module - Multi-tenant Module Bootstrap
 *
 * Backend para vendedores del ERP.
 * Incluye gestión de ventas, clientes, cotizaciones,
 * comisiones y dashboard de vendedor.
 *
 * @package    Sellers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

// Define module key for this module
if ( ! defined('SELLERS_KEY'))
{
	define('SELLERS_KEY', 'sellers');
}

/**
 * Check if this module is active for the current tenant
 */
function sellers_is_active()
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

	return in_array(SELLERS_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (sellers_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'Sellers\\Controller_Dashboard'    => __DIR__.'/classes/controller/dashboard.php',
		'Sellers\\Controller_Sales'        => __DIR__.'/classes/controller/sales.php',
		'Sellers\\Controller_Customers'    => __DIR__.'/classes/controller/customers.php',
		'Sellers\\Controller_Quotes'       => __DIR__.'/classes/controller/quotes.php',
		'Sellers\\Controller_Commissions'  => __DIR__.'/classes/controller/commissions.php',
		'Sellers\\Model_Sale'              => __DIR__.'/classes/model/sale.php',
		'Sellers\\Model_Customer'          => __DIR__.'/classes/model/customer.php',
		'Sellers\\Model_Quote'             => __DIR__.'/classes/model/quote.php',
		'Sellers\\Model_Commission'        => __DIR__.'/classes/model/commission.php',
		'Sellers\\Service_SalesManager'    => __DIR__.'/classes/service/salesmanager.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('Sellers', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'sellers'                    => 'sellers/dashboard/index',
		'sellers/dashboard'          => 'sellers/dashboard/index',
		'sellers/sales'              => 'sellers/sales/index',
		'sellers/sales/(:any)'       => 'sellers/sales/$1',
		'sellers/customers'          => 'sellers/customers/index',
		'sellers/customers/(:any)'   => 'sellers/customers/$1',
		'sellers/quotes'             => 'sellers/quotes/index',
		'sellers/quotes/(:any)'      => 'sellers/quotes/$1',
		'sellers/commissions'        => 'sellers/commissions/index',
		'sellers/commissions/(:any)' => 'sellers/commissions/$1',
	));

	\Log::info('Sellers Module: Module loaded and activated for tenant');
}
else
{
	\Log::debug('Sellers Module: Module not active for current tenant');
}
