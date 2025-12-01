<?php
/**
 * Store Module - Home Controller
 *
 * @package    Store
 * @version    1.0.0
 */

namespace Store;

class Controller_Home extends \Controller
{
	public function action_index()
	{
		$data = array(
			'page_title' => 'Tienda',
			'featured_products' => array(),
			'categories' => array(),
			'promotions' => array(),
		);

		return \Response::forge(\View::forge('store/home', $data, false));
	}
}
