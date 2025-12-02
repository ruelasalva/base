<?php

class Controller_Admin_Helpdesk_Incident extends Controller_Admin
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
		$data        = array();
		$incidents_info = array();
		$per_page    = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$incidents = Model_Tickets_Incident::query()
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
			$incidents = $incidents->where(DB::expr("CONCAT(`t0`.`name`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $incidents->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('incidents', $config);

		# SE EJECUTA EL QUERY
		$incidents = $incidents->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SE RECORRE ELEMENTO POR ELEMENTO
		foreach($incidents as $incident)
		{
			# SI SE OBTIENE INFORMACION
			if(!empty($incidents))
			{
				# SE ALMACENA LA INFORMACION
				$incidents_info[] = array(
					'id'            => $incident->id,
					'type_id'       => $incident->typeticket->name,
					'name'          => $incident->name
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['incidents']  = $incidents_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Incidencias';
		$this->template->content = View::forge('admin/helpdesk/incidencias/index', $data, false);
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
				Response::redirect('admin/helpdesk/incident/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/helpdesk/incident');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/incident');
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
	public function action_info($incident_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($incident_id == 0 || !is_numeric($incident_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/incident');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$incident = Model_Tickets_Incident::query()
		->where('id', $incident_id)
		//->where('id','>=', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($incident))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['id']      = $incident->id;
			$data['type_id'] = $incident->typeticket->name;
			$data['name']    = $incident->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/incident');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $incident_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información';
		$this->template->content = View::forge('admin/helpdesk/incidencias/info', $data);
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
		$data            = array();
		$classes         = array();
		$fields          = array('id', 'type_id', 'name');
		$typeticket_opts = array();

		# SE RECORRE CAMPO POR CAMPO
		foreach ($fields as $field) {
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
			$val->add_field('name', 'Nombre', 'required');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE CREA EL MODELO CON LA INFORMACION
				$ticket = new Model_Tickets_Incident(array(
					'type_id' => $val->validated('type_id'),
					'name'    => $val->validated('name')
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($ticket->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó la incidencia <b>' . Input::post('name') . '</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/helpdesk/incident');
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

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['classes']         = $classes;
		$data['typeticket_opts'] = $typeticket_opts;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar Incidencia';
		$this->template->content = View::forge('admin/helpdesk/incidencias/agregar', $data);
	}

	/**
	* EDITAR
	*
	* PERMITE EDITAR UN REGISTRO A LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_editar($incident_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($incident_id == 0 || !is_numeric($incident_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/incident');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data             = array();
		$classes          = array();
		$fields           = array('type_id', 'id', 'name');
		$typetickets_opts = array();

		# SE RECORRE CAMPO POR CAMPO
		foreach($fields as $field)
		{
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array(
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$incident = Model_Tickets_Incident::query()
		->where('id', $incident_id)
		//->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($incident))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['id'] = $incident->id;
			$data['type_id'] = $incident->typeticket->name;
			//$data['type_id'] = $incident->typeticket;
			$data['name'] = $incident->name;

		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/incident');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('incident');
			$val->add_callable('Rules');
			$val->add_field('type_id', 'Tipo', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('id', 'ID', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('name', 'Nombre', 'required');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$incident->type_id = $val->validated('type_id');
				$incident->id = $val->validated('id');
				$incident->name = $val->validated('name');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($incident->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de <b>' . $incident->id . '</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/helpdesk/incident/editar/' . $incident_id);
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

		# SE ESTABLECE LA OPCIÓN POR DEFECTO
		$default_type_name = $incident->typeticket->name;

		# SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
		$types = Model_Tickets_Type::query()
		->get();

		$typetickets_opts = array('' => $default_type_name);
		# SI SE OBTIENE INFORMACIÓN
		if(!empty($types))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($types as $type)
			{
				# SE ALMACENA LA OPCIÓN
				$typetickets_opts[$type->id] = $type->name;
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['classes'] = $classes;
		$data['typetickets_opts'] = $typetickets_opts;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar incidente';
		$this->template->content = View::forge('admin/helpdesk/incidencias/editar', $data);
	}
}
