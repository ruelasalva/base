<?php
/**
 * Providers Module - Multi-tenant Module Bootstrap
 *
 * Backend para proveedores del ERP.
 * Incluye gestión de productos, inventario, órdenes de compra,
 * y dashboard de proveedor.
 *
 * @package    Providers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

// Define module key for this module
if ( ! defined('PROVIDERS_KEY'))
{
	define('PROVIDERS_KEY', 'providers');
}

/**
 * Check if this module is active for the current tenant
 */
function providers_is_active()
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

	return in_array(PROVIDERS_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (providers_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'Providers\\Controller_Dashboard'   => __DIR__.'/classes/controller/dashboard.php',
		'Providers\\Controller_Products'    => __DIR__.'/classes/controller/products.php',
		'Providers\\Controller_Inventory'   => __DIR__.'/classes/controller/inventory.php',
		'Providers\\Controller_Orders'      => __DIR__.'/classes/controller/orders.php',
		'Providers\\Model_Product'          => __DIR__.'/classes/model/product.php',
		'Providers\\Model_Inventory'        => __DIR__.'/classes/model/inventory.php',
		'Providers\\Model_PurchaseOrder'    => __DIR__.'/classes/model/purchaseorder.php',
		'Providers\\Service_Catalog'        => __DIR__.'/classes/service/catalog.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('Providers', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'providers'                   => 'providers/dashboard/index',
		'providers/dashboard'         => 'providers/dashboard/index',
		'providers/products'          => 'providers/products/index',
		'providers/products/(:any)'   => 'providers/products/$1',
		'providers/inventory'         => 'providers/inventory/index',
		'providers/inventory/(:any)'  => 'providers/inventory/$1',
		'providers/orders'            => 'providers/orders/index',
		'providers/orders/(:any)'     => 'providers/orders/$1',
	));

	\Log::info('Providers Module: Module loaded and activated for tenant');
}
else
{
	\Log::debug('Providers Module: Module not active for current tenant');
}
