<?php

/**
* CONTROLADOR ADMIN_EMPLEADOS
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Empleados extends Controller_Admin
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
		if(!Auth::member(100) && !Auth::member(50))
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
		$data       = array();
		$users_info = array();
		$per_page   = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$users = Model_Employee::query()
		->related('user')
		->where('id','>=', 0);

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
			$users = $users->where(DB::expr("CONCAT(`t0`.`name`, ' ', `t0`.`email`,' ', `t0`.`codigo`,' ', `t1`.`username`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $users->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
    		'show_last'      => true,
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('users', $config);

		# SE EJECUTA EL QUERY
		$users = $users->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($users))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($users as $user)
			{



				# SE ALMACENA LA INFORMACION
				$users_info[] = array(
					'id' 			=> $user->id,
					'codigo' 		=> $user->codigo ?: 'SN/E',
					'username' 		=> $user->user ? $user->user->username : 'Sin Usuario',
					'name' 			=> ($user->name && $user->last_name) ? ($user->name . ' ' . $user->last_name) : 'Sin nombre',
					'code_seller' 	=> $user->code_seller ?: 'SN/V',
					'email' 		=> $user->email ?: 'Sin correo electrónico',
					'department_id' => $user->department ? $user->department->name : 'Sin departamento',
					'phone' 		=> $user->phone ?: 'Sin teléfono',
					'updated_at' 	=> $user->updated_at ? date('d/m/Y - H:i', $user->updated_at) : 'No ha sido actualizado',
				);

			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['users']      = $users_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Empleados';
		$this->template->content = View::forge('admin/empleados/index', $data, false);
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
				Response::redirect('admin/empleados/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/empleados');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/empleados');
		}
	}


	/**
	* INFO
	*
	* MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_info($user_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($user_id == 0 || !is_numeric($user_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/empleados');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data           = array();
		$addresses_info = array();
		$tax_data_info  = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$user = Model_Employee::query()
		->where('id', $user_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($user))
		{

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['username'] 			= ($user->user_id != '' && $user->user) ? $user->user->username : 'Nombre de usuario no disponible';
			$data['email_user'] 		= ($user->user_id != '' && $user->user) ? $user->user->email : 'No cuenta con correo';
			$data['email']        		= ($user->email != '') ? $user->email : 'Sin Email';
			$data['name']         		= $user->name;
			$data['codigo']         	= $user->codigo;
			$data['code_seller']       	= $user->code_seller;
			$data['last_name']    		= $user->last_name;
			$data['phone']        		= ($user->phone != '') ? $user->phone : 'Sin Telefono';
			$data['department_id']      = $user->department->name;

		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/empleados');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $user_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del empleado';
		$this->template->content = View::forge('admin/empleados/info', $data);
	}


	/**
	* EDITAR
	*
	* PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_editar($employee_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($employee_id == 0 || !is_numeric($employee_id))
		{
			Response::redirect('admin/empleados');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data            = array();
		$classes         = array();
		$fields          = array('email', 'email_user', 'name', 'last_name', 'code_seller','phone', 'department_id', 'codigo', 'user_id');
		$department_opts = array();
		$user_opts       = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$employee = Model_Employee::query()
		->related('user')
		->where('id', $employee_id)
		->get_one();

		# SI NO SE OBTIENE INFORMACION
		if(empty($employee))
		{
			Response::redirect('admin/empleados');
		}

		# SE RECORRE CAMPO POR CAMPO
		foreach($fields as $field)
		{
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array (
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('employee');
			$val->add_field('email', 'Email', 'min_length[1]|valid_email');
			$val->add_field('name', 'Nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('last_name', 'Apellidos', 'required|min_length[1]|max_length[255]');
			$val->add_field('phone', 'Teléfono', 'min_length[1]|max_length[255]');
			$val->add_field('codigo', 'Código', 'min_length[1]|max_length[255]');
			$val->add_field('code_seller', 'vendedor', 'min_length[1]|max_length[255]');
			$val->add_field('department_id', 'Departamento', 'min_length[1]|max_length[255]');

			# SI SE SLECCIONA UN USUARIO
			if(Input::post('user_id') != 'none')
			{
				# SE AGREGA LA VALIDACION DEL CAMPO
				$val->add_field('user_id', 'Usuario', 'min_length[1]');
			}

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SI EXISTE UN CODIGO
				if(!empty($val->validated('codigo')))
				{
					# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
					$existing_employee = Model_Employee::query()
					->where('codigo', $val->validated('codigo'))
					->where('id', '!=', $employee_id)
					->get_one();

					# SI EXISTE UN EMPLEADO CON EL CODIGO ASIGNADO
					if(!empty($existing_employee))
					{
						Session::set_flash('error', 'No se pudo actualizar el empleado. Ya existe un empleado con el mismo código.');
					}
					else
					{
						# SE ESTEBLECE LA NUEVA INFORMACION
						$employee->codigo = $val->validated('codigo');
					}
				}

				# SI EXISTE UN USUARIO
				if(!empty($val->validated('user_id')))
				{
					# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
					$existing_user_employee = Model_Employee::query()
					->where('user_id', $val->validated('user_id'))
					->where('id', '!=', $employee_id)
					->get_one();

					# SI EXISTE UN EMPLEADO CON EL USUARIO ASIGNADO
					if(!empty($existing_user_employee))
					{
						Session::set_flash('error', 'No se pudo actualizar el empleado. Ya existe un empleado con el mismo código.');
					}
					else
					{
						# SE ESTEBLECE LA NUEVA INFORMACION
						$employee->user_id   = $val->validated('user_id');
					}
				}

				# SE ESTEBLECE LA NUEVA INFORMACION
				$employee->name      		= $val->validated('name');
				$employee->last_name 		= $val->validated('last_name');
				$employee->phone     		= $val->validated('phone');
				$employee->email     		= $val->validated('email');
				$employee->code_seller     	= $val->validated('code_seller');
				$employee->department_id    = $val->validated('department_id');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($employee->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información del empleado <b>'.$employee->name.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/empleados/editar/'.$employee_id);
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifica.');

				# SE ALMACENA LOS ERRORES DETECTADOS
				$data['errors'] = $val->error();
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$department_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$departments = Model_Employees_Department::query()->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($departments))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($departments as $department)
			{
				# SE ALMACENA LA OPCION
				$department_opts += array($department->id => $department->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$user_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$users = Model_User::query()->where('group', '>', '2')->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($users))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($users as $user)
			{
				# SE ALMACENA LA OPCION
				$user_opts += array($user->id => $user->username);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['username'] 		 = ($employee->user_id && $employee->user) ? $employee->user->username : 'N/A';
		$data['email_user'] 	 = ($employee->user_id && $employee->user) ? $employee->user->email : 'N/A';
		$data['name'] 			 = $employee->name;
		$data['last_name'] 		 = $employee->last_name;
		$data['email'] 			 = $employee->email;
		$data['codigo'] 		 = $employee->codigo;
		$data['code_seller'] 	 = $employee->code_seller;
		$data['user_id'] 		 = $employee->user_id;
		$data['department_id'] 	 = $employee->department_id;
		$data['phone'] 			 = ($employee->phone != '') ? $employee->phone : 'N/A';
		$data['id'] 			 = $employee_id;
		$data['classes'] 		 = $classes;
		$data['department_opts'] = $department_opts;
		$data['user_opts'] 		 = $user_opts;

		# SE CARGA LA VISTA
		$this->template->title = 'Editar Empleado';
		$this->template->content = View::forge('admin/empleados/editar', $data);
	}


	/**
	* AGREGAR EMPLEADO
	*
	* PERMITE AGREGAR UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_agregar()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data = array();
		$classes = array();
		$fields = array('user_id', 'email_user', 'email', 'name', 'last_name', 'phone', 'department_id', 'codigo','code_seller');
		$department_opts = array();
		$user_opts = array('' => 'Seleccionar');

		# OBTENER LA LISTA DE DEPARTAMENTOS
		$departments = Model_Employees_Department::query()->get();
		foreach ($departments as $department) {
			$department_opts[$department->id] = $department->name;
		}

		# OBTENER LA LISTA DE USUARIOS
		$users = Model_User::query()->where('group', '>', '2')->get();
		foreach ($users as $user) {
			$user_opts[$user->id] = $user->username;
		}

		# SE RECORRE CAMPO POR CAMPO
		foreach ($fields as $field) {
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array(
				'form-group' => null,
				'form-control' => null,
			);
		}

		# SI SE UTILIZA EL METODO POST
		if (Input::method() == 'POST') {

			# Se guarda la información ingresada por el usuario en caso de error
			$data['user_id'] 		= Input::post('user_id');
			$data['email_user'] 	= Input::post('email_user');
			$data['email'] 			= Input::post('email');
			$data['name'] 			= Input::post('name');
			$data['last_name'] 		= Input::post('last_name');
			$data['phone'] 			= Input::post('phone');
			$data['department_id'] 	= Input::post('department_id');
			$data['codigo'] 		= Input::post('codigo');
			$data['code_seller'] 	= Input::post('code_seller');

			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('employee');
			$val->add_callable('Rules');
			$val->add_field('user_id', 'ID de Usuario', 'min_length[1]');
			$val->add_field('email_user', 'Email de Usuario', 'min_length[1]|max_length[255]');
			$val->add_field('email', 'Email', 'min_length[7]|max_length[255]|valid_email');
			$val->add_field('name', 'Nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('last_name', 'Apellidos', 'required|min_length[1]|max_length[255]');
			$val->add_field('phone', 'Teléfono', 'min_length[1]|max_length[255]');
			$val->add_field('department_id', 'ID de Departamento', 'required|valid_string[numeric]');
			$val->add_field('codigo', 'Código', 'min_length[1]|max_length[255]');
			$val->add_field('code_seller', 'Vendedor', 'min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if ($val->run()) {
				try {
					// Verificar si el usuario ya está asignado a otro empleado
					$existing_user_employee = Model_Employee::query()
					->where('user_id', $val->validated('user_id'))
					->get_one();

					if ($existing_user_employee) {
						// Usuario ya asignado a otro empleado
						Session::set_flash('error', 'No se pudo agregar el empleado. El usuario ya está asignado a otro empleado.');
					} else {
						// Verificar si se proporcionó un código de empleado
						$codigo = $val->validated('codigo');
						if (!empty($codigo)) {
							$existing_employee = Model_Employee::query()
							->where('codigo', $codigo)
							->get_one();

							if ($existing_employee) {
								// Empleado con el mismo número de empleado ya existe
								Session::set_flash('error', 'No se pudo agregar el empleado. Ya existe un empleado con el mismo número de empleado.');
							} else {
								# FECHA DE CREACION
								$currentDate = time();

								# SE ALMACENA LA INFORMACION EN EL MODELO
								$employee_data = array(
									'name'			=> $val->validated('name'),
									'last_name' 	=> $val->validated('last_name'),
									'phone' 		=> $val->validated('phone'),
									'department_id' => $val->validated('department_id'),
									'email' 		=> $val->validated('email'),
									'codigo' 		=> $val->validated('codigo'),
									'code_seller'	=> $val->validated('code_seller'),
									'deleted' 		=> 0,
									'created_at' 	=> $currentDate,
								);

								// Agregar user_id y email_user si se selecciona un usuario
								$user_id = $val->validated('user_id');
								if (!empty($user_id)) {
									$employee_data['user_id'] = $user_id;

									// Obtener el correo electrónico del usuario seleccionado
									$selected_user = Model_User::find($user_id);
									if ($selected_user) {
										$employee_data['email_user'] = $selected_user->email;
									}
								}

								$employee = Model_Employee::forge($employee_data);

								# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
								if ($employee->save()) {
									# SE ESTABLECE EL MENSAJE DE EXITO
									Session::set_flash('success', 'Se agregó el empleado <b>' . $val->validated('name') . '</b> correctamente.');

									# SE REDIRECCIONA AL USUARIO
									Response::redirect('admin/empleados');
								}
							}
						} else {
							# FECHA DE CREACION
							$currentDate = time();

							# SE ALMACENA LA INFORMACION EN EL MODELO
							$employee_data = array(
								'name' 			=> $val->validated('name'),
								'last_name' 	=> $val->validated('last_name'),
								'phone' 		=> $val->validated('phone'),
								'department_id' => $val->validated('department_id'),
								'email' 		=> $val->validated('email'),
								'code_seller'	=> $val->validated('code_seller'),
								'deleted' 		=> 0,
								'created_at' 	=> $currentDate,
							);

							// Agregar user_id y email_user si se selecciona un usuario
							$user_id = $val->validated('user_id');
							if (!empty($user_id)) {
								$employee_data['user_id'] = $user_id;

								// Obtener el correo electrónico del usuario seleccionado
								$selected_user = Model_User::find($user_id);
								if ($selected_user) {
									$employee_data['email_user'] = $selected_user->email;
								}
							}

							$employee = Model_Employee::forge($employee_data);

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if ($employee->save()) {
								# SE ESTABLECE EL MENSAJE DE EXITO
								Session::set_flash('success', 'Se agregó el empleado <b>' . $val->validated('name') . '</b> correctamente.');

								# SE REDIRECCIONA AL USUARIO
								Response::redirect('admin/empleados');
							}
						}
					}
				} catch (\Exception $e) {
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', 'No se pudo agregar el empleado. Por favor intenta nuevamente.');

					# SE RECORRE CLASE POR CLASE
					foreach ($classes as $name => $class) {
						# SE ESTABLECE EL VALOR DE LAS CLASES
						$classes[$name]['form-group'] = 'has-danger';
						$classes[$name]['form-control'] = 'is-invalid';
					}

					# ALMACENAR LA INFORMACION PARA LA VISTA
					foreach ($fields as $field) {
						$data[$field] = Input::post($field);
					}
				}
			} else {
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifica.');

				# SE ALMACENA LOS ERRORES DETECTADOS
				$data['errors'] = $val->error();

				# SE RECORRE CLASE POR CLASE
				foreach ($classes as $name => $class) {
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes[$name]['form-group'] = ($val->error($name)) ? 'has-danger' : 'has-success';
					$classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
				}

				# ALMACENAR LA INFORMACION PARA LA VISTA
				foreach ($fields as $field) {
					$data[$field] = Input::post($field);
				}
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['classes'] = $classes;
		$data['department_opts'] = $department_opts;
		$data['user_opts'] = $user_opts;

		# CARGA LA VISTA
		$this->template->title = 'Agregar empleado';
		$this->template->content = View::forge('admin/empleados/agregar', $data);
	}






}
