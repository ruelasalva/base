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

		# SE BUSCA LA INFORMACION DIRECTAMENTE DE LA TABLA PROVIDERS
		$query = DB::select('*')->from('providers')->where('deleted_at', null);

		# SI HAY UNA BUSQUEDA
		if ($search != '')
		{
			# SE LIMPIA LA CADENA DE BUSQUEDA
			$search = str_replace('+', ' ', rawurldecode($search));
			$search = str_replace(' ', '%', $search);

			# SE AGREGA LA CLAUSULA DE BUSQUEDA
			$query->where_open()
				->where('company_name', 'like', '%'.$search.'%')
				->or_where('email', 'like', '%'.$search.'%')
				->or_where('code', 'like', '%'.$search.'%')
				->or_where('tax_id', 'like', '%'.$search.'%')
				->where_close();
		}

		# CONTAR TOTAL
		$count_query = clone $query;
		$total_items = $count_query->select(DB::expr('COUNT(*) as count'))->execute()->get('count');

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $total_items,
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
			'show_last'      => true,
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('admins', $config);

		# SE EJECUTA EL QUERY CON PAGINACION
		$providers = $query->order_by('id', 'desc')
			->limit($pagination->per_page)
			->offset($pagination->offset)
			->execute()
			->as_array();

		# SI SE OBTIENE INFORMACION
		if (!empty($providers))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach ($providers as $provider)
			{
				# DETERMINAR ESTADO
				$status = $provider['is_suspended'] ? 'Suspendido' : 'Activo';
				$banned = $provider['is_suspended'] ? 'Sí' : 'No';

				# SE ALMACENA LA INFORMACION
				$providers_info[] = array(
					'id'        => $provider['id'],
					'username'  => $provider['code'] ? $provider['code'] : 'N/A',
					'name'      => $provider['company_name'], 
					'email'     => $provider['email'] ? $provider['email'] : 'Sin email',
					'rfc'     	=> $provider['tax_id'] ? $provider['tax_id'] : 'Sin RFC',
					'code_sap'  => $provider['code'] ? $provider['code'] : 'No asignado',
					'group'     => 'Proveedor',
					'connected' => $status,
					'banned'    => $banned
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
		->related('payment_term')
        ->where('id', $provider_id)
        ->get_one();

    # VALIDAR QUE EXISTA Y TENGA USUARIO RELACIONADO
    if (!$provider) {
        Session::set_flash('error', 'No se encontró información del proveedor.');
        Response::redirect('admin/proveedores');
    }

    # OBTENER ESTADO DE PERFIL SI EXISTE USER
    $status = [];
    $banned = 'No';
    if ($provider->user) {
        $status = @unserialize($provider->user->profile_fields) ?: [];
        $banned = (isset($status['banned']) && $status['banned']) ? 'Sí' : 'No';
    }

    
	# PASAR OBJETO PROVIDER COMPLETO + DATOS ADICIONALES
	$data['provider']     = $provider;
	$data['provider_id']  = $provider->id;
	$data['id']           = $provider->id;
	$data['banned']       = $banned;
	
	# DATOS INDIVIDUALES (para compatibilidad con vistas antiguas)
	$data['code_sap']     = $provider->code;
	$data['name']         = $provider->company_name;
	$data['rfc']          = $provider->tax_id;
	$data['email']        = $provider->email;
	$data['phone']        = $provider->phone;
	$data['employee_id']  = $provider->contact_name;
	$data['payment_terms_id'] = $provider->payment_terms ?? 0;
	$data['provider_type'] = $provider->provider_type ?? '';
	$data['origin']       = $provider->origin ?? '';

	# DEPARTAMENTOS QUE SURTE EL PROVEEDOR (Sistema de Identidades)
	$main_dept = Model_Provider_Department::get_primary($provider->id);
	$data['employees_department_name'] = $main_dept && $main_dept->department
		? $main_dept->department->name
		: 'No asignado';

	# TODOS LOS DEPARTAMENTOS DEL PROVEEDOR
	$data['departments'] = Model_Provider_Department::get_active_departments($provider->id);

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

    # CONTACTOS (VARIOS) - Con relación a usuario
    $data['contact'] = Model_Providers_Contact::query()
		->related('user')
		->where('provider_id', $provider->id)
		->get();

	# TENANTS DISPONIBLES (para modal de crear usuario)
	$data['all_tenants'] = Helper_User_Tenant::get_all_tenants();

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
			if($admin->group_id != 100 && $admin->group_id != 50 && $admin->group_id != 25 && $admin->group_id != 20 && $admin->group_id != 10)
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/proveedores');
			}

			# SE DESERIALIZAN LOS CAMPOS EXTRAS
			$status = unserialize($admin->profile_fields);

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['username']  			= $admin->username;
			$data['email']     			= $admin->email;
			$data['group']     			= $admin->group_id;
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
	
	/**
	 * DASHBOARD
	 * Métricas clave del módulo de proveedores
	 */
	public function action_dashboard()
	{
		$data = [];
		
		// Proveedores pendientes de validación
		$data['pending_validation'] = DB::select(DB::expr('COUNT(*) as count'))
			->from('providers')
			->where('is_suspended', 1)
			->where('deleted_at', null)
			->execute()
			->get('count');
		
		// Facturas pendientes
		$data['bills_pending'] = DB::select(DB::expr('COUNT(*) as count'), DB::expr('COALESCE(SUM(total), 0) as total'))
			->from('providers_bills')
			->where('status', 1)
			->where('deleted', 0)
			->execute()
			->current();
		
		// Facturas aceptadas del mes
		$start_of_month = mktime(0, 0, 0, date('m'), 1, date('Y'));
		$data['bills_accepted'] = DB::select(DB::expr('COUNT(*) as count'), DB::expr('COALESCE(SUM(total), 0) as total'))
			->from('providers_bills')
			->where('status', 2)
			->where('deleted', 0)
			->where('validated_at', '>=', $start_of_month)
			->execute()
			->current();
		
		// Facturas rechazadas del mes
		$data['bills_rejected'] = DB::select(DB::expr('COUNT(*) as count'))
			->from('providers_bills')
			->where('status', 3)
			->where('deleted', 0)
			->where('validated_at', '>=', $start_of_month)
			->execute()
			->get('count');
		
		// Contrarecibos pendientes
		$data['receipts_pending'] = DB::select(DB::expr('COUNT(*) as count'), DB::expr('COALESCE(SUM(total), 0) as total'))
			->from('providers_receipts')
			->where('deleted', 0)
			->where('status', 1)
			->where('payment_date_actual', null)
			->execute()
			->current();
		
		// Contrarecibos vencidos
		$data['receipts_overdue'] = DB::select(DB::expr('COUNT(*) as count'), DB::expr('COALESCE(SUM(total), 0) as total'))
			->from('providers_receipts')
			->where('deleted', 0)
			->where('status', 1)
			->where('programmed_payment_date', '<', time())
			->where('payment_date_actual', null)
			->execute()
			->current();
		
		// Top 5 proveedores por monto (corregido: usar company_name)
		$data['top_providers'] = DB::select(
				'p.id',
				'p.company_name',
				DB::expr('COUNT(b.id) as bill_count'),
				DB::expr('COALESCE(SUM(b.total), 0) as total_amount')
			)
			->from(array('providers', 'p'))
			->join(array('providers_bills', 'b'), 'INNER')
			->on('p.id', '=', 'b.provider_id')
			->where('b.deleted', 0)
			->where('b.created_at', '>=', $start_of_month)
			->group_by('p.id', 'p.company_name')
			->order_by('total_amount', 'DESC')
			->limit(5)
			->execute()
			->as_array();
		
		// Actividad reciente
		$data['recent_activity'] = DB::select('l.*', array('p.company_name', 'provider_name'))
			->from(array('providers_action_logs', 'l'))
			->join(array('providers', 'p'), 'LEFT')
			->on('l.provider_id', '=', 'p.id')
			->order_by('l.created_at', 'DESC')
			->limit(10)
			->execute()
			->as_array();
		
		$this->template->title = 'Dashboard - Proveedores';
		$this->template->content = View::forge('admin/proveedores/dashboard', $data);
	}
	
	/**
	 * CONFIGURACION
	 * Parámetros de facturación y pago
	 */
	public function action_config()
	{
		$data = [];
		
		// Obtener configuración actual
		$data['config'] = DB::select()
			->from('providers_billing_config')
			->where('tenant_id', 1)
			->execute()
			->current();
		
		// Si es POST, guardar
		if (Input::method() === 'POST') {
			try {
				$holidays_input = Input::post('holidays', '');
				$holidays_array = array_filter(array_map('trim', explode("\n", $holidays_input)));
				
				$config_data = array(
					'invoice_receive_days' => implode(',', Input::post('invoice_receive_days', array())),
					'invoice_receive_limit_time' => Input::post('invoice_receive_limit_time', '14:00:00'),
					'payment_terms_days' => (int)Input::post('payment_terms_days', 30),
					'payment_days' => implode(',', Input::post('payment_days', array())),
					'holidays' => json_encode($holidays_array),
					'auto_generate_receipt' => (int)Input::post('auto_generate_receipt', 0),
					'require_purchase_order' => (int)Input::post('require_purchase_order', 0),
					'max_amount_without_po' => (float)Input::post('max_amount_without_po', 5000),
					'updated_at' => DB::expr('NOW()')
				);
				
				if ($data['config']) {
					DB::update('providers_billing_config')
						->set($config_data)
						->where('tenant_id', 1)
						->execute();
				} else {
					$config_data['tenant_id'] = 1;
					$config_data['created_at'] = DB::expr('NOW()');
					DB::insert('providers_billing_config')
						->set($config_data)
						->execute();
				}
				
				Session::set_flash('success', 'Configuración actualizada correctamente');
				Response::redirect('admin/proveedores/config');
				
			} catch (Exception $e) {
				Log::error('Error guardando configuración: ' . $e->getMessage());
				Session::set_flash('error', 'Error al guardar configuración');
			}
		}
		
		// Decodificar holidays
		if ($data['config'] && !empty($data['config']['holidays'])) {
			$data['config']['holidays_text'] = implode("\n", json_decode($data['config']['holidays'], true));
		} else {
			$data['config']['holidays_text'] = '';
		}
		
		$this->template->title = 'Configuración - Facturación y Pago';
		$this->template->content = View::forge('admin/proveedores/config', $data);
	}
	
	/**
	 * SUSPENDER (AJAX)
	 */
	public function action_suspend($id = null)
	{
		if (Input::method() !== 'POST') {
			echo json_encode(array('error' => 'Método no permitido'));
			exit;
		}
		
		$reason = Input::post('reason');
		
		if (empty($reason)) {
			echo json_encode(array('error' => 'Debe especificar una razón'));
			exit;
		}
		
		try {
			$admin_id = Auth::get('id');
			
			DB::update('providers')
				->set(array(
					'is_suspended' => 1,
					'suspended_reason' => $reason,
					'suspended_at' => DB::expr('NOW()'),
					'updated_at' => DB::expr('NOW()')
				))
				->where('id', $id)
				->execute();
			
			Helper_ProviderLog::log_provider_suspension($id, $reason, $admin_id);
			
			echo json_encode(array(
				'success' => true,
				'message' => 'Cuenta suspendida correctamente'
			));
			
		} catch (Exception $e) {
			Log::error('Error suspendiendo proveedor: ' . $e->getMessage());
			echo json_encode(array('error' => 'Error al suspender cuenta'));
		}
		
		exit;
	}
	
	/**
	 * ACTIVAR (AJAX)
	 */
	public function action_activate($id = null)
	{
		if (Input::method() !== 'POST') {
			echo json_encode(array('error' => 'Método no permitido'));
			exit;
		}
		
		try {
			$admin_id = Auth::get('id');
			
			DB::update('providers')
				->set(array(
					'is_suspended' => 0,
					'suspended_reason' => null,
					'activated_at' => DB::expr('NOW()'),
					'activated_by' => $admin_id,
					'updated_at' => DB::expr('NOW()')
				))
				->where('id', $id)
				->execute();
			
			Helper_ProviderLog::log_provider_activation($id, $admin_id);
			
			echo json_encode(array(
				'success' => true,
				'message' => 'Cuenta activada correctamente'
			));
			
		} catch (Exception $e) {
			Log::error('Error activando proveedor: ' . $e->getMessage());
			echo json_encode(array('error' => 'Error al activar cuenta'));
		}
		
		exit;
	}
	
	/**
	 * RESETEAR CONTRASEÑA (AJAX)
	 */
	public function action_reset_password($id = null)
	{
		if (Input::method() !== 'POST') {
			echo json_encode(array('error' => 'Método no permitido'));
			exit;
		}
		
		try {
			$provider = DB::select('email', 'company_name')
				->from('providers')
				->where('id', $id)
				->execute()
				->current();
			
			if (!$provider || empty($provider['email'])) {
				echo json_encode(array('error' => 'Proveedor no encontrado o sin email'));
				exit;
			}
			
			// Generar token
			$token = bin2hex(random_bytes(32));
			$expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
			
			DB::insert('providers_email_confirmations')
				->set(array(
					'provider_id' => $id,
					'email' => $provider['email'],
					'token' => $token,
					'expires_at' => $expires_at,
					'ip_address' => Input::real_ip(),
					'created_at' => DB::expr('NOW()')
				))
				->execute();
			
			Helper_ProviderLog::log_password_reset_request($id, $provider['email']);
			
			// TODO: Enviar email
			
			echo json_encode(array(
				'success' => true,
				'message' => 'Se ha enviado un email con instrucciones'
			));
			
		} catch (Exception $e) {
			Log::error('Error generando token: ' . $e->getMessage());
			echo json_encode(array('error' => 'Error al generar token'));
		}
		
		exit;
	}

	/**
	 * GESTIÓN DE ACCESO AL PORTAL DEL PROVEEDOR
	 */
	public function action_manage_access($provider_id = null)
	{
		if (!$provider_id) {
			Session::set_flash('error', 'ID de proveedor no válido');
			Response::redirect('admin/proveedores');
		}

		$provider = Model_Provider::find($provider_id);
		if (!$provider) {
			Session::set_flash('error', 'Proveedor no encontrado');
			Response::redirect('admin/proveedores');
		}

		$data = array();
		$data['provider'] = $provider;

		// Verificar si tiene usuario asociado
		$user = $provider->get_user();
		$data['user'] = $user;

		// Si tiene usuario, obtener sus tenants
		if ($user) {
			$data['user_tenants'] = Helper_User_Tenant::get_user_tenants($user->id);
		} else {
			$data['user_tenants'] = array();
		}

		// Obtener todos los tenants disponibles
		$data['all_tenants'] = Helper_User_Tenant::get_all_tenants();

		$this->template->title = 'Gestionar Acceso - ' . $provider->company_name;
		$this->template->content = View::forge('admin/proveedores/manage_access', $data);
	}

	/**
	 * CREAR USUARIO PARA PROVEEDOR
	 */
	public function action_create_user()
	{
		if (Input::method() !== 'POST') {
			Response::redirect('admin/proveedores');
		}

		$provider_id = Input::post('provider_id');
		$provider = Model_Provider::find($provider_id);

		if (!$provider) {
			Session::set_flash('error', 'Proveedor no encontrado');
			Response::redirect('admin/proveedores');
		}

		// Verificar si ya tiene usuario
		if ($provider->get_user()) {
			Session::set_flash('error', 'Este proveedor ya tiene usuario asignado');
			Response::redirect('admin/proveedores/manage_access/' . $provider_id);
		}

		try {
			// Crear usuario
			$username = 'prov_' . strtolower($provider->code);
			$password = Input::post('password', 'temporal123');

			$user = Model_User::forge(array(
				'username' => $username,
				'email' => $provider->email,
				'password' => Auth::hash_password($password),
				'group_id' => Input::post('group_id', 50), // Grupo "Proveedores"
				'tenant_id' => $provider->tenant_id,
				'first_name' => $provider->contact_name ?: $provider->company_name,
				'last_name' => '',
				'is_active' => 1,
				'is_verified' => 1
			));
			$user->save();

			// Crear identidad
			Model_User_Identity::create_identity(
				$user->id,
				'provider',
				$provider->id,
				true, // Es primary
				true, // Puede login
				Input::post('access_level', 'readonly')
			);

			// Asignar tenants seleccionados
			$selected_tenants = Input::post('tenants', array());
			$default_tenant = Input::post('default_tenant', $provider->tenant_id);

			if (empty($selected_tenants)) {
				// Si no seleccionó ninguno, asignar solo el del proveedor
				Helper_User_Tenant::assign($user->id, $provider->tenant_id, true);
			} else {
				foreach ($selected_tenants as $tenant_id) {
					$is_default = ($tenant_id == $default_tenant);
					Helper_User_Tenant::assign($user->id, $tenant_id, $is_default);
				}
			}

			Session::set_flash('success', 'Usuario creado exitosamente. Username: ' . $username);
			Response::redirect('admin/proveedores/manage_access/' . $provider_id);

		} catch (Exception $e) {
			Log::error('Error creando usuario para proveedor: ' . $e->getMessage());
			Session::set_flash('error', 'Error al crear usuario: ' . $e->getMessage());
			Response::redirect('admin/proveedores/manage_access/' . $provider_id);
		}
	}

	/**
	 * ACTUALIZAR TENANTS DE UN USUARIO
	 */
	public function action_update_tenants()
	{
		if (Input::method() !== 'POST') {
			Response::redirect('admin/proveedores');
		}

		$user_id = Input::post('user_id');
		$provider_id = Input::post('provider_id');

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
			Response::redirect('admin/proveedores/manage_access/' . $provider_id);

		} catch (Exception $e) {
			Log::error('Error actualizando tenants: ' . $e->getMessage());
			Session::set_flash('error', 'Error al actualizar acceso');
			Response::redirect('admin/proveedores/manage_access/' . $provider_id);
		}
	}

	/**
	 * ELIMINAR USUARIO DE PROVEEDOR
	 */
	public function action_delete_user()
	{
		if (Input::method() !== 'POST') {
			echo json_encode(array('error' => 'Método no permitido'));
			exit;
		}

		$user_id = Input::post('user_id');
		$provider_id = Input::post('provider_id');

		try {
			// Desactivar usuario
			DB::update('users')
				->set(array('is_active' => 0, 'deleted_at' => DB::expr('NOW()')))
				->where('id', $user_id)
				->execute();

			// Las identities se eliminan automáticamente por CASCADE

			echo json_encode(array(
				'success' => true,
				'message' => 'Usuario eliminado correctamente'
			));

		} catch (Exception $e) {
			Log::error('Error eliminando usuario: ' . $e->getMessage());
			echo json_encode(array('error' => 'Error al eliminar usuario'));
		}

		exit;
	}

	/**
	 * CREAR USUARIO PARA CONTACTO
	 */
	public function action_create_contact_user()
	{
		if (Input::method() !== 'POST') {
			Session::set_flash('error', 'Método no permitido');
			Response::redirect('admin/proveedores');
		}

		$contact_id = Input::post('contact_id');
		$provider_id = Input::post('provider_id');
		$password = Input::post('password');
		$tenant_ids = Input::post('tenant_ids', array());
		$default_tenant = Input::post('default_tenant');
		$access_level = Input::post('access_level', 'readonly');

		try {
			// Obtener contacto
			$contact = Model_Providers_Contact::find($contact_id);
			if (!$contact) {
				Session::set_flash('error', 'Contacto no encontrado');
				Response::redirect('admin/proveedores/info/' . $provider_id);
			}

			// Verificar si ya tiene usuario
			if ($contact->has_user()) {
				Session::set_flash('error', 'El contacto ya tiene un usuario asignado');
				Response::redirect('admin/proveedores/info/' . $provider_id);
			}

			// Obtener proveedor
			$provider = Model_Provider::find($provider_id);
			if (!$provider) {
				Session::set_flash('error', 'Proveedor no encontrado');
				Response::redirect('admin/proveedores');
			}

			// Generar username: prov_codigosap_nombre
			$base_username = 'prov_' . strtolower($provider->code) . '_' . strtolower($contact->name);
			$base_username = preg_replace('/[^a-z0-9_]/', '', $base_username);
			$username = $base_username;
			$counter = 1;

			// Verificar username único
			while (Model_User::query()->where('username', $username)->count() > 0) {
				$username = $base_username . $counter;
				$counter++;
			}

			DB::start_transaction();

			// Crear usuario
			$user = Model_User::forge(array(
				'username' => $username,
				'password' => Auth::hash_password($password),
				'name' => $contact->get_full_name(),
				'email' => $contact->email,
				'group_id' => 50, // Grupo Proveedores
				'is_active' => 1,
				'created_at' => time(),
				'updated_at' => time(),
			));
			$user->save();

			// Crear identidad
			Model_User_Identity::create_identity(
				$user->id,
				'provider',
				$provider_id,
				false, // No es primaria (la principal es del proveedor)
				1, // Puede hacer login
				$access_level
			);

			// Asignar tenants
			if (!empty($tenant_ids)) {
				foreach ($tenant_ids as $tenant_id) {
					$is_default = ($tenant_id == $default_tenant);
					Helper_User_Tenant::assign($user->id, $tenant_id, $is_default);
				}
			}

			// Actualizar contacto
			$contact->user_id = $user->id;
			$contact->updated_at = time();
			$contact->save();

			DB::commit_transaction();

			Session::set_flash('success', 'Usuario creado correctamente. Username: ' . $username);
			Response::redirect('admin/proveedores/info/' . $provider_id . '#panel-contactos-proveedor');

		} catch (Exception $e) {
			DB::rollback_transaction();
			Log::error('[CONTACT USER] Error: ' . $e->getMessage());
			Session::set_flash('error', 'Error al crear usuario para el contacto');
			Response::redirect('admin/proveedores/info/' . $provider_id);
		}
	}

	/**
	 * ELIMINAR USUARIO DE CONTACTO
	 */
	public function action_delete_contact_user()
	{
		if (Input::method() !== 'POST') {
			echo json_encode(array('error' => 'Método no permitido'));
			exit;
		}

		$contact_id = Input::post('contact_id');
		$provider_id = Input::post('provider_id');

		try {
			$contact = Model_Providers_Contact::find($contact_id);
			if (!$contact || !$contact->has_user()) {
				echo json_encode(array('error' => 'Contacto sin usuario'));
				exit;
			}

			DB::start_transaction();

			// Desactivar usuario
			DB::update('users')
				->set(array('is_active' => 0, 'deleted_at' => DB::expr('NOW()')))
				->where('id', $contact->user_id)
				->execute();

			// Limpiar referencia en contacto
			$contact->user_id = null;
			$contact->updated_at = time();
			$contact->save();

			DB::commit_transaction();

			echo json_encode(array(
				'success' => true,
				'message' => 'Usuario eliminado correctamente'
			));

		} catch (Exception $e) {
			DB::rollback_transaction();
			Log::error('[DELETE CONTACT USER] ' . $e->getMessage());
			echo json_encode(array('error' => 'Error al eliminar usuario'));
		}

		exit;
	}

	/**
	 * GESTIONAR TENANTS DE CONTACTO
	 */
	public function action_manage_contact_tenants($contact_id = null)
	{
		if (!$contact_id) {
			Session::set_flash('error', 'ID de contacto no válido');
			Response::redirect('admin/proveedores');
		}

		$contact = Model_Providers_Contact::query()
			->related('user')
			->related('provider')
			->where('id', $contact_id)
			->get_one();

		if (!$contact || !$contact->has_user()) {
			Session::set_flash('error', 'Contacto sin usuario');
			Response::redirect('admin/proveedores');
		}

		$data = array(
			'contact' => $contact,
			'provider' => $contact->provider,
			'user' => $contact->user,
			'user_tenants' => Helper_User_Tenant::get_user_tenants($contact->user_id),
			'all_tenants' => Helper_User_Tenant::get_all_tenants(),
		);

		$this->template->title = 'Gestionar Backends - ' . $contact->get_full_name();
		$this->template->content = View::forge('admin/proveedores/manage_contact_tenants', $data, false);
	}

	/**
	 * ACTUALIZAR TENANTS DE CONTACTO
	 */
	public function action_update_contact_tenants()
	{
		if (Input::method() !== 'POST') {
			Session::set_flash('error', 'Método no permitido');
			Response::redirect('admin/proveedores');
		}

		$contact_id = Input::post('contact_id');
		$provider_id = Input::post('provider_id');
		$selected_tenants = Input::post('tenant_ids', array());
		$default_tenant = Input::post('default_tenant');

		try {
			$contact = Model_Providers_Contact::find($contact_id);
			if (!$contact || !$contact->has_user()) {
				Session::set_flash('error', 'Contacto sin usuario');
				Response::redirect('admin/proveedores/info/' . $provider_id);
			}

			// Desactivar todos los tenants actuales
			DB::update('user_tenants')
				->set(array('is_active' => 0, 'updated_at' => time()))
				->where('user_id', $contact->user_id)
				->execute();

			// Asignar los seleccionados
			foreach ($selected_tenants as $tenant_id) {
				$is_default = ($tenant_id == $default_tenant);
				Helper_User_Tenant::assign($contact->user_id, $tenant_id, $is_default);
			}

			Session::set_flash('success', 'Acceso a backends actualizado correctamente');
			Response::redirect('admin/proveedores/manage_contact_tenants/' . $contact_id);

		} catch (Exception $e) {
			Log::error('[UPDATE CONTACT TENANTS] ' . $e->getMessage());
			Session::set_flash('error', 'Error al actualizar acceso');
			Response::redirect('admin/proveedores/info/' . $provider_id);
		}
	}
}
