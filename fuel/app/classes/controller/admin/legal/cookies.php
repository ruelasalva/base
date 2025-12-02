<?php
/**
 * CONTROLADOR COOKIES
 *
 * Maneja las preferencias de cookies de usuarios y anónimos.
 */
class Controller_Admin_Legal_Cookies extends Controller_Admin
{

     /**
     * INDEX
     *  LISTA LAS PREFERENCIAS DE COOKIES DE USUARIOS Y ANÓNIMOS
     */
    public function action_index($search = '')
    {
        $data = [];
        $prefs_info = [];
        $per_page = 50;

        # USUARIOS LOGUEADOS
        $prefs = Model_User_Cookies_Preference::query()->related('user');

        if ($search != '') {
            $search = str_replace('+', ' ', rawurldecode($search));
            $search = str_replace(' ', '%', $search);
            $prefs->where(DB::expr("CONCAT(`t1`.`username`, ' ', `t1`.`email`)"), 'like', '%'.$search.'%');
        }

        $config = [
            'name'           => 'admin',
            'pagination_url' => Uri::current(),
            'total_items'    => $prefs->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
            'show_first'     => true,
            'show_last'      => true,
        ];

        $pagination = Pagination::forge('prefs', $config);

        $prefs = $prefs->order_by('updated_at','desc')
            ->rows_limit($pagination->per_page)
            ->rows_offset($pagination->offset)
            ->get();

        foreach ($prefs as $pref) {
            $prefs_info[] = [
                'id'        => $pref->id,
                'user'      => $pref->user ? $pref->user->username.' ('.$pref->user->email.')' : 'Sin usuario',
                'analytics' => $pref->analytics == 0 ? 'Acepta' : 'Rechaza',
                'marketing' => $pref->marketing == 0 ? 'Acepta' : 'Rechaza',
                'personal'  => $pref->personalization == 0 ? 'Acepta' : 'Rechaza',
                'updated'   => $pref->updated_at ? date('d/m/Y H:i',$pref->updated_at) : 'N/A',
                'type'      => 'user',
            ];
        }

        # ANÓNIMOS
        $anon_prefs = Model_Anonymous_Cookies_Accept::query();

        if ($search != '') {
            $anon_prefs->where('token','like','%'.$search.'%');
        }

        $anon_results = $anon_prefs->order_by('updated_at','desc')->get();

        foreach ($anon_results as $pref) {
            $prefs_info[] = [
                'id'        => $pref->id,
                'user'      => 'Anónimo ('.$pref->token.')',
                'analytics' => $pref->analytics == 0 ? 'Acepta' : 'Rechaza',
                'marketing' => $pref->marketing == 0 ? 'Acepta' : 'Rechaza',
                'personal'  => $pref->personalization == 0 ? 'Acepta' : 'Rechaza',
                'updated'   => $pref->updated_at ? date('d/m/Y H:i',$pref->updated_at) : 'N/A',
                'type'      => 'anon',
            ];
        }

        $data['prefs']      = $prefs_info;
        $data['search']     = str_replace('%',' ',$search);
        $data['pagination'] = $pagination->render();

        $this->template->title   = 'Gestión - Preferencias de Cookies';
        $this->template->content = View::forge('admin/legal/cookies/index', $data, false);
    }

   
    /**
     * INFO
     *
     * MUESTRA EL DETALLE DE UNA PREFERENCIA DE COOKIES
     *
     * @access  public
     * @param   int|null $id
     * @return  void
     */
    public function action_info($id = null)
    {
        # VALIDACIÓN DEL ID
        if (is_null($id)) {
            \Session::set_flash('error', 'No se especificó la preferencia de cookies.');
            \Response::redirect('admin/legal/cookies');
        }

        # BUSCAR EL REGISTRO CON RELACIÓN A USER (si existe)
        $cookie = Model_User_Cookies_Preference::find($id, [
            'related' => ['user']
        ]);

        # VALIDAR EXISTENCIA
        if (!$cookie) {
            \Session::set_flash('error', 'Preferencia de cookies no encontrada.');
            \Response::redirect('admin/legal/cookies');
        }

        # DATOS A VISTA
        $data = [
            'cookie' => $cookie
        ];

        $this->template->title   = 'Gestión - Detalle Preferencias de Cookies';
        $this->template->content = \View::forge('admin/legal/cookies/info', $data, false);
    }


