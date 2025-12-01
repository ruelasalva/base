<?php
/**
 * ERP Sellers Module - Commissions Controller
 *
 * @package    ERP_Sellers
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Sellers;

/**
 * Commissions Controller for the Sellers Module
 *
 * Provides commission tracking functionality.
 */
class Controller_Commissions extends \Controller
{
	/**
	 * Index action - displays commissions overview
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Mis Comisiones',
			'breadcrumb' => array(
				'Dashboard' => 'sellers',
				'Comisiones' => 'sellers/commissions',
			),
			'commissions' => array(),
			'summary' => array(
				'month' => 0,
				'year' => 0,
				'pending' => 0,
			),
		);

		return \Response::forge(\View::forge('erp_sellers/commissions/index', $data, false));
	}
}
