<?php

/**
 * Controller_Admin_Almacenes
 * 
 * Gestión de almacenes y ubicaciones
 * - CRUD de almacenes
 * - Gestión de ubicaciones dentro de cada almacén
 * - Control de permisos
 * - Auditoría con logs
 */
class Controller_Admin_Almacenes extends Controller_Admin
{
	/**
	 * INDEX - Listado de almacenes
	 */
	public function action_index()
	{
		// Verificar permisos
		if (!Helper_Permission::can('almacenes', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver almacenes');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener todos los almacenes
		$almacenes = DB::select('a.*')
			->select([DB::expr('COUNT(l.id)'), 'locations_count'])
			->select([DB::expr('u.username'), 'manager_name'])
			->from(['almacenes', 'a'])
			->join(['almacen_locations', 'l'], 'LEFT')
			->on('a.id', '=', 'l.almacen_id')
			->join(['users', 'u'], 'LEFT')
			->on('a.manager_user_id', '=', 'u.id')
			->where('a.tenant_id', $tenant_id)
			->group_by('a.id')
			->order_by('a.type', 'ASC')
			->order_by('a.name', 'ASC')
			->execute()
			->as_array();

		$data = [
			'title' => 'Gestión de Almacenes',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'almacenes' => $almacenes,
			'can_create' => Helper_Permission::can('almacenes', 'create'),
			'can_edit' => Helper_Permission::can('almacenes', 'edit'),
			'can_delete' => Helper_Permission::can('almacenes', 'delete')
		];

		$data['content'] = View::forge('admin/almacenes/index', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * CREAR - Formulario y guardado de nuevo almacén
	 */
	public function action_crear()
	{
		// Verificar permisos
		if (!Helper_Permission::can('almacenes', 'create'))
		{
			Session::set_flash('error', 'No tienes permisos para crear almacenes');
			Response::redirect('admin/almacenes');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Procesar formulario
		if (Input::method() === 'POST')
		{
			try {
				// Validar campos requeridos
				$val = Validation::forge();
				$val->add_field('code', 'Código', 'required|max_length[50]');
				$val->add_field('name', 'Nombre', 'required|max_length[255]');
				$val->add_field('type', 'Tipo', 'required');

				if ($val->run())
				{
					// Verificar que el código no exista
					$exists = DB::select(DB::expr('COUNT(*) as count'))
						->from('almacenes')
						->where('tenant_id', $tenant_id)
						->where('code', Input::post('code'))
						->execute()
						->get('count');

					if ($exists > 0)
					{
						Session::set_flash('error', 'Ya existe un almacén con ese código');
						Response::redirect('admin/almacenes/crear');
					}

					// Insertar almacén
					list($insert_id, $rows_affected) = DB::insert('almacenes')->set([
						'tenant_id' => $tenant_id,
						'code' => Input::post('code'),
						'name' => Input::post('name'),
						'description' => Input::post('description'),
						'type' => Input::post('type'),
						'address' => Input::post('address'),
						'city' => Input::post('city'),
						'state' => Input::post('state'),
						'country' => Input::post('country', 'México'),
						'postal_code' => Input::post('postal_code'),
						'phone' => Input::post('phone'),
						'manager_user_id' => Input::post('manager_user_id') ?: null,
						'capacity_m2' => Input::post('capacity_m2') ?: null,
						'capacity_units' => Input::post('capacity_units') ?: null,
						'notes' => Input::post('notes'),
						'is_active' => (int)Input::post('is_active', 1),
						'created_by' => Auth::get('id')
					])->execute();

					// Log
					Helper_Log::record(
						'almacenes',
						'create',
						$insert_id,
						'Almacén creado: ' . Input::post('name'),
						null,
						Input::post()
					);

					Session::set_flash('success', 'Almacén creado exitosamente');
					Response::redirect('admin/almacenes');
				}
				else
				{
					Session::set_flash('error', $val->error());
				}
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error al crear almacén: ' . $e->getMessage());
			}
		}

		// Obtener usuarios para el select de responsable
		$users = DB::select('id', 'username', 'email')
			->from('users')
			->where('is_active', 1)
			->order_by('username', 'ASC')
			->execute()
			->as_array();

		$data = [
			'title' => 'Crear Almacén',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'users' => $users
		];

		$data['content'] = View::forge('admin/almacenes/crear', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * EDITAR - Formulario y guardado de almacén existente
	 */
	public function action_editar($id = null)
	{
		// Verificar permisos
		if (!Helper_Permission::can('almacenes', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para editar almacenes');
			Response::redirect('admin/almacenes');
		}

		if (!$id)
		{
			Session::set_flash('error', 'ID de almacén no proporcionado');
			Response::redirect('admin/almacenes');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener almacén
		$almacen = DB::select('*')
			->from('almacenes')
			->where('id', $id)
			->where('tenant_id', $tenant_id)
			->execute()
			->current();

		if (!$almacen)
		{
			Session::set_flash('error', 'Almacén no encontrado');
			Response::redirect('admin/almacenes');
		}

		// Procesar formulario
		if (Input::method() === 'POST')
		{
			try {
				$old_data = $almacen;

				// Actualizar almacén
				DB::update('almacenes')
					->set([
						'name' => Input::post('name'),
						'description' => Input::post('description'),
						'type' => Input::post('type'),
						'address' => Input::post('address'),
						'city' => Input::post('city'),
						'state' => Input::post('state'),
						'country' => Input::post('country', 'México'),
						'postal_code' => Input::post('postal_code'),
						'phone' => Input::post('phone'),
						'manager_user_id' => Input::post('manager_user_id') ?: null,
						'capacity_m2' => Input::post('capacity_m2') ?: null,
						'capacity_units' => Input::post('capacity_units') ?: null,
						'notes' => Input::post('notes'),
						'is_active' => (int)Input::post('is_active', 1)
					])
					->where('id', $id)
					->where('tenant_id', $tenant_id)
					->execute();

				// Log
				Helper_Log::record(
					'almacenes',
					'edit',
					$id,
					'Almacén editado: ' . Input::post('name'),
					$old_data,
					Input::post()
				);

				Session::set_flash('success', 'Almacén actualizado exitosamente');
				Response::redirect('admin/almacenes');
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error al actualizar almacén: ' . $e->getMessage());
			}
		}

		// Obtener usuarios para el select
		$users = DB::select('id', 'username', 'email')
			->from('users')
			->where('is_active', 1)
			->order_by('username', 'ASC')
			->execute()
			->as_array();

		$data = [
			'title' => 'Editar Almacén: ' . $almacen['name'],
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'almacen' => $almacen,
			'users' => $users
		];

		$data['content'] = View::forge('admin/almacenes/editar', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * ELIMINAR - Borrado de almacén (validando que no tenga productos)
	 */
	public function action_eliminar($id = null)
	{
		// Verificar permisos
		if (!Helper_Permission::can('almacenes', 'delete'))
		{
			Session::set_flash('error', 'No tienes permisos para eliminar almacenes');
			Response::redirect('admin/almacenes');
		}

		if (!$id)
		{
			Session::set_flash('error', 'ID de almacén no proporcionado');
			Response::redirect('admin/almacenes');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener almacén
		$almacen = DB::select('*')
			->from('almacenes')
			->where('id', $id)
			->where('tenant_id', $tenant_id)
			->execute()
			->current();

		if (!$almacen)
		{
			Session::set_flash('error', 'Almacén no encontrado');
			Response::redirect('admin/almacenes');
		}

		try {
			// TODO: Validar que no tenga productos en stock
			// (Implementar cuando exista la tabla de inventario)

			// Eliminar (las ubicaciones se borran por CASCADE)
			DB::delete('almacenes')
				->where('id', $id)
				->where('tenant_id', $tenant_id)
				->execute();

			// Log
			Helper_Log::record(
				'almacenes',
				'delete',
				$id,
				'Almacén eliminado: ' . $almacen['name'],
				$almacen,
				null
			);

			Session::set_flash('success', 'Almacén eliminado exitosamente');
		}
		catch (Exception $e)
		{
			Session::set_flash('error', 'Error al eliminar almacén: ' . $e->getMessage());
		}

		Response::redirect('admin/almacenes');
	}

	/**
	 * UBICACIONES - Gestión de ubicaciones dentro de un almacén
	 */
	public function action_ubicaciones($almacen_id = null)
	{
		// Verificar permisos
		if (!Helper_Permission::can('almacenes', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver almacenes');
			Response::redirect('admin/almacenes');
		}

		if (!$almacen_id)
		{
			Session::set_flash('error', 'ID de almacén no proporcionado');
			Response::redirect('admin/almacenes');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener almacén
		$almacen = DB::select('*')
			->from('almacenes')
			->where('id', $almacen_id)
			->where('tenant_id', $tenant_id)
			->execute()
			->current();

		if (!$almacen)
		{
			Session::set_flash('error', 'Almacén no encontrado');
			Response::redirect('admin/almacenes');
		}

		// Si es POST, crear/editar ubicación
		if (Input::method() === 'POST')
		{
			if (!Helper_Permission::can('almacenes', 'edit'))
			{
				Session::set_flash('error', 'No tienes permisos para editar ubicaciones');
				Response::redirect('admin/almacenes/ubicaciones/' . $almacen_id);
			}

			$ubicacion_id = Input::post('ubicacion_id');
			$code = trim(Input::post('code'));
			$name = trim(Input::post('name'));
			$type = Input::post('type');
			$aisle = trim(Input::post('aisle'));
			$section = trim(Input::post('section'));
			$level = trim(Input::post('level'));
			$capacity_units = Input::post('capacity_units');
			$notes = trim(Input::post('notes'));
			$is_active = Input::post('is_active') ? 1 : 0;

			// Validar código único
			$exists = DB::select(DB::expr('COUNT(*) as count'))
				->from('almacen_locations')
				->where('almacen_id', $almacen_id)
				->where('code', $code);

			if ($ubicacion_id)
			{
				$exists->where('id', '!=', $ubicacion_id);
			}

			$exists = $exists->execute()->current();

			if ($exists['count'] > 0)
			{
				Session::set_flash('error', 'Ya existe una ubicación con ese código en este almacén');
				Response::redirect('admin/almacenes/ubicaciones/' . $almacen_id);
			}

			try {
				if ($ubicacion_id)
				{
					// Editar
					$old_data = DB::select('*')
						->from('almacen_locations')
						->where('id', $ubicacion_id)
						->where('almacen_id', $almacen_id)
						->execute()
						->current();

					DB::update('almacen_locations')
						->set([
							'code' => $code,
							'name' => $name,
							'type' => $type,
							'aisle' => $aisle ?: null,
							'section' => $section ?: null,
							'level' => $level ?: null,
							'capacity_units' => $capacity_units ?: null,
							'notes' => $notes ?: null,
							'is_active' => $is_active,
							'updated_at' => date('Y-m-d H:i:s')
						])
						->where('id', $ubicacion_id)
						->where('almacen_id', $almacen_id)
						->execute();

					Helper_Log::record(
						'almacenes',
						'update_location',
						$ubicacion_id,
						'Ubicación actualizada: ' . $code,
						$old_data,
						[
							'code' => $code,
							'name' => $name,
							'type' => $type,
							'is_active' => $is_active
						]
					);

					Session::set_flash('success', 'Ubicación actualizada exitosamente');
				}
				else
				{
					// Crear
					$result = DB::insert('almacen_locations')
						->set([
							'almacen_id' => $almacen_id,
							'code' => $code,
							'name' => $name,
							'type' => $type,
							'aisle' => $aisle ?: null,
							'section' => $section ?: null,
							'level' => $level ?: null,
							'capacity_units' => $capacity_units ?: null,
							'notes' => $notes ?: null,
							'is_active' => $is_active,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						])
						->execute();

					$new_id = $result[0];

					Helper_Log::record(
						'almacenes',
						'create_location',
						$new_id,
						'Nueva ubicación creada: ' . $code,
						null,
						[
							'almacen_id' => $almacen_id,
							'code' => $code,
							'name' => $name,
							'type' => $type
						]
					);

					Session::set_flash('success', 'Ubicación creada exitosamente');
				}
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error al guardar ubicación: ' . $e->getMessage());
			}

			Response::redirect('admin/almacenes/ubicaciones/' . $almacen_id);
		}

		// Obtener ubicaciones
		$ubicaciones = DB::select('*')
			->from('almacen_locations')
			->where('almacen_id', $almacen_id)
			->order_by('code', 'ASC')
			->execute()
			->as_array();

		$data = [
			'title' => 'Ubicaciones: ' . $almacen['name'],
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'almacen' => $almacen,
			'ubicaciones' => $ubicaciones,
			'can_create' => Helper_Permission::can('almacenes', 'edit')
		];

		$data['content'] = View::forge('admin/almacenes/ubicaciones', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * Obtener ubicación por ID (AJAX)
	 */
	public function action_get_ubicacion($id = null)
	{
		if (!Helper_Permission::can('almacenes', 'view'))
		{
			return $this->response([
				'success' => false,
				'message' => 'Sin permisos'
			], 403);
		}

		if (!$id)
		{
			return $this->response([
				'success' => false,
				'message' => 'ID no proporcionado'
			], 400);
		}

		$ubicacion = DB::select('*')
			->from('almacen_locations')
			->where('id', $id)
			->execute()
			->current();

		if (!$ubicacion)
		{
			return $this->response([
				'success' => false,
				'message' => 'Ubicación no encontrada'
			], 404);
		}

		return $this->response([
			'success' => true,
			'ubicacion' => $ubicacion
		]);
	}

	/**
	 * Eliminar ubicación (AJAX)
	 */
	public function action_eliminar_ubicacion($id = null)
	{
		if (!Helper_Permission::can('almacenes', 'delete'))
		{
			return $this->response([
				'success' => false,
				'message' => 'No tienes permisos para eliminar ubicaciones'
			], 403);
		}

		if (!$id)
		{
			return $this->response([
				'success' => false,
				'message' => 'ID no proporcionado'
			], 400);
		}

		try {
			$ubicacion = DB::select('*')
				->from('almacen_locations')
				->where('id', $id)
				->execute()
				->current();

			if (!$ubicacion)
			{
				return $this->response([
					'success' => false,
					'message' => 'Ubicación no encontrada'
				], 404);
			}

			// TODO: Validar que no tenga productos en stock
			// (Implementar cuando exista la tabla de inventario)

			DB::delete('almacen_locations')
				->where('id', $id)
				->execute();

			Helper_Log::record(
				'almacenes',
				'delete_location',
				$id,
				'Ubicación eliminada: ' . $ubicacion['code'],
				$ubicacion,
				null
			);

			return $this->response([
				'success' => true,
				'message' => 'Ubicación eliminada exitosamente'
			]);
		}
		catch (Exception $e)
		{
			return $this->response([
				'success' => false,
				'message' => 'Error al eliminar: ' . $e->getMessage()
			], 500);
		}
	}
}
