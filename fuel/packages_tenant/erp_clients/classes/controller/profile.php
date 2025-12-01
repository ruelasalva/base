<?php
/**
 * ERP Clients Module - Profile Controller
 *
 * @package    ERP_Clients
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Clients;

/**
 * Profile Controller for the Clients Module
 *
 * Provides client profile management functionality.
 */
class Controller_Profile extends \Controller
{
	/**
	 * Index action - displays profile page
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Mi Perfil',
			'breadcrumb' => array(
				'Dashboard' => 'clients',
				'Perfil' => 'clients/profile',
			),
		);

		return \Response::forge(\View::forge('erp_clients/profile/index', $data, false));
	}

	/**
	 * Edit action - edit profile
	 *
	 * @return \Response
	 */
	public function action_editar()
	{
		$data = array(
			'module_name' => 'Editar Perfil',
			'breadcrumb' => array(
				'Dashboard' => 'clients',
				'Perfil' => 'clients/profile',
				'Editar' => 'clients/profile/editar',
			),
		);

		return \Response::forge(\View::forge('erp_clients/profile/editar', $data, false));
	}
}
