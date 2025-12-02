<?php
/**
 * HELPER LEGAL
 *
 * CENTRALIZA EL USO DE DOCUMENTOS LEGALES, CONSENTIMIENTOS Y PREFERENCIAS.
 *
 * CONVENCIÓN DE FLAGS:
 * - 0 = ACTIVO / ACEPTADO / PERMITIDO
 * - 1 = INACTIVO / RECHAZADO / NO PERMITIDO
 */

class Helper_Legal
{
    ///////////////////////////////////////////////////////
    // CONSENTIMIENTOS DE USUARIOS
    ///////////////////////////////////////////////////////


    /**
     * MAPEAR GRUPO DE USUARIO A CATEGORÍA DE DOCUMENTOS
     */
    protected static function map_user_category($user_id = null)
    {
        if (!$user_id) {
            return 'visitante';
        }

        $user = \Model_User::find($user_id);
        if (!$user) {
            return 'visitante';
        }

        switch ((int) $user->group) {
            case 1:  return 'cliente';
            case 10: return 'proveedor';
            case 15: return 'socio';
            case 20:
            case 25:
            case 30:
            case 50:
            case 100: return 'empleado';
            default: return 'general';
        }
    }

    /**
     * Generar URL para ver documento legal según el grupo del usuario
     */
    protected static function get_document_url($doc_id, $user_id = null)
    {
        $category = self::map_user_category($user_id);

        switch ($category) {
            case 'proveedor':
                return 'proveedores/perfil/legal/info/'.$doc_id;
            case 'socio':
                return 'socios/perfil/legal/info/'.$doc_id;
            case 'empleado':
                return 'admin/perfil/legal/info/'.$doc_id;
            case 'cliente':
                return 'clientes/perfil/legal/info/'.$doc_id;
            default:
                return 'admin/legal/documentos/info/'.$doc_id;
        }
    }


    /**
     * RENDERIZAR TODOS LOS DOCUMENTOS LEGALES PARA UN USUARIO
     *
     * @param int|null $user_id
     * @return string HTML con todos los consentimientos
     */
    public static function render_documents_for_user($user_id = null)
    {
        $category = self::map_user_category($user_id);

        // Trae documentos activos de la categoría y los generales
        $docs = \Model_Legal_Document::query()
            ->where('active', 0)
            ->where_open()
                ->where('category', $category)
                ->or_where('category', 'general')
            ->where_close()
            ->order_by('created_at', 'desc')
            ->get();

        if (!$docs) {
            return "<p class='text-muted'>No hay documentos legales configurados.</p>";
        }

        $html = "<div class='consents-group'>";
        foreach ($docs as $doc) {
            if ($doc->required == 1) {
                $html .= self::render_required_checkbox($doc->shortcode, $user_id);
            } else {
                $html .= self::render_checkbox($doc->shortcode, $user_id);
            }
        }
        $html .= "</div>";

        \Log::info("[LEGAL] Renderizados documentos para User={$user_id}, Category={$category}, Docs=".count($docs));

        return $html;
    }

    /**
     * RENDERIZAR SOLO DOCUMENTOS OBLIGATORIOS PARA UN USUARIO
     *
     * Útil en el flujo de registro o alta de usuario.
     */
    public static function render_required_documents_for_user($user_id = null)
    {
        $category = self::map_user_category($user_id);

        $docs = \Model_Legal_Document::query()
            ->where('active', 0)
            ->where('required', 1) // solo obligatorios
            ->where_open()
                ->where('category', $category)
                ->or_where('category', 'general')
            ->where_close()
            ->order_by('created_at', 'desc')
            ->get();

        if (!$docs) {
            return "<p class='text-muted'>No hay documentos obligatorios configurados.</p>";
        }

        $html = "<div class='consents-required'>";
        foreach ($docs as $doc) {
            $html .= self::render_required_checkbox($doc->shortcode, $user_id);
        }
        $html .= "</div>";

        \Log::info("[LEGAL] Renderizados SOLO OBLIGATORIOS para User={$user_id}, Category={$category}, Docs=".count($docs));

        return $html;
    }

    
    /**
     * OBTENER DOCUMENTO LEGAL POR SHORTCODE
     *
     * @param string $shortcode Identificador corto del documento
     * @return Model_Legal_Document|null
     */
    public static function get_document($shortcode)
    {
        return Model_Legal_Document::query()
            ->where('shortcode', $shortcode)
            ->where('active', 0) // 0 = activo
            ->order_by('version', 'desc')
            ->get_one();
    }

