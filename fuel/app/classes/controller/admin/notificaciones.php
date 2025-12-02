<?php

class Controller_Admin_Notificaciones extends Controller_Admin
{
     // ===========================
    // ARRAY GLOBAL DE GRUPOS DEL SISTEMA
    // ===========================
    protected $group_definitions = array(
        -1   => array('name' => 'Banned',        'roles' => array('banned')),
         0   => array('name' => 'Guests',        'roles' => array()),
         1   => array('name' => 'Users',         'roles' => array('user')),
         10  => array('name' => 'Provider',      'roles' => array('user')),
         15  => array('name' => 'Partner',       'roles' => array('user')),
         30  => array('name' => 'Externo',       'roles' => array('user')),
         20  => array('name' => 'Empleados',     'roles' => array('user','moderator')),
         25  => array('name' => 'Vendedores',    'roles' => array('user','moderator')),
         50  => array('name' => 'Moderators',    'roles' => array('user', 'moderator')),
         100 => array('name' => 'Administrators','roles' => array('user', 'moderator', 'admin')),
    );
    
    
    /**
     * MUESTRA EL LISTADO DE NOTIFICACIONES GENERADAS (MANUALES Y POR EVENTO)
     * SOPORTA FILTRO POR TIPO, ESTADO, EVENTO Y BÚSQUEDA POR TÍTULO
     */
    public function action_index()
{
    // ===========================
    // OBTENER NOTIFICACIONES REALES (manuales y de evento) PARA EL USUARIO ACTUAL
    // ===========================
    $user_id = Auth::get('id');

    // Filtros de búsqueda
    $search = Input::get('search', '');
    $type   = Input::get('type', '');
    $page   = (int)Input::get('page', 1);
    $per_page = 25;
    $offset = ($page-1) * $per_page;

    $query = Model_Notification_Recipient::query()
        ->related('notification')
        ->where('user_id', $user_id);

    if ($type) {
        $query->related('notification')->where('notification.type', $type);
    }
    if ($search) {
        $query->related('notification')->where_open()
            ->where('notification.title', 'like', "%$search%")
            ->or_where('notification.message', 'like', "%$search%")
        ->where_close();
    }

    $total = $query->count();
    $recipients = $query
        ->order_by('created_at', 'desc')
        ->rows_limit($per_page)
        ->rows_offset($offset)
        ->get();

    $notifications = [];
    foreach ($recipients as $rec) {
        $notif = $rec->notification;
        if (!$notif) continue;
        $notifications[] = $notif;
    }

    // ===========================
    // OBTENER REGLAS/PLANTILLAS DE EVENTOS
    // ===========================
    $event_configs = Model_Notification_Events_Config::query()
        ->order_by('created_at', 'desc')
        ->get();

    // Prepara para la vista los grupos y usuarios destino
    $plantillas = [];
    foreach ($event_configs as $conf) {
        $targets = $conf->targets ?: [];
        $grupos = [];
        $usuarios = [];
        foreach ($targets as $t) {
            if ($t->group_id)   $grupos[] = $t->group_id;
            if ($t->user_id)    $usuarios[] = $t->user_id;
        }
        $plantillas[] = [
            'id'         => $conf->id,
            'event_key'  => $conf->event_key,
            'title'      => $conf->title,
            'message'    => $conf->message,
            'url_pattern'=> $conf->url_pattern,
            'icon'       => $conf->icon,
            'priority'   => $conf->priority,
            'grupos'     => $grupos,
            'usuarios'   => $usuarios,
            'active'     => $conf->active,
        ];
    }

    // Trae todos los usuarios solo si quieres mostrar nombres en la card de plantillas
    $users = Model_User::query()->get();

    // ===========================
    // PASA DATOS A LA VISTA
    // ===========================
    $data = [
        'notifications'     => $notifications,
        'plantillas'        => $plantillas,
        'group_definitions' => $this->group_definitions,
        'users'             => $users,
        'total'             => $total,
        'per_page'          => $per_page,
        'page'              => $page,
        'type'              => $type,
        'search'            => $search,
    ];

    $this->template->title = 'Notificaciones';
    $this->template->content = View::forge('admin/notificaciones/index', $data);
}




