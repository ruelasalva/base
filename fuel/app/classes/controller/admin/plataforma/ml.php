<?php
/**
 * CONTROLADOR DE MERCADO LIBRE (PLATAFORMA)
 */

class Controller_Admin_Plataforma_Ml extends Controller_Admin
{
    /**
     * LISTADO PRINCIPAL
     */
    public function action_index()
    {
        $configuraciones = Model_Plataforma_Ml_Configuration::query()
            ->order_by('id', 'desc')
            ->get();

        $this->template->title = 'Mercado Libre';
        $this->template->content = View::forge('admin/plataformas/ml/index', [
            'configs' => $configuraciones,
        ], false);
    }

    public function action_panel($id = null)
{
    $config = Model_Plataforma_Ml_Configuration::find($id);

    if (!$config) {
        Session::set_flash('error', 'Cuenta de Mercado Libre no encontrada.');
        return Response::redirect('admin/plataforma/ml');
    }

    // KPIs básicos
    $total_products = Model_Plataforma_Ml_Product::query()
        ->where('configuration_id', $config->id)
        ->count();

    $published = Model_Plataforma_Ml_Product::query()
        ->where('configuration_id', $config->id)
        ->where('ml_item_id', 'is not', null)
        ->where('ml_enabled', 1)
        ->count();

    $inactive = Model_Plataforma_Ml_Product::query()
        ->where('configuration_id', $config->id)
        ->where('ml_enabled', 0)
        ->count();

    $linked_only = Model_Plataforma_Ml_Product::query()
        ->where('configuration_id', $config->id)
        ->where('ml_item_id', null)
        ->count();

    $errors_7d = Model_Plataforma_Ml_Error::query()
        ->where('configuration_id', $config->id)
        ->where('created_at', '>=', time() - 7 * 86400)
        ->count();

    $last_logs = Model_Plataforma_Ml_Log::query()
        ->where('configuration_id', $config->id)
        ->order_by('id', 'desc')
        ->limit(5)
        ->get();

    $this->template->title = 'Panel Mercado Libre';

    $this->template->content = View::forge('admin/plataformas/ml/panel', array(
        'config'         => $config,
        'total_products' => $total_products,
        'published'      => $published,
        'inactive'       => $inactive,
        'linked_only'    => $linked_only,
        'errors_7d'      => $errors_7d,
        'last_logs'      => $last_logs,
    ), false);
}

/**
 * SINCRONIZACIÓN MANUAL (stub inicial)
 */
public function action_sync($id = null)
{
    $config = Model_Plataforma_Ml_Configuration::find($id);

    if (!$config) {
        Session::set_flash('error', 'Cuenta de Mercado Libre no encontrada.');
        return Response::redirect('admin/plataforma/ml');
    }

    // Aquí después conectaremos con Task/cron.
    \Log::info('[ML][SYNC_MANUAL] Disparada sync manual para config_id='.$config->id);

    Session::set_flash('success', 'Sincronización manual registrada. El proceso será ejecutado por el backend.');

    return Response::redirect('admin/plataforma/ml/panel/'.$config->id);
}


    /**
     * AGREGAR CONFIGURACIÓN
     */
    public function action_agregar()
    {
        if (Input::method() == 'POST') {

            $val = Validation::forge();
            $val->add_field('name', 'Nombre', 'required|max_length[150]');
            $val->add_field('client_id', 'Client ID', 'required');
            $val->add_field('client_secret', 'Client Secret', 'required');
            $val->add_field('redirect_uri', 'Redirect URI', 'required');
            $val->add_field('mode', 'Mode', 'required');

            if ($val->run()) {

                $config = Model_Plataforma_Ml_Configuration::forge([
                    'name'          => Input::post('name'),
                    'client_id'     => Input::post('client_id'),
                    'client_secret' => Input::post('client_secret'),
                    'redirect_uri'  => Input::post('redirect_uri'),
                    'mode'          => Input::post('mode'),
                    'account_email' => Input::post('account_email'),
                    'expires_in_last' => null,

                    // Campos de control
                    'is_active'     => 0,
                    'last_sync_catalog'     => null,
                    'last_sync_orders'      => null,
                    'last_sync_promotions'  => null,
                    'last_sync_webhooks'    => null,
                ]);

                if ($config->save()) {
                    Session::set_flash('success', 'Configuración creada correctamente. Ahora conecta la cuenta.');
                    return Response::redirect('admin/plataforma/ml');
                }

                Session::set_flash('error', 'Error al guardar la configuración.');
            }
        }

        $this->template->title = 'Agregar Cuenta ML';
        $this->template->content = View::forge('admin/plataformas/ml/agregar', [], false);
    }

