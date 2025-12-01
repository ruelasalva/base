<?php
/**
 * ERP Provider Module - Products Controller
 *
 * @package    ERP_Provider
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Provider;

/**
 * Products Controller for the Provider Module
 *
 * Provides product management functionality for providers.
 */
class Controller_Products extends \Controller
{
	/**
	 * Index action - displays product list
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Mis Productos',
			'breadcrumb' => array(
				'Dashboard' => 'provider',
				'Productos' => 'provider/products',
			),
			'products' => array(),
		);

		return \Response::forge(\View::forge('erp_provider/products/index', $data, false));
	}

	/**
	 * Create action - show product creation form
	 *
	 * @return \Response
	 */
	public function action_agregar()
	{
		$data = array(
			'module_name' => 'Agregar Producto',
			'breadcrumb' => array(
				'Dashboard' => 'provider',
				'Productos' => 'provider/products',
				'Agregar' => 'provider/products/agregar',
			),
		);

		return \Response::forge(\View::forge('erp_provider/products/agregar', $data, false));
	}
}
