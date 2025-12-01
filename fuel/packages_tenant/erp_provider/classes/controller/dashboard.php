<?php
/**
 * ERP Provider Module - Dashboard Controller
 *
 * @package    ERP_Provider
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Provider;

/**
 * Dashboard Controller for the Provider Module
 *
 * Provides the main provider dashboard with product stats,
 * pending orders, and inventory overview.
 */
class Controller_Dashboard extends \Controller
{
	/**
	 * Index action - displays the provider dashboard
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Panel de Proveedor',
			'breadcrumb' => array(
				'Dashboard' => 'provider',
			),
			'stats' => array(
				'products' => array(
					'title' => 'Mis Productos',
					'count' => 0,
					'icon' => 'cube',
					'link' => 'provider/products',
				),
				'orders' => array(
					'title' => 'Órdenes Pendientes',
					'count' => 0,
					'icon' => 'shopping-cart',
					'link' => 'provider/orders',
				),
				'inventory' => array(
					'title' => 'Stock Bajo',
					'count' => 0,
					'icon' => 'warning',
					'link' => 'provider/inventory',
				),
			),
			'quick_links' => array(
				array('title' => 'Gestión de Productos', 'url' => 'provider/products', 'icon' => 'cubes'),
				array('title' => 'Inventario', 'url' => 'provider/inventory', 'icon' => 'archive'),
				array('title' => 'Órdenes de Compra', 'url' => 'provider/orders', 'icon' => 'file-text'),
			),
		);

		return \Response::forge(\View::forge('erp_provider/dashboard', $data, false));
	}
}
