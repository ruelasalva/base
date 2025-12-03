<?php

/**
 * CONTROLLER ADMIN ROLES
 * 
 * Gestión de roles y permisos del sistema
 * - CRUD de roles
 * - Asignación de permisos a roles
 * - Visualización de matriz de permisos
 * 
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Roles extends Controller_Admin
{
	/**
	 * INDEX - LISTA DE ROLES
	 */
	public function action_index()
	{
		// Verificar permisos
		if (!Helper_Permission::can('roles', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver roles');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener todos los roles
		$roles = DB::select('r.*', [DB::expr('COUNT(DISTINCT rp.permission_id)'), 'permission_count'], [DB::expr('COUNT(DISTINCT ur.user_id)'), 'user_count'])
			->from(['roles', 'r'])
			->join(['role_permissions', 'rp'], 'LEFT')
			->on('r.id', '=', 'rp.role_id')
			->join(['user_roles', 'ur'], 'LEFT')
			->on('r.id', '=', 'ur.role_id')
			->and_on('ur.tenant_id', '=', DB::expr($tenant_id))
			->where('r.is_active', 1)
			->group_by('r.id')
			->order_by('r.level', 'DESC')
			->execute()
			->as_array();

		$data = [
			'title' => 'Roles',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'roles' => $roles,
			'can_create' => Helper_Permission::can('roles', 'create'),
			'can_edit' => Helper_Permission::can('roles', 'edit'),
			'can_delete' => Helper_Permission::can('roles', 'delete'),
			'can_permissions' => Helper_Permission::can('roles', 'permissions')
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/roles/index', $data);
	}

	/**
	 * VIEW - VER DETALLE DE UN ROL
	 */
	public function action_view($role_id = null)
	{
		if (!$role_id)
		{
			Session::set_flash('error', 'ID de rol no válido');
			Response::redirect('admin/roles');
		}

		// Verificar permisos
		if (!Helper_Permission::can('roles', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver roles');
			Response::redirect('admin/roles');
		}

		// Obtener rol
		$role = DB::select()->from('roles')->where('id', $role_id)->execute()->current();

		if (!$role)
		{
			Session::set_flash('error', 'Rol no encontrado');
			Response::redirect('admin/roles');
		}

		// Obtener permisos del rol
		$permissions = DB::select('p.*')
			->from(['role_permissions', 'rp'])
			->join(['permissions', 'p'], 'INNER')
			->on('rp.permission_id', '=', 'p.id')
			->where('rp.role_id', $role_id)
			->where('p.is_active', 1)
			->order_by('p.module', 'ASC')
			->order_by('p.action', 'ASC')
			->execute()
			->as_array();

		// Agrupar permisos por módulo
		$permissions_by_module = [];
		foreach ($permissions as $perm)
		{
			$module = $perm['module'];
			if (!isset($permissions_by_module[$module]))
			{
				$permissions_by_module[$module] = [];
			}
			$permissions_by_module[$module][] = $perm;
		}

		// Obtener usuarios con este rol
		$tenant_id = Session::get('tenant_id', 1);
		$users = DB::select('u.id', 'u.username', 'u.email', 'u.first_name', 'u.last_name')
			->from(['user_roles', 'ur'])
			->join(['users', 'u'], 'INNER')
			->on('ur.user_id', '=', 'u.id')
			->where('ur.role_id', $role_id)
			->where('ur.tenant_id', $tenant_id)
			->where('u.is_active', 1)
			->execute()
			->as_array();

		$data = [
			'title' => 'Detalle de Rol: ' . $role['display_name'],
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'role' => $role,
			'permissions_by_module' => $permissions_by_module,
			'users' => $users,
			'can_edit' => Helper_Permission::can('roles', 'edit'),
			'can_permissions' => Helper_Permission::can('roles', 'permissions')
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/roles/view', $data);
	}

	/**
	 * PERMISSIONS - GESTIONAR PERMISOS DE UN ROL
	 */
	public function action_permissions($role_id = null)
	{
		if (!$role_id)
		{
			Session::set_flash('error', 'ID de rol no válido');
			Response::redirect('admin/roles');
		}

		// Verificar permisos
		if (!Helper_Permission::can('roles', 'permissions'))
		{
			Session::set_flash('error', 'No tienes permisos para gestionar permisos de roles');
			Response::redirect('admin/roles');
		}

		// Obtener rol
		$role = DB::select()->from('roles')->where('id', $role_id)->execute()->current();

		if (!$role)
		{
			Session::set_flash('error', 'Rol no encontrado');
			Response::redirect('admin/roles');
		}

		// Si es POST, guardar permisos
		if (Input::method() === 'POST')
		{
			try
			{
				$selected_permissions = Input::post('permissions', []);

				// Eliminar permisos actuales
				DB::delete('role_permissions')->where('role_id', $role_id)->execute();

				// Insertar nuevos permisos
				if (!empty($selected_permissions))
				{
					$inserts = [];
					foreach ($selected_permissions as $perm_id)
					{
						$inserts[] = [
							'role_id' => $role_id,
							'permission_id' => $perm_id,
							'created_at' => time()
						];
					}

					DB::insert('role_permissions')->columns(['role_id', 'permission_id', 'created_at'])->values($inserts)->execute();
				}

				Session::set_flash('success', 'Permisos actualizados correctamente');
				Response::redirect('admin/roles/view/' . $role_id);
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error al actualizar permisos: ' . $e->getMessage());
			}
		}

		// Obtener todos los permisos disponibles agrupados por módulo
		$all_permissions = DB::select()
			->from('permissions')
			->where('is_active', 1)
			->order_by('module', 'ASC')
			->order_by('action', 'ASC')
			->execute()
			->as_array();

		$permissions_by_module = [];
		foreach ($all_permissions as $perm)
		{
			$module = $perm['module'];
			if (!isset($permissions_by_module[$module]))
			{
				$permissions_by_module[$module] = [];
			}
			$permissions_by_module[$module][] = $perm;
		}

		// Obtener permisos actuales del rol
		$current_permissions = DB::select('permission_id')
			->from('role_permissions')
			->where('role_id', $role_id)
			->execute()
			->as_array();

		$current_permission_ids = array_column($current_permissions, 'permission_id');

		$data = [
			'title' => 'Permisos de Rol: ' . $role['display_name'],
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => Session::get('tenant_id', 1),
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'role' => $role,
			'permissions_by_module' => $permissions_by_module,
			'current_permission_ids' => $current_permission_ids
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/roles/permissions', $data);
	}

	/**
	 * NEW - CREAR NUEVO ROL
	 */
	public function action_new()
	{
		// Verificar permisos
		if (!Helper_Permission::can('roles', 'create'))
		{
			Session::set_flash('error', 'No tienes permisos para crear roles');
			Response::redirect('admin/roles');
		}

		if (Input::method() === 'POST')
		{
			try
			{
				$name = trim(Input::post('name'));
				$display_name = trim(Input::post('display_name'));
				$description = trim(Input::post('description'));
				$level = (int)Input::post('level', 1);

				// Validar
				if (empty($name) || empty($display_name))
				{
					throw new Exception('El nombre y nombre para mostrar son requeridos');
				}

				// Verificar que no exista
				$exists = DB::select()->from('roles')->where('name', $name)->execute()->current();
				if ($exists)
				{
					throw new Exception('Ya existe un rol con ese nombre');
				}

				// Insertar
				$result = DB::insert('roles')->set([
					'name' => $name,
					'display_name' => $display_name,
					'description' => $description,
					'level' => $level,
					'is_system' => 0,
					'is_active' => 1,
					'created_at' => time(),
					'updated_at' => time()
				])->execute();

				Session::set_flash('success', 'Rol creado correctamente');
				Response::redirect('admin/roles/view/' . $result[0]);
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error: ' . $e->getMessage());
			}
		}

		$data = [
			'title' => 'Crear Rol',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => Session::get('tenant_id', 1),
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin()
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/roles/new', $data);
	}

	/**
	 * EDIT - EDITAR ROL
	 */
	public function action_edit($role_id = null)
	{
		if (!$role_id)
		{
			Session::set_flash('error', 'ID de rol no válido');
			Response::redirect('admin/roles');
		}

		// Verificar permisos
		if (!Helper_Permission::can('roles', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para editar roles');
			Response::redirect('admin/roles');
		}

		// Obtener rol
		$role = DB::select()->from('roles')->where('id', $role_id)->execute()->current();

		if (!$role)
		{
			Session::set_flash('error', 'Rol no encontrado');
			Response::redirect('admin/roles');
		}

		// No permitir editar roles del sistema
		if ($role['is_system'] && !Helper_Permission::is_super_admin())
		{
			Session::set_flash('error', 'No puedes editar roles del sistema');
			Response::redirect('admin/roles');
		}

		if (Input::method() === 'POST')
		{
			try
			{
				$display_name = trim(Input::post('display_name'));
				$description = trim(Input::post('description'));
				$level = (int)Input::post('level', 1);

				// Validar
				if (empty($display_name))
				{
					throw new Exception('El nombre para mostrar es requerido');
				}

				// Actualizar
				DB::update('roles')->set([
					'display_name' => $display_name,
					'description' => $description,
					'level' => $level,
					'updated_at' => time()
				])->where('id', $role_id)->execute();

				Session::set_flash('success', 'Rol actualizado correctamente');
				Response::redirect('admin/roles/view/' . $role_id);
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error: ' . $e->getMessage());
			}
		}

		$data = [
			'title' => 'Editar Rol: ' . $role['display_name'],
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => Session::get('tenant_id', 1),
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'role' => $role
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/roles/edit', $data);
	}

	/**
	 * DELETE - ELIMINAR ROL (AJAX)
	 */
	public function action_delete($role_id = null)
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
		if (!Helper_Permission::can('roles', 'delete'))
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'No tienes permisos para eliminar roles'
			]), 403, ['Content-Type' => 'application/json']);
		}

		try
		{
			// Obtener rol
			$role = DB::select()->from('roles')->where('id', $role_id)->execute()->current();

			if (!$role)
			{
				throw new Exception('Rol no encontrado');
			}

			// No permitir eliminar roles del sistema
			if ($role['is_system'])
			{
				throw new Exception('No se pueden eliminar roles del sistema');
			}

			// Verificar si hay usuarios con este rol
			$user_count = DB::select(DB::expr('COUNT(*) as count'))
				->from('user_roles')
				->where('role_id', $role_id)
				->execute()
				->current();

			if ($user_count['count'] > 0)
			{
				throw new Exception('No se puede eliminar el rol porque tiene usuarios asignados');
			}

			// Eliminar permisos del rol
			DB::delete('role_permissions')->where('role_id', $role_id)->execute();

			// Marcar como inactivo en lugar de eliminar
			DB::update('roles')->set(['is_active' => 0, 'updated_at' => time()])->where('id', $role_id)->execute();

			return Response::forge(json_encode([
				'success' => true,
				'message' => 'Rol eliminado correctamente'
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
