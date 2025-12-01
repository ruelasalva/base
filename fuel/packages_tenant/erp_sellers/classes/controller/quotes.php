<?php
/**
 * ERP Sellers Module - Quotes Controller
 *
 * @package    ERP_Sellers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Sellers;

/**
 * Quotes Controller for the Sellers Module
 *
 * Provides quote management functionality.
 */
class Controller_Quotes extends \Controller
{
	/**
	 * Index action - displays quotes list
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Cotizaciones',
			'breadcrumb' => array(
				'Dashboard' => 'sellers',
				'Cotizaciones' => 'sellers/quotes',
			),
			'quotes' => array(),
		);

		return \Response::forge(\View::forge('erp_sellers/quotes/index', $data, false));
	}

	/**
	 * Create action - show quote creation form
	 *
	 * @return \Response
	 */
	public function action_agregar()
	{
		$data = array(
			'module_name' => 'Nueva Cotización',
			'breadcrumb' => array(
				'Dashboard' => 'sellers',
				'Cotizaciones' => 'sellers/quotes',
				'Nueva' => 'sellers/quotes/agregar',
			),
		);

		return \Response::forge(\View::forge('erp_sellers/quotes/agregar', $data, false));
	}
}
