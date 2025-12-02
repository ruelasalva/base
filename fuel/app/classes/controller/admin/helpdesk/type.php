<?php

class Controller_Admin_Helpdesk_Type extends Controller_Admin
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
		$types_info = array();
		$per_page    = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$types = Model_Tickets_Type::query()
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
			$types = $types->where(DB::expr("CONCAT(`t0`.`name`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $types->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('types', $config);

		# SE EJECUTA EL QUERY
		$types = $types->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SE RECORRE ELEMENTO POR ELEMENTO
		foreach($types as $type)
		{
			# SI SE OBTIENE INFORMACION
			if(!empty($types))
			{
				# SE ALMACENA LA INFORMACION
				$types_info[] = array(
					'id'   => $type->id,
					'name' => $type->name
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['types']      = $types_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Tipos';
		$this->template->content = View::forge('admin/helpdesk/tipos/index', $data, false);
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
				Response::redirect('admin/helpdesk/type/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/helpdesk/type');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/type');
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
	public function action_info($type_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($type_id == 0 || !is_numeric($type_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/type');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$type = Model_Tickets_Type::query()
		->where('id', $type_id)
		//->where('id','>=', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($type))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['id']   = $type->id;
			$data['name'] = $type->name;

		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/type');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $type_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información';
		$this->template->content = View::forge('admin/helpdesk/tipos/info', $data);
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
		$fields  = array('id', 'name');

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
			$val->add_field('name', 'Nombre', 'required');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE CREA EL MODELO CON LA INFORMACION
				$ticket = new Model_Tickets_Type(array(
					'name' => $val->validated('name')
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($ticket->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el tipo <b>' . Input::post('name') . '</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/helpdesk/type');
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
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar Incidencia';
		$this->template->content = View::forge('admin/helpdesk/tipos/agregar', $data);
	}


	/**
	* EDITAR
	*
	* PERMITE EDITAR UN REGISTRO A LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_editar($type_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($type_id == 0 || !is_numeric($type_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/type');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('id','name');

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
		$type = Model_Tickets_Type::query()
		->where('id', $type_id)
		//->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($type))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['id'] = $type->id;
			$data['name'] = $type->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/helpdesk/type');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('type');
			$val->add_callable('Rules');
			$val->add_field('id', 'Id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('name', 'Nombre', 'required');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				#FECHA DE CIERRE
				$currentDate = time();

				# SE ESTEBLECE LA NUEVA INFORMACION
				$type->id = $val->validated('id');
				$type->name = $val->validated('name');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($type->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de <b>'.$type->id.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/helpdesk/type/editar/'.$type_id);
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
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar tipo';
		$this->template->content = View::forge('admin/helpdesk/tipos/editar', $data);
	}
}
