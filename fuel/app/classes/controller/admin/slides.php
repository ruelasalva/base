<?php

/**
 * CONTROLADOR ADMIN_SLIDES
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Slides extends Controller_Admin
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
	public function action_index()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data        = array();
		$slides_info = array();
		$per_page    = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$slides = Model_Slide::query();

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $slides->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('slides', $config);

		# SE EJECUTA EL QUERY
		$slides = $slides->order_by('order', 'asc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($slides))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($slides as $slide)
			{
				# SE ALMACENA LA INFORMACION
				$slides_info[] = array(
					'id'    	=> $slide->id,
					'image' 	=> $slide->image,
					'url'   	=> $slide->url,
					'status'   	=> $slide->status,
					'order' 	=> $slide->order
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['slides']     = $slides_info;
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Slides';
		$this->template->content = View::forge('admin/slides/index', $data, false);
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
		$fields  = array('image', 'url');

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
			$val = Validation::forge('slide');
			$val->add_callable('Rules');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('url', 'url', 'required|valid_url');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE OBTIENE EL ORDEN MAXIMO
				$order = Model_Slide::query()
				->max('order');

				# SE CREA EL MODELO CON LA INFORMACION
				$slide = new Model_Slide(array(
					'image' => $val->validated('image'),
					'url'   => $val->validated('url'),
					'order' => $order + 1,
					'status' => 1
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($slide->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el slide correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/slides');
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
		$this->template->title   = 'Agregar slide';
		$this->template->content = View::forge('admin/slides/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($slide_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($slide_id == 0 || !is_numeric($slide_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/slides');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data       = array();
		$users_info = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$slide = Model_Slide::query()
		->where('id', $slide_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($slide))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['image'] = $slide->image;
			$data['url']   = $slide->url;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/slides');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $slide_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del slide';
		$this->template->content = View::forge('admin/slides/info', $data);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($slide_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($slide_id == 0 || !is_numeric($slide_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/slides');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('image', 'url');

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
		$slide = Model_Slide::query()
		->where('id', $slide_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($slide))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['image'] = $slide->image;
			$data['url']   = $slide->url;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/slides');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('slide');
			$val->add_callable('Rules');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('url', 'url', 'required|valid_url');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$slide->image = $val->validated('image');
				$slide->url   = $val->validated('url');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($slide->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información del slide correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/slides/editar/'.$slide_id);
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
		$data['id']      = $slide_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar slide';
		$this->template->content = View::forge('admin/slides/editar', $data);
	}


	/**
	 * ELIMINAR
	 *
	 * ELIMINA UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($slide_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($slide_id == 0 || !is_numeric($slide_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/slides');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$slide = Model_Slide::query()
		->where('id', $slide_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($slide))
		{
			# SI EL ARCHIVO EXISTE
			if(file_exists(DOCROOT.'assets/uploads/'.$slide->image))
			{
				# SE ELIMINA EL ARCHIVO
				File::delete(DOCROOT.'assets/uploads/'.$slide->image);
			}

			# SI SE ELIMINO EL REGISTRO EN LA BASE DE DATOS
			if($slide->delete())
			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó el slide correctamente.');
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/slides');
	}
}
