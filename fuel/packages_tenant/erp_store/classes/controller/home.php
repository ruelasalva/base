<?php
/**
 * ERP Store Module - Home Controller
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Home Controller for the Store Module
 *
 * Provides the main store homepage with featured products,
 * categories, and promotions.
 */
class Controller_Home extends \Controller
{
	/**
	 * Index action - displays the store homepage
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'page_title' => 'Tienda',
			'featured_products' => array(),
			'categories' => array(),
			'promotions' => array(),
		);

		return \Response::forge(\View::forge('erp_store/home', $data, false));
	}
}