    // BUSCAR: (opcional, si tienes búsqueda AJAX separada)
    public function action_buscar() {
        if (!Helper_Permission::can('config_notificaciones', 'view')) {
            Session::set_flash('error', 'NO TIENES PERMISO PARA VER NOTIFICACIONES.');
            Response::redirect('admin/notificaciones');
        }

        $search = Input::get('q', '');
        $query = Model_Notification::query()
            ->where('title', 'like', "%{$search}%")
            ->or_where('message', 'like', "%{$search}%")
            ->order_by('created_at', 'desc')
            ->rows_limit(10);

        $notifications = $query->get();

        return Response::json($notifications);  
    }

    // INFO: Muestra detalle de una notificación (incluye destinatarios y estado de lectura)
    /**
     * ACCIÓN: DETALLE DE NOTIFICACIÓN (INFO)
     */
    public function action_info($id = null)
    {
        // ==============================
        // VALIDA ID Y PERMISOS
        // ==============================
        if (!$id || !is_numeric($id)) {
            Session::set_flash('error', 'ID DE NOTIFICACIÓN INVÁLIDO.');
            Response::redirect('admin/notificaciones');
        }

        if (!Helper_Permission::can('config_notificaciones', 'view')) {
            Session::set_flash('error', 'NO TIENES PERMISO PARA VER NOTIFICACIONES.');
            Response::redirect('admin');
        }

        // ==============================
        // OBTIENE NOTIFICACIÓN Y SUS RECIPIENTS (con usuario)
        // ==============================
        $notification = Model_Notification::find($id, array(
            'related' => array('recipients', 'recipients.user')
        ));

        if (!$notification) {
            Session::set_flash('error', 'NO SE ENCONTRÓ LA NOTIFICACIÓN.');
            Response::redirect('admin/notificaciones');
        }

        // SI TIENES TABLA DE EVENTOS, BUSCA EL EVENTO (opcional)
        $evento = null;
        if ($notification->type == 'evento' && property_exists($notification, 'event_key')) {
            $evento = Model_Notification_Events_Config::query()
                ->where('event_key', $notification->event_key)
                ->get_one();
        }

        // ==============================
        // ENVÍA SOLO LOS DATOS NECESARIOS, EL ARRAY DE GRUPOS SE USA DIRECTO EN LA VISTA
        // ==============================
        $data = array(
            'notification' => $notification,
            'recipients'   => $notification->recipients,
            'evento'       => $evento,
            // 'group_definitions' => $this->group_definitions, // YA NO SE PASA, SE USA DESDE $controller
        );

        $this->template->title = 'Detalle de Notificación';
        $this->template->content = View::forge('admin/notificaciones/info', $data);
    }


 

