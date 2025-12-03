<?php

/**
 * CONTROLADOR SYSTEM_MODULES
 * 
 * Gestión de módulos del sistema
 */
class Controller_Admin_System_Modules extends Controller_Admin
{
	public function before()
	{
		parent::before();

		# Verificar permisos de administrador
		if (!Auth::member(100))
		{
			Session::set_flash('error', 'No tienes permisos para administrar módulos del sistema.');
			Response::redirect('admin');
		}
	}

	/**
	 * LISTADO DE MÓDULOS
	 */
	public function action_index()
	{
		$data = [];

		// Obtener todos los módulos
		$modules = DB::select('*')
			->from('system_modules')
			->order_by('category', 'ASC')
			->order_by('order_position', 'ASC')
			->execute()
			->as_array();

		$data['modules'] = $modules;

		$this->template->title = 'Gestión de Módulos del Sistema';
		$this->template->content = View::forge('admin/system_modules/index', $data);
	}

	/**
	 * EDITAR MÓDULO
	 */
	public function action_editar($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de módulo no especificado');
			Response::redirect('admin/system_modules');
		}

		$data = [];

		// Obtener módulo
		$module = DB::select('*')
			->from('system_modules')
			->where('id', $id)
			->execute()
			->current();

		if (!$module) {
			Session::set_flash('error', 'Módulo no encontrado');
			Response::redirect('admin/system_modules');
		}

		// Si es POST, guardar
		if (Input::method() === 'POST') {
			try {
				DB::update('system_modules')
					->set([
						'display_name' => Input::post('display_name'),
						'description' => Input::post('description'),
						'icon' => Input::post('icon'),
						'category' => Input::post('category'),
						'order_position' => (int)Input::post('order_position'),
						'is_active' => (int)Input::post('is_active', 0)
					])
					->where('id', $id)
					->execute();

				Session::set_flash('success', 'Módulo actualizado correctamente');
				Response::redirect('admin/system_modules');

			} catch (Exception $e) {
				Log::error('Error actualizando módulo: ' . $e->getMessage());
				Session::set_flash('error', 'Error al actualizar módulo: ' . $e->getMessage());
			}
		}

		$data['module'] = $module;

		$this->template->title = 'Editar Módulo: ' . $module['display_name'];
		$this->template->content = View::forge('admin/system_modules/editar', $data);
	}
}
