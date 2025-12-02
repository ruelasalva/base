<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_MARCAS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Generales_Condiciones_Pago extends Controller_Admin
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
        # PERMISO PARA VER
        if (!Helper_Permission::can('catalogo_condiciones_pago', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver condiciones de pago.');
            Response::redirect('admin');
        }

        $data        = array();
        $terms_info  = array();
        $per_page    = 100;

        $terms = Model_Payments_Term::query();

        if($search != '')
        {
            $original_search = $search;
            $search = str_replace('+', ' ', rawurldecode($search));
            $search = str_replace(' ', '%', $search);

            $terms = $terms->where_open()
                ->where(DB::expr("CONCAT(`t0`.`name`, ' ', `t0`.`code`)"), 'like', '%'.$search.'%')
            ->where_close();
        }

        $config = array(
            'name'           => 'admin',
            'pagination_url' => Uri::current(),
            'total_items'    => $terms->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
        );

        $pagination = Pagination::forge('terms', $config);

        $terms = $terms->order_by('id', 'desc')
            ->rows_limit($pagination->per_page)
            ->rows_offset($pagination->offset)
            ->get();

        if(!empty($terms))
        {
            foreach($terms as $term)
            {
                $terms_info[] = array(
                    'id'                => $term->id,
                    'code'              => $term->code,
                    'name'              => $term->name,
                    'base_date_type'    => $term->base_date_type,
                    'installment_count' => $term->installment_count,
                    'days_tolerance'    => $term->days_tolerance,
                );
            }
        }

        $data['terms']      = $terms_info;
        $data['search']     = str_replace('%', ' ', $search);
        $data['pagination'] = $pagination->render();

        $this->template->title   = 'Condiciones de Pago';
        $this->template->content = View::forge('admin/catalogo/generales/condiciones/pago/index', $data, false);
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
            Response::redirect('admin/catalogo/generales/condiciones/pago/index/'.$search);
        }
        else
        {
            Response::redirect('admin/catalogo/generales/condiciones/pago');
        }
    }
    else
    {
        Response::redirect('admin/catalogo/generales/condiciones/pago');
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
    #PERMISO PARA CREAR
    if (!Helper_Permission::can('catalogo_condiciones_pago', 'create')) {
        Session::set_flash('error', 'No tienes permiso para crear condiciones de pago.');
        Response::redirect('admin/catalogo/generales/condiciones/pago');
    }

    # INICIALIZA VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('code', 'name', 'base_date_type', 'start_offset_days', 'days_tolerance', 'installment_count');

    foreach($fields as $field)
    {
        $classes[$field] = array (
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # POST
    if(Input::method() == 'POST')
    {
        $val = Validation::forge('payment_term');
        $val->add_callable('Rules');
        $val->add_field('code',              'código',             'required|min_length[1]|max_length[16]');
        $val->add_field('name',              'nombre',             'required|min_length[1]|max_length[128]');
        $val->add_field('base_date_type',    'tipo base',          'max_length[32]');
        $val->add_field('start_offset_days', 'días de inicio',     'valid_string[numeric]');
        $val->add_field('days_tolerance',    'días de tolerancia', 'valid_string[numeric]');
        $val->add_field('installment_count', 'parcialidades',      'valid_string[numeric]');

        if($val->run())
        {
            $term = new Model_Payments_Term(array(
                'code'              => $val->validated('code'),
                'name'              => $val->validated('name'),
                'base_date_type'    => $val->validated('base_date_type'),
                'start_offset_days' => $val->validated('start_offset_days'),
                'days_tolerance'    => $val->validated('days_tolerance'),
                'installment_count' => $val->validated('installment_count'),
                'created_at'        => time(),
                'updated_at'        => time(),
            ));

            if($term->save())
            {
                Session::set_flash('success', 'Se agregó la condición de pago <b>'.$val->validated('name').'</b> correctamente.');
                Response::redirect('admin/catalogo/generales/condiciones/pago');
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

    $this->template->title   = 'Agregar condición de pago';
    $this->template->content = View::forge('admin/catalogo/generales/condiciones/pago/agregar', $data);
}



	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($term_id = 0)
{
    # PERMISO PARA VER
    if (!Helper_Permission::can('catalogo_condiciones_pago', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver condiciones de pago.');
        Response::redirect('admin/catalogo/generales/condiciones/pago');
    }

    # VALIDACIÓN DE ID
    if($term_id == 0 || !is_numeric($term_id))
    {
        Response::redirect('admin/catalogo/generales/condiciones/pago');
    }

    # INICIALIZA VARIABLES
    $data = array();

    # BUSCA LA CONDICIÓN DE PAGO
    $term = Model_Payments_Term::query()
        ->where('id', $term_id)
        ->get_one();

    if(!empty($term))
    {
        $data['id']                = $term->id;
        $data['code']              = $term->code;
        $data['name']              = $term->name;
        $data['base_date_type']    = $term->base_date_type;
        $data['start_offset_days'] = $term->start_offset_days;
        $data['days_tolerance']    = $term->days_tolerance;
        $data['installment_count'] = $term->installment_count;
        $data['created_at']        = $term->created_at;
        $data['updated_at']        = $term->updated_at;
    }
    else
    {
        Response::redirect('admin/catalogo/generales/condiciones/pago');
    }

    # CARGA LA VISTA
    $this->template->title   = 'Información de la condición de pago';
    $this->template->content = View::forge('admin/catalogo/generales/condiciones/pago/info', $data);
}



	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($term_id = 0)
{
    # PERMISO PARA EDITAR
    if (!Helper_Permission::can('catalogo_condiciones_pago', 'edit')) {
        Session::set_flash('error', 'No tienes permiso para editar condiciones de pago.');
        Response::redirect('admin/catalogo/generales/condiciones/pago');
    }

    # VALIDACIÓN DE ID
    if($term_id == 0 || !is_numeric($term_id))
    {
        Response::redirect('admin/catalogo/generales/condiciones/pago');
    }

    # INICIALIZA VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('code', 'name', 'base_date_type', 'start_offset_days', 'days_tolerance', 'installment_count');

    foreach($fields as $field)
    {
        $classes[$field] = array (
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # BUSCA EL REGISTRO
    $term = Model_Payments_Term::query()
        ->where('id', $term_id)
        ->get_one();

    if(!empty($term))
    {
        if (Input::method() != 'POST') {
            $data['code']              = $term->code;
            $data['name']              = $term->name;
            $data['base_date_type']    = $term->base_date_type;
            $data['start_offset_days'] = $term->start_offset_days;
            $data['days_tolerance']    = $term->days_tolerance;
            $data['installment_count'] = $term->installment_count;
        }
    }
    else
    {
        Response::redirect('admin/catalogo/generales/condiciones/pago');
    }

    # POST
    if(Input::method() == 'POST')
    {
        $val = Validation::forge('payment_term');
        $val->add_callable('Rules');
        $val->add_field('code',              'código',             'required|min_length[1]|max_length[16]');
        $val->add_field('name',              'nombre',             'required|min_length[1]|max_length[128]');
        $val->add_field('base_date_type',    'tipo base',          'max_length[32]');
        $val->add_field('start_offset_days', 'días de inicio',     'valid_string[numeric]');
        $val->add_field('days_tolerance',    'días de tolerancia', 'valid_string[numeric]');
        $val->add_field('installment_count', 'parcialidades',      'valid_string[numeric]');

        if($val->run())
        {
            $term->code              = $val->validated('code');
            $term->name              = $val->validated('name');
            $term->base_date_type    = $val->validated('base_date_type');
            $term->start_offset_days = $val->validated('start_offset_days');
            $term->days_tolerance    = $val->validated('days_tolerance');
            $term->installment_count = $val->validated('installment_count');
            $term->updated_at        = time();

            if($term->save())
            {
                Session::set_flash('success', 'Se actualizó la condición de pago <b>'.$term->name.'</b> correctamente.');
                Response::redirect('admin/catalogo/generales/condiciones/pago/editar/'.$term_id);
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

    $data['id']      = $term_id;
    $data['classes'] = $classes;

    $this->template->title   = 'Editar condición de pago';
    $this->template->content = View::forge('admin/catalogo/generales/condiciones/pago/editar', $data);
}



	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($term_id = 0)
{
    # PERMISO PARA ELIMINAR
    if (!Helper_Permission::can('catalogo_condiciones_pago', 'delete')) {
        Session::set_flash('error', 'No tienes permiso para eliminar condiciones de pago.');
        Response::redirect('admin/catalogo/generales/condiciones/pago');
    }

    # VALIDACIÓN DE ID
    if($term_id == 0 || !is_numeric($term_id))
    {
        Response::redirect('admin/catalogo/generales/condiciones/pago');
    }

    # BUSCA EL REGISTRO
    $term = Model_Payments_Term::query()
        ->where('id', $term_id)
        ->get_one();

    if(!empty($term))
    {
        if($term->delete())
        {
            Session::set_flash('success', 'Se eliminó la condición de pago <b>'.$term->name.'</b> correctamente.');
        }
        else
        {
            Session::set_flash('error', 'No se pudo eliminar la condición de pago.');
        }
    }

    Response::redirect('admin/catalogo/generales/condiciones/pago');
}


}
