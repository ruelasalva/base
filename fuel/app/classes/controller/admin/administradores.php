<?php

/**
 * CONTROLADOR ADMIN_ADMINISTRADORES
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Administradores extends Controller_Admin
{
	/**
	 * BEFORE
	 *
	 * @return Void
	 */
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
        parent::before();

		# SI EL USUARIO NO TIENE PERMISOS
		if(!Auth::member(100))
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No tienes los permisos para acceder a esta sección.');

			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin');
		}
	}


	/**
	 * INDEX
	 *
	 * MUESTRA UNA LISTADO DE REGISTROS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_index($search = '')
	{
		# SE INICIALIZAN LAS VARIABLES
		$data        = array();
		$admins_info = array();
		$per_page    = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$admins = Model_User::query()
		->where_open()
		->where('group', 100)
		->or_where('group', 50)
		->or_where('group', 30)
		->or_where('group', 25)
		->or_where('group', 20)
		->where_close();

		# SI HAY UNA BUSQUEDA
		if($search != '')
		{
			# SE ALMACENA LA BUSQUEDA ORIGINAL
			$original_search = $search;

			# SE LIMPIA LA CADENA DE BUSQUEDA
			$search = str_replace('+', ' ', rawurldecode($search));

			# SE REEMPLAZA LOS ESPACIOS POR PORCENTAJES
			$search = str_replace(' ', '%', $search);

			# SE AGREGA LA CLAUSULA
			$admins = $admins->where(DB::expr("CONCAT(`t0`.`username`, ' ', `t0`.`email`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $admins->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
    		'show_last'      => true,
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('admins', $config);

		# SE EJECUTA EL QUERY
		$admins = $admins->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($admins))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($admins as $admin)
			{
				# SE DESERIALIZAN LOS CAMPOS EXTRAS
				$status = unserialize($admin->profile_fields);

				# SE ESTABLECE EL NOMBRE DEL GRUPO
				switch($admin->group)
				{
					case 20:  $group = 'Empleado'; break;
					case 25:  $group = 'Vendedor'; break;
					case 30:  $group = 'Externo'; break;
					case 50:  $group = 'Moderador'; break;
					case 100: $group = 'Administrador'; break;
					default:  $group = 'Desconocido'; break;
				}

				# SE ALMACENA LA INFORMACION
				$admins_info[] = array(
					'id'        => $admin->id,
					'username'  => $admin->username,
					'full_name' => $status['full_name'],
					'email'     => $admin->email,
					'group'     => $group,
					'connected' => ($status['connected']) ? 'Conectado' : 'Desconectado',
					'banned'    => ($status['banned']) ? 'Sí' : 'No'
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['admins']     = $admins_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Administradores';
		$this->template->content = View::forge('admin/administradores/index', $data, false);
	}


	/**
	 * BUSCAR
	 *
	 * REDIRECCIONA A LA URL DE BUSCAR REGISTROS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_buscar()
	{
		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE OBTIENEN LOS VALORES
			$data = array(
				'search' => ($_POST['search'] != '') ? $_POST['search'] : '',
			);

			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('search');
			$val->add_callable('Rules');
			$val->add_field('search', 'search', 'max_length[100]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run($data))
			{
				# SE REMPLAZAN ALGUNOS CARACTERES
				$search = str_replace(' ', '+', $val->validated('search'));
				$search = str_replace('*', '', $search);

				# SE ALMACENA LA CADENA DE BUSQUEDA
				$search = ($val->validated('search') != '') ? $search : '';

				# SE REDIRECCIONA A BUSCAR
				Response::redirect('admin/administradores/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/administradores');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/administradores');
		}
	}


	/**
	 * AGREGAR
	 *
	 * PERMITE AGREGAR UN REGISTRO A LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_agregar()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('username', 'email', 'password', 'group', 'full_name');

		# SE RECORRE CAMPO POR CAMPO
		foreach($fields as $field)
		{
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array (
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SI SE UTILIZA EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('admin');
			$val->add_callable('Rules');
			$val->add_field('username', 'usuario', 'required|valid_string[alpha,numeric]|min_length[3]|max_length[50]');
			$val->add_field('email', 'email', 'required|min_length[7]|max_length[255]|valid_email');
			$val->add_field('password', 'contraseña', 'required|min_length[6]|max_length[20]');
			$val->add_field('group', 'tipo de usuario', 'required|valid_string[numeric]|numeric_min[10]|numeric_max[100]');
			$val->add_field('full_name', 'nombre completo', 'required|alphabetic_spaces|min_length[3]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				try
				{
					# SE ALMACENA LA INFORMACION EN EL MODELO
					$user = Auth::instance()->create_user(
						$val->validated('username'),
						$val->validated('password'),
						$val->validated('email'),
						$val->validated('group'),
						array(
							'full_name' => $val->validated('full_name'),
							'connected' => false,
							'banned'    => false
						)
					);

					# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
					if($user)
					{
						# SE ESTABLECE EL NOMBRE DEL GRUPO
						switch($val->validated('group'))
						{
							case 20:  $group = 'empleado'; break;
							case 25:  $group = 'vendedor'; break;
							case 30:  $group = 'externo'; break;
							case 50:  $group = 'moderador'; break;
							case 100: $group = 'administrador'; break;
							default:  $group = 'desconocido'; break;
						}

						# SE ESTABLECE EL MENSAJE DE EXITO
						Session::set_flash('success', 'Se agregó el '.$group.' <b>'.$val->validated('username').'</b> correctamente.');

						# SE REDIRECCIONA AL USUARIO
						Response::redirect('admin/administradores');
					}
				}
				catch(\SimpleUserUpdateException $e)
				{
					# SI EL USUARIO YA ESTA REGISTRADO EN LA BASE DE DATOS
					if($e->getMessage() == 'Username already exists')
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
                        Session::set_flash('error', 'El nombre de usuario <b>'.$val->validated('username').'</b> ya ha sido registrado, por favor intenta con uno diferente.');

                        # SE ESTABLECE EL VALOR DE LAS CLASES
                        $classes['username']['form-group']   = 'has-danger';
                        $classes['username']['form-control'] = 'is-invalid';
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
                        Session::set_flash('error', 'El correo <b>'.$val->validated('email').'</b> ya ha sido registrado, por favor intenta con uno diferente.');

                        # SE ESTABLECE EL VALOR DE LAS CLASES
						$classes['email']['form-group']   = 'has-danger';
                        $classes['email']['form-control'] = 'is-invalid';
					}

					# SI LA CONTRASEÑA ES VALIDA
					if($classes['password']['form-group'] == 'has-success')
					{
						# SE ESTABLECE EL VALOR DE LAS CLASES
						$classes['password']['form-group']   = null;
						$classes['password']['form-control'] = null;
					}

					# SE ALMACENA LA INFORMACION PARA LA VISTA
					$data['username']  = Input::post('username');
					$data['email']     = Input::post('email');
					$data['group']     = Input::post('group');
					$data['full_name'] = Input::post('full_name');
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');

				# SE ALMACENA LOS ERRORES DETECTADOS
				$data['errors'] = $val->error();

				# SE RECORRE CLASE POR CLASE
				foreach($classes as $name => $class)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
					$classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
				}

				# SI LA CONTRASEÑA ES VALIDA
				if($classes['password']['form-group'] == 'has-success')
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['password']['form-group']   = null;
					$classes['password']['form-control'] = null;
				}

				# SE ALMACENA LA INFORMACION PARA LA VISTA
				$data['username']  = Input::post('username');
				$data['email']     = Input::post('email');
				$data['group']     = Input::post('group');
				$data['full_name'] = Input::post('full_name');
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar administrador';
		$this->template->content = View::forge('admin/administradores/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($admin_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($admin_id == 0 || !is_numeric($admin_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/administradores');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$admin = Model_User::query()
		->where('id', $admin_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($admin))
		{
			# SI EL USUARIO NO ES ADMINISTRADOR
			if($admin->group != 100 && $admin->group != 50 && $admin->group != 25 && $admin->group != 20)
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/administradores');
			}

			# SE ESTABLECE EL NOMBRE DEL GRUPO
			switch($admin->group)
			{
				case 20:  $group = 'Empleado'; break;
				case 25:  $group = 'Vendedor'; break;
				case 30:  $group = 'Externo'; break;
				case 50:  $group = 'Moderador'; break;
				case 100: $group = 'Administrador'; break;
				default:  $group = 'Desconocido'; break;
			}

			# SE DESERIALIZAN LOS CAMPOS EXTRAS
			$status = unserialize($admin->profile_fields);

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['username']  = $admin->username;
			$data['email']     = $admin->email;
			$data['group']     = $group;
			$data['full_name'] = $status['full_name'];
			$data['banned']    = ($status['banned']) ? 'Sí' : 'No';
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/administradores');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $admin_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del administrador';
		$this->template->content = View::forge('admin/administradores/info', $data);
	}

	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($admin_id = 0)
	{
		# SI EL ADMINISTRADOR QUIERE EDITAR SU PROPIO PERFIL
		if($admin_id == Auth::get('id'))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/perfil');
		}

		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($admin_id == 0 || !is_numeric($admin_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/administradores');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('email', 'password', 'group', 'banned', 'full_name');

		# SE RECORRE CAMPO POR CAMPO
		foreach($fields as $field)
		{
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array (
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$admin = Model_User::query()
		->where('id', $admin_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($admin))
		{
			# SI EL USUARIO NO ES ADMINISTRADOR
			if($admin->group != 100 && $admin->group != 50  && $admin->group != 30 && $admin->group != 25 && $admin->group != 20)
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/administradores');
			}

			# SE DESERIALIZAN LOS CAMPOS EXTRAS
			$status = unserialize($admin->profile_fields);

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['username']  = $admin->username;
			$data['email']     = $admin->email;
			$data['group']     = $admin->group;
			$data['full_name'] = $status['full_name'];
			$data['banned']    = $status['banned'];
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/administradores');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('admin');
			$val->add_callable('Rules');
			$val->add_field('username', 'usuario', 'required|valid_string[alpha,numeric]|min_length[3]|max_length[50]');
			$val->add_field('email', 'email', 'required|min_length[7]|max_length[255]|valid_email');
			$val->add_field('password', 'contraseña', 'min_length[6]|max_length[20]');
			$val->add_field('group', 'tipo de usuario', 'required|valid_string[numeric]|numeric_min[10]|numeric_max[100]');
			$val->add_field('full_name', 'nombre completo', 'required|alphabetic_spaces|min_length[3]|max_length[255]');
			$val->add_field('banned', 'baneado', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				try
				{
					# SE ESTEBLECE LA NUEVA INFORMACION
					$data_to_update = array(
						'email'     => $val->validated('email'),
						'group'     => $val->validated('group'),
						'full_name' => $val->validated('full_name'),
						'banned'    => ($val->validated('banned')) ? true : false
					);

					# SE ACTUALIZA LA INFORMACION DEL USUARIO EN LA BASE DE DATOS
					$user = Auth::instance()->update_user($data_to_update, $admin->username);

					# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
					if($user)
					{
						# SE ESTABLECE EL MENSAJE DE EXITO
						Session::set_flash('success', 'Se actualizó la información de <b>'.$admin->username.'</b> correctamente.');

						# SI HAY UN PASSWORD
						if($val->validated('password') != '')
						{
							# SE RESETEA LA CONTRASEÑA DEL USUARIO Y SE ALMACENA
							$new_password = Auth::reset_password($admin->username);

							# SE CAMBIA LA CONTRASEÑA
							Auth::change_password($new_password, $val->validated('password'), $admin->username);
						}

						# SE REDIRECCIONA AL USUARIO
						Response::redirect('admin/administradores/editar/'.$admin_id);
					}
				}
				catch(\SimpleUserUpdateException $e)
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', 'El correo electrónico <b>'.$val->validated('email').'</b> ya está asociado a otra cuenta, por favor escribe una dirección de correo electrónico diferente.');

					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['email']['form-group']   = 'has-danger';
					$classes['email']['form-control'] = 'is-invalid';

					# SI LA CONTRASEÑA ES VALIDA
					if($classes['password']['form-group'] == 'has-success')
					{
						# SE ESTABLECE EL VALOR DE LAS CLASES
						$classes['password']['form-group']   = null;
						$classes['password']['form-control'] = null;
					}

					# SE ALMACENA LA INFORMACION PARA LA VISTA
					$data['email']     = $val->validated('email');
					$data['group']     = $val->validated('group');
					$data['full_name'] = $val->validated('full_name');
					$data['banned']    = $val->validated('banned');
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');

				# SE ALMACENA LOS ERRORES DETECTADOS
				$data['errors'] = $val->error();

				# SE RECORRE CLASE POR CLASE
				foreach($classes as $name => $class)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
					$classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
				}

				# SI LA CONTRASEÑA ES VALIDA
				if($classes['password']['form-group'] == 'has-success')
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['password']['form-group']   = null;
					$classes['password']['form-control'] = null;
				}

				# SE ALMACENA LA INFORMACION PARA LA VISTA
				$data['email']     = Input::post('email');
				$data['group']     = Input::post('group');
				$data['full_name'] = Input::post('full_name');
				$data['banned']    = Input::post('banned');
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']      = $admin_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar administrador';
		$this->template->content = View::forge('admin/administradores/editar', $data);
	}
}
