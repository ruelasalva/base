<?php

/**
 * Controller_Admin_Modules
 * 
 * Gestión de módulos del sistema
 * - Listado de módulos por categoría
 * - Activar/desactivar módulos
 * - Validación de uso antes de desactivar
 * - Configuración de módulos
 */
class Controller_Admin_Modules extends Controller_Admin
{
    /**
     * Index - Lista de módulos por categoría
     */
    public function action_index()
    {
        // Verificar permisos
        if (!Helper_Permission::can('modules', 'view'))
        {
            Session::set_flash('error', 'No tienes permisos para gestionar módulos');
            Response::redirect('admin');
        }

        $tenant_id = Session::get('tenant_id', 1);
        
        // Obtener todos los módulos con estado
        $all_modules = Helper_Module::get_all_modules($tenant_id);
        
        // DEBUG temporal - Mostrar en pantalla si está vacío
        if (empty($all_modules)) {
            echo '<html><body><pre style="background:#dc3545;color:white;padding:30px;margin:20px;font-size:14px;border-radius:5px;">';
            echo "<strong>❌ ERROR DEBUG:</strong> Helper_Module::get_all_modules() retornó vacío\n\n";
            echo "<strong>Contexto:</strong>\n";
            echo "  tenant_id parametro: " . var_export($tenant_id, true) . "\n";
            echo "  Session tenant_id: " . var_export(Session::get('tenant_id'), true) . "\n";
            echo "  User ID: " . var_export(Auth::get('id'), true) . "\n\n";
            
            echo "<strong>Test de conexión DB:</strong>\n";
            try {
                $test = DB::select('id', 'name', 'display_name', 'category')
                    ->from('system_modules')
                    ->where('is_active', 1)
                    ->limit(5)
                    ->execute()
                    ->as_array();
                echo "  ✓ Query directa funciona: " . count($test) . " módulos encontrados\n";
                echo "  Ejemplo: " . json_encode($test[0] ?? [], JSON_PRETTY_PRINT) . "\n\n";
            } catch (Exception $e) {
                echo "  ✗ Query directa falló: " . $e->getMessage() . "\n\n";
            }
            
            echo "<strong>Ver logs en:</strong> fuel/app/logs/2025/12/03.php\n";
            echo '</pre></body></html>';
            die();
        }
        
        \Log::info('Módulos encontrados: ' . count($all_modules));
        
        // Agrupar por categoría
        $grouped = [];
        foreach ($all_modules as $module) {
            $category = $module['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $module;
        }

        // Nombres de categorías en español
        $category_names = [
            'core' => 'Núcleo del Sistema',
            'ventas' => 'Ventas',
            'compras' => 'Compras',
            'inventario' => 'Inventario',
            'crm' => 'CRM',
            'rrhh' => 'Recursos Humanos',
            'finanzas' => 'Finanzas',
            'reportes' => 'Reportes',
            'otros' => 'Otros'
        ];

        // Iconos por categoría
        $category_icons = [
            'core' => 'fa-cog',
            'ventas' => 'fa-shopping-cart',
            'compras' => 'fa-truck',
            'inventario' => 'fa-boxes',
            'crm' => 'fa-handshake',
            'rrhh' => 'fa-users-cog',
            'finanzas' => 'fa-dollar-sign',
            'reportes' => 'fa-chart-line',
            'otros' => 'fa-ellipsis-h'
        ];

        // Ordenar categorías
        $ordered_categories = ['core', 'ventas', 'compras', 'inventario', 'crm', 'rrhh', 'finanzas', 'reportes', 'otros'];
        
        // Preparar datos para la vista
        $data = [
            'title' => 'Gestión de Módulos',
            'username' => Auth::get('username'),
            'email' => Auth::get('email'),
            'tenant_id' => $tenant_id,
            'is_super_admin' => Helper_Permission::is_super_admin(),
            'is_admin' => Helper_Permission::is_admin(),
            'grouped_modules' => $grouped,
            'ordered_categories' => $ordered_categories,
            'category_names' => $category_names,
            'category_icons' => $category_icons,
            'can_activate' => Helper_Permission::can('modules', 'activate')
        ];

        // Renderizar con el template del sistema
        $data['content'] = View::forge('admin/modules/index', $data);
        $template_file = Helper_Template::get_template_file();
        return View::forge($template_file, $data);
    }

    /**
     * Toggle - Activar/desactivar módulo (AJAX)
     */
    public function action_toggle()
    {
        // Solo AJAX
        if (!Input::is_ajax())
        {
            Response::redirect('admin/modules');
        }

        // Verificar permisos
        if (!Helper_Permission::can('modules', 'activate'))
        {
            return Response::forge(json_encode([
                'success' => false,
                'message' => 'No tienes permisos para activar/desactivar módulos'
            ]), 403, ['Content-Type' => 'application/json']);
        }

        // Obtener datos
        $module_id = Input::post('module_id');
        $action = Input::post('action'); // 'activate' o 'deactivate'
        $tenant_id = Session::get('tenant_id', 1);

        // Validar datos
        if (!$module_id || !$action)
        {
            return Response::forge(json_encode([
                'success' => false,
                'message' => 'Datos incompletos'
            ]), 400, ['Content-Type' => 'application/json']);
        }

        try {
            if ($action === 'activate') {
                // Activar módulo
                $result = Helper_Module::activate($module_id, [], $tenant_id);
            } elseif ($action === 'deactivate') {
                // Desactivar módulo (con validación)
                $result = Helper_Module::deactivate($module_id, $tenant_id);
            } else {
                return Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Acción no válida'
                ]), 400, ['Content-Type' => 'application/json']);
            }

