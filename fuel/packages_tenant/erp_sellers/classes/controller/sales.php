<?php
/**
 * ERP Sellers Module - Sales Controller
 *
 * @package    ERP_Sellers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Sellers;

/**
 * Sales Controller for the Sellers Module
 *
 * Provides sales management functionality.
 */
class Controller_Sales extends \Controller
{
	/**
	 * Index action - displays sales list
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Mis Ventas',
			'breadcrumb' => array(
				'Dashboard' => 'sellers',
				'Ventas' => 'sellers/sales',
			),
			'sales' => array(),
		);

		return \Response::forge(\View::forge('erp_sellers/sales/index', $data, false));
	}

	/**
	 * Create action - show new sale form
	 *
	 * @return \Response
	 */
	public function action_agregar()
	{
		$data = array(
			'module_name' => 'Nueva Venta',
			'breadcrumb' => array(
				'Dashboard' => 'sellers',
				'Ventas' => 'sellers/sales',
				'Nueva' => 'sellers/sales/agregar',
			),
		);

		return \Response::forge(\View::forge('erp_sellers/sales/agregar', $data, false));
	}
}
