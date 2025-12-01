<?php
/**
 * ERP Sellers Module - Multi-tenant Module Bootstrap
 *
 * Backend para vendedores del ERP.
 * Incluye gestión de ventas, clientes, cotizaciones,
 * comisiones y dashboard de vendedor.
 *
 * @package    ERP_Sellers
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
if ( ! defined('ERP_SELLERS_KEY'))
{
	define('ERP_SELLERS_KEY', 'erp_sellers');
}

/**
 * Check if this module is active for the current tenant
 */
function erp_sellers_is_active()
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
	return in_array(ERP_SELLERS_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (erp_sellers_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'ERP_Sellers\\Controller_Dashboard'    => __DIR__.'/classes/controller/dashboard.php',
		'ERP_Sellers\\Controller_Sales'        => __DIR__.'/classes/controller/sales.php',
		'ERP_Sellers\\Controller_Customers'    => __DIR__.'/classes/controller/customers.php',
		'ERP_Sellers\\Controller_Quotes'       => __DIR__.'/classes/controller/quotes.php',
		'ERP_Sellers\\Controller_Commissions'  => __DIR__.'/classes/controller/commissions.php',
		'ERP_Sellers\\Model_Sale'              => __DIR__.'/classes/model/sale.php',
		'ERP_Sellers\\Model_Customer'          => __DIR__.'/classes/model/customer.php',
		'ERP_Sellers\\Model_Quote'             => __DIR__.'/classes/model/quote.php',
		'ERP_Sellers\\Model_Commission'        => __DIR__.'/classes/model/commission.php',
		'ERP_Sellers\\Service_SalesManager'    => __DIR__.'/classes/service/salesmanager.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('ERP_Sellers', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'sellers'                    => 'erp_sellers/dashboard/index',
		'sellers/dashboard'          => 'erp_sellers/dashboard/index',
		'sellers/sales'              => 'erp_sellers/sales/index',
		'sellers/sales/(:any)'       => 'erp_sellers/sales/$1',
		'sellers/customers'          => 'erp_sellers/customers/index',
		'sellers/customers/(:any)'   => 'erp_sellers/customers/$1',
		'sellers/quotes'             => 'erp_sellers/quotes/index',
		'sellers/quotes/(:any)'      => 'erp_sellers/quotes/$1',
		'sellers/commissions'        => 'erp_sellers/commissions/index',
		'sellers/commissions/(:any)' => 'erp_sellers/commissions/$1',
	));

	// Log module activation
	\Log::info('ERP Sellers Module: Module loaded and activated for tenant');
}
else
{
	// Log that module is not active
	\Log::debug('ERP Sellers Module: Module not active for current tenant');
}
