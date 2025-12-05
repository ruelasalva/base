<?php

/**
 * Controlador para módulo: integracion_facebook
 * Redirige a vista de módulo en desarrollo
 */
class Controller_Admin_integracionfacebook extends Controller_Admin_Base
{
    public function action_index()
    {
        // Obtener información del módulo desde base de datos
        $module_info = DB::select('display_name', 'icon', 'category')
            ->from('modules')
            ->where('name', 'integracion_facebook')
            ->execute()
            ->current();
        
        if (!$module_info) {
            Response::redirect('admin');
        }
        
        $data = [
            'title' => $module_info['display_name'] . ' - En Desarrollo',
            'module_name' => $module_info['display_name'],
            'module_icon' => $module_info['icon'],
            'module_category' => $module_info['category'],
            'username' => Auth::get('username'),
            'email' => Auth::get('email'),
            'tenant_id' => Helper_Tenant::get_tenant_id(),
            'is_super_admin' => Helper_Permission::is_super_admin(),
            'is_admin' => Helper_Permission::is_admin(),
        ];
        
        return View::forge('admin/endesarrollo/index', $data);
    }
    
    public function router($method, $params)
    {
        return $this->action_index();
    }
}
