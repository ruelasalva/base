<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.8.2
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2019 Fuel Development Team
 * @link       https://fuelphp.com
 */

/**
 * -----------------------------------------------------------------------------
 *  Auth Package Configuration
 * -----------------------------------------------------------------------------
 *
 *  Configuration for the Auth package using SimpleAuth driver.
 *
 */

return array(

	/**
	 * -------------------------------------------------------------------------
	 *  Driver to use
	 * -------------------------------------------------------------------------
	 *
	 *  Available drivers: Simpleauth, Ormauth
	 *  For multi-tenant ERP we use Simpleauth
	 *
	 */
	'driver' => 'Simpleauth',

	/**
	 * -------------------------------------------------------------------------
	 *  Verify multiple logins
	 * -------------------------------------------------------------------------
	 *
	 *  Set to true if you want to verify multiple logins with the same
	 *  username/email at the same time.
	 *
	 */
	'verify_multiple_logins' => false,

	/**
	 * -------------------------------------------------------------------------
	 *  Salt for password hashing
	 * -------------------------------------------------------------------------
	 *
	 *  IMPORTANT: Change this to a random string unique to your application!
	 *
	 */
	'salt' => 'ERP_MULTI_TENANT_SALT_CHANGE_ME_IN_PRODUCTION',

	/**
	 * -------------------------------------------------------------------------
	 *  Number of iterations for password hashing
	 * -------------------------------------------------------------------------
	 */
	'iterations' => 10000,

);
