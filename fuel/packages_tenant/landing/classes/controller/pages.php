<?php
/**
 * Landing Module - Pages Controller
 *
 * @package    Landing
 * @version    1.0.0
 */

namespace Landing;

class Controller_Pages extends \Controller
{
	public function action_view($slug = null)
	{
		if ($slug === null)
		{
			\Response::redirect('landing');
		}
		$data = array('page_title' => 'Página', 'slug' => $slug, 'content' => '');
		return \Response::forge(\View::forge('landing/pages/view', $data, false));
	}
}
