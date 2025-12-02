<?php

/**
 * CONTROLADOR ADMIN_EMPLEADOS_DEPARTAMENTO
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Empleados_Departamento extends Controller_Admin
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
		$departments_info = array();
		$per_page        = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$departments = Model_Employees_Department::query()
		->where('deleted', 0);

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
			$departments = $departments->where(DB::expr("CONCAT(`t0`.`name`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $departments->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('departments', $config);

		# SE EJECUTA EL QUERY
		$departments = $departments->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($departments))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($departments as $department)
			{
				# SE ALMACENA LA INFORMACION
				$departments_info[] = array(
					'id'   => $department->id,
					'name' => $department->name
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['departments'] = $departments_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Departamentos';
		$this->template->content = View::forge('admin/empleados/departamento/index', $data, false);
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
				Response::redirect('admin/empleados/departamento/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/empleados/departamento');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/empleados/departamento');
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
			$val = Validation::forge('department');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{

				# SE CREA EL MODELO CON LA INFORMACION
				$department = new Model_Employees_Department(array(
					
					'name'    => $val->validated('name'),
					'deleted' => 0
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($department->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó la categoría <b>'.$val->validated('name').'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/empleados/departamento');
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
		$this->template->title   = 'Agregar departamento';
		$this->template->content = View::forge('admin/empleados/departamento/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($department_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($department_id == 0 || !is_numeric($department_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/empleados/departamento');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$department = Model_Employees_Department::query()
		->where('id', $department_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($department))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name'] = $department->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/empleados/departamento');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $department_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del departamento';
		$this->template->content = View::forge('admin/empleados/departamento/info', $data);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($department_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($department_id == 0 || !is_numeric($department_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/empleados/departamento');
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
		$department = Model_Employees_Department::query()
		->where('id', $department_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($department))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name'] = $department->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/empleados/departamento');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('department');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				

			

				# SE ESTEBLECE LA NUEVA INFORMACION
				$department->slug = $slug_temp;
				$department->name = $val->validated('name');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($department->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de <b>'.$department->name.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/empleados/departamento/editar/'.$department_id);
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
		$data['id']      = $department_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar departamento';
		$this->template->content = View::forge('admin/empleados/departamento/editar', $data);
	}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($department_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($department_id == 0 || !is_numeric($department_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/empleados/departamento');
		}

		# SE INICIALIZAN LAS VARIABLES
		$relations_info = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$department = Model_Employees_Department::query()
		->where('id', $department_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($department))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$products = Model_Product::query()
			->where('department_id', $department_id)
			->where('deleted', 0)
			->get();

			# SI SE OBTIENE INFORMACION
			if(!empty($products))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($products as $product)
				{
					# SE ALMACENA LA INFORMAICON DE LA RELACION
					$relations_info .= Html::anchor('admin/catalogo/productos/editar/'.$product->id, $product->name, array('target' => '_blank')).' - ';
				}

				# SE ELIMINAN LOS CARAQCTERES SOBRANTES Y SE AGREGA EL PUNTO
				$relations_info = substr($relations_info, 0, -3);

				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'No se puede eliminar la categoría <b>'.$department->name.'</b> porque tiene productos asignados a ella:<br>'.$relations_info);
			}
			else
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$department->deleted = 1;

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($department->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se eliminó la categoría <b>'.$department->name.'</b> correctamente.');
				}
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/empleados/departamento');
	}
}
