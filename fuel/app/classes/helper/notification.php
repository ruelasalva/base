<?php
/**
 * Helper_Notification
 * 
 * Sistema de notificaciones multi-tenant para el ERP
 * Permite enviar notificaciones a usuarios individuales o por rol
 * Integrado con el sistema de auditoría
 * 
 * @package    Base
 * @category   Helpers
 * @author     Admin
 */

class Helper_Notification
{
	/**
	 * Enviar notificación a un usuario específico
	 * 
	 * @param int $user_id ID del usuario destinatario
	 * @param string $title Título de la notificación
	 * @param string $message Mensaje de la notificación
	 * @param string $type Tipo: info, success, warning, danger, system
	 * @param string|null $link URL de enlace (opcional)
	 * @return bool
	 */
	public static function send($user_id, $title, $message, $type = 'info', $link = null)
	{
		try {
			// Validar tipo
			$valid_types = ['info', 'success', 'warning', 'danger', 'system'];
			if (!in_array($type, $valid_types)) {
				$type = 'info';
			}

			// Obtener tenant_id del usuario destinatario
			$user = DB::select('tenant_id')
				->from('users')
				->where('id', $user_id)
				->execute()
				->current();

			if (empty($user)) {
				\Log::error('Helper_Notification::send() - Usuario no encontrado: ' . $user_id);
				return false;
			}

			// Insertar notificación
			list($insert_id, $rows_affected) = DB::insert('notifications')
				->set([
					'tenant_id' => $user['tenant_id'],
					'user_id' => $user_id,
					'type' => $type,
					'title' => $title,
					'message' => $message,
					'link' => $link,
					'is_read' => 0,
					'created_at' => date('Y-m-d H:i:s')
				])
				->execute();

			// Registrar en auditoría
			if ($insert_id) {
				Helper_Log::record('notifications', 'send', $insert_id, 
					"Notificación enviada a usuario {$user_id}: {$title}",
					null,
					['user_id' => $user_id, 'type' => $type, 'title' => $title]
				);
			}

			return $rows_affected > 0;

		} catch (Exception $e) {
			\Log::error('Helper_Notification::send() - Error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Enviar notificación a todos los usuarios de un rol
	 * 
	 * @param int $role_id ID del rol
	 * @param string $title Título
	 * @param string $message Mensaje
	 * @param string $type Tipo
	 * @param string|null $link Enlace
	 * @return int Cantidad de notificaciones enviadas
	 */
	public static function send_to_role($role_id, $title, $message, $type = 'info', $link = null)
	{
		try {
			$tenant_id = Auth::get('tenant_id');

			// Obtener usuarios del rol en este tenant
			$users = DB::select('u.id', 'u.tenant_id')
				->from(['users', 'u'])
				->join(['user_roles', 'ur'], 'INNER')
				->on('ur.user_id', '=', 'u.id')
				->where('ur.role_id', $role_id)
				->where('u.tenant_id', $tenant_id)
				->where('u.is_active', 1)
				->execute()
				->as_array();

			$sent_count = 0;

			foreach ($users as $user) {
				if (self::send($user['id'], $title, $message, $type, $link)) {
					$sent_count++;
				}
			}

			// Registrar en auditoría
			Helper_Log::record('notifications', 'send_to_role', $role_id,
				"Notificaciones enviadas a rol {$role_id}: {$sent_count} usuarios",
				null,
				['role_id' => $role_id, 'sent_count' => $sent_count, 'title' => $title]
			);

			return $sent_count;

		} catch (Exception $e) {
			\Log::error('Helper_Notification::send_to_role() - Error: ' . $e->getMessage());
			return 0;
		}
	}

	/**
	 * Enviar notificación a todos los usuarios del tenant
	 * 
	 * @param string $title Título
	 * @param string $message Mensaje
	 * @param string $type Tipo
	 * @param string|null $link Enlace
	 * @return int Cantidad enviada
	 */
	public static function send_to_all($title, $message, $type = 'system', $link = null)
	{
		try {
			$tenant_id = Auth::get('tenant_id');

			// Obtener todos los usuarios activos del tenant
			$users = DB::select('id', 'tenant_id')
				->from('users')
				->where('tenant_id', $tenant_id)
				->where('is_active', 1)
				->execute()
				->as_array();

			$sent_count = 0;

			foreach ($users as $user) {
				if (self::send($user['id'], $title, $message, $type, $link)) {
					$sent_count++;
				}
			}

			// Registrar en auditoría
			Helper_Log::record('notifications', 'send_to_all', null,
				"Notificación masiva enviada: {$sent_count} usuarios",
				null,
				['sent_count' => $sent_count, 'title' => $title, 'type' => $type]
			);

			return $sent_count;

		} catch (Exception $e) {
			\Log::error('Helper_Notification::send_to_all() - Error: ' . $e->getMessage());
			return 0;
		}
	}

	/**
	 * Obtener notificaciones no leídas de un usuario
	 * 
	 * @param int|null $user_id ID del usuario (por defecto usuario actual)
	 * @param int $limit Límite de resultados
	 * @return array
	 */
	public static function get_unread($user_id = null, $limit = 50)
	{
		try {
			if ($user_id === null) {
				$user_id = Auth::get('id');
			}

			$tenant_id = Auth::get('tenant_id');

			$notifications = DB::select()
				->from('notifications')
				->where('tenant_id', $tenant_id)
				->where('user_id', $user_id)
				->where('is_read', 0)
				->order_by('created_at', 'DESC')
				->limit($limit)
				->execute()
				->as_array();

			return $notifications;

		} catch (Exception $e) {
			\Log::error('Helper_Notification::get_unread() - Error: ' . $e->getMessage());
			return [];
		}
	}

	/**
	 * Obtener todas las notificaciones de un usuario (con filtros)
	 * 
	 * @param array $filters Filtros: user_id, type, is_read, date_from, date_to
	 * @param int $limit Límite
	 * @param int $offset Desplazamiento
	 * @return array
	 */
	public static function get($filters = [], $limit = 50, $offset = 0)
	{
		try {
			$tenant_id = Auth::get('tenant_id');

			$query = DB::select()
				->from('notifications')
				->where('tenant_id', $tenant_id);

			// Filtros
			if (!empty($filters['user_id'])) {
				$query->where('user_id', $filters['user_id']);
			}

			if (!empty($filters['type'])) {
				$query->where('type', $filters['type']);
			}

			if (isset($filters['is_read']) && $filters['is_read'] !== '') {
				$query->where('is_read', (int)$filters['is_read']);
			}

			if (!empty($filters['date_from'])) {
				$query->where('created_at', '>=', $filters['date_from'] . ' 00:00:00');
			}

			if (!empty($filters['date_to'])) {
				$query->where('created_at', '<=', $filters['date_to'] . ' 23:59:59');
			}

			$notifications = $query
				->order_by('created_at', 'DESC')
				->limit($limit)
				->offset($offset)
				->execute()
				->as_array();

			return $notifications;

		} catch (Exception $e) {
			\Log::error('Helper_Notification::get() - Error: ' . $e->getMessage());
			return [];
		}
	}

	/**
	 * Contar notificaciones según filtros
	 * 
	 * @param array $filters Mismos filtros que get()
	 * @return int
	 */
	public static function count($filters = [])
	{
		try {
			$tenant_id = Auth::get('tenant_id');

			$query = DB::select(DB::expr('COUNT(*) as total'))
				->from('notifications')
				->where('tenant_id', $tenant_id);

			// Aplicar filtros
			if (!empty($filters['user_id'])) {
				$query->where('user_id', $filters['user_id']);
			}

			if (!empty($filters['type'])) {
				$query->where('type', $filters['type']);
			}

			if (isset($filters['is_read']) && $filters['is_read'] !== '') {
				$query->where('is_read', (int)$filters['is_read']);
			}

			if (!empty($filters['date_from'])) {
				$query->where('created_at', '>=', $filters['date_from'] . ' 00:00:00');
			}

			if (!empty($filters['date_to'])) {
				$query->where('created_at', '<=', $filters['date_to'] . ' 23:59:59');
			}

			$result = $query->execute()->current();

			return isset($result['total']) ? (int)$result['total'] : 0;

		} catch (Exception $e) {
			\Log::error('Helper_Notification::count() - Error: ' . $e->getMessage());
			return 0;
		}
	}

	/**
	 * Contar notificaciones no leídas de un usuario
	 * 
	 * @param int|null $user_id ID del usuario (por defecto usuario actual)
	 * @return int
	 */
	public static function count_unread($user_id = null)
	{
		try {
			if ($user_id === null) {
				$user_id = Auth::get('id');
			}

			$tenant_id = Auth::get('tenant_id');

			$result = DB::select(DB::expr('COUNT(*) as total'))
				->from('notifications')
				->where('tenant_id', $tenant_id)
				->where('user_id', $user_id)
				->where('is_read', 0)
				->execute()
				->current();

			return isset($result['total']) ? (int)$result['total'] : 0;

		} catch (Exception $e) {
			\Log::error('Helper_Notification::count_unread() - Error: ' . $e->getMessage());
			return 0;
		}
	}

	/**
	 * Marcar notificación como leída
	 * 
	 * @param int $notification_id ID de la notificación
	 * @return bool
	 */
	public static function mark_as_read($notification_id)
	{
		try {
			$tenant_id = Auth::get('tenant_id');
			$user_id = Auth::get('id');

			$rows = DB::update('notifications')
				->set([
					'is_read' => 1,
					'read_at' => date('Y-m-d H:i:s')
				])
				->where('id', $notification_id)
				->where('tenant_id', $tenant_id)
				->where('user_id', $user_id)
				->execute();

			return $rows > 0;

		} catch (Exception $e) {
			\Log::error('Helper_Notification::mark_as_read() - Error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Marcar todas las notificaciones de un usuario como leídas
	 * 
	 * @param int|null $user_id ID del usuario (por defecto usuario actual)
	 * @return int Cantidad de notificaciones marcadas
	 */
	public static function mark_all_read($user_id = null)
	{
		try {
			if ($user_id === null) {
				$user_id = Auth::get('id');
			}

			$tenant_id = Auth::get('tenant_id');

			$rows = DB::update('notifications')
				->set([
					'is_read' => 1,
					'read_at' => date('Y-m-d H:i:s')
				])
				->where('tenant_id', $tenant_id)
				->where('user_id', $user_id)
				->where('is_read', 0)
				->execute();

			if ($rows > 0) {
				Helper_Log::record('notifications', 'mark_all_read', null,
					"Usuario {$user_id} marcó {$rows} notificaciones como leídas",
					null,
					['user_id' => $user_id, 'count' => $rows]
				);
			}

			return $rows;

		} catch (Exception $e) {
			\Log::error('Helper_Notification::mark_all_read() - Error: ' . $e->getMessage());
			return 0;
		}
	}

	/**
	 * Eliminar una notificación
	 * 
	 * @param int $notification_id ID de la notificación
	 * @return bool
	 */
	public static function delete($notification_id)
	{
		try {
			$tenant_id = Auth::get('tenant_id');
			$user_id = Auth::get('id');

			// Obtener datos antes de eliminar para auditoría
			$notification = DB::select()
				->from('notifications')
				->where('id', $notification_id)
				->where('tenant_id', $tenant_id)
				->where('user_id', $user_id)
				->execute()
				->current();

			if (empty($notification)) {
				return false;
			}

			$rows = DB::delete('notifications')
				->where('id', $notification_id)
				->where('tenant_id', $tenant_id)
				->where('user_id', $user_id)
				->execute();

			if ($rows > 0) {
				Helper_Log::record('notifications', 'delete', $notification_id,
					"Notificación eliminada: {$notification['title']}",
					$notification,
					null
				);
			}

			return $rows > 0;

		} catch (Exception $e) {
			\Log::error('Helper_Notification::delete() - Error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Limpiar notificaciones antiguas
	 * 
	 * @param int $days Días de antigüedad (por defecto 90)
	 * @param bool $only_read Eliminar solo las leídas (por defecto true)
	 * @return int Cantidad eliminada
	 */
	public static function cleanup($days = 90, $only_read = true)
	{
		try {
			$tenant_id = Auth::get('tenant_id');
			$date_limit = date('Y-m-d H:i:s', strtotime("-{$days} days"));

			$query = DB::delete('notifications')
				->where('tenant_id', $tenant_id)
				->where('created_at', '<', $date_limit);

			if ($only_read) {
				$query->where('is_read', 1);
			}

			$rows = $query->execute();

			if ($rows > 0) {
				Helper_Log::record('notifications', 'cleanup', null,
					"Limpieza de notificaciones: {$rows} eliminadas (>{$days} días)",
					null,
					['days' => $days, 'only_read' => $only_read, 'deleted' => $rows]
				);
			}

			return $rows;

		} catch (Exception $e) {
			\Log::error('Helper_Notification::cleanup() - Error: ' . $e->getMessage());
			return 0;
		}
	}
}
