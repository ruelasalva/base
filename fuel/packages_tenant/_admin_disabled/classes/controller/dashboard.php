<?php
/**
 * Admin Module - Dashboard Controller
 *
 * @package    Admin
 * @version    1.0.0
 */

namespace Admin;

/**
 * Admin Dashboard Controller
 */
class Controller_Dashboard extends \Controller_Template
{
	/**
	 * Template a usar
	 */
	public $template = 'template';
	
	/**
	 * Before
	 */
	public function before()
	{
		parent::before();
		
		// Si no existe template, no usar auto_render
		if ( ! \View::exists($this->template))
		{
			$this->auto_render = false;
		}
	}
	
	/**
	 * Dashboard principal
	 */
	public function action_index()
	{
		// Datos de ejemplo para el dashboard
		$data = array(
			'module_name' => 'Panel de Administración',
			'page_title' => 'Dashboard',
			'stats' => array(
				'users' => array(
					'title' => 'Usuarios',
					'count' => 0,
					'icon' => 'user',
					'link' => 'admin/users'
				),
				'orders' => array(
					'title' => 'Pedidos Hoy',
					'count' => 0,
					'icon' => 'shopping-cart',
					'link' => 'admin/reports'
				),
				'products' => array(
					'title' => 'Productos',
					'count' => 0,
					'icon' => 'cube',
					'link' => 'admin/reports'
				),
				'revenue' => array(
					'title' => 'Ingresos Hoy',
					'count' => '$0.00',
					'icon' => 'usd',
					'link' => 'admin/reports'
				),
			),
			'quick_links' => array(
				array('title' => 'Gestión de Usuarios', 'url' => 'admin/users', 'icon' => 'users'),
				array('title' => 'Configuración', 'url' => 'admin/settings', 'icon' => 'cog'),
				array('title' => 'Reportes', 'url' => 'admin/reports', 'icon' => 'bar-chart'),
			),
		);
		
		// Si auto_render está desactivado, devolver la vista directamente
		if ( ! $this->auto_render)
		{
			return \Response::forge(\View::forge('admin::dashboard/index', $data, false));
		}
		
		// Asignar vista al template
		$this->template->title = 'Dashboard - Admin';
		$this->template->content = \View::forge('admin::dashboard/index', $data, false);
	}
}
