<?php

/**
 * CONTROLLER ADMIN PERMISSIONS
 * 
 * Gestión de permisos del sistema
 * - CRUD de permisos
 * - Visualización por módulo
 * - Asignación a roles
 * 
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Permissions extends Controller_Admin
{
	/**
	 * INDEX - LISTA DE PERMISOS
	 */
	public function action_index()
	{
		// Verificar permisos
		if (!Helper_Permission::can('permissions', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver permisos');
			Response::redirect('admin');
		}

		// Obtener todos los permisos agrupados por módulo
		$all_permissions = DB::select()
			->from('permissions')
			->order_by('module', 'ASC')
			->order_by('action', 'ASC')
			->execute()
			->as_array();

		$permissions_by_module = [];
		$stats = ['total' => 0, 'active' => 0, 'modules' => 0];

		foreach ($all_permissions as $perm)
		{
			$module = $perm['module'];
			if (!isset($permissions_by_module[$module]))
			{
				$permissions_by_module[$module] = [];
			}
			$permissions_by_module[$module][] = $perm;
			
			$stats['total']++;
			if ($perm['is_active']) $stats['active']++;
		}

		$stats['modules'] = count($permissions_by_module);

		$data = [
			'title' => 'Permisos del Sistema',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => Session::get('tenant_id', 1),
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'permissions_by_module' => $permissions_by_module,
			'stats' => $stats,
			'can_create' => Helper_Permission::can('permissions', 'create'),
			'can_edit' => Helper_Permission::can('permissions', 'edit'),
			'can_delete' => Helper_Permission::can('permissions', 'delete')
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/permissions/index', $data);
	}

	/**
	 * VIEW - VER DETALLE DE UN PERMISO
	 */
	public function action_view($id = null)
	{
		if (!$id)
		{
			Session::set_flash('error', 'ID de permiso no válido');
			Response::redirect('admin/permissions');
		}

		// Verificar permisos
		if (!Helper_Permission::can('permissions', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver permisos');
			Response::redirect('admin/permissions');
		}

		// Obtener permiso
		$permission = DB::select()->from('permissions')->where('id', $id)->execute()->current();

		if (!$permission)
		{
			Session::set_flash('error', 'Permiso no encontrado');
			Response::redirect('admin/permissions');
		}

		// Obtener roles que tienen este permiso
		$roles = DB::select('r.*')
			->from(['role_permissions', 'rp'])
			->join(['roles', 'r'], 'INNER')
			->on('rp.role_id', '=', 'r.id')
			->where('rp.permission_id', $id)
			->where('r.is_active', 1)
			->order_by('r.level', 'DESC')
			->execute()
			->as_array();

		// Contar usuarios afectados (tienen roles con este permiso)
		$user_count = 0;
		$tenant_id = Session::get('tenant_id', 1);
		
		foreach ($roles as $role)
		{
			$count = DB::select(DB::expr('COUNT(DISTINCT user_id) as total'))
				->from('user_roles')
				->where('role_id', $role['id'])
				->where('tenant_id', $tenant_id)
				->execute()
				->current();
			
			$user_count += $count['total'];
		}

		$data = [
			'title' => 'Detalle de Permiso: ' . $permission['name'],
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'permission' => $permission,
			'roles' => $roles,
			'user_count' => $user_count,
			'can_edit' => Helper_Permission::can('permissions', 'edit'),
			'can_delete' => Helper_Permission::can('permissions', 'delete')
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/permissions/view', $data);
	}

	/**
	 * NEW - CREAR NUEVO PERMISO
	 */
	public function action_new()
	{
		// Verificar permisos
		if (!Helper_Permission::can('permissions', 'create'))
		{
			Session::set_flash('error', 'No tienes permisos para crear permisos');
			Response::redirect('admin/permissions');
		}

		if (Input::method() === 'POST')
		{
			try
			{
				$module = trim(Input::post('module'));
				$action = trim(Input::post('action'));
				$name = trim(Input::post('name'));
				$description = trim(Input::post('description'));

				// Validar
				if (empty($module) || empty($action) || empty($name))
				{
					throw new Exception('Módulo, acción y nombre son requeridos');
				}

				// Verificar que no exista
				$exists = DB::select()
					->from('permissions')
					->where('module', $module)
					->where('action', $action)
					->execute()
					->current();

				if ($exists)
				{
					throw new Exception('Ya existe un permiso con ese módulo y acción');
				}

				// Insertar
				$result = DB::insert('permissions')->set([
					'module' => $module,
					'action' => $action,
					'name' => $name,
					'description' => $description,
					'is_active' => 1,
					'created_at' => time(),
					'updated_at' => time()
				])->execute();

				Session::set_flash('success', 'Permiso creado correctamente');
				Response::redirect('admin/permissions/view/' . $result[0]);
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error: ' . $e->getMessage());
			}
		}

		// Obtener módulos existentes para sugerencias
		$modules = DB::select(DB::expr('DISTINCT module'))
			->from('permissions')
			->order_by('module', 'ASC')
			->execute()
			->as_array();

		$data = [
			'title' => 'Crear Permiso',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => Session::get('tenant_id', 1),
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'existing_modules' => array_column($modules, 'module')
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/permissions/new', $data);
	}

	/**
	 * EDIT - EDITAR PERMISO
	 */
	public function action_edit($id = null)
	{
		if (!$id)
		{
			Session::set_flash('error', 'ID de permiso no válido');
			Response::redirect('admin/permissions');
		}

		// Verificar permisos
		if (!Helper_Permission::can('permissions', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para editar permisos');
			Response::redirect('admin/permissions');
		}

		// Obtener permiso
		$permission = DB::select()->from('permissions')->where('id', $id)->execute()->current();

		if (!$permission)
		{
			Session::set_flash('error', 'Permiso no encontrado');
			Response::redirect('admin/permissions');
		}

		if (Input::method() === 'POST')
		{
			try
			{
				$name = trim(Input::post('name'));
				$description = trim(Input::post('description'));
				$is_active = Input::post('is_active', 0);

				// Validar
				if (empty($name))
				{
					throw new Exception('El nombre es requerido');
				}

				// Actualizar
				DB::update('permissions')->set([
					'name' => $name,
					'description' => $description,
					'is_active' => $is_active,
					'updated_at' => time()
				])->where('id', $id)->execute();

				Session::set_flash('success', 'Permiso actualizado correctamente');
				Response::redirect('admin/permissions/view/' . $id);
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error: ' . $e->getMessage());
			}
		}

		$data = [
			'title' => 'Editar Permiso: ' . $permission['name'],
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => Session::get('tenant_id', 1),
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'permission' => $permission
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/permissions/edit', $data);
	}

	/**
	 * DELETE - ELIMINAR PERMISO (AJAX)
	 */
	public function action_delete($id = null)
	{
		// Solo aceptar POST
		if (Input::method() !== 'POST')
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'Método no permitido'
			]), 405, ['Content-Type' => 'application/json']);
		}

		// Verificar permisos
		if (!Helper_Permission::can('permissions', 'delete'))
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'No tienes permisos para eliminar permisos'
			]), 403, ['Content-Type' => 'application/json']);
		}

		try
		{
			// Verificar si el permiso está en uso
			$in_use = DB::select()
				->from('role_permissions')
				->where('permission_id', $id)
				->execute()
				->current();

			if ($in_use)
			{
				throw new Exception('No se puede eliminar. Este permiso está asignado a uno o más roles');
			}

			// Eliminar
			DB::delete('permissions')->where('id', $id)->execute();

			return Response::forge(json_encode([
				'success' => true,
				'message' => 'Permiso eliminado correctamente'
			]), 200, ['Content-Type' => 'application/json']);
		}
		catch (Exception $e)
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => $e->getMessage()
			]), 400, ['Content-Type' => 'application/json']);
		}
	}

	/**
	 * TOGGLE - ACTIVAR/DESACTIVAR PERMISO (AJAX)
	 */
	public function action_toggle($id = null)
	{
		// Solo aceptar POST
		if (Input::method() !== 'POST')
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'Método no permitido'
			]), 405, ['Content-Type' => 'application/json']);
		}

		// Verificar permisos
		if (!Helper_Permission::can('permissions', 'edit'))
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'No tienes permisos para modificar permisos'
			]), 403, ['Content-Type' => 'application/json']);
		}

		try
		{
			$permission = DB::select()->from('permissions')->where('id', $id)->execute()->current();

			if (!$permission)
			{
				throw new Exception('Permiso no encontrado');
			}

			$new_status = $permission['is_active'] ? 0 : 1;

			DB::update('permissions')
				->set(['is_active' => $new_status, 'updated_at' => time()])
				->where('id', $id)
				->execute();

			return Response::forge(json_encode([
				'success' => true,
				'message' => $new_status ? 'Permiso activado' : 'Permiso desactivado',
				'new_status' => $new_status
			]), 200, ['Content-Type' => 'application/json']);
		}
		catch (Exception $e)
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => $e->getMessage()
			]), 400, ['Content-Type' => 'application/json']);
		}
	}
}
