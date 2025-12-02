<?php
class Helper_Log
{
    /**
     * REGISTRAR LOG GENERAL USANDO EL ORM
     *
     * @param string $action    Acción ejecutada (agregar, editar, eliminar, login, upload)
     * @param string $entity    Entidad afectada (proveedor, factura, orden_compra)
     * @param int    $entity_id ID de la entidad
     * @param string $level     Nivel del log (info, debug, error, warning)
     * @param string $message   Descripción del evento
     * @param array  $extra     Datos adicionales
     */
    public static function write($action, $entity, $entity_id = null, $level = 'info', $message = '', $extra = [])
    {
        $user_id  = \Auth::check() ? \Auth::get('id') : null;
        $group_id = \Auth::check() ? \Auth::get('group_id') : null;
        $ip       = \Input::ip();
        $url      = \Uri::string();
        $module   = \Request::main()->controller;

        $context = [
            'user_id'  => $user_id,
            'group_id' => $group_id,
            'ip'       => $ip,
            'url'      => $url,
            'extra'    => $extra,
        ];

        // === ARCHIVO LOG FUELPHP ===
        \Log::$level("[{$module}] {$action} {$entity} ID:{$entity_id} | {$message} | ".json_encode($context));

        // === TABLA MYSQL USANDO ORM ===
        try {
            $log = Model_System_Log::forge([
                'level'     => $level,
                'module'    => $module,
                'action'    => $action,
                'entity'    => $entity,
                'entity_id' => $entity_id,
                'message'   => $message,
                'context'   => json_encode($context, JSON_UNESCAPED_UNICODE),
                'user_id'   => $user_id,
                'group_id'  => $group_id,
                'ip'        => $ip,
                'url'       => $url,
            ]);
            $log->save();
        } catch (\Exception $e) {
            \Log::error('[Helper_Log] Error al guardar en DB: '.$e->getMessage());
        }
    }

    // === ACCESOS RÁPIDOS ===
    public static function agregar($entity, $entity_id, $msg, $extra = [])
    {
        self::write('agregar', $entity, $entity_id, 'info', $msg, $extra);
    }

    public static function editar($entity, $entity_id, $msg, $extra = [])
    {
        self::write('editar', $entity, $entity_id, 'info', $msg, $extra);
    }

    public static function eliminar($entity, $entity_id, $msg, $extra = [])
    {
        self::write('eliminar', $entity, $entity_id, 'warning', $msg, $extra);
    }
}