    /**
     * POST ACCEPT
     *
     * Registra la aceptación de cookies para usuarios logueados y anónimos.
     * Responde solo a peticiones AJAX.
     *
     * @access  public
     * @return  string (JSON)
     */    
    public function post_accept()
    {
        if (!Input::is_ajax()) {
            return json_encode(['status' => 'error', 'msg' => 'Método no permitido']);
        }

        $prefs = [
            'analytics'       => (int) Input::post('analytics', 1),
            'marketing'       => (int) Input::post('marketing', 1),
            'personalization' => (int) Input::post('personalization', 1),
        ];

        if (Auth::check()) {
            # Usuario logueado
            $user_id = Auth::get('id');
            $model = Helper_Legal::update_cookies_preferences($user_id, $prefs);
            return json_encode(['status' => 'ok', 'type' => 'user', 'id' => $model->id]);
        } else {
            # Usuario anónimo
            $token = Cookie::get('cookie_token');
            if (!$token) {
                $token = sha1(uniqid().mt_rand());
                Cookie::set('cookie_token', $token, 60*60*24*365); // 1 año
            }

            $model = Model_Anonymous_Cookies_Accept::query()->where('token', $token)->get_one();

            if (!$model) {
                $model = Model_Anonymous_Cookies_Accept::forge();
                $model->token = $token;
                $model->necessary = 0; // siempre aceptadas
                $model->ip_address = Input::real_ip();
                $model->user_agent = Input::user_agent();
            }

            $model->analytics       = $prefs['analytics'];
            $model->marketing       = $prefs['marketing'];
            $model->personalization = $prefs['personalization'];
            $model->save();

            return json_encode(['status' => 'ok', 'type' => 'anon', 'id' => $model->id, 'token' => $token]);
        }
    }

    /**
     * AGREGAR
     *
     * PERMITE CREAR UNA NUEVA PREFERENCIA DE COOKIES
     *
     * @access  public
     * @return  void
     */
    public function action_agregar()
    {
        # VARIABLES
        $data      = [];
        $classes   = [];
        $user_opts = [];

        # OPCIONES DE USUARIOS (solo activos)
        foreach (Model_User::query()->where('id','>', 0)->order_by('username', 'asc')->get() as $u) {
            $user_opts[$u->id] = $u->username . ' (' . $u->email . ')';
        }

        # SI SE UTILIZÓ EL MÉTODO POST
        if (Input::method() == 'POST')
        {
            $val = Validation::forge('cookies');
            $val->add_field('user_id', 'Usuario', 'valid_string[numeric]');
            $val->add_field('token', 'Token', 'max_length[255]');

            if ($val->run())
            {
                try
                {
                    $prefs = Model_User_Cookies_Preference::forge();
                    $prefs->user_id        = Input::post('user_id') ?: null;
                    $prefs->token          = Input::post('token') ?: null;
                    $prefs->necessary      = 0; // siempre aceptadas
                    $prefs->analytics      = Input::post('analytics', 1);
                    $prefs->marketing      = Input::post('marketing', 1);
                    $prefs->personalization= Input::post('personalization', 1);
                    $prefs->accepted_at    = time();
                    $prefs->updated_at     = time();
                    $prefs->ip_address     = Input::real_ip();
                    $prefs->user_agent     = Input::user_agent();

                    if ($prefs->save())
                    {
                        \Session::set_flash('success','Preferencia de cookies registrada correctamente.');
                        \Response::redirect('admin/legal/cookies');
                    }
                    else
                    {
                        \Session::set_flash('error','No se pudo guardar la preferencia.');
                    }
                }
                catch(\Exception $e)
                {
                    \Log::error('[COOKIES] Error al guardar preferencia: '.$e->getMessage());
                    \Session::set_flash('error','Ocurrió un error al guardar. Verifique los datos.');
                }
            }
            else
            {
                $data['errors'] = $val->error();
                \Session::set_flash('error','Encontramos errores en el formulario.');
            }
        }

        # DATOS PARA LA VISTA
        $data['user_opts'] = $user_opts;
        $this->template->title   = 'Gestión - Nueva Preferencia de Cookies';
        $this->template->content = \View::forge('admin/legal/cookies/agregar', $data);
    }

}
