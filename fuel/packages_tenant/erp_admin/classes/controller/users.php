<?php
/**
 * ERP Admin Module - Users Controller
 *
 * @package    ERP_Admin
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Admin;

/**
 * Users Controller for the Admin Module
 *
 * Provides user management functionality including listing,
 * creating, editing, and deleting users.
 */
class Controller_Users extends \Controller
{
	/**
	 * Index action - displays user list
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Gestión de Usuarios',
			'breadcrumb' => array(
				'Dashboard' => 'admin',
				'Usuarios' => 'admin/users',
			),
			'users' => array(),
		);

		return \Response::forge(\View::forge('erp_admin/users/index', $data, false));
	}

	/**
	 * Create action - show user creation form
	 *
	 * @return \Response
	 */
	public function action_agregar()
	{
		$data = array(
			'module_name' => 'Agregar Usuario',
			'breadcrumb' => array(
				'Dashboard' => 'admin',
				'Usuarios' => 'admin/users',
				'Agregar' => 'admin/users/agregar',
			),
		);

		return \Response::forge(\View::forge('erp_admin/users/agregar', $data, false));
	}

	/**
	 * Edit action - show user edit form
	 *
	 * @param int $id User ID
	 * @return \Response
	 */
	public function action_editar($id = null)
	{
		if ($id === null)
		{
			\Session::set_flash('error', 'No se proporcionó un ID válido.');
			\Response::redirect('admin/users');
		}

		$data = array(
			'module_name' => 'Editar Usuario',
			'breadcrumb' => array(
				'Dashboard' => 'admin',
				'Usuarios' => 'admin/users',
				'Editar' => 'admin/users/editar/'.$id,
			),
			'user_id' => $id,
		);

		return \Response::forge(\View::forge('erp_admin/users/editar', $data, false));
	}

	/**
	 * Delete action - soft delete a user
	 *
	 * @param int $id User ID
	 * @return void
	 */
	public function action_eliminar($id = null)
	{
		if ($id === null)
		{
			\Session::set_flash('error', 'No se proporcionó un ID válido.');
			\Response::redirect('admin/users');
		}

		// Perform soft delete logic here
		\Session::set_flash('success', 'Usuario eliminado correctamente.');
		\Response::redirect('admin/users');
	}
}
