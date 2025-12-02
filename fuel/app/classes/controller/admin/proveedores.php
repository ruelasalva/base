<?php

/**
 * CONTROLADOR ADMIN_PROVEEDORES
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Proveedores extends Controller_Admin
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
		$data        	= array();
		$providers_info = array();
		$per_page    	= 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$providers = Model_User::query()
		->related('provider') # RELACION CON LA TABLA PROVEEDORES
		->where('group', 10); # ES EL NUMERO DE GRUPO PROVEEDORES

		# SI HAY UNA BUSQUEDA
		if ($search != '')
		{
			# SE LIMPIA LA CADENA DE BUSQUEDA
			$search = str_replace('+', ' ', rawurldecode($search));
			$search = str_replace(' ', '%', $search);

			# SE AGREGA LA CLAUSULA DE BUSQUEDA
			$providers = $providers->where(DB::expr("CONCAT(`t0`.`username`, ' ', `t0`.`email`,' ',`t1`.`code_sap` )"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $providers->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
			'show_last'      => true,
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('admins', $config);

		# SE EJECUTA EL QUERY
		$providers = $providers->order_by('id', 'desc')
			->rows_limit($pagination->per_page)
			->rows_offset($pagination->offset)
			->get();

		# SI SE OBTIENE INFORMACION
		if (!empty($providers))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach ($providers as $provider)
			{
				# SE DESERIALIZAN LOS CAMPOS EXTRAS
				$status = unserialize($provider->profile_fields);

				# SE ESTABLECE EL NOMBRE DEL GRUPO
				switch ($provider->group)
				{
					case 10:  $group = 'Proveedor'; break;
					case 20:  $group = 'Empleado'; break;
					case 25:  $group = 'Vendedor'; break;
					case 50:  $group = 'Moderador'; break;
					case 100: $group = 'Administrador'; break;
					default:  $group = 'Desconocido'; break;
				}

				# TRAER EL NOMBRE DEL PROVEEDOR SI TIENE
				$provider_name = isset($provider->provider) ? $provider->provider->name : 'No asignado';

				# SE ALMACENA LA INFORMACION
				$providers_info[] = array(
					'id'        => $provider->id,
					'username'  => $provider->username,
					'name'      => $provider_name, 
					'email'     => $provider->email,
					'rfc'     	=> $provider->provider->rfc,
					'code_sap' => (!empty($provider->provider) && !empty($provider->provider->code_sap)) ? $provider->provider->code_sap : 'No asignado',
					'group'     => $group,
					'connected' => ($status['connected']) ? 'Conectado' : 'Desconectado',
					'banned'    => ($status['banned']) ? 'Sí' : 'No'
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['providers']      = $providers_info;
		$data['search']     	= str_replace('%', ' ', $search);
		$data['pagination'] 	= $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Proveedores';
		$this->template->content = View::forge('admin/proveedores/index', $data, false);
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
				Response::redirect('admin/proveedores/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/proveedores');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/proveedores');
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
    $fields  = array('username', 'email', 'password', 'group', 'name', 'rfc', 'code_sap', 'require_purchase');

    # SE GUARDAN LOS VALORES INGRESADOS POR EL USUARIO
    foreach($fields as $field)
    {
        $data[$field] = Input::post($field, ''); // SI NO HAY VALOR SE CARGA VACÍO
        $classes[$field] = array(
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # SI SE UTILIZA EL METODO POST
    if (Input::method() == 'POST')
    {
        # SE CREA LA VALIDACIÓN DE LOS CAMPOS
        $val = Validation::forge('admin');
        $val->add_callable('Rules');
        $val->add_field('username', 'usuario', 'required|valid_string[alpha,numeric]|min_length[3]|max_length[50]');
        $val->add_field('email', 'email', 'required|min_length[7]|max_length[255]|valid_email');
        $val->add_field('password', 'contraseña', 'required|min_length[6]|max_length[20]');
        $val->add_field('name', 'razón social', 'required|min_length[3]|max_length[250]');
        $val->add_field('rfc', 'rfc', 'required|valid_string[alpha,numeric]|min_length[3]|max_length[20]');
        $val->add_field('code_sap', 'código sistemas', 'valid_string[alpha,numeric]|min_length[3]|max_length[10]');
        $val->add_field('require_purchase', 'require orden', 'min_length[1]');

        # SI NO HAY NINGÚN PROBLEMA CON LA VALIDACIÓN
        if ($val->run())
        {
            try
            {
                # VERIFICAR SI EL RFC YA EXISTE
                $rfc_existente = Model_Provider::query()
                    ->where('rfc', strtoupper($val->validated('rfc')))
                    ->get_one();

                if ($rfc_existente) {
                    throw new Exception('El RFC <b>' . strtoupper($val->validated('rfc')) . '</b> ya está registrado. Intenta con uno diferente.');
                }

                # DEFINIR EL GRUPO PARA PROVEEDORES
                $group = 10;

                # CREAR EL USUARIO
                $user_id = Auth::instance()->create_user(
                    $val->validated('username'),
                    $val->validated('password'),
                    $val->validated('email'),
                    $group,
                    array(
                        'connected' => false,
                        'banned'    => false
                    )
                );

                if (!$user_id) {
                    throw new Exception('Error al crear el usuario.');
                }

                # VALIDAR SI EL USUARIO SE CREÓ
                $user = Model_User::find($user_id);
                if (!$user) {
                    throw new Exception('Usuario no encontrado en la base de datos.');
                }

                # CREAR EL PROVEEDOR ASOCIADO AL USUARIO
                $provider = new Model_Provider(array(
                    'user_id'          => $user_id,
                    'name'             => strtoupper($val->validated('name')),
                    'rfc'              => strtoupper($val->validated('rfc')),
                    'employee_id'      => 0, 
                    'payment_terms_id' => 0, 
                    'code_sap'         => !empty($val->validated('code_sap')) ? strtoupper($val->validated('code_sap')) : '',
                    'require_purchase' => (int) $val->validated('require_purchase'),
                    'created_at'       => time(),
                    
                ));

                \Log::debug('Datos a guardar del proveedor: ' . json_encode($provider->to_array()));

                if (!$provider->save()) {
                    \Log::error('Error al guardar el proveedor en la BD.');
                    throw new Exception('No se pudo guardar el proveedor en la base de datos.');
                }

                \Log::debug('Proveedor guardado correctamente con ID: ' . $provider->id);

                # SE ESTABLECE EL MENSAJE DE ÉXITO
                Session::set_flash('success', 'Se agregó el proveedor <b>' . $val->validated('username') . '</b> correctamente.');

                # SE REDIRECCIONA AL USUARIO
                Response::redirect('admin/proveedores');
            }
            catch (\SimpleUserUpdateException $e)
            {
                # SI EL USUARIO YA ESTÁ REGISTRADO EN LA BASE DE DATOS
                if ($e->getMessage() == 'Username already exists')
                {
                    Session::set_flash('error', 'El nombre de proveedor <b>'.$val->validated('username').'</b> ya ha sido registrado, por favor intenta con uno diferente.');
                    $classes['username']['form-group']   = 'has-danger';
                    $classes['username']['form-control'] = 'is-invalid';
                }
                else
                {
                    Session::set_flash('error', 'El correo <b>'.$val->validated('email').'</b> ya ha sido registrado, por favor intenta con uno diferente.');
                    $classes['email']['form-group']   = 'has-danger';
                    $classes['email']['form-control'] = 'is-invalid';
                }
            }
            catch (Exception $e)
            {
                # CAPTURAR OTROS ERRORES
                Session::set_flash('error', 'Error inesperado: ' . $e->getMessage());
            }
        }
        else
        {
            # SE ESTABLECE EL MENSAJE DE ERROR
            Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');

            # SE ALMACENA LOS ERRORES DETECTADOS
            $data['errors'] = $val->error();

            # SE RECORRE CLASE POR CLASE
            foreach ($classes as $name => $class)
            {
                $classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
                $classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
            }
        }
    }

    # SE ALMACENA LA INFORMACIÓN PARA LA VISTA
    $data['classes'] = $classes;

    # SE CARGA LA VISTA
    $this->template->title   = 'Agregar Proveedor';
    $this->template->content = View::forge('admin/proveedores/agregar', $data);
}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($provider_id = 0)
{
    # VALIDAR QUE SE RECIBA UN ID VÁLIDO
    if ($provider_id == 0 || !is_numeric($provider_id)) {
        Response::redirect('admin/proveedores');
    }

    # OBTENER EL PROVEEDOR Y SUS RELACIONES
    $provider = Model_Provider::query()
        ->related('user')
		->related('employee')
		->related('departments')
		->related('payment_term')
        ->where('user_id', $provider_id)
        ->get_one();

    # VALIDAR QUE EXISTA Y TENGA USUARIO RELACIONADO
    if (!$provider || !$provider->user) {
        Session::set_flash('error', 'No se encontró información del proveedor.');
        Response::redirect('admin/proveedores');
    }

    # OBTENER ESTADO DE PERFIL (BANEADO, ETC.)
    $status = @unserialize($provider->user->profile_fields) ?: [];
	$banned = (isset($status['banned']) && $status['banned']) ? 'Sí' : 'No';

    
	# DATOS GENERALES DEL PROVEEDOR
	$data['code_sap']     		= $provider->code_sap;
	$data['name']         		= $provider->name;
	$data['rfc']          		= $provider->rfc;
	$data['employee_id']  		= $provider->employee_id;
	$data['payment_terms_id']   = ($provider->payment_terms_id) ? $provider->payment_term->name : 0;
	$data['email']        		= $provider->user->email;
	$data['username']     		= $provider->user->username;
	$data['provider_id']  		= $provider->id;
	$data['origin']  			= $provider->origin;
	$data['provider_type']  	= $provider->provider_type;
	
	$data['provider_id']  		= $provider->id;
	$data['id']           		= $provider->user->id;
	$data['banned']       		= $banned;

	# DEPARTAMENTO PRINCIPAL O PRIMERO
	$main_dept = Model_Providers_Department::query()
		->related('department')
		->where('provider_id', $provider->id)
		->where('main', 1)
		->get_one();

	if (!$main_dept) {
		$main_dept = Model_Providers_Department::query()
			->related('department')
			->where('provider_id', $provider->id)
			->order_by('id', 'asc')
			->get_one();
	}

	$data['employees_department_name'] = $main_dept && $main_dept->department
		? $main_dept->department->name
		: 'No asignado';


    # DATOS FISCALES
    $data['tax_data'] = Model_Providers_Tax_Datum::query()
        ->related('cfdi')
        ->related('payment_method')
        ->related('sat_tax_regime')
        ->related('state')
        ->where('provider_id', $provider->id)
        ->get_one();

    # DIRECCIONES / ENTREGAS (VARIAS)
    $data['delivery'] = Model_Providers_Delivery::query()
        ->related('state')
        ->where('provider_id', $provider->id)
        ->where('deleted', 0)
        ->get();

    # DATOS DE COMPRAS (VARIOS)
    $data['purchases'] = Model_Providers_Purchase::query()
        ->where('provider_id', $provider->id)
        ->get();

    # DATOS DE CUENTAS POR PAGAR (VARIOS)
    $data['bank_accounts'] = Model_Providers_Account::query()
		->related('bank')
		->related('currency')
        ->where('provider_id', $provider->id)
        ->get();

    # CONTACTOS (VARIOS)
    $data['contact'] = Model_Providers_Contact::query()
		->where('provider_id', $provider->id)
		->get();

	# DEPARTAMENTOS QUE SURTE (VARIOS)
	$data['departments'] = Model_Providers_Department::query()
		->related('department')
		->where('provider_id', $provider->id)
		->where('deleted', 0)
		->order_by('main', 'desc') // primero el principal
		->order_by('id', 'asc')
		->get();


    # CARGAR LA VISTA
    $this->template->title   = 'Información del Proveedor';
    $this->template->content = View::forge('admin/proveedores/info', $data);
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
			Response::redirect('admin/proveedores');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('email', 'password', 'group', 'banned', 'name', 'rfc', 'code_sap', 'require_purchase');

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
		->related('provider')
		->where('id', $admin_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($admin))
		{
			# SI EL USUARIO NO ES ADMINISTRADOR
			if($admin->group != 100 && $admin->group != 50 && $admin->group != 25 && $admin->group != 20 && $admin->group != 10)
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/proveedores');
			}

			# SE DESERIALIZAN LOS CAMPOS EXTRAS
			$status = unserialize($admin->profile_fields);

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['username']  			= $admin->username;
			$data['email']     			= $admin->email;
			$data['group']     			= $admin->group;
			$data['name'] 	   			= $admin->provider->name;
			$data['rfc'] 	   			= $admin->provider->rfc;
			$data['code_sap']  			= $admin->provider->code_sap;
			$data['require_purchase']  	= $admin->provider->require_purchase;
			$data['banned']    			= $status['banned'];
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/proveedores');
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
			$val->add_field('name', 'nombre completo', 'required|alphabetic_spaces|min_length[3]|max_length[255]');
			$val->add_field('rfc', 'rfc', 'required|min_length[3]|max_length[255]');
			$val->add_field('code_sap', 'codigo sistema', 'min_length[0]|max_length[255]');
			$val->add_field('require_purchase', 'requiere orden', 'min_length[1]');
			$val->add_field('banned', 'baneado', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				try
				{
					# SE ESTEBLECE LA NUEVA INFORMACION
					$data_to_update = array(
						'email'     => $val->validated('email'),
						'banned'    => ($val->validated('banned')) ? true : false
					);

					# SE ACTUALIZA LA INFORMACION DEL USUARIO EN LA BASE DE DATOS
					$user = Auth::instance()->update_user($data_to_update, $admin->username);

					# SE VERIFICA SI EXISTE EL PROVEEDOR ASOCIADO
					if ($admin->provider) {
						
						# ACTUALIZAR EL PROVEEDOR
						$admin->provider->name     = strtoupper($val->validated('name')); 
						$admin->provider->rfc      = strtoupper($val->validated('rfc'));  
						$admin->provider->code_sap = !empty($val->validated('code_sap')) ? strtoupper($val->validated('code_sap')) : null;
						$admin->provider->require_purchase = !empty($val->validated('require_purchase'));
						
						# GUARDAR LOS CAMBIOS EN EL MODELO PROVIDER
						if (!$admin->provider->save()) {
							throw new Exception('No se pudo actualizar la información del proveedor.');
						}
					}


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
						Response::redirect('admin/proveedores/editar/'.$admin_id);
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
					$data['email']     			= $val->validated('email');
					$data['group']     			= $val->validated('group');
					$data['name'] 	   			= $val->validated('name');
					$data['rfc'] 	   			= $val->validated('rfc');
					$data['code_sap']  			= $val->validated('code_sap');
					$data['require_purchase']  	= $val->validated('require_purchase');
					$data['banned']    			= $val->validated('banned');
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
				$data['email']     			= Input::post('email');
				$data['group']     			= Input::post('group');
				$data['name'] 	   			= Input::post('name');
				$data['rfc'] 	   			= Input::post('rfc');
				$data['code_sap']  			= Input::post('code_sap');
				$data['require_purchase']  	= Input::post('require_purchase');
				$data['banned']    			= Input::post('banned');
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']      = $admin_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar administrador';
		$this->template->content = View::forge('admin/proveedores/editar', $data);
	}
}
