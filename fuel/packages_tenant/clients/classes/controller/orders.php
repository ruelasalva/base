<?php
/**
 * Clients Module - Orders Controller
 *
 * @package    Clients
 * @version    1.0.0
 */

namespace Clients;

class Controller_Orders extends \Controller
{
	public function action_index()
	{
		$data = array('module_name' => 'Mis Pedidos', 'orders' => array());
		return \Response::forge(\View::forge('clients/orders/index', $data, false));
	}

	public function action_info($id = null)
	{
		if ($id === null)
		{
			\Session::set_flash('error', 'No se proporcionó un ID válido.');
			\Response::redirect('clients/orders');
		}
		$data = array('module_name' => 'Detalle del Pedido', 'order_id' => $id);
		return \Response::forge(\View::forge('clients/orders/info', $data, false));
	}
}
