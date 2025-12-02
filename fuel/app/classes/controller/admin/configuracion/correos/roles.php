<?php

/**
 * CONTROLADOR ADMIN_CONFIGURACION_CORREOS_ROLES
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Configuracion_Correos_Roles extends Controller_Admin
{
    public function before()
    {
        parent::before();

        if (!Auth::check()) {
            Session::set_flash('error', 'Debes iniciar sesión.');
            Response::redirect('admin/login');
        }

        if (!Helper_Permission::can('config_correos', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver roles de correo.');
            Response::redirect('admin');
        }
    }

    /**
     * INDEX
     *
     * Lista de roles configurados
     */
    public function action_index($search = '')
    {
        # VARIABLES
        $data        = array();
        $roles_info  = array();
        $per_page    = 50;

        # QUERY BASE
        $query = Model_Emails_Role::query()
        ->where('deleted', 0);   // Solo roles activos

        # SI HAY UNA BUSQUEDA
        if ($search != '') {
            $original_search = $search;
            $search = str_replace('+', ' ', rawurldecode($search));
            $search = str_replace(' ', '%', $search);

            $query->where_open()
                ->where('role', 'like', '%'.$search.'%')
                ->or_where('from_email', 'like', '%'.$search.'%')
                ->or_where('to_emails', 'like', '%'.$search.'%')
                ->where_close();
        }

        # PAGINACIÓN
        $config = array(
            'name'           => 'admin',
            'pagination_url' => Uri::current(),
            'total_items'    => $query->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
            'show_first'     => true,
            'show_last'      => true,
        );
        $pagination = Pagination::forge('roles', $config);

        # RESULTADOS
        $roles = $query->order_by('id', 'desc')
                    ->rows_limit($pagination->per_page)
                    ->rows_offset($pagination->offset)
                    ->get();

        # FORMATEO PARA VISTA
        foreach ($roles as $r) {
            $roles_info[] = array(
                'id'            => $r->id,
                'role'          => $r->role,
                'from_email'    => $r->from_email,
                'from_name'     => $r->from_name,
                'reply_to'      => ($r->reply_to_email) ? $r->reply_to_email : '-',
                'to_emails'     => $r->to_emails,
                'updated_at'    => date('d/m/Y H:i', $r->updated_at),
            );
        }

        # PASO A VISTA
        $data['roles']      = $roles_info;
        $data['search']     = str_replace('%', ' ', $search);
        $data['pagination'] = $pagination->render();

        $this->template->title   = 'Roles de Correo';
        $this->template->content = View::forge('admin/configuracion/correos/roles/index', $data, false);
    }

    /**
     * BUSCAR  
     * Redirige a la página de resultados de búsqueda
     * @param   void   
     *  @return  void
     */
    public function action_buscar()
    {
        if (Input::method() == 'POST') {
            $search = trim(Input::post('search', ''));
            $val = Validation::forge('search');
            $val->add_callable('Rules');
            $val->add_field('search', 'search', 'max_length[100]');

            if ($val->run(array('search' => $search))) {
                $search = str_replace(' ', '+', $val->validated('search'));
                Response::redirect('admin/configuracion/correos/roles/index/'.$search);
            } else {
                Response::redirect('admin/configuracion/correos/roles');
            }
        } else {
            Response::redirect('admin/configuracion/correos/roles');
        }
    }

    /**
     * AGREGAR
     * Agregar un nuevo rol de correo
     * @return void
     */
    public function action_agregar()
    {
        # VARIABLES
        $data    = array();
        $classes = array();
        $errors  = array();

        # CAMPOS
        $fields = ['role','from_email','from_name','reply_to_email','reply_to_name','to_emails'];
        foreach ($fields as $field) {
            $classes[$field] = ['form-group'=>null, 'form-control'=>null];
            $data[$field] = '';
        }

        if (Input::method() == 'POST')
        {
            $role = Model_Emails_Role::forge(array(
                'role'           => trim(Input::post('role')),
                'from_email'     => trim(Input::post('from_email')),
                'from_name'      => trim(Input::post('from_name')),
                'reply_to_email' => trim(Input::post('reply_to_email')),
                'reply_to_name'  => trim(Input::post('reply_to_name')),
                'to_emails'      => trim(Input::post('to_emails')),
                'created_at'     => time(),
                'updated_at'     => time(),
                'deleted'        => 0,   // siempre inicia como activo
            ));


            # VALIDACIONES
            if (empty($role->role)) {
                $errors['role'] = 'El nombre del rol es obligatorio.';
                $classes['role']['form-group'] = 'has-danger';
                $classes['role']['form-control'] = 'is-invalid';
            }
            if (!filter_var($role->from_email, FILTER_VALIDATE_EMAIL)) {
                $errors['from_email'] = 'Correo remitente no válido.';
                $classes['from_email']['form-group'] = 'has-danger';
                $classes['from_email']['form-control'] = 'is-invalid';
            }
            if ($role->reply_to_email != '' && !filter_var($role->reply_to_email, FILTER_VALIDATE_EMAIL)) {
                $errors['reply_to_email'] = 'Correo reply-to no válido.';
                $classes['reply_to_email']['form-group'] = 'has-danger';
                $classes['reply_to_email']['form-control'] = 'is-invalid';
            }

            # SI NO HAY ERRORES
            if (empty($errors)) {
                if ($role->save()) {
                    Session::set_flash('success','Rol creado correctamente.');
                    Response::redirect('admin/configuracion/correos/roles');
                } else {
                    Session::set_flash('error','Ocurrió un error al guardar.');
                }
            } else {
                Session::set_flash('error','Corrige los errores del formulario.');
                $data['errors'] = $errors;
            }

            # Reinyectar valores en caso de error
            foreach ($fields as $field) {
                $data[$field] = Input::post($field);
            }
        }

        $data['classes'] = $classes;

        $this->template->title   = 'Agregar Rol de Correo';
        $this->template->content = View::forge('admin/configuracion/correos/roles/agregar',$data,false);
    }


    /**
     * EDITAR  
     *  Editar un rol de correo existente
     * @param   int     $id     ID del rol a editar
     * @return  void
     */
    public function action_editar($id = 0)
    {
        # VARIABLES
        $data    = array();
        $classes = array();
        $errors  = array();

        # CAMPOS
        $fields = ['role','from_email','from_name','reply_to_email','reply_to_name','to_emails'];
        foreach ($fields as $field) {
            $classes[$field] = ['form-group'=>null, 'form-control'=>null];
        }

        # OBTENER ROL
        $role = Model_Emails_Role::find($id, array('where' => array('deleted' => 0)));
        if (!$role) {
            Session::set_flash('error','Rol no encontrado o eliminado.');
            Response::redirect('admin/configuracion/correos/roles');
        }


        if (Input::method() == 'POST')
        {
            $role->role           = trim(Input::post('role'));
            $role->from_email     = trim(Input::post('from_email'));
            $role->from_name      = trim(Input::post('from_name'));
            $role->reply_to_email = trim(Input::post('reply_to_email'));
            $role->reply_to_name  = trim(Input::post('reply_to_name'));
            $role->to_emails      = trim(Input::post('to_emails'));
            $role->updated_at     = time();

            # VALIDACIONES
            if (empty($role->role)) {
                $errors['role'] = 'El nombre del rol es obligatorio.';
                $classes['role']['form-group'] = 'has-danger';
                $classes['role']['form-control'] = 'is-invalid';
            } else {
                # Validar que no exista duplicado
                $exists = Model_Emails_Role::query()
                    ->where('role', $role->role)
                    ->where('id', '!=', $role->id)
                    ->get_one();
                if ($exists) {
                    $errors['role'] = 'Este rol ya está registrado.';
                    $classes['role']['form-group'] = 'has-danger';
                    $classes['role']['form-control'] = 'is-invalid';
                }
            }
            if (!filter_var($role->from_email, FILTER_VALIDATE_EMAIL)) {
                $errors['from_email'] = 'Correo remitente no válido.';
                $classes['from_email']['form-group'] = 'has-danger';
                $classes['from_email']['form-control'] = 'is-invalid';
            }
            if ($role->reply_to_email != '' && !filter_var($role->reply_to_email, FILTER_VALIDATE_EMAIL)) {
                $errors['reply_to_email'] = 'Correo reply-to no válido.';
                $classes['reply_to_email']['form-group'] = 'has-danger';
                $classes['reply_to_email']['form-control'] = 'is-invalid';
            }

            # GUARDAR
            if (empty($errors)) {
                if ($role->save()) {
                    Session::set_flash('success','Rol actualizado correctamente.');
                    Response::redirect('admin/configuracion/correos/roles');
                } else {
                    Session::set_flash('error','Error al actualizar el rol.');
                }
            } else {
                Session::set_flash('error','Corrige los errores.');
                $data['errors'] = $errors;
            }
        }

        # CARGAR DATOS A VISTA
        foreach ($fields as $field) { $data[$field] = $role->$field; }
        $data['id']      = $role->id;
        $data['classes'] = $classes;

        $this->template->title   = 'Editar Rol de Correo';
        $this->template->content = View::forge('admin/configuracion/correos/roles/editar',$data,false);
    }

    /**
     * INFO
     * Detalle de un rol de correo
     * @param   int     $id     ID del rol a ver
     * @return  void
     */
    public function action_info($id = 0)
    {
        $role = Model_Emails_Role::find($id, array('where' => array('deleted' => 0)));
        if (!$role) {
            Session::set_flash('error','Rol no encontrado o eliminado.');
            Response::redirect('admin/configuracion/correos/roles');
        }


        $data = array(
            'id'         => $role->id,
            'role'       => $role->role,
            'from_email' => $role->from_email,
            'from_name'  => $role->from_name,
            'reply_to'   => ($role->reply_to_email ?: '-'),
            'reply_name' => $role->reply_to_name,
            'to_emails'  => $role->to_emails,
            'created_at' => date('d/m/Y H:i',$role->created_at),
            'updated_at' => date('d/m/Y H:i',$role->updated_at),
        );

        $this->template->title   = 'Detalle Rol de Correo';
        $this->template->content = View::forge('admin/configuracion/correos/roles/info',$data,false);
    }

    /**
     * ELIMINAR
     * Elimina (lógicamente) un rol de correo
     * @param   int     $id     ID del rol a eliminar
     */
    public function action_eliminar($id = 0)
    {
        $role = Model_Emails_Role::find($id);

        if (!$role) {
            Session::set_flash('error','Rol no encontrado.');
            Response::redirect('admin/configuracion/correos/roles');
        }

        # BORRADO LÓGICO
        $role->deleted   = 1;
        $role->updated_at = time();

        if ($role->save()) {
            Session::set_flash('success','Rol eliminado correctamente.');
        } else {
            Session::set_flash('error','Error al eliminar el rol.');
        }

        Response::redirect('admin/configuracion/correos/roles');
    }




}
