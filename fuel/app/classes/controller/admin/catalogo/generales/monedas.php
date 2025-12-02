<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_MARCAS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Generales_Monedas extends Controller_Admin
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
	public function action_index($search = '')
{
    #HELPER DE PERMISO PARA VISTA
    if (!Helper_Permission::can('catalogo_monedas', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver monedas.');
        Response::redirect('admin');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data        = array();
    $curriencies_info = array();
    $per_page    = 100;

    # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
    $curriencies = Model_Currency::query()
        ->where('deleted', 0);

    # SI HAY UNA BUSQUEDA
    if($search != '')
    {
        $original_search = $search;
        $search = str_replace('+', ' ', rawurldecode($search));
        $search = str_replace(' ', '%', $search);

        # Puedes buscar por nombre, código o símbolo
        $curriencies = $curriencies->where_open()
            ->where(DB::expr("CONCAT(`t0`.`name`, `t0`.`code`, `t0`.`symbol`)"), 'like', '%'.$search.'%')
        ->where_close();
    }

    # SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
    $config = array(
        'name'           => 'admin',
        'pagination_url' => Uri::current(),
        'total_items'    => $curriencies->count(),
        'per_page'       => $per_page,
        'uri_segment'    => 'pagina',
    );

    $pagination = Pagination::forge('curriencies', $config);

    $curriencies = $curriencies->order_by('id', 'desc')
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

    if(!empty($curriencies))
    {
        foreach($curriencies as $currenci)
        {
            $curriencies_info[] = array(
                'id'           => $currenci->id,
                'code'         => $currenci->code,
                'name'         => $currenci->name,
                'symbol'       => $currenci->symbol,
                'type_exchange'=> $currenci->type_exchange,
                'deleted'      => $currenci->deleted,
            );
        }
    }

    $data['curriencies'] = $curriencies_info;
    $data['search']      = str_replace('%', ' ', $search);
    $data['pagination']  = $pagination->render();

    $this->template->title   = 'Monedas';
    $this->template->content = View::forge('admin/catalogo/generales/monedas/index', $data, false);
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
        $data = array(
            'search' => ($_POST['search'] != '') ? $_POST['search'] : '',
        );

        $val = Validation::forge('search');
        $val->add_field('search', 'search', 'max_length[100]');

        if($val->run($data))
        {
            $search = str_replace(' ', '+', $val->validated('search'));
            $search = str_replace('*', '', $search);
            $search = ($val->validated('search') != '') ? $search : '';
            Response::redirect('admin/catalogo/generales/monedas/index/'.$search);
        }
        else
        {
            Response::redirect('admin/catalogo/generales/monedas');
        }
    }
    else
    {
        Response::redirect('admin/catalogo/generales/monedas');
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
    #HELPER DE PERMISO PARA CREAR
    if (!Helper_Permission::can('catalogo_monedas', 'create')) {
        Session::set_flash('error', 'No tienes permiso para crear monedas.');
        Response::redirect('admin/catalogo/generales/monedas');	
    }

    # SE INICIALIZAN LAS VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('name', 'code', 'symbol', 'type_exchange');

    # SE RECORRE CAMPO POR CAMPO PARA INICIALIZAR CLASES
    foreach($fields as $field)
    {
        $classes[$field] = array (
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # SI SE UTILIZA EL METODO POST
    if(Input::method() == 'POST')
    {
        # SE CREA LA VALIDACION DE LOS CAMPOS
        $val = Validation::forge('currency');
        $val->add_callable('Rules');
        $val->add_field('name', 'nombre', 'required|min_length[1]|max_length[64]');
        $val->add_field('code', 'código', 'required|min_length[1]|max_length[8]');
        $val->add_field('symbol', 'símbolo', 'required|min_length[1]|max_length[8]');
        $val->add_field('type_exchange', 'tipo de cambio', 'required|valid_number|min_length[1]|max_length[16]');

        # SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
        if($val->run())
        {
            # CREA EL MODELO CON LA INFORMACION
            $currency = new Model_Currency(array(
                'name'         => $val->validated('name'),
                'code'         => $val->validated('code'),
                'symbol'       => $val->validated('symbol'),
                'type_exchange'=> $val->validated('type_exchange'),
                'deleted'      => 0,
                'created_at'   => time(),
                'updated_at'   => time(),
            ));

            # SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
            if($currency->save())
            {
                # SE ESTABLECE EL MENSAJE DE EXITO
                Session::set_flash('success', 'Se agregó la moneda <b>'.$val->validated('name').'</b> correctamente.');

                # SE REDIRECCIONA AL USUARIO
                Response::redirect('admin/catalogo/generales/monedas');
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
    $this->template->title   = 'Agregar moneda';
    $this->template->content = View::forge('admin/catalogo/generales/monedas/agregar', $data);
}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($currency_id = 0)
{
    #HELPER DE PERMISO PARA VER
    if (!Helper_Permission::can('catalogo_monedas', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver monedas.');
        Response::redirect('admin');
    }

    # SI NO SE RECIBE UN ID O NO ES UN NUMERO
    if($currency_id == 0 || !is_numeric($currency_id))
    {
        Response::redirect('admin/catalogo/generales/monedas');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data = array();

    # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
    $currency = Model_Currency::query()
        ->where('id', $currency_id)
        ->where('deleted', 0)
        ->get_one();

    # SI SE OBTIENE INFORMACION
    if(!empty($currency))
    {
        # SE ALMACENA LA INFORMACION PARA LA VISTA
        $data['id']            = $currency_id;
        $data['name']          = $currency->name;
        $data['code']          = $currency->code;
        $data['symbol']        = $currency->symbol;
        $data['type_exchange'] = $currency->type_exchange;
        $data['deleted']       = $currency->deleted;
        $data['created_at']    = $currency->created_at;
        $data['updated_at']    = $currency->updated_at;
    }
    else
    {
        Response::redirect('admin/catalogo/generales/monedas');
    }

    # SE CARGA LA VISTA
    $this->template->title   = 'Información de la moneda';
    $this->template->content = View::forge('admin/catalogo/generales/monedas/info', $data);
}



	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($currency_id = 0)
{
    #HELPER DE PERMISO PARA EDITAR
    if (!Helper_Permission::can('catalogo_monedas', 'edit')) {
        Session::set_flash('error', 'No tienes permiso para editar monedas.');
        Response::redirect('admin/catalogo/generales/monedas');
    }

    # SI NO SE RECIBE UN ID O NO ES UN NUMERO
    if($currency_id == 0 || !is_numeric($currency_id))
    {
        Response::redirect('admin/catalogo/generales/monedas');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('name', 'code', 'symbol', 'type_exchange');

    foreach($fields as $field)
    {
        $classes[$field] = array (
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
    $currency = Model_Currency::query()
        ->where('id', $currency_id)
        ->where('deleted', 0)
        ->get_one();

    if(!empty($currency))
    {
        # CARGA DATOS ORIGINALES PARA LA VISTA
        $data['name']         = $currency->name;
        $data['code']         = $currency->code;
        $data['symbol']       = $currency->symbol;
        $data['type_exchange']= $currency->type_exchange;
    }
    else
    {
        Response::redirect('admin/catalogo/generales/monedas');
    }

    # SI SE UTILIZO EL METODO POST
    if(Input::method() == 'POST')
    {
        $val = Validation::forge('currency');
        $val->add_callable('Rules');
        $val->add_field('name', 'nombre', 'required|min_length[1]|max_length[64]');
        $val->add_field('code', 'código', 'required|min_length[1]|max_length[8]');
        $val->add_field('symbol', 'símbolo', 'required|min_length[1]|max_length[8]');
        $val->add_field('type_exchange', 'tipo de cambio', 'required|valid_number|min_length[1]|max_length[16]');

        if($val->run())
        {
            # ACTUALIZA LOS CAMPOS
            $currency->name          = $val->validated('name');
            $currency->code          = $val->validated('code');
            $currency->symbol        = $val->validated('symbol');
            $currency->type_exchange = $val->validated('type_exchange');
            $currency->updated_at    = time();

            if($currency->save())
            {
                Session::set_flash('success', 'Se actualizó la información de la moneda <b>'.$currency->name.'</b> correctamente.');
                Response::redirect('admin/catalogo/generales/monedas/editar/'.$currency_id);
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

    # SE ALMACENA LA INFORMACION PARA LA VISTA
    $data['id']      = $currency_id;
    $data['classes'] = $classes;

    # SE CARGA LA VISTA
    $this->template->title   = 'Editar moneda';
    $this->template->content = View::forge('admin/catalogo/generales/monedas/editar', $data);
}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($currency_id = 0)
{
    #HELPER DE PERMISO PARA ELIMINAR
    if (!Helper_Permission::can('catalogo_monedas', 'delete')) {
        Session::set_flash('error', 'No tienes permiso para eliminar monedas.');
        Response::redirect('admin/catalogo/generales/monedas');
    }

    # SI NO SE RECIBE UN ID O NO ES UN NUMERO
    if($currency_id == 0 || !is_numeric($currency_id))
    {
        Response::redirect('admin/catalogo/generales/monedas');
    }

    # SE INICIALIZAN LAS VARIABLES
    $relations_info = '';

    # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
    $currency = Model_Currency::query()
        ->where('id', $currency_id)
        ->where('deleted', 0)
        ->get_one();

    # SI SE OBTIENE INFORMACION
    if(!empty($currency))
    {
        // Si tienes tablas relacionadas, por ejemplo facturas, podrías poner aquí la comprobación.
        // Ejemplo:
        /*
        $invoices = Model_Invoice::query()
            ->where('currency_id', $currency_id)
            ->where('deleted', 0)
            ->get();

        if(!empty($invoices))
        {
            foreach($invoices as $invoice)
            {
                $relations_info .= Html::anchor('admin/ventas/facturas/editar/'.$invoice->id, $invoice->folio, array('target' => '_blank')).' - ';
            }
            $relations_info = substr($relations_info, 0, -3);
            Session::set_flash('error', 'No se puede eliminar la moneda <b>'.$currency->name.'</b> porque tiene facturas asignadas:<br>'.$relations_info);
        }
        else
        {
        */
            # SE ESTEBLECE LA NUEVA INFORMACION
            $currency->deleted = 1;

            # SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
            if($currency->save())
            {
                Session::set_flash('success', 'Se eliminó la moneda <b>'.$currency->name.'</b> correctamente.');
            }
        //}
    }

    # SE REDIRECCIONA AL USUARIO
    Response::redirect('admin/catalogo/generales/monedas');
}

}
