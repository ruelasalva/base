<?php
/**
 * Landing Module - About Controller
 *
 * @package    Landing
 * @version    1.0.0
 */

namespace Landing;

class Controller_About extends \Controller
{
	public function action_index()
	{
		$data = array(
			'page_title' => 'Nosotros',
			'company_name' => 'ERP Solutions',
			'description' => 'Somos una empresa dedicada a proveer soluciones empresariales.',
			'team' => array(),
		);

		return \Response::forge(\View::forge('landing/about', $data, false));
	}
}
