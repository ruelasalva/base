<?php
/**
 * ERP Clients Module - Dashboard Controller
 *
 * @package    ERP_Clients
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Clients;

/**
 * Dashboard Controller for the Clients Module
 *
 * Provides the main clients dashboard with order history,
 * profile info, and support tickets.
 */
class Controller_Dashboard extends \Controller
{
	/**
	 * Index action - displays the clients dashboard
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Mi Cuenta',
			'breadcrumb' => array(
				'Dashboard' => 'clients',
			),
			'stats' => array(
				'orders' => array(
					'title' => 'Mis Pedidos',
					'count' => 0,
					'icon' => 'shopping-bag',
					'link' => 'clients/orders',
				),
				'pending' => array(
					'title' => 'Pedidos en Proceso',
					'count' => 0,
					'icon' => 'clock-o',
					'link' => 'clients/orders',
				),
				'tickets' => array(
					'title' => 'Tickets de Soporte',
					'count' => 0,
					'icon' => 'life-ring',
					'link' => 'clients/support',
				),
			),
			'quick_links' => array(
				array('title' => 'Mis Pedidos', 'url' => 'clients/orders', 'icon' => 'shopping-bag'),
				array('title' => 'Mi Perfil', 'url' => 'clients/profile', 'icon' => 'user'),
				array('title' => 'Soporte', 'url' => 'clients/support', 'icon' => 'life-ring'),
			),
		);

		return \Response::forge(\View::forge('erp_clients/dashboard', $data, false));
	}
}
