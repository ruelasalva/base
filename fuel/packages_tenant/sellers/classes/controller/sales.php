<?php
/**
 * Sellers Module - Sales Controller
 *
 * @package    Sellers
 * @version    1.0.0
 */

namespace Sellers;

class Controller_Sales extends \Controller
{
	public function action_index()
	{
		$data = array('module_name' => 'Mis Ventas', 'sales' => array());
		return \Response::forge(\View::forge('sellers/sales/index', $data, false));
	}

	public function action_agregar()
	{
		$data = array('module_name' => 'Nueva Venta');
		return \Response::forge(\View::forge('sellers/sales/agregar', $data, false));
	}
}
