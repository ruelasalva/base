<?php

/**
 * CONTROLADOR ADMIN_CONFIGURACION_CORREOS_TEMPLATES
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Configuracion_Correos_Templates extends Controller_Admin
{
    public function before()
    {
        parent::before();

        if (!Auth::check()) {
            Session::set_flash('error', 'Debes iniciar sesión.');
            Response::redirect('admin/login');
        }

        if (!Helper_Permission::can('config_correos', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver plantillas de correo.');
            Response::redirect('admin');
        }
    }

    /**
     * INDEX
     *
     * Lista de plantillas configuradas
     */
    public function action_index($search = '')
    {
        # VARIABLES
        $data            = array();
        $templates_info  = array();
        $per_page        = 50;

        # QUERY BASE (solo activos)
        $query = Model_Emails_Template::query()
            ->where('deleted', 0);

        # FILTRO DE BÚSQUEDA
        if ($search != '') {
            $original_search = $search;
            $search = str_replace('+', ' ', rawurldecode($search));
            $search = str_replace(' ', '%', $search);

            $query->where_open()
                ->where('code', 'like', '%'.$search.'%')
                ->or_where('role', 'like', '%'.$search.'%')
                ->or_where('subject', 'like', '%'.$search.'%')
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
        $pagination = Pagination::forge('templates', $config);

        # RESULTADOS
        $templates = $query->order_by('id', 'desc')
                        ->rows_limit($pagination->per_page)
                        ->rows_offset($pagination->offset)
                        ->get();

        foreach ($templates as $t) {
            $templates_info[] = array(
                'id'        => $t->id,
                'code'      => $t->code,
                'role'      => $t->role,
                'subject'   => $t->subject,
                'view'      => $t->view,
                'updated_at'=> date('d/m/Y H:i', $t->updated_at),
            );
        }

        $data['templates']  = $templates_info;
        $data['search']     = str_replace('%', ' ', $search);
        $data['pagination'] = $pagination->render();

        $this->template->title   = 'Plantillas de Correo';
        $this->template->content = View::forge('admin/configuracion/correos/templates/index',$data,false);
    }


    /**
     * BUSCAR
     * Procesa el formulario de búsqueda y redirige a la URL con el término buscado
     * @param none
     * @return none
     * @uses  Response::redirect
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
                Response::redirect('admin/configuracion/correos/templates/index/'.$search);
            } else {
                Response::redirect('admin/configuracion/correos/templates');
            }
        } else {
            Response::redirect('admin/configuracion/correos/templates');
        }
    }

    /**
     * AGREGAR
     * Agregar una nueva plantilla de correo
     * @param none
     * @return none
     * @uses  Model_Emails_Template
     */
    public function action_agregar()
    {
        $data    = array();
        $classes = array();
        $errors  = array();

        $fields = ['code','role','subject','view'];
        foreach ($fields as $f) {
            $classes[$f] = ['form-group'=>null,'form-control'=>null];
            $data[$f] = '';
        }

        if (Input::method() == 'POST')
        {
            $template = Model_Emails_Template::forge(array(
                'code'       => strtolower(trim(Input::post('code'))),
                'role'       => trim(Input::post('role')),
                'subject'    => trim(Input::post('subject')),
                'view'       => trim(Input::post('view')),
                'created_at' => time(),
                'updated_at' => time(),
                'deleted'    => 0,
            ));

            # VALIDACIONES
            if (empty($template->code)) {
                $errors['code'] = 'El código es obligatorio.';
                $classes['code']['form-group'] = 'has-danger';
                $classes['code']['form-control'] = 'is-invalid';
            } else {
                $exists = Model_Emails_Template::query()
                    ->where('code',$template->code)
                    ->where('deleted',0)
                    ->get_one();
                if ($exists) {
                    $errors['code'] = 'Este código ya existe.';
                    $classes['code']['form-group'] = 'has-danger';
                    $classes['code']['form-control'] = 'is-invalid';
                }
            }
            if (empty($template->role)) {
                $errors['role'] = 'El rol asociado es obligatorio.';
                $classes['role']['form-group'] = 'has-danger';
                $classes['role']['form-control'] = 'is-invalid';
            }
            if (empty($template->subject)) {
                $errors['subject'] = 'El asunto es obligatorio.';
                $classes['subject']['form-group'] = 'has-danger';
                $classes['subject']['form-control'] = 'is-invalid';
            }
            if (empty($template->view)) {
                $errors['view'] = 'La vista es obligatoria.';
                $classes['view']['form-group'] = 'has-danger';
                $classes['view']['form-control'] = 'is-invalid';
            }

            # GUARDAR
            if (empty($errors)) {
                if ($template->save()) {
                    Session::set_flash('success','Plantilla creada correctamente.');
                    Response::redirect('admin/configuracion/correos/templates');
                } else {
                    Session::set_flash('error','Error al guardar la plantilla.');
                }
            } else {
                Session::set_flash('error','Corrige los errores del formulario.');
                $data['errors'] = $errors;
            }

            foreach ($fields as $f) {
                $data[$f] = Input::post($f);
            }
        }

        $data['classes'] = $classes;

        $this->template->title   = 'Agregar Plantilla de Correo';
        $this->template->content = View::forge('admin/configuracion/correos/templates/agregar',$data,false);
    }


    /**
     * INFO
     * Muestra el detalle de una plantilla
     * @param int $id ID de la plantilla
     * @return none
     */
    public function action_info($id = 0)
    {
        $tpl = Model_Emails_Template::query()
            ->where('id',$id)
            ->where('deleted',0)
            ->get_one();

        if (!$tpl) {
            Session::set_flash('error','Plantilla no encontrada o eliminada.');
            Response::redirect('admin/configuracion/correos/templates');
        }

        $data = array(
            'id'         => $tpl->id,
            'code'       => $tpl->code,
            'role'       => $tpl->role,
            'subject'    => $tpl->subject,
            'view'       => $tpl->view,
            'created_at' => date('d/m/Y H:i',$tpl->created_at),
            'updated_at' => date('d/m/Y H:i',$tpl->updated_at),
        );

        $this->template->title   = 'Detalle Plantilla de Correo';
        $this->template->content = View::forge('admin/configuracion/correos/templates/info',$data,false);
    }


    /**
     * EDITAR  
     * Editar una plantilla de correo
     * @param int $id ID de la plantilla
     * @return none
     */
    public function action_editar($id = 0)
    {
        $data    = array();
        $classes = array();
        $errors  = array();

        $fields = ['code','role','subject','view'];
        foreach ($fields as $f) {
            $classes[$f] = ['form-group'=>null,'form-control'=>null];
        }

        # OBTENER PLANTILLA ACTIVA
        $tpl = Model_Emails_Template::query()
            ->where('id',$id)
            ->where('deleted',0)
            ->get_one();

        if (!$tpl) {
            Session::set_flash('error','Plantilla no encontrada o eliminada.');
            Response::redirect('admin/configuracion/correos/templates');
        }

        if (Input::method() == 'POST')
        {
            $tpl->code       = strtolower(trim(Input::post('code')));
            $tpl->role       = trim(Input::post('role'));
            $tpl->subject    = trim(Input::post('subject'));
            $tpl->view       = trim(Input::post('view'));
            $tpl->updated_at = time();

            # VALIDACIONES
            if (empty($tpl->code)) {
                $errors['code'] = 'El código es obligatorio.';
                $classes['code']['form-group'] = 'has-danger';
                $classes['code']['form-control'] = 'is-invalid';
            } else {
                $exists = Model_Emails_Template::query()
                    ->where('code',$tpl->code)
                    ->where('id','!=',$tpl->id)
                    ->where('deleted',0)
                    ->get_one();
                if ($exists) {
                    $errors['code'] = 'Este código ya existe en otra plantilla.';
                    $classes['code']['form-group'] = 'has-danger';
                    $classes['code']['form-control'] = 'is-invalid';
                }
            }
            if (empty($tpl->role)) {
                $errors['role'] = 'El rol asociado es obligatorio.';
                $classes['role']['form-group'] = 'has-danger';
                $classes['role']['form-control'] = 'is-invalid';
            }
            if (empty($tpl->subject)) {
                $errors['subject'] = 'El asunto es obligatorio.';
                $classes['subject']['form-group'] = 'has-danger';
                $classes['subject']['form-control'] = 'is-invalid';
            }
            if (empty($tpl->view)) {
                $errors['view'] = 'La vista es obligatoria.';
                $classes['view']['form-group'] = 'has-danger';
                $classes['view']['form-control'] = 'is-invalid';
            }

            # GUARDAR
            if (empty($errors)) {
                if ($tpl->save()) {
                    Session::set_flash('success','Plantilla actualizada correctamente.');
                    Response::redirect('admin/configuracion/correos/templates');
                } else {
                    Session::set_flash('error','Error al actualizar la plantilla.');
                }
            } else {
                Session::set_flash('error','Corrige los errores.');
                $data['errors'] = $errors;
            }
        }

        # CARGAR DATOS PARA LA VISTA
        foreach ($fields as $f) { $data[$f] = $tpl->$f; }
        $data['id']      = $tpl->id;
        $data['classes'] = $classes;

        $this->template->title   = 'Editar Plantilla de Correo';
        $this->template->content = View::forge('admin/configuracion/correos/templates/editar',$data,false);
    }


    /**
     * ELIMINAR
     * Elimina (soft delete) una plantilla de correo
     * @param int $id ID de la plantilla
     */
    public function action_eliminar($id = 0)
    {
        $tpl = Model_Emails_Template::find($id);

        if (!$tpl || $tpl->deleted == 1) {
            Session::set_flash('error','Plantilla no encontrada o ya eliminada.');
            Response::redirect('admin/configuracion/correos/templates');
        }

        $tpl->deleted    = 1;
        $tpl->updated_at = time();

        if ($tpl->save()) {
            Session::set_flash('success','Plantilla eliminada correctamente.');
        } else {
            Session::set_flash('error','Error al eliminar la plantilla.');
        }

        Response::redirect('admin/configuracion/correos/templates');
    }

    public function action_editor($id = 0)
{
    $template = Model_Emails_Template::find($id);

    if (!$template) {
        Session::set_flash('error', 'Plantilla no encontrada.');
        Response::redirect('admin/configuracion/correos/templates');
    }

    $content = '';

    // Si existe archivo físico lo cargamos
    if (!empty($template->view)) {
        $view_path = APPPATH . 'views/' . $template->view . '.php';
        if (file_exists($view_path)) {
            $content = file_get_contents($view_path);
        }
    }

    // Si no hubo archivo, usamos el campo "content" de BD
    if (empty($content) && !empty($template->content)) {
        $content = $template->content;
    }

    $data = array(
        'id'      => $template->id,
        'code'    => $template->code,
        'content' => $content,
    );

    $this->template->title   = 'Editar contenido de plantilla';
    $this->template->content = View::forge('admin/configuracion/correos/templates/editor', $data, false);
}


public function post_guardar_editor($id = 0)
{
    $template = Model_Emails_Template::find($id);

    if (!$template) {
        return $this->response(['msg' => 'error', 'detail' => 'Plantilla no encontrada']);
    }

    $nuevoContenido = Input::post('content');

    try {
        if (!empty($template->view)) {
            $view_path = APPPATH . 'views/' . $template->view . '.php';
            file_put_contents($view_path, $nuevoContenido);
        } else {
            $template->content = $nuevoContenido;
            $template->updated_at = time();
            $template->save();
        }

        return $this->response(['msg' => 'ok']);
    } catch (\Exception $e) {
        \Log::error('[Correo][Editor] Error al guardar: '.$e->getMessage());
        return $this->response(['msg' => 'error']);
    }
}




}
