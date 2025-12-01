<?php
/**
 * Providers Module - Orders Controller
 *
 * @package    Providers
 * @version    1.0.0
 */

namespace Providers;

class Controller_Orders extends \Controller
{
	public function action_index()
	{
		$data = array('module_name' => 'Órdenes de Compra', 'orders' => array());
		return \Response::forge(\View::forge('providers/orders/index', $data, false));
	}
}