    /**
     * RENDERIZAR CHECKBOX DE ACEPTACIÓN
     *
     * @param string $shortcode Documento a mostrar
     * @param int|null $user_id Usuario (opcional si ya está autenticado)
     * @return string HTML del checkbox + link
     */
    public static function render_checkbox($shortcode, $user_id = null)
    {
        $doc = self::get_document($shortcode);
        if (!$doc) {
            return '';
        }

        $doc_url = self::get_document_url($doc->id, $user_id);

        $checked = false;
        $status_msg = '';
        $already_accepted = false;

        if ($user_id) {
            $consent = Model_User_Consent::query()
                ->where('user_id', $user_id)
                ->where('document_id', $doc->id)
                ->order_by('accepted_at', 'desc')
                ->get_one();

            if ($consent) {
                if ($consent->accepted == 0 && $consent->version == $doc->version) {
                    // Aceptado y misma versión
                    $checked = true;
                    $already_accepted = true;
                    
                    $status_msg = "<p class='text-success mb-0'>
                                    <i class='fa fa-check'></i> 
                                    Ya aceptaste el " .
                                    Html::anchor($doc_url, $doc->title, [
                                        'target' => '_blank',
                                        'class'  => 'legal-link'
                                    ]) . ".
                                </p>";
                } elseif ($consent->version != $doc->version) {
                    //Nueva versión
                    $status_msg = "<small class='text-warning d-block'>
                                    ⚠ Documento actualizado a versión {$doc->version}, acepta nuevamente.
                                </small>";
                } elseif ($consent->accepted != 0) {
                    //Rechazado
                    $status_msg = "<small class='text-danger d-block'>
                                    No aceptado. Debes aceptarlo.
                                </small>";
                }
            }
        }

        // solo requerido si el doc tiene flag required = 1 y aún no está aceptado
        $attrs = ['id' => 'consent_'.$doc->id];
        if ($doc->required == 1 && !$already_accepted) {
            $attrs['required'] = 'required';
        }

        $checkbox = '';
        if (!$already_accepted) {
            // si no está aceptado o tiene nueva versión → mostrar checkbox
            $checkbox = Form::checkbox("consent[{$doc->id}]", 1, $checked, $attrs) .
                "<label for='consent_{$doc->id}' class='form-check-label'>
                    Acepto " .  Html::anchor($doc_url, $doc->title, [
                        'target' => '_blank',
                        'class'  => 'legal-link'
                    ]) . "
                </label>";
        }

        return "<div class='form-check mb-2'>
                    {$checkbox}
                    {$status_msg}
                </div>";
    }



    /**
     * RENDERIZAR CHECKBOX DE ACEPTACIÓN OBLIGATORIO
     *
     * Siempre marcará como "required" independientemente del flag en BD.
     */
    public static function render_required_checkbox($shortcode, $user_id = null)
    {
        $doc = self::get_document($shortcode);
        if (!$doc) {
            return '';
        }

        $doc_url = self::get_document_url($doc->id, $user_id);

        $checked = false;
        $status_msg = '';
        $already_accepted = false;

        if ($user_id) {
            $consent = Model_User_Consent::query()
                ->where('user_id', $user_id)
                ->where('document_id', $doc->id)
                ->order_by('accepted_at', 'desc')
                ->get_one();

            if ($consent) {
                if ($consent->accepted == 0 && $consent->version == $doc->version) {
                    // aceptado y misma versión
                    $checked = true;
                    $already_accepted = true;
                    $status_msg = "<p class='text-success mb-0'>
                                    <i class='fa fa-check'></i> 
                                    Ya aceptaste el " .
                                    Html::anchor($doc_url, $doc->title, [
                                        'target' => '_blank',
                                        'class'  => 'legal-link'
                                    ]) . ".
                                </p>";
                } elseif ($consent->version != $doc->version) {
                    $status_msg = "<small class='text-warning d-block'>
                                    ⚠ Documento actualizado a versión {$doc->version}, acepta nuevamente.
                                </small>";
                } elseif ($consent->accepted != 0) {
                    $status_msg = "<small class='text-danger d-block'>
                                    No aceptado. Debes aceptarlo.
                                </small>";
                }
            }
        }

        // En este helper siempre se exige aceptación
        $attrs = [
            'id'       => 'consent_'.$doc->id,
            'required' => 'required'
        ];

        $checkbox = '';
        if (!$already_accepted) {
            $checkbox = Form::checkbox("consent[{$doc->id}]", 1, $checked, $attrs) .
                "<label for='consent_{$doc->id}' class='form-check-label'>
                    Acepto " . Html::anchor($doc_url, $doc->title, [
                        'target' => '_blank',
                        'class'  => 'legal-link'
                    ]) . "
                </label>";
        }

        return "<div class='form-check mb-2'>
                    {$checkbox}
                    {$status_msg}
                </div>";
    }



    /**
     * REGISTRAR CONSENTIMIENTO DE USUARIO
     *
     * @param int    $user_id     ID del usuario
     * @param int    $document_id ID del documento
     * @param bool   $accepted    true = aceptado, false = rechazado
     * @param array  $extra       Datos adicionales (ej. newsletter, ip, etc.)
     * @param string $channel     Canal de aceptación (ej: web, app, socio, proveedor)
     * @return Model_User_Consent
     */
    public static function register_consent($user_id, $document_id, $accepted = true, $extra = [], $channel = 'web')
    {
        $doc = Model_Legal_Document::find($document_id);

        $consent = Model_User_Consent::forge();
        $consent->user_id     = $user_id;
        $consent->document_id = $document_id;
        $consent->accepted    = $accepted ? 0 : 1; // 0 = aceptado
        $consent->ip_address  = Input::real_ip();
        $consent->user_agent  = Input::user_agent();
        $consent->channel     = $channel;
        $consent->extra       = $extra ? json_encode($extra) : null;
        $consent->accepted_at = time();
        $consent->version     = $doc ? $doc->version : '1.0'; // guardar versión vigente

        $consent->save();

        \Log::info("[LEGAL] Consentimiento registrado. User={$user_id}, Doc={$document_id}, Versión={$consent->version}, Accepted={$consent->accepted}, Channel={$channel}");

        return $consent;
    }


    /**
     * VERIFICAR SI EL USUARIO YA ACEPTÓ UN DOCUMENTO
     *
     * @param int    $user_id   ID del usuario
     * @param string $shortcode Shortcode del documento
     * @return bool
     */
    public static function has_accepted($user_id, $shortcode)
    {
        $doc = self::get_document($shortcode);
        if (!$doc) {
            return false; // No hay documento activo
        }

        $consent = Model_User_Consent::query()
            ->where('user_id', $user_id)
            ->where('document_id', $doc->id)
            ->where('version', $doc->version)
            ->where('accepted', 0) // 0 = aceptado
            ->get_one();

        return $consent ? true : false;
    }

    /**
     * VERIFICAR ESTADO DE CONSENTIMIENTO
     *
     * @param int    $user_id   ID del usuario
     * @param string $shortcode Shortcode del documento
     * @return array ['status' => 'accepted|outdated|missing', 'doc' => $doc]
     */
    public static function check_consent_status($user_id, $shortcode)
    {
        $doc = self::get_document($shortcode);
        if (!$doc) {
            return ['status' => 'missing', 'doc' => null];
        }

        $consent = Model_User_Consent::query()
            ->where('user_id', $user_id)
            ->where('document_id', $doc->id)
            ->order_by('accepted_at', 'desc')
            ->get_one();

        if (!$consent) {
            return ['status' => 'missing', 'doc' => $doc];
        }

        if ($consent->version != $doc->version || $consent->accepted != 0) {
            return ['status' => 'outdated', 'doc' => $doc]; // Versión nueva o rechazado
        }

        return ['status' => 'accepted', 'doc' => $doc];
    }

    /**
     * OBTENER HISTÓRICO DE CONSENTIMIENTOS DE UN USUARIO
     *
     * @param int $user_id
     * @return array
     */
    public static function user_history($user_id)
    {
        return Model_User_Consent::query()
            ->related('document')
            ->where('user_id', $user_id)
            ->order_by('accepted_at', 'desc')
            ->get();
    }
    



    ///////////////////////////////////////////////////////
    // PREFERENCIAS DE COOKIES
    ///////////////////////////////////////////////////////
    
        /**
     * DEBUG COOKIES
     *
     * Muestra en pantalla lo que se está guardando en cookies (usuario o anónimo).
     * SOLO USAR EN PRUEBAS.
     */

         ///////////////////////////////////////////////////////
    // PREFERENCIAS DE COOKIES
    ///////////////////////////////////////////////////////

    /**
     * RENDERIZAR PREFERENCIAS DE COOKIES PARA UN USUARIO O ANÓNIMO
     *
     * @param int|null $user_id
     * @return string HTML con los switches de cookies
     */
    public static function render_cookies_preferences_for_user($user_id = null)
    {
        $prefs = self::get_cookies_preferences($user_id);

        $necessary       = $prefs ? (int)$prefs->necessary : 1;
        $analytics       = $prefs ? (int)$prefs->analytics : 1;
        $marketing       = $prefs ? (int)$prefs->marketing : 1;
        $personalization = $prefs ? (int)$prefs->personalization : 1;

        $already_saved   = $prefs ? true : false;

        $html  = "<div class='cookies-preferences border p-3 rounded'>";
        $html .= "<h5 class='mb-3'><i class='fa fa-cookie'></i> Preferencias de Cookies</h5>";

        // ============================
        // NECESSARY
        // ============================
        if ($already_saved) {
            // Ya guardó → mostrar estado real pero deshabilitado
            $html .= "<div class='form-check mb-3'>
                        <input class='form-check-input' type='checkbox' 
                            id='cookies_necessary' 
                            name='cookies[necessary]' 
                            value='{$necessary}' "
                            .($necessary == 0 ? 'checked' : '')." disabled>
                        <label class='form-check-label fw-bold' for='cookies_necessary'>
                            Cookies necesarias (siempre activas)
                        </label>
                        <small class='form-text text-muted d-block'>
                            Se registran datos técnicos como tu <b>IP</b> y <b>navegador</b> 
                            para garantizar el funcionamiento básico del sistema.
                        </small>
                    </div>";
        } else {
            // Primera vez → obligatorio
            $html .= "<div class='form-check mb-3'>
                        <input class='form-check-input' type='checkbox' 
                            id='cookies_necessary' 
                            name='cookies[necessary]' 
                            value='0' required>
                        <label class='form-check-label fw-bold' for='cookies_necessary'>
                            Cookies necesarias (obligatorio aceptarlas para continuar)
                        </label>
                        <small class='form-text text-muted d-block'>
                            Debes aceptar estas cookies para poder guardar tus preferencias.
                        </small>
                    </div>";
        }

        // ============================
        // ANALYTICS
        // ============================
        $html .= "<div class='form-check form-switch mb-2'>
                    <input class='form-check-input' type='checkbox' 
                        id='cookies_analytics' 
                        name='cookies[analytics]' 
                        value='0' ".($analytics == 0 ? 'checked' : '').">
                    <label class='form-check-label' for='cookies_analytics'>
                        Permitir cookies de analítica
                    </label>
                </div>";

        // ============================
        // MARKETING
        // ============================
        $html .= "<div class='form-check form-switch mb-2'>
                    <input class='form-check-input' type='checkbox' 
                        id='cookies_marketing' 
                        name='cookies[marketing]' 
                        value='0' ".($marketing == 0 ? 'checked' : '').">
                    <label class='form-check-label' for='cookies_marketing'>
                        Permitir cookies de marketing
                    </label>
                </div>";

        // ============================
        // PERSONALIZACIÓN
        // ============================
        $html .= "<div class='form-check form-switch mb-2'>
                    <input class='form-check-input' type='checkbox' 
                        id='cookies_personalization' 
                        name='cookies[personalization]' 
                        value='0' ".($personalization == 0 ? 'checked' : '').">
                    <label class='form-check-label' for='cookies_personalization'>
                        Permitir cookies de personalización
                    </label>
                </div>";

        $html .= "</div>";

        \Log::info("[LEGAL][COOKIES] Renderizado formulario cookies para User=".($user_id ?: 'ANON')." Primera vez=".($already_saved ? 'no' : 'sí')." Necesarias={$necessary}");

        return $html;
    }


    /**
 * RENDERIZAR PREFERENCIAS DE COOKIES (PERFIL DEL USUARIO)
 *
 * Muestra el estado real de lo que el usuario tiene guardado.
 */
