<?php

/**
 * CONTROLLER ADMIN INVENTORY
 * 
 * Gestión de Inventario - Vista de Stock y Movimientos
 * - Vista consolidada de stock por producto
 * - Acceso rápido a movimientos de inventario
 * - Alertas de stock bajo
 * - Enlace al módulo de productos
 * 
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Inventory extends Controller_Admin
{
	/**
	 * INDEX - VISTA DE INVENTARIO CON ACCIONES
	 */
	public function action_index()
	{
		// Verificar permisos
		if (!Helper_Permission::can('inventory', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver inventario');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener productos con stock
		$products = DB::select()
			->from('products')
			->where('tenant_id', $tenant_id)
			->where('deleted_at', null)
			->order_by('name', 'ASC')
			->execute()
			->as_array();

		// Calcular estadísticas
		$stats = [
			'total_products' => count($products),
			'total_value' => array_sum(array_map(function($p) { 
				return $p['sale_price'] * $p['stock_quantity']; 
			}, $products)),
			'low_stock' => count(array_filter($products, function($p) { 
				return $p['min_stock'] > 0 && $p['stock_quantity'] <= $p['min_stock']; 
			})),
			'out_of_stock' => count(array_filter($products, function($p) { 
				return $p['stock_quantity'] <= 0; 
			}))
		];

		$data = [
			'title' => 'Inventario',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'products' => $products,
			'stats' => $stats,
			'can_create' => Helper_Permission::can('inventory', 'create'),
			'can_edit' => Helper_Permission::can('inventory', 'edit'),
			'can_delete' => Helper_Permission::can('inventory', 'delete')
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/inventory/index', $data);
	}

	/**
	 * Redirigir a módulo de productos para agregar
	 */
	public function action_new()
	{
		Response::redirect('admin/productos/create');
	}
}
