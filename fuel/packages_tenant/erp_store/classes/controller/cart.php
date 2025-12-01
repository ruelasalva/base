<?php
/**
 * ERP Store Module - Cart Controller
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Cart Controller for the Store Module
 *
 * Provides shopping cart functionality.
 */
class Controller_Cart extends \Controller
{
	/**
	 * Index action - displays cart
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'page_title' => 'Carrito de Compras',
			'items' => array(),
			'subtotal' => 0,
			'total' => 0,
		);

		return \Response::forge(\View::forge('erp_store/cart/index', $data, false));
	}

	/**
	 * Add action - add product to cart
	 *
	 * @param int $product_id Product ID
	 * @return void
	 */
	public function action_agregar($product_id = null)
	{
		if ($product_id === null)
		{
			\Session::set_flash('error', 'Producto no válido.');
			\Response::redirect('tienda/catalogo');
		}

		// Add to cart logic here
		\Session::set_flash('success', 'Producto agregado al carrito.');
		\Response::redirect('tienda/carrito');
	}

	/**
	 * Remove action - remove product from cart
	 *
	 * @param int $item_id Cart item ID
	 * @return void
	 */
	public function action_eliminar($item_id = null)
	{
		if ($item_id === null)
		{
			\Session::set_flash('error', 'Producto no válido.');
			\Response::redirect('tienda/carrito');
		}

		// Remove from cart logic here
		\Session::set_flash('success', 'Producto eliminado del carrito.');
		\Response::redirect('tienda/carrito');
	}
}
