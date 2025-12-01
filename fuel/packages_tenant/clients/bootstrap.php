<?php
/**
 * Clients Module - Multi-tenant Module Bootstrap
 *
 * Backend para clientes del ERP.
 * Incluye gestión de pedidos, historial de compras,
 * perfil del cliente, notificaciones y soporte.
 *
 * @package    Clients
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

// Define module key for this module
if ( ! defined('CLIENTS_KEY'))
{
	define('CLIENTS_KEY', 'clients');
}

/**
 * Check if this module is active for the current tenant
 */
function clients_is_active()
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

	return in_array(CLIENTS_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (clients_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'Clients\\Controller_Dashboard'    => __DIR__.'/classes/controller/dashboard.php',
		'Clients\\Controller_Orders'       => __DIR__.'/classes/controller/orders.php',
		'Clients\\Controller_Profile'      => __DIR__.'/classes/controller/profile.php',
		'Clients\\Controller_Support'      => __DIR__.'/classes/controller/support.php',
		'Clients\\Model_Order'             => __DIR__.'/classes/model/order.php',
		'Clients\\Model_Profile'           => __DIR__.'/classes/model/profile.php',
		'Clients\\Model_Ticket'            => __DIR__.'/classes/model/ticket.php',
		'Clients\\Service_OrderManager'    => __DIR__.'/classes/service/ordermanager.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('Clients', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'clients'                  => 'clients/dashboard/index',
		'clients/dashboard'        => 'clients/dashboard/index',
		'clients/orders'           => 'clients/orders/index',
		'clients/orders/(:any)'    => 'clients/orders/$1',
		'clients/profile'          => 'clients/profile/index',
		'clients/profile/(:any)'   => 'clients/profile/$1',
		'clients/support'          => 'clients/support/index',
		'clients/support/(:any)'   => 'clients/support/$1',
	));

	\Log::info('Clients Module: Module loaded and activated for tenant');
}
else
{
	\Log::debug('Clients Module: Module not active for current tenant');
}
