<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_SUBCATEGORIAS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Subcategorias extends Controller_Admin
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
		$subcategories_info = array();
		$per_page        = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$subcategories = Model_Subcategory::query()
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
			$subcategories = $subcategories->where(DB::expr("CONCAT(`t0`.`name`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $subcategories->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('subcategories', $config);

		# SE EJECUTA EL QUERY
		$subcategories = $subcategories->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($subcategories))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($subcategories as $subcategory)
			{
				# SE ALMACENA LA INFORMACION
				$subcategories_info[] = array(
					'id'   => $subcategory->id,
					'name' => $subcategory->name,
					'status' => $subcategory->status
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['categories'] = $subcategories_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Grupo';
		$this->template->content = View::forge('admin/catalogo/subcategorias/index', $data, false);
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
				Response::redirect('admin/catalogo/subcategorias/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/catalogo/subcategorias');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/subcategorias');
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
			$val = Validation::forge('subcategory');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE INICIALIZA EL CONTADOR
				$count = 1;

				# SE GENERA EL SLUG A PARTIR DEL NOMBRE
				$original_slug = Inflector::friendly_title($val->validated('name'), '-', true);
				$slug_temp     = Inflector::friendly_title($val->validated('name'), '-', true);

				# HACER HASTA NO ENCONTRAR EL SLUG REPETIDO
				do{
					# SE VERIFICA SI EXISTE EL SLUG EN LA BASE DE DATOS
					$exist = Model_Subcategory::query()
					->where('slug', $slug_temp)
					->get();

					# SI EL SLUG EXISTE
					if(!empty($exist))
					{
						# SE LE AGREGA EL VALOR DEL CONTADOR AL FINAL DE SLUG
						$slug_temp = $original_slug.'-'.$count;

						# SE INCREMENTA EL CONTADOR
						$count++;
					}
				}while(!empty($exist));
				# FIN DE LA VERIFICACION DEL SLUG

				# SE CREA EL MODELO CON LA INFORMACION
				$subcategory = new Model_Subcategory(array(
					'slug'    => $slug_temp,
					'name'    => $val->validated('name'),
					'deleted' => 0,
					'status' => 0
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($subcategory->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el grupo <b>'.$val->validated('name').'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/subcategorias');
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
		$this->template->content = View::forge('admin/catalogo/subcategorias/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($subcategory_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($subcategory_id == 0 || !is_numeric($subcategory_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/subcategorias');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$subcategory = Model_Subcategory::query()
		->where('id', $subcategory_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($subcategory))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name'] = $subcategory->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/subcategorias');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $subcategory_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del grupo';
		$this->template->content = View::forge('admin/catalogo/subcategorias/info', $data);
	}



	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($subcategory_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($subcategory_id == 0 || !is_numeric($subcategory_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/subcategorias');
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
		$subcategory = Model_Subcategory::query()
		->where('id', $subcategory_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($subcategory))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name'] = $subcategory->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/subcategorias');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('subcategory');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE INICIALIZA EL CONTADOR
				$count = 1;

				# SE GENERA EL SLUG A PARTIR DEL NOMBRE
				$original_slug = Inflector::friendly_title($val->validated('name'), '-', true);
				$slug_temp     = Inflector::friendly_title($val->validated('name'), '-', true);

				# HACER HASTA NO ENCONTRAR EL SLUG REPETIDO
				do{
					# SE VERIFICA SI EXISTE EL SLUG EN LA BASE DE DATOS
					$exist = Model_Subcategory::query()
					->where('slug', $slug_temp)
					->get_one();

					# SI EL SLUG EXISTE
					if(!empty($exist))
					{
						# SI EL ID DEL SLUG ES DIFERENTE AL ID ORIGINAL
						if($exist->id != $subcategory->id)
						{
							# SE LE AGREGA EL VALOR DEL CONTADOR AL FINAL DE SLUG
							$slug_temp = $original_slug.'-'.$count;

							# SE INCREMENTA EL CONTADOR
							$count++;
						}
						else
						{
							# SE ROMPE EL CICLO DEL DO-WHILE
							break;
						}
					}
				}while(!empty($exist));
				# FIN DE LA VERIFICACION DEL SLUG

				# SE ESTEBLECE LA NUEVA INFORMACION
				$subcategory->slug = $slug_temp;
				$subcategory->name = $val->validated('name');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($subcategory->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información del <b>'.$subcategory->name.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/subcategorias/editar/'.$subcategory_id);
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
		$data['id']      = $subcategory_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar grupo';
		$this->template->content = View::forge('admin/catalogo/subcategorias/editar', $data);
	}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($subcategory_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($subcategory_id == 0 || !is_numeric($subcategory_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/subcategorias');
		}

		# SE INICIALIZAN LAS VARIABLES
		$relations_info = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$subcategory = Model_Subcategory::query()
		->where('id', $subcategory_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($subcategory))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$products = Model_Product::query()
			->where('subcategory_id', $subcategory_id)
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
				Session::set_flash('error', 'No se puede eliminar el grupo <b>'.$subcategory->name.'</b> porque tiene productos asignados a ella:<br>'.$relations_info);
			}
			else
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$subcategory->deleted = 1;

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($subcategory->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se eliminó el grupo <b>'.$subcategory->name.'</b> correctamente.');
				}
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/catalogo/subcategorias');
	}
}
