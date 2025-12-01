<?php
/**
 * Sellers Module - Quotes Controller
 *
 * @package    Sellers
 * @version    1.0.0
 */

namespace Sellers;

class Controller_Quotes extends \Controller
{
	public function action_index()
	{
		$data = array('module_name' => 'Cotizaciones', 'quotes' => array());
		return \Response::forge(\View::forge('sellers/quotes/index', $data, false));
	}

	public function action_agregar()
	{
		$data = array('module_name' => 'Nueva Cotización');
		return \Response::forge(\View::forge('sellers/quotes/agregar', $data, false));
	}
}
