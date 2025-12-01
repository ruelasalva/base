<?php
/**
 * Providers Module - Products Controller
 *
 * @package    Providers
 * @version    1.0.0
 */

namespace Providers;

class Controller_Products extends \Controller
{
	public function action_index()
	{
		$data = array('module_name' => 'Mis Productos', 'products' => array());
		return \Response::forge(\View::forge('providers/products/index', $data, false));
	}

	public function action_agregar()
	{
		$data = array('module_name' => 'Agregar Producto');
		return \Response::forge(\View::forge('providers/products/agregar', $data, false));
	}
}
