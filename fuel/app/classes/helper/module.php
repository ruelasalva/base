<?php

/**
 * Helper_Module
 * 
 * Gestiona la activación, desactivación y validación de módulos del sistema.
 * Implementa lógica de validación para prevenir desactivación cuando existen registros.
 */
class Helper_Module
{
    /**
     * Verifica si un módulo puede ser desactivado
     * 
     * Valida dos condiciones:
     * 1. El flag is_core debe ser 0 (módulos core no pueden desactivarse)
     * 2. No debe existir ningún registro en las tablas asociadas al módulo
     * 
     * @param int $module_id ID del módulo
     * @param int $tenant_id ID del tenant (null usa el actual)
     * @return array ['can_deactivate' => bool, 'reason' => string, 'details' => array]
     */
    public static function can_deactivate($module_id, $tenant_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }

        try {
            // 1. Verificar flag is_core
            $module = DB::select('name', 'display_name', 'is_core')
                ->from('modules')
                ->where('id', $module_id)
                ->execute()
                ->current();

            if (!$module) {
                return [
                    'can_deactivate' => false,
                    'reason' => 'Módulo no encontrado',
                    'details' => []
                ];
            }

            // Si el módulo es core (is_core = 1), no se puede desactivar
            if ($module['is_core'] == 1) {
                return [
                    'can_deactivate' => false,
                    'reason' => 'Este módulo es parte del núcleo del sistema y no puede desactivarse',
                    'module_name' => $module['display_name'],
                    'details' => []
                ];
            }

            // 2. Verificar si existen registros en module_usage
            $usage = DB::select('table_name', 'record_count', 'last_activity')
                ->from('module_usage')
                ->where('module_id', $module_id)
                ->where('tenant_id', $tenant_id)
                ->where('record_count', '>', 0)
                ->execute()
                ->as_array();

            if (count($usage) > 0) {
                $total_records = array_sum(array_column($usage, 'record_count'));
                $tables = [];
                
                foreach ($usage as $row) {
                    $tables[] = [
                        'table' => $row['table_name'],
                        'count' => $row['record_count'],
                        'last_activity' => $row['last_activity']
                    ];
                }

                return [
                    'can_deactivate' => false,
                    'reason' => "No se puede desactivar {$module['display_name']}: existen {$total_records} registros en uso",
                    'module_name' => $module['display_name'],
                    'total_records' => $total_records,
                    'details' => $tables
                ];
            }

            // 3. Si pasa todas las validaciones, puede desactivarse
            return [
                'can_deactivate' => true,
                'reason' => 'El módulo puede desactivarse',
                'module_name' => $module['display_name'],
                'details' => []
            ];

        } catch (Exception $e) {
            Helper_Log::record('module', 'error', 'Error al validar desactivación de módulo', [
                'module_id' => $module_id,
                'tenant_id' => $tenant_id,
                'error' => $e->getMessage()
            ]);

            return [
                'can_deactivate' => false,
                'reason' => 'Error al validar el módulo: ' . $e->getMessage(),
                'details' => []
            ];
        }
    }

    /**
     * Activa un módulo para un tenant
     * 
     * @param int $module_id ID del módulo
     * @param array $settings Configuración específica del módulo (opcional)
     * @param int $tenant_id ID del tenant (null usa el actual)
     * @return array ['success' => bool, 'message' => string]
     */
    public static function activate($module_id, $settings = [], $tenant_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }

        try {
            // Verificar que el módulo existe
            $module = DB::select('name', 'display_name', 'is_enabled')
                ->from('modules')
                ->where('id', $module_id)
                ->execute()
                ->current();

            if (!$module) {
                return [
                    'success' => false,
                    'message' => 'Módulo no encontrado'
                ];
            }

            if ($module['is_enabled'] == 0) {
                return [
                    'success' => false,
                    'message' => 'Este módulo no está disponible en el sistema'
                ];
            }

            // Verificar si ya está activado
            $exists = DB::select(DB::expr('COUNT(*) as total'))
                ->from('tenant_modules')
                ->where('tenant_id', $tenant_id)
                ->where('module_id', $module_id)
                ->execute()
                ->current();

            if ($exists['total'] > 0) {
                // Ya existe, actualizar a activo
                DB::update('tenant_modules')
                    ->set([
                        'is_active' => 1,
                        'config' => empty($settings) ? null : json_encode($settings),
                        'activated_at' => Date::forge()->format('mysql')
                    ])
                    ->where('tenant_id', $tenant_id)
                    ->where('module_id', $module_id)
                    ->execute();
            } else {
                // Insertar nuevo registro
                DB::insert('tenant_modules')
                    ->set([
                        'tenant_id' => $tenant_id,
                        'module_id' => $module_id,
                        'is_active' => 1,
                        'config' => empty($settings) ? null : json_encode($settings),
                        'activated_at' => Date::forge()->format('mysql'),
                        'activated_by' => Auth::get('id', 1)
                    ])
                    ->execute();
            }

            // Registrar en log
            Helper_Log::record('module', 'activate', "Módulo {$module['display_name']} activado", [
                'module_id' => $module_id,
                'module_name' => $module['name'],
                'tenant_id' => $tenant_id,
                'settings' => $settings
            ]);

            return [
                'success' => true,
                'message' => "Módulo {$module['display_name']} activado correctamente"
            ];

        } catch (Exception $e) {
            Helper_Log::record('module', 'error', 'Error al activar módulo', [
                'module_id' => $module_id,
                'tenant_id' => $tenant_id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error al activar el módulo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Desactiva un módulo para un tenant
     * Valida que se pueda desactivar antes de proceder
     * 
     * @param int $module_id ID del módulo
     * @param int $tenant_id ID del tenant (null usa el actual)
     * @return array ['success' => bool, 'message' => string, 'details' => array]
     */
    public static function deactivate($module_id, $tenant_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }

        try {
            // Validar si se puede desactivar
            $validation = static::can_deactivate($module_id, $tenant_id);
            
            if (!$validation['can_deactivate']) {
                return [
                    'success' => false,
                    'message' => $validation['reason'],
                    'details' => $validation['details']
                ];
            }

            // Obtener nombre del módulo
            $module = DB::select('name', 'display_name')
                ->from('modules')
                ->where('id', $module_id)
                ->execute()
                ->current();

            // Desactivar el módulo
            $rows = DB::update('tenant_modules')
                ->set([
                    'is_active' => 0,
                    'deactivated_at' => Date::forge()->format('mysql')
                ])
                ->where('tenant_id', $tenant_id)
                ->where('module_id', $module_id)
                ->execute();

            if ($rows > 0) {
                // Registrar en log
                Helper_Log::record('module', 'deactivate', "Módulo {$module['display_name']} desactivado", [
                    'module_id' => $module_id,
                    'module_name' => $module['name'],
                    'tenant_id' => $tenant_id
                ]);

                return [
                    'success' => true,
                    'message' => "Módulo {$module['display_name']} desactivado correctamente",
                    'details' => []
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'El módulo no estaba activado',
                    'details' => []
                ];
            }

        } catch (Exception $e) {
            Helper_Log::record('module', 'error', 'Error al desactivar módulo', [
                'module_id' => $module_id,
                'tenant_id' => $tenant_id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error al desactivar el módulo: ' . $e->getMessage(),
                'details' => []
            ];
        }
    }

    /**
     * Obtiene la lista de módulos activos para un tenant
     * 
     * @param int $tenant_id ID del tenant (null usa el actual)
     * @param string $category Filtrar por categoría (opcional)
     * @return array Lista de módulos activos
     */
    public static function get_active_modules($tenant_id = null, $category = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }

        try {
            $query = DB::select(
                    'm.id',
                    'm.name',
                    'm.display_name',
                    'm.description',
                    'm.icon',
                    'm.category',
                    'tm.config',
                    'tm.activated_at'
                )
                ->from(['modules', 'm'])
                ->join(['tenant_modules', 'tm'], 'INNER')
                ->on('m.id', '=', 'tm.module_id')
                ->where('m.is_enabled', 1)
                ->where('tm.tenant_id', $tenant_id)
                ->where('tm.is_active', 1);

            if ($category !== null) {
                $query->where('m.category', $category);
            }

            $query->order_by('m.menu_order', 'ASC');

            return $query->execute()->as_array();

        } catch (Exception $e) {
            Helper_Log::record('module', 'error', 'Error al obtener módulos activos', [
                'tenant_id' => $tenant_id,
                'category' => $category,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Obtiene todos los módulos con su estado de activación
     * 
     * @param int $tenant_id ID del tenant (null usa el actual)
     * @return array Lista de módulos con estado
     */
    public static function get_all_modules($tenant_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }

        try {
            $query = DB::select(
                    'm.id',
                    'm.name',
                    'm.display_name',
                    'm.description',
                    'm.icon',
                    'm.category',
                    'm.is_core',
                    'm.menu_order',
                    [DB::expr('IFNULL(tm.is_active, 0)'), 'is_tenant_active'],
                    'tm.config',
                    'tm.activated_at'
                )
                ->from(['modules', 'm'])
                ->join(['tenant_modules', 'tm'], 'LEFT')
                ->on('m.id', '=', 'tm.module_id')
                ->on('tm.tenant_id', '=', DB::expr($tenant_id))
                ->where('m.is_enabled', 1)
                ->order_by('m.category', 'ASC')
                ->order_by('m.menu_order', 'ASC');
            
            $modules = $query->execute()->as_array();

            return $modules;

        } catch (Exception $e) {
            Helper_Log::record('module', 'error', 'Error al obtener todos los módulos', [
                'tenant_id' => $tenant_id,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Actualiza el conteo de registros de un módulo
     * Debe llamarse después de operaciones CRUD en tablas asociadas al módulo
     * 
     * @param int $module_id ID del módulo
     * @param string $table_name Nombre de la tabla
     * @param int $tenant_id ID del tenant (null usa el actual)
     * @return bool
     */
    public static function update_usage($module_id, $table_name, $tenant_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }

        try {
            // Contar registros en la tabla
            $count_query = DB::select(DB::expr('COUNT(*) as total'))
                ->from($table_name);

            // Si la tabla tiene tenant_id, filtrar por tenant
            $table_cols = DB::list_columns($table_name);
            if (array_key_exists('tenant_id', $table_cols)) {
                $count_query->where('tenant_id', $tenant_id);
            }

            $result = $count_query->execute()->current();
            $record_count = $result['total'];

            // Verificar si existe el registro
            $exists = DB::select(DB::expr('COUNT(*) as total'))
                ->from('module_usage')
                ->where('tenant_id', $tenant_id)
                ->where('module_id', $module_id)
                ->where('table_name', $table_name)
                ->execute()
                ->current();

            if ($exists['total'] > 0) {
                // Actualizar registro existente
                DB::update('module_usage')
                    ->set([
                        'record_count' => $record_count,
                        'last_activity' => Date::forge()->format('mysql'),
                        'updated_at' => Date::forge()->format('mysql')
                    ])
                    ->where('tenant_id', $tenant_id)
                    ->where('module_id', $module_id)
                    ->where('table_name', $table_name)
                    ->execute();
            } else {
                // Insertar nuevo registro
                DB::insert('module_usage')
                    ->set([
                        'tenant_id' => $tenant_id,
                        'module_id' => $module_id,
                        'table_name' => $table_name,
                        'record_count' => $record_count,
                        'last_activity' => Date::forge()->format('mysql'),
                        'created_at' => Date::forge()->format('mysql'),
                        'updated_at' => Date::forge()->format('mysql')
                    ])
                    ->execute();
            }

            return true;

        } catch (Exception $e) {
            Helper_Log::record('module', 'error', 'Error al actualizar uso de módulo', [
                'module_id' => $module_id,
                'table_name' => $table_name,
                'tenant_id' => $tenant_id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Obtiene el uso (record_count) de un módulo
     * 
     * @param int $module_id ID del módulo
     * @param int $tenant_id ID del tenant (null usa el actual)
     * @return array ['total_records' => int, 'tables' => array]
     */
    public static function get_usage($module_id, $tenant_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }

        try {
            $usage = DB::select('table_name', 'record_count', 'last_activity')
                ->from('module_usage')
                ->where('module_id', $module_id)
                ->where('tenant_id', $tenant_id)
                ->execute()
                ->as_array();

            $total = array_sum(array_column($usage, 'record_count'));

            return [
                'total_records' => $total,
                'tables' => $usage
            ];

        } catch (Exception $e) {
            Helper_Log::record('module', 'error', 'Error al obtener uso de módulo', [
                'module_id' => $module_id,
                'tenant_id' => $tenant_id,
                'error' => $e->getMessage()
            ]);

            return [
                'total_records' => 0,
                'tables' => []
            ];
        }
    }

    /**
     * Verifica si un módulo está activo para un tenant
     * 
     * @param string $module_name Nombre del módulo
     * @param int $tenant_id ID del tenant (null usa el actual)
     * @return bool
     */
    public static function is_active($module_name, $tenant_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }

        try {
            $result = DB::select(DB::expr('COUNT(*) as total'))
                ->from('modules', 'm')
                ->join(['tenant_modules', 'tm'], 'INNER')
                ->on('m.id', '=', 'tm.module_id')
                ->where('m.name', $module_name)
                ->where('m.is_enabled', 1)
                ->where('tm.tenant_id', $tenant_id)
                ->where('tm.is_active', 1)
                ->execute()
                ->current();

            return $result['total'] > 0;

        } catch (Exception $e) {
            return false;
        }
    }
}
