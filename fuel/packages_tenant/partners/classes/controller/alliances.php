<?php
/**
 * Partners Module - Alliances Controller
 *
 * @package    Partners
 * @version    1.0.0
 */

namespace Partners;

class Controller_Alliances extends \Controller
{
	public function action_index()
	{
		$data = array('module_name' => 'Mis Alianzas', 'alliances' => array());
		return \Response::forge(\View::forge('partners/alliances/index', $data, false));
	}

	public function action_agregar()
	{
		$data = array('module_name' => 'Nueva Alianza');
		return \Response::forge(\View::forge('partners/alliances/agregar', $data, false));
	}
}
