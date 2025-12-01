<?php
/**
 * ERP Landing Module - Pages Controller
 *
 * @package    ERP_Landing
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Landing;

/**
 * Pages Controller for the Landing Module
 *
 * Provides dynamic page view functionality.
 */
class Controller_Pages extends \Controller
{
	/**
	 * View action - displays a page by slug
	 *
	 * @param string $slug Page slug
	 * @return \Response
	 */
	public function action_view($slug = null)
	{
		if ($slug === null)
		{
			\Response::redirect('landing');
		}

		$data = array(
			'page_title' => 'Página',
			'slug' => $slug,
			'content' => '',
		);

		return \Response::forge(\View::forge('erp_landing/pages/view', $data, false));
	}
}
