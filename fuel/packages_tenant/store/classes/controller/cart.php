<?php
/**
 * Store Module - Cart Controller
 *
 * @package    Store
 * @version    1.0.0
 */

namespace Store;

class Controller_Cart extends \Controller
{
	public function action_index()
	{
		$data = array('page_title' => 'Carrito de Compras', 'items' => array(), 'subtotal' => 0, 'total' => 0);
		return \Response::forge(\View::forge('store/cart/index', $data, false));
	}

	public function action_agregar($product_id = null)
	{
		if ($product_id === null)
		{
			\Session::set_flash('error', 'Producto no válido.');
			\Response::redirect('tienda/catalogo');
		}
		\Session::set_flash('success', 'Producto agregado al carrito.');
		\Response::redirect('tienda/carrito');
	}

	public function action_eliminar($item_id = null)
	{
		if ($item_id === null)
		{
			\Session::set_flash('error', 'Producto no válido.');
			\Response::redirect('tienda/carrito');
		}
		\Session::set_flash('success', 'Producto eliminado del carrito.');
		\Response::redirect('tienda/carrito');
	}
}
