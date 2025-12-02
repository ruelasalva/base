<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_MARCAS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Generales_Tipodecambio extends Controller_Admin
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

			# SE VERIFICA QUE EL USUARIO ESTA LOGUEADO
			if (!Auth::check()) {
			# SE MANDA MENSAJE SI NO	
			Session::set_flash('error', 'Debes iniciar sesión.');
			# Y SE REDIRECIONA A QUE SE LOGUEE
			Response::redirect('admin/login');
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
	public function action_index($search = '', $currency_id = 0)
{
    # PERMISO PARA VER
    if (!Helper_Permission::can('catalogo_tipodecambio', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver tipos de cambio.');
        Response::redirect('admin');
    }

    $data = array();
    $exchanges_info = array();
    $per_page = 100;

    # FILTROS POR GET (SIEMPRE)
    $search      = trim(Input::get('search', ''));
    $currency_id = intval(Input::get('currency_id', 0));

    # OBTENER MONEDAS DISPONIBLES PARA EL FILTRO
    $currencies = Model_Currency::query()->where('deleted', 0)->order_by('name', 'asc')->get();
    $data['currencies'] = $currencies;
    $data['selected_currency'] = $currency_id;
    $data['search'] = $search;

    # CONSULTA PRINCIPAL
    $exchanges = Model_Exchange::query()->related('currency')->order_by('date', 'desc')->order_by('currency_id', 'asc');

    # FILTRO POR MONEDA
    if ($currency_id > 0) {
        $exchanges->where('currency_id', $currency_id);
    }

    # FILTRO POR FECHA
    if ($search != '') {
        $exchanges->where('date', 'like', '%'.$search.'%');
    }

    # PAGINACIÓN
    $querystring = http_build_query(array_filter(array('search'=>$search, 'currency_id'=>$currency_id)));
    $config = array(
        'name'           => 'admin',
        'pagination_url' => Uri::current().($querystring ? '?'.$querystring : ''),
        'total_items'    => $exchanges->count(),
        'per_page'       => $per_page,
        'uri_segment'    => 'pagina',
    );

    $pagination = Pagination::forge('exchanges', $config);

    $exchanges = $exchanges->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

    # FORMATEO PARA LA VISTA
    foreach($exchanges as $exchange)
    {
        $exchanges_info[] = array(
            'id'         => $exchange->id,
            'currency'   => $exchange->currency->name,
            'currency_id'=> $exchange->currency_id,
            'rate'       => $exchange->rate,
            'date'       => $exchange->date,
        );
    }

    $data['exchanges']   = $exchanges_info;
    $data['pagination']  = $pagination->render();

    $this->template->title   = 'Tipos de Cambio';
    $this->template->content = View::forge('admin/catalogo/generales/tipodecambio/index', $data, false);
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
    if(Input::method() == 'POST')
    {
        $search      = trim(Input::post('search', ''));
        $currency_id = intval(Input::post('currency_id', 0));

        // Construye querystring solo con los valores capturados (opcional)
        $params = array();
        if ($search !== '')      $params['search'] = $search;
        if ($currency_id > 0)    $params['currency_id'] = $currency_id;

        $url = 'admin/catalogo/generales/tipodecambio';
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        Response::redirect($url);
    }
    else
    {
        Response::redirect('admin/catalogo/generales/tipodecambio');
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
    # PERMISO PARA CREAR
    if (!Helper_Permission::can('catalogo_tipodecambio', 'create')) {
        Session::set_flash('error', 'No tienes permiso para crear tipos de cambio.');
        Response::redirect('admin/catalogo/generales/tipodecambio');
    }

    # INICIALIZA VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('currency_id', 'rate', 'date');

    foreach($fields as $field)
    {
        $classes[$field] = array (
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # OBTIENE MONEDAS DISPONIBLES
    $currencies = Model_Currency::query()->where('deleted', 0)->order_by('name', 'asc')->get();
    $data['currencies'] = $currencies;

    # POST
    if(Input::method() == 'POST')
    {
        $val = Validation::forge('exchange');
        $val->add_field('currency_id', 'moneda', 'required|valid_string[numeric]');
        $val->add_field('rate',       'tipo de cambio', 'required|numeric_between[0.000001,99999999]');
        $val->add_field('date',       'fecha', 'required|match_pattern[#^\d{4}-\d{2}-\d{2}$#]');

        if($val->run())
        {
            // Verifica si ya existe registro para esa moneda y fecha
            $exists = Model_Exchange::query()
                ->where('currency_id', $val->validated('currency_id'))
                ->where('date', $val->validated('date'))
                ->get_one();

            if ($exists) {
                Session::set_flash('error', 'Ya existe un tipo de cambio para esa moneda y fecha.');
            } else {
                $exchange = new Model_Exchange(array(
                    'currency_id' => $val->validated('currency_id'),
                    'rate'        => $val->validated('rate'),
                    'date'        => $val->validated('date'),
                    'created_at'  => time(),
                    'updated_at'  => time(),
                ));

                if($exchange->save())
                {
                    Session::set_flash('success', 'Se agregó el tipo de cambio correctamente.');
                    Response::redirect('admin/catalogo/generales/tipodecambio');
                }
            }
        }
        else
        {
            Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');
            $data['errors'] = $val->error();

            foreach($classes as $name => $class)
            {
                $classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
                $classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
                $data[$name] = Input::post($name);
            }
        }
    }

    $data['classes'] = $classes;

    $this->template->title   = 'Agregar tipo de cambio';
    $this->template->content = View::forge('admin/catalogo/generales/tipodecambio/agregar', $data);
}



	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($exchange_id = 0)
{
    # PERMISO PARA VER
    if (!Helper_Permission::can('catalogo_tipodecambio', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver tipos de cambio.');
        Response::redirect('admin/catalogo/generales/tipodecambio');
    }

    # VALIDACIÓN DE ID
    if($exchange_id == 0 || !is_numeric($exchange_id))
    {
        Response::redirect('admin/catalogo/generales/tipodecambio');
    }

    # INICIALIZA VARIABLES
    $data = array();

    # BUSCA EL TIPO DE CAMBIO
    $exchange = Model_Exchange::query()
        ->related('currency')
        ->where('id', $exchange_id)
        ->get_one();

    if(!empty($exchange))
    {
        $data['id']         = $exchange->id;
        $data['currency']   = $exchange->currency ? $exchange->currency->name : '';
        $data['rate']       = $exchange->rate;
        $data['date']       = $exchange->date;
        $data['created_at'] = $exchange->created_at;
        $data['updated_at'] = $exchange->updated_at;
    }
    else
    {
        Response::redirect('admin/catalogo/generales/tipodecambio');
    }

    $this->template->title   = 'Información del tipo de cambio';
    $this->template->content = View::forge('admin/catalogo/generales/tipodecambio/info', $data);
}




	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($exchange_id = 0)
{
    # PERMISO PARA EDITAR
    if (!Helper_Permission::can('catalogo_tipodecambio', 'edit')) {
        Session::set_flash('error', 'No tienes permiso para editar tipos de cambio.');
        Response::redirect('admin/catalogo/generales/tipodecambio');
    }

    # VALIDACIÓN DE ID
    if($exchange_id == 0 || !is_numeric($exchange_id))
    {
        Response::redirect('admin/catalogo/generales/tipodecambio');
    }

    # INICIALIZA VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('currency_id', 'rate', 'date');

    foreach($fields as $field)
    {
        $classes[$field] = array (
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # OBTIENE MONEDAS DISPONIBLES
    $currencies = Model_Currency::query()->where('deleted', 0)->order_by('name', 'asc')->get();
    $data['currencies'] = $currencies;

    # BUSCA EL REGISTRO
    $exchange = Model_Exchange::query()
        ->where('id', $exchange_id)
        ->get_one();

    if(!empty($exchange))
    {
        if (Input::method() != 'POST') {
            $data['currency_id'] = $exchange->currency_id;
            $data['rate']        = $exchange->rate;
            $data['date']        = $exchange->date;
        }
    }
    else
    {
        Response::redirect('admin/catalogo/generales/tipodecambio');
    }

    # POST
    if(Input::method() == 'POST')
    {
        $val = Validation::forge('exchange');
        $val->add_field('currency_id', 'moneda', 'required|valid_string[numeric]');
        $val->add_field('rate',       'tipo de cambio', 'required|numeric_between[0.000001,99999999]');
        $val->add_field('date',       'fecha', 'required|match_pattern[#^\d{4}-\d{2}-\d{2}$#]');

        if($val->run())
        {
            // SOLO PERMITE EDITAR rate (no debería poder cambiar moneda ni fecha)
            $exchange->rate       = $val->validated('rate');
            $exchange->updated_at = time();

            if($exchange->save())
            {
                Session::set_flash('success', 'Se actualizó el tipo de cambio correctamente.');
                Response::redirect('admin/catalogo/generales/tipodecambio/editar/'.$exchange_id);
            }
        }
        else
        {
            Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');
            $data['errors'] = $val->error();

            foreach($classes as $name => $class)
            {
                $classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
                $classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
                $data[$name] = Input::post($name);
            }
        }
    }

    $data['id']      = $exchange_id;
    $data['classes'] = $classes;

    $this->template->title   = 'Editar tipo de cambio';
    $this->template->content = View::forge('admin/catalogo/generales/tipodecambio/editar', $data);
}




}
