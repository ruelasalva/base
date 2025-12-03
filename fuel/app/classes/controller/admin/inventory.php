<?php

/**
 * CONTROLLER ADMIN INVENTORY
 * 
 * Gestión del módulo de Inventario
 * - Lista de productos
 * - Agregar/Editar productos
 * - Control de stock
 * - Movimientos de inventario
 * 
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Inventory extends Controller_Admin
{
	/**
	 * INDEX - LISTA DE PRODUCTOS
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

		// Obtener productos
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
				return $p['stock_quantity'] <= $p['min_stock']; 
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
	 * NEW - NUEVO PRODUCTO
	 */
	public function action_new()
	{
		// Verificar permisos
		if (!Helper_Permission::can('inventory', 'create'))
		{
			Session::set_flash('error', 'No tienes permisos para crear productos');
			Response::redirect('admin/inventory');
		}

		$tenant_id = Session::get('tenant_id', 1);

		if (Input::method() == 'POST')
		{
			try
			{
				$name = Input::post('name');
				$slug = \Inflector::friendly_title($name, '-', true);
				
				$result = DB::insert('products')->set([
					'tenant_id' => $tenant_id,
					'sku' => Input::post('sku', 'PROD-' . time()),
					'name' => $name,
					'slug' => $slug,
					'description' => Input::post('description'),
					'sale_price' => Input::post('sale_price', 0),
					'cost_price' => Input::post('cost_price', 0),
					'stock_quantity' => Input::post('stock_quantity', 0),
					'min_stock' => Input::post('min_stock', 0),
					'category_id' => Input::post('category_id'),
					'is_active' => Input::post('is_active', 1)
				])->execute();

				Session::set_flash('success', 'Producto creado correctamente');
				Response::redirect('admin/inventory');
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error al crear producto: ' . $e->getMessage());
			}
		}

		$data = [
			'title' => 'Nuevo Producto',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin()
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/inventory/new', $data);
	}
}
