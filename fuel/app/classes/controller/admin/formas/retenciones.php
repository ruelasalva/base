<?php

/**
 * CONTROLADOR ADMIN_FORMAS_USOSCFDI
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Formas_Retenciones extends Controller_Admin
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
    #HELPER DE PERMISO PARA VISTA
    if (!Helper_Permission::can('catalogo_retenciones', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver retenciones.');
        Response::redirect('admin');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data = array();
    $retentions_info = array();
    $per_page = 100;

    # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
    $retentions = Model_Retention::query();

    # SI HAY UNA BUSQUEDA
    if($search != '')
    {
        $original_search = $search;
        $search = str_replace('+', ' ', rawurldecode($search));
        $search = str_replace(' ', '%', $search);

        # Busca por código o descripción
        $retentions = $retentions->where_open()
            ->where(DB::expr("CONCAT(`t0`.`description`, ' ', `t0`.`code`)"), 'like', '%'.$search.'%')
        ->where_close();
    }

    # PAGINACIÓN
    $config = array(
        'name'           => 'admin',
        'pagination_url' => Uri::current(),
        'total_items'    => $retentions->count(),
        'per_page'       => $per_page,
        'uri_segment'    => 'pagina',
    );
    $pagination = Pagination::forge('retentions', $config);

    $retentions = $retentions->order_by('id', 'desc')
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

    if(!empty($retentions))
    {
        foreach($retentions as $r)
        {
            $retentions_info[] = array(
                'id'          => $r->id,
                'code'        => $r->code,
                'description' => $r->description,
                'type'        => $r->type,
                'category'    => $r->category,
                'rate'        => $r->rate,
                'account'     => $r->account,
            );
        }
    }

    $data['retentions'] = $retentions_info;
    $data['search']     = str_replace('%', ' ', $search);
    $data['pagination'] = $pagination->render();

    $this->template->title   = 'Retenciones';
    $this->template->content = View::forge('admin/retenciones/index', $data, false);
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
            Response::redirect('admin/formas_retenciones/index/'.$search);
        }
        else
        {
            Response::redirect('admin/formas_retenciones');
        }
    }
    else
    {
        Response::redirect('admin/formas_retenciones');
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
    if (!Helper_Permission::can('catalogo_retenciones', 'create')) {
        Session::set_flash('error', 'No tienes permiso para crear retenciones.');
        Response::redirect('admin/formas_retenciones');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('code', 'description', 'type', 'category', 'valid_from', 'base_type', 'rate', 'account', 'factor_type');

    # INICIALIZA CLASES DE VALIDACIÓN POR CAMPO
    foreach($fields as $field)
    {
        $classes[$field] = array (
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # SI SE UTILIZA EL MÉTODO POST
    if(Input::method() == 'POST')
    {
        # VALIDACIÓN DE CAMPOS
        $val = Validation::forge('retention');
        $val->add_callable('Rules');
        $val->add_field('code', 'código', 'required|min_length[1]|max_length[8]');
        $val->add_field('description', 'descripción', 'required|min_length[1]|max_length[128]');
        $val->add_field('type', 'tipo', 'required|min_length[1]|max_length[32]');
        $val->add_field('category', 'categoría', 'max_length[32]');
        $val->add_field('valid_from', 'vigencia desde', 'valid_date[Y-m-d]');
        $val->add_field('base_type', 'tipo de base', 'max_length[16]');
        $val->add_field('rate', 'tasa', 'numeric_between[0,100]');
        $val->add_field('account', 'cuenta contable', 'max_length[32]');
        $val->add_field('factor_type', 'tipo de factor', 'max_length[16]');

        # SI NO HAY ERRORES
        if($val->run())
        {
            $retention = new Model_Retention(array(
                'code'        => $val->validated('code'),
                'description' => $val->validated('description'),
                'type'        => $val->validated('type'),
                'category'    => Input::post('category', null),
                'valid_from'  => Input::post('valid_from', null),
                'base_type'   => Input::post('base_type', null),
                'rate'        => Input::post('rate', null),
                'account'     => Input::post('account', null),
                'factor_type' => Input::post('factor_type', null),
                'created_at'  => time(),
                'updated_at'  => time(),
            ));

            if($retention->save())
            {
                Session::set_flash('success', 'Se agregó la retención <b>'.$val->validated('code').' - '.$val->validated('description').'</b> correctamente.');
                Response::redirect('admin/formas_retenciones');
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

    # SE ALMACENA INFORMACIÓN PARA LA VISTA
    $data['classes'] = $classes;

    # SE CARGA LA VISTA
    $this->template->title   = 'Agregar retención';
    $this->template->content = View::forge('admin/retenciones/agregar', $data);
}



	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($retention_id = 0)
{
    #HELPER DE PERMISO PARA VER
    if (!Helper_Permission::can('catalogo_retenciones', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver retenciones.');
        Response::redirect('admin');
    }

    # SI NO SE RECIBE UN ID O NO ES UN NÚMERO
    if($retention_id == 0 || !is_numeric($retention_id))
    {
        Response::redirect('admin/formas_retenciones');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data = array();

    # SE BUSCA LA INFORMACIÓN A TRAVÉS DEL MODELO
    $retention = Model_Retention::query()
        ->where('id', $retention_id)
        ->get_one();

    # SI SE OBTIENE INFORMACIÓN
    if(!empty($retention))
    {
        $data['id']          = $retention_id;
        $data['code']        = $retention->code;
        $data['description'] = $retention->description;
        $data['type']        = $retention->type;
        $data['category']    = $retention->category;
        $data['valid_from']  = $retention->valid_from;
        $data['base_type']   = $retention->base_type;
        $data['rate']        = $retention->rate;
        $data['account']     = $retention->account;
        $data['factor_type'] = $retention->factor_type;
        $data['created_at']  = $retention->created_at;
        $data['updated_at']  = $retention->updated_at;
    }
    else
    {
        Response::redirect('admin/formas_retenciones');
    }

    # SE CARGA LA VISTA
    $this->template->title   = 'Información de la retención';
    $this->template->content = View::forge('admin/retenciones/info', $data);
}



	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($retention_id = 0)
{
    #HELPER DE PERMISO PARA EDITAR
    if (!Helper_Permission::can('catalogo_retenciones', 'edit')) {
        Session::set_flash('error', 'No tienes permiso para editar retenciones.');
        Response::redirect('admin/formas_retenciones');
    }

    # SI NO SE RECIBE UN ID O NO ES UN NUMERO
    if($retention_id == 0 || !is_numeric($retention_id))
    {
        Response::redirect('admin/formas_retenciones');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('code', 'description', 'type', 'category', 'valid_from', 'base_type', 'rate', 'account', 'factor_type');

    foreach($fields as $field)
    {
        $classes[$field] = array (
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
    $retention = Model_Retention::query()
        ->where('id', $retention_id)
        ->get_one();

    if(!empty($retention))
    {
        # CARGA DATOS ORIGINALES PARA LA VISTA (SOLO si no viene de POST con error)
        if (Input::method() != 'POST') {
            $data['code']        = $retention->code;
            $data['description'] = $retention->description;
            $data['type']        = $retention->type;
            $data['category']    = $retention->category;
            $data['valid_from']  = $retention->valid_from;
            $data['base_type']   = $retention->base_type;
            $data['rate']        = $retention->rate;
            $data['account']     = $retention->account;
            $data['factor_type'] = $retention->factor_type;
        }
    }
    else
    {
        Response::redirect('admin/formas_retenciones');
    }

    # SI SE UTILIZO EL METODO POST
    if(Input::method() == 'POST')
    {
        $val = Validation::forge('retention');
        $val->add_callable('Rules');
        $val->add_field('code', 'código', 'required|min_length[1]|max_length[8]');
        $val->add_field('description', 'descripción', 'required|min_length[1]|max_length[128]');
        $val->add_field('type', 'tipo', 'required|min_length[1]|max_length[32]');
        $val->add_field('category', 'categoría', 'max_length[32]');
        $val->add_field('valid_from', 'vigencia desde', 'valid_date[Y-m-d]');
        $val->add_field('base_type', 'tipo de base', 'max_length[16]');
        $val->add_field('rate', 'tasa', 'numeric_between[0,100]');
        $val->add_field('account', 'cuenta contable', 'max_length[32]');
        $val->add_field('factor_type', 'tipo de factor', 'max_length[16]');

        if($val->run())
        {
            $retention->code        = $val->validated('code');
            $retention->description = $val->validated('description');
            $retention->type        = $val->validated('type');
            $retention->category    = Input::post('category', null);
            $retention->valid_from  = Input::post('valid_from', null);
            $retention->base_type   = Input::post('base_type', null);
            $retention->rate        = Input::post('rate', null);
            $retention->account     = Input::post('account', null);
            $retention->factor_type = Input::post('factor_type', null);
            $retention->updated_at  = time();

            if($retention->save())
            {
                Session::set_flash('success', 'Se actualizó la retención <b>'.$retention->code.' - '.$retention->description.'</b> correctamente.');
                Response::redirect('admin/formas_retenciones/editar/'.$retention_id);
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

    # SE ALMACENA LA INFORMACIÓN PARA LA VISTA
    $data['id']      = $retention_id;
    $data['classes'] = $classes;

    # SE CARGA LA VISTA
    $this->template->title   = 'Editar retención';
    $this->template->content = View::forge('admin/retenciones/editar', $data);
}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($retention_id = 0)
{
    #HELPER DE PERMISO PARA ELIMINAR
    if (!Helper_Permission::can('catalogo_retenciones', 'delete')) {
        Session::set_flash('error', 'No tienes permiso para eliminar retenciones.');
        Response::redirect('admin/formas_retenciones');
    }

    # VALIDACIÓN DE ID
    if($retention_id == 0 || !is_numeric($retention_id))
    {
        Response::redirect('admin/formas_retenciones');
    }

    # OBTIENE LA RETENCIÓN
    $retention = Model_Retention::query()
        ->where('id', $retention_id)
        ->get_one();

    if(!empty($retention))
    {
        // Si tu tabla tiene el campo deleted, realiza borrado lógico:
        // $retention->deleted = 1;
        // $retention->save();

        // Si es borrado físico:
        if($retention->delete())
        {
            Session::set_flash('success', 'Se eliminó la retención <b>'.$retention->code.' - '.$retention->description.'</b> correctamente.');
        }
        else
        {
            Session::set_flash('error', 'No se pudo eliminar la retención.');
        }
    }

    # REDIRECCIONA AL LISTADO
    Response::redirect('admin/retenciones');
}

}
