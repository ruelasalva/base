<?php
/**
 * ERP Landing Module - About Controller
 *
 * @package    ERP_Landing
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Landing;

/**
 * About Controller for the Landing Module
 *
 * Provides the about us page.
 */
class Controller_About extends \Controller
{
	/**
	 * Index action - displays the about page
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'page_title' => 'Nosotros',
			'company_name' => 'ERP Solutions',
			'description' => 'Somos una empresa dedicada a proveer soluciones empresariales.',
			'team' => array(),
		);

		return \Response::forge(\View::forge('erp_landing/about', $data, false));
	}
}