    /**
     * EDITAR CONFIG
     */
    public function action_editar($id = null)
    {
        $config = Model_Plataforma_Ml_Configuration::find($id);

        if (!$config) {
            Session::set_flash('error', 'Configuración no encontrada.');
            return Response::redirect('admin/plataforma/ml');
        }

        if (Input::method() == 'POST') {

            $config->name          = Input::post('name');
            $config->client_id     = Input::post('client_id');
            $config->client_secret = Input::post('client_secret');
            $config->redirect_uri  = Input::post('redirect_uri');
            $config->mode          = Input::post('mode');
            $config->account_email = Input::post('account_email');

            if ($config->save()) {
                Session::set_flash('success', 'Configuración actualizada.');
                return Response::redirect('admin/plataforma/ml');
            }

            Session::set_flash('error', 'No se pudo actualizar la configuración.');
        }

        $this->template->title = 'Editar Cuenta ML';
        $this->template->content = View::forge('admin/plataformas/ml/editar', [
            'config' => $config
        ], false);
    }

    /**
     * INICIAR OAUTH
     */
    public function action_oauth($id)
    {
        $config = Model_Plataforma_Ml_Configuration::find($id);

        if (!$config) {
            Session::set_flash('error', 'Configuración no encontrada.');
            return Response::redirect('admin/plataforma/ml');
        }

        /**
         * CORRECCIÓN: Agregar "state" con el ID de la cuenta
         */
        $params = http_build_query([
            'response_type' => 'code',
            'client_id'     => $config->client_id,
            'redirect_uri'  => $config->redirect_uri,
            'state'         => $config->id,
        ]);

        return Response::redirect("https://auth.mercadolibre.com.mx/authorization?{$params}");
    }

    /**
     * CALLBACK OAUTH2
     */
    public function action_callback()
    {
        $code  = Input::get('code', null);
        $state = Input::get('state', null);   // <-- CORRECCIÓN CRÍTICA

        if (!$code || !$state) {
            Session::set_flash('error', 'Respuesta inválida de Mercado Libre.');
            return Response::redirect('admin/plataforma/ml');
        }

        /**
         * CORRECCIÓN: buscar la configuración REAL por ID (state)
         */
        $config = Model_Plataforma_Ml_Configuration::find($state);

        if (!$config) {
            Session::set_flash('error', 'No se identificó la cuenta ML.');
            return Response::redirect('admin/plataforma/ml');
        }

        try {
            $client = new Service_Ml_Client($config);
            $tokens = $client->exchange_code_for_tokens($code);

            $config->access_token     = $tokens['access_token'];
            $config->refresh_token    = $tokens['refresh_token'];
            $config->token_expires_at = time() + $tokens['expires_in'];
            $config->expires_in_last  = $tokens['expires_in'];

            // Opcional: obtener datos del usuario conectado
            if (isset($tokens['user_id'])) {
                $config->user_id_ml = $tokens['user_id'];
            }

            $config->is_active = 1;
            $config->save();

            Session::set_flash('success', 'Cuenta conectada exitosamente.');
        }
        catch (Exception $e) {
            \Log::error("[ML][CALLBACK] Error: ".$e->getMessage());
            Session::set_flash('error', 'Error al procesar la conexión: '.$e->getMessage());
        }

        return Response::redirect('admin/plataforma/ml');
    }

    /**
     * ELIMINAR CONFIG
     */
    public function action_eliminar($id)
    {
        $config = Model_Plataforma_Ml_Configuration::find($id);

        if (!$config) {
            Session::set_flash('error', 'Configuración no encontrada.');
            return Response::redirect('admin/plataforma/ml');
        }

        $config->delete();

        Session::set_flash('success', 'Configuración eliminada correctamente.');
        return Response::redirect('admin/plataforma/ml');
    }
}
