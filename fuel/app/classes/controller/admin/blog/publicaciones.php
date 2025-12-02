<?php

/**
 * CONTROLADOR ADMIN_BLOG_PUBLICACIONES
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Blog_Publicaciones extends Controller_Admin
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
		$data       = array();
		$posts_info = array();
		$per_page   = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$posts = Model_Post::query()
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
			$posts = $posts->where(DB::expr("CONCAT(`t0`.`title`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $posts->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('posts', $config);

		# SE EJECUTA EL QUERY
		$posts = $posts->order_by('publication_date', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($posts))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($posts as $post)
			{
				# SE ALMACENA LA INFORMACION
				$posts_info[] = array(
					'id'             => $post->id,
					'title'          => Str::truncate($post->title, '60', '...'),
					'title_complete' => $post->title,
					'category'       => $post->category->name,
					'date'           => date('d/m/Y - H:i', $post->publication_date),
					'status'         => $post->status
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['posts']      = $posts_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Publicaciones';
		$this->template->content = View::forge('admin/blog/publicaciones/index', $data, false);
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
				Response::redirect('admin/blog/publicaciones/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/blog/publicaciones');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/blog/publicaciones');
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
		$data          = array();
		$classes       = array();
		$fields        = array('title', 'category', 'image', 'intro', 'content', 'labels', 'date', 'time');
		$category_opts = array();
		$labels_opts   = array();
		$date          = date('d/m/Y');
		$time          = date('H:i');

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
			$val = Validation::forge('post');
			$val->add_callable('Rules');
			$val->add_field('title', 'título', 'required|min_length[1]|max_length[255]');
			$val->add_field('category', 'categoría', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('intro', 'introducción', 'required|min_length[1]');
			$val->add_field('content', 'contenido', 'required|min_length[1]');
			$val->add_field('labels', 'etiquetas', 'required');
			$val->add_field('date', 'fecha de publicación', 'required|date');
			$val->add_field('time', 'hora de publicación', 'required|min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE INICIALIZA EL CONTADOR
				$count = 1;

				# SE GENERA EL SLUG A PARTIR DEL NOMBRE
				$original_slug = Inflector::friendly_title($val->validated('title'), '-', true);
				$slug_temp     = Inflector::friendly_title($val->validated('title'), '-', true);

				# HACER HASTA NO ENCONTRAR EL SLUG REPETIDO
				do{
					# SE VERIFICA SI EXISTE EL SLUG EN LA BASE DE DATOS
					$exist = Model_Post::query()
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
				$post = new Model_Post(array(
					'category_id'      => $val->validated('category'),
					'slug'             => $slug_temp,
					'title'            => $val->validated('title'),
					'image'            => $val->validated('image'),
					'intro'            => $val->validated('intro'),
					'content'          => $val->validated('content'),
					'publication_date' => $this->date2unixtime($val->validated('date'), $val->validated('time')),
					'status'           => 1,
					'deleted'          => 0
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($post->save())
				{
					# SE RECORRE ELEMENTO POR ELEMENTO
					foreach(Input::post('labels') as $relation)
					{
						# SE CREA EL MODELO CON LA INFORMACION
						$labels = new Model_Posts_Labels_Relation(array(
							'post_id'  => $post->id,
							'label_id' => $relation,
						));

						# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
						$labels->save();
					}

					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó la publicación <b>'.$val->validated('title').'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/blog/publicaciones');
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

		# SE ESTBLECE LA OPCION POR DEFAULT
		$category_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$categories = Model_Posts_Category::query()
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

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$labels = Model_Posts_Label::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($labels))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($labels as $label)
			{
				# SE ALMACENA LA OPCION
				$labels_opts += array($label->id => $label->name);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['classes']       = $classes;
		$data['category_opts'] = $category_opts;
		$data['labels_opts']   = $labels_opts;
		$data['date']          = $date;
		$data['time']          = $time;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar publicación';
		$this->template->content = View::forge('admin/blog/publicaciones/agregar', $data, false);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($post_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($post_id == 0 || !is_numeric($post_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/blog/publicaciones');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data   = array();
		$labels = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$post = Model_Post::query()
		->related('labels')
		->where('id', $post_id)
		->where('deleted', 0)
		->order_by('labels.name', 'asc')
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($post))
		{
			# SI EXISTE UNA RELACION
			if(!empty($post->labels))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($post->labels as $relation)
				{
					# SE ALMACENA LA INFORMACION
					$labels .= ', '.$relation->name;
				}

				# SE ELIMINAN LOS PRIMEROS 2 CARACTERES
				$labels = substr($labels, 2);
			}

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['title']    = $post->title;
			$data['category'] = $post->category->name;
			$data['image']    = $post->image;
			$data['intro']    = $post->intro;
			$data['content']  = $post->content;
			$data['labels']   = $labels;
			$data['date']     = date('d/m/Y', $post->publication_date);
			$data['time']     = date('H:i', $post->publication_date);
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/blog/publicaciones');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $post_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información de la publicación';
		$this->template->content = View::forge('admin/blog/publicaciones/info', $data, false);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($post_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($post_id == 0 || !is_numeric($post_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/blog/publicaciones');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data          = array();
		$classes       = array();
		$fields        = array('title', 'category', 'image', 'intro', 'content', 'labels', 'date', 'time');
		$category_opts = array();
		$labels        = array();
		$labels_opts   = array();
		$new_relations = array();
		$date          = date('d/m/Y');
		$time          = date('H:i');

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
		$post = Model_Post::query()
		->where('id', $post_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($post))
		{
			# SI EXISTE UNA RELACION
			if(!empty($post->labels))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($post->labels as $relation)
				{
	                # SE ALMACENA LA INFORMACION
					$labels[] = $relation['id'];
				}
			}

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['title']    = $post->title;
			$data['category'] = $post->category_id;
			$data['image']    = $post->image;
			$data['intro']    = $post->intro;
			$data['content']  = $post->content;
			$data['labels']   = $labels;
			$data['date']     = date('d/m/Y', $post->publication_date);
			$data['time']     = date('H:i', $post->publication_date);
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/blog/publicaciones');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('post');
			$val->add_callable('Rules');
			$val->add_field('title', 'título', 'required|min_length[1]|max_length[255]');
			$val->add_field('category', 'categoría', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('intro', 'introducción', 'required|min_length[1]');
			$val->add_field('content', 'contenido', 'required|min_length[1]');
			$val->add_field('labels', 'etiquetas', 'required');
			$val->add_field('date', 'fecha de publicación', 'required|date');
			$val->add_field('time', 'hora de publicación', 'required|min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE INICIALIZA EL CONTADOR
				$count = 1;

				# SE GENERA EL SLUG A PARTIR DEL NOMBRE
				$original_slug = Inflector::friendly_title($val->validated('title'), '-', true);
				$slug_temp     = Inflector::friendly_title($val->validated('title'), '-', true);

				# HACER HASTA NO ENCONTRAR EL SLUG REPETIDO
				do{
					# SE VERIFICA SI EXISTE EL SLUG EN LA BASE DE DATOS
					$exist = Model_Post::query()
					->where('id', '!=', $post_id)
					->where('slug', $slug_temp)
					->get_one();

					# SI EL SLUG EXISTE
					if(!empty($exist))
					{
						# SI EL ID DEL SLUG ES DIFERENTE AL ID ORIGINAL
						if($exist->id != $post->id)
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
				$post->category_id      = $val->validated('category');
				$post->slug             = $slug_temp;
				$post->title            = $val->validated('title');
				$post->image            = $val->validated('image');
				$post->intro            = $val->validated('intro');
				$post->content          = $val->validated('content');
				$post->publication_date = $this->date2unixtime($val->validated('date'), $val->validated('time'));

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($post->save())
				{
					# SI EXISTEN RELACIONES
					if(!empty(Input::post('labels')))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$original_relations = Model_Posts_Labels_Relation::query()
						->where('post_id', $post_id)
						->get();

						# SI SE OBTIENE INFORMACION
						if(!empty($original_relations))
						{
							# SE CONVIERTE EL OBJETO A UN ARREGLO UNICAMENTE CON LOS IDS
							$relations_id = \Arr::pluck($original_relations, 'label_id');

							# SE OBTIENE LA DIFERENCIA DE LOS ORIGINALES CON LAS DEL POST
							$differences = array_diff($relations_id, Input::post('labels'));

							# SE OBTIENE LA DIFERENCIA DE LAS DEL POST CON LAS ORIGINALES
							$new_relations = array_diff(Input::post('labels'), $relations_id);

							# SI EXISTEN DIFERENCIAS
							if(!empty($differences))
							{
								# SE RECORRE ELEMENTO POR ELEMENTO
								foreach($differences as $difference)
								{
									# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
									$erased = Model_Posts_Labels_Relation::query()
									->where('post_id', $post_id)
									->where('label_id', $difference)
									->get_one();

									# SI SE OBTIENE INFORMACION
									if(!empty($erased))
									{
										# SE ELIMINA EL REGISTRO EN LA BASE DE DATOS
										$erased->delete();
									}
								}
							}
						}
					}

					# SI HAY NUEVOS REGISTROS
					if(!empty($new_relations))
					{
						# SE RECORRE ELEMENTO POR ELEMENTO
						foreach($new_relations as $relation)
						{
							# SE CREA EL MODELO CON LA INFORMACION
							$posts = new Model_Posts_Labels_Relation(array(
								'post_id'  => $post_id,
								'label_id' => $relation,
							));

							# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
							$posts->save();
						}
					}

					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de la publicación <b>'.$post->title.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/blog/publicaciones/editar/'.$post_id);
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

		# SE ESTBLECE LA OPCION POR DEFAULT
		$category_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$categories = Model_Posts_Category::query()
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

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$labels = Model_Posts_Label::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($labels))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($labels as $label)
			{
				# SE ALMACENA LA OPCION
				$labels_opts += array($label->id => $label->name);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']            = $post_id;
		$data['classes']       = $classes;
		$data['category_opts'] = $category_opts;
		$data['labels_opts']   = $labels_opts;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar publicación';
		$this->template->content = View::forge('admin/blog/publicaciones/editar', $data, false);
	}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($post_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($post_id == 0 || !is_numeric($post_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/blog/publicaciones');
		}

		# SE INICIALIZAN LAS VARIABLES
		$relations_info = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$post = Model_Post::query()
		->where('id', $post_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($post))
		{
			# SE ESTEBLECE LA NUEVA INFORMACION
			$post->deleted = 1;

			# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
			if($post->save())
			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó el publicación <b>'.$post->title.'</b> correctamente.');
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/blog/publicaciones');
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
