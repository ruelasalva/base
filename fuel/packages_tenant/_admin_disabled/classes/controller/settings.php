<?php
/**
 * Admin Module - Settings Controller
 *
 * @package    Admin
 * @version    1.0.0
 */

namespace Admin;

class Controller_Settings extends \Controller
{
	public function action_index()
	{
		$data = array(
			'module_name' => 'Configuración del Sistema',
			'settings_groups' => array(
				'general' => 'Configuración General',
				'email' => 'Configuración de Email',
				'security' => 'Seguridad',
				'integrations' => 'Integraciones',
			),
		);

		return \Response::forge(\View::forge('admin/settings/index', $data, false));
	}
}
