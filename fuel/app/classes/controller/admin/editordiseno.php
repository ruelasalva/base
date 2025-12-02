<?php
/**
 * CONTROLADOR DEL EDITOR DE DISEÑO - ACTION INDEX
 * MUESTRA EL LISTADO DE PLANTILLAS (THEME LAYOUTS)
 * 
 * @author  TU NOMBRE
 * @date    2025-06-27
 */
class Controller_Admin_Editordiseno extends Controller_Admin
{
    /**
     * PROCESO BEFORE PARA SEGURIDAD Y TEMPLATING
     */
    public function before()
    {
        parent::before();
        // VERIFICA LOGIN
        if (!Auth::check()) {
            Session::set_flash('error', 'Debes iniciar sesión.');
            Response::redirect('admin/login');
        }
        // CHECA PERMISOS (SUPONIENDO Helper_Permission::can())
        if (!Helper_Permission::can('editor_diseno', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver el editor de plantillas.');
            Response::redirect('admin');
        }
        // TÍTULO DE PÁGINA
        $this->template->title = 'Editor de Plantillas';
    }

    /**
     * INDEX
     * MUESTRA UNA LISTA DE PLANTILLAS GUARDADAS
     * @access public
     * @return void
     */
   public function action_index()
{
    // OBTIENE TODAS LAS PLANTILLAS ORDENADAS POR FECHA DE ACTUALIZACIÓN
    $plantillas = Model_Theme_Layout::query()
        ->order_by('updated_at', 'desc')
        ->order_by('created_at', 'desc')
        ->get();

    // SI USAS ORM, CONVIERTE A ARRAY (SI TU VISTA USA ARRAY)
    $plantillas_array = [];
    foreach ($plantillas as $p) {
        $plantillas_array[] = [
            'id'         => $p->id,
            'name'       => $p->name,
            'preview'    => $p->preview,
            'updated_at' => $p->updated_at ? date('Y-m-d H:i:s', is_numeric($p->updated_at) ? $p->updated_at : strtotime($p->updated_at)) : null,
            'created_at' => $p->created_at,
        ];
    }

    // PASA A LA VISTA
    $this->template->content = View::forge('admin/editordiseno/index', [
        'plantillas' => $plantillas_array
    ]);
}


    /**
 * AGREGA UNA NUEVA PLANTILLA VISUAL
 * MUESTRA EL FORMULARIO DE CAPTURA DE NOMBRE Y REDIRECCIONA AL EDITOR PARA DISEÑO.
 * @access public
 * @return void
 */
public function action_agregar()
{
    // VERIFICA PERMISO
    if (!Helper_Permission::can('editor_diseno', 'create')) {
        Session::set_flash('error', 'No tienes permiso para agregar plantillas.');
        Response::redirect('admin/editordiseno');
    }

    // SI ENVÍA FORMULARIO (POST)
    if (Input::method() == 'POST') {
        $val = Validation::forge();
        $val->add('name', 'Nombre de la plantilla')
            ->add_rule('required')
            ->add_rule('max_length', 80);

        if ($val->run()) {
            // CREA EL REGISTRO VACÍO (solo nombre)
            $plantilla = Model_Theme_Layout::forge([
                'name' => Input::post('name'),
                'html' => '',
                'css' => '',
                'components' => '',
                'styles' => '',
            ]);
            $plantilla->save();
            // REDIRECCIONA AL EDITOR VISUAL PARA ESA PLANTILLA
            Response::redirect('admin/editordiseno/editar/' . $plantilla->id);
        } else {
            Session::set_flash('error', 'El nombre es obligatorio y debe tener menos de 80 caracteres.');
            $this->template->set_global('name', Input::post('name'), false);
        }
    }

    // MUESTRA FORMULARIO PARA AGREGAR
    $this->template->content = View::forge('admin/editordiseno/agregar');
}


/**
 * EDITA UNA PLANTILLA VISUAL EXISTENTE USANDO EL EDITOR GRAPESJS
 * SOLO ACCESIBLE POR USUARIOS CON PERMISO 'editor_diseno' EN MODO EDIT
 * @author  [Tu nombre]
 * @date    [Fecha]
 */
public function action_editar($id = null)
{
    // VERIFICA PERMISO
    if (!Helper_Permission::can('editor_diseno', 'edit')) {
        Session::set_flash('error', 'No tienes permiso para editar plantillas.');
        Response::redirect('admin/editordiseno');
    }

     // VALIDAR ID
    if (empty($id) || !is_numeric($id)) {
        Session::set_flash('error', 'ID inválido');
        Response::redirect('admin/editordiseno');
    }

    // OBTIENE LA PLANTILLA
    $plantilla = Model_Theme_Layout::find($id);
    if (!$plantilla) {
        Session::set_flash('error', 'No se encontró la plantilla.');
        Response::redirect('admin/editordiseno');
    }

    // ENVÍA A LA VISTA (plantilla puede estar vacía)
    $this->template->content = View::forge('admin/editordiseno/editar', [
        'plantilla' => $plantilla,
    ]);
}


/**
 * infi UNA PLANTILLA VISUAL EXISTENTE USANDO EL EDITOR GRAPESJS
 * SOLO ACCESIBLE POR USUARIOS CON PERMISO 'editor_diseno' EN MODO EDIT
 * @author  [Tu nombre]
 * @date    [Fecha]
 */
/**
 * INFO
 *
 * MUESTRA LA INFORMACIÓN Y VISTA PREVIA DE LA PLANTILLA VISUAL
 * @param int $id
 */
public function action_infod($id = null)
{
    // PERMISOS
    if (!Helper_Permission::can('editor_diseno', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver plantillas.');
        Response::redirect('admin/editordiseno');
    }

    // BUSCAR PLANTILLA
    $plantilla = Model_Theme_Layout::find($id);
    if (!$plantilla) {
        Session::set_flash('error', 'No se encontró la plantilla solicitada.');
        Response::redirect('admin/editordiseno');
    }

    // ARMA ARRAY PARA LA VISTA
    $plantilla_array = [
        'id'         => $plantilla->id,
        'name'       => $plantilla->name,
        'preview'    => $plantilla->preview,
        'updated_at' => $plantilla->updated_at ? date('d/m/Y H:i', is_numeric($plantilla->updated_at) ? $plantilla->updated_at : strtotime($plantilla->updated_at)) : null,
        'created_at' => $plantilla->created_at ? date('d/m/Y H:i', is_numeric($plantilla->created_at) ? $plantilla->created_at : strtotime($plantilla->created_at)) : null,
    ];

    // PASA A LA VISTA
    $this->template->content = View::forge('admin/editordiseno/info', [
        'plantilla' => $plantilla_array
    ]);
}

/**
 * PREVIEW DE LA PLANTILLA VISUAL (RENDERIZADO ESTÁTICO)
 * Muestra sólo el HTML+CSS guardado, sin edición, sin menú, sin controles
 */
public function action_info($id = null)
{
    $plantilla = Model_Theme_Layout::find($id);
    if (!$plantilla) {
        return Response::forge('No se encontró la plantilla', 404);
    }

    // Estos datos los pasas a la vista
    $this->template->content = View::forge('admin/editordiseno/info', [
        'plantilla' => $plantilla,
        'html_preview' => $plantilla->html ?? '',
        'css_preview'  => $plantilla->css ?? '',
        // Puedes pasar más info si necesitas
    ]);
}





}
