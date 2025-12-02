<?php

/**
 * HELPER DE PERMISOS PARA CONTROL FINO DE ACCIONES POR USUARIO Y GRUPO
 */
class Helper_Permission
{
    /**
     * VERIFICA SI EL USUARIO TIENE PERMISO POR RECURSO/ACCIÓN O POR GRUPO
     *
     * @param string $recurso            // Ejemplo: 'ventas', 'clientes', 'proveedores'
     * @param string $accion             // Ejemplo: 'ver', 'editar', 'eliminar', 'crear'
     * @param int|null $user_id          // ID de usuario (opcional, toma el actual si es null)
     * @param array $grupos_permitidos   // IDs de grupos que tienen permiso por default (opcional)
     * @return bool
     */
    public static function can($resource, $action, $user_id = null)
    {
        if ($user_id === null) {
            $user_id = \Auth::get('id');
        }

        // --- 1. REVISA PERMISOS POR USUARIO (SESION)
        $user_permissions = \Session::get('user_permissions');
        $has_user_permission = false;
        if ($user_permissions && isset($user_permissions[$resource])) {
            $has_user_permission = true;
            // ¿TIENE AL MENOS UN PERMISO EN 1?
            $perm_values = $user_permissions[$resource];
            // Si todos los permisos están en 0, no tomarlo y pasar a grupo
            if (array_sum($perm_values) > 0) {
                // Si el permiso específico está activo, regresa true/false
                return !empty($perm_values[$action]) ? true : false;
            }
            // Si todos los permisos de usuario para este recurso están en 0, sigue a grupo
        }

        // --- 2. SI NO HAY PERMISO DE USUARIO O TODOS EN 0, BUSCA EN GRUPO (SESION)
        $user = \Model_User::find($user_id);
        if ($user) {
            $group_permissions = \Session::get('group_permissions');
            if ($group_permissions && isset($group_permissions[$resource][$action])) {
                return (bool)$group_permissions[$resource][$action];
            }
            // Como refuerzo, busca directo en BD (si no estuviera en sesión)
            $group_permission = \Model_Permission_Group::query()
                ->where('group_id', $user->group)
                ->where('resource', $resource)
                ->get_one();
            if ($group_permission) {
                $field = 'can_' . strtolower($action);
                return (isset($group_permission->$field) && $group_permission->$field == 1);
            }
        }

        // --- 3. SI NO HAY PERMISO POR USUARIO NI GRUPO, NO TIENE PERMISO
        return false;
    }


        // CARGA O REFRESCA LOS PERMISOS DEL USUARIO EN SESIÓN
        public static function refresh_session_permissions($user_id = null)
        {
            if ($user_id === null) {
                $user_id = \Auth::get('id');
            }
            $permissions = \Model_Permission::query()->where('user_id', $user_id)->get();
            $perms_array = array();
            foreach ($permissions as $perm) {
                $perms_array[$perm->resource] = array(
                    'view'   => (int) $perm->can_view,
                    'edit'   => (int) $perm->can_edit,
                    'delete' => (int) $perm->can_delete,
                    'create' => (int) $perm->can_create,
                );
            }
            \Session::set('user_permissions', $perms_array);
        }

        // CARGA O REFRESCA LOS PERMISOS DE GRUPO EN SESIÓN
        public static function refresh_session_group_permissions($group_id = null)
    {
        if ($group_id === null) {
            $user = \Auth::get('id') ? \Model_User::find(\Auth::get('id')) : null;
            $group_id = $user ? $user->group : null;
        }
        $perms = [];
        if ($group_id !== null) {
            $permissions = \Model_Permission_Group::query()->where('group_id', $group_id)->get();
            foreach ($permissions as $perm) {
                $perms[$perm->resource] = array(
                    'view'   => (int) $perm->can_view,
                    'edit'   => (int) $perm->can_edit,
                    'delete' => (int) $perm->can_delete,
                    'create' => (int) $perm->can_create,
                );
            }
        }
        error_log('REFRESH GROUP PERMISSIONS PARA GRUPO ' . $group_id . ': ' . print_r($perms, true));
        \Session::set('group_permissions', $perms);
    }





}
