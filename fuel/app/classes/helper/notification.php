<?php
/**
 * HELPER: NOTIFICATION
 * FUNCIONES PARA CREAR NOTIFICACIONES DEL SISTEMA
 * VERSIÓN JULIO 2025
 */

class Helper_Notification
{
    /**
     * CREA UNA NUEVA NOTIFICACIÓN EN EL SISTEMA
     * @param array $data  Arreglo asociativo con los campos necesarios
     * @return int|null    ID de la notificación creada o NULL si falló
     */
    public static function create_notification($data = array())
    {
        try {
            // CAMPOS POR DEFECTO
            $defaults = array(
                'user_id'       => null,  // NULL para notificación global
                'user_group_id' => null,  // NULL si no aplica
                'type'          => '',
                'title'         => '',
                'message'       => '',
                'url'           => null,
                'status'        => 0, // 0 = pendiente, 1 = leído
                'priority'      => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'read_at'       => null,
                'expires_at'    => null,
                'icon'          => null,
                'active'        => 1,
                'params'        => null,
                'created_by'    => (\Auth::check() ? \Auth::get('id') : null)
            );
            // MEZCLA LOS CAMPOS DEL USUARIO CON LOS POR DEFECTO
            $data = array_merge($defaults, $data);

            // CREA EL OBJETO DE NOTIFICACIÓN
            $notif = Model_Notification::forge($data);

            // GUARDA EN BD
            if ($notif->save()) {
                \Log::info('[NOTIFICATION] Notification created for user_id: ' . ($data['user_id'] ?? 'NULL') . ', title: ' . $data['title']);
                return $notif->id;
            } else {
                \Log::error('[NOTIFICATION] Failed to create notification: ' . json_encode($data));
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('[NOTIFICATION] Exception: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * DISPARA UNA NOTIFICACIÓN DE EVENTO ADMINISTRABLE.
     * @param string $event_key
     * @param array $vars
     */
    public static function notify_event($event_key, $vars = [])
    {
        // LOG INICIO DEL HELPER
        \Log::info('[Helper_Notification] notify_event', ['event_key' => $event_key, 'vars' => $vars]);

        $config = Model_Notification_Events_Config::query()
            ->where('event_key', $event_key)
            ->where('active', 1)
            ->get_one();

        if (!$config) {
            \Log::warning('[Helper_Notification] No se encontró configuración para el evento', ['event_key' => $event_key]);
            return false;
        }

  	

        $replace = function($text) use ($vars) {
            foreach ($vars as $k => $v) {
                $text = str_replace('{' . $k . '}', $v, $text);
            }
            return $text;
        };

        // ===== REEMPLAZA VARIABLES EN MENSAJE, TÍTULO Y URL =====
		$title   = self::parse_placeholders($config->title, $vars);
		$message = self::parse_placeholders($config->message, $vars);
		$url     = self::parse_placeholders($config->url_pattern, $vars);
        $icon    = $config->icon ?: 'fa fa-bell';
        $priority = $config->priority ?: 1;

        $notification = Model_Notification::forge([
            'type'       => 'evento',
            'title'      => $title,
            'message'    => $message,
            'url'        => $url,
            'icon'       => $icon,
            'priority'   => $priority,
            'params'     => null,
            'active'     => 1,
            'created_by' => Auth::get('id'),
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $notification->save();

        // DETERMINA DESTINATARIOS (targets)
        $dest_users = [];
        foreach ($config->targets as $target) {
            if ($target->group_id) {
                $usuarios = Model_User::query()->where('group', $target->group_id)->get();
                foreach ($usuarios as $user) {
                    $dest_users[] = $user->id;
                }
            }
            if ($target->user_id) {
                $dest_users[] = $target->user_id;
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

        \Log::info('[Helper_Notification] Notificación de evento creada', [
            'event_key' => $event_key,
            'notification_id' => $notification->id,
            'destinatarios' => $dest_users
        ]);

        return true;
    }

    /**
     * PARSEA LOS PLACEHOLDERS EN UN TEXTO
     * Reemplaza {KEY} por el valor correspondiente en $vars.
     * Si no se encuentra, deja (N/D).
     * @param string $text
     * @param array $vars
     * @return string
     */
    public static function parse_placeholders($text, $vars = [])
    {
        foreach ($vars as $key => $value) {
            $text = str_replace('{' . strtoupper($key) . '}', $value, $text);
        }
        // Reemplaza cualquier placeholder restante por (N/D)
        $text = preg_replace('/{([A-Z0-9_]+)}/', '(N/D)', $text);
        return $text;
    }

}
