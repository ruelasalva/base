<?php
/**
 * ERP Store Module - Product Controller
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Product Controller for the Store Module
 *
 * Provides product detail view functionality.
 */
class Controller_Product extends \Controller
{
	/**
	 * View action - displays product details
	 *
	 * @param int $id Product ID
	 * @return \Response
	 */
	public function action_view($id = null)
	{
		if ($id === null)
		{
			\Response::redirect('tienda/catalogo');
		}

		$data = array(
			'page_title' => 'Producto',
			'product_id' => $id,
			'product' => null,
			'related_products' => array(),
		);

		return \Response::forge(\View::forge('erp_store/product/view', $data, false));
	}
}
