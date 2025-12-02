<?php

/**
 * CONTROLADOR ADMIN_BANNERS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Banners extends Controller_Admin
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
		$banners_info = array();
		$per_page    = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$banners = Model_Banner::query();

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $banners->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('banners', $config);

		# SE EJECUTA EL QUERY
		$banners = $banners->order_by('order', 'asc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($banners))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($banners as $banner)
			{
				# SE ALMACENA LA INFORMACION
				$banners_info[] = array(
					'id'    => $banner->id,
					'image' => $banner->image,
					'url'   => $banner->url,
					'order' => $banner->order,
					'status' => $banner->status
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['banners']    = $banners_info;
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Banners';
		$this->template->content = View::forge('admin/banners/index', $data, false);
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
			$val = Validation::forge('banner');
			$val->add_callable('Rules');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('url', 'url', 'required|valid_url');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE OBTIENE EL ORDEN MAXIMO
				$order = Model_Banner::query()
				->max('order');

				# SE CREA EL MODELO CON LA INFORMACION
				$banner = new Model_Banner(array(
					'image' => $val->validated('image'),
					'url'   => $val->validated('url'),
					'order' => $order + 1,
					'status' => 1
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($banner->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el banner correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/banners');
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
		$this->template->title   = 'Agregar banner';
		$this->template->content = View::forge('admin/banners/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($banner_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($banner_id == 0 || !is_numeric($banner_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/banners');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data       = array();
		$users_info = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$banner = Model_Banner::query()
		->where('id', $banner_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($banner))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['image'] = $banner->image;
			$data['url']   = $banner->url;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/banners');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $banner_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del banner';
		$this->template->content = View::forge('admin/banners/info', $data);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($banner_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($banner_id == 0 || !is_numeric($banner_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/banners');
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
		$banner = Model_Banner::query()
		->where('id', $banner_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($banner))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['image'] = $banner->image;
			$data['url']   = $banner->url;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/banners');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('banner');
			$val->add_callable('Rules');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('url', 'url', 'required|valid_url');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$banner->image = $val->validated('image');
				$banner->url   = $val->validated('url');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($banner->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información del banner correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/banners/editar/'.$banner_id);
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
		$data['id']      = $banner_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar banner';
		$this->template->content = View::forge('admin/banners/editar', $data);
	}


	/**
	 * ELIMINAR
	 *
	 * ELIMINA UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($banner_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($banner_id == 0 || !is_numeric($banner_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/banners');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$banner = Model_Banner::query()
		->where('id', $banner_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($banner))
		{
			# SI EL ARCHIVO EXISTE
			if(file_exists(DOCROOT.'assets/uploads/'.$banner->image))
			{
				# SE ELIMINA EL ARCHIVO
				File::delete(DOCROOT.'assets/uploads/'.$banner->image);
			}

			# SI SE ELIMINO EL REGISTRO EN LA BASE DE DATOS
			if($banner->delete())
			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó el banner correctamente.');
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/banners');
	}
}
