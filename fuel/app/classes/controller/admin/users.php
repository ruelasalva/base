<?php

/**
 * CONTROLLER ADMIN USERS
 * 
 * Gestión de usuarios del sistema
 * - Lista de usuarios
 * - Crear/Editar usuarios
 * - Asignar roles
 * - Gestión de permisos
 * 
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Users extends Controller_Admin
{
	/**
	 * INDEX - LISTA DE USUARIOS
	 */
	public function action_index()
	{
		// Verificar permisos
		if (!Helper_Permission::can('users', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver usuarios');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener todos los usuarios
		$users = DB::select('u.*')
			->from(['users', 'u'])
			->where('u.deleted_at', null)
			->order_by('u.username', 'ASC')
			->execute()
			->as_array();

		// Obtener roles de cada usuario para este tenant
		foreach ($users as &$user) {
			$roles = DB::select('r.display_name', 'r.name', 'r.level')
				->from(['user_roles', 'ur'])
				->join(['roles', 'r'], 'INNER')
				->on('ur.role_id', '=', 'r.id')
				->where('ur.user_id', '=', $user['id'])
				->where('ur.tenant_id', '=', $tenant_id)
				->where('r.is_active', '=', 1)
				->order_by('r.level', 'DESC')
				->execute()
				->as_array();

			$user['roles'] = $roles;
		}

		// Estadísticas
		$stats = [
			'total_users' => count($users),
			'active_users' => count(array_filter($users, function($u) { 
				return $u['is_active'] == 1;
			})),
			'admin_users' => count(array_filter($users, function($u) { 
				return !empty(array_filter($u['roles'], function($r) {
					return in_array($r['name'], ['super_admin', 'admin']);
				}));
			}))
		];

		$data = [
			'title' => 'Usuarios',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'users' => $users,
			'stats' => $stats,
			'can_create' => Helper_Permission::can('users', 'create'),
			'can_edit' => Helper_Permission::can('users', 'edit'),
			'can_delete' => Helper_Permission::can('users', 'delete'),
			'can_roles' => Helper_Permission::can('users', 'roles')
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/users/index', $data);
	}

	/**
	 * NEW - NUEVO USUARIO
	 */
	public function action_new()
	{
		// Verificar permisos
		if (!Helper_Permission::can('users', 'create'))
		{
			Session::set_flash('error', 'No tienes permisos para crear usuarios');
			Response::redirect('admin/users');
		}

		if (Input::method() == 'POST')
		{
			try
			{
				$username = trim(Input::post('username'));
				$email = trim(Input::post('email'));
				$password = Input::post('password');
				$first_name = trim(Input::post('first_name'));
				$last_name = trim(Input::post('last_name'));
				$role_ids = Input::post('roles', []);

				// Validaciones
				if (empty($username) || empty($email) || empty($password))
				{
					throw new Exception('Usuario, email y contraseña son requeridos');
				}

				// Verificar que no exista el usuario
				$exists = DB::select()->from('users')->where('username', $username)->or_where('email', $email)->execute()->current();
				if ($exists)
				{
					throw new Exception('Ya existe un usuario con ese nombre de usuario o email');
				}

				// Crear usuario
				$user_id = DB::insert('users')->set([
					'username' => $username,
					'email' => $email,
					'password' => Auth::hash_password($password),
					'first_name' => $first_name,
					'last_name' => $last_name,
					'group_id' => 10, // Default group
					'is_active' => 1,
					'is_verified' => 1,
					'created_at' => date('Y-m-d H:i:s')
				])->execute();

				// Asignar roles
				$tenant_id = Session::get('tenant_id', 1);
				if (!empty($role_ids))
				{
					foreach ($role_ids as $role_id)
					{
						DB::insert('user_roles')->set([
							'user_id' => $user_id[0],
							'role_id' => $role_id,
							'tenant_id' => $tenant_id,
							'created_at' => time()
						])->execute();
					}
				}

				Session::set_flash('success', 'Usuario creado correctamente');
				Response::redirect('admin/users');
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error: ' . $e->getMessage());
			}
		}

		// Obtener roles disponibles
		$roles = DB::select()->from('roles')->where('is_active', 1)->order_by('level', 'DESC')->execute()->as_array();

		$data = [
			'title' => 'Nuevo Usuario',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => Session::get('tenant_id', 1),
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'roles' => $roles
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/users/new', $data);
	}

	/**
	 * EDIT - EDITAR USUARIO
	 */
	public function action_edit($id = null)
	{
		if (!$id)
		{
			Session::set_flash('error', 'ID de usuario requerido');
			Response::redirect('admin/users');
		}

		// Verificar permisos
		if (!Helper_Permission::can('users', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para editar usuarios');
			Response::redirect('admin/users');
		}

		// Obtener usuario
		$user = DB::select()->from('users')->where('id', $id)->where('deleted_at', null)->execute()->current();

		if (!$user)
		{
			Session::set_flash('error', 'Usuario no encontrado');
			Response::redirect('admin/users');
		}

		if (Input::method() == 'POST')
		{
			try
			{
				$update_data = [
					'username' => trim(Input::post('username')),
					'email' => trim(Input::post('email')),
					'first_name' => trim(Input::post('first_name')),
					'last_name' => trim(Input::post('last_name')),
					'is_active' => Input::post('is_active', 1),
					'updated_at' => date('Y-m-d H:i:s')
				];

				// Si se proporciona nueva contraseña
				if (Input::post('password'))
				{
					$update_data['password'] = Auth::hash_password(Input::post('password'));
				}

				DB::update('users')
					->set($update_data)
					->where('id', $id)
					->execute();

				// Actualizar roles
				$role_ids = Input::post('roles', []);
				$tenant_id = Session::get('tenant_id', 1);

				// Eliminar roles actuales del tenant
				DB::delete('user_roles')
					->where('user_id', $id)
					->where('tenant_id', $tenant_id)
					->execute();

				// Insertar nuevos roles
				if (!empty($role_ids))
				{
					foreach ($role_ids as $role_id)
					{
						DB::insert('user_roles')->set([
							'user_id' => $id,
							'role_id' => $role_id,
							'tenant_id' => $tenant_id,
							'created_at' => time()
						])->execute();
					}
				}

				Session::set_flash('success', 'Usuario actualizado correctamente');
				Response::redirect('admin/users');
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error: ' . $e->getMessage());
			}
		}

		// Obtener roles disponibles
		$roles = DB::select()->from('roles')->where('is_active', 1)->order_by('level', 'DESC')->execute()->as_array();
		
		// Obtener roles actuales del usuario en este tenant
		$tenant_id = Session::get('tenant_id', 1);
		$user_roles = DB::select('role_id')
			->from('user_roles')
			->where('user_id', $id)
			->where('tenant_id', $tenant_id)
			->execute()
			->as_array();

		$user_role_ids = array_column($user_roles, 'role_id');

		$data = [
			'title' => 'Editar Usuario',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'user' => $user,
			'roles' => $roles,
			'user_role_ids' => $user_role_ids
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/users/edit', $data);
	}

	/**
	 * VIEW - VER DETALLE DE USUARIO
	 */
	public function action_view($id = null)
	{
		if (!$id)
		{
			Session::set_flash('error', 'ID de usuario requerido');
			Response::redirect('admin/users');
		}

		// Verificar permisos
		if (!Helper_Permission::can('users', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver usuarios');
			Response::redirect('admin/users');
		}

		// Obtener usuario
		$user = DB::select()->from('users')->where('id', $id)->where('deleted_at', null)->execute()->current();

		if (!$user)
		{
			Session::set_flash('error', 'Usuario no encontrado');
			Response::redirect('admin/users');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener roles del usuario en este tenant
		$roles = DB::select('r.*')
			->from(['user_roles', 'ur'])
			->join(['roles', 'r'], 'INNER')
			->on('ur.role_id', '=', 'r.id')
			->where('ur.user_id', '=', $id)
			->where('ur.tenant_id', '=', $tenant_id)
			->where('r.is_active', '=', 1)
			->order_by('r.level', 'DESC')
			->execute()
			->as_array();

		// Obtener permisos efectivos del usuario
		$permissions = [];
		foreach ($roles as $role)
		{
			$role_perms = DB::select('p.module', 'p.action', 'p.name')
				->from(['role_permissions', 'rp'])
				->join(['permissions', 'p'], 'INNER')
				->on('rp.permission_id', '=', 'p.id')
				->where('rp.role_id', '=', $role['id'])
				->where('p.is_active', '=', 1)
				->execute()
				->as_array();

			foreach ($role_perms as $perm)
			{
				$key = $perm['module'] . '.' . $perm['action'];
				if (!isset($permissions[$key]))
				{
					$permissions[$key] = $perm;
				}
			}
		}

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

		$data = [
			'title' => 'Detalle de Usuario: ' . $user['username'],
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'user' => $user,
			'roles' => $roles,
			'permissions_by_module' => $permissions_by_module,
			'can_edit' => Helper_Permission::can('users', 'edit'),
			'can_roles' => Helper_Permission::can('users', 'roles')
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/users/view', $data);
	}

	/**
	 * DELETE - ELIMINAR USUARIO (AJAX)
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
		if (!Helper_Permission::can('users', 'delete'))
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'No tienes permisos para eliminar usuarios'
			]), 403, ['Content-Type' => 'application/json']);
		}

		try
		{
			// No permitir eliminar al usuario actual
			if ($id == Auth::get('id'))
			{
				throw new Exception('No puedes eliminar tu propio usuario');
			}

			// Soft delete
			DB::update('users')
				->set(['deleted_at' => date('Y-m-d H:i:s')])
				->where('id', $id)
				->execute();

			return Response::forge(json_encode([
				'success' => true,
				'message' => 'Usuario eliminado correctamente'
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
	 * GESTIONAR TENANTS (BACKENDS) DE UN USUARIO
	 */
	public function action_manage_tenants($user_id = null)
	{
		if (!$user_id) {
			Session::set_flash('error', 'ID de usuario no válido');
			Response::redirect('admin/users');
		}

		// Verificar permisos
		if (!Helper_Permission::can('users', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para gestionar usuarios');
			Response::redirect('admin/users');
		}

		$user = Model_User::find($user_id);
		if (!$user) {
			Session::set_flash('error', 'Usuario no encontrado');
			Response::redirect('admin/users');
		}

		$data = array();
		$data['user'] = $user;
		$data['user_tenants'] = Helper_User_Tenant::get_user_tenants($user_id);
		$data['all_tenants'] = Helper_User_Tenant::get_all_tenants();

		$this->template->title = 'Gestionar Backends - ' . $user->username;
		$this->template->content = View::forge('admin/users/manage_tenants', $data);
	}

	/**
	 * ACTUALIZAR TENANTS DE UN USUARIO
	 */
	public function action_update_user_tenants()
	{
		if (Input::method() !== 'POST') {
			Response::redirect('admin/users');
		}

		// Verificar permisos
		if (!Helper_Permission::can('users', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin/users');
		}

		$user_id = Input::post('user_id');

		try {
			$selected_tenants = Input::post('tenants', array());
			$default_tenant = Input::post('default_tenant');

			// Desactivar todos los actuales
			DB::update('user_tenants')
				->set(array('is_active' => 0, 'updated_at' => time()))
				->where('user_id', $user_id)
				->execute();

			// Asignar los seleccionados
			foreach ($selected_tenants as $tenant_id) {
				$is_default = ($tenant_id == $default_tenant);
				Helper_User_Tenant::assign($user_id, $tenant_id, $is_default);
			}

			Session::set_flash('success', 'Acceso a backends actualizado correctamente');
			Response::redirect('admin/users/manage_tenants/' . $user_id);

		} catch (Exception $e) {
			Log::error('Error actualizando tenants: ' . $e->getMessage());
			Session::set_flash('error', 'Error al actualizar acceso');
			Response::redirect('admin/users/manage_tenants/' . $user_id);
		}
	}

	/**
	 * ASIGNAR TODOS LOS TENANTS A UN USUARIO (SUPER ADMIN)
	 */
	public function action_assign_all_tenants()
	{
		if (Input::method() !== 'POST') {
			echo json_encode(array('error' => 'Método no permitido'));
			exit;
		}

		// Verificar permisos
		if (!Helper_Permission::can('users', 'edit'))
		{
			echo json_encode(array('error' => 'No tienes permisos'));
			exit;
		}

		$user_id = Input::post('user_id');
		$default_tenant = Input::post('default_tenant', 1);

		try {
			$count = Helper_User_Tenant::assign_all_tenants($user_id, $default_tenant);

			echo json_encode(array(
				'success' => true,
				'message' => 'Usuario asignado a ' . $count . ' backends',
				'count' => $count
			));

		} catch (Exception $e) {
			Log::error('Error asignando todos los tenants: ' . $e->getMessage());
			echo json_encode(array('error' => 'Error al asignar backends'));
		}

		exit;
	}
}