/**
 * RENDERIZAR PREFERENCIAS DE COOKIES EN PERFIL
 *
 * Similar a render_cookies_preferences_for_user pero:
 * - Siempre muestra "necessary" como activas y deshabilitadas.
 * - Analytics, marketing y personalización sí pueden modificarse.
 * - No se bloquea por primera vez (esto es solo lectura/edición dentro del perfil).
 */
/**
 * RENDERIZAR PREFERENCIAS DE COOKIES EN PERFIL
 *
 * Lógica:
 * - 0 = aceptado
 * - 1 = rechazado
 * - Necessary siempre 0 (aceptado) y bloqueado
 */
public static function render_cookies_preferences_profile($user_id = null)
{
    $prefs = self::get_cookies_preferences($user_id);

    $necessary       = $prefs ? (int)$prefs->necessary : 0; // siempre 0 = aceptado
    $analytics       = $prefs ? (int)$prefs->analytics : 1;
    $marketing       = $prefs ? (int)$prefs->marketing : 1;
    $personalization = $prefs ? (int)$prefs->personalization : 1;

    $html  = "<div class='cookies-preferences border p-3 rounded'>";
    $html .= "<h5 class='mb-3'><i class='fa fa-cookie'></i> Preferencias de Cookies</h5>";

    // ============================
    // NECESSARY → siempre aceptado (0), bloqueado
    // ============================
    $html .= "<div class='form-check mb-3'>
                <input class='form-check-input' type='checkbox' 
                    id='cookies_necessary_profile' 
                    value='0' checked disabled>
                <label class='form-check-label fw-bold' for='cookies_necessary_profile'>
                    Cookies necesarias (siempre activas)
                </label>
                <small class='form-text text-muted d-block'>
                    Estas cookies capturan tu <b>IP</b> y <b>navegador</b> para garantizar 
                    el funcionamiento básico del sistema. No se pueden desactivar.
                </small>
              </div>";

    // ============================
    // ANALYTICS
    // ============================
    $html .= "<div class='form-check form-switch mb-2'>
                <input class='form-check-input' type='checkbox' 
                    id='cookies_analytics_profile' 
                    name='cookies[analytics]' 
                    value='0' ".($analytics == 0 ? 'checked' : '').">
                <label class='form-check-label' for='cookies_analytics_profile'>
                    Permitir cookies de analítica
                </label>
              </div>";

    // ============================
    // MARKETING
    // ============================
    $html .= "<div class='form-check form-switch mb-2'>
                <input class='form-check-input' type='checkbox' 
                    id='cookies_marketing_profile' 
                    name='cookies[marketing]' 
                    value='0' ".($marketing == 0 ? 'checked' : '').">
                <label class='form-check-label' for='cookies_marketing_profile'>
                    Permitir cookies de marketing
                </label>
              </div>";

    // ============================
    // PERSONALIZACIÓN
    // ============================
    $html .= "<div class='form-check form-switch mb-2'>
                <input class='form-check-input' type='checkbox' 
                    id='cookies_personalization_profile' 
                    name='cookies[personalization]' 
                    value='0' ".($personalization == 0 ? 'checked' : '').">
                <label class='form-check-label' for='cookies_personalization_profile'>
                    Permitir cookies de personalización
                </label>
              </div>";

    $html .= "</div>";

    \Log::info("[LEGAL][COOKIES] Renderizado perfil cookies para User=".($user_id ?: 'ANON')." Necessary=0 (aceptado)");

    return $html;
}






    /**
     * DEBUG COOKIES
     *
     * Muestra en pantalla lo que se está guardando en cookies (usuario o anónimo).
     * SOLO USAR EN PRUEBAS.
     */
    public static function debug_cookies($prefs = [], $user_id = null)
    {
        echo "<pre style='background:#111;color:#0f0;padding:10px;'>";
        echo "=== DEBUG COOKIES ===\n";
        echo "UserID: " . ($user_id ?: 'ANON') . "\n";
        echo "Prefs recibidas: " . json_encode($prefs) . "\n";

        try {
            if ($user_id) {
                $model = \Model_User_Cookies_Preference::query()
                    ->where('user_id', $user_id)
                    ->get_one();
                if (!$model) {
                    $model = \Model_User_Cookies_Preference::forge();
                    $model->user_id     = $user_id;
                    $model->accepted_at = time();
                    echo "-> Creando nuevo registro en user_cookies_preferences\n";
                } else {
                    echo "-> Usando registro existente en user_cookies_preferences ID={$model->id}\n";
                }
            } else {
                $token = self::getAnonToken();
                echo "-> Token anónimo: {$token}\n";

                $model = \Model_Anonymous_Cookies_Accept::query()
                    ->where('token', $token)
                    ->get_one();

                if (!$model) {
                    $model = \Model_Anonymous_Cookies_Accept::forge();
                    $model->token       = $token;
                    $model->accepted_at = time();
                    echo "-> Creando nuevo registro en anonymous_cookies_accepts\n";
                } else {
                    echo "-> Usando registro existente en anonymous_cookies_accepts ID={$model->id}\n";
                }
            }

            // Asignar valores
            $model->necessary       = 0;
            $model->analytics       = isset($prefs['analytics']) ? $prefs['analytics'] : 1;
            $model->marketing       = isset($prefs['marketing']) ? $prefs['marketing'] : 1;
            $model->personalization = isset($prefs['personalization']) ? $prefs['personalization'] : 1;
            $model->updated_at      = time();
            $model->ip_address      = \Input::real_ip();
            $model->user_agent      = \Input::user_agent();

            echo "-> Valores antes de guardar:\n";
            print_r([
                'necessary'       => $model->necessary,
                'analytics'       => $model->analytics,
                'marketing'       => $model->marketing,
                'personalization' => $model->personalization,
                'ip_address'      => $model->ip_address,
                'user_agent'      => $model->user_agent,
                'token'           => property_exists($model, 'token') ? $model->token : null,
                'user_id'         => property_exists($model, 'user_id') ? $model->user_id : null,
            ]);

            if ($model->save()) {
                echo " Guardado correctamente en " . get_class($model) . " con ID={$model->id}\n";
            } else {
                echo " Error al guardar en " . get_class($model) . "\n";
            }
        } catch (\Exception $e) {
            echo " Excepción: " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine() . "\n";
        }

        echo "</pre>";
        exit;
    }

    
     /**
     * OBTENER TOKEN PARA ANÓNIMOS
     */
    protected static function getAnonToken()
    {
        $token = \Cookie::get('anon_cookie_token');
        if (!$token) {
            $token = sha1(uniqid(mt_rand(), true));
            \Cookie::set('anon_cookie_token', $token, 60*60*24*365); // 1 año
        }
        return $token;
    }

    /**
     * OBTENER PREFERENCIAS DE COOKIES (USER o ANÓNIMO)
     */
    public static function get_cookies_preferences($user_id = null)
    {
        if ($user_id) {
            return \Model_User_Cookies_Preference::query()
                ->where('user_id', $user_id)
                ->get_one();
        } else {
            $token = self::getAnonToken();
            return \Model_Anonymous_Cookies_Accept::query()
                ->where('token', $token)
                ->get_one();
        }
    }

    /**
 * ACTUALIZAR PREFERENCIAS DE COOKIES
 */
