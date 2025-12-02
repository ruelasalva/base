<?php

class Controller_Admin_Crm_Ticket extends Controller_Admin
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
		if(!Auth::member(100) && !Auth::member(50) && !Auth::member(30) && !Auth::member(25) && !Auth::member(20))
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
		$data           = array();
		$tickets_info   = array();
		$per_page       = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$tickets = Model_Ticket::query()
		->where('status_id','<>', 5);

		# AGREGAR EL FILTRO PARA EL ID DEL USUARIO EN LA SESIÓN
		$user_id = Auth::get_user_id()[1]; // OBTIENE EL ID DEL USUARIO DE LA SESIÓN
		$tickets = $tickets->where('user_id', $user_id);

		# BUSCAR EL EMPLEADO CORRESPONDIENTE AL USUARIO ACTUAL
		$employee = Model_Employee::query()
		->where('user_id', $user_id)
		->get_one();

		if($employee)
		{
			$tickets = $tickets->where('employee_id', $employee->id);
		}
		else
		{
			# NO SE ENCONTRÓ UN EMPLEADO CORRESPONDIENTE AL USUARIO ACTUAL, ASÍ QUE NO MOSTRAR TICKETS
			$tickets = $tickets->where('employee_id', -1);
		}

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
			$tickets = $tickets->where(DB::expr("CONCAT(`t0`.`description`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $tickets->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
    		'show_last'      => true,
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('tickets', $config);

		# SE EJECUTA EL QUERY
		$tickets = $tickets->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SE RECORRE ELEMENTO POR ELEMENTO
		foreach($tickets as $ticket)
		{
			# SI SE OBTIENE INFORMACION
			if(!empty($tickets))
			{
				# VERIFICAR SI TIENE USUARIO ASIGNADO
				if($ticket->asig_user_id <= 0)
				{
					$asiguser = 'Aun no se asignado a un usuario';
				}
				else
				{
					# AQUÍ ASUMIMOS QUE ASIGUSER ES EL OBJETO DEL USUARIO ASIGNADO
					# SI ASIGUSER NO ES EL OBJETO DEL USUARIO ASIGNADO, AJUSTA ESTA LÍNEA EN CONSECUENCIA
					$asiguser = $ticket->asiguser->name;
				}

				# SE ALMACENA LA INFORMACION
				$tickets_info[] = array(
					'id'            => $ticket->id,
					'type_id'       => $ticket->typeticket->name,
					'incident_id'   => $ticket->incidentticket->name,
					'description'   => Str::truncate($ticket->description, '60' , '...'),
					'status_id'     => $ticket->statusticket->name,
					'priority_id'   => $ticket->priorityticket->name,
					'user_id'       => $ticket->user->username,
					'asig_user_id'  => $asiguser,
					'rating'        => $ticket->rating,
					'created_at'    => date('d/m/Y - H:i', $ticket->created_at)
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['tickets']    = $tickets_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Tickets';
		$this->template->content = View::forge('admin/crm/ticket/index', $data, false);
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
				Response::redirect('admin/crm/ticket/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/crm/ticket');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/ticket');
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
	public function action_info($ticket_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($ticket_id == 0 || !is_numeric($ticket_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/ticket');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$ticket = Model_Ticket::query()
		->where('id', $ticket_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($ticket))
		{
			# VERIFICAR SI TIENE USUARIO ASIGNADO
			if($ticket->asig_user_id <= 0)
			{
				$asiguser = 'Aun no se asignado a un usuario';
			}
			else
			{
				# AQUÍ ASUMIMOS QUE ASIGUSER ES EL OBJETO DEL USUARIO ASIGNADO
				# SI ASIGUSER NO ES EL OBJETO DEL USUARIO ASIGNADO, AJUSTA ESTA LÍNEA EN CONSECUENCIA
				$asiguser = $ticket->asiguser->name;
			}

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['type_id']      = $ticket->typeticket->name;
			$data['description']  = $ticket->description;
			$data['status_id']    = $ticket->statusticket->name;
			$data['status']    	  = $ticket->status_id;
			$data['priority_id']  = $ticket->priorityticket->name;
			$data['incident_id']  = $ticket->incidentticket->name;
			$data['user_id']      = $ticket->employee->name;
			$data['solution']     = $ticket->solution;
			$data['asig_user_id'] = $asiguser;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/ticket');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $ticket_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del Ticket';
		$this->template->content = View::forge('admin/crm/ticket/info', $data);
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
		$data                = array();
		$classes             = array();
		$fields              = array('id', 'type_id', 'incident_id', 'description', 'status_id', 'priority_id');
		$typeticket_opts     = array();
		$incidentticket_opts = array();
		$priorityticket_opts = array();

		# SE RECORRE CAMPO POR CAMPO
		foreach($fields as $field)
		{
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array(
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SI SE UTILIZA EL METODO POST
		if(Input::method() == 'POST')
		{
			# OBTIENE EL ID DEL USUARIO ACTUAL
			$user_id = Auth::get('id');

			# OBTIENE EL ID DEL EMPLEADO ASOCIADO AL USUARIO
			$employee = Model_Employee::query()
			->where('user_id', $user_id)
			->get_one();

			# SI SE OBTIENE INFORMACION
			if(!empty($employee))
			{
				# SE CREA LA VALIDACION DE LOS CAMPOS
				$val = Validation::forge('ticket');
				$val->add_field('type_id', 'Tipo', 'required|valid_string[numeric]|numeric_min[1]');
				$val->add_field('incident_id', 'Tipo', 'required|valid_string[numeric]|numeric_min[1]');
				$val->add_field('description', 'Descripción', 'required');
				$val->add_field('priority_id', 'Prioridad', 'required|valid_string[numeric]|numeric_min[1]');

				# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
				if($val->run())
				{
					# CREA UNA NUEVA INSTANCIA DEL MODELO SURVEY Y GUARDA LOS DATOS
					$currentDate = time();

					# SE CREA EL MODELO CON LA INFORMACION
					$ticket = new Model_Ticket(array(
						'type_id'     => $val->validated('type_id'),
						'incident_id' => $val->validated('incident_id'),
						'description' => $val->validated('description'),
						'user_id'     => $user_id,
						'employee_id' => $employee->id,
						'priority_id' => $val->validated('priority_id'),
						'rating'      => 0,
						'created_at'  => $currentDate
					));

					# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
					if($ticket->save())
					{

						 // OBTENER EL NOMBRE DEL USUARIO QUE CREÓ EL TICKET
						$usuario_nombre = '';
						$usuario = Model_User::find($ticket->user_id);
						if ($usuario) {
							$usuario_nombre = $usuario->username; // O puedes concatenar nombre completo si lo tienes
						}

						// LLAMA AL HELPER Y PASA LAS VARIABLES
						Helper_Notification::notify_event('ticket_nuevo', [
							'ID'          => $ticket->id,
							'FOLIO'       => $ticket->folio ?? $ticket->id, // O usa el ID si no hay folio propio
							'DESCRIPCION' => $ticket->description,
							'USUARIO'     => $usuario_nombre,
						]);

						// === LOG GLOBAL DEL SISTEMA ===
						Helper_Log::agregar('ticket', $ticket->id, 'Se creó un nuevo ticket desde el admin CRM', [
							'usuario'     => $usuario_nombre,
							'type_id'     => $ticket->type_id,
							'incident_id' => $ticket->incident_id,
							'priority_id' => $ticket->priority_id,
							'descripcion' => $ticket->description,
						]);
						
						# SE CREA EL LOG DEL TICKET
						# SE INICIALIZAN LAS VARIABLES
						$employee_name  = '';
						$asig_user_name = '';

						# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
						$employee_log = Model_Employee::query()
						->where('user_id', Auth::get('id'))
						->get_one();

						# SE SE OBTIENE INFORMACION
						if(!empty($employee_log))
						{
							# SE ALMACENA EL NOMBRE DEL EMPLEADO
							$employee_name = $employee_log->name.' '.$employee_log->last_name;
						}

						# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
						$asig_user_log = Model_Employee::query()
						->where('id', $val->validated('asig_user_id'))
						->get_one();

						# SE SE OBTIENE INFORMACION
						if(!empty($asig_user_log))
						{
							# SE ALMACENA EL NOMBRE DEL USUARIO ASIGNADO
							$asig_user_name = $asig_user_log->name.' '.$asig_user_log->last_name;
						}

						# SE CREA EL MODELO CON LA INFORMACION
						$log = new Model_Tickets_Log(array(
							'ticket_id' => $ticket->id,
							'message'   => 'El empleado <b>'.$employee_name.'</b> creó el ticket.',
							'color'     => 'text-success',
							'date'      => time()
						));

						# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
						$log->save();

						# SE ESTABLECE EL MENSAJE DE EXITO
						Session::set_flash('success', 'Se agregó el ticket <b>'.Input::post('subject').'</b> correctamente.');

						# SE REDIRECCIONA AL USUARIO
						Response::redirect('admin/crm/ticket');
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
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'El usuario actual no cuenta con un registro de empleado.');

				# SE RECORRE CLASE POR CLASE
				foreach($classes as $name => $class)
				{
					# SE ALMACENA LA INFORMACION PARA LA VISTA
					$data[$name] = Input::post($name);
				}
			}
		}

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$typeticket_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$types = Model_Tickets_Type::query()
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACIÓN
		if(!empty($types))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($types as $type)
			{
				# SE ALMACENA LA OPCIÓN
				$typeticket_opts[$type->id] = $type->name;
			}
		}

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$incidentticket_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$incidents = Model_Tickets_Incident::query()
		->order_by('name', 'asc')
		->get();

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$priorityticket_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$prioritys = Model_Tickets_Priority::query()
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACIÓN
		if(!empty($prioritys))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($prioritys as $priority)
			{
				# SE ALMACENA LA OPCIÓN
				$priorityticket_opts[$priority->id] = $priority->name;
			}
		}


		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['classes']             = $classes;
		$data['typeticket_opts']     = $typeticket_opts;
		$data['incidentticket_opts'] = $incidentticket_opts;
		$data['priorityticket_opts'] = $priorityticket_opts;



		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar Ticket';
		$this->template->content = View::forge('admin/crm/ticket/agregar', $data);
	}

	/**
	* FINALIZAR TICKET
	*
	* MUESTRA UNA LISTADO DE REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_finalizar($ticket_id = 0)
	{

		# SE INICIALIZAN LAS VARIABLES
		$logs_info           = array();

		# SI NO SE RECIBE UN ID DE TICKET O NO ES UN NÚMERO VÁLIDO
		if($ticket_id == 0 || !is_numeric($ticket_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/ticket');
		}

		# SE BUSCA EL TICKET A TRAVÉS DEL MODELO
		$ticket = Model_Ticket::find($ticket_id);

		# SI NO SE ENCUENTRA EL TICKET
		if(!$ticket)
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/ticket');
		}

		# VALIDAR QUE EL TICKET TENGA UN USUARIO ASIGNADO
		if(empty($ticket->asig_user_id))
		{
			# MOSTRAR MENSAJE DE ERROR
			Session::set_flash('error', 'El ticket debe tener un usuario asignado y haber pasado por sus procesos antes de marcarlo como "Finalizado".');
			Response::redirect('admin/crm/ticket/editar/'.$ticket_id);
		}

		# GUARDAR LA HORA DE ACTUALIZACIÓN
		$currentDate        = time();
		$ticket->updated_at = $currentDate;

		# CAMBIAR EL ESTADO A "FINALIZADO"
		$ticket->status_id = 4;
		$ticket->save();

		# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
		$admin_log = Model_User::query()
			->where('id', Auth::get('id'))
			->get_one();

		$admin_name = '';
		if(!empty($admin_log))
		{
			$admin_profile = unserialize($admin_log->profile_fields);
			$admin_name = $admin_profile['full_name'];
		}

		# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
		$asig_user_log = Model_Employee::query()
			->where('id', $ticket->asig_user_id)
			->get_one();

		$asig_user_name = '';
		if(!empty($asig_user_log))
		{
			$asig_user_name = $asig_user_log->name.' '.$asig_user_log->last_name;
		}

		# SE CREA EL MODELO CON LA INFORMACION
		$message = 'El empleado <b>'.$admin_name.'</b> da por concluido el ticket del empleado <b>'.$asig_user_name.'</b> y se cierra por completo.';
		$log = new Model_Tickets_Log(array(
			'ticket_id' => $ticket->id,
			'message'   => $message,
			'color'     => 'text-muted',
			'date'      => time()
		));
		
		#SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
		$log->save();

		# SE REDIRECCIONA AL USUARIO (OPCIONAL)
		Session::set_flash('success', 'El ticket se marcó como "Finalizado" correctamente.');
		Response::redirect('admin/crm/ticket');
	}


	/**
	* INICIAR TICKET
	*
	* MUESTRA UNA LISTADO DE REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_iniciar($ticket_id = 0)
	{

		# SE INICIALIZAN LAS VARIABLES
		$logs_info           = array();

		# SI NO SE RECIBE UN ID DE TICKET O NO ES UN NÚMERO VÁLIDO
		if($ticket_id == 0 || !is_numeric($ticket_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/ticket');
		}

		# SE BUSCA EL TICKET A TRAVÉS DEL MODELO
		$ticket = Model_Ticket::find($ticket_id);

		# SI NO SE ENCUENTRA EL TICKET
		if(!$ticket)
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/ticket');
		}

		# VALIDAR QUE EL TICKET TENGA UN USUARIO ASIGNADO
		if(empty($ticket->asig_user_id && $ticket->status_id == 6 ))
		{
			# MOSTRAR MENSAJE DE ERROR
			Session::set_flash('error', 'El ticket debe de estar asignado con un usuario para poder "INICIAR".');
			Response::redirect('admin/crm/ticket/info/'.$ticket_id);
		}

		# GUARDAR LA HORA DE ACTUALIZACIÓN
		$currentDate        = time();
		$ticket->updated_at = $currentDate;

		# CAMBIAR EL ESTADO A "ATENDIENDO"
		$ticket->status_id = 3;
		$ticket->save();

		# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
		$admin_log = Model_User::query()
			->where('id', Auth::get('id'))
			->get_one();

		$admin_name = '';
		if(!empty($admin_log))
		{
			$admin_profile = unserialize($admin_log->profile_fields);
			$admin_name = $admin_profile['full_name'];
		}

		# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
		$asig_user_log = Model_Employee::query()
			->where('id', $ticket->asig_user_id)
			->get_one();

		$asig_user_name = '';
		if(!empty($asig_user_log))
		{
			$asig_user_name = $asig_user_log->name.' '.$asig_user_log->last_name;
		}

		# SE CREA EL MODELO CON LA INFORMACION
		$message = 'El empleado <b>'.$admin_name.'</b> inicio el ticket del empleado <b>'.$asig_user_name.'</b>.';
		$log = new Model_Tickets_Log(array(
			'ticket_id' => $ticket->id,
			'message'   => $message,
			'color'     => 'text-info',
			'date'      => time()
		));
		
		#SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
		$log->save();


		# SE REDIRECCIONA AL USUARIO (OPCIONAL)
		Session::set_flash('success', 'El ticket se empezo a atender correctamente.');
		Response::redirect('admin/crm/ticket');
	}


	/**
	* CANCELAR TICKET
	*
	* CANCELA Y ELIMINA LOGICAMENTE EL REGISTRO EN EL INDEX DE TICKETS
	*
	* @access  public
	* @return  Void
	*/
	public function action_cancelar($ticket_id = 0)
	{
	# SI NO SE RECIBE UN ID DE LA TAREA O NO ES UN NÚMERO VÁLIDO
	if($ticket_id == 0 || !is_numeric($ticket_id))
	{
		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/crm/ticket');
	}

	# SE BUSCA LA TAREA A TRAVÉS DEL MODELO
	$ticket = Model_Ticket::find($ticket_id);

	# SI NO SE ENCUENTRA LA TAREA
	if(!$ticket)
	{
		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/crm/ticket');
	}

	# SI EL STATUS_ID ES 4, NO PERMITIR LA CANCELACIÓN
	if($ticket->status_id == 4)
	{
		# SE ESTABLECE EL MENSAJE DE ERROR
		Session::set_flash('error', 'Los ticket finalizados no pueden ser cancelados.');
		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/crm/ticket');
	}

	# GUARDAR LA HORA DE ACTUALIZACIÓN
	$currentDate        = time();
	$ticket->updated_at = $currentDate;

	# SE CANCELA LA TAREA
	$ticket->status_id = 5;
	$ticket->save();

	# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
		$admin_log = Model_User::query()
			->where('id', Auth::get('id'))
			->get_one();

		$admin_name = '';
		if(!empty($admin_log))
		{
			$admin_profile = unserialize($admin_log->profile_fields);
			$admin_name = $admin_profile['full_name'];
		}

		# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
		$asig_user_log = Model_Employee::query()
			->where('id', $ticket->asig_user_id)
			->get_one();

		$asig_user_name = '';
		if(!empty($asig_user_log))
		{
			$asig_user_name = $asig_user_log->name.' '.$asig_user_log->last_name;
		}

		# SE CREA EL MODELO CON LA INFORMACION
		$message = 'El empleado <b>'.$admin_name.'</b> cancelo el ticket que estaba asignado a <b>'.$asig_user_name.'</b>.';
		$log = new Model_Tickets_Log(array(
			'ticket_id' => $ticket->id,
			'message'   => $message,
			'color'     => 'text-danger',
			'date'      => time()
		));
		
		#SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
		$log->save();

	# SE REDICIRECIONA AL INDEX SIN LA TAREA CANCELADA
	Session::set_flash('success', 'Se ha cancelado el ticket y no aparecerá en su lista.');
	Response::redirect('admin/crm/ticket');
	}


	/**
	* UPDATE
	*
	* AGREGAR AQUÍ OTRAS ACCIONES DEL CONTROLADOR, COMO EDITAR Y ELIMINAR TICKETS.
	*
	* @access  public
	* @return  Void
	*/
	public function action_update($ticket_id)
	{
		# OBTENER EL TICKET POR SU ID
		$ticket = Model_Ticket::find($ticket_id);

		# VERIFICAR SI EL TICKET EXISTE
		if(!$ticket)
		{
			# SI EL TICKET NO EXISTE, REDIRIGIR A LA PÁGINA DE ERROR O MOSTRAR UN MENSAJE DE ERROR
			Session::set_flash('error', 'Ticket no encontrado.');
			Response::redirect('admin/crm/tickets/index');
		}

		# PROCESAR EL FORMULARIO DE ACTUALIZACIÓN
		if(Input::method() == 'POST')
		{
			$val = Validation::forge();
			$val->add_field('status', 'Estado', 'required');
			$val->add_field('completion_date', 'Fecha de Finalización', 'valid_date[Y-m-d]');

			if($val->run())
			{
				# ACTUALIZAR EL TICKET CON LOS DATOS PROPORCIONADOS
				$ticket->status = Input::post('status');
				$ticket->completion_date = Input::post('completion_date');
				$ticket->save();

				# REDIRIGIR AL USUARIO A LA LISTA DE TICKETS O MOSTRAR UN MENSAJE DE ÉXITO
				Session::set_flash('success', 'Ticket actualizado exitosamente.');
				Response::redirect('admin/crm/tickets/index');
			}
			else
			{
				# SI LA VALIDACIÓN FALLA, MOSTRAR EL FORMULARIO CON LOS MENSAJES DE ERROR
				$this->template->title   = 'Actualizar Ticket';
				$this->template->content = View::forge('admin/crm/tickets/editar');
				$this->template->content->set_safe('val', $val);
			}
		}
		else
		{
			# MOSTRAR EL FORMULARIO DE ACTUALIZACIÓN DE TICKETS
			$this->template->title   = 'Actualizar Ticket';
			$this->template->content = View::forge('admin/crm/tickets/editar');
		}
	}

	/**
	* CALIFICAR
	*
	* ALMACENA UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_calificar($ticket_id = 0, $rating = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($ticket_id == 0 || !is_numeric($ticket_id) || $rating == 0 || !is_numeric($rating))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/ticket');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$ticket = Model_Ticket::query()
		->where('id', $ticket_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($ticket))
		{
			# SI EXISTE UNA CALIFICACION VALIDA
			if($rating == 1 || $rating == 2)
			{
				# SE ESTABLECE LA NUEVA INFORMACION
				$ticket->rating = $rating;

				# SI SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
				if($ticket->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se guardó la calificación del ticket correctamente.');
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', 'No fue posible almacenar la calificación del ticket, por favor inténtalo nuevamente.');
				}
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/crm/ticket');
	}
}
