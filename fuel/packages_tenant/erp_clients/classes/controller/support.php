<?php
/**
 * ERP Clients Module - Support Controller
 *
 * @package    ERP_Clients
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Clients;

/**
 * Support Controller for the Clients Module
 *
 * Provides support ticket management functionality.
 */
class Controller_Support extends \Controller
{
	/**
	 * Index action - displays support tickets
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Soporte',
			'breadcrumb' => array(
				'Dashboard' => 'clients',
				'Soporte' => 'clients/support',
			),
			'tickets' => array(),
		);

		return \Response::forge(\View::forge('erp_clients/support/index', $data, false));
	}

	/**
	 * Create action - create new support ticket
	 *
	 * @return \Response
	 */
	public function action_agregar()
	{
		$data = array(
			'module_name' => 'Nuevo Ticket',
			'breadcrumb' => array(
				'Dashboard' => 'clients',
				'Soporte' => 'clients/support',
				'Nuevo' => 'clients/support/agregar',
			),
		);

		return \Response::forge(\View::forge('erp_clients/support/agregar', $data, false));
	}
}
