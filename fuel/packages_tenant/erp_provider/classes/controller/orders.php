<?php
/**
 * ERP Provider Module - Orders Controller
 *
 * @package    ERP_Provider
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Provider;

/**
 * Orders Controller for the Provider Module
 *
 * Provides purchase order management functionality.
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
			'module_name' => 'Órdenes de Compra',
			'breadcrumb' => array(
				'Dashboard' => 'provider',
				'Órdenes' => 'provider/orders',
			),
			'orders' => array(),
		);

		return \Response::forge(\View::forge('erp_provider/orders/index', $data, false));
	}
}
