<?php
/**
 * ERP Clients Module - Multi-tenant Module Bootstrap
 *
 * Backend para clientes del ERP.
 * Incluye gestión de pedidos, historial de compras,
 * perfil del cliente, notificaciones y soporte.
 *
 * @package    ERP_Clients
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
if ( ! defined('ERP_CLIENTS_KEY'))
{
	define('ERP_CLIENTS_KEY', 'erp_clients');
}

/**
 * Check if this module is active for the current tenant
 */
function erp_clients_is_active()
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
	return in_array(ERP_CLIENTS_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (erp_clients_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'ERP_Clients\\Controller_Dashboard'    => __DIR__.'/classes/controller/dashboard.php',
		'ERP_Clients\\Controller_Orders'       => __DIR__.'/classes/controller/orders.php',
		'ERP_Clients\\Controller_Profile'      => __DIR__.'/classes/controller/profile.php',
		'ERP_Clients\\Controller_Support'      => __DIR__.'/classes/controller/support.php',
		'ERP_Clients\\Model_Order'             => __DIR__.'/classes/model/order.php',
		'ERP_Clients\\Model_Profile'           => __DIR__.'/classes/model/profile.php',
		'ERP_Clients\\Model_Ticket'            => __DIR__.'/classes/model/ticket.php',
		'ERP_Clients\\Service_OrderManager'    => __DIR__.'/classes/service/ordermanager.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('ERP_Clients', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'clients'                  => 'erp_clients/dashboard/index',
		'clients/dashboard'        => 'erp_clients/dashboard/index',
		'clients/orders'           => 'erp_clients/orders/index',
		'clients/orders/(:any)'    => 'erp_clients/orders/$1',
		'clients/profile'          => 'erp_clients/profile/index',
		'clients/profile/(:any)'   => 'erp_clients/profile/$1',
		'clients/support'          => 'erp_clients/support/index',
		'clients/support/(:any)'   => 'erp_clients/support/$1',
	));

	// Log module activation
	\Log::info('ERP Clients Module: Module loaded and activated for tenant');
}
else
{
	// Log that module is not active
	\Log::debug('ERP Clients Module: Module not active for current tenant');
}
