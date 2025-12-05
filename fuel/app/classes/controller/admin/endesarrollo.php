<?php

/**
 * Controlador genérico para módulos en desarrollo
 * Este controlador maneja todas las rutas de módulos que aún no tienen implementación completa
 */
class Controller_Admin_Endesarrollo extends Controller_Admin_Base
{
    /**
     * Vista principal para módulos en desarrollo
     * Muestra información profesional sobre el estado del módulo
     */
    public function action_index()
    {
        // Obtener información del módulo desde la URL
        $module_name = \Input::get('modulo', 'este módulo');
        $module_icon = \Input::get('icon', 'fa-cog');
        $module_category = \Input::get('categoria', 'sistema');
        
        // Mapeo de categorías a nombres en español
        $category_names = [
            'core' => 'Núcleo del Sistema',
            'contabilidad' => 'Contabilidad',
            'finanzas' => 'Finanzas',
            'compras' => 'Compras',
            'inventario' => 'Inventario',
            'sales' => 'Ventas',
            'rrhh' => 'Recursos Humanos',
            'marketing' => 'Marketing',
            'backend' => 'Backend & Portales',
            'integraciones' => 'Integraciones',
            'system' => 'Sistema'
        ];
        
        $category_display = isset($category_names[$module_category]) 
            ? $category_names[$module_category] 
            : ucfirst($module_category);
        
        // Preparar datos para la vista
        $data = [
            'title' => $module_name . ' - En Desarrollo',
            'module_name' => $module_name,
            'module_icon' => $module_icon,
            'module_category' => $category_display,
            'username' => \Auth::get('username'),
            'email' => \Auth::get('email'),
            'tenant_id' => \Helper_Tenant::get_tenant_id(),
            'is_super_admin' => \Helper_Permission::is_super_admin(),
            'is_admin' => \Helper_Permission::is_admin(),
        ];
        
        return \View::forge('admin/endesarrollo/index', $data);
    }
    
    /**
     * Manejo de cualquier acción no definida
     * Redirige a la vista principal de desarrollo
     */
    public function router($method, $params)
    {
        return $this->action_index();
    }
}
