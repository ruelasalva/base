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

return array(
	/**
	 * -------------------------------------------------------------------------
	 *  Default route
	 * -------------------------------------------------------------------------
	 *
	 */

	'_root_' => 'main/index',

	/**
	 * -------------------------------------------------------------------------
	 *  Page not found
	 * -------------------------------------------------------------------------
	 *
	 */

	'_404_' => 'welcome/404',

	/**
	 * -------------------------------------------------------------------------
	 *  Installer routes
	 * -------------------------------------------------------------------------
	 *
	 *  El instalador permite configurar la base de datos y ejecutar migraciones
	 *  cuando el código se despliega en un nuevo dominio.
	 *
	 */

	'install'              => 'install/index',
	'install/configurar'   => 'install/configurar',
	'install/ejecutar'     => 'install/ejecutar',
	'install/auto_install' => 'install/auto_install',
	'install/crear_admin'  => 'install/crear_admin',
	'install/completado'   => 'install/completado',
	'install/verificar_db' => 'install/verificar_db',

	/**
	 * -------------------------------------------------------------------------
	 *  Authentication routes
	 * -------------------------------------------------------------------------
	 */

	'auth/login'        => 'auth/login',
	'auth/logout'       => 'auth/logout',
	'auth/register'     => 'auth/register',
	'auth/forgot'       => 'auth/forgot',
	'auth/reset/:token' => 'auth/reset/$1',

	/**
	 * -------------------------------------------------------------------------
	 *  Diagnostic and Utilities
	 * -------------------------------------------------------------------------
	 */

	'diagnostico'       => 'diagnostico/index',

	/**
	 * -------------------------------------------------------------------------
	 *  Error routes
	 * -------------------------------------------------------------------------
	 */

	'error/403' => 'error/403',
	'error/404' => 'error/404',
	'error/500' => 'error/500',

	/**
	 * -------------------------------------------------------------------------
	 *  Example for Presenter
	 * -------------------------------------------------------------------------
	 *
	 *  A route for showing page using Presenter
	 *
	 */

	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
);
