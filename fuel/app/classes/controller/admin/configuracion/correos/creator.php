<?php

class Controller_Admin_Configuracion_Correos_Creator extends Controller_Admin
{

    /** 
     * LISTA DE PLANTILLAS BASE
     * Muestra las plantillas base disponibles para clonar
     * @return void
    */
    public function action_index()
    {
        # Plantillas base disponibles (hardcodeadas por ahora)
        $templates = [
            [
                'code'    => 'bienvenida',
                'title'   => 'Correo de Bienvenida',
                'subject' => '¡Bienvenido a Distribuidora Sajor!',
                'desc'    => 'Un correo de presentación para nuevos clientes.',
                'view'    => 'email_templates/base/bienvenida',
            ],
            [
                'code'    => 'recuperar_password',
                'title'   => 'Recuperación de Contraseña',
                'subject' => 'Instrucciones para recuperar tu acceso',
                'desc'    => 'Incluye un enlace temporal para resetear la clave.',
                'view'    => 'email_templates/base/recovery',
            ],
            [
                'code'    => 'notificacion',
                'title'   => 'Notificación General',
                'subject' => 'Nueva notificación en tu cuenta',
                'desc'    => 'Formato estándar para avisos e informes.',
                'view'    => 'email_templates/base/notificacion',
            ],
        ];

        $data['templates'] = $templates;
        $this->template->title = 'Creador de Plantillas';
        $this->template->content = View::forge('admin/configuracion/correos/creator/index', $data, false);
    }

    /**
     * VISTA PREVIA
     * Muestra una plantilla base con datos de ejemplo
     * @param string $code Código único de la plantilla (bienvenida, recovery, notificacion)
     * @return void
     */
    public function action_preview($code = null)
{
    \Log::debug('[Preview] Iniciando vista previa, parámetro recibido: ' . print_r($code, true));

    if (!$code) {
        \Log::error('[Preview] No se recibió código de plantilla.');
        Session::set_flash('error', 'Plantilla no encontrada.');
        Response::redirect('admin/configuracion/correos/creator');
    }

    $view = 'email_templates/base/' . $code;
    \Log::debug('[Preview] Vista esperada: ' . $view);

    if (!file_exists(APPPATH . 'views/' . $view . '.php')) {
        \Log::error('[Preview] No se encontró el archivo físico en: ' . APPPATH . 'views/' . $view . '.php');
        Session::set_flash('error', 'Vista de plantilla no encontrada.');
        Response::redirect('admin/configuracion/correos/creator');
    }

    # Datos de ejemplo
    $data = [
        'name'       => 'Ejemplo',
        'last_name'  => 'Demo',
        'email'      => 'contacto@sajor.mx',
        'phone'      => '33 3942 7070',
        'message'    => 'Este es un ejemplo de contenido dinámico en la plantilla ' . $code,
        'product'    => 'Producto Demo',
        'link'       => 'https://www.sajor.com.mx/reset/demo123'
    ];
    \Log::debug('[Preview] Datos de ejemplo preparados: ' . print_r($data, true));

    try {
        # Renderizar cuerpo de plantilla
        $body = \View::forge($view, $data, false)->render();
        \Log::info('[Preview] Renderizado de body exitoso para vista: ' . $view);
    } catch (\Exception $e) {
        \Log::error('[Preview] Error renderizando body: ' . $e->getMessage());
        Session::set_flash('error', 'Error al renderizar la vista de plantilla.');
        Response::redirect('admin/configuracion/correos/creator');
    }

    try {
        # Usar layout general
        $html = \View::forge('email_templates/layout', [
            'subject' => ucfirst($code) . ' - Vista previa',
            'body'    => $body
        ], false)->render();
        \Log::info('[Preview] Layout renderizado correctamente para plantilla: ' . $code);
    } catch (\Exception $e) {
        \Log::error('[Preview] Error renderizando layout: ' . $e->getMessage());
        Session::set_flash('error', 'Error al renderizar layout de correo.');
        Response::redirect('admin/configuracion/correos/creator');
    }

    # Mostrar resultado
    echo $html;
    \Log::debug('[Preview] Renderizado final mostrado en navegador para código: ' . $code);
    exit;
}




    /**
     * CLONAR
     * Crea una nueva plantilla en BD a partir de una base
     */
    public function action_clonar($code = '')
    {
        $map = [
            'bienvenida' => [
                'subject' => '¡Bienvenido a Distribuidora Sajor!',
                'view'    => 'email_templates/base/bienvenida'
            ],
            'recuperar_password' => [
                'subject' => 'Instrucciones para recuperar tu acceso',
                'view'    => 'email_templates/base/recovery'
            ],
            'notificacion' => [
                'subject' => 'Nueva notificación en tu cuenta',
                'view'    => 'email_templates/base/notificacion'
            ],
        ];

        if (!isset($map[$code])) {
            Session::set_flash('error', 'Plantilla base no encontrada.');
            Response::redirect('admin/configuracion/correos/templates/creator');
        }

        $viewPath = APPPATH.'views/'.$map[$code]['view'].'.php';
        $content = file_exists($viewPath) ? file_get_contents($viewPath) : '';

        $tpl = Model_Emails_Template::forge([
            'code'       => $code.'_custom_'.time(),
            'role'       => 'ventas', // puedes cambiar a otro rol si lo requieres
            'subject'    => $map[$code]['subject'],
            'view'       => $map[$code]['view'],
            'content'    => $content,
            'deleted'    => 0,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        if ($tpl->save()) {
            Session::set_flash('success', 'Plantilla clonada correctamente.');
            Response::redirect('admin/configuracion/correos/templates/editar/'.$tpl->id);
        } else {
            Session::set_flash('error', 'No se pudo clonar la plantilla.');
            Response::redirect('admin/configuracion/correos/templates/creator');
        }
    }
}