            return Response::forge(json_encode($result), 200, ['Content-Type' => 'application/json']);

        } catch (Exception $e) {
            Helper_Log::record('module', 'error', 'Error en toggle de módulo', [
                'module_id' => $module_id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);

            return Response::forge(json_encode([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ]), 500, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Usage - Ver uso de un módulo (AJAX)
     */
    public function action_usage($module_id = null)
    {
        // Solo AJAX
        if (!Input::is_ajax())
        {
            Response::redirect('admin/modules');
        }

        // Verificar permiso
        if (!Helper_Permission::can('modules', 'view'))
        {
            return Response::forge(json_encode([
                'success' => false,
                'message' => 'No tienes permiso'
            ]), 403, ['Content-Type' => 'application/json']);
        }

        if (!$module_id)
        {
            return Response::forge(json_encode([
                'success' => false,
                'message' => 'ID de módulo no proporcionado'
            ]), 400, ['Content-Type' => 'application/json']);
        }

        $tenant_id = Session::get('tenant_id', 1);

        try {
            $usage = Helper_Module::get_usage($module_id, $tenant_id);
            $validation = Helper_Module::can_deactivate($module_id, $tenant_id);

            return Response::forge(json_encode([
                'success' => true,
                'usage' => $usage,
                'can_deactivate' => $validation['can_deactivate'],
                'reason' => $validation['reason']
            ]), 200, ['Content-Type' => 'application/json']);

        } catch (Exception $e) {
            return Response::forge(json_encode([
                'success' => false,
                'message' => 'Error al obtener uso del módulo: ' . $e->getMessage()
            ]), 500, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Settings - Configuración de un módulo
     */
    public function action_settings($module_id = null)
    {
        // Verificar permiso
        if (!Helper_Permission::can('modules', 'configure'))
        {
            Session::set_flash('error', 'No tienes permiso para configurar módulos');
            Response::redirect('admin/modules');
        }

        if (!$module_id)
        {
            Session::set_flash('error', 'ID de módulo no proporcionado');
            Response::redirect('admin/modules');
        }

        $tenant_id = Session::get('tenant_id', 1);

        // Obtener módulo
        $module = DB::select(
                'sm.id',
                'sm.name',
                'sm.display_name',
                'sm.description',
                'sm.icon',
                'sm.category',
                'tm.config as settings',
                'tm.is_active'
            )
            ->from(['system_modules', 'sm'])
            ->join(['tenant_modules', 'tm'], 'LEFT')
            ->on('sm.id', '=', 'tm.module_id')
            ->on('tm.tenant_id', '=', DB::expr($tenant_id))
            ->where('sm.id', $module_id)
            ->execute()
            ->current();

        if (!$module)
        {
            Session::set_flash('error', 'Módulo no encontrado');
            Response::redirect('admin/modules');
        }

        // Procesar formulario
        if (Input::method() === 'POST')
        {
            $settings = Input::post('settings', []);

            try {
                // Actualizar settings
                DB::update('tenant_modules')
                    ->set([
                        'config' => json_encode($settings)
                    ])
                    ->where('tenant_id', $tenant_id)
                    ->where('module_id', $module_id)
                    ->execute();

                // Log
                Helper_Log::record('module', 'settings', "Configuración actualizada: {$module['display_name']}", [
                    'module_id' => $module_id,
                    'settings' => $settings
                ]);

                Session::set_flash('success', 'Configuración guardada correctamente');
                Response::redirect('admin/modules');

            } catch (Exception $e) {
                Session::set_flash('error', 'Error al guardar configuración: ' . $e->getMessage());
            }
        }

        // Decodificar settings actuales
        $current_settings = [];
        if (!empty($module['settings']))
        {
            $current_settings = json_decode($module['settings'], true);
        }

        // Preparar datos para la vista
        $data = [
            'title' => 'Configuración: ' . $module['display_name'],
            'username' => Auth::get('username'),
            'email' => Auth::get('email'),
            'tenant_id' => $tenant_id,
            'is_super_admin' => Helper_Permission::is_super_admin(),
            'is_admin' => Helper_Permission::is_admin(),
            'module' => $module,
            'current_settings' => $current_settings
        ];

        // Renderizar con el template del sistema
        $data['content'] = View::forge('admin/modules/settings', $data);
        $template_file = Helper_Template::get_template_file();
        return View::forge($template_file, $data);
    }
}
