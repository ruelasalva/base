<?php

/**
 * CONTROLADOR ADMIN_CUPONES
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Cupones extends Controller_Admin
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
		$coupons_info = array();
		$per_page     = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$coupons = Model_Coupon::query()
        ->where('deleted', '>=', 0);

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
			$coupons = $coupons->where(DB::expr("CONCAT(`t0`.`name`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $coupons->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('coupons', $config);

		# SE EJECUTA EL QUERY
		$coupons = $coupons->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($coupons))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($coupons as $coupon)
			{

				# SE ALMACENA EL ESTATUS PARA
				$status = array(
					'0' => 'Activo',
					'1' => 'Cancelado'
				);

				# SE ALMACENA LA INFORMACION
				$coupons_info[] = array(
					'id'        => $coupon->id,
					'name'      => $coupon->name,
                    'discount'  => '$'.number_format($coupon->discount, '2', '.', ','),
					'quantity'  => $coupon->quantity,
					'available' => $coupon->available,
					'used'      => $coupon->quantity - $coupon->available,
					'minimum'   => ($coupon->minimum == 1) ? 'Sí' : 'No',
					'deleted'   => $status[$coupon->deleted],
					'validity'  => date('d/m/Y', $coupon->start_date).' - '.date('d/m/Y', $coupon->end_date),
                    'date'      => date('d/m/Y - H:i', $coupon->created_at)
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['coupons']    = $coupons_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Cupones';
		$this->template->content = View::forge('admin/cupones/index', $data, false);
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
				Response::redirect('admin/cupones/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/cupones');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/cupones');
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
		$fields  = array('name', 'code', 'discount', 'quantity', 'start_date', 'end_date', 'minimum', 'total_minimum');

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
			$val = Validation::forge('coupon');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('code', 'texto del cupón', 'required|min_length[1]|max_length[255]');
			$val->add_field('discount', 'descuento', 'required|float');
			$val->add_field('quantity', 'cantidad de cupones', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('start_date', 'fecha de inicio', 'required|date');
			$val->add_field('end_date', 'fecha final', 'required|date');
			$val->add_field('minimum', 'cantidad mínima', 'required|valid_string[numeric]|numeric_min[0]');

			# SI EXISTE UNA CANTUDAD MINIMA
			if(Input::post('minimum') == 1)
			{
				# SE AGREGA LA REGLA DE VALIDACION
				$val->add_field('total_minimum', 'mínimo del pedido', 'required|float');
			}

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
                # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
        		$coupon_exist = Model_Coupons_Code::query()
        		->where('code', $val->validated('code'))
        		->get_one();

                # SI NO SE OBTIENE INFORMACION
        		if(empty($coupon_exist))
        		{
                    # SE CREA EL MODELO CON LA INFORMACION
    				$coupon = new Model_Coupon(array(
    					'name'          => $val->validated('name'),
    					'discount'      => $val->validated('discount'),
    					'quantity'      => $val->validated('quantity'),
                        'available'     => $val->validated('quantity'),
                        'minimum'       => $val->validated('minimum'),
                        'total_minimum' => (Input::post('minimum') == 1) ? $val->validated('total_minimum') : 0,
    					'start_date'    => $this->date2unix($val->validated('start_date')),
    					'end_date'      => $this->date2unix($val->validated('end_date')),
    					'deleted'       => 0
    				));

    				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
    				if($coupon->save())
    				{
                        # SE GENERAN LOS CODIGOS
    					for($i = 1; $i <= $val->validated('quantity'); $i++)
    					{
    						# SE CREA EL MODELO PARA EL CODIGO
    						$code = new Model_Coupons_Code(array(
    							'coupon_id' => $coupon->id,
    							'sale_id'   => 0,
    							'code'      => $val->validated('code'),
    							'used'      => 0
    						));

    						# SE ALMACENA EL CODIGO EN LA BASE DE DATOS
    						$code->save();
    					}

    					# SE ESTABLECE EL MENSAJE DE EXITO
    					Session::set_flash('success', 'Se agregó el cupón <b>'.$val->validated('name').'</b> correctamente.');

    					# SE REDIRECCIONA AL USUARIO
    					Response::redirect('admin/cupones');
    				}
                }
                else
    			{
    				# SE ESTABLECE EL MENSAJE DE ERROR
    				Session::set_flash('error', 'Ya existe un cupón con ese texto, por favor ingresa uno diferente.');

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
		$this->template->title   = 'Agregar cupón';
		$this->template->content = View::forge('admin/cupones/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($coupon_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($coupon_id == 0 || !is_numeric($coupon_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/cupones');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data       = array();
        $codes_info = array();
        $code_name  = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$coupon = Model_Coupon::query()
		->where('id', $coupon_id)
        ->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($coupon))
		{
            # SI EXISTE LA RELACION
			if(!empty($coupon->codes))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($coupon->codes as $code)
				{
					# SE ALMACENA LA INFORMACION
					$codes_info[] = array(
						'id'    => $code->id,
						'code'  => $code->code,
						'sale_id'  => $code->sale_id,
						'used'  => ($code->used == 1) ? 'Sí' : 'No'
					);

                    # SE ALMACENA EL CODIGO DEL CUPON
                    $code_name = $code->code;
				}
			}

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name']          = $coupon->name;
			$data['code']          = $code_name;
            $data['discount']      = '$'.number_format($coupon->discount, '2', '.', ',');
            $data['quantity']      = $coupon->quantity;
            $data['available']     = $coupon->available;
            $data['used']          = $coupon->quantity - $coupon->available;
            $data['start_date']    = date('d/m/Y', $coupon->start_date);
            $data['end_date']      = date('d/m/Y', $coupon->end_date);
			$data['minimum']       = $coupon->minimum;
			$data['minimum_txt']   = ($coupon->minimum == 1) ? 'Sí' : 'No';
			$data['total_minimum'] = '$'.number_format($coupon->total_minimum, '2', '.', ',');
            $data['codes']         = $codes_info;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/cupones');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $coupon_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del cupón';
		$this->template->content = View::forge('admin/cupones/info', $data);
	}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($coupon_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($coupon_id == 0 || !is_numeric($coupon_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/cupones');
		}

		# SE INICIALIZAN LAS VARIABLES
		$relations_info = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$coupon = Model_Coupon::query()
		->where('id', $coupon_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($coupon))
		{
			# SE ESTEBLECE LA NUEVA INFORMACION
			$coupon->deleted = 1;

			# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
			if($coupon->save())
			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó el cupón <b>'.$coupon->name.'</b> correctamente.');
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/cupones');
	}


    /**
	 * DATE2UNIX
	 *
	 * CONVIERTE UNA FECHA EN UNIXTIME
	 *
	 * @access  private
	 * @return  Int
	 */
	private function date2unix($date = 0)
	{
		# SE ESTABLECE LA FECHA
		$date = ($date != 0) ? $date : date('d/m/Y');

		# SE CORTA LAS CADENAS
		$date = explode('/', $date);

		# SE DEVUELVE EL UNIXTIME
		return mktime(0, 0, 0, $date[1], $date[0], $date[2]);
	}
}
