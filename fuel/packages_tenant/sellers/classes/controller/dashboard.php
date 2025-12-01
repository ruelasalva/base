<?php
/**
 * Sellers Module - Dashboard Controller
 *
 * @package    Sellers
 * @version    1.0.0
 */

namespace Sellers;

class Controller_Dashboard extends \Controller
{
	public function action_index()
	{
		$data = array(
			'module_name' => 'Panel de Vendedor',
			'stats' => array(
				'sales_today' => array('title' => 'Ventas Hoy', 'count' => 0, 'icon' => 'shopping-cart', 'link' => 'sellers/sales'),
				'customers' => array('title' => 'Mis Clientes', 'count' => 0, 'icon' => 'users', 'link' => 'sellers/customers'),
				'quotes' => array('title' => 'Cotizaciones Pendientes', 'count' => 0, 'icon' => 'file-text', 'link' => 'sellers/quotes'),
				'commission' => array('title' => 'Comisión del Mes', 'count' => '$0.00', 'icon' => 'usd', 'link' => 'sellers/commissions'),
			),
			'quick_links' => array(
				array('title' => 'Nueva Venta', 'url' => 'sellers/sales/agregar', 'icon' => 'plus'),
				array('title' => 'Mis Clientes', 'url' => 'sellers/customers', 'icon' => 'users'),
				array('title' => 'Cotizaciones', 'url' => 'sellers/quotes', 'icon' => 'file-text'),
				array('title' => 'Mis Comisiones', 'url' => 'sellers/commissions', 'icon' => 'money'),
			),
		);

		return \Response::forge(\View::forge('sellers/dashboard', $data, false));
	}
}
