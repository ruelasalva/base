<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_LISTAS_PRECIOS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Listas_Precios extends Controller_Admin
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
		$data            = array();
		$listas_info = array();
		$per_page        = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$listas = Model_Customers_Type::query()
		->where('id', '>',0)
		->order_by('id', 'asc');

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
			$listas = $listas->where(DB::expr("CONCAT(`t0`.`name`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $listas->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('listas', $config);

		# SE EJECUTA EL QUERY
		$listas = $listas->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($listas))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($listas as $lista)
			{
				# SE ALMACENA LA INFORMACION
				$listas_info[] = array(
					'id'   => $lista->id,
					'name' => $lista->name
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['listas'] = $listas_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Grupo';
		$this->template->content = View::forge('admin/listas_precios/index', $data, false);
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
				Response::redirect('admin/listas_precios/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/catalogo/listas_precios');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/listas_precios');
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
		$fields  = array('name');

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
			$val = Validation::forge('lista');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				
				# SE CREA EL MODELO CON LA INFORMACION
				$lista = new Model_Customers_Type(array(
					//'slug'    => $slug_temp,
					'name'    => $val->validated('name'),
					
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($lista->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el grupo <b>'.$val->validated('name').'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/listas_precios');
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
		$this->template->title   = 'Agregar grupo';
		$this->template->content = View::forge('admin/listas_precios/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($lista_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($lista_id == 0 || !is_numeric($lista_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/listas_precios');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$lista = Model_Customers_Type::query()
		->where('id', $lista_id)
		//->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($lista))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name'] = $lista->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/listas_precios');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $lista_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del grupo';
		$this->template->content = View::forge('admin/listas_precios/info', $data);
	}



	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($lista_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($lista_id == 0 || !is_numeric($lista_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/listas_precios');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('name');

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
		$lista = Model_Customers_Type::query()
		->where('id', $lista_id)
		//->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($lista))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name'] = $lista->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/listas_precios');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('lista');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				
				$lista->name = $val->validated('name');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($lista->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información del <b>'.$lista->name.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/listas_precios/editar/'.$lista_id);
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
		$data['id']      = $lista_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar grupo';
		$this->template->content = View::forge('admin/listas_precios/editar', $data);
	}


	
}
