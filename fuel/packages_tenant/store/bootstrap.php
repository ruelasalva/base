<?php
/**
 * Store Module - Multi-tenant Module Bootstrap
 *
 * Frontend de tienda del ERP.
 * Incluye catálogo de productos, carrito de compras,
 * proceso de checkout, y búsqueda de productos.
 *
 * @package    Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

// Define module key for this module
if ( ! defined('STORE_KEY'))
{
	define('STORE_KEY', 'store');
}

/**
 * Check if this module is active for the current tenant
 */
function store_is_active()
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

	return in_array(STORE_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (store_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'Store\\Controller_Home'       => __DIR__.'/classes/controller/home.php',
		'Store\\Controller_Catalog'    => __DIR__.'/classes/controller/catalog.php',
		'Store\\Controller_Product'    => __DIR__.'/classes/controller/product.php',
		'Store\\Controller_Cart'       => __DIR__.'/classes/controller/cart.php',
		'Store\\Controller_Checkout'   => __DIR__.'/classes/controller/checkout.php',
		'Store\\Controller_Search'     => __DIR__.'/classes/controller/search.php',
		'Store\\Model_Product'         => __DIR__.'/classes/model/product.php',
		'Store\\Model_Category'        => __DIR__.'/classes/model/category.php',
		'Store\\Model_Cart'            => __DIR__.'/classes/model/cart.php',
		'Store\\Model_Order'           => __DIR__.'/classes/model/order.php',
		'Store\\Service_CartManager'   => __DIR__.'/classes/service/cartmanager.php',
		'Store\\Service_Checkout'      => __DIR__.'/classes/service/checkout.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('Store', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'tienda'                   => 'store/home/index',
		'tienda/catalogo'          => 'store/catalog/index',
		'tienda/catalogo/(:any)'   => 'store/catalog/$1',
		'tienda/producto/(:num)'   => 'store/product/view/$1',
		'tienda/carrito'           => 'store/cart/index',
		'tienda/carrito/(:any)'    => 'store/cart/$1',
		'tienda/checkout'          => 'store/checkout/index',
		'tienda/checkout/(:any)'   => 'store/checkout/$1',
		'tienda/buscar'            => 'store/search/index',
		'tienda/buscar/(:any)'     => 'store/search/$1',
	));

	\Log::info('Store Module: Module loaded and activated for tenant');
}
else
{
	\Log::debug('Store Module: Module not active for current tenant');
}
