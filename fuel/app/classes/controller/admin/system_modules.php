<?php

/**
 * CONTROLADOR SYSTEM_MODULES
 * 
 * DEPRECADO: Esta ruta ha sido unificada con /admin/modules
 * 
 * Este controlador ahora solo redirige a la nueva ubicación
 * para mantener compatibilidad con enlaces antiguos.
 * 
 * @deprecated Usar Controller_Admin_Modules en su lugar
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
	 * Redirige a la nueva interfaz unificada
	 */
	public function action_index()
	{
		Session::set_flash('info', 'La gestión de módulos se ha movido a una nueva interfaz mejorada.');
		Response::redirect('admin/modules');
	}

	/**
	 * EDITAR MÓDULO
	 * Redirige a la nueva interfaz unificada
	 */
	public function action_editar($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de módulo no especificado');
			Response::redirect('admin/modules');
		}

		Session::set_flash('info', 'La gestión de módulos se ha movido a una nueva interfaz mejorada.');
		Response::redirect('admin/modules/editar/' . $id);
	}
}
