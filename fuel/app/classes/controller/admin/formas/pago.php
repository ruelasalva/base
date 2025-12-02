<?php

/**
 * CONTROLADOR ADMIN_FORMAS_PAGO
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Formas_Pago extends Controller_Admin
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
		$data                 = array();
		$payment_methods_info = array();
		$per_page             = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$payment_methods = Model_Payments_Method::query()
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
			$payment_methods = $payment_methods->where(DB::expr("CONCAT(`t0`.`name`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $payment_methods->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('payment_methods', $config);

		# SE EJECUTA EL QUERY
		$payment_methods = $payment_methods->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($payment_methods))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($payment_methods as $payment_method)
			{
				# SE ALMACENA LA INFORMACION
				$payment_methods_info[] = array(
					'id'   => $payment_method->id,
					'code' => $payment_method->code,
					'name' => $payment_method->name
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['payment_methods'] = $payment_methods_info;
		$data['search']          = str_replace('%', ' ', $search);
		$data['pagination']      = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Formas de pago';
		$this->template->content = View::forge('admin/formas_pago/index', $data, false);
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
				Response::redirect('admin/formas_pago/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/formas_pago');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/forma_pago');
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
		$fields  = array('code', 'name');

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
			$val = Validation::forge('payment_method');
			$val->add_callable('Rules');
			$val->add_field('code', 'código', 'required|min_length[1]|max_length[255]');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE CREA EL MODELO CON LA INFORMACION
				$payment_method = new Model_Payments_Method(array(
					'code'    => $val->validated('code'),
					'name'    => $val->validated('name'),
					'deleted' => 0
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($payment_method->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó la forma de pago <b>'.$val->validated('name').'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/formas_pago');
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
		$this->template->title   = 'Agregar forma de pago';
		$this->template->content = View::forge('admin/formas_pago/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($payment_method_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($payment_method_id == 0 || !is_numeric($payment_method_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/formas_pago');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$payment_method = Model_Payments_Method::query()
		->where('id', $payment_method_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($payment_method))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['code'] = $payment_method->code;
			$data['name'] = $payment_method->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/formas_pago');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $payment_method_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información de la forma de pago';
		$this->template->content = View::forge('admin/formas_pago/info', $data);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($payment_method_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($payment_method_id == 0 || !is_numeric($payment_method_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/formas_pago');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('code', 'name');

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
		$payment_method = Model_Payments_Method::query()
		->where('id', $payment_method_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($payment_method))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['code'] = $payment_method->code;
			$data['name'] = $payment_method->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/formas_pago');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('payment_method');
			$val->add_callable('Rules');
			$val->add_field('code', 'código', 'required|min_length[1]|max_length[255]');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$payment_method->code = $val->validated('code');
				$payment_method->name = $val->validated('name');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($payment_method->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de <b>'.$payment_method->name.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/formas_pago/editar/'.$payment_method_id);
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
		$data['id']      = $payment_method_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar forma de pago';
		$this->template->content = View::forge('admin/formas_pago/editar', $data);
	}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($payment_method_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($payment_method_id == 0 || !is_numeric($payment_method_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/formas_pago');
		}

		# SE INICIALIZAN LAS VARIABLES
		$relations_info = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$payment_method = Model_Payments_Method::query()
		->where('id', $payment_method_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($payment_method))
		{
			# SE ESTEBLECE LA NUEVA INFORMACION
			$payment_method->deleted = 1;

			# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
			if($payment_method->save())
			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó la forma de pago <b>'.$payment_method->name.'</b> correctamente.');
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/formas_pago');
	}
}
