<?php

/**
 * CONTROLADOR ADMIN_USUARIOS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Usuarios extends Controller_Admin
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
		if(!Auth::member(100) && !Auth::member(50) && !Auth::member(25))
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
		$users = Model_User::query()
		->related('customer')
		->where('group', 1);

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
			$users = $users->where(DB::expr("CONCAT(`t0`.`username`, ' ', `t0`.`email`,' ', `t1`.`id`)"), 'like', '%'.$search.'%');
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
				# SE DESERIALIZAN LOS CAMPOS EXTRAS
				$status = unserialize($user->profile_fields);

				# SE ALMACENA LA INFORMACION
				$users_info[] = array(
					'id'       	=> $user->id,
					'customer_id'  => $user->customer->id,
					'username' 	=> $user->username,
					'name'     	=> $user->customer->name.' '.$user->customer->last_name,
					'email'    	=> $user->email,
					'type'     	=> $user->customer->type->name,
					'sap_code' => $user->customer->sap_code,
					'connected' => ($status['connected']) ? 'Conectado' : 'Desconectado',
					'banned'    => ($status['banned']) ? 'Sí' : 'No',
					'updated_at' => date('d/m/Y - H:i', $user->updated_at),
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['users']      = $users_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Usuarios';
		$this->template->content = View::forge('admin/usuarios/index', $data, false);
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
				Response::redirect('admin/usuarios/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/usuarios');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
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
			Response::redirect('admin/usuarios');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data           = array();
		$addresses_info = array();
		$tax_data_info  = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$user = Model_User::query()
		->where('id', $user_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($user))
		{
			# SI EXISTEN DIRECCIONES
			if(!empty($user->customer->addresses))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($user->customer->addresses as $address)
				{
					# SE ALMACENA LA INFORMACION
					$addresses_info[] = array(
						'id'      => $address->id,
						'street'  => $address->street.' '.$address->number,
						'colony'  => $address->colony,
						'zipcode' => $address->zipcode,
						'city'    => $address->city,
						'state'   => $address->state->name
					);
				}
			}

			# SI EXISTEN DATOS DE FACTURACION
			if(!empty($user->customer->tax_data))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($user->customer->tax_data as $tax_datum)
				{
					# SE ALMACENA LA INFORMACION
					$tax_data_info[] = array(
						'id'              => $tax_datum->id,
						'rfc'             => $tax_datum->rfc,
						'business_name'   => $tax_datum->business_name,
						'address'         => ($tax_datum->internal_number != '') ? $tax_datum->street.' #'.$tax_datum->number.', Int.'.$tax_datum->internal_number : $tax_datum->street.' #'.$tax_datum->number,
						'default'         => ($tax_datum->default == 1) ? 'Sí' : 'No'
					);
				}
			}

			# SE ORDENA EL ARREGLO
            $tax_data_info = Arr::sort($tax_data_info, 'default', 'desc');

			# SE DESERIALIZAN LOS CAMPOS EXTRAS
			$status = unserialize($user->profile_fields);

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['username']     = $user->username;
			$data['email']        = $user->email;
			$data['name']         = $user->customer->name;
			$data['last_name']    = $user->customer->last_name;
			$data['phone']        = ($user->customer->phone != '') ? $user->customer->phone : 'N/A';
			$data['type']         = $user->customer->type->name;
			$data['sap_code']    = $user->customer->sap_code;
			$data['addresses']    = $addresses_info;
			$data['tax_data']     = $tax_data_info;
			$data['require_bill'] = ($user->customer->require_bill) ? 'Sí' : 'No';
			$data['banned']       = ($status['banned']) ? 'Sí' : 'No';
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $user_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del usuario';
		$this->template->content = View::forge('admin/usuarios/info', $data);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($user_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($user_id == 0 || !is_numeric($user_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('email', 'name', 'banned','last_name', 'phone', 'sap_code', 'type', 'require_bill');

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
		$user = Model_User::query()
		->related('customer')
		->where('id', $user_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($user))
		{
			# SE DESERIALIZAN LOS CAMPOS EXTRAS
			$status = unserialize($user->profile_fields);

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['username']     = $user->username;
			$data['email']        = $user->email;
			$data['name']         = $user->customer->name;
			$data['last_name']    = $user->customer->last_name;
			$data['phone']        = ($user->customer->phone != '') ? $user->customer->phone : 'N/A';
			$data['type']         = $user->customer->type_id;
			$data['sap_code']    = $user->customer->sap_code;
			$data['require_bill'] = $user->customer->require_bill;
			$data['banned']       = $status['banned'];
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('user');
			$val->add_callable('Rules');
			$val->add_field('email', 'email', 'required|min_length[1]|valid_email');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');
            $val->add_field('last_name', 'apellidos', 'required|min_length[1]|max_length[255]');
            $val->add_field('phone', 'teléfono', 'required|min_length[1]|max_length[255]');
            $val->add_field('type', 'tipo de cliente', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('sap_code', 'codigo sap', 'min_length[1]|max_length[7]');
			$val->add_field('require_bill', 'factura en todas las compras', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');
			$val->add_field('banned', 'baneado', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				try
				{
					# SE ESTEBLECE LA NUEVA INFORMACION
					$data_to_update = array(
						'email' => $val->validated('email'),
						'banned'=> ($val->validated('banned')) ? true : false
					);

					# SE ACTUALIZA LA INFORMACION DEL USUARIO EN LA BASE DE DATOS
					$user_auth = Auth::instance()->update_user($data_to_update, $user->username);

					# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
					if($user_auth)
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$customer = Model_Customer::query()
						->where('user_id', $user_id)
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($customer))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$customer->name         = $val->validated('name');
							$customer->last_name    = $val->validated('last_name');
							$customer->phone        = $val->validated('phone');
							$customer->type_id      = $val->validated('type');
							$customer->sap_code    = $val->validated('sap_code');
							$customer->require_bill = $val->validated('require_bill');
							$customer->banned       = $val->validated('banned');

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($customer->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								Session::set_flash('success', 'Se actualizó la información del usuario correctamente.');

								# SE REDIRECCIONA AL USUARIO
								Response::redirect('admin/usuarios/editar/'.$user_id);
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							Session::set_flash('error', 'No se encontró la información del cliente asociada al usuario.');

							# SE RECORRE CLASE POR CLASE
							foreach($classes as $name => $class)
							{
								# SE ESTABLECE EL VALOR DE LAS CLASES
								$classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
								$classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';

								# SE ALMACENA LA INFORMACION PARA LA VISTA
								$data[$name] = Input::post($name);
							}
						}
					}
				}
				catch(\SimpleUserUpdateException $e)
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', 'El correo electrónico <b>'.$val->validated('email').'</b> ya está asociado a otra cuenta, por favor escribe una dirección de correo electrónico diferente.');

					# SE RECORRE CLASE POR CLASE
					foreach($classes as $name => $class)
					{
						# SE ESTABLECE EL VALOR DE LAS CLASES
						$classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
						$classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';

						# SE ALMACENA LA INFORMACION PARA LA VISTA
						$data[$name] = Input::post($name);
					}
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

					# SE ALMACENA LA INFORMACION PARA LA VISTA
					$data[$name] = Input::post($name);
				}
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']        = $user_id;
		$data['classes']   = $classes;
		$data['type_opts'] = Model_Customers_Type::get_for_input();

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar usuario';
		$this->template->content = View::forge('admin/usuarios/editar', $data);
	}


	/**
	 * EDITAR_DIRECCION
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar_direccion($user_id = 0, $address_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($user_id == 0 || !is_numeric($user_id) || $address_id == 0 || !is_numeric($address_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('name', 'last_name', 'phone', 'street', 'number', 'banned','codigosap','internal_number', 'colony', 'zipcode', 'city', 'state', 'details');

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
		$user = Model_User::query()
		->where('id', $user_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($user))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$address = Model_Customers_Address::query()
			->where('customer_id', $user->customer->id)
			->where('id', $address_id)
			->get_one();

			# SI SE OBTIENE INFORMACION
			if(!empty($address))
			{
				# SE DESERIALIZAN LOS CAMPOS EXTRAS
				$status = unserialize($user->profile_fields);

				# SE ALMACENA LA INFORMACION PARA LA VISTA
				$data['username']        = $user->username;
				$data['state']           = $address->state_id;
				$data['name']            = $address->name;
				$data['last_name']       = $address->last_name;
				$data['phone']           = $address->phone;
				$data['street']          = $address->street;
				$data['number']          = $address->number;
				$data['internal_number'] = $address->internal_number;
				$data['colony']          = $address->colony;
				$data['zipcode']         = $address->zipcode;
				$data['city']            = $address->city;
				$data['details']         = $address->details;
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/usuarios');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('address');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('last_name', 'apellidos', 'required|min_length[1]|max_length[255]');
			$val->add_field('phone', 'teléfono', 'required|min_length[1]|max_length[255]');
			$val->add_field('street', 'calle', 'required|min_length[1]|max_length[255]');
			$val->add_field('number', '# exterior', 'required|min_length[1]|max_length[255]');
			$val->add_field('internal_number', '# interior', 'min_length[1]|max_length[255]');
			$val->add_field('colony', 'colonia', 'required|min_length[1]|max_length[255]');
			$val->add_field('zipcode', 'código postal', 'required|min_length[1]|max_length[255]');
			$val->add_field('city', 'ciudad', 'required|min_length[1]|max_length[255]');
			$val->add_field('state', 'estado', 'required|valid_string[numeric]|numeric_between[1,32]');
			$val->add_field('details', 'información adicional', 'min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$address->state_id        = $val->validated('state');
				$address->name            = $val->validated('name');
				$address->last_name       = $val->validated('last_name');
				$address->phone           = $val->validated('phone');
				$address->street          = $val->validated('street');
				$address->number          = $val->validated('number');
				$address->internal_number = $val->validated('internal_number');
				$address->colony          = $val->validated('colony');
				$address->zipcode         = $val->validated('zipcode');
				$address->city            = $val->validated('city');
				$address->details         = $val->validated('details');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($address->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de la dirección correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/usuarios/editar_direccion/'.$user_id.'/'.$address_id);
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

					# SE ALMACENA LA INFORMACION PARA LA VISTA
					$data[$name] = Input::post($name);
				}
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']         = $address_id;
		$data['user_id']    = $user_id;
		$data['state_opts'] = Model_State::get_for_input();
		$data['classes']    = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar dirección';
		$this->template->content = View::forge('admin/usuarios/editar_direccion', $data);
	}


	/**
	 * AGREGAR_FACTURACION
	 *
	 * PERMITE AGREGAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_agregar_facturacion($user_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($user_id == 0 || !is_numeric($user_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('business_name', 'rfc', 'street', 'number', 'internal_number', 'colony', 'zipcode', 'city', 'state', 'payment_method', 'cfdi', 'sat_tax_regime', 'csf', 'default');

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
		$user = Model_User::query()
		->where('id', $user_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($user))
		{
			# SE OBTIENEN LA INFORMACION DEL CLIENTE
	        $customer = Model_Customer::get_one(array('id_user' => $user_id));

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['username'] = $user->username;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE INICIALIZAN LAS VARIABLES
			$csf = '';

			# SE OBTIENE LA REFERENCIA DE LA CSF
			$csf = $_FILES['csf']['name'];

			# SI EL USUARIO SUBE LA CSF
			if(!empty($csf))
			{
				# SE ESTABLECE LA CONFIGURACION PARA LOS ARCHIVOS
				$csf_config = array(
					'auto_process'        => false,
					'path'                => DOCROOT.DS.'assets/csf',
					'randomize'           => true,
					'auto_rename'         => true,
					'normalize'           => true,
					'normalize_separator' => '-',
					'ext_whitelist'       => array('pdf'),
					'max_size'            => 20971520,
				);

				# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
				Upload::process($csf_config);

				# SI EL ARCHIVO ES VALIDO
				if(Upload::is_valid())
				{
					# SE SUBE EL ARCHIVO
					Upload::save();

					# SE OBTIENE LA INFORMACION DE LOS ARCHIVOS
					$value = Upload::get_files();

					# SE ALMACENA EL NOMBRE DEL ARCHIVO
					$csf = (isset($value[0]['saved_as'])) ? $value[0]['saved_as'] : '';
				}
			}

			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('tax_data');
            $val->add_callable('rules');
			$val->add_field('business_name', 'Razón social', 'required|min_length[1]|max_length[255]');
			$val->add_field('rfc', 'RFC', 'required|min_length[1]|max_length[255]');
			$val->add_field('street', 'Calle', 'required|min_length[1]|max_length[255]');
			$val->add_field('number', '# Exterior', 'required|min_length[1]|max_length[255]');
			$val->add_field('internal_number', '# Interior', 'min_length[1]|max_length[255]');
			$val->add_field('colony', 'Colonia', 'required|min_length[1]|max_length[255]');
			$val->add_field('zipcode', 'Código postal', 'required|min_length[1]|max_length[255]');
			$val->add_field('city', 'Ciudad', 'required|min_length[1]|max_length[255]');
			$val->add_field('state', 'Estado', 'required|valid_string[numeric]|numeric_between[1,32]');
			$val->add_field('payment_method', 'Forma de pago', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('sat_tax_regime', 'Régimen fiscal', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('cfdi', 'Uso del CFDI', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('default', 'Default', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SI EL RFC FUE MARCADO COMO PREDETERMINADO
                if($val->validated('default') == 1)
                {
                    # SE BUSCA SI HAY UNA REGISTRO POR DEFECTO
                    $query = array(
                        'id_customer' => $customer->id,
                        'default'     => 1
                    );

					# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
                    $default_rfc = Model_Customers_Tax_Datum::get_one($query);

                    # SI SE OBTUVO INFORMACION
                    if(!empty($default_rfc))
                    {
                        # SE CAMBIA EL VALOR DEFAULT A 0
                        Model_Customers_Tax_Datum::do_update(array('default' => 0), $default_rfc->id);
                    }
                }

				# SE CREA EL MODELO CON LA INFORMACION
				$tax_datum = new Model_Customers_Tax_Datum(array(
					'customer_id'       => $customer->id,
					'payment_method_id' => $val->validated('payment_method'),
					'cfdi_id'           => $val->validated('cfdi'),
					'sat_tax_regime_id' => $val->validated('sat_tax_regime'),
					'state_id'          => $val->validated('state'),
					'business_name'     => $val->validated('business_name'),
					'rfc'               => $val->validated('rfc'),
					'street'            => $val->validated('street'),
					'number'            => $val->validated('number'),
					'internal_number'   => $val->validated('internal_number'),
					'colony'            => $val->validated('colony'),
					'zipcode'           => $val->validated('zipcode'),
					'city'              => $val->validated('city'),
					'csf'               => $csf,
					'default'           => $val->validated('default')
				));

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($tax_datum->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el RFC '.$val->validated('rfc').' correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/usuarios/info/'.$user_id);
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

					# SE ALMACENA LA INFORMACION PARA LA VISTA
					$data[$name] = Input::post($name);
				}
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['user_id']              = $user_id;
		$data['states_opts']          = Model_State::get_for_input();
		$data['payment_methods_opts'] = Model_Payments_Method::get_for_input();
		$data['cfdis_opts']           = Model_Cfdi::get_for_input();
		$data['sat_tax_regimes_opts'] = Model_Sat_Tax_Regime::get_for_input();
		$data['classes']              = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar datos de facturación';
		$this->template->content = View::forge('admin/usuarios/agregar_facturacion', $data);
	}


	/**
	 * EDITAR_FACTURACION
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar_facturacion($user_id = 0, $tax_datum_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($user_id == 0 || !is_numeric($user_id) || $tax_datum_id == 0 || !is_numeric($tax_datum_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('business_name', 'rfc', 'street', 'number', 'internal_number', 'colony', 'zipcode', 'city', 'state', 'payment_method', 'cfdi', 'sat_tax_regime', 'csf', 'default');

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
		$user = Model_User::query()
		->where('id', $user_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($user))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$tax_datum = Model_Customers_Tax_Datum::query()
			->where('customer_id', $user->customer->id)
			->where('id', $tax_datum_id)
			->get_one();

			# SI SE OBTIENE INFORMACION
			if(!empty($tax_datum))
			{
				# SE ALMACENA LA INFORMACION PARA LA VISTA
				$data['username']        = $user->username;
				$data['business_name']   = $tax_datum->business_name;
				$data['rfc']             = $tax_datum->rfc;
				$data['street']          = $tax_datum->street;
				$data['number']          = $tax_datum->number;
				$data['internal_number'] = $tax_datum->internal_number;
				$data['colony']          = $tax_datum->colony;
				$data['zipcode']         = $tax_datum->zipcode;
				$data['city']            = $tax_datum->city;
				$data['state']           = $tax_datum->state_id;
				$data['payment_method']  = $tax_datum->payment_method_id;
				$data['cfdi']            = $tax_datum->cfdi_id;
				$data['sat_tax_regime']  = $tax_datum->sat_tax_regime_id;
				$data['default']         = $tax_datum->default;
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/usuarios');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE INICIALIZAN LAS VARIABLES
			$csf = '';

			# SE OBTIENE LA REFERENCIA DEL CV
			$csf = $_FILES['csf']['name'];

			# SI EL USUARIO SUBE EL CV
			if(!empty($csf))
			{
				# SE ESTABLECE LA CONFIGURACION PARA LOS ARCHIVOS
				$csf_config = array(
					'auto_process'        => false,
					'path'                => DOCROOT.DS.'assets/csf',
					'randomize'           => true,
					'auto_rename'         => true,
					'normalize'           => true,
					'normalize_separator' => '-',
					'ext_whitelist'       => array('pdf'),
					'max_size'            => 20971520,
				);

				# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
				Upload::process($csf_config);

				# SI EL ARCHIVO ES VALIDO
				if(Upload::is_valid())
				{
					# SE SUBE EL ARCHIVO
					Upload::save();

					# SE OBTIENE LA INFORMACION DE LOS ARCHIVOS
					$value = Upload::get_files();

					# SE ALMACENA EL NOMBRE DEL ARCHIVO
					$csf = (isset($value[0]['saved_as'])) ? $value[0]['saved_as'] : '';
				}
			}

			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('tax_data');
            $val->add_callable('rules');
			$val->add_field('business_name', 'Razón social', 'required|min_length[1]|max_length[255]');
			$val->add_field('rfc', 'RFC', 'required|min_length[1]|max_length[255]');
			$val->add_field('street', 'Calle', 'required|min_length[1]|max_length[255]');
			$val->add_field('number', '# Exterior', 'required|min_length[1]|max_length[255]');
			$val->add_field('internal_number', '# Interior', 'min_length[1]|max_length[255]');
			$val->add_field('colony', 'Colonia', 'required|min_length[1]|max_length[255]');
			$val->add_field('zipcode', 'Código postal', 'required|min_length[1]|max_length[255]');
			$val->add_field('city', 'Ciudad', 'required|min_length[1]|max_length[255]');
			$val->add_field('state', 'Estado', 'required|valid_string[numeric]|numeric_between[1,32]');
			$val->add_field('payment_method', 'Forma de pago', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('sat_tax_regime', 'Régimen fiscal', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('cfdi', 'Uso del CFDI', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('default', 'Default', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SI EL RFC FUE MARCADO COMO PREDETERMINADO
                if($val->validated('default') == 1)
                {
                    # SE BUSCA SI HAY UNA REGISTRO POR DEFECTO
                    $query = array(
                        'id_customer' => $customer->id,
                        'default'     => 1
                    );

					# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
                    $default_rfc = Model_Customers_Tax_Datum::get_one($query);

                    # SI SE OBTUVO INFORMACION
                    if(!empty($default_rfc))
                    {
                        # SE CAMBIA EL VALOR DEFAULT A 0
                        Model_Customers_Tax_Datum::do_update(array('default' => 0), $default_rfc->id);
                    }
                }

				# SI HAY UNA CONSTANSIA DE SITUACION FISCAL NUEVA
				if($csf != '')
				{
					# SI EXISTE LA CONSTANCIA DE SITUACION FISCAL
					if($tax_datum->csf != '')
					{
						# SI EL ARCHIVO RECIEN SUBIDO EXISTE
						if(file_exists(DOCROOT.'assets/csf/'.$tax_datum->csf.'.pdf'))
						{
							# SE ELIMINAN EL ARCHIVO
							File::delete(DOCROOT.'assets/csf/'.$tax_datum->csf.'.pdf');
						}
					}
				}

				# SE ESTEBLECE LA NUEVA INFORMACION
				$tax_datum->payment_method_id = $val->validated('payment_method');
				$tax_datum->cfdi_id           = $val->validated('cfdi');
				$tax_datum->sat_tax_regime_id = $val->validated('sat_tax_regime');
				$tax_datum->state_id          = $val->validated('state');
				$tax_datum->business_name     = $val->validated('business_name');
				$tax_datum->rfc               = $val->validated('rfc');
				$tax_datum->street            = $val->validated('street');
				$tax_datum->number            = $val->validated('number');
				$tax_datum->internal_number   = $val->validated('internal_number');
				$tax_datum->colony            = $val->validated('colony');
				$tax_datum->zipcode           = $val->validated('zipcode');
				$tax_datum->city              = $val->validated('city');
				$tax_datum->csf               = ($csf != '') ? $csf : $tax_datum->csf;
				$tax_datum->default           = $val->validated('default');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($tax_datum->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de facturación correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/usuarios/editar_facturacion/'.$user_id.'/'.$tax_datum_id);
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

					# SE ALMACENA LA INFORMACION PARA LA VISTA
					$data[$name] = Input::post($name);
				}
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']                   = $tax_datum_id;
		$data['user_id']              = $user_id;
		$data['states_opts']          = Model_State::get_for_input();
		$data['payment_methods_opts'] = Model_Payments_Method::get_for_input();
		$data['cfdis_opts']           = Model_Cfdi::get_for_input();
		$data['sat_tax_regimes_opts'] = Model_Sat_Tax_Regime::get_for_input();
		$data['classes']              = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar datos de facturación';
		$this->template->content = View::forge('admin/usuarios/editar_facturacion', $data);
	}


	/**
	 * ELIMINAR_FACTURACION
	 *
	 * PERMITE ELIMINAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar_facturacion($user_id = 0, $tax_datum_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($user_id == 0 || !is_numeric($user_id) || $tax_datum_id == 0 || !is_numeric($tax_datum_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$user = Model_User::query()
		->where('id', $user_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($user))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$tax_datum = Model_Customers_Tax_Datum::query()
			->where('customer_id', $user->customer->id)
			->where('id', $tax_datum_id)
			->get_one();

			# SI SE OBTIENE INFORMACION
			if(!empty($tax_datum))
			{
				# SI EXISTE LA CONSTANCIA DE SITUACION FISCAL
				if($tax_datum->csf != '')
				{
					# SI EL ARCHIVO RECIEN SUBIDO EXISTE
					if(file_exists(DOCROOT.'assets/csf/'.$tax_datum->csf.'.pdf'))
					{
						# SE ELIMINAN EL ARCHIVO
						File::delete(DOCROOT.'assets/csf/'.$tax_datum->csf.'.pdf');
					}
				}

                # SE ELIMINA EL RFC
                Model_Customers_Tax_Datum::do_delete($tax_datum->id);

				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó el RFC correctamente.');

				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/usuarios/info/'.$user_id);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/usuarios');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}
	}

	/**
	 * RECUPERAR
	 *
	 * MANDA EL CORREO DE RECUPERACION DE CONTRASEÑA
	 *
	 * @access  public
	 * @return  Void
	 */
	
	public function action_admin_recuperar_contrasena($id)
	{
		
		  # Buscar el usuario por ID
        $user = Model_User::find($id);

        # Verificar si el usuario existe y pertenece al grupo correcto (grupo 1 en este caso)
        if ($user && $user->group == 1) {
            # Crear un hash aleatorio para el enlace de recuperación
            $hash = Str::random('alnum', 16);

            # Preparar los datos para actualizar el usuario
            $data_to_update = ['token' => $hash];

            # Intentar actualizar la información del usuario en la base de datos
            if (Auth::instance()->update_user($data_to_update, $user->username)) {
                # Enviar correo de recuperación
                $this->send_recovery_email($user, $hash);
            } else {
                Session::set_flash('error', 'Error al actualizar la información del usuario.');
            }
        } else {
            Session::set_flash('error', 'Usuario no encontrado o no permitido.');
        }

        # Redireccionar a una página específica o renderizar una vista
        Response::redirect_back('admin/usuarios/info');
	}


	 /**
     * Enviar correo electrónico de recuperación
     *
     * @param   object $user Usuario al que enviar el correo
     * @param   string $hash Hash de recuperación
     * @return  void
     */
    private function send_recovery_email($user, $hash)
    {
        $link = Uri::base(false) . 'recuperar-contrasena/nueva-contrasena/' . $user->id . '/' . $hash;
        $email = Email::forge();
        $email->from('sistemas@sajor.com.mx', 'Distribuidora Sajor');
        $email->to($user->email, $user->username);
        $email->subject('Recuperación de contraseña');
        $email->html_body(View::forge('email_templates/recovery', ['link' => $link], false));

        try {
            if ($email->send()) {
                Session::set_flash('success', 'Correo de recuperación enviado correctamente.');
            } else {
                Session::set_flash('error', 'No es posible enviar el correo en este momento.');
            }
        } catch (Exception $e) {
            Session::set_flash('error', 'Error al enviar el correo: ' . $e->getMessage());
        }
    }


}
