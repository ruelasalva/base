<?php
/**
 * ERP Store Module - Checkout Controller
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Checkout Controller for the Store Module
 *
 * Provides checkout process functionality.
 */
class Controller_Checkout extends \Controller
{
	/**
	 * Index action - displays checkout page
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'page_title' => 'Finalizar Compra',
			'cart_items' => array(),
			'total' => 0,
			'step' => 1,
		);

		return \Response::forge(\View::forge('erp_store/checkout/index', $data, false));
	}

	/**
	 * Shipping action - shipping information
	 *
	 * @return \Response
	 */
	public function action_envio()
	{
		$data = array(
			'page_title' => 'Información de Envío',
			'step' => 2,
		);

		return \Response::forge(\View::forge('erp_store/checkout/envio', $data, false));
	}

	/**
	 * Payment action - payment information
	 *
	 * @return \Response
	 */
	public function action_pago()
	{
		$data = array(
			'page_title' => 'Método de Pago',
			'step' => 3,
		);

		return \Response::forge(\View::forge('erp_store/checkout/pago', $data, false));
	}

	/**
	 * Confirm action - confirm order
	 *
	 * @return \Response
	 */
	public function action_confirmar()
	{
		$data = array(
			'page_title' => 'Confirmar Pedido',
			'step' => 4,
		);

		return \Response::forge(\View::forge('erp_store/checkout/confirmar', $data, false));
	}

	/**
	 * Complete action - order completed
	 *
	 * @param string $order_id Order ID
	 * @return \Response
	 */
	public function action_completado($order_id = null)
	{
		$data = array(
			'page_title' => 'Pedido Completado',
			'order_id' => $order_id,
		);

		return \Response::forge(\View::forge('erp_store/checkout/completado', $data, false));
	}
}
