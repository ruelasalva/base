<?php
/**
 * Providers Module - Dashboard Controller
 *
 * @package    Providers
 * @version    1.0.0
 */

namespace Providers;

class Controller_Dashboard extends \Controller
{
	public function action_index()
	{
		$data = array(
			'module_name' => 'Panel de Proveedor',
			'stats' => array(
				'products' => array('title' => 'Mis Productos', 'count' => 0, 'icon' => 'cube', 'link' => 'providers/products'),
				'orders' => array('title' => 'Órdenes Pendientes', 'count' => 0, 'icon' => 'shopping-cart', 'link' => 'providers/orders'),
				'inventory' => array('title' => 'Stock Bajo', 'count' => 0, 'icon' => 'warning', 'link' => 'providers/inventory'),
			),
			'quick_links' => array(
				array('title' => 'Gestión de Productos', 'url' => 'providers/products', 'icon' => 'cubes'),
				array('title' => 'Inventario', 'url' => 'providers/inventory', 'icon' => 'archive'),
				array('title' => 'Órdenes de Compra', 'url' => 'providers/orders', 'icon' => 'file-text'),
			),
		);

		return \Response::forge(\View::forge('providers/dashboard', $data, false));
	}
}
