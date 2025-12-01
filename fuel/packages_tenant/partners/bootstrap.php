<?php
/**
 * Partners Module - Multi-tenant Module Bootstrap
 *
 * Backend para socios comerciales del ERP.
 * Incluye gestión de alianzas, contratos, comisiones de partner,
 * y dashboard de partner.
 *
 * @package    Partners
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

// Define module key for this module
if ( ! defined('PARTNERS_KEY'))
{
	define('PARTNERS_KEY', 'partners');
}

/**
 * Check if this module is active for the current tenant
 */
function partners_is_active()
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

	return in_array(PARTNERS_KEY, $active_modules, true);
}

/**
 * Initialize the module only if active for current tenant
 */
if (partners_is_active())
{
	// Register module classes with autoloader
	\Autoloader::add_classes(array(
		'Partners\\Controller_Dashboard'   => __DIR__.'/classes/controller/dashboard.php',
		'Partners\\Controller_Alliances'   => __DIR__.'/classes/controller/alliances.php',
		'Partners\\Controller_Contracts'   => __DIR__.'/classes/controller/contracts.php',
		'Partners\\Controller_Commissions' => __DIR__.'/classes/controller/commissions.php',
		'Partners\\Model_Alliance'         => __DIR__.'/classes/model/alliance.php',
		'Partners\\Model_Contract'         => __DIR__.'/classes/model/contract.php',
		'Partners\\Model_Commission'       => __DIR__.'/classes/model/commission.php',
		'Partners\\Service_PartnerManager' => __DIR__.'/classes/service/partnermanager.php',
	));

	// Add namespace for the module
	\Autoloader::add_namespace('Partners', __DIR__.'/classes/');

	// Register module routes
	\Router::add(array(
		'partners'                     => 'partners/dashboard/index',
		'partners/dashboard'           => 'partners/dashboard/index',
		'partners/alliances'           => 'partners/alliances/index',
		'partners/alliances/(:any)'    => 'partners/alliances/$1',
		'partners/contracts'           => 'partners/contracts/index',
		'partners/contracts/(:any)'    => 'partners/contracts/$1',
		'partners/commissions'         => 'partners/commissions/index',
		'partners/commissions/(:any)'  => 'partners/commissions/$1',
	));

	\Log::info('Partners Module: Module loaded and activated for tenant');
}
else
{
	\Log::debug('Partners Module: Module not active for current tenant');
}
