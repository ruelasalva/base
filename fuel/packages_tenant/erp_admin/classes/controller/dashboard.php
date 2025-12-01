<?php
/**
 * ERP Admin Module - Dashboard Controller
 *
 * @package    ERP_Admin
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Admin;

/**
 * Dashboard Controller for the Admin Module
 *
 * Provides the main administrative dashboard with system overview,
 * quick stats, and access to all admin functions.
 */
class Controller_Dashboard extends \Controller
{
	/**
	 * Index action - displays the admin dashboard
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Panel de Administración',
			'breadcrumb' => array(
				'Dashboard' => 'admin',
			),
			'stats' => array(
				'users' => array(
					'title' => 'Usuarios',
					'count' => 0,
					'icon' => 'user',
					'link' => 'admin/users',
				),
				'orders' => array(
					'title' => 'Pedidos Hoy',
					'count' => 0,
					'icon' => 'shopping-cart',
					'link' => 'admin/reports',
				),
				'products' => array(
					'title' => 'Productos',
					'count' => 0,
					'icon' => 'cube',
					'link' => 'admin/reports',
				),
				'revenue' => array(
					'title' => 'Ingresos Hoy',
					'count' => '$0.00',
					'icon' => 'usd',
					'link' => 'admin/reports',
				),
			),
			'quick_links' => array(
				array('title' => 'Gestión de Usuarios', 'url' => 'admin/users', 'icon' => 'users'),
				array('title' => 'Configuración', 'url' => 'admin/settings', 'icon' => 'cog'),
				array('title' => 'Reportes', 'url' => 'admin/reports', 'icon' => 'bar-chart'),
			),
		);

		return \Response::forge(\View::forge('erp_admin/dashboard', $data, false));
	}
}
