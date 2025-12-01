<?php
/**
 * ERP Sellers Module - Dashboard Controller
 *
 * @package    ERP_Sellers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Sellers;

/**
 * Dashboard Controller for the Sellers Module
 *
 * Provides the main sellers dashboard with sales stats,
 * commissions, and customer overview.
 */
class Controller_Dashboard extends \Controller
{
	/**
	 * Index action - displays the sellers dashboard
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Panel de Vendedor',
			'breadcrumb' => array(
				'Dashboard' => 'sellers',
			),
			'stats' => array(
				'sales_today' => array(
					'title' => 'Ventas Hoy',
					'count' => 0,
					'icon' => 'shopping-cart',
					'link' => 'sellers/sales',
				),
				'customers' => array(
					'title' => 'Mis Clientes',
					'count' => 0,
					'icon' => 'users',
					'link' => 'sellers/customers',
				),
				'quotes' => array(
					'title' => 'Cotizaciones Pendientes',
					'count' => 0,
					'icon' => 'file-text',
					'link' => 'sellers/quotes',
				),
				'commission' => array(
					'title' => 'Comisión del Mes',
					'count' => '$0.00',
					'icon' => 'usd',
					'link' => 'sellers/commissions',
				),
			),
			'quick_links' => array(
				array('title' => 'Nueva Venta', 'url' => 'sellers/sales/agregar', 'icon' => 'plus'),
				array('title' => 'Mis Clientes', 'url' => 'sellers/customers', 'icon' => 'users'),
				array('title' => 'Cotizaciones', 'url' => 'sellers/quotes', 'icon' => 'file-text'),
				array('title' => 'Mis Comisiones', 'url' => 'sellers/commissions', 'icon' => 'money'),
			),
		);

		return \Response::forge(\View::forge('erp_sellers/dashboard', $data, false));
	}
}
