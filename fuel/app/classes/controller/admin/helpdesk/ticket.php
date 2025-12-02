<?php

class Controller_Admin_Helpdesk_Ticket extends Controller_Admin
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
		$data              = array();
		$tickets_info      = array();
		$per_page          = 100;
		$asig_user_opts    = array();
		$statusticket_opts = array();
		$search            = Input::get('s');
		$start_date        = Input::get('r1');
		$end_date          = Input::get('r2');
		$asig_user_id      = Input::get('asig_user');
		$status_id         = Input::get('status');

		# SI NO HAY RAGO DE FECHAS
 		if($start_date == 0 && $end_date == 0)
 		{
			# SE INICIALIZAN LOS RANGOS DE FECHAS
			$start_date = $this->date2unixtime(date('01'.'/m/Y', time()));
			$end_date   = $this->date2unixtime(date('d/m/Y', time()), 'end');
		}

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$tickets = Model_Ticket::query()
		->related('employee')
		->related('asiguser')
		->where('id', '>=', 0);

		# SI HAY UN RANGO DE FECHAS
		if($start_date != 0 && $end_date != 0)
		{
			# SE AGREGA LA CLAUSULA
			$tickets = $tickets->where('created_at', 'between', array($start_date, $end_date));
		}

		# SI HAY UN USUARIO ASIGANDO
		if($asig_user_id !== null)
		{
			# SE AGREGA LA CLAUSULA
			$tickets = $tickets->where('asig_user_id', '=', $asig_user_id);
		}

		# SI HAY UN ESTATUS
		if($status_id !== null)
		{
			# SE AGREGA LA CLAUSULA
			$tickets = $tickets->where('status_id', '=', $status_id);
		}

		# SI HAY UNA BUSQUEDA
		if($search != '')
		{
			# SE AGREGA LA CLAUSULA
			$tickets = $tickets->where(DB::expr("CONCAT(`t0`.`description`, ' ', `t1`.`name`, ' ', `t2`.`name`)"), 'like', '%' . $search . '%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::base(false).substr($_SERVER['REQUEST_URI'], 1),
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
					$asiguser = 'Aun no se ha asignado a un usuario';
				}
				else
				{
					# AQUÍ ASUMIMOS QUE ASIGUSER ES EL OBJETO DEL USUARIO ASIGNADO
					# SI ASIGUSER NO ES EL OBJETO DEL USUARIO ASIGNADO, AJUSTA ESTA LÍNEA EN CONSECUENCIA
					$asiguser = $ticket->asiguser->name;
				}

				# CALCULAR LA DIFERENCIA DE TIEMPO SIEMPRE Y CUANDO EL TICKET ESTE FINALIZADO
				if ($ticket->status_id == 4){
					if (!is_null($ticket->start_date) && !is_null($ticket->finish_date)) {
						# SI START TIENE DATO CALCULAR
						$time_to_solve = $this->calculate_time_difference($ticket->start_date, $ticket->finish_date);
						} else {
						# SI START NO TIENE CALCULAR CON CREATED Y UPDATE
						$time_to_solve = ($ticket->updated_at && $ticket->created_at) ? $this->calculate_time_difference($ticket->created_at, $ticket->updated_at) : 'N/A';
					}
				}else{
                    #SI EL TICKET NO ESTA FINALIZADO QUE ES EL 4 SE DEBE PONER EN TIEMPO 0
                    $time_to_solve = 0;
                }

				# SE ALMACENA LA INFORMACION
				$tickets_info[] = array(
					'id'            => $ticket->id,
					'type_id'       => $ticket->typeticket->name,
					'incident_id'   => $ticket->incidentticket->name,
					'description'   => $ticket->description,
					'status_id'     => $ticket->statusticket->name,
					'priority_id'   => $ticket->priorityticket->name,
					'department_id' => $ticket->employee->department->name,
					'employee_id'   => $ticket->employee->name . ' ' . $ticket->employee->last_name,
					'user_id'       => $ticket->user->username,
					'asig_user_id'  => $asiguser,
					'rating'        => $ticket->rating,
					'start'         => $ticket->start_date,
					'finish'        => $ticket->finish_date,
					'created_at'    => $ticket->created_at,
					'updated_at'    => $ticket->updated_at,
					'time_to_solve'	=> $time_to_solve
				);
			}
		}

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$asig_user_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$asig_users = Model_Employee::query()
		->where('department_id', '=', '1')
		//->order_by('username', 'asc')
		->get();

		# SI SE OBTIENE INFORMACIÓN
		if(!empty($asig_users))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($asig_users as $asig_user)
			{
				# SE ALMACENA LA OPCIÓN
				$asig_user_opts[$asig_user->id] = $asig_user->name . ' ' . $asig_user->last_name;
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['start_date']        = ($start_date != 0) ? date('d/m/Y', $start_date) : '';
		$data['end_date']          = ($end_date != 0) ? date('d/m/Y', $end_date) : '';
		$data['start_date_unix']   = ($start_date != 0) ? $start_date : '';
 		$data['end_date_unix']     = ($end_date != 0) ? $end_date : '';
		$data['asig_user_opts']    = $asig_user_opts;
		$data['statusticket_opts'] = $statusticket_opts;
		$data['tickets']           = $tickets_info;
		$data['search']            = str_replace('%', ' ', $search);
		$data['pagination']        = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title = 'Tickets';
		$this->template->content = View::forge('admin/helpdesk/ticket/index', $data, false);
	}


	/**
	* INDEX DE TICKETS ASIGNADOS AL USUARIO EN CUESTION
	*
	* MUESTRA UNA LISTADO DE REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_asignados($search = '')
	{
		# SE INICIALIZAN LAS VARIABLES
		$data         = array();
		$tickets_info = array();
		$per_page     = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$tickets = Model_Ticket::query()
		->related('employee')
		->where('id','>=', 0);

		# AGREGAR EL FILTRO PARA EL ID DEL USUARIO EN LA SESIÓN
		$user_id = Auth::get_user_id()[1]; // OBTIENE EL ID DEL USUARIO DE LA SESIÓN

		# BUSCAR EL EMPLEADO CORRESPONDIENTE AL USUARIO ACTUAL
		$employee = Model_Employee::query()
		->where('user_id', $user_id)
		->get_one();

		if($employee)
		{
			$tickets = $tickets->where('asig_user_id', $employee->id);
		}
		else
		{
			# NO SE ENCONTRÓ UN EMPLEADO CORRESPONDIENTE AL USUARIO ACTUAL, ASÍ QUE NO MOSTRAR TICKETS
			$tickets = $tickets->where('asig_user_id', -1);
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
			$tickets = $tickets->where(DB::expr("CONCAT(`t0`.`description`, ' ' , `t1`.`name`)"), 'like', '%'.$search.'%');
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
					'description'   => $ticket->description,
					'status_id'     => $ticket->statusticket->name,
					'priority_id'   => $ticket->priorityticket->name,
					'employee_id'   => $ticket->employee->name. ' '. $ticket->employee->last_name,
					'department_id' => $ticket->employee->department->name,
					'asig_user_id'  => $asiguser,
					'created_at' 	=> date('d/m/Y - H:i', $ticket->created_at),
					'updated_at' 	=>  $ticket->updated_at
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['tickets']        = $tickets_info;
		$data['search']         = str_replace('%', ' ', $search);
		$data['pagination']     = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Tickets';
		$this->template->content = View::forge('admin/helpdesk/ticket/asignados', $data, false);
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
				'search'     => ($_POST['search'] != '') ? $_POST['search'] : '',
				'start_date' => ($_POST['start_date'] != 0) ? $_POST['start_date'] : '',
				'end_date'   => ($_POST['end_date'] != 0) ? $_POST['end_date'] : '',
			);

			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('search');
			$val->add_callable('Rules');
			$val->add_field('search', 'search', 'max_length[100]');
			$val->add_field('start_date', 'start_date', 'excact_lengt[10]');
			$val->add_field('end_date', 'end_date', 'excact_lengt[10]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run($data))
			{
				# SE ALMACENA LA CADENA DE BUSQUEDA
				$search = ($val->validated('search') != '') ? $val->validated('search') : '';

				# SE ALMACENA LA CADENA DE BUSQUEDA
				$start_date = ($val->validated('start_date') != '') ? $this->date2unixtime($val->validated('start_date')) : 0;
				$end_date   = ($val->validated('end_date') != '') ? $this->date2unixtime($val->validated('end_date'), 'end') : 0;

				# SI EXISTE UNA BUSQUEDA
				if($search != '')
				{
					# SI EXISTE UN RANGO DE FECHAS
					if($start_date > 0 && $end_date > 0)
					{
						# SE REDIRECCIONA A BUSCAR
						Response::redirect('admin/helpdesk/ticket/index?s='.$search.'&r1='.$start_date.'&r2='.$end_date);
					}
					else
					{
						# SE REDIRECCIONA A BUSCAR
						Response::redirect('admin/helpdesk/ticket/index?s='.$search);
					}
				}
				else
				{
					# SI EXISTE UN RANGO DE FECHAS
					if($start_date > 0 && $end_date > 0)
					{
						# SE REDIRECCIONA A BUSCAR
						Response::redirect('admin/helpdesk/ticket/index?r1='.$start_date.'&r2='.$end_date);
					}
					else
					{
						# SE REDIRECCIONA AL USUARIO
						Response::redirect('admin/helpdesk/ticket');
					}
				}
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/helpdesk/ticket');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket');
		}
	}


	/**
	* BUSCAR ASIGNADOS
	*
	* REDIRECCIONA A LA URL DE BUSCAR REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_buscar_asig()
	{
		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# RECUPERAR EL ID DE USUARIO ALMACENADO EN LA SESIÓN
			$user_id = Session::get('user_id');

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
				Response::redirect('admin/helpdesk/ticket/asignados/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/helpdesk/ticket');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket');
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
			Response::redirect('admin/helpdesk/ticket');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data      = array();
		$logs_info = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$ticket = Model_Ticket::query()
		->where('id', $ticket_id)
		//->where('id','>=', 0)
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
				$asiguser = $ticket->asiguser->name.' '.$ticket->asiguser->last_name;
			}

			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$ticket_logs = Model_Tickets_Log::query()
			->where('ticket_id', $ticket_id)
			->order_by('id', 'asc')
	        ->get();

			# SI SE OBTIENE INFORMACION
			if(!empty($ticket_logs))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($ticket_logs as $log)
				{
					# SE ALMACENA LA INFORMACION
					$logs_info[] = array(
						'message' => $log->message,
						'color'   => $log->color,
						'date'    => date('d/m/Y - H:i', $log->date)
					);
				}
			}

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['type_id']      = $ticket->typeticket->name;
			$data['description']  = $ticket->description;
			$data['status_id']    = $ticket->statusticket->name;
			$data['priority_id']  = $ticket->priorityticket->name;
			$data['incident_id']  = $ticket->incidentticket->name;
			$data['user_id']      = $ticket->employee->name. ' '. $ticket->employee->last_name;
			$data['asig_user_id'] = $asiguser;
			$data['solution']     = $ticket->solution;
			$data['logs']         = $logs_info;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $ticket_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del ticket';
		$this->template->content = View::forge('admin/helpdesk/ticket/info', $data, false);
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
		$fields              = array('id', 'type_id', 'user_id', 'employee_id', 'incident_id', 'description', 'status_id', 'priority_id','asig_user_id', 'start_date', 'finish_date', 'date', 'time');
		$typeticket_opts     = array();
		$incidentticket_opts = array();
		$priorityticket_opts = array();
		$employee_opts       = array();
		$asig_user_opts      = array();
		$date                = date('d/m/Y');
		$time                = date('H:i');

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
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('ticket');
			$val->add_field('type_id', 'Tipo ticket', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('employee_id', 'Empleado solicitante', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('asig_user_id', 'Usuario de soporte', 'min_length[1]');
			$val->add_field('incident_id', 'Tipo de Incidente', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('description', 'Descripción detallada', 'required|min_length[1]');
			$val->add_field('priority_id', 'Tipo de Prioridad', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('date', 'fecha de publicación', 'required|date');
			$val->add_field('time', 'hora de publicación', 'required|min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# CREA UNA NUEVA INSTANCIA DEL MODELO SURVEY Y GUARDA LOS DATOS
				$user_id     = Auth::get('id');
				$currentDate = time();

				# SE CREA EL MODELO CON LA INFORMACION
				$ticket = new Model_Ticket(array(
					'type_id'      => $val->validated('type_id'),
					'incident_id'  => $val->validated('incident_id'),
					'description'  => $val->validated('description'),
					'user_id'      => $user_id,
					'employee_id'  => $val->validated('employee_id'),
					'asig_user_id' => $val->validated('asig_user_id'),
					'priority_id'  => $val->validated('priority_id'),
					'rating'       => 0,
					'created_at'   => $this->datetime2unixtime($val->validated('date'), $val->validated('time'))
				));

				# CAMBIAR EL STATUS_ID A "ASIGNADO" SOLO SI SE SELECCIONA UN USUARIO
				if(!empty($ticket->asig_user_id))
				{
					$ticket->status_id = '2';
				}

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($ticket->save())
				{
					# SE INICIALIZAN LAS VARIABLES
					$amdin_name     = '';
					$employee_name  = '';
					$asig_user_name = '';

					# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
					$admin_log = Model_User::query()
					->where('id', Auth::get('id'))
					->get_one();

					# SE SE OBTIENE INFORMACION
					if(!empty($admin_log))
					{
						# SE DESERIALIZAN LOS CAMPOS EXTRAS
						$admin_profile = unserialize($admin_log->profile_fields);

						# SE ALMACENA EL NOMBRE DEL ADMINISTRADOR
						$admin_name = $admin_profile['full_name'];
					}

					# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
					$employee_log = Model_Employee::query()
					->where('id', $val->validated('employee_id'))
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
						'message'   => 'El administrador <b>'.$admin_name.'</b> creó el ticket del empleado <b>'.$employee_name.'</b>, asignado a <b>'.$asig_user_name.'</b>.',
						'color'     => 'text-success',
						'date'      => time()
					));

					# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
					$log->save();

					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el ticket <b>' . Input::post('subject') . '</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/helpdesk/ticket');
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

				# SE ALMACENLA LA INFORMACION DE LA FECHA DE PUBLICACION
				$date = (Input::post('date') != '') ? Input::post('date') : $date;
				$time = (Input::post('time') != '') ? Input::post('time') : $time;
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

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$employee_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$employees = Model_Employee::query()
		->get();

		# SI SE OBTIENE INFORMACIÓN
		if(!empty($employees))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($employees as $employee)
			{
				# CONCATENAR NAME Y LAST_NAME
				$full_name = $employee->name.' '.$employee->last_name;

				# SE ALMACENA LA OPCIÓN
				$employee_opts[$employee->id] = $full_name;
			}
		}

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$asig_user_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$asig_users = Model_Employee::query()
		->where('department_id','=','1')
		//->order_by('username', 'asc')
		->get();

		# SI SE OBTIENE INFORMACIÓN
		if(!empty($asig_users))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($asig_users as $asig_user)
			{
				# SE ALMACENA LA OPCIÓN
				$asig_user_opts[$asig_user->id] = $asig_user->name. ' '. $asig_user->last_name;
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['classes']             = $classes;
		$data['typeticket_opts']     = $typeticket_opts;
		$data['incidentticket_opts'] = $incidentticket_opts;
		$data['priorityticket_opts'] = $priorityticket_opts;
		$data['employee_opts']       = $employee_opts;
		$data['asig_user_opts']      = $asig_user_opts;
		$data['date']                = $date;
		$data['time']                = $time;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar Ticket';
		$this->template->content = View::forge('admin/helpdesk/ticket/agregar', $data);
	}


	/**
	* EDITAR
	*
	* PERMITE EDITAR UN REGISTRO A LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_editar($ticket_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($ticket_id == 0 || !is_numeric($ticket_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data                = array();
		$classes             = array();
		$fields              = array('type_id', 'user_id','incident_id', 'description', 'status_id', 'priority_id','asig_user_id','solution','employee_id','date','start_date', 'finish_date', 'updated_at','created_at');
		$typeticket_opts     = array();
		$incidentticket_opts = array();
		$statusticket_opts   = array();
		$priorityticket_opts = array();
		$asig_user_opts      = array();
		$employee_opts       = array();
		$logs_info           = array();
		$date                = date('d/m/Y');

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
		$ticket = Model_Ticket::query()
		->where('id', $ticket_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($ticket))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$ticket_logs = Model_Tickets_Log::query()
			->where('ticket_id', $ticket_id)
			->order_by('id', 'asc')
	        ->get();

			# SI SE OBTIENE INFORMACION
			if(!empty($ticket_logs))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($ticket_logs as $log)
				{
					# SE ALMACENA LA INFORMACION
					$logs_info[] = array(
						'message' => $log->message,
						'color'   => $log->color,
						'date'    => date('d/m/Y - H:i', $log->date)
					);
				}
			}

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['type_id']      = $ticket->type_id;
			$data['incident_id']  = $ticket->incident_id;
			$data['description']  = $ticket->description;
			$data['status_id']    = $ticket->status_id;
			$data['priority_id']  = $ticket->priority_id;
			$data['asig_user_id'] = $ticket->asig_user_id;
			$data['employee_id']  = $ticket->employee_id;
			$data['empleado']     = $ticket->employee->name;
			$data['username']     = $ticket->user->username;
			$data['solution']     = $ticket->solution;
			//$data['date']     	  = date('d/m/Y', $ticket->created_at);
			$data['created_at']   = date('d/m/Y', $ticket->created_at);
			$data['logs']         = $logs_info;

			 # Guardar el valor original de created_at
        	$data['original_created_at'] = date('d/m/Y', $ticket->created_at);
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('ticket');
			$val->add_callable('Rules');
			$val->add_field('type_id', 'Tpo', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('incident_id', 'Incidente', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('description', 'Descripcion', 'required');
			$val->add_field('status_id', 'Estatus', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('priority_id', 'Prioridad', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('asig_user_id', 'Usuario', 'min_length[1]');
			$val->add_field('employee_id', 'Usuario', 'min_length[1]');
			$val->add_field('created_at', 'fecha de publicación', 'required|date');
			$val->add_field('solution', 'Solucion', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# FECHA DE CIERRE
				$currentDate = time();
				$inputDate = $this->datetime2unixtime($val->validated('created_at'));

				# SE ESTEBLECE LA NUEVA INFORMACION
				$ticket->type_id      = $val->validated('type_id');
				$ticket->incident_id  = $val->validated('incident_id');
				$ticket->description  = $val->validated('description');
				$ticket->status_id    = $val->validated('status_id');
				$ticket->priority_id  = $val->validated('priority_id');
				$ticket->asig_user_id = $val->validated('asig_user_id');
				$ticket->employee_id  = $val->validated('employee_id');
				$ticket->solution     = $val->validated('solution');
				$ticket->updated_at   = $currentDate;
								
				 # ACTUALIZAR CREATED_AT SOLO SI LA FECHA INGRESADA ES DIFERENTE DE LA FECHA ACTUAL
					if ($val->validated('created_at') != $data['original_created_at']) {
						$ticket->created_at = $inputDate;
					}

				# SI EL ESTATUS ES FINALIZADO Y START_DATE Y FINISH_DATE ESTÁN VACÍOS
				if ($ticket->status_id == 4) {  
					if (empty($ticket->start_date)) {
						$ticket->start_date = $currentDate;
					}
					if (empty($ticket->finish_date)) {
						$ticket->finish_date = $currentDate;
					}
				} elseif ($ticket->status_id == 3) {
					# SI EL ESTATUS ES 3, SOLO PONER LA HORA EN START_DATE
					if (empty($ticket->start_date)) {
						$ticket->start_date = $currentDate;
					}
				}


				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($ticket->save())
				{
					# SE INICIALIZAN LAS VARIABLES
					$amdin_name     = '';
					$employee_name  = '';
					$asig_user_name = '';

					# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
					$admin_log = Model_User::query()
					->where('id', Auth::get('id'))
					->get_one();

					# SE SE OBTIENE INFORMACION
					if(!empty($admin_log))
					{
						# SE DESERIALIZAN LOS CAMPOS EXTRAS
						$admin_profile = unserialize($admin_log->profile_fields);

						# SE ALMACENA EL NOMBRE DEL ADMINISTRADOR
						$admin_name = $admin_profile['full_name'];
					}

					# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
					$employee_log = Model_Employee::query()
					->where('id', $val->validated('employee_id'))
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

					# SI LA SOLUCION DEL TICKET CAMBIO
					if($data['solution'] != $val->validated('solution'))
					{
						# SE ESTABLECE EL MENSAJE
						$message = 'El administrador <b>'.$admin_name.'</b> agregó una solución al ticket del empleado <b>'.$employee_name.'</b>, asignado a <b>'.$asig_user_name.'</b>.';
					}
					else
					{
						# SE ESTABLECE EL MENSAJE
						$message = 'El administrador <b>'.$admin_name.'</b> editó el ticket del empleado <b>'.$employee_name.'</b>, asignado a <b>'.$asig_user_name.'</b>.';
					}

					# SE CREA EL MODELO CON LA INFORMACION
					$log = new Model_Tickets_Log(array(
						'ticket_id' => $ticket->id,
						'message'   => $message,
						'color'     => 'text-warning',
						'date'      => time()
					));

					# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
					$log->save();

					# SI EL ESTATUS DEL TICKET CAMBIO
					if($data['status_id'] != $val->validated('status_id'))
					{
						# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
						$status_log = Model_Tickets_Status::query()
						->where('id', $val->validated('status_id'))
						->get_one();

						# SE SE OBTIENE INFORMACION
						if(!empty($status_log) && $admin_name != '')
						{
							# SE ESTABLECE EL MENSAJE
							$message = 'El administrador <b>'.$admin_name.'</b> cambió el estatus del ticket a <b>'.$status_log->name.'</b>.';

							# SE CREA EL MODELO CON LA INFORMACION
							$log = new Model_Tickets_Log(array(
								'ticket_id' => $ticket->id,
								'message'   => $message,
								'color'     => 'text-danger',
								'date'      => time()
							));

							# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
							$log->save();
						}
					}

					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de <b>'.$ticket->id.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/helpdesk/ticket/editar/'.$ticket_id);
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
				
				# SE ALMACENLA LA INFORMACION DE LA FECHA DE PUBLICACION
				$date = (Input::post('date') != '') ? Input::post('date') : $date;
			}
		}

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$typeticket_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$types = Model_Tickets_Type::query()
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACIÓN
		if (!empty($types)) {
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach ($types as $type) {
				# SE ALMACENA LA OPCIÓN
				$typeticket_opts[$type->id] = $type->name;
			}
		}

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$incidentticket_opts += array('0' => 'Selecciona una opción');

		# SI EL USUARIO SELECCIONO UN TIPO
		if($ticket->type_id != 0)
		{
			# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
			$incidents = Model_Tickets_Incident::query()
			->where('type_id', $ticket->type_id)
			->order_by('name', 'asc')
			->get();

			# SI SE OBTIENE INFORMACIÓN
			if(!empty($incidents))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($incidents as $incident)
				{
					# SE ALMACENA LA OPCIÓN
					$incidentticket_opts[$incident->id] = $incident->name;
				}
			}
		}

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

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$employee_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$employees = Model_Employee::query()
		->get();

		# SI SE OBTIENE INFORMACIÓN
		if(!empty($employees))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($employees as $employee)
			{
				# SE ALMACENA LA OPCIÓN
				$employee_opts[$employee->id] = $employee->name.' ' . $employee->last_name;
			}
		}

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$asig_user_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$asig_users = Model_Employee::query()
		->where('department_id','=','1')
		//->order_by('username', 'asc')
		->get();

		# SI SE OBTIENE INFORMACIÓN
		if(!empty($asig_users))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($asig_users as $asig_user)
			{
				# SE ALMACENA LA OPCIÓN
				$asig_user_opts[$asig_user->id] = $asig_user->name . ' '. $asig_user->last_name;
			}
		}

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$statusticket_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$status = Model_Tickets_Status::query()
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACIÓN
		if(!empty($status))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($status as $statu)
			{
				# SE ALMACENA LA OPCIÓN
				$statusticket_opts[$statu->id] = $statu->name;
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']                  = $ticket_id;
		$data['classes']             = $classes;
		$data['typeticket_opts']     = $typeticket_opts;
		$data['incidentticket_opts'] = $incidentticket_opts;
		$data['statusticket_opts']   = $statusticket_opts;
		$data['priorityticket_opts'] = $priorityticket_opts;
		$data['asig_user_opts']      = $asig_user_opts;
		$data['employee_opts']       = $employee_opts;
		$data['date']                = $date;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar Ticket';
		$this->template->content = View::forge('admin/helpdesk/ticket/editar', $data, false);
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
			Response::redirect('admin/helpdesk/ticket');
		}

		# SE BUSCA EL TICKET A TRAVÉS DEL MODELO
		$ticket = Model_Ticket::find($ticket_id);

		# SI NO SE ENCUENTRA EL TICKET
		if(!$ticket)
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket');
		}

		# VALIDAR QUE EL TICKET TENGA UN USUARIO ASIGNADO
		if(empty($ticket->asig_user_id))
		{
			# MOSTRAR MENSAJE DE ERROR
			Session::set_flash('error', 'El ticket debe tener un usuario asignado y haber pasado por sus procesos antes de marcarlo como "Finalizado".');
			Response::redirect('admin/helpdesk/ticket/editar/'.$ticket_id);
		}

		# GUARDAR LA HORA DE ACTUALIZACIÓN
		$currentDate        = time();
		$ticket->updated_at = $currentDate;

		# ACTUALIZAR SOLO SI ESTA VACIO
		if (empty($ticket->start_date)){
		$ticket->start_date = $currentDate;
		}

		# SIEMPRE ACTUALIZAR
		$ticket->finish_date = $currentDate;
		

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
		$message = 'El administrador <b>'.$admin_name.'</b> da por concluido el ticket del empleado <b>'.$asig_user_name.'</b> y se cierra por completo.';
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
		Response::redirect('admin/helpdesk/ticket');
	}

	/**
	* ASIGNAR TICKET
	*
	* MUESTRA UNA LISTADO DE REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_asignar($ticket_id = 0)
	{

		# SE INICIALIZAN LAS VARIABLES
		$logs_info           = array();

		# SI NO SE RECIBE UN ID DE TICKET O NO ES UN NÚMERO VÁLIDO
		if($ticket_id == 0 || !is_numeric($ticket_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket');
		}

		# SE BUSCA EL TICKET A TRAVÉS DEL MODELO
		$ticket = Model_Ticket::find($ticket_id);

		# SI NO SE ENCUENTRA EL TICKET
		if(!$ticket)
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket');
		}

		# VALIDAR QUE EL TICKET TENGA UN USUARIO ASIGNADO
		if(empty($ticket->asig_user_id))
		{
			# MOSTRAR MENSAJE DE ERROR
			Session::set_flash('error', 'El ticket debe tener un usuario asignado".');
			Response::redirect('admin/helpdesk/ticket/editar/'.$ticket_id);
		}

		# GUARDAR LA HORA DE ACTUALIZACIÓN
		$currentDate        = time();
		$ticket->updated_at = $currentDate;

		# CAMBIAR EL ESTADO A "FINALIZADO"
		$ticket->status_id = 2;
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
		$message = 'El administrador <b>'.$admin_name.'</b> asignó ticket del empleado <b>'.$asig_user_name.'</b>.';
		$log = new Model_Tickets_Log(array(
			'ticket_id' => $ticket->id,
			'message'   => $message,
			'color'     => 'text-warning',
			'date'      => time()
		));
		
		#SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
		$log->save();

		# SE REDIRECCIONA AL USUARIO (OPCIONAL)
		Session::set_flash('success', 'El ticket fue asignado correctamente.');
		Response::redirect('admin/helpdesk/ticket');
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
			Response::redirect('admin/helpdesk/ticket');
		}

		# SE BUSCA EL TICKET A TRAVÉS DEL MODELO
		$ticket = Model_Ticket::find($ticket_id);

		# SI NO SE ENCUENTRA EL TICKET
		if(!$ticket)
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket');
		}

		# VALIDAR QUE EL TICKET TENGA UN USUARIO ASIGNADO
		if(empty($ticket->asig_user_id && $ticket->status_id == 2 ))
		{
			# MOSTRAR MENSAJE DE ERROR
			Session::set_flash('error', 'El ticket debe de estar asignado con un usuario para poder "INICIAR".');
			Response::redirect('admin/helpdesk/ticket/editar/'.$ticket_id);
		}

		# GUARDAR LA HORA DE ACTUALIZACIÓN
		$currentDate        = time();
		$ticket->updated_at = $currentDate;
		$ticket->start_date = $currentDate;

		# CAMBIAR EL ESTADO A "FINALIZADO"
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
		$message = 'El administrador <b>'.$admin_name.'</b> inicio el ticket del empleado <b>'.$asig_user_name.'</b>.';
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
		Response::redirect('admin/helpdesk/ticket');
	}

	/**
	* CERRAR TICKET
	*
	* MUESTRA UNA LISTADO DE REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_cerrar($ticket_id = 0)
	{

		# SE INICIALIZAN LAS VARIABLES
		$logs_info           = array();


		# SI NO SE RECIBE UN ID DE TICKET O NO ES UN NÚMERO VÁLIDO
		if($ticket_id == 0 || !is_numeric($ticket_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket');
		}

		# SE BUSCA EL TICKET A TRAVÉS DEL MODELO
		$ticket = Model_Ticket::find($ticket_id);

		# SI NO SE ENCUENTRA EL TICKET
		if(!$ticket)
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket');
		}

		# VALIDAR QUE EL TICKET TENGA UN USUARIO ASIGNADO
		if(empty($ticket->asig_user_id && $ticket->status_id == 3))
		{
			# MOSTRAR MENSAJE DE ERROR
			Session::set_flash('error', 'El ticket debe estar asignado y en estatus de atendiendo para poder "FINALIZAR".');
			Response::redirect('admin/helpdesk/ticket/editar/'.$ticket_id);
		}

		# GUARDAR LA HORA DE ACTUALIZACIÓN
		$currentDate        = time();
		$ticket->updated_at = $currentDate;
		$ticket->finish_date = $currentDate;

		# CAMBIAR EL ESTADO A "ATENDIENDO"
		$ticket->status_id = 6;
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
		$message = 'El administrador <b>'.$admin_name.'</b> finalizo el ticket del empleado <b>'.$asig_user_name.'</b>.';
		$log = new Model_Tickets_Log(array(
			'ticket_id' => $ticket->id,
			'message'   => $message,
			'color'     => 'text-success',
			'date'      => time()
		));
		
		#SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
		$log->save();

		# SE REDIRECCIONA AL USUARIO (OPCIONAL)
		Session::set_flash('success', 'El ticket fue atendido correctamente.');
		Response::redirect('admin/helpdesk/ticket');
	}


	/**
	* GRAFICAS
	*
	* MUESTRA LOS REGISTROS GRAFICADOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_graficas()
	{
		# SE INICIALIZAN LAS VARIABLES
		$tickets_info 	 = array();
		$asiguser_json 	 = array();
		$tickets_json 	 = array();
		$tickets_json2 	 = array(); // ARREGLO PARA LOS DATOS DE OTRA GRÁFICA
		$status_json 	 = array(); // ARREGLO PARA LOS DATOS DE ESTADO
		$tickets_json3 	 = array();
		$type_json 		 = array();
		$tickets_json4 	 = array();
		$department_json = array();
		$start_date      = Input::get('r1');
		$end_date        = Input::get('r2');

		# SI NO HAY RAGO DE FECHAS
 		if($start_date == 0 && $end_date == 0)
 		{
			# SE INICIALIZAN LOS RANGOS DE FECHAS
			$start_date = $this->date2unixtime(date('01'.'/m/Y', time()));
			$end_date   = $this->date2unixtime(date('d/m/Y', time()), 'end');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$tickets = Model_Ticket::query()
		->related('asiguser');

		# SI HAY UN RANGO DE FECHAS
		if($start_date != 0 && $end_date != 0)
		{
			# SE AGREGA LA CLAUSULA
			$tickets = $tickets->where('created_at', 'between', array($start_date, $end_date));
		}

		# SE EJECUTA EL QUERY
		$tickets = $tickets->order_by('asig_user_id', 'asc')
		->group_by('asig_user_id')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($tickets))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($tickets as $ticket)
			{
				# SE OBTIENE LA REFERENCIA A TRAVES DEL MODELO
				$tickets_count = Model_Ticket::query()->where('asig_user_id', $ticket->asig_user_id);

				# SI HAY UNA BUSQUEDA
				if($start_date != 0 && $end_date != 0)
				{
					# SE AGREGA LA CLAUSULA
					$tickets_count = $tickets_count->where('created_at', 'between', array($start_date, $end_date));
				}

				# SE ALMACENA LA INFORMACION
				$tickets_info[] = array(
					'user' => (!empty($ticket->asiguser->name)) ? $ticket->asiguser->name : 'Sin nombre',
					'tickets' => $tickets_count->count()
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$tickets2 = Model_Ticket::query()
		->related('statusticket');

		# SI HAY UNA BUSQUEDA
		if($start_date != 0 && $end_date != 0)
		{
			# SE AGREGA LA CLAUSULA
			$tickets2 = $tickets2->where('created_at', 'between', array($start_date, $end_date));
		}

		# SE EJECUTA EL QUERY
		$tickets2 = $tickets2->order_by('status_id', 'asc')
		->group_by('status_id')
		->get();

		# SI EXISTE INFORMACION RELACIONADA CON EL ESTADO
		if(!empty($tickets2))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($tickets2 as $ticket)
			{
				# SE OBTIENE LA REFERENCIA A TRAVES DEL MODELO
				$tickets_count_status = Model_Ticket::query()->where('status_id', $ticket->status_id);

				# SI HAY UNA BUSQUEDA
				if($start_date != 0 && $end_date != 0)
				{
					# SE AGREGA LA CLAUSULA
					$tickets_count_status = $tickets_count_status->where('created_at', 'between', array($start_date, $end_date));
				}

				# ALMACENA LA INFORMACIÓN RELACIONADA CON EL ESTADO
				$status_info[] = array(
					'status' => (!empty($ticket->statusticket->name)) ? $ticket->statusticket->name : 'Sin nombre',
					'tickets2' => $tickets_count_status->count()
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$tickets3 = Model_Ticket::query()
		->related('typeticket');

		# SI HAY UNA BUSQUEDA
		if($start_date != 0 && $end_date != 0)
		{
			# SE AGREGA LA CLAUSULA
			$tickets3 = $tickets3->where('created_at', 'between', array($start_date, $end_date));
		}

		# SE EJECUTA EL QUERY
		$tickets3 = $tickets3->order_by('type_id', 'asc')
		->group_by('type_id')
		->get();

		# SI EXISTE INFORMACION RELACIONADA CON EL TIPO
		if(!empty($tickets3))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($tickets3 as $ticket)
			{
				# SE OBTIENE LA REFERENCIA A TRAVES DEL MODELO
				$tickets_count_types = Model_Ticket::query()->where('type_id', $ticket->type_id);

				# SI HAY UNA BUSQUEDA
				if($start_date != 0 && $end_date != 0)
				{
					# SE AGREGA LA CLAUSULA
					$tickets_count_types = $tickets_count_types->where('created_at', 'between', array($start_date, $end_date));
				}

				# ALMACENA LA INFORMACIÓN RELACIONADA CON EL TIPO
				$type_info[] = array(
					'type'     => (!empty($ticket->typeticket->name)) ? $ticket->typeticket->name : 'Sin nombre',
					'tickets3' => $tickets_count_types->count()
				);
			}
		}

		# SQL CRUDO PARA OBTENER DATOS RELACIONADOS CON LOS DEPARTAMENTOS
		$query = DB::query('SELECT d.name,  COUNT(*) as ticket_count FROM tickets t
		LEFT JOIN employees e ON t.employee_id = e.id
		INNER JOIN employees_departments d on e.department_id = d.id
		GROUP BY d.id
		ORDER BY d.id ASC');
		$department_data = $query->execute()->as_array();

		# SI EXISTE INFORMACION RELACIONADA CON EL DEPARTAMENTO
		if(!empty($department_data))
		{
			foreach($department_data as $department)
			{
				# ALMACENA LA INFORMACIÓN RELACIONADA CON EL TIPO
				$department_info[] = array(
					'department' => $department['name'],
					'tickets4'   => $department['ticket_count']
				);
			}
		}

		# SI EXISTE INFORMACION
		if(!empty($tickets_info))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($tickets_info as $key => $ticket)
			{
				# SE ALMACENA LA INFORMACION
				Arr::insert($asiguser_json, $ticket['user'], $key);
				Arr::insert($tickets_json, $ticket['tickets'], $key);
			}
		}

		# SI EXISTE INFORMACION RELACIONADA CON EL ESTADO
		if(!empty($status_info))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($status_info as $key => $ticket)
			{
				# SE ALMACENA LA INFORMACION EN EL ARREGLO DE ESTADO
				Arr::insert($status_json, $ticket['status'], $key);
				Arr::insert($tickets_json2, $ticket['tickets2'], $key);
			}
		}

		# SI EXISTE INFORMACION RELACIONADA CON EL TIPO
		if(!empty($type_info))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($type_info as $key => $ticket)
			{
				# SE ALMACENA LA INFORMACION EN EL ARREGLO DE TIPO
				Arr::insert($type_json, $ticket['type'], $key);
				Arr::insert($tickets_json3, $ticket['tickets3'], $key);
			}
		}

		# SI EXISTE INFORMACION RELACIONADA CON EL DEPARTAMENTO
		if(!empty($department_info))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($department_info as $key => $ticket)
			{
				# SE ALMACENA LA INFORMACION EN EL ARREGLO DE DEPARTAMENTO
				Arr::insert($department_json, $ticket['department'], $key);
				Arr::insert($tickets_json4, $ticket['tickets4'], $key);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['users']      = json_encode($asiguser_json);
		$data['tickets']    = json_encode($tickets_json);
		$data['tickets2']   = json_encode($tickets_json2);
		$data['status']     = json_encode($status_json);
		$data['type']       = json_encode($type_json);
		$data['tickets3']   = json_encode($tickets_json3);
		$data['department'] = json_encode($department_json);
		$data['tickets4']   = json_encode($tickets_json4);
		$data['start_date'] = ($start_date != 0) ? date('d/m/Y', $start_date) : '';
		$data['end_date']   = ($end_date != 0) ? date('d/m/Y', $end_date) : '';

		# SE CARGA LA VISTA
		$this->template->title   = 'Tickets';
		$this->template->content = View::forge('admin/helpdesk/ticket/graficas', $data, false);
	}


	/**
	 * BUSCAR_GRAFICAS
	 *
	 * REDIRECCIONA A LA URL DE BUSCAR REGISTROS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_buscar_graficas()
	{
		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE OBTIENEN LOS VALORES
			$data = array(
				'start_date' => ($_POST['start_date'] != 0) ? $_POST['start_date'] : '',
				'end_date'   => ($_POST['end_date'] != 0) ? $_POST['end_date'] : '',
			);

			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('search');
			$val->add_callable('Rules');
			$val->add_field('start_date', 'start_date', 'excact_lengt[10]');
			$val->add_field('end_date', 'end_date', 'excact_lengt[10]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run($data))
			{
				# SE ALMACENA LA CADENA DE BUSQUEDA
				$start_date = ($val->validated('start_date') != '') ? $this->date2unixtime($val->validated('start_date')) : 0;
				$end_date   = ($val->validated('end_date') != '') ? $this->date2unixtime($val->validated('end_date'), 'end') : 0;

				# SE REDIRECCIONA A BUSCAR
				Response::redirect('admin/helpdesk/ticket/graficas/index?r1='.$start_date.'&r2='.$end_date);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/helpdesk/ticket/graficas');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/ticket/graficas');
		}
	}

	/**
	 * DATE2UNIXTIME
	 *
	 * CONVIERTE UNA FECHA EN UNIXTIME
	 *
	 * @access  private
	 * @return  Int
	 */
	private function date2unixtime($date = 0, $type = 'start')
	{
		# SE ESTABLECE EL VALOR DE DATE
		$date = ($date != 0) ? $date : date('d/m/Y');

		# SE CORTA LA CADENA POR LAS DIAGONALES
		$date = explode('/', $date);

		# SE DEVUELVE EL UNIXTIME
		return ($type == 'start') ? mktime(0, 0, 0, $date[1], $date[0], $date[2]) : mktime(23, 59, 59, $date[1], $date[0], $date[2]);
	}


	/**
	* DATETIME2UNIXTIME
	*
	* CONVIERTE UNA FECHA EN UNIXTIME
	*
	* @access  private
	* @return  Int
	*/
	private function datetime2unixtime($date = 0, $time = 0)
	{
		# SE ESTABLECE LA FECHA
		$date = ($date != 0) ? $date : date('d/m/Y');
		$time = ($time != 0) ? $time : date('H:i');

		# SE CORTA LAS CADENAS
		$date = explode('/', $date);
		$time = explode(':', $time);

		# SE DEVUELVE EL UNIXTIME
		return mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
	}

	private function calculate_time_difference($start, $end)
	{
		# CALCULAR LA DIFERENCIA EN SEGUNDOS
		$diff = $end - $start;

		# CALCULAR LOS MINUTOS
		$minutes = floor($diff / 60);

		# DEVOLVER EL FORMATO DE TIEMPO
		return sprintf('%d M', $minutes);
	}
}
