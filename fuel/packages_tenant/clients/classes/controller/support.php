<?php
/**
 * Clients Module - Support Controller
 *
 * @package    Clients
 * @version    1.0.0
 */

namespace Clients;

class Controller_Support extends \Controller
{
	public function action_index()
	{
		$data = array('module_name' => 'Soporte', 'tickets' => array());
		return \Response::forge(\View::forge('clients/support/index', $data, false));
	}

	public function action_agregar()
	{
		$data = array('module_name' => 'Nuevo Ticket');
		return \Response::forge(\View::forge('clients/support/agregar', $data, false));
	}
}
