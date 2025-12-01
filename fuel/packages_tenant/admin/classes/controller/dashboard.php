<?php
/**
 * Admin Module - Dashboard Controller
 *
 * @package    Admin
 * @version    1.0.0
 */

namespace Admin;

class Controller_Dashboard extends \Controller
{
	public function action_index()
	{
		$data = array(
			'module_name' => 'Panel de Administración',
			'stats' => array(
				'users' => array('title' => 'Usuarios', 'count' => 0, 'icon' => 'user', 'link' => 'admin/users'),
				'orders' => array('title' => 'Pedidos Hoy', 'count' => 0, 'icon' => 'shopping-cart', 'link' => 'admin/reports'),
				'products' => array('title' => 'Productos', 'count' => 0, 'icon' => 'cube', 'link' => 'admin/reports'),
				'revenue' => array('title' => 'Ingresos Hoy', 'count' => '$0.00', 'icon' => 'usd', 'link' => 'admin/reports'),
			),
			'quick_links' => array(
				array('title' => 'Gestión de Usuarios', 'url' => 'admin/users', 'icon' => 'users'),
				array('title' => 'Configuración', 'url' => 'admin/settings', 'icon' => 'cog'),
				array('title' => 'Reportes', 'url' => 'admin/reports', 'icon' => 'bar-chart'),
			),
		);

		return \Response::forge(\View::forge('admin/dashboard', $data, false));
	}
}
