<?php
/**
 * CONTROLADOR CONSENTIMIENTOS
 *
 * ADMINISTRA LOS CONSENTIMIENTOS DE LOS USUARIOS
 */
class Controller_Admin_Legal_Consentimientos extends Controller_Admin
{
    
    /**
     * INDEX
     *
     * MUESTRA UNA LISTADO DE CONSENTIMIENTOS
     *
     * @access  public
     * @return  Void
     */
    public function action_index($search = '')
    {
        # SE INICIALIZAN LAS VARIABLES
        $data           = [];
        $consents_info  = [];
        $per_page       = 50;

        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
        $consents = Model_User_Consent::query()
            ->related('user')
            ->related('document')
            ->where('id','>=',0);

        # SI HAY UNA BUSQUEDA
        if ($search != '')
        {
            # SE ALMACENA LA BUSQUEDA ORIGINAL
            $original_search = $search;

            # SE LIMPIA LA CADENA DE BUSQUEDA
            $search = str_replace('+', ' ', rawurldecode($search));

            # SE REEMPLAZA LOS ESPACIOS POR PORCENTAJES
            $search = str_replace(' ', '%', $search);

            # SE AGREGA LA CLAUSULA
            $consents = $consents->where(
                DB::expr("CONCAT(`t0`.`shortcode`, ' ', `t0`.`channel`, ' ', `t1`.`username`, ' ', `t1`.`email`, ' ', `t2`.`title`)"),
                'like',
                '%'.$search.'%'
            );
        }

        # SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
        $config = [
            'name'           => 'admin',
            'pagination_url' => Uri::current(),
            'total_items'    => $consents->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
            'show_first'     => true,
            'show_last'      => true,
        ];

        # SE CREA LA INSTANCIA DE LA PAGINACION
        $pagination = Pagination::forge('consents', $config);

        # SE EJECUTA EL QUERY
        $consents = $consents->order_by('accepted_at', 'desc')
            ->rows_limit($pagination->per_page)
            ->rows_offset($pagination->offset)
            ->get();

        # SI SE OBTIENE INFORMACION
        if (!empty($consents))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach ($consents as $c)
            {
                # SE ALMACENA LA INFORMACION
                $consents_info[] = [
                    'id'        => $c->id,
                    'user'      => $c->user ? $c->user->username : 'N/A',
                    'email'     => $c->user ? $c->user->email : 'Sin correo',
                    'document'  => $c->document ? $c->document->title : $c->shortcode,
                    'estado'    => $c->accepted == 0 ? 'Aceptado' : 'Rechazado',
                    'channel'   => ucfirst($c->channel),
                    'ip'        => $c->ip_address,
                    'fecha'     => $c->accepted_at ? date('d/m/Y - H:i', $c->accepted_at) : 'N/A',
                ];
            }
        }

        # SE ALMACENA LA INFORMACION PARA LA VISTA
        $data['consents']   = $consents_info;
        $data['search']     = str_replace('%', ' ', $search);
        $data['pagination'] = $pagination->render();

