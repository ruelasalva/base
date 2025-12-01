<?php
/**
 * Providers Module - Inventory Controller
 *
 * @package    Providers
 * @version    1.0.0
 */

namespace Providers;

class Controller_Inventory extends \Controller
{
	public function action_index()
	{
		$data = array('module_name' => 'Gestión de Inventario', 'inventory' => array());
		return \Response::forge(\View::forge('providers/inventory/index', $data, false));
	}
}
