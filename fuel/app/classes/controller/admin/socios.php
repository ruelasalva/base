<?php

/**
 * CONTROLADOR ADMIN_SOCIOS_DE_NEGOCIO
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Socios extends Controller_Admin
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
		$partners_info = array();
		$per_page   = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$partners = Model_User::query()
		->related('partner')
		->related('partner.employee') 
		->related('partner.customer') 
		->related('partner.type') 
		->where('group', 15);
		

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
			$partners = $partners->where(DB::expr("CONCAT(`t1`.`code_sap`, ' ', `t1`.`name`,' ', `t1`.`id`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $partners->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
			'show_last'      => true,
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('partners', $config);

		# SE EJECUTA EL QUERY
		$partners = $partners->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SE OBTIENE INFORMACION
		if (!empty($partners))
		{
			foreach ($partners as $partner)
			{
				# SE DESERIALIZAN LOS CAMPOS EXTRAS
				$status = unserialize($partner->profile_fields);

				# SE OBTIENE DATO FISCAL DEL SOCIO
				$tax_data = Model_Partners_Tax_Datum::query()
					->where('partner_id', $partner->partner->id)
					->get_one();

				# SE VERIFICA SI TIENE CONSTANCIA FISCAL (CSF)
				$csf_status = ($tax_data && !empty($tax_data->csf)) ? 'Sí' : 'No';

				# SE OBTIENE CUÁNTAS DIRECCIONES DE ENTREGA TIENE
				$delivery_count = Model_Partners_Delivery::query()
					->where('partner_id', $partner->partner->id)
					->where('deleted', 0)
					->count();

				# SE OBTIENE CUÁNTOS CONTACTOS TIENE
				$contact_count = Model_Partners_Contact::query()
					->where('partner_id', $partner->partner->id)
					->where('deleted', 0)
					->count();

				# SE ALMACENA LA INFORMACIÓN DEL SOCIO
				$partners_info[] = array(
					'id'            => $partner->partner->id,
					'user_id'       => $partner->id,
					'username'      => $partner->username,
					'rfc'           => $partner->partner->rfc,
					'name'          => $partner->partner->name,
					'employee_id'   => $partner->partner->employee->name ?? 'No tiene asignado',
					'customer_id'   => $partner->partner->customer->name ?? 'No cuenta',
					'email'         => $partner->email,
					'type_id'       => $partner->partner->type->name ?? 'Sin asignar',
					'code_sap'      => $partner->partner->code_sap,
					'connected'     => ($status['connected']) ? 'Conectado' : 'Desconectado',
					'banned'        => ($status['banned']) ? 'Sí' : 'No',
					'updated_at'    => !empty($partner->updated_at) ? date('d/m/Y - H:i', $partner->updated_at) : '',

					# NUEVOS CAMPOS AGREGADOS
					'csf'           => $csf_status,
					'deliveries'    => $delivery_count,
					'contacts'      => $contact_count
				);
			}
		}


		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['partners']      = $partners_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Socios de Negocios';
		$this->template->content = View::forge('admin/socios/index', $data, false);
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
				Response::redirect('admin/socios/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/socios');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/socios');
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
    $fields  = array('code_sap', 'username', 'name', 'email', 'password', 'rfc', 'type_id', 'customer_id', 'employee_id', 'user_id', 'deleted','banned');
	$employee_opts = array('' => 'Selecionar');
	$customer_opts = array('' => 'Selecionar');
	$type_opts = array('' => 'Selecionar');


	# OBTENER LA LISTA DE VENDEDORES
		$employees = Model_Employee::query()->get();
		foreach ($employees as $employee) {
			$employee_opts[$employee->id] = $employee->name;
		}

	# OBTENER LA LISTA DE CLIENTES
		$customers = Model_Customer::query()->get();
		foreach ($customers as $customer) {
			$customer_opts[$customer->id] = $customer->name;
		}

	# OBTENER LA LISTA DE PRECIOS
		$types = Model_Customers_Type::query()->get();
		foreach ($types as $type) {
			$type_opts[$type->id] = $type->name;
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

    # SI SE UTILIZA EL METODO POST
    if(Input::method() == 'POST')
    {
        # SE CREA LA VALIDACION DE LOS CAMPOS
        $val = Validation::forge('partner');
        $val->add_callable('Rules');
        $val->add_field('code_sap', 'codigo cliente', 'required|valid_string[alpha,numeric]|min_length[7]|max_length[7]');
        $val->add_field('name', 'nombre cliente', 'required|valid_string[varchar]|min_length[3]|max_length[250]');
        $val->add_field('email', 'email', 'required|min_length[7]|max_length[255]|valid_email');
        $val->add_field('password', 'contraseña', 'required|min_length[6]|max_length[20]');
        $val->add_field('rfc', 'rfc', 'required|valid_string[alpha,numeric]|min_length[10]|max_length[13]');
        $val->add_field('type_id', 'Tipo de usuario', 'valid_string[numeric]');
        $val->add_field('customer_id', 'Cliente web', 'valid_string[numeric]');
        $val->add_field('employee_id', 'Vendedor', 'valid_string[numeric]');

        # SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
        if($val->run())
        {
            try
            {
                # Verificar si ya existe el código del socio de negocios
                $existing_codigo = Model_Partner::query()
                ->where('code_sap', $val->validated('code_sap'))
                ->get_one();

                if($existing_codigo)
                {
                    Session::set_flash('error','Socio de Negocios dado de alta anteriormente');
                }
                else
                {

					# DEFINIR EL GRUPO PARA PROVEEDORES
					$group = 15;

					$username = $val->validated('code_sap');

					# CREAR EL USUARIO
					$user_id = Auth::instance()->create_user(
						$username,
						$val->validated('password'),
						$val->validated('email'),
						$group,
						array(
							'connected' => false,
							'banned'    => false
						)
					);

					# VALIDAR SI EL USUARIO SE CREÓ
					$user = Model_User::find($user_id);
					if (!$user) {
						throw new Exception('Usuario no encontrado en la base de datos.');
					}

                    # SE CREA EL SOCIO DE NEGOCIO
                    $partner = Model_Partner::forge(array(
						'user_id'     => $user_id,
						'code_sap'    => strtoupper($val->validated('code_sap')),
						'name'        => strtoupper($val->validated('name')),
						'email'       => $val->validated('email'),
						'rfc'         => strtoupper($val->validated('rfc')),
						'type_id'     => ($val->validated('type_id') !== '' && $val->validated('type_id') !== null) ? $val->validated('type_id') : '',
						'customer_id' => ($val->validated('customer_id') !== '' && $val->validated('customer_id') !== null) ? $val->validated('customer_id') : '',
						'employee_id' => ($val->validated('employee_id') !== '' && $val->validated('employee_id') !== null) ? $val->validated('employee_id') : '',
						'deleted'     => 0,
						'created_at'  => time(),
                    ));
                
                    # GUARDAR EL SOCIO DE NEGOCIO
                    if ($partner->save())
                    {
                        # SE ESTABLECE EL MENSAJE DE EXITO
                        Session::set_flash('success', 'El socio de negocio <b>'.$val->validated('nombre').'</b> ha sido agregado correctamente.');

                        # SE REDIRECCIONA AL USUARIO
                        Response::redirect('admin/socios');
                    }
                }
            }
            catch(\Exception $e)
            {
                # SE ESTABLECE EL MENSAJE DE ERROR
                Session::set_flash('error', 'Ha ocurrido un error al intentar agregar el socio de negocio.');

                # Registrar el error
                Log::error('Error al guardar el socio de negocio: ' . $e->getMessage());

                # SE ALMACENA LOS ERRORES DETECTADOS
                $data['errors'] = $val->error();
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
            $data['code_sap']    = Input::post('code_sap');
            $data['name']      	 = Input::post('name');
            $data['email']       = Input::post('email');
            $data['password']    = Input::post('password');
            $data['rfc']         = Input::post('rfc');
            $data['type_id']     = Input::post('type_id');
            $data['customer_id'] = Input::post('customer_id');
            $data['employee_id'] = Input::post('employee_id');
        }
    }

    # SE ALMACENA LA INFORMACION PARA LA VISTA
    $data['classes'] = $classes;
    $data['employee_opts'] = $employee_opts;
    $data['customer_opts'] = $customer_opts;
    $data['type_opts'] = $type_opts;

    # SE CARGA LA VISTA
    $this->template->title   = 'Agregar Socio de Negocio';
    $this->template->content = View::forge('admin/socios/agregar', $data);
	}


	/**
	 * AGREGAR MASIVAMENTE
	 *
	 * PERMITE AGREGAR VARIOS REGISTROS A LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_importar_csv() 
	{
		#VARIABLES
		$data    = array();
		$classes = array('file' => array('form-group' => null, 'form-control' => null));
		$success_count = 0;
		$error_count   = 0;
		$error_logs    = array();
		$error_rows    = array(); // <-- arreglo para almacenar errores detallados

		if (Input::method() == 'POST')
		{
			Log::debug('POST recibido en importar_csv.');

			#VALIDAR SI EL ARCHIVO FUE SUBIDO SIN NO DAR MENSAJE PARA QUE NO SE OLVIDE CARGARLO
			if (!isset($_FILES['file']) || $_FILES['file']['error'] !== 0)
			{
				Log::error('No se seleccionó ningún archivo para subir.');
				Session::set_flash('error', 'Asegúrate de subir un archivo CSV válido.');
				Response::redirect('admin/socios/importar_csv');
			}

			#LA CONFIGURACION DE DONDE SE ALMACENARA EL ARCHIVO
			$config = array(
				'path' => DOCROOT . 'assets/csv/',
				'randomize' => true,
				'ext_whitelist' => array('csv'),
			);

			Upload::process($config);

			if (Upload::is_valid())
			{
				Upload::save();
				$file = Upload::get_files(0);
				$csv_path = $file['saved_to'] . $file['saved_as'];
				Log::debug('Archivo CSV guardado en: ' . $csv_path);

				$lines = file($csv_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

				if (count($lines) <= 1)
				{
					Session::set_flash('error', 'El archivo CSV no contiene registros.');
					Log::error('CSV vacío o sin filas suficientes.');
					Response::redirect('admin/socios/importar_csv');
				}

				$header = str_getcsv(array_shift($lines));
				Log::debug('Encabezados CSV: ' . implode(', ', $header));

				foreach ($lines as $i => $line)
				{
					$row = str_getcsv($line);
					$fila = $i + 2;

					if (count($row) != count($header))
					{
						Log::error("Fila $fila: número incorrecto de columnas.");
						$error_logs[] = "Fila $fila: columnas inválidas.";
						$error_rows[] = array($fila, '-', '-', 'Número incorrecto de columnas');
						$error_count++;
						continue;
					}

					$info = array_combine($header, $row);

					#VALIDACIONES POR SI YA ESTABA AGREGADO ANTERIORMENTE
					$existing = Model_Partner::query()->where('code_sap', trim($info['code_sap']))->get_one();
					if ($existing)
					{
						Log::warning("Socio ya existe: " . $info['code_sap']);
						$error_logs[] = "Fila $fila: socio ya registrado con código " . $info['code_sap'];
						$error_rows[] = array($fila, $info['code_sap'], $info['email'], 'Socio ya registrado');
						$error_count++;
						continue;
					}

					#CREACION DEL USUARIO
					try {
						$username = strtoupper(trim($info['code_sap']));
						$password = trim($info['password']);
						$email    = trim($info['email']);
						$group    = 15; #ESTE ES EL GRUPO DE SOCIOS

						#VERIFICA SI EL CORREO ESTA EN USO
						$existing_user = Model_User::query()->where('email', $email)->get_one();

						if ($existing_user)
						{
							Log::error("Fila $fila: El correo '$email' ya está en uso por el usuario '{$existing_user->username}' (ID {$existing_user->id})");
							$error_logs[] = "Fila $fila: el correo <strong>$email</strong> ya está en uso por el usuario <strong>{$existing_user->username}</strong>.";
							$error_rows[] = array($fila, $username, $email, "Correo ya usado por {$existing_user->username}");
							$error_count++;
							continue;
						}

						$user_id = Auth::instance()->create_user(
							$username,
							$password,
							$email,
							$group,
							array(
								'connected' => false,
								'banned'    => false
							)
						);

						Log::debug("Usuario creado con ID $user_id para $username");
					} catch (Exception $e) {
						Log::error("Error al crear usuario en fila $fila: " . $e->getMessage() . " | Usuario: $username | Email: $email");
						$error_logs[] = "Fila $fila: error creando usuario <strong>$username</strong> - " . $e->getMessage();
						$error_rows[] = array($fila, $username, $email, "Error creando usuario: " . $e->getMessage());
						$error_count++;
						continue;
					}

					#CREO EL PARTNER CON LOS DATOS DEL EXCEL
					try {
						$partner = Model_Partner::forge(array(
							'user_id'     => $user_id,
							'code_sap'    => strtoupper(trim($info['code_sap'])),
							'name'        => strtoupper(trim($info['name'])),
							'email'       => $email,
							'rfc'         => strtoupper(trim($info['rfc'])),
							'type_id'     => (!empty($row['type_id'])) ? $row['type_id'] : '',
							'customer_id' => (!empty($row['customer_id'])) ? $row['customer_id'] : '',
							'employee_id' => (!empty($row['employee_id'])) ? $row['employee_id'] : '',
							'deleted'     => 0,
							'created_at'  => time(),
						));

						if ($partner->save())
						{
							Log::debug("Partner guardado: " . $partner->code_sap);
							$success_count++;
						}
						else
						{
							Log::error("Error al guardar el socio en fila $fila");
							$error_logs[] = "Fila $fila: error al guardar socio.";
							$error_rows[] = array($fila, $username, $email, "Error al guardar socio");
							$error_count++;
						}
					} catch (Exception $e) {
						Log::error("Error en fila $fila: " . $e->getMessage());
						$error_logs[] = "Fila $fila: error inesperado.";
						$error_rows[] = array($fila, $username, $email, "Error inesperado: " . $e->getMessage());
						$error_count++;
					}
				}

				#SI HAY ERRORES CREAMOS EL ARCHIVO CSV PARA DESCARGA
				if ($error_count > 0 && !empty($error_rows))
				{
					$csv_error_path = DOCROOT . 'assets/csv/errores/';
					if (!is_dir($csv_error_path)) {
						mkdir($csv_error_path, 0777, true);
					}

					$filename = 'errores_socios_' . date('Ymd_His') . '.csv';
					$full_path = $csv_error_path . $filename;

					$fp = fopen($full_path, 'w');
					fputcsv($fp, array('Fila', 'Usuario', 'Correo', 'Error'));
					foreach ($error_rows as $row) {
						fputcsv($fp, $row);
					}
					fclose($fp);
				}

				#MENSAJE FINAL
				$summary = "Importación finalizada:<br>";
				$summary .= "<strong>$success_count</strong> socios creados.<br>";
				if ($error_count > 0)
				{
					$summary .= "<strong>$error_count</strong> errores:<br>" . implode('<br>', $error_logs);
					$summary .= "<br><a href='" . Uri::base(false) . "assets/csv/errores/$filename' target='_blank' class='btn btn-sm btn-warning mt-2'>Descargar errores CSV</a>";
					Session::set_flash('error', $summary);
				}
				else
				{
					Session::set_flash('success', $summary);
				}

				Response::redirect('admin/socios/importar_csv');
			}
			else
			{
				Log::error('Archivo no válido o no es CSV.');
				Session::set_flash('error', 'El archivo debe ser formato CSV.');
				Response::redirect('admin/socios/importar');
			}
		}

		$data['classes'] = $classes;
		$this->template->title = 'Importar Socios CSV';
		$this->template->content = View::forge('admin/socios/importar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($partner_id = 0)
{
	# VALIDAR QUE SE RECIBA UN ID VÁLIDO
	if ($partner_id == 0 || !is_numeric($partner_id)) {
		Response::redirect('admin/socios');
	}

	# OBTENER EL PARTNER
	$partner = Model_Partner::query()
		->related('user')
		->related('type')
		->related('customer')
		->related('employee')
		->where('id', $partner_id)
		->get_one();

	if (!$partner || !$partner->user) {
		Session::set_flash('error', 'No se encontró información del socio.');
		Response::redirect('admin/socios');
	}

	$status = @unserialize($partner->user->profile_fields) ?: [];
$banned = (isset($status['banned']) && $status['banned']) ? 'Sí' : 'No';

# INFO BÁSICA
$data = [
    'id'             => $partner->user->id,
    'user_id'        => $partner->id,
    'code_sap'       => $partner->code_sap,
    'name'           => $partner->name,
    'rfc'            => $partner->rfc,
    'email'          => $partner->user->email,
    'type_id'        => $partner->type_id,
    'customer_id'    => $partner->customer_id,
    'employee_id'    => $partner->employee_id,
    'banned'         => $banned
];


	# BLOQUES RELACIONADOS
	$data['tax_data']  = Model_Partners_Tax_Datum::query()->related('cfdi')->related('payment_method')->related('sat_tax_regime')->related('state')->where('partner_id', $partner->id)->get_one();
	
	$data['delivery']  = Model_Partners_Delivery::query()
	->related('state')
	->where('partner_id', $partner->id)
	->where('deleted', 0)
	->get(); // múltiples
	
	
	
	$data['contact'] = Model_Partners_Contact::query()
    ->where('partner_id', $partner->id)
    ->get(); // ← Asegúrate de usar get() y no get_one()
	
	$data['customer_id']   = $partner->customer_id; // El ID para el select del modal
	$data['customer_id'] = !empty($partner->customer) ? $partner->customer->name : '-'; // El nombre para mostrar en info

	$data['employee_id']   = $partner->employee_id;
	$data['employee_id'] = !empty($partner->employee) ? $partner->employee->name.' '.$partner->employee->last_name : '-';

	$data['type_id']       = $partner->type_id;
	$data['type_id']     = !empty($partner->type) ? $partner->type->name : '-';	
	
	$data['partner_id'] = $partner->id;


	# CARGAR VISTA
	$this->template->title   = 'Información del Socio de Negocio';
	$this->template->content = View::forge('admin/socios/info', $data);
}//ELIMINAR MAS DELANTE

/**
	 * INFO
	 *
	 * PERMITE INFORMACION DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_infos($partner_id = 0)
{
	# SI NO SE RECIBE UN ID O NO ES UN NUMERO
	if ($partner_id == 0 || !is_numeric($partner_id)) {
		Response::redirect('admin/socios');
	}

	# SE INICIALIZAN LAS VARIABLES
	$data = array();

	# SE BUSCA EL PARTNER Y SUS RELACIONES
	$partner = Model_Partner::query()
		->related('user')
		->related('type')
		->related('customer')
		->related('employee')
		->where('id', $partner_id)
		->get_one();

	# SI NO SE ENCUENTRA EL SOCIO
	if (!$partner || !$partner->user) {
		Session::set_flash('error', 'No se encontró información del socio.');
		Response::redirect('admin/socios');
	}

	# SE DESERIALIZAN LOS CAMPOS DEL USUARIO
	$status = unserialize($partner->user->profile_fields);

	# SE ALMACENA LA INFORMACION GENERAL DEL SOCIO
	$data['partner'] = array(
		'id'           => $partner->id,
		'user_id'      => $partner->user->id,
		'username'     => $partner->user->username,
		'name'         => $partner->name,
		'email'        => $partner->user->email,
		'type'         => $partner->type->name ?? 'Sin lista de precios',
		'customer'     => $partner->customer->name ?? 'Sin cliente web',
		'employee'     => $partner->employee->name ?? 'Sin vendedor',
		'code_sap'     => $partner->code_sap,
		'banned'       => (!empty($status['banned']) && $status['banned']) ? 'Sí' : 'No',
	);

	# DATOS FISCALES (UNO SOLO)
	$data['tax_data'] = Model_Partners_Tax_Datum::query()
		->related('cfdi')
		->related('payment_method')
		->related('sat_tax_regime')
		->related('state')
		->where('partner_id', $partner_id)
		->get_one();

	# DATOS DE ENTREGA (VARIOS)
	$data['deliveries'] = Model_Partners_Delivery::query()
		->related('state')
		->where('partner_id', $partner_id)
		->where('deleted', 0)
		->order_by('id', 'asc')
		->get();

	# DATOS DE COMPRAS (UNO SOLO)
	$data['purchases'] = Model_Partners_Purchase::query()
		->where('partner_id', $partner_id)
		->get_one();

	# DATOS DE CUENTAS POR PAGAR (UNO SOLO)
	$data['account'] = Model_Partners_Account::query()
		->where('partner_id', $partner_id)
		->get_one();

	# CONTACTOS RELACIONADOS (VARIOS)
	$data['contacts'] = Model_Partners_Contact::query()
		->where('partner_id', $partner_id)
		->where('deleted', 0)
		->order_by('id', 'asc')
		->get();

	# CARGAR LA VISTA
	$this->template->title   = 'Información del Socio de Negocio';
	$this->template->content = View::forge('admin/socios/info', $data);
}




	/**
	* EDITAR
	*
	* PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_editar($partner_id = 0)
	{
		if ($partner_id == 0 || !is_numeric($partner_id)) {
			Response::redirect('admin/socios');
		}

		# INICIALIZAR VARIABLES
		$data = [];
		$classes = [];
		$fields = ['code_sap', 'name', 'email', 'rfc', 'type_id', 'customer_id', 'employee_id', 'banned'];
		$customer_opts = [];
		$employee_opts = [];
		$type_opts = [];

		# BUSCAR USUARIO Y PARTNER
		$user = Model_User::query()
			->related('partner')
			->where('id', $partner_id)
			->get_one();

		if (empty($user) || empty($user->partner)) {
			Response::redirect('admin/socios');
		}

		# OPCIONES PARA LOS SELECTS
		$customers = Model_Customer::query()->order_by('name', 'asc')->get();
		$customer_opts = ['' => 'Seleccionar...'];
		foreach ($customers as $customer) {
			$customer_opts[$customer->id] = $customer->name;
		}

		$employees = Model_Employee::query()->order_by('name', 'asc')->get();
		$employee_opts = ['' => 'Seleccionar...'];
		foreach ($employees as $employee) {
			$employee_opts[$employee->id] = $employee->name;
		}

		$types = Model_Customers_Type::query()->order_by('name', 'asc')->get();
		$type_opts = ['' => 'Seleccionar...'];
		foreach ($types as $type) {
			$type_opts[$type->id] = $type->name;
		}

		# DESEARIALIZAR STATUS (banned)
		$profile_fields = @unserialize($user->profile_fields) ?: [];
		$current_banned = isset($profile_fields['banned']) ? $profile_fields['banned'] : 0;

		# DATOS PARA LA VISTA
		$data['code_sap']    = $user->partner->code_sap;
		$data['name']        = $user->partner->name;
		$data['email']       = $user->email;
		$data['rfc']         = $user->partner->rfc;
		$data['type_id']     = $user->partner->type_id ?: '';
		$data['customer_id'] = $user->partner->customer_id ?: '';
		$data['employee_id'] = $user->partner->employee_id ?: '';
		$data['banned']      = $current_banned;

		foreach ($fields as $field) {
			$classes[$field] = [
				'form-group' => null,
				'form-control' => null,
			];
		}

		# SI SE ENVIO POST
		if (Input::method() == 'POST') {
			$val = Validation::forge('partner');
			$val->add_callable('Rules');
			$val->add_field('code_sap', 'Código', 'required|min_length[1]|max_length[7]');
			$val->add_field('name', 'Razón social', 'required|min_length[1]|max_length[255]');
			$val->add_field('email', 'Email', 'required|min_length[1]|valid_email');
			$val->add_field('rfc', 'RFC', 'required|min_length[12]|max_length[13]');
			$val->add_field('type_id', 'Lista de precios', 'numeric_min[1]');
			$val->add_field('customer_id', 'Usuario Web', 'numeric_min[1]');
			$val->add_field('employee_id', 'Vendedor', 'numeric_min[1]');
			$val->add_field('banned', 'Baneado', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			if ($val->run()) {
				try {
					# VALIDAR SI CAMBIA EMAIL
					$nuevo_email = $val->validated('email');

					if ($nuevo_email != $user->email) {
						$email_existente = Model_User::query()
							->where('email', $nuevo_email)
							->where('id', '!=', $user->id)
							->get_one();

						if (!empty($email_existente)) {
							Session::set_flash('error', 'El correo electrónico ingresado ya existe. Usa uno diferente.');
							Response::redirect('admin/socios/editar/' . $partner_id);
						}

						$user->email = $nuevo_email;
						
					}

					# ACTUALIZAR CAMPOS DE PARTNER
					$user->partner->code_sap    = $val->validated('code_sap');
					$user->partner->name        = $val->validated('name');
					$user->partner->rfc         = $val->validated('rfc');
					$user->partner->type_id     = $val->validated('type_id');
					$user->partner->customer_id = $val->validated('customer_id');
					$user->partner->employee_id = $val->validated('employee_id');

					# ACTUALIZAR CAMPO BANNED EN PROFILE_FIELDS
					$profile_fields['banned'] = $val->validated('banned');
					$user->profile_fields = serialize($profile_fields);

					# GUARDAR TODO
					if ($user->save() && $user->partner->save()) {
						Session::set_flash('success', 'Se actualizó correctamente la información del socio.');
						Response::redirect('admin/socios/editar/' . $partner_id);
					}
				} catch (\Exception $e) {
					Session::set_flash('error', 'Hubo un error al actualizar la información del socio.');
				}
			} else {
				Session::set_flash('error', 'Encontramos errores en el formulario.');
				$data['errors'] = $val->error();
			}
		}

		# VARIABLES PARA LA VISTA
		$data['id'] = $partner_id;
		$data['classes'] = $classes;
		$data['customer_opts'] = $customer_opts;
		$data['employee_opts'] = $employee_opts;
		$data['type_opts'] = $type_opts;

		$this->template->title = 'Editar Socio de Negocio';
		$this->template->content = View::forge('admin/socios/editar', $data);
	}





	/**
	 * RECUPERAR
	 *
	 * MANDA EL CORREO DE RECUPERACION DE CONTRASEÑA
	 *
	 * @access  public
	 * @return  Void
	 */
	
	public function action_recuperar_contrasena_socios($id)
	{
		
		# Buscar el usuario por ID
        $user = Model_User::find($id);

        # Verificar si el usuario existe y pertenece al grupo correcto (grupo 15 en este caso)
        if ($user && $user->group == 15) {
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
        Response::redirect_back('admin/socios/info');
	}


	/**
     * Enviar correo electrónico de recuperación
     *
     * @param   object $partner Usuario al que enviar el correo
     * @param   string $hash Hash de recuperación
     * @return  void
     */
	private function send_recovery_email($user, $hash)
    {
        $link = Uri::base(false) . 'recuperar-contrasena-socios/nueva-contrasena/' . $user->id . '/' . $hash;
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
