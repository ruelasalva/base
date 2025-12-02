<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_MARCAS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Generales_Impuestos extends Controller_Admin
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
    if (!Helper_Permission::can('catalogo_impuestos', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver impuestos.');
        Response::redirect('admin');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data         = array();
    $taxes_info   = array();
    $per_page     = 100;

    # CONSULTA PRINCIPAL
    $taxes = Model_Tax::query();

    # SI HAY UNA BUSQUEDA
    if($search != '')
    {
        $original_search = $search;
        $search = str_replace('+', ' ', rawurldecode($search));
        $search = str_replace(' ', '%', $search);

        # Puedes buscar por nombre, código o tipo_sat
        $taxes = $taxes->where_open()
            ->where(DB::expr("CONCAT(`t0`.`name`, ' ', `t0`.`code`, ' ', `t0`.`tipo_sat`)"), 'like', '%'.$search.'%')
        ->where_close();
    }

    # PAGINACIÓN
    $config = array(
        'name'           => 'admin',
        'pagination_url' => Uri::current(),
        'total_items'    => $taxes->count(),
        'per_page'       => $per_page,
        'uri_segment'    => 'pagina',
    );

    $pagination = Pagination::forge('admin', $config);

    $taxes = $taxes->order_by('id', 'desc')
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

    # FORMATEO PARA LA VISTA
    if(!empty($taxes))
    {
        foreach($taxes as $tax)
        {
            $taxes_info[] = array(
                'id'         => $tax->id,
                'code'       => $tax->code,
                'name'       => $tax->name,
                'type_factor'=> $tax->type_factor,
                'rate'       => $tax->rate,
                'clave_sat'  => $tax->clave_sat,
                'tipo_sat'   => $tax->tipo_sat,
            );
        }
    }

    $data['taxes']      = $taxes_info;
    $data['search']     = str_replace('%', ' ', $search);
    $data['pagination'] = $pagination->render();

    $this->template->title   = 'Impuestos';
    $this->template->content = View::forge('admin/catalogo/generales/impuestos/index', $data, false);
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
            Response::redirect('admin/catalogo/generales/impuestos/index/'.$search);
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
    if (!Helper_Permission::can('catalogo_impuestos', 'create')) {
        Session::set_flash('error', 'No tienes permiso para crear impuestos.');
        Response::redirect('admin/catalogo/generales/impuestos');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('code', 'name', 'type_factor', 'rate', 'clave_sat', 'tipo_sat');

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
        $val = Validation::forge('tax');
        $val->add_callable('Rules');
        $val->add_field('code',        'código',        'required|min_length[1]|max_length[16]');
        $val->add_field('name',        'nombre',        'required|min_length[1]|max_length[128]');
        $val->add_field('type_factor', 'tipo de factor','required|min_length[1]|max_length[16]');
        $val->add_field('rate',        'tasa',          'required|min_length[1]|[numeric]');
        $val->add_field('clave_sat',   'clave SAT',     'required|min_length[1]|max_length[8]');
        $val->add_field('tipo_sat',    'tipo SAT',      'required|min_length[1]|max_length[32]');

        # SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
        if($val->run())
        {
            # CREA EL MODELO CON LA INFORMACION
            $tax = new Model_Tax(array(
                'code'        => $val->validated('code'),
                'name'        => $val->validated('name'),
                'type_factor' => $val->validated('type_factor'),
                'rate'        => $val->validated('rate'),
                'clave_sat'   => $val->validated('clave_sat'),
                'tipo_sat'    => $val->validated('tipo_sat'),
                'created_at'  => time(),
                'updated_at'  => time(),
            ));

            # SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
            if($tax->save())
            {
                Session::set_flash('success', 'Se agregó el impuesto <b>'.$val->validated('name').'</b> correctamente.');
                Response::redirect('admin/catalogo/generales/impuestos');
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
    $this->template->title   = 'Agregar impuesto';
    $this->template->content = View::forge('admin/catalogo/generales/impuestos/agregar', $data);
}



	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($tax_id = 0)
{
    #HELPER DE PERMISO PARA VER
    if (!Helper_Permission::can('catalogo_impuestos', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver impuestos.');
        Response::redirect('admin');
    }

    # VALIDACIÓN DE ID
    if($tax_id == 0 || !is_numeric($tax_id))
    {
        Response::redirect('admin/catalogo/generales/impuestos');
    }

    # INICIALIZA VARIABLES
    $data = array();

    # BUSCA EL IMPUESTO
    $tax = Model_Tax::query()
        ->where('id', $tax_id)
        ->get_one();

    if(!empty($tax))
    {
        $data['id']          = $tax->id;
        $data['code']        = $tax->code;
        $data['name']        = $tax->name;
        $data['type_factor'] = $tax->type_factor;
        $data['rate']        = $tax->rate;
        $data['clave_sat']   = $tax->clave_sat;
        $data['tipo_sat']    = $tax->tipo_sat;
        $data['created_at']  = $tax->created_at;
        $data['updated_at']  = $tax->updated_at;
    }
    else
    {
        Response::redirect('admin/catalogo/generales/impuestos');
    }

    # CARGA LA VISTA
    $this->template->title   = 'Información del impuesto';
    $this->template->content = View::forge('admin/catalogo/generales/impuestos/info', $data);
}




	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($tax_id = 0)
{
    #HELPER DE PERMISO PARA EDITAR
    if (!Helper_Permission::can('catalogo_impuestos', 'edit')) {
        Session::set_flash('error', 'No tienes permiso para editar impuestos.');
        Response::redirect('admin/catalogo/generales/impuestos');
    }

    # VALIDACIÓN DE ID
    if($tax_id == 0 || !is_numeric($tax_id))
    {
        Response::redirect('admin/catalogo/generales/impuestos');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('code', 'name', 'type_factor', 'rate', 'clave_sat', 'tipo_sat');

    foreach($fields as $field)
    {
        $classes[$field] = array (
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # SE BUSCA EL IMPUESTO
    $tax = Model_Tax::query()
        ->where('id', $tax_id)
        ->get_one();

    if(!empty($tax))
    {
        # SI NO SE VIENE DE UN POST, CARGA LOS VALORES ORIGINALES
        if (Input::method() != 'POST') {
            $data['code']        = $tax->code;
            $data['name']        = $tax->name;
            $data['type_factor'] = $tax->type_factor;
            $data['rate']        = $tax->rate;
            $data['clave_sat']   = $tax->clave_sat;
            $data['tipo_sat']    = $tax->tipo_sat;
        }
    }
    else
    {
        Response::redirect('admin/catalogo/generales/impuestos');
    }

    # SI SE UTILIZA EL METODO POST
    if(Input::method() == 'POST')
    {
        $val = Validation::forge('tax');
        $val->add_callable('Rules');
        $val->add_field('code',        'código',        'required|min_length[1]|max_length[16]');
        $val->add_field('name',        'nombre',        'required|min_length[1]|max_length[128]');
        $val->add_field('type_factor', 'tipo de factor','required|min_length[1]|max_length[16]');
        $val->add_field('rate',        'tasa',          'required|valid_string[numeric]');
        $val->add_field('clave_sat',   'clave SAT',     'required|min_length[1]|max_length[8]');
        $val->add_field('tipo_sat',    'tipo SAT',      'required|min_length[1]|max_length[32]');

        if($val->run())
        {
            $tax->code        = $val->validated('code');
            $tax->name        = $val->validated('name');
            $tax->type_factor = $val->validated('type_factor');
            $tax->rate        = $val->validated('rate');
            $tax->clave_sat   = $val->validated('clave_sat');
            $tax->tipo_sat    = $val->validated('tipo_sat');
            $tax->updated_at  = time();

            if($tax->save())
            {
                Session::set_flash('success', 'Se actualizó el impuesto <b>'.$tax->name.'</b> correctamente.');
                Response::redirect('admin/catalogo/generales/impuestos/editar/'.$tax_id);
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

    $data['id']      = $tax_id;
    $data['classes'] = $classes;

    $this->template->title   = 'Editar impuesto';
    $this->template->content = View::forge('admin/catalogo/generales/impuestos/editar', $data);
}



	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($tax_id = 0)
{
    #HELPER DE PERMISO PARA ELIMINAR
    if (!Helper_Permission::can('catalogo_impuestos', 'delete')) {
        Session::set_flash('error', 'No tienes permiso para eliminar impuestos.');
        Response::redirect('admin/catalogo/generales/impuestos');
    }

    # VALIDACIÓN DE ID
    if($tax_id == 0 || !is_numeric($tax_id))
    {
        Response::redirect('admin/catalogo/generales/impuestos');
    }

    # BUSCA EL IMPUESTO
    $tax = Model_Tax::query()
        ->where('id', $tax_id)
        ->get_one();

    if(!empty($tax))
    {
        if($tax->delete())
        {
            Session::set_flash('success', 'Se eliminó el impuesto <b>'.$tax->name.'</b> correctamente.');
        }
        else
        {
            Session::set_flash('error', 'No se pudo eliminar el impuesto.');
        }
    }

    Response::redirect('admin/catalogo/generales/impuestos');
}


}
