<?php
/**
 * HELPER EMAIL
 *
 * CENTRALIZA EL ENVÍO DE CORREOS BASADOS EN ROLES Y PLANTILLAS CONFIGURADAS EN BD.
 */
class Helper_Email
{
    /**
     * ENVIAR CORREO BASADO EN ROL Y CÓDIGO DE PLANTILLA
     *
     * @param string $role     ROL CONFIGURADO (EJ: ventas, soporte, contacto)
     * @param string $code     CÓDIGO ÚNICO DE LA PLANTILLA (EJ: venta_confirmacion_user)
     * @param array  $data     VARIABLES A PASAR A LA VISTA DE CORREO
     *
     * @return bool
     */
    public static function send($role, $code, $data = [])
    {
        # BUSCAR PLANTILLA EN BD
        $template = Model_Emails_Template::query()
            ->where('deleted', 0)
            ->where('role', $role)
            ->where('code', $code)
            ->get_one();

        if (!$template) {
            \Log::error("[EMAIL] Plantilla no encontrada: role={$role}, code={$code}");
            return false;
        }

        # BUSCAR CONFIGURACIÓN DE ROL (FROM, REPLY-TO, TO_EMAILS)
        $role_cfg = Model_Emails_Role::query()
            ->where('deleted', 0)
            ->where('role', $role)
            ->get_one();

        if (!$role_cfg) {
            \Log::error("[EMAIL] Configuración de rol no encontrada: role={$role}");
            return false;
        }

        try {
            # === 1. CONSTRUCCIÓN DEL CUERPO ===
            if (!empty($template->content)) {
                # USAR EL CONTENIDO DE BD CON REEMPLAZO SIMPLE {{var}}
                $body = $template->content;
                foreach ($data as $key => $value) {
                    $body = str_replace('{{'.$key.'}}', $value, $body);
                }
            } else {
                # SI NO HAY CONTENIDO, USAR LA VISTA FÍSICA
                $path = \Finder::search('views', $template->view, '.php');
                if (!$path) {
                    \Log::error("[EMAIL] Vista no encontrada: {$template->view}");
                    return false;
                }
                $body = \View::forge($template->view, $data, false)->render();
            }

            # === 2. LAYOUT GENERAL ===
            $layout_path = \Finder::search('views', 'email_templates/layout', '.php');
            if (!$layout_path) {
                \Log::error("[EMAIL] Layout no encontrado: email_templates/layout");
                return false;
            }

            $html = \View::forge('email_templates/layout', [
                'subject' => $template->subject,
                'body'    => $body
            ], false)->render();

            # === 3. CONFIGURACIÓN DE CORREO ===
            $email = \Email::forge();
            $email->from($role_cfg->from_email, $role_cfg->from_name);

            if ($role_cfg->reply_to_email) {
                $email->reply_to($role_cfg->reply_to_email, $role_cfg->reply_to_name);
            }

            # DESTINATARIOS
            $to_list = explode(',', $role_cfg->to_emails);
            foreach ($to_list as $to) {
                $to = trim($to);
                if ($to !== '') {
                    $email->to($to);
                }
            }

            # ASUNTO
            $email->subject($template->subject);

            # CUERPO HTML
            $email->html_body($html, false);

            # INTENTAR ENVIAR
            $result = $email->send();

            # LOGS
            \Log::info("[EMAIL] Role={$role}, Code={$code}, To=" . implode(',', $to_list) . ", Subject={$template->subject}");
            \Log::info("[EMAIL] Rendered body: \n" . $body);

            return $result;

        } catch (\Exception $e) {
            \Log::error("[EMAIL] Error enviando correo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * TESTEAR ENVÍO DE CORREO
     *
     * ENVÍA UN CORREO DE PRUEBA USANDO EL ROL Y PLANTILLA DEFINIDA.
     * NO REQUIERE VARIABLES, SE REEMPLAZAN POR VALORES DE DEMO.
     *
     * @param string $role   ROL CONFIGURADO
     * @param string $code   CÓDIGO DE LA PLANTILLA
     * @param string $to     CORREO DESTINO DE PRUEBA
     *
     * @return bool
     */
    public static function test($role, $code, $to)
    {
        \Log::debug("[EMAIL][TEST] Iniciando prueba para role={$role}, code={$code}, to={$to}");

        # DATA DEMO
        $demo_data = [
            'name'       => 'Usuario Demo',
            'email'      => 'demo@sajor.com.mx',
            'phone'      => '333-000-0000',
            'message'    => 'Este es un correo de prueba generado automáticamente.',
            'date'       => date('Y-m-d H:i:s'),
        ];

        # ENVIAR CON LA DATA DE DEMO Y DESTINO FORZADO
        $result = self::send($role, $code, $demo_data);

        if ($result) {
            \Log::debug("[EMAIL][TEST] Correo de prueba enviado correctamente a {$to}");
        } else {
            \Log::error("[EMAIL][TEST] Falló el envío de prueba para role={$role}, code={$code}");
        }

        return $result;
    }
}
