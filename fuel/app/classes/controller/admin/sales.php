<?php

/**
 * CONTROLLER ADMIN SALES
 * 
 * Gestión del módulo de Ventas
 * - Lista de ventas
 * - Nueva venta
 * - Detalle de venta
 * - Reportes
 * 
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Sales extends Controller_Admin
{
	/**
	 * INDEX - LISTA DE VENTAS
	 */
	public function action_index()
	{
		// Verificar permisos
		if (!Helper_Permission::can('sales', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver ventas');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$user_id = Auth::get('id');

		// Obtener ventas recientes
		$sales = DB::select('s.*')
			->from(['sales', 's'])
			->where('s.tenant_id', $tenant_id)
			->order_by('s.id', 'DESC')
			->limit(50)
			->execute()
			->as_array();

		// Calcular totales
		$stats = DB::select(
				DB::expr('COUNT(*) as total_sales'),
				DB::expr('SUM(total) as total_amount'),
				DB::expr('SUM(total - discount) as completed_amount')
			)
			->from('sales')
			->where('tenant_id', $tenant_id)
			->execute()
			->current();

		$data = [
			'title' => 'Ventas',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'sales' => $sales,
			'stats' => $stats,
			'can_create' => Helper_Permission::can('sales', 'create'),
			'can_edit' => Helper_Permission::can('sales', 'edit'),
			'can_delete' => Helper_Permission::can('sales', 'delete')
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/sales/index', $data);
	}

	/**
	 * NEW - NUEVA VENTA
	 */
	public function action_new()
	{
		// Verificar permisos
		if (!Helper_Permission::can('sales', 'create'))
		{
			Session::set_flash('error', 'No tienes permisos para crear ventas');
			Response::redirect('admin/sales');
		}

		$tenant_id = Session::get('tenant_id', 1);

		$data = [
			'title' => 'Nueva Venta',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin()
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/sales/new', $data);
	}

	/**
	 * VIEW - DETALLE DE VENTA
	 */
	public function action_view($id = null)
	{
		if (!$id)
		{
			Session::set_flash('error', 'ID de venta requerido');
			Response::redirect('admin/sales');
		}

		// Verificar permisos
		if (!Helper_Permission::can('sales', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver ventas');
			Response::redirect('admin/sales');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener venta con items
		$sale = DB::select('s.*', 'u.username')
			->from(['sales', 's'])
			->join(['users', 'u'], 'LEFT')
			->on('s.user_id', '=', 'u.id')
			->where('s.id', $id)
			->where('s.tenant_id', $tenant_id)
			->execute()
			->current();

		if (!$sale)
		{
			Session::set_flash('error', 'Venta no encontrada');
			Response::redirect('admin/sales');
		}

		// Obtener items de la venta
		$items = DB::select('si.*', 'p.name as product_name')
			->from(['sales_items', 'si'])
			->join(['products', 'p'], 'LEFT')
			->on('si.product_id', '=', 'p.id')
			->where('si.sale_id', $id)
			->execute()
			->as_array();

		$data = [
			'title' => 'Detalle de Venta #' . $id,
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'sale' => $sale,
			'items' => $items
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/sales/view', $data);
	}
}
