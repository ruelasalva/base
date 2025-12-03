<?php

/**
 * CONTROLLER ADMIN CRM
 * 
 * Gestión del módulo de CRM (Customer Relationship Management)
 * - Lista de clientes
 * - Agregar/Editar clientes
 * - Historial de interacciones
 * - Oportunidades de venta
 * 
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Crm extends Controller_Admin
{
	/**
	 * INDEX - LISTA DE CLIENTES
	 */
	public function action_index()
	{
		// Verificar permisos
		if (!Helper_Permission::can('crm', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver CRM');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener clientes
		$customers = DB::select()
			->from('customers')
			->where('tenant_id', $tenant_id)
			->where('deleted_at', null)
			->order_by('company_name', 'ASC')
			->limit(100)
			->execute()
			->as_array();

		// Estadísticas
		$stats = DB::select(
				DB::expr('COUNT(*) as total_customers'),
				DB::expr('COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_customers'),
				DB::expr('COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_this_month')
			)
			->from('customers')
			->where('tenant_id', $tenant_id)
			->where('deleted_at', null)
			->execute()
			->current();

		$data = [
			'title' => 'CRM - Clientes',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'customers' => $customers,
			'stats' => $stats,
			'can_create' => Helper_Permission::can('crm', 'create'),
			'can_edit' => Helper_Permission::can('crm', 'edit'),
			'can_delete' => Helper_Permission::can('crm', 'delete')
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/crm/index', $data);
	}

	/**
	 * NEW - NUEVO CLIENTE
	 */
	public function action_new()
	{
		// Verificar permisos
		if (!Helper_Permission::can('crm', 'create'))
		{
			Session::set_flash('error', 'No tienes permisos para crear clientes');
			Response::redirect('admin/crm');
		}

		$tenant_id = Session::get('tenant_id', 1);

		if (Input::method() == 'POST')
		{
			try
			{
				$result = DB::insert('customers')->set([
					'tenant_id' => $tenant_id,
					'user_id' => Auth::get('id'),
					'code' => 'CLI-' . time(),
					'customer_type' => Input::post('customer_type', 'business'),
					'company_name' => Input::post('company_name'),
					'first_name' => Input::post('first_name'),
					'last_name' => Input::post('last_name'),
					'tax_id' => Input::post('tax_id'),
					'is_active' => 1
				])->execute();

				Session::set_flash('success', 'Cliente creado correctamente');
				Response::redirect('admin/crm');
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error al crear cliente: ' . $e->getMessage());
			}
		}

		$data = [
			'title' => 'Nuevo Cliente',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin()
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/crm/new', $data);
	}

	/**
	 * VIEW - DETALLE DEL CLIENTE
	 */
	public function action_view($id = null)
	{
		if (!$id)
		{
			Session::set_flash('error', 'ID de cliente requerido');
			Response::redirect('admin/crm');
		}

		// Verificar permisos
		if (!Helper_Permission::can('crm', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver clientes');
			Response::redirect('admin/crm');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener cliente
		$customer = DB::select()
			->from('customers')
			->where('id', $id)
			->where('tenant_id', $tenant_id)
			->execute()
			->current();

		if (!$customer)
		{
			Session::set_flash('error', 'Cliente no encontrado');
			Response::redirect('admin/crm');
		}

		// Obtener ventas del cliente
		$sales = DB::select()
			->from('sales')
			->where('customer_id', $id)
			->where('tenant_id', $tenant_id)
			->order_by('sale_date', 'DESC')
			->limit(10)
			->execute()
			->as_array();

		$data = [
			'title' => 'Cliente: ' . $customer['name'],
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'customer' => $customer,
			'sales' => $sales
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/crm/view', $data);
	}
}
