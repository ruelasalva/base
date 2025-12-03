<?php

/**
 * CONTROLLER ADMIN MODULES
 * 
 * Gestión de módulos del sistema
 * - Listar módulos disponibles
 * - Activar/desactivar módulos por tenant
 * - Configurar módulos
 * 
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Modules extends Controller_Admin
{
	/**
	 * INDEX - LISTA DE MÓDULOS
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

		// Obtener módulos agrupados por categoría con estado
		$modules_by_category = Helper_Module::get_modules_by_category($tenant_id);

		// Nombres de categorías en español
		$category_names = [
			'core' => 'Módulos Base',
			'business' => 'Módulos de Negocio',
			'sales' => 'Ventas y CRM',
			'marketing' => 'Marketing Digital',
			'backend' => 'Backends y APIs',
			'system' => 'Sistema'
		];

		$data = [
			'title' => 'Gestión de Módulos',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'modules_by_category' => $modules_by_category,
			'category_names' => $category_names,
			'can_enable' => Helper_Permission::can('modules', 'enable')
		];

		$data['content'] = View::forge('admin/modules/index', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * ENABLE - ACTIVAR MÓDULO VÍA AJAX
	 */
	public function action_enable()
	{
		// Solo AJAX
		if (!Input::is_ajax())
		{
			Response::redirect('admin/modules');
		}

		// Verificar permisos
		if (!Helper_Permission::can('modules', 'enable'))
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'No tienes permisos para activar módulos'
			]), 403, ['Content-Type' => 'application/json']);
		}

		$module_id = Input::post('module_id');
		$tenant_id = Session::get('tenant_id', 1);
		$user_id = Auth::get('id');

		if (!$module_id)
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'ID de módulo requerido'
			]), 400, ['Content-Type' => 'application/json']);
		}

		$result = Helper_Module::enable($module_id, $tenant_id, $user_id);

		return Response::forge(json_encode($result), 200, ['Content-Type' => 'application/json']);
	}

	/**
	 * DISABLE - DESACTIVAR MÓDULO VÍA AJAX
	 */
	public function action_disable()
	{
		// Solo AJAX
		if (!Input::is_ajax())
		{
			Response::redirect('admin/modules');
		}

		// Verificar permisos
		if (!Helper_Permission::can('modules', 'enable'))
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'No tienes permisos para desactivar módulos'
			]), 403, ['Content-Type' => 'application/json']);
		}

		$module_id = Input::post('module_id');
		$tenant_id = Session::get('tenant_id', 1);

		if (!$module_id)
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'ID de módulo requerido'
			]), 400, ['Content-Type' => 'application/json']);
		}

		$result = Helper_Module::disable($module_id, $tenant_id);

		return Response::forge(json_encode($result), 200, ['Content-Type' => 'application/json']);
	}
}
