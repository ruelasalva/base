<?php
/**
 * ERP Clients Module - Orders Controller
 *
 * @package    ERP_Clients
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Clients;

/**
 * Orders Controller for the Clients Module
 *
 * Provides order history and tracking functionality.
 */
class Controller_Orders extends \Controller
{
	/**
	 * Index action - displays orders list
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Mis Pedidos',
			'breadcrumb' => array(
				'Dashboard' => 'clients',
				'Pedidos' => 'clients/orders',
			),
			'orders' => array(),
		);

		return \Response::forge(\View::forge('erp_clients/orders/index', $data, false));
	}

	/**
	 * View action - displays order details
	 *
	 * @param int $id Order ID
	 * @return \Response
	 */
	public function action_info($id = null)
	{
		if ($id === null)
		{
			\Session::set_flash('error', 'No se proporcionó un ID válido.');
			\Response::redirect('clients/orders');
		}

		$data = array(
			'module_name' => 'Detalle del Pedido',
			'breadcrumb' => array(
				'Dashboard' => 'clients',
				'Pedidos' => 'clients/orders',
				'Detalle' => 'clients/orders/info/'.$id,
			),
			'order_id' => $id,
		);

		return \Response::forge(\View::forge('erp_clients/orders/info', $data, false));
	}
}
