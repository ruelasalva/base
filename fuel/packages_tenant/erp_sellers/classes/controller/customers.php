<?php
/**
 * ERP Sellers Module - Customers Controller
 *
 * @package    ERP_Sellers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Sellers;

/**
 * Customers Controller for the Sellers Module
 *
 * Provides customer management functionality for sellers.
 */
class Controller_Customers extends \Controller
{
	/**
	 * Index action - displays customer list
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Mis Clientes',
			'breadcrumb' => array(
				'Dashboard' => 'sellers',
				'Clientes' => 'sellers/customers',
			),
			'customers' => array(),
		);

		return \Response::forge(\View::forge('erp_sellers/customers/index', $data, false));
	}

	/**
	 * Create action - show customer creation form
	 *
	 * @return \Response
	 */
	public function action_agregar()
	{
		$data = array(
			'module_name' => 'Agregar Cliente',
			'breadcrumb' => array(
				'Dashboard' => 'sellers',
				'Clientes' => 'sellers/customers',
				'Agregar' => 'sellers/customers/agregar',
			),
		);

		return \Response::forge(\View::forge('erp_sellers/customers/agregar', $data, false));
	}
}
