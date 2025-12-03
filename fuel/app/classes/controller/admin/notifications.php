<?php

/**
 * CONTROLLER ADMIN NOTIFICATIONS
 * 
 * Gestión de notificaciones del sistema
 * - Ver notificaciones del usuario
 * - Marcar como leídas
 * - Eliminar notificaciones
 * - Enviar notificaciones (admin)
 * 
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Notifications extends Controller_Admin
{
	/**
	 * INDEX - MIS NOTIFICACIONES
	 */
	public function action_index()
	{
		$user_id = Auth::get('id');
		$tenant_id = Auth::get('tenant_id');

		// Filtros
		$filters = [
			'user_id' => $user_id,
			'type' => Input::get('type', null),
			'is_read' => Input::get('is_read', null),
			'date_from' => Input::get('date_from', null),
			'date_to' => Input::get('date_to', null),
		];

		// Paginación
		$page = (int)Input::get('page', 1);
		$per_page = 30;
		$offset = ($page - 1) * $per_page;

		// Obtener notificaciones
		$notifications = Helper_Notification::get($filters, $per_page, $offset);
		$total = Helper_Notification::count($filters);
		$total_pages = ceil($total / $per_page);

		// Estadísticas
		$stats = [
			'total' => Helper_Notification::count(['user_id' => $user_id]),
			'unread' => Helper_Notification::count_unread($user_id),
			'today' => Helper_Notification::count([
				'user_id' => $user_id,
				'date_from' => date('Y-m-d')
			]),
			'week' => Helper_Notification::count([
				'user_id' => $user_id,
				'date_from' => date('Y-m-d', strtotime('-7 days'))
			]),
		];

		// Tipos disponibles
		$types = ['info', 'success', 'warning', 'danger', 'system'];

		$data = [
			'title' => 'Mis Notificaciones',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'can_admin' => Helper_Permission::can('notifications', 'admin'),
			'notifications' => $notifications,
			'stats' => $stats,
			'types' => $types,
			'filters' => $filters,
			'pagination' => [
				'page' => $page,
				'per_page' => $per_page,
				'total' => $total,
				'total_pages' => $total_pages
			]
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/notifications/index', $data);
	}

	/**
	 * VIEW - VER DETALLE DE NOTIFICACIÓN
	 */
	public function action_view($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de notificación no válido');
			Response::redirect('admin/notifications');
		}

		$user_id = Auth::get('id');
		$tenant_id = Auth::get('tenant_id');

		// Obtener notificación
		$notification = DB::select()
			->from('notifications')
			->where('id', $id)
			->where('tenant_id', $tenant_id)
			->where('user_id', $user_id)
			->execute()
			->current();

		if (empty($notification)) {
			Session::set_flash('error', 'Notificación no encontrada');
			Response::redirect('admin/notifications');
		}

		// Marcar como leída automáticamente
		if ($notification['is_read'] == 0) {
			Helper_Notification::mark_as_read($id);
			$notification['is_read'] = 1;
			$notification['read_at'] = date('Y-m-d H:i:s');
		}

		$data = [
			'title' => 'Detalle de Notificación',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'notification' => $notification
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/notifications/view', $data);
	}

	/**
	 * MARK_READ - MARCAR COMO LEÍDA (AJAX)
	 */
	public function action_mark_read()
	{
		if (!Input::is_ajax()) {
			Response::redirect('admin/notifications');
		}

		$id = Input::post('id', null);

		if (!$id) {
			return $this->response([
				'success' => false,
				'message' => 'ID no proporcionado'
			]);
		}

		$success = Helper_Notification::mark_as_read($id);

		return $this->response([
			'success' => $success,
			'message' => $success ? 'Notificación marcada como leída' : 'Error al marcar como leída'
		]);
	}

	/**
	 * MARK_ALL_READ - MARCAR TODAS COMO LEÍDAS (AJAX)
	 */
	public function action_mark_all_read()
	{
		if (!Input::is_ajax()) {
			Response::redirect('admin/notifications');
		}

		$user_id = Auth::get('id');
		$count = Helper_Notification::mark_all_read($user_id);

		return $this->response([
			'success' => true,
			'message' => "Se marcaron {$count} notificaciones como leídas",
			'count' => $count
		]);
	}

	/**
	 * DELETE - ELIMINAR NOTIFICACIÓN
	 */
	public function action_delete($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de notificación no válido');
			Response::redirect('admin/notifications');
		}

		$success = Helper_Notification::delete($id);

		if ($success) {
			Session::set_flash('success', 'Notificación eliminada correctamente');
		} else {
			Session::set_flash('error', 'Error al eliminar la notificación');
		}

		Response::redirect('admin/notifications');
	}

	/**
	 * GET_COUNT - OBTENER CANTIDAD DE NO LEÍDAS (AJAX)
	 */
	public function action_get_count()
	{
		if (!Input::is_ajax()) {
			Response::redirect('admin/notifications');
		}

		$user_id = Auth::get('id');
		$count = Helper_Notification::count_unread($user_id);

		return $this->response([
			'success' => true,
			'count' => $count
		]);
	}

	/**
	 * GET_UNREAD - OBTENER NOTIFICACIONES NO LEÍDAS (AJAX)
	 */
	public function action_get_unread()
	{
		if (!Input::is_ajax()) {
			Response::redirect('admin/notifications');
		}

		$user_id = Auth::get('id');
		$limit = (int)Input::get('limit', 10);

		$notifications = Helper_Notification::get_unread($user_id, $limit);

		return $this->response([
			'success' => true,
			'count' => count($notifications),
			'notifications' => $notifications
		]);
	}

	/**
	 * SEND - ENVIAR NOTIFICACIÓN (ADMIN)
	 */
	public function action_send()
	{
		// Verificar permisos
		if (!Helper_Permission::can('notifications', 'admin')) {
			Session::set_flash('error', 'No tienes permisos para enviar notificaciones');
			Response::redirect('admin/notifications');
		}

		$tenant_id = Auth::get('tenant_id');

		if (Input::method() == 'POST') {
			$send_to = Input::post('send_to'); // user, role, all
			$user_id = Input::post('user_id', null);
			$role_id = Input::post('role_id', null);
			$title = Input::post('title');
			$message = Input::post('message');
			$type = Input::post('type', 'info');
			$link = Input::post('link', null);

			$sent_count = 0;

			switch ($send_to) {
				case 'user':
					if ($user_id) {
						$success = Helper_Notification::send($user_id, $title, $message, $type, $link);
						$sent_count = $success ? 1 : 0;
					}
					break;

				case 'role':
					if ($role_id) {
						$sent_count = Helper_Notification::send_to_role($role_id, $title, $message, $type, $link);
					}
					break;

				case 'all':
					$sent_count = Helper_Notification::send_to_all($title, $message, $type, $link);
					break;
			}

			if ($sent_count > 0) {
				Session::set_flash('success', "Notificación enviada a {$sent_count} usuario(s)");
			} else {
				Session::set_flash('error', 'Error al enviar la notificación');
			}

			Response::redirect('admin/notifications/send');
		}

		// Obtener usuarios y roles para el formulario
		$users = DB::select('id', 'username', 'email')
			->from('users')
			->where('tenant_id', $tenant_id)
			->where('is_active', 1)
			->order_by('username', 'ASC')
			->execute()
			->as_array();

		$roles = DB::select('id', 'name', 'display_name')
			->from('roles')
			->where('is_active', 1)
			->order_by('level', 'DESC')
			->execute()
			->as_array();

		$types = ['info', 'success', 'warning', 'danger', 'system'];

		$data = [
			'title' => 'Enviar Notificación',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'users' => $users,
			'roles' => $roles,
			'types' => $types
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/notifications/send', $data);
	}

	/**
	 * CLEANUP - LIMPIAR NOTIFICACIONES ANTIGUAS
	 */
	public function action_cleanup()
	{
		if (!Input::is_ajax()) {
			Response::redirect('admin/notifications');
		}

		$days = (int)Input::post('days', 90);
		$only_read = (bool)Input::post('only_read', true);

		$deleted = Helper_Notification::cleanup($days, $only_read);

		return $this->response([
			'success' => true,
			'message' => "Se eliminaron {$deleted} notificaciones",
			'deleted' => $deleted
		]);
	}
}
