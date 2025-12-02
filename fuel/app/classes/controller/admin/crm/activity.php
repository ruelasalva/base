<?php

class Controller_Admin_Crm_Activity extends Controller_Admin
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
		# Inicializar variables
		$data            = [];
		$activities_info = [];
		$per_page        = 100;

		# Crear la consulta con expresiones crudas
		$activities = Model_Activity::query()
		->select([
			'act_num',
			[DB::expr('COUNT(*)'), 'total_activities'], // Total de actividades
			[DB::expr('SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END)'), 'total_iniciacion'], // Total de iniciaciones
			[DB::expr('SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END)'), 'total_seguimiento'], // Total de seguimientos
			[DB::expr('SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END)'), 'total_ventas'], // Total de ventas
			[DB::expr('SUM(CASE WHEN invoice = 1 THEN 1 ELSE 0 END)'), 'total_facturados'], // Total facturados
			[DB::expr('SUM(total)'), 'total_monto'], // Total del monto
			[DB::expr('MIN(global_date)'), 'first_global_date'], // Primera fecha en cada grupo
		])
		->related('employee') // Relación con el empleado/agente
		->group_by('act_num') // Agrupar por act_num
		->order_by(DB::expr('MIN(global_date)'), 'desc'); // Ordenar por la primera fecha del grupo

		# Filtro para el usuario actual
		$user_id = Auth::get_user_id()[1];
		$activities->where('user_id', $user_id);

		# Buscar el empleado correspondiente al usuario
		$employee = Model_Employee::query()
		->where('user_id', $user_id)
		->get_one();

		if($employee)
		{
			$activities->where('employee_id', $employee->id);
		}
		else
		{
			# No mostrar actividades si no se encuentra un empleado
			$activities->where('employee_id', -1);
		}

		# Filtro de búsqueda
		if($search != '')
		{
			$original_search = $search;
			$search = str_replace('+', ' ', rawurldecode($search));
			$search = str_replace(' ', '%', $search);

			# Aplicar filtro de búsqueda por descripción
			$activities->where(DB::expr("CONCAT(`t0`.`description`)"), 'like', '%' . $search . '%');
		}

		# Configuración de paginación
		$config = [
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => count($activities->get_query()->execute()), // Cantidad total de registros
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
			'show_last'      => true,
		];

		# Crear la instancia de paginación
		$pagination = Pagination::forge('activities', $config);

		# Obtener los resultados paginados
		$activities = $activities
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# Procesar los resultados
		foreach($activities as $activity)
		{
			$activities_info[] = [
				'act_num'           => $activity->act_num,
				'agent'             => $activity->employee->name. ' '.$activity->employee->last_name ?? 'Desconocido',
				'total_activities'  => $activity->total_activities,
				'global_date'       => date('d/m/Y', $activity->first_global_date), // Usar global_date como clave
				'total_iniciacion'  => $activity->total_iniciacion,
				'total_seguimiento' => $activity->total_seguimiento,
				'total_ventas'      => $activity->total_ventas,
				'total_facturados'  => $activity->total_facturados,
				'total_monto'       => number_format($activity->total_monto, 2),
			];
		}

		# Preparar datos para la vista
		$data['activities'] = $activities_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# Cargar la vista
		$this->template->title   = 'Actividades Diarias';
		$this->template->content = View::forge('admin/crm/activity/index', $data, false);
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
		$category_opts = array();
		$contact_opts  = array();
		$time_opts     = array();
		$type_opts     = array();
		$status_opts   = array();
		$act_num       = Auth::get('id').'-'.time();

		# SE ESTBLECE LA OPCION POR DEFAULT
		$contact_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$contacts = Model_Activitys_Methods_Contact::query()
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($contacts))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($contacts as $contact)
			{
				# SE ALMACENA LA OPCION
				$contact_opts += array($contact->id => $contact->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$time_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$times = Model_Activitys_Time::query()
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($times))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($times as $time)
			{
				# SE ALMACENA LA OPCION
				$time_opts += array($time->id => $time->name .' Minutos');
			}
		}


		# SE ESTBLECE LA OPCION POR DEFAULT
		$type_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$types = Model_Activitys_Type::query()
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($types))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($types as $type)
			{
				# SE ALMACENA LA OPCION
				$type_opts += array($type->id => $type->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$status_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$statuss = Model_Activitys_Status::query()
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($statuss))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($statuss as $status)
			{
				# SE ALMACENA LA OPCION
				$status_opts += array($status->id => $status->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$category_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$categories = Model_Category::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($categories))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($categories as $category)
			{
				# SE ALMACENA LA OPCION
				$category_opts += array($category->id => $category->name);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['act_num']       = $act_num;
		$data['category_opts'] = $category_opts;
		$data['contact_opts']  = $contact_opts;
		$data['time_opts']     = $time_opts;
		$data['type_opts']     = $type_opts;
		$data['status_opts']   = $status_opts;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar actividad diaria';
		$this->template->content = View::forge('admin/crm/activity/agregar',$data);
	}

	/**
	* EDITAR
	*
	* PERMITE EDITAR UN REGISTRO A LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_editar($act_num = null)
	{
		# SE INICIALIZAN LAS VARIABLES
		$data            = array();
		$activities_info = array();
		$category_opts   = array();
		$contact_opts    = array();
		$time_opts       = array();
		$type_opts       = array();
		$status_opts     = array();

		# SI NO EXISTE EL ACT_NUM
		if(!$act_num)
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No se proporcionó un identificador válido.');

			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/activity');
		}

		# SE BUSCA INFORMACION A TRAVES DEL MODELO
		$activity_num = Model_Activitys_Num::query()
		->where('act_num', $act_num)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($activity_num))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['completed']   = $activity_num->completed;
			$data['global_date'] = date('d/m/Y', $activity_num->date);

			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$activities = Model_Activity::query()
			->where('act_num', $act_num)
			->order_by('created_at', 'asc')
			->get();

			# SI SE OBTIENE INFORMACION
			if(!empty($activities))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($activities as $activity)
				{
					# SE ALMACENA LA INFORMACION
					$activities_info[] = array(
						'id'          => $activity->id,
						'customer'    => $activity->customer,
						'company'     => $activity->company,
						'user_id'     => $activity->user_id,
						'employee'    => $activity->employee_id,
						'hour'        => $activity->hour,
						'invoice'      => ($activity->invoice == 0) ? 'No' : 'Sí',
						'foreing'     => ($activity->foreing == 0) ? 'No' : 'Sí',
						'time'        => $activity->time->name,
						'contact'     => $activity->contact->name,
						'category'    => $activity->category->name,
						'status'      => $activity->status->name,
						'type'        => $activity->type->name,
						'total'       => $activity->total,
						'comments'    => $activity->comments,
						'global_date' => $activity->global_date
					);
				}
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No se encontraron actividades para este identificador.');

			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/activity');
		}

		# CONFIGURAR OPCIONES PARA SELECTS
		$contact_opts  = ['0' => 'Selecciona una opción'] + Arr::assoc_to_keyval(Model_Activitys_Methods_Contact::find('all'), 'id', 'name');
		$time_opts     = ['0' => 'Selecciona una opción'] + Arr::assoc_to_keyval(Model_Activitys_Time::find('all'), 'id', 'name');
		$type_opts     = ['0' => 'Selecciona una opción'] + Arr::assoc_to_keyval(Model_Activitys_Type::find('all'), 'id', 'name');
		$status_opts   = ['0' => 'Selecciona una opción'] + Arr::assoc_to_keyval(Model_Activitys_Status::find('all'), 'id', 'name');
		$category_opts = ['0' => 'Selecciona una opción'] + Arr::assoc_to_keyval(
			Model_Category::query()->where('deleted', 0)->get(),
			'id',
			'name'
		);

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['act_num']       = $act_num;
		$data['activities']    = $activities_info;
		$data['category_opts'] = $category_opts;
		$data['contact_opts']  = $contact_opts;
		$data['time_opts']     = $time_opts;
		$data['type_opts']     = $type_opts;
		$data['status_opts']   = $status_opts;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar Actividades';
		$this->template->content = View::forge('admin/crm/activity/editar', $data);
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

}