        # SE CARGA LA VISTA
        $this->template->title   = 'Consentimientos';
        $this->template->content = View::forge('admin/legal/consentimientos/index', $data, false);
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
        # SI SE UTILIZÓ EL MÉTODO POST
        if (Input::method() == 'POST')
        {
            # SE OBTIENEN LOS VALORES
            $data = [
                'search' => ($_POST['search'] != '') ? $_POST['search'] : '',
            ];

            # SE CREA LA VALIDACIÓN DE LOS CAMPOS
            $val = Validation::forge('search');
            $val->add_callable('Rules');
            $val->add_field('search', 'search', 'max_length[100]');

            # SI NO HAY PROBLEMA CON LA VALIDACIÓN
            if ($val->run($data))
            {
                # SE REEMPLAZAN ALGUNOS CARACTERES
                $search = str_replace(' ', '+', $val->validated('search'));
                $search = str_replace('*', '', $search);

                # SE ALMACENA LA CADENA DE BÚSQUEDA
                $search = ($val->validated('search') != '') ? $search : '';

                # SE REDIRECCIONA A BUSCAR
                Response::redirect('admin/legal/consentimientos/index/'.$search);
            }
            else
            {
                # SI FALLA LA VALIDACIÓN REDIRECCIONA AL LISTADO
                Response::redirect('admin/legal/consentimientos');
            }
        }
        else
        {
            # SI NO ES POST REDIRECCIONA AL LISTADO
            Response::redirect('admin/legal/consentimientos');
        }
    }



    /**
     * INFO
     *
     * MUESTRA EL DETALLE DE UN CONSENTIMIENTO
     *
     * @access  public
     * @param   int  $id
     * @return  Void
     */
    public function action_info($id = null)
    {
        # SI NO SE ENVÍA ID
        is_null($id) and Response::redirect('admin/legal/consentimientos');

        # SE BUSCA EL CONSENTIMIENTO
        $consent = Model_User_Consent::find($id, [
            'related' => ['user', 'document']
        ]);

        # SI NO EXISTE
        if (!$consent) {
            Session::set_flash('error', 'Consentimiento no encontrado.');
            Response::redirect('admin/legal/consentimientos');
        }

        # SE MAPEAN LOS DATOS PARA LA VISTA
        $data['consent'] = [
            'id'        => $consent->id,
            'usuario'   => $consent->user ? $consent->user->username : 'N/A',
            'email'     => $consent->user ? $consent->user->email : 'Sin correo',
            'documento' => $consent->document ? $consent->document->title : $consent->shortcode,
            'shortcode' => $consent->document ? $consent->document->shortcode : 'N/A',
            'estado'    => $consent->accepted == 0 ? 'Aceptado' : 'Rechazado',
            'canal'     => ucfirst($consent->channel),
            'ip'        => $consent->ip_address,
            'user_agent'=> $consent->user_agent,
            'extra'     => $consent->extra ? json_encode(json_decode($consent->extra), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 'N/A',
            'fecha'     => $consent->accepted_at ? date('d/m/Y - H:i', $consent->accepted_at) : 'N/A',
        ];

        # CARGAR VISTA
        $this->template->title   = 'Detalle Consentimiento';
        $this->template->content = View::forge('admin/legal/consentimientos/info', $data, false);
    }


    /**
     * AGREGAR
     *
     * REGISTRA UN NUEVO CONSENTIMIENTO MANUAL
     *
     * @access  public
     * @return  Void
     */
    public function action_agregar()
    {
        # SE INICIALIZAN VARIABLES
        $data = [];

        # SI SE UTILIZÓ POST
        if (Input::method() == 'POST')
        {
            $consent = Model_User_Consent::forge([
                'user_id'     => Input::post('user_id'),
                'document_id' => Input::post('document_id'),
                'shortcode'   => Input::post('shortcode'),
                'accepted'    => Input::post('accepted', 0),
                'channel'     => Input::post('channel', 'web'),
                'ip_address'  => Input::real_ip(),
                'user_agent'  => Input::user_agent(),
                'extra'       => json_encode(Input::post('extra', [])),
                'accepted_at' => time(),
                'created_at'  => time(),
                'updated_at'  => time(),
            ]);

            if ($consent->save()) {
                \Log::info("[CONSENTS] Consentimiento creado ID={$consent->id}");
                Session::set_flash('success','Consentimiento registrado correctamente.');
                Response::redirect('admin/legal/consentimientos');
            } else {
                Session::set_flash('error','No se pudo guardar el consentimiento.');
            }
        }

        # CARGA LA VISTA
        $this->template->title   = 'Consentimientos - Nuevo';
        $this->template->content = View::forge('admin/legal/consentimientos/agregar', $data, false);
    }


    /**
     * ELIMINAR CONSENTIMIENTO (LÓGICO)
     */
    public function action_eliminar($id = null)
    {
        $consent = Model_User_Consent::find($id);

        if ($consent)
        {
            $consent->delete();
            \Session::set_flash('success','Consentimiento eliminado.');
        }
        else
        {
            \Session::set_flash('error','Consentimiento no encontrado.');
        }

        \Response::redirect('admin/legal/consentimientos');
    }



    /**
     * DETALLE DE CONSENTIMIENTOS PENDIENTES DE UN USUARIO
     */
    public function action_infopendiente($user_id = null)
    {
        if (!$user_id) {
            \Session::set_flash('error','Usuario no especificado.');
            \Response::redirect('admin/legal/consentimientos');
        }

        // Usuario
        $user = Model_User::find($user_id);
        if (!$user) {
            \Session::set_flash('error','Usuario no encontrado.');
            \Response::redirect('admin/legal/consentimientos');
        }

        // Documentos requeridos activos
        $docs = Model_Legal_Document::query()
            ->where('active', 0)
            ->where('required', 1)
            ->get();

        $pendientes = [];

        foreach ($docs as $doc) {
            $consent = Model_User_Consent::query()
        ->where('user_id', $user_id)
        ->where('document_id', $doc->id)
        ->get_one();

    if (!$consent || $consent->accepted == 1) { // no existe o rechazado → pendiente
        $pendientes[] = $doc;
    }
        }

        $data['user']       = $user;
        $data['pendientes'] = $pendientes;

        $this->template->title   = 'Gestión - Consentimientos Pendientes';
        $this->template->content = View::forge('admin/legal/consentimientos/infopendiente', $data);
}


}
