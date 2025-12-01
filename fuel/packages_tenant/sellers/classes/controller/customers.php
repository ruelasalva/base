<?php
/**
 * Sellers Module - Customers Controller
 *
 * @package    Sellers
 * @version    1.0.0
 */

namespace Sellers;

class Controller_Customers extends \Controller
{
	public function action_index()
	{
		$data = array('module_name' => 'Mis Clientes', 'customers' => array());
		return \Response::forge(\View::forge('sellers/customers/index', $data, false));
	}

	public function action_agregar()
	{
		$data = array('module_name' => 'Agregar Cliente');
		return \Response::forge(\View::forge('sellers/customers/agregar', $data, false));
	}
}