public static function update_cookies_preferences($prefs = [], $user_id = null)
{
    \Log::info("[LEGAL][COOKIES] Iniciando actualización de cookies. UserID=".($user_id ?: 'ANON')." Prefs=".json_encode($prefs));

    try {
        // Forzar revalidación con Auth en caso de estar en template
        if (\Auth::check()) {
            $user_id = \Auth::get('id');
            \Log::info("[LEGAL][COOKIES] Auth detectado en template. Forzando user_id={$user_id}");
        }

        if ($user_id) {
            // ==========================
            // Usuario autenticado
            // ==========================
            $model = \Model_User_Cookies_Preference::query()
                ->where('user_id', $user_id)
                ->get_one();

            if (!$model) {
                $model = \Model_User_Cookies_Preference::forge();
                $model->user_id     = $user_id;
                $model->accepted_at = time();
                \Log::info("[LEGAL][COOKIES] Creando nuevo registro en user_cookies_preferences para User={$user_id}");
            } else {
                \Log::info("[LEGAL][COOKIES] Usando registro existente en user_cookies_preferences ID={$model->id}");
            }

            // Buscar si hay un registro anónimo con el token actual y migrar
            $token = self::getAnonToken();
            $anon  = \Model_Anonymous_Cookies_Accept::query()
                ->where('token', $token)
                ->get_one();

            if ($anon) {
                \Log::info("[LEGAL][COOKIES] Migrando preferencias de anónimo a user_id={$user_id}");
                $model->necessary       = $anon->necessary;
                $model->analytics       = $anon->analytics;
                $model->marketing       = $anon->marketing;
                $model->personalization = $anon->personalization;
                $model->accepted_at     = $anon->accepted_at ?: time();
                $model->ip_address      = $anon->ip_address;
                $model->user_agent      = $anon->user_agent;
                $model->updated_at      = time();

                $anon->delete();
                \Log::info("[LEGAL][COOKIES] Registro anónimo eliminado tras migración.");
            }

        } else {
            // ==========================
            // Visitante anónimo
            // ==========================
            $token = self::getAnonToken();
            \Log::info("[LEGAL][COOKIES] Usando token anónimo={$token}");

            $model = \Model_Anonymous_Cookies_Accept::query()
                ->where('token', $token)
                ->get_one();

            if (!$model) {
                $model = \Model_Anonymous_Cookies_Accept::forge();
                $model->token       = $token;
                $model->accepted_at = time();
                \Log::info("[LEGAL][COOKIES] Creando nuevo registro en anonymous_cookies_accepts con token={$token}");
            } else {
                \Log::info("[LEGAL][COOKIES] Usando registro existente en anonymous_cookies_accepts ID={$model->id}");
            }
        }

        // ==========================
        // Guardar valores
        // ==========================

        // Solo actualizamos "necessary" si viene en el payload (modal inicial).
        if (isset($prefs['necessary'])) {
            $model->necessary = (int)$prefs['necessary'];
        }

        // Estos sí pueden actualizarse siempre
        $model->analytics       = isset($prefs['analytics']) ? (int)$prefs['analytics'] : $model->analytics;
        $model->marketing       = isset($prefs['marketing']) ? (int)$prefs['marketing'] : $model->marketing;
        $model->personalization = isset($prefs['personalization']) ? (int)$prefs['personalization'] : $model->personalization;
        $model->updated_at      = time();
        $model->ip_address      = \Input::real_ip();
        $model->user_agent      = \Input::user_agent();

        if ($model->save()) {
            \Log::info("[LEGAL][COOKIES] Preferencias guardadas correctamente. Tabla=".get_class($model)." ID={$model->id}");
        } else {
            \Log::error("[LEGAL][COOKIES] Error al guardar registro en ".get_class($model));
        }

        return $model;
    } catch (\Exception $e) {
        \Log::error("[LEGAL][COOKIES] Excepción: ".$e->getMessage()." en ".$e->getFile().":".$e->getLine());
        return null;
    }
}


    /**
     * MIGRAR PREFERENCIAS DE COOKIES DE ANÓNIMO A USUARIO LOGUEADO
     *
     * - Copia las preferencias guardadas con token anónimo
     * - Las pasa al registro user_cookies_preferences
     * - Elimina el registro anónimo para evitar duplicados
     *
     * @param int $user_id  ID del usuario autenticado
     * @return bool
     */
    public static function migrateAnonToUser($user_id)
    {
        try {
            if (!$user_id) {
                \Log::warning("[LEGAL][COOKIES] No se pudo migrar: user_id vacío.");
                return false;
            }

            // Obtener token anónimo actual
            $token = self::getAnonToken();
            if (!$token) {
                \Log::info("[LEGAL][COOKIES] No existe token anónimo, no hay nada que migrar.");
                return false;
            }

            // Buscar preferencias anónimas
            $anon = \Model_Anonymous_Cookies_Accept::query()
                ->where('token', $token)
                ->get_one();

            if (!$anon) {
                \Log::info("[LEGAL][COOKIES] No hay registro anónimo con token={$token}, no se migra.");
                return false;
            }

            // Buscar o crear preferencias del usuario
            $userPrefs = \Model_User_Cookies_Preference::query()
                ->where('user_id', $user_id)
                ->get_one();

            if (!$userPrefs) {
                $userPrefs = \Model_User_Cookies_Preference::forge();
                $userPrefs->user_id = $user_id;
                $userPrefs->accepted_at = $anon->accepted_at ?: time();
                \Log::info("[LEGAL][COOKIES] Creando nuevo registro en user_cookies_preferences para User={$user_id}");
            } else {
                \Log::info("[LEGAL][COOKIES] Actualizando registro existente en user_cookies_preferences ID={$userPrefs->id}");
            }

            // Copiar valores
            $userPrefs->necessary       = $anon->necessary;
            $userPrefs->analytics       = $anon->analytics;
            $userPrefs->marketing       = $anon->marketing;
            $userPrefs->personalization = $anon->personalization;
            $userPrefs->updated_at      = time();
            $userPrefs->ip_address      = $anon->ip_address;
            $userPrefs->user_agent      = $anon->user_agent;

            // Guardar en tabla de usuario
            $userPrefs->save();

            // Eliminar registro anónimo
            $anon->delete();
            \Log::info("[LEGAL][COOKIES] Migración completada: token {$token} → user_id {$user_id}");

            return true;
        } catch (\Exception $e) {
            \Log::error("[LEGAL][COOKIES] Error en migración de anónimo a usuario: ".$e->getMessage());
            return false;
        }
    }




    /**
     * RENDERIZAR FORM DE COOKIES (con switches)
     */
    public static function render_cookies_form($user_id = null)
    {
        $prefs = self::get_cookies_preferences($user_id);

        $analytics       = $prefs ? $prefs->analytics : 1;
        $marketing       = $prefs ? $prefs->marketing : 1;
        $personalization = $prefs ? $prefs->personalization : 1;

        $form  = "<div class='cookies-preferences'>";
        $form .= "<div class='form-check form-switch'>
                    <input class='form-check-input' type='checkbox' id='cookies_analytics' name='cookies[analytics]' value='0' ".($analytics == 0 ? 'checked' : '').">
                    <label class='form-check-label' for='cookies_analytics'>Permitir cookies de analítica</label>
                </div>";
        $form .= "<div class='form-check form-switch'>
                    <input class='form-check-input' type='checkbox' id='cookies_marketing' name='cookies[marketing]' value='0' ".($marketing == 0 ? 'checked' : '').">
                    <label class='form-check-label' for='cookies_marketing'>Permitir cookies de marketing</label>
                </div>";
        $form .= "<div class='form-check form-switch'>
                    <input class='form-check-input' type='checkbox' id='cookies_personalization' name='cookies[personalization]' value='0' ".($personalization == 0 ? 'checked' : '').">
                    <label class='form-check-label' for='cookies_personalization'>Permitir cookies de personalización</label>
                </div>";
        $form .= "</div>";

        return $form;
    }

    /**
     * NORMALIZAR PREFERENCIAS DE COOKIES
     *
     * Convierte las preferencias recibidas (formulario HTML o JSON) en el formato esperado.
     *
     * @param array $prefs Array con las preferencias recibidas
     * @return array Array normalizado con keys: necessary, analytics, marketing, personalization
     */
    /**este es para normalizar en los perfiles y no tener que meter mas codigo por si creo otro perfil y lo uso por Html */
    public static function normalize_cookies_prefs($prefs)
    {
        // Si ya vino como JSON completo (Vue), devolver directo
        if (isset($prefs['necessary']) && isset($prefs['analytics']) && isset($prefs['marketing']) && isset($prefs['personalization'])) {
            return [
                'necessary'       => (int)$prefs['necessary'],
                'analytics'       => (int)$prefs['analytics'],
                'marketing'       => (int)$prefs['marketing'],
                'personalization' => (int)$prefs['personalization'],
            ];
        }

        // Caso formulario HTML → solo checkboxes marcados
        // Aquí "1" = rechazado, "0" = aceptado
        return [
            'necessary'       => 0, // siempre aceptadas
            'analytics'       => isset($prefs['analytics']) ? 0 : 1,
            'marketing'       => isset($prefs['marketing']) ? 0 : 1,
            'personalization' => isset($prefs['personalization']) ? 0 : 1,
        ];
    }




    ///////////////////////////////////////////////////////
    // EXPORTAR DOCUMENTOS LEGALES A PDF   
    ///////////////////////////////////////////////////////

    /**
     * EXPORTAR DOCUMENTO LEGAL A PDF
     *
     * @param Model_Legal_Document $doc      Documento a exportar
     * @param string|null          $filename Nombre del archivo (opcional)
     * @param bool                 $forceDownload true = forzar descarga, false = vista previa en navegador
     * @return void
     */
    public static function export_pdf($doc, $filename = null, $forceDownload = true)
    {
        // 1. Validación de documento
        if (!$doc || $doc->allow_download != 0) {
            \Session::set_flash('error','Documento no disponible para descarga.');
            \Response::redirect('admin/legal/documentos');
        }

        // 2. Preparar datos
        $data['doc'] = $doc;
        $data['is_pdf_export'] = true;

        // 3. Renderizar la vista PDF
        $html = \View::forge('admin/legal/documentos/imprimir', $data)->render();

        // 4. Instanciar Dompdf
        $dompdf = new \Dompdf\Dompdf();

        // 5. Configurar opciones
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true); // soporte para imágenes remotas
        $options->set('defaultFont', 'sans-serif');
        $dompdf->setOptions($options);

        // 6. Cargar HTML
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        // 7. Renderizar
        try {
            $dompdf->render();
        } catch (\Exception $e) {
            \Log::error('Dompdf render error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine());
            \Session::set_flash('error','Ocurrió un error al generar el PDF.');
            \Response::redirect('admin/legal/documentos');
        }

        // 8. Nombre de archivo
        $filename = $filename ?: 'documento_legal_'.$doc->id.'.pdf';

        // 9. Salida
        $dompdf->stream($filename, ["Attachment" => $forceDownload ? 1 : 0]);
        exit();
    }

    /**
     * Genera pdf para el cliente
     */
    public static function export_pdf_frontend($doc, $filename = null, $forceDownload = true)
    {
        if (!$doc || $doc->allow_download != 0) {
            throw new \HttpNotFoundException("Documento no disponible para descarga.");
        }

        $data['doc'] = $doc;
        $data['is_pdf_export'] = true;

        // Renderizar vista exclusiva del cliente
        $html = \View::forge('documentos/legal/imprimir', $data)->render();

        $dompdf = new \Dompdf\Dompdf();
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $filename = $filename ?: 'documento_'.$doc->shortcode.'.pdf';
        $dompdf->stream($filename, ["Attachment" => $forceDownload ? 1 : 0]);
        exit();
    }



}
