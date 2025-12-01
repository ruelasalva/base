<?php
/**
 * Store Module - Catalog Controller
 *
 * @package    Store
 * @version    1.0.0
 */

namespace Store;

class Controller_Catalog extends \Controller
{
	public function action_index()
	{
		$data = array('page_title' => 'Catálogo', 'products' => array(), 'categories' => array(), 'filters' => array());
		return \Response::forge(\View::forge('store/catalog/index', $data, false));
	}

	public function action_categoria($category = null)
	{
		$data = array('page_title' => 'Categoría', 'category' => $category, 'products' => array());
		return \Response::forge(\View::forge('store/catalog/categoria', $data, false));
	}
}
