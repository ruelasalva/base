<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_MONTOS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Montos extends Controller_Admin
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
		$data         = array();
		$amounts_info = array();
		$per_page     = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$amounts = Model_Amount::query();

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
			$amounts = $amounts->where(DB::expr("CONCAT(`t0`.`name`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $amounts->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('amounts', $config);

		# SE EJECUTA EL QUERY
		$amounts = $amounts->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($amounts))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($amounts as $amount)
			{
				# SE ALMACENA LA INFORMACION
				$amounts_info[] = array(
					'id'   => $amount->id,
					'name' => $amount->name
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['amounts']    = $amounts_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Montos';
		$this->template->content = View::forge('admin/catalogo/montos/index', $data, false);
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
				Response::redirect('admin/catalogo/montos/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/catalogo/montos');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
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
			$val = Validation::forge('amount');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE CREA EL MODELO CON LA INFORMACION
				$amount = new Model_Amount(array(
					'name' => $val->validated('name')
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($amount->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el monto <b>'.$val->validated('name').'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/montos');
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
		$this->template->title   = 'Agregar monto';
		$this->template->content = View::forge('admin/catalogo/montos/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($amount_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($amount_id == 0 || !is_numeric($amount_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data                = array();
		$prices_amounts_info = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$amount = Model_Amount::query()
		->related('prices_amounts')
		->where('id', $amount_id)
		->order_by('prices_amounts.min_amount', 'asc')
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($amount))
		{
			# SI EXISTEN RANGOS
			if(!empty($amount->prices_amounts))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($amount->prices_amounts as $price_amount)
				{
					# SE ALMACENA LA INFORMACION
					$prices_amounts_info[] = array(
						'id'         => $price_amount->id,
						'min_amount' => '$'.number_format($price_amount->min_amount, '2', '.', ','),
						'max_amount' => '$'.number_format($price_amount->max_amount, '2', '.', ','),
						'percentage' => number_format($price_amount->percentage, '2', '.', ',').'%'
					);
				}
			}

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name']           = $amount->name;
			$data['prices_amounts'] = $prices_amounts_info;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $amount_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del monto';
		$this->template->content = View::forge('admin/catalogo/montos/info', $data);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($amount_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($amount_id == 0 || !is_numeric($amount_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data                = array();
		$classes             = array();
		$fields              = array('name');
		$prices_amounts_info = array();

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
		$amount = Model_Amount::query()
		->where('id', $amount_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($amount))
		{
			# SI EXISTEN RANGOS
			if(!empty($amount->prices_amounts))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($amount->prices_amounts as $price_amount)
				{
					# SE ALMACENA LA INFORMACION
					$prices_amounts_info[] = array(
						'id'         => $price_amount->id,
						'min_amount' => '$'.number_format($price_amount->min_amount, '2', '.', ','),
						'max_amount' => '$'.number_format($price_amount->max_amount, '2', '.', ','),
						'percentage' => number_format($price_amount->percentage, '2', '.', ',').'%'
					);
				}
			}
			
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name']           = $amount->name;
			$data['prices_amounts'] = $prices_amounts_info;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('amount');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$amount->name = $val->validated('name');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($amount->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de <b>'.$amount->name.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/montos/editar/'.$amount_id);
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
		$data['id']      = $amount_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar monto';
		$this->template->content = View::forge('admin/catalogo/montos/editar', $data);
	}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($amount_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($amount_id == 0 || !is_numeric($amount_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$relations_info = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$amount = Model_Amount::query()
		->related('products_prices_amounts')
		->where('id', $amount_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($amount))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$products_prices_amounts = Model_Products_Prices_Amount::query()
			->related('product')
			->where('amount_id', $amount_id)
			->get();

			# SI SE OBTIENE INFORMACION
			if(!empty($products_prices_amounts))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($products_prices_amounts as $product_price_amount)
				{
					# SE ALMACENA LA INFORMAICON DE LA RELACION
					$relations_info .= Html::anchor('admin/catalogo/productos/editar/'.$product_price_amount->id, $product_price_amount->product->name, array('target' => '_blank')).' - ';
				}

				# SE ELIMINAN LOS CARAQCTERES SOBRANTES Y SE AGREGA EL PUNTO
				$relations_info = substr($relations_info, 0, -3);

				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'No se puede eliminar el monto <b>'.$amount->name.'</b> porque tiene productos asignados a ella:<br>'.$relations_info);
			}
			else
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$price_amount = Model_Prices_Amount::query()
				->where('amount_id', $amount_id)
				->get();

				# SI SE OBTIENE INFORMACION
				if(!empty($price_amount))
				{
					# SE ALMACENA LA INFORMAICON DE LA RELACION
					$relations_info .= Html::anchor('admin/catalogo/montos/info/'.$amount_id, 'Ver rangos', array('target' => '_blank')).' - ';

					# SE ELIMINAN LOS CARAQCTERES SOBRANTES Y SE AGREGA EL PUNTO
					$relations_info = substr($relations_info, 0, -3);

					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', 'No se puede eliminar el monto <b>'.$amount->name.'</b> porque tiene rangos asignados a ella:<br>'.$relations_info);
				}
				else
				{
					# SI SE ELIMINO EL REGISTRO EN LA BASE DE DATOS
					if($amount->delete())
					{
						# SE ESTABLECE EL MENSAJE DE EXITO
						Session::set_flash('success', 'Se eliminó el monto <b>'.$amount->name.'</b> correctamente.');
					}
	                else
	                {
						# SE ESTABLECE EL MENSAJE DE ERROR
						Session::set_flash('error', 'No se pudo eliminar el monto <b>'.$amount->name.'</b> correctamente.');
					}
				}
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/catalogo/montos');
	}


	/**
	 * AGREGAR RANGO
	 *
	 * PERMITE AGREGAR UN REGISTRO A LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_agregar_rango($amount_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($amount_id == 0 || !is_numeric($amount_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('min_amount', 'max_amount', 'percentage');

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
		$amount = Model_Amount::query()
		->where('id', $amount_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($amount))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name'] = $amount->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SI SE UTILIZA EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('amount');
			$val->add_callable('Rules');
			$val->add_field('min_amount', 'monto mínimo', 'required|float');
			$val->add_field('max_amount', 'monto máximo', 'required|float');
			$val->add_field('percentage', 'porcentaje de descuento', 'required|float');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE CREA EL MODELO CON LA INFORMACION
				$price_amount = new Model_Prices_Amount(array(
					'amount_id' => $amount_id,
					'min_amount' => $val->validated('min_amount'),
					'max_amount' => $val->validated('max_amount'),
					'percentage' => $val->validated('percentage')
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($price_amount->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el rango correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/montos/info/'.$amount_id);
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
		$data['id']      = $amount_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar rango';
		$this->template->content = View::forge('admin/catalogo/montos/agregar_rango', $data);
	}


	/**
	 * INFO_RANGO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info_rango($price_amount_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($price_amount_id == 0 || !is_numeric($price_amount_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$price_amount = Model_Prices_Amount::query()
		->related('amount')
		->where('id', $price_amount_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($price_amount))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['amount_id']  = $price_amount->amount_id;
			$data['name']       = $price_amount->amount->name;
			$data['min_amount'] = $price_amount->min_amount;
			$data['max_amount'] = $price_amount->max_amount;
			$data['percentage'] = $price_amount->percentage;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $price_amount_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del rango';
		$this->template->content = View::forge('admin/catalogo/montos/info_rango', $data);
	}


	/**
	 * EDITAR_RANGO
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar_rango($price_amount_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($price_amount_id == 0 || !is_numeric($price_amount_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('min_amount', 'max_amount', 'percentage');

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
		$price_amount = Model_Prices_Amount::query()
		->related('amount')
		->where('id', $price_amount_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($price_amount))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['amount_id']  = $price_amount->amount_id;
			$data['name']       = $price_amount->amount->name;
			$data['min_amount'] = $price_amount->min_amount;
			$data['max_amount'] = $price_amount->max_amount;
			$data['percentage'] = $price_amount->percentage;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('amount');
			$val->add_callable('Rules');
			$val->add_field('min_amount', 'monto mínimo', 'required|float');
			$val->add_field('max_amount', 'monto máximo', 'required|float');
			$val->add_field('percentage', 'porcentaje de descuento', 'required|float');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$price_amount->min_amount = $val->validated('min_amount');
				$price_amount->max_amount = $val->validated('max_amount');
				$price_amount->percentage = $val->validated('percentage');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($price_amount->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/montos/editar_rango/'.$price_amount_id);
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
		$data['id']      = $price_amount_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar rango';
		$this->template->content = View::forge('admin/catalogo/montos/editar_rango', $data);
	}


	/**
	 * ELIMINAR RANGO
	 *
	 * ELIMINA UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar_rango($price_amount_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($price_amount_id == 0 || !is_numeric($price_amount_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$price_amount = Model_Prices_Amount::query()
		->related('amount')
		->where('id', $price_amount_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($price_amount))
		{
			# SE ALMACENA EL ID
			$amount_id = $price_amount->amount->id;

			# SI SE ELIMINO EL REGISTRO EN LA BASE DE DATOS
			if($price_amount->delete())
			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó el rango correctamente.');

				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/catalogo/montos/info/'.$amount_id);
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/catalogo/montos');
	}
}
