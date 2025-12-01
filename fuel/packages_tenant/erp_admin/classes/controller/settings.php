<?php
/**
 * ERP Admin Module - Settings Controller
 *
 * @package    ERP_Admin
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Admin;

/**
 * Settings Controller for the Admin Module
 *
 * Provides system configuration functionality.
 */
class Controller_Settings extends \Controller
{
	/**
	 * Index action - displays settings page
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Configuración del Sistema',
			'breadcrumb' => array(
				'Dashboard' => 'admin',
				'Configuración' => 'admin/settings',
			),
			'settings_groups' => array(
				'general' => 'Configuración General',
				'email' => 'Configuración de Email',
				'security' => 'Seguridad',
				'integrations' => 'Integraciones',
			),
		);

		return \Response::forge(\View::forge('erp_admin/settings/index', $data, false));
	}
}
