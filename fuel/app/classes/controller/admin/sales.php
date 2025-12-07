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

		// Obtener ventas recientes SIN usar ORM para evitar el error de employee_id
		$sales = DB::select('s.*', 'c.first_name', 'c.last_name', 'c.company_name', 'c.customer_type')
			->from(['sales', 's'])
			->join(['customers', 'c'], 'LEFT')
			->on('s.customer_id', '=', 'c.id')
			->where('s.tenant_id', $tenant_id)
			->order_by('s.sale_date', 'DESC')
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

	/**
	 * EDIT - EDITAR VENTA
	 */
	public function action_edit($id = null)
	{
		if (!$id)
		{
			Session::set_flash('error', 'ID de venta requerido');
			Response::redirect('admin/sales');
		}

		// Verificar permisos
		if (!Helper_Permission::can('sales', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para editar ventas');
			Response::redirect('admin/sales');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$sale = Model_Sale::find($id);

		if (!$sale)
		{
			Session::set_flash('error', 'Venta no encontrada');
			Response::redirect('admin/sales');
		}

		// Verificar si puede editarse
		if (!$sale->can_edit())
		{
			Session::set_flash('error', 'Esta venta no puede ser editada en su estado actual');
			Response::redirect('admin/sales/view/' . $id);
		}

		if (Input::method() == 'POST')
		{
			try
			{
				$old_status = $sale->status;
				$old_total = $sale->total;

				// Actualizar campos
				$sale->status = Input::post('status', $sale->status);
				$sale->total = Input::post('total', $sale->total);
				$sale->discount = Input::post('discount', $sale->discount);
				
				if ($sale->save())
				{
					// Registrar log
					$sale->log_change(
						'edit',
						'Venta editada por ' . Auth::get('username'),
						['status' => $old_status, 'total' => $old_total],
						['status' => $sale->status, 'total' => $sale->total]
					);

					Session::set_flash('success', 'Venta actualizada exitosamente');
					Response::redirect('admin/sales/view/' . $id);
				}
			}
			catch (Exception $e)
			{
				\Log::error('Error al editar venta: ' . $e->getMessage());
				Session::set_flash('error', 'Error al actualizar la venta: ' . $e->getMessage());
			}
		}

		$data = [
			'title' => 'Editar Venta #' . $id,
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'sale' => $sale
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/sales/edit', $data);
	}

	/**
	 * DELETE - CANCELAR VENTA
	 */
	public function action_delete($id = null)
	{
		if (!$id)
		{
			Session::set_flash('error', 'ID de venta requerido');
			Response::redirect('admin/sales');
		}

		// Verificar permisos
		if (!Helper_Permission::can('sales', 'delete'))
		{
			Session::set_flash('error', 'No tienes permisos para cancelar ventas');
			Response::redirect('admin/sales');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$sale = Model_Sale::find($id);

		if (!$sale)
		{
			Session::set_flash('error', 'Venta no encontrada');
			Response::redirect('admin/sales');
		}

		// Verificar si puede cancelarse
		if (!$sale->can_cancel())
		{
			Session::set_flash('error', 'Esta venta no puede ser cancelada');
			Response::redirect('admin/sales/view/' . $id);
		}

		try
		{
			$old_status = $sale->status;
			$sale->status = -1; // Cancelada

			if ($sale->save())
			{
				// Registrar log
				$sale->log_change(
					'cancel',
					'Venta cancelada por ' . Auth::get('username'),
					['status' => $old_status],
					['status' => -1]
				);

				Session::set_flash('success', 'Venta cancelada exitosamente');
			}
		}
		catch (Exception $e)
		{
			\Log::error('Error al cancelar venta: ' . $e->getMessage());
			Session::set_flash('error', 'Error al cancelar la venta: ' . $e->getMessage());
		}

		Response::redirect('admin/sales');
	}

	/**
	 * STATS - ESTADÍSTICAS DE VENTAS
	 */
	public function action_stats()
	{
		// Verificar permisos
		if (!Helper_Permission::can('sales', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver estadísticas');
			Response::redirect('admin/sales');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Estadísticas generales
		$stats = DB::select(
				DB::expr('COUNT(*) as total_sales'),
				DB::expr('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as paid_sales'),
				DB::expr('SUM(CASE WHEN status >= 1 THEN total ELSE 0 END) as total_revenue'),
				DB::expr('AVG(CASE WHEN status >= 1 THEN total ELSE 0 END) as avg_ticket'),
				DB::expr('SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as abandoned_carts')
			)
			->from('sales')
			->where('tenant_id', $tenant_id)
			->execute()
			->current();

		// Ventas por mes (últimos 12 meses)
		$monthly_sales = DB::select(
				DB::expr('DATE_FORMAT(FROM_UNIXTIME(sale_date), "%Y-%m") as month'),
				DB::expr('COUNT(*) as sales_count'),
				DB::expr('SUM(total) as total_amount')
			)
			->from('sales')
			->where('tenant_id', $tenant_id)
			->where('status', '>=', 1)
			->where('sale_date', '>', time() - (365 * 24 * 60 * 60))
			->group_by('month')
			->order_by('month', 'DESC')
			->execute()
			->as_array();

		// Top productos vendidos
		$top_products = DB::select(
				'p.name',
				DB::expr('SUM(sp.quantity) as total_quantity'),
				DB::expr('SUM(sp.total) as total_amount')
			)
			->from(['sales_products', 'sp'])
			->join(['products', 'p'], 'INNER')
			->on('sp.product_id', '=', 'p.id')
			->join(['sales', 's'], 'INNER')
			->on('sp.sale_id', '=', 's.id')
			->where('s.tenant_id', $tenant_id)
			->where('s.status', '>=', 1)
			->group_by('p.id')
			->order_by('total_quantity', 'DESC')
			->limit(10)
			->execute()
			->as_array();

		$data = [
			'title' => 'Estadísticas de Ventas',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'stats' => $stats,
			'monthly_sales' => $monthly_sales,
			'top_products' => $top_products
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/sales/stats', $data);
	}
}

