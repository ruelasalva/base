<?php
/**
 * ERP Store Module - Catalog Controller
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Catalog Controller for the Store Module
 *
 * Provides product catalog browsing functionality.
 */
class Controller_Catalog extends \Controller
{
	/**
	 * Index action - displays all products
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'page_title' => 'Catálogo',
			'products' => array(),
			'categories' => array(),
			'filters' => array(),
		);

		return \Response::forge(\View::forge('erp_store/catalog/index', $data, false));
	}

	/**
	 * Category action - displays products in a category
	 *
	 * @param string $category Category slug
	 * @return \Response
	 */
	public function action_categoria($category = null)
	{
		$data = array(
			'page_title' => 'Categoría',
			'category' => $category,
			'products' => array(),
		);

		return \Response::forge(\View::forge('erp_store/catalog/categoria', $data, false));
	}
}
