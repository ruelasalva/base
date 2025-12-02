<?php
/**
 * Admin Module - Users Controller
 *
 * @package    Admin
 * @version    1.0.0
 */

namespace Admin;

class Controller_Users extends \Controller
{
	public function action_index()
	{
		$data = array(
			'module_name' => 'Gestión de Usuarios',
			'users' => array(),
		);

		return \Response::forge(\View::forge('admin/users/index', $data, false));
	}

	public function action_agregar()
	{
		$data = array('module_name' => 'Agregar Usuario');
		return \Response::forge(\View::forge('admin/users/agregar', $data, false));
	}

	public function action_editar($id = null)
	{
		if ($id === null)
		{
			\Session::set_flash('error', 'No se proporcionó un ID válido.');
			\Response::redirect('admin/users');
		}
		$data = array('module_name' => 'Editar Usuario', 'user_id' => $id);
		return \Response::forge(\View::forge('admin/users/editar', $data, false));
	}

	public function action_eliminar($id = null)
	{
		if ($id === null)
		{
			\Session::set_flash('error', 'No se proporcionó un ID válido.');
			\Response::redirect('admin/users');
		}
		\Session::set_flash('success', 'Usuario eliminado correctamente.');
		\Response::redirect('admin/users');
	}
}
