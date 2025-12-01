<?php
/**
 * Clients Module - Profile Controller
 *
 * @package    Clients
 * @version    1.0.0
 */

namespace Clients;

class Controller_Profile extends \Controller
{
	public function action_index()
	{
		$data = array('module_name' => 'Mi Perfil');
		return \Response::forge(\View::forge('clients/profile/index', $data, false));
	}

	public function action_editar()
	{
		$data = array('module_name' => 'Editar Perfil');
		return \Response::forge(\View::forge('clients/profile/editar', $data, false));
	}
}
