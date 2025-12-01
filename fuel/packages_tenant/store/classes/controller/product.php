<?php
/**
 * Store Module - Product Controller
 *
 * @package    Store
 * @version    1.0.0
 */

namespace Store;

class Controller_Product extends \Controller
{
	public function action_view($id = null)
	{
		if ($id === null)
		{
			\Response::redirect('tienda/catalogo');
		}
		$data = array('page_title' => 'Producto', 'product_id' => $id, 'product' => null, 'related_products' => array());
		return \Response::forge(\View::forge('store/product/view', $data, false));
	}
}
