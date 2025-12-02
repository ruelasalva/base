<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_TAGS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Tags extends Controller_Admin
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
		$tags_info = array();
		$per_page    = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$tags = Model_Tag::query()
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
			$tags = $tags->where(DB::expr("CONCAT(`t0`.`name`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $tags->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('tags', $config);

		# SE EJECUTA EL QUERY
		$tags = $tags->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($tags))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($tags as $tag)
			{
				# SE ALMACENA LA INFORMACION
				$tags_info[] = array(
					'id'   => $tag->id,
					'name' => $tag->name
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['tags']     = $tags_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Etiquetas'; //este es el titulo de la pagina
		$this->template->content = View::forge('admin/catalogo/tags/index', $data, false);
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
				Response::redirect('admin/catalogo/tags/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/catalogo/tags');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/tags');
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
		$fields  = array('name', 'image');

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
			$val = Validation::forge('tag');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('image', 'imagen', 'required|min_length[1]');

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
					$exist = Model_Tag::query()
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
				$tag = new Model_Tag(array(
					'slug'    => $slug_temp,
					'name'    => $val->validated('name'),
					'image'   => $val->validated('image'),
					'deleted' => 0
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($tag->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó la etiqueta <b>'.$val->validated('name').'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/tags');
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
		$this->template->title   = 'Agregar marca';
		$this->template->content = View::forge('admin/catalogo/tags/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($tag_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($tag_id == 0 || !is_numeric($tag_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/tags');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$tag = Model_Tag::query()
		->where('id', $tag_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($tag))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name']  = $tag->name;
			$data['image'] = $tag->image;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/tags');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $tag_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información de la marca';
		$this->template->content = View::forge('admin/catalogo/tags/info', $data);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($tag_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($tag_id == 0 || !is_numeric($tag_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/tags');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('name', 'image');

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
		$tag = Model_Tag::query()
		->where('id', $tag_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($tag))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name']  = $tag->name;
			$data['image'] = $tag->image;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/tags');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('tag');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('image', 'imagen', 'required|min_length[1]');

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
					$exist = Model_tag::query()
					->where('slug', $slug_temp)
					->get_one();

					# SI EL SLUG EXISTE
					if(!empty($exist))
					{
						# SI EL ID DEL SLUG ES DIFERENTE AL ID ORIGINAL
						if($exist->id != $tag->id)
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
				$tag->slug  = $slug_temp;
				$tag->name  = $val->validated('name');
				$tag->image = $val->validated('image');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($tag->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de <b>'.$tag->name.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/tags/editar/'.$tag_id);
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
		$data['id']      = $tag_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar marca';
		$this->template->content = View::forge('admin/catalogo/tags/editar', $data);
	}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($tag_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($tag_id == 0 || !is_numeric($tag_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/tags');
		}

		# SE INICIALIZAN LAS VARIABLES
		$relations_info = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$tag = Model_tag::query()
		->where('id', $tag_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($tag))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$products = Model_Product::query()
			->where('tag_id', $tag_id)
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
				Session::set_flash('error', 'No se puede eliminar la etiqueta <b>'.$tag->name.'</b> porque tiene productos asignados a ella:<br>'.$relations_info);
			}
			else
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$tag->deleted = 1;

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($tag->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se eliminó la etiqueta <b>'.$tag->name.'</b> correctamente.');
				}
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/catalogo/tags');
	}
}
