<?php
/**
 * Partners Module - Contracts Controller
 *
 * @package    Partners
 * @version    1.0.0
 */

namespace Partners;

class Controller_Contracts extends \Controller
{
	public function action_index()
	{
		$data = array('module_name' => 'Contratos', 'contracts' => array());
		return \Response::forge(\View::forge('partners/contracts/index', $data, false));
	}

	public function action_agregar()
	{
		$data = array('module_name' => 'Nuevo Contrato');
		return \Response::forge(\View::forge('partners/contracts/agregar', $data, false));
	}
}
