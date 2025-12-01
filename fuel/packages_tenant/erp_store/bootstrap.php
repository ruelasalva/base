<?php
/**
 * ERP Store Module - Multi-tenant Module Bootstrap
 *
 * Frontend de tienda del ERP.
 * Incluye catálogo de productos, carrito de compras,
 * proceso de checkout, y búsqueda de productos.
 *
 * @package    ERP_Store
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
if ( ! defined('ERP_STORE_KEY'))
{
	define('ERP_STORE_KEY', 'erp_store');
}

/**
 * Check if this module is active for the current tenant
 */
function erp_store_is_active()
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
	return in_array(ERP_STORE_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (erp_store_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'ERP_Store\\Controller_Home'       => __DIR__.'/classes/controller/home.php',
		'ERP_Store\\Controller_Catalog'    => __DIR__.'/classes/controller/catalog.php',
		'ERP_Store\\Controller_Product'    => __DIR__.'/classes/controller/product.php',
		'ERP_Store\\Controller_Cart'       => __DIR__.'/classes/controller/cart.php',
		'ERP_Store\\Controller_Checkout'   => __DIR__.'/classes/controller/checkout.php',
		'ERP_Store\\Controller_Search'     => __DIR__.'/classes/controller/search.php',
		'ERP_Store\\Model_Product'         => __DIR__.'/classes/model/product.php',
		'ERP_Store\\Model_Category'        => __DIR__.'/classes/model/category.php',
		'ERP_Store\\Model_Cart'            => __DIR__.'/classes/model/cart.php',
		'ERP_Store\\Model_Order'           => __DIR__.'/classes/model/order.php',
		'ERP_Store\\Service_CartManager'   => __DIR__.'/classes/service/cartmanager.php',
		'ERP_Store\\Service_Checkout'      => __DIR__.'/classes/service/checkout.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('ERP_Store', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'tienda'                   => 'erp_store/home/index',
		'tienda/catalogo'          => 'erp_store/catalog/index',
		'tienda/catalogo/(:any)'   => 'erp_store/catalog/$1',
		'tienda/producto/(:num)'   => 'erp_store/product/view/$1',
		'tienda/carrito'           => 'erp_store/cart/index',
		'tienda/carrito/(:any)'    => 'erp_store/cart/$1',
		'tienda/checkout'          => 'erp_store/checkout/index',
		'tienda/checkout/(:any)'   => 'erp_store/checkout/$1',
		'tienda/buscar'            => 'erp_store/search/index',
		'tienda/buscar/(:any)'     => 'erp_store/search/$1',
	));

	// Log module activation
	\Log::info('ERP Store Module: Module loaded and activated for tenant');
}
else
{
	// Log that module is not active
	\Log::debug('ERP Store Module: Module not active for current tenant');
}
