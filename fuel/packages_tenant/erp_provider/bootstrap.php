<?php
/**
 * ERP Provider Module - Multi-tenant Module Bootstrap
 *
 * Backend para proveedores del ERP.
 * Incluye gestión de productos, inventario, órdenes de compra,
 * y dashboard de proveedor.
 *
 * @package    ERP_Provider
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
if ( ! defined('ERP_PROVIDER_KEY'))
{
	define('ERP_PROVIDER_KEY', 'erp_provider');
}

/**
 * Check if this module is active for the current tenant
 */
function erp_provider_is_active()
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
	return in_array(ERP_PROVIDER_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (erp_provider_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'ERP_Provider\\Controller_Dashboard'   => __DIR__.'/classes/controller/dashboard.php',
		'ERP_Provider\\Controller_Products'    => __DIR__.'/classes/controller/products.php',
		'ERP_Provider\\Controller_Inventory'   => __DIR__.'/classes/controller/inventory.php',
		'ERP_Provider\\Controller_Orders'      => __DIR__.'/classes/controller/orders.php',
		'ERP_Provider\\Model_Product'          => __DIR__.'/classes/model/product.php',
		'ERP_Provider\\Model_Inventory'        => __DIR__.'/classes/model/inventory.php',
		'ERP_Provider\\Model_PurchaseOrder'    => __DIR__.'/classes/model/purchaseorder.php',
		'ERP_Provider\\Service_Catalog'        => __DIR__.'/classes/service/catalog.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('ERP_Provider', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'provider'                  => 'erp_provider/dashboard/index',
		'provider/dashboard'        => 'erp_provider/dashboard/index',
		'provider/products'         => 'erp_provider/products/index',
		'provider/products/(:any)'  => 'erp_provider/products/$1',
		'provider/inventory'        => 'erp_provider/inventory/index',
		'provider/inventory/(:any)' => 'erp_provider/inventory/$1',
		'provider/orders'           => 'erp_provider/orders/index',
		'provider/orders/(:any)'    => 'erp_provider/orders/$1',
	));

	// Log module activation
	\Log::info('ERP Provider Module: Module loaded and activated for tenant');
}
else
{
	// Log that module is not active
	\Log::debug('ERP Provider Module: Module not active for current tenant');
}
