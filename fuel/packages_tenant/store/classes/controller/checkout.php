<?php
/**
 * Store Module - Checkout Controller
 *
 * @package    Store
 * @version    1.0.0
 */

namespace Store;

class Controller_Checkout extends \Controller
{
	public function action_index()
	{
		$data = array('page_title' => 'Finalizar Compra', 'cart_items' => array(), 'total' => 0, 'step' => 1);
		return \Response::forge(\View::forge('store/checkout/index', $data, false));
	}

	public function action_envio()
	{
		$data = array('page_title' => 'Información de Envío', 'step' => 2);
		return \Response::forge(\View::forge('store/checkout/envio', $data, false));
	}

	public function action_pago()
	{
		$data = array('page_title' => 'Método de Pago', 'step' => 3);
		return \Response::forge(\View::forge('store/checkout/pago', $data, false));
	}

	public function action_confirmar()
	{
		$data = array('page_title' => 'Confirmar Pedido', 'step' => 4);
		return \Response::forge(\View::forge('store/checkout/confirmar', $data, false));
	}

	public function action_completado($order_id = null)
	{
		$data = array('page_title' => 'Pedido Completado', 'order_id' => $order_id);
		return \Response::forge(\View::forge('store/checkout/completado', $data, false));
	}
}
