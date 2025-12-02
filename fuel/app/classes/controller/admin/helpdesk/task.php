<?php

class Controller_Admin_Helpdesk_Task extends Controller_Admin
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
		if(!Auth::member(100) && !Auth::member(50) && !Auth::member(25) && !Auth::member(20))
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
		$tasks_info = array();
		$per_page   = 100;

		# OBTENER LOS DATOS DE LOS PARÁMETROS GET
		$employee_id = Input::get('employee_id');
		$status_id = Input::get('status');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$tasks = Model_Task::query()
		->related('employee')
		->order_by('status_id','asc')
		->where('id','>=', 0);

		# SI SE HAN ENVIADO DATOS GET, AJUSTAR LA CONSULTA
		if($employee_id !== null)
		{
			$tasks = $tasks->where('employee_id', '=', $employee_id);
		}

		if($status_id !== null)
		{
			$tasks = $tasks->where('status_id', '=', $status_id);
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
			$tasks = $tasks->where(DB::expr("CONCAT(`t0`.`description`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $tasks->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
    		'show_last'      => true,
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('tickets', $config);

		# SE EJECUTA EL QUERY
		$tasks = $tasks->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SE RECORRE ELEMENTO POR ELEMENTO
		foreach($tasks as $task)
		{
			# SI SE OBTIENE INFORMACION
			if(!empty($tasks))
			{
				# VERIFICAR QUE SE TENGA EL EMPLEADO ASIGNADO
				if($task->employee)
				{
					$user_id = $task->employee->name;
				}
				else
				{
					$user_id = 'Aun no se ha asignado a un usuario';
				}

				# SE ALMACENA LA INFORMACION
				$tasks_info[] = array(
					'id'            => $task->id,
					'description'   => Str::truncate($task->description, '60', '...'),
					'status_id'     => $task->statusticket->name,
					'department_id' => $task->employee->department->name,
					'employee_id' 	=> $task->employee->name,
					'user_id'       => $task->user->username,
					'comments'       => Str::truncate($task->comments, '60','...'),
					'created_at'    => date('d/m/Y', $task->created_at),
					'updated_at'    => date('d/m/Y', $task->updated_at),
					'commitment_at' => date('d/m/Y', $task->commitment_at),
					'finish_at' 	=> !empty($task->finish_at) ? date('d/m/Y', $task->finish_at) : ''
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['tasks']      = $tasks_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Tareas Pendientes';
		$this->template->content = View::forge('admin/helpdesk/task/index', $data, false);
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
				Response::redirect('admin/helpdesk/task/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/helpdesk/task');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/task');
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
	public function action_info($task_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($task_id == 0 || !is_numeric($task_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/task');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$task = Model_Task::query()
		->where('id', $task_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($task))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['user_id']       = $task->employee->name.' '.$task->employee->last_name;
			$data['description']   = $task->description;
			$data['comments']      = $task->comments;
			$data['created_at']    = date('d/m/Y', $task->created_at);
			$data['commitment_at'] = date('d/m/Y',$task->commitment_at);
			$data['finish_at']     = !empty($task->finish_at) ? date('d/m/Y',$task->finish_at) : '';
			$data['status_id']     = $task->statusticket->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/task');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $task_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del la tarea pendiente';
		$this->template->content = View::forge('admin/helpdesk/task/info', $data);
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
		$data          = array();
		$classes       = array();
		$fields        = array('employee_id', 'description', 'date');
		$employee_opts = array();
		$date          = date('d/m/Y');
		$time          = date('H:i');


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
			# ESTADO DE ELIMINADO
			$deleted = (0);

			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('task');
			$val->add_field('employee_id', 'Empleado asignado', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('description', 'Descripción', 'required');
			$val->add_field('date', 'fecha de publicación', 'required|date');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE OBTINE LA HORA Y SE GRABA
				$currentDate = time();

				# OBTIENE EL ID DEL USUSARIO DEL EMPLEADO ASOCIADO
				$employee_id = $val->validated('employee_id');
				$employee    = Model_Employee::find($employee_id);
				$user_id     = $employee->user_id;

				# SE CREA EL MODELO CON LA INFORMACION
				$task = new Model_Task(array(
					'description'     	=> $val->validated('description'),
					'user_id'         	=> $user_id,
					'employee_id'     	=> $val->validated('employee_id'),
					'created_at'      	=> $currentDate,
					'deleted'      		=> $deleted,
					'commitment_at'     =>$this->date2unixtime($val->validated('date'))
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($task->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el pendiente correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/helpdesk/task');
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');

				# SE ALMACENA LOS ERRORES DETECTADOS
				$data['errors'] = $val->error();

				# SE RECORRE CLASE POR CLASE
				foreach ($classes as $name => $class) {
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

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['classes']       = $classes;
		$data['date']          = $date;
		$data['employee_opts'] = $employee_opts;
		$data['time']          = $time;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar Pendiente';
		$this->template->content = View::forge('admin/helpdesk/task/agregar', $data);
	}


	/**
	* EDITAR
	*
	* PERMITE EDITAR UN REGISTRO A LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_editar($task_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($task_id == 0 || !is_numeric($task_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/task');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data            = array();
		$classes         = array();
		$fields          = array('id', 'created_at', 'updated_at', 'finish_at' ,'commitment_at' , 'description', 'status_id', 'user_id', 'comments', 'date','time','employee_id');
		$statustask_opts = array();
		$employee_opts   = array();
		$date            = date('d/m/Y');
		$time            = date('H:i');

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
		$task = Model_Task::query()
		->where('id', $task_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($task))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['id']              = $task->id;
			$data['user_id']         = $task->employee->name . ' ' . $task->employee->last_name;
			$data['employee_id']     = $task->employee_id;
			$data['description']     = $task->description;
			$data['comments']        = $task->comments;
			$data['created_at']      = date('d/m/Y', $task->created_at);
			$data['commitment_date'] = !empty($task->commitment_at) ? date('d/m/Y', $task->commitment_at) : '';
			$data['date']            = !empty($task->finish_at) ? date('d/m/Y', $task->finish_at) : '';
			$data['status_id']       = $task->status_id;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/task');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('task');
			$val->add_callable('Rules');
			$val->add_field('description', 'Descripción', 'min_length[1]');
			$val->add_field('comments', 'comentarios', 'min_length[1]');
			$val->add_field('commitment_date', 'fecha de finalizacón', 'date min_length[1]');
			$val->add_field('date', 'fecha de finalizacón', 'date min_length[1]');
			$val->add_field('employee_id', 'Empleado', 'min_length[1]');
			$val->add_field('status_id', 'Estatus', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# FECHA DE CIERRE
				$currentDate = time();

				# OBTIENE EL ID DEL USUSARIO DEL EMPLEADO ASOCIADO
				$employee_id = $val->validated('employee_id');
				$employee = Model_Employee::find($employee_id);
				$user_id = $employee->user_id;

				# SE ESTEBLECE LA NUEVA INFORMACION
				$task->comments    = $val->validated('comments');
				$task->description = $val->validated('description');
				$task->user_id     = $user_id;
				$task->employee_id = $val->validated('employee_id');
				$task->status_id   = $val->validated('status_id');
				$task->updated_at  = $currentDate;

				# SI SE HA PROPORCIONADO UNA NUEVA FECHA SE ACTULIZA EL CAMPO SI NO QUEDA COMO ESTA
				if(!empty($val->validated('commitment_date')))
				{
					$task->commitment_at = $this->date2unixtime($val->validated('commitment_date'));
				}

				# SI SE HA PROPORCIONADO UNA NUEVA FECHA SE ACTULIZA EL CAMPO SI NO QUEDA COMO ESTA
				if(!empty($val->validated('date')))
				{
					$task->finish_at = $this->date2unixtime($val->validated('date'));
				}

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($task->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el pendiente <b>' . Input::post('subject') . '</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/helpdesk/task');
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');

				# SE ALMACENA LOS ERRORES DETECTADOS
				$data['errors'] = $val->error();

				# SE RECORRE CLASE POR CLASE
				foreach ($classes as $name => $class) {
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
		$statustask_opts += array('0' => 'Selecciona una opción');

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
				$statustask_opts[$statu->id] = $statu->name;
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] 				= $task_id;
		$data['classes'] 			= $classes;
		$data['statustask_opts']   	= $statustask_opts;
		$data['employee_opts']      = $employee_opts;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar Tarea pendiente';
		$this->template->content = View::forge('admin/helpdesk/task/editar', $data, false);
	}


	/**
	* FINALIZAR TAREA
	*
	* MUESTRA UNA LISTADO DE REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_finalizar($task_id = 0)
	{
		# SI NO SE RECIBE UN ID DE TASK O NO ES UN NÚMERO VÁLIDO
		if($task_id == 0 || !is_numeric($task_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/task');
		}

		# SE BUSCA EL TASK A TRAVÉS DEL MODELO
		$task = Model_Task::find($task_id);

		# SI NO SE ENCUENTRA EL TICKET
		if(!$task)
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/task');
		}

		# VALIDAR QUE EL TASK TENGA UN USUARIO ASIGNADO
		if(empty($task->employee_id))
		{
			# MOSTRAR MENSAJE DE ERROR
			Session::set_flash('error', 'la tarea debe tener un empleado asignado antes de marcarlo como "Finalizado".');
			Response::redirect('admin/helpdesk/task/editar/'.$task_id);
		}

		# GUARDAR LA HORA DE ACTUALIZACIÓN
		$currentDate        = time();
		$task->updated_at = $currentDate;

		# CAMBIAR EL ESTADO A "FINALIZADO"
		$task->status_id = 4;
		$task->save();

		# SE REDIRECCIONA AL USUARIO (OPCIONAL)
		Session::set_flash('success', 'La tarea se marcó como "Finalizado" correctamente.');
		Response::redirect('admin/helpdesk/task');
	}


	/**
	* CANCELAR TAREA
	*
	* MUESTRA UNA LISTADO DE REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_cancelar($task_id = 0)
	{
		# SI NO SE RECIBE UN ID DE LA TAREA O NO ES UN NÚMERO VÁLIDO
		if($task_id == 0 || !is_numeric($task_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/task');
		}

		# SE BUSCA LA TAREA A TRAVÉS DEL MODELO
		$task = Model_Task::find($task_id);

		# SI NO SE ENCUENTRA LA TAREA
		if(!$task)
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/task');
		}

		# GUARDAR LA HORA DE ACTUALIZACIÓN
		$currentDate        = time();
		$task->updated_at = $currentDate;

		# SE CANCELA LA TAREA
		$task->deleted = 1;
		$task->status_id = 5;
		$task->save();

		# SE REDICIRECIONA AL INDEX SIN LA TAREA CANCELADA
		Session::set_flash('success', 'Se a cancelado la tarea y ya no aparecera en su lista.');
		Response::redirect('admin/helpdesk/task');
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
			Response::redirect('admin/helpdesk/tickets/index');
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
				Response::redirect('admin/helpdesk/tickets/index');
			}
			else
			{
				# SI LA VALIDACIÓN FALLA, MOSTRAR EL FORMULARIO CON LOS MENSAJES DE ERROR
				$this->template->title   = 'Actualizar Ticket';
				$this->template->content = View::forge('admin/helpdesk/tickets/editar');
				$this->template->content->set_safe('val', $val);
			}
		}
		else
		{
			# MOSTRAR EL FORMULARIO DE ACTUALIZACIÓN DE TICKETS
			$this->template->title   = 'Actualizar Ticket';
			$this->template->content = View::forge('admin/helpdesk/tickets/editar');
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
	private function date2unixtime($date = 0, $time = 0)
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

}
