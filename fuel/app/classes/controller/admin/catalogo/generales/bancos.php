<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_MARCAS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Generales_Bancos extends Controller_Admin
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
    if (!Helper_Permission::can('catalogo_bancos', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver bancos.');
        Response::redirect('admin');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data    = array();
    $banks_info = array();
    $per_page = 100;

    # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
    $banks = Model_Bank::query();

    # SI HAY UNA BUSQUEDA
    if($search != '')
    {
        $original_search = $search;
        $search = str_replace('+', ' ', rawurldecode($search));
        $search = str_replace(' ', '%', $search);

        # BÚSQUEDA SOLO POR NOMBRE
        $banks = $banks->where(DB::expr("CONCAT(`t0`.`name`)"), 'like', '%'.$search.'%');
    }

    # SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
    $config = array(
        'name'           => 'admin',
        'pagination_url' => Uri::current(),
        'total_items'    => $banks->count(),
        'per_page'       => $per_page,
        'uri_segment'    => 'pagina',
    );

    $pagination = Pagination::forge('banks', $config);

    $banks = $banks->order_by('id', 'desc')
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

    if(!empty($banks))
    {
        foreach($banks as $bank)
        {
            $banks_info[] = array(
                'id'   => $bank->id,
                'name' => $bank->name,
                'created_at' => $bank->created_at,
                'updated_at' => $bank->updated_at,
            );
        }
    }

    $data['banks']      = $banks_info;
    $data['search']     = str_replace('%', ' ', $search);
    $data['pagination'] = $pagination->render();

    $this->template->title   = 'Bancos';
    $this->template->content = View::forge('admin/catalogo/generales/bancos/index', $data, false);
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
            Response::redirect('admin/catalogo/generales/bancos/index/'.$search);
        }
        else
        {
            Response::redirect('admin/catalogo/generales/bancos');
        }
    }
    else
    {
        Response::redirect('admin/catalogo/generales/bancos');
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
    if (!Helper_Permission::can('catalogo_bancos', 'create')) {
        Session::set_flash('error', 'No tienes permiso para crear bancos.');
        Response::redirect('admin/catalogo/generales/bancos');	
    }

    # SE INICIALIZAN LAS VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('name');

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
        $val = Validation::forge('bank');
        $val->add_callable('Rules');
        $val->add_field('name', 'nombre', 'required|min_length[1]|max_length[64]');

        # SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
        if($val->run())
        {
            # CREA EL MODELO CON LA INFORMACION
            $bank = new Model_Bank(array(
                'name'       => $val->validated('name'),
                'created_at' => time(),
                'updated_at' => time(),
            ));

            # SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
            if($bank->save())
            {
                # SE ESTABLECE EL MENSAJE DE EXITO
                Session::set_flash('success', 'Se agregó el banco <b>'.$val->validated('name').'</b> correctamente.');

                # SE REDIRECCIONA AL USUARIO
                Response::redirect('admin/catalogo/generales/bancos');
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
    $this->template->title   = 'Agregar banco';
    $this->template->content = View::forge('admin/catalogo/generales/bancos/agregar', $data);
}



	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($bank_id = 0)
{
    #HELPER DE PERMISO PARA VER
    if (!Helper_Permission::can('catalogo_bancos', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver bancos.');
        Response::redirect('admin');
    }

    # SI NO SE RECIBE UN ID O NO ES UN NUMERO
    if($bank_id == 0 || !is_numeric($bank_id))
    {
        Response::redirect('admin/catalogo/generales/bancos');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data = array();

    # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
    $bank = Model_Bank::query()
        ->where('id', $bank_id)
        ->get_one();

    # SI SE OBTIENE INFORMACION
    if(!empty($bank))
    {
        $data['id']         = $bank_id;
        $data['name']       = $bank->name;
        $data['created_at'] = $bank->created_at;
        $data['updated_at'] = $bank->updated_at;
    }
    else
    {
        Response::redirect('admin/catalogo/generales/bancos');
    }

    # SE CARGA LA VISTA
    $this->template->title   = 'Información del banco';
    $this->template->content = View::forge('admin/catalogo/generales/bancos/info', $data);
}




	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($bank_id = 0)
{
    #HELPER DE PERMISO PARA EDITAR
    if (!Helper_Permission::can('catalogo_bancos', 'edit')) {
        Session::set_flash('error', 'No tienes permiso para editar bancos.');
        Response::redirect('admin/catalogo/generales/bancos');
    }

    # SI NO SE RECIBE UN ID O NO ES UN NUMERO
    if($bank_id == 0 || !is_numeric($bank_id))
    {
        Response::redirect('admin/catalogo/generales/bancos');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('name');

    foreach($fields as $field)
    {
        $classes[$field] = array (
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
    $bank = Model_Bank::query()
        ->where('id', $bank_id)
        ->get_one();

    if(!empty($bank))
    {
        # CARGA DATOS ORIGINALES PARA LA VISTA
        $data['name'] = $bank->name;
    }
    else
    {
        Response::redirect('admin/catalogo/generales/bancos');
    }

    # SI SE UTILIZO EL METODO POST
    if(Input::method() == 'POST')
    {
        $val = Validation::forge('bank');
        $val->add_callable('Rules');
        $val->add_field('name', 'nombre', 'required|min_length[1]|max_length[64]');

        if($val->run())
        {
            # ACTUALIZA LOS CAMPOS
            $bank->name = $val->validated('name');
            $bank->updated_at = time();

            if($bank->save())
            {
                Session::set_flash('success', 'Se actualizó la información del banco <b>'.$bank->name.'</b> correctamente.');
                Response::redirect('admin/catalogo/generales/bancos/editar/'.$bank_id);
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
    $data['id']      = $bank_id;
    $data['classes'] = $classes;

    # SE CARGA LA VISTA
    $this->template->title   = 'Editar banco';
    $this->template->content = View::forge('admin/catalogo/generales/bancos/editar', $data);
}



	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($bank_id = 0)
{
    #HELPER DE PERMISO PARA ELIMINAR
    if (!Helper_Permission::can('catalogo_bancos', 'delete')) {
        Session::set_flash('error', 'No tienes permiso para eliminar bancos.');
        Response::redirect('admin/catalogo/generales/bancos');
    }

    # SI NO SE RECIBE UN ID O NO ES UN NUMERO
    if($bank_id == 0 || !is_numeric($bank_id))
    {
        Response::redirect('admin/catalogo/generales/bancos');
    }

    # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
    $bank = Model_Bank::query()
        ->where('id', $bank_id)
        ->get_one();

    # SI SE OBTIENE INFORMACION
    if(!empty($bank))
    {
        // Si después quieres verificar relaciones (por ejemplo, cuentas bancarias), hazlo aquí.

        // Eliminación física (elimina el registro realmente)
        if($bank->delete())
        {
            Session::set_flash('success', 'Se eliminó el banco <b>'.$bank->name.'</b> correctamente.');
        }
        else
        {
            Session::set_flash('error', 'No se pudo eliminar el banco.');
        }
    }

    # SE REDIRECCIONA AL USUARIO
    Response::redirect('admin/catalogo/generales/bancos');
}


}