    /**
     * ACCIÓN: AGREGAR UNA NOTIFICACIÓN (MANUAL O POR EVENTO)
     */
  public function action_agregar()
{
    // ===========================
    // PERMISOS
    // ===========================
    if (!Helper_Permission::can('config_notificaciones', 'create')) {
        Session::set_flash('error', 'NO TIENES PERMISO PARA AGREGAR NOTIFICACIONES.');
        Response::redirect('admin/notificaciones');
    }

    // ===========================
    // FILTRAR GRUPOS EXCLUIDOS
    // ===========================
    $excluded_groups = array(1, 10, 15);

    // ===========================
    // OBTENER USUARIOS (SOLO LOS DE GRUPOS PERMITIDOS)
    // ===========================
    $users = Model_User::query()
        ->where('group', 'not in', $excluded_groups)
        ->order_by('username', 'asc')
        ->get();

    // ===========================
    // OBTENER LISTA DE GRUPOS PERMITIDOS
    // ===========================
    $groups = array();
    foreach ($users as $u) {
        if ($u->group !== null && $u->group !== '' && !in_array($u->group, $excluded_groups)) {
            $groups[$u->group] = $u->group;
        }
    }
    ksort($groups);

    // ===========================
    // PROCESO DE FORMULARIO POST
    // ===========================
    if (Input::method() == 'POST') {
        // ===========================
        // LOG DE DATOS RECIBIDOS
        // ===========================
        \Log::info('[NOTIF][AGREGAR] POST', Input::post());

        $type        = Input::post('type', 'manual'); // manual/evento
        $title       = trim(Input::post('title'));
        $message     = trim(Input::post('message'));
        $url         = trim(Input::post('url'));
        $icon        = trim(Input::post('icon'));
        $priority    = (int)Input::post('priority', 1);
        $expires_at  = Input::post('expires_at') ? strtotime(Input::post('expires_at')) : null;
        $user_ids    = Input::post('user_ids', []);  // Array de IDs usuario
        $group_ids   = Input::post('group_ids', []); // Array de IDs de grupo (solo permitidos)

        // ===========================
        // SI ES MANUAL: CREAR NOTIFICACIÓN Y DESTINATARIOS
        // ===========================
        if ($type == 'manual') {
            $notif = Model_Notification::forge([
                'type'        => 'manual',
                'title'       => $title,
                'message'     => $message,
                'url'         => $url,
                'icon'        => $icon,
                'priority'    => $priority,
                'active'      => 1,
                'created_by'  => Auth::get('id'),
                'created_at'  => time(),
                'updated_at'  => time(),
                'expires_at'  => $expires_at,
            ]);
            $saved = $notif->save();
            \Log::info('[NOTIF][MANUAL] Guardando notificación', ['id' => $notif->id, 'saved' => $saved]);

            // AGREGAR USUARIOS DESTINATARIOS POR GRUPO
            $dest_users = $user_ids;
            if (!empty($group_ids)) {
                $group_users = Model_User::query()
                    ->where('group', 'in', $group_ids)
                    ->where('group', 'not in', $excluded_groups) // por seguridad
                    ->get();
                foreach ($group_users as $user) {
                    $dest_users[] = $user->id;
                }
            }
            $dest_users = array_unique($dest_users);

            foreach ($dest_users as $uid) {
                $recipient = Model_Notification_Recipient::forge([
                    'notification_id' => $notif->id,
                    'user_id'         => $uid,
                    'user_group_id'   => null,
                    'status'          => 0,
                    'created_at'      => time(),
                    'updated_at'      => time(),
                ]);
                $r_saved = $recipient->save();
                \Log::info('[NOTIF][MANUAL] Guardando destinatario', ['uid' => $uid, 'recipient_id' => $recipient->id, 'saved' => $r_saved]);
            }

            Session::set_flash('success', 'Notificación creada y enviada.');
            Response::redirect('admin/notificaciones');
        }

        // ===========================
        // SI ES POR EVENTO: GUARDAR CONFIGURACIÓN
        // ===========================
        elseif ($type == 'evento') {
            $event_key   = trim(Input::post('event_key'));
            $url_pattern = trim(Input::post('url_pattern'));

            // === VALIDACIÓN DE DUPLICADO POR event_key ===
            $existe = Model_Notification_Events_Config::query()
                ->where('event_key', $event_key)
                ->get_one();

            if ($existe) {
                Session::set_flash('error', 'Ya existe una regla para ese evento (event_key). Edita la existente o elige otro.');
                Response::redirect('admin/notificaciones/agregar');
            }

            $config = Model_Notification_Events_Config::forge([
                'event_key'   => $event_key,
                'title'       => $title,
                'message'     => $message,
                'url_pattern' => $url_pattern,
                'active'      => 1,
                'created_at'  => time(),
                'updated_at'  => time(),
            ]);
            $conf_saved = $config->save();
            \Log::info('[NOTIF][EVENTO] Guardando configuración de evento', ['id' => $config->id, 'saved' => $conf_saved]);

            // GUARDAR DESTINATARIOS DE EVENTO (GRUPOS)
            if (!empty($group_ids)) {
                foreach ($group_ids as $gid) {
                    $target = Model_Notification_Events_Config_Target::forge([
                        'config_id'  => $config->id,
                        'group_id'   => $gid,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);
                    $t_saved = $target->save();
                    \Log::info('[NOTIF][EVENTO] Guardando target (grupo)', ['group_id' => $gid, 'target_id' => $target->id, 'saved' => $t_saved]);
                }
            }
            // GUARDAR DESTINATARIOS DE EVENTO (USUARIOS)
            foreach ($user_ids as $uid) {
                $target = Model_Notification_Events_Config_Target::forge([
                    'config_id'  => $config->id,
                    'user_id'    => $uid,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);
                $t_saved = $target->save();
                \Log::info('[NOTIF][EVENTO] Guardando target (usuario)', ['user_id' => $uid, 'target_id' => $target->id, 'saved' => $t_saved]);
            }

            Session::set_flash('success', 'Regla de evento guardada.');
            Response::redirect('admin/notificaciones');
        }
    }

    // ===========================
    // ENVÍA DATOS A LA VISTA
    // ===========================
    $data = [
        'users'   => $users,
        'groups'  => $groups,
        'group_definitions' => $this->group_definitions,
    ];
    $this->template->title = 'Agregar Notificación';
    $this->template->content = View::forge('admin/notificaciones/agregar', $data);
}





    // EDITAR: Editar notificación manual o configuración de evento
    public function action_editar($id = null)
{
    if (!$id || !is_numeric($id)) {
        Session::set_flash('error', 'ID DE NOTIFICACIÓN INVÁLIDO.');
        Response::redirect('admin/notificaciones');
    }

    if (!Helper_Permission::can('config_notificaciones', 'edit')) {
        Session::set_flash('error', 'NO TIENES PERMISO PARA EDITAR NOTIFICACIONES.');
        Response::redirect('admin');
    }

    // GRUPOS A EXCLUIR
    $excluded_groups = array(1, 10, 15);

    // OBTIENE NOTIFICACIÓN Y SUS RELACIONES
    $notification = Model_Notification::find($id, array(
        'related' => array('recipients', 'recipients.user')
    ));

    if (!$notification) {
        Session::set_flash('error', 'NO SE ENCONTRÓ LA NOTIFICACIÓN.');
        Response::redirect('admin/notificaciones');
    }

    // OBTIENE USUARIOS PERMITIDOS
    $users = Model_User::query()
        ->where('group', 'not in', $excluded_groups)
        ->order_by('username', 'asc')
        ->get();

    // OBTIENE GRUPOS ÚNICOS DEL LISTADO DE USUARIOS
    $groups = array();
    foreach ($users as $u) {
        if ($u->group !== null && $u->group !== '' && !in_array($u->group, $excluded_groups)) {
            $groups[$u->group] = $u->group;
        }
    }
    ksort($groups);

    // =======================
    // FORMULARIO POST
    // =======================
    if (Input::method() == 'POST') {
        $type        = Input::post('type', $notification->type);
        $title       = trim(Input::post('title'));
        $message     = trim(Input::post('message'));
        $url         = trim(Input::post('url'));
        $icon        = trim(Input::post('icon'));
        $priority    = (int)Input::post('priority', 1);
        $expires_at  = Input::post('expires_at') ? strtotime(Input::post('expires_at')) : null;
        $user_ids    = Input::post('user_ids', []);  // Array de IDs usuario
        $group_ids   = Input::post('group_ids', []); // Array de IDs de grupo

        // ===== VALIDACIÓN: OBLIGAR AL MENOS UNO =====
        if (empty($user_ids) && empty($group_ids)) {
            Session::set_flash('error', 'Debes seleccionar al menos un grupo o usuario destinatario.');
            // Conserva selección para el usuario en la vista
            $selected_users  = [];
            $selected_groups = [];
            if (!empty($user_ids)) $selected_users = $user_ids;
            if (!empty($group_ids)) $selected_groups = $group_ids;

            $data = [
                'notification'      => $notification,
                'users'             => $users,
                'groups'            => $groups,
                'group_definitions' => $this->group_definitions,
                'selected_users'    => $selected_users,
                'selected_groups'   => $selected_groups,
            ];

            $this->template->title = 'Editar Notificación';
            $this->template->content = View::forge('admin/notificaciones/editar', $data);
            return; // NO SIGUE, SOLO MUESTRA LA VISTA DE NUEVO
        }

        // --- Si es manual ---
        if ($type == 'manual') {
            // ACTUALIZA CAMPOS
            $notification->title      = $title;
            $notification->message    = $message;
            $notification->url        = $url;
            $notification->icon       = $icon;
            $notification->priority   = $priority;
            $notification->updated_at = time();
            $notification->expires_at = $expires_at;
            $notification->save();

            // ACTUALIZA DESTINATARIOS
            // Primero borra todos los anteriores (si quieres mantener historial, usa un borrado lógico)
            foreach ($notification->recipients as $r) {
                $r->delete();
            }
            // Luego inserta los nuevos
            $dest_users = $user_ids;
            if (!empty($group_ids)) {
                $group_users = Model_User::query()
                    ->where('group', 'in', $group_ids)
                    ->where('group', 'not in', $excluded_groups)
                    ->get();
                foreach ($group_users as $user) {
                    $dest_users[] = $user->id;
                }
            }
            $dest_users = array_unique($dest_users);

            foreach ($dest_users as $uid) {
                $recipient = Model_Notification_Recipient::forge([
                    'notification_id' => $notification->id,
                    'user_id'         => $uid,
                    'user_group_id'   => null,
                    'status'          => 0,
                    'created_at'      => time(),
                    'updated_at'      => time(),
                ]);
                $recipient->save();
            }

            Session::set_flash('success', 'Notificación actualizada correctamente.');
            Response::redirect('admin/notificaciones/info/'.$notification->id);
        }

        // --- Si es por evento ---
        elseif ($type == 'evento') {
            // Busca la configuración (la lógica puede requerir ajustarse a tu modelo)
            $config = Model_Notification_Events_Config::query()
                ->where('event_key', $notification->event_key)
                ->get_one();

            if ($config) {
                $config->title       = $title;
                $config->message     = $message;
                $config->url_pattern = trim(Input::post('url_pattern'));
                $config->updated_at  = time();
                $config->save();

                // Borrar targets previos
                foreach ($config->targets as $t) {
                    $t->delete();
                }
                if (!empty($group_ids)) {
                    foreach ($group_ids as $gid) {
                        $target = Model_Notification_Events_Config_Target::forge([
                            'config_id'  => $config->id,
                            'group_id'   => $gid,
                            'created_at' => time(),
                            'updated_at' => time(),
                        ]);
                        $target->save();
                    }
                }
                foreach ($user_ids as $uid) {
                    $target = Model_Notification_Events_Config_Target::forge([
                        'config_id'  => $config->id,
                        'user_id'    => $uid,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);
                    $target->save();
                }
                Session::set_flash('success', 'Regla de evento actualizada.');
                Response::redirect('admin/notificaciones/info/'.$notification->id);
            } else {
                Session::set_flash('error', 'No se encontró configuración de evento.');
            }
        }
    }

    // OBTENER DESTINATARIOS SELECCIONADOS (para prellenar selects)
    $selected_users = array();
    foreach ($notification->recipients as $r) {
        $selected_users[] = $r->user_id;
    }
    $selected_groups = array();
    foreach ($selected_users as $uid) {
        foreach ($users as $u) {
            if ($u->id == $uid) {
                $selected_groups[] = $u->group;
            }
        }
    }
    $selected_groups = array_unique($selected_groups);

    $data = [
        'notification'      => $notification,
        'users'             => $users,
        'groups'            => $groups,
        'group_definitions' => $this->group_definitions,
        'selected_users'    => $selected_users,
        'selected_groups'   => $selected_groups,
    ];

    $this->template->title = 'Editar Notificación';
    $this->template->content = View::forge('admin/notificaciones/editar', $data);
}



public function action_editarauto($id = null)
{
    // ===========================
    // 1. VALIDACIÓN DE PERMISOS
    // ===========================
    if (!Helper_Permission::can('config_notificaciones', 'edit')) {
        Session::set_flash('error', 'NO TIENES PERMISO PARA EDITAR REGLAS AUTOMÁTICAS.');
        Response::redirect('admin/notificaciones');
    }

    // ===========================
    // 2. OBTIENE LA REGLA
    // ===========================
    $config = Model_Notification_Events_Config::find($id);
    if (!$config) {
        Session::set_flash('error', 'Regla automática no encontrada.');
        Response::redirect('admin/notificaciones');
    }

    // ===========================
    // 3. OBTIENE LOS DESTINATARIOS ACTUALES (GRUPOS Y USUARIOS)
    // ===========================
    $group_ids = [];
    $user_ids = [];
    foreach ($config->targets as $t) {
        if ($t->group_id) $group_ids[] = $t->group_id;
        if ($t->user_id)  $user_ids[]  = $t->user_id;
    }

    // ===========================
    // 4. CATÁLOGOS DE GRUPOS Y USUARIOS
    // ===========================
    $excluded_groups = array(1, 10, 15);
    $users = Model_User::query()
        ->where('group', 'not in', $excluded_groups)
        ->order_by('username', 'asc')
        ->get();

    $groups = [];
    foreach ($users as $u) {
        if ($u->group !== null && $u->group !== '' && !in_array($u->group, $excluded_groups)) {
            $groups[$u->group] = $u->group;
        }
    }
    ksort($groups);

    // ===========================
    // 5. PROCESO DE GUARDADO SI POST
    // ===========================
    if (Input::method() == 'POST') {
        $config->title       = trim(Input::post('title'));
        $config->message     = trim(Input::post('message'));
        $config->event_key   = trim(Input::post('event_key'));
        $config->url_pattern = trim(Input::post('url_pattern'));
        $config->icon        = trim(Input::post('icon'));
        $config->priority    = (int)Input::post('priority', 1);
        $config->active      = (int)Input::post('active', 1);
        $config->updated_at  = time();

        $user_ids_post  = Input::post('user_ids', []);
        $group_ids_post = Input::post('group_ids', []);

        // ========== VALIDACIÓN OBLIGATORIA ==========
        if (empty($user_ids_post) && empty($group_ids_post)) {
            Session::set_flash('error', 'Debes seleccionar al menos un grupo o usuario destinatario.');

            // Prellenar para no perder selección
            $group_ids = $group_ids_post;
            $user_ids  = $user_ids_post;

            $data = [
                'config'           => $config,
                'group_ids'        => $group_ids,
                'user_ids'         => $user_ids,
                'users'            => $users,
                'groups'           => $groups,
                'group_definitions'=> $this->group_definitions,
            ];

            $this->template->title = 'Editar Regla Automática';
            $this->template->content = View::forge('admin/notificaciones/editar_automatica', $data);
            return;
        }

        if ($config->save()) {
            // BORRAR DESTINATARIOS ANTERIORES
            foreach ($config->targets as $target) {
                $target->delete();
            }
            // AGREGAR NUEVOS GRUPOS
            if (!empty($group_ids_post)) {
                foreach ($group_ids_post as $gid) {
                    $t = Model_Notification_Events_Config_Target::forge([
                        'config_id'  => $config->id,
                        'group_id'   => $gid,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);
                    $t->save();
                }
            }
            // AGREGAR NUEVOS USUARIOS
            if (!empty($user_ids_post)) {
                foreach ($user_ids_post as $uid) {
                    $t = Model_Notification_Events_Config_Target::forge([
                        'config_id'  => $config->id,
                        'user_id'    => $uid,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);
                    $t->save();
                }
            }

            Session::set_flash('success', 'Regla automática actualizada correctamente.');
            Response::redirect('admin/notificaciones');
        } else {
            Session::set_flash('error', 'Error al guardar los cambios.');
        }

        // Actualizar los arrays para no perder cambios en la vista
        $group_ids = $group_ids_post;
        $user_ids  = $user_ids_post;
    }

    // ===========================
    // 6. ENVÍA DATOS A LA VISTA
    // ===========================
    $data = [
        'config'           => $config,
        'group_ids'        => $group_ids,
        'user_ids'         => $user_ids,
        'users'            => $users,
        'groups'           => $groups,
        'group_definitions'=> $this->group_definitions,
    ];

    $this->template->title = 'Editar Regla Automática';
    $this->template->content = View::forge('admin/notificaciones/editar_automatica', $data);
}




    // ELIMINAR: (si lo manejas)
   public function action_eliminar($id = null)
{
    // ==========================
    // VALIDAR ID Y PERMISOS
    // ==========================
    if (!$id || !is_numeric($id)) {
        Session::set_flash('error', 'ID DE NOTIFICACIÓN INVÁLIDO.');
        Response::redirect('admin/notificaciones');
    }

    if (!Helper_Permission::can('config_notificaciones', 'delete')) {
        Session::set_flash('error', 'NO TIENES PERMISO PARA ELIMINAR NOTIFICACIONES.');
        Response::redirect('admin/notificaciones');
    }

    // ==========================
    // OBTENER NOTIFICACIÓN
    // ==========================
    $notification = Model_Notification::find($id, array('related' => array('recipients')));

    if (!$notification) {
        Session::set_flash('error', 'NO SE ENCONTRÓ LA NOTIFICACIÓN.');
        Response::redirect('admin/notificaciones');
    }

    try {
        // ==========================
        // ELIMINAR RECIPIENTS (RELACIÓN has_many)
        // ==========================
        foreach ($notification->recipients as $r) {
            $r->delete();
        }

        // ==========================
        // ELIMINAR LA NOTIFICACIÓN
        // ==========================
        $notification->delete();

        \Log::info('NOTIFICACIÓN ELIMINADA', [
            'notification_id' => $id,
            'deleted_by' => Auth::get('id')
        ]);
        Session::set_flash('success', 'Notificación eliminada correctamente.');

    } catch (\Exception $e) {
        \Log::error('ERROR AL ELIMINAR NOTIFICACIÓN: ' . $e->getMessage());
        Session::set_flash('error', 'Hubo un error al eliminar la notificación.');
    }

    Response::redirect('admin/notificaciones');
}

}
