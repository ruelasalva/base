<?php

/**
 * HELPER DE AUDITORÍA Y LOGS
 * 
 * Registra todas las acciones importantes del sistema
 * para trazabilidad, compliance y troubleshooting
 * 
 * Uso:
 * Helper_Log::record('users', 'create', $user_id, 'Usuario creado', null, $user_data);
 * Helper_Log::record('inventory', 'edit', $product_id, 'Precio actualizado', $old_price, $new_price);
 * 
 * @package  app
 */
class Helper_Log
{
	/**
	 * Registrar una acción en el log de auditoría
	 * 
	 * @param string $module      Módulo (users, inventory, sales, etc.)
	 * @param string $action      Acción (create, edit, delete, view, login, etc.)
	 * @param int    $record_id   ID del registro afectado (opcional)
	 * @param string $description Descripción legible de la acción
	 * @param mixed  $old_data    Datos anteriores (opcional, para updates)
	 * @param mixed  $new_data    Datos nuevos (opcional)
	 * @return bool
	 */
	public static function record($module, $action, $record_id = null, $description = '', $old_data = null, $new_data = null)
	{
		try
		{
			// Obtener información del usuario actual
			$user_id = Auth::check() ? Auth::get('id') : null;
			$username = Auth::check() ? Auth::get('username') : 'system';
			$tenant_id = Session::get('tenant_id', 1);

			// Obtener IP y User Agent
			$ip_address = Input::real_ip();
			$user_agent = Input::user_agent();

			// Convertir datos a JSON si son arrays u objetos
			$old_data_json = null;
			$new_data_json = null;

			if ($old_data !== null)
			{
				$old_data_json = is_string($old_data) ? $old_data : json_encode($old_data, JSON_UNESCAPED_UNICODE);
			}

			if ($new_data !== null)
			{
				$new_data_json = is_string($new_data) ? $new_data : json_encode($new_data, JSON_UNESCAPED_UNICODE);
			}

			// Insertar log
			$result = DB::insert('audit_logs')->set([
				'tenant_id' => $tenant_id,
				'user_id' => $user_id,
				'username' => $username,
				'module' => $module,
				'action' => $action,
				'record_id' => $record_id,
				'description' => $description,
				'old_data' => $old_data_json,
				'new_data' => $new_data_json,
				'ip_address' => $ip_address,
				'user_agent' => $user_agent
			])->execute();

			return $result ? true : false;
		}
		catch (Exception $e)
		{
			// No lanzar excepción para no interrumpir el flujo
			// Solo registrar en log de errores
			\Log::error('Helper_Log::record - Error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Registrar un inicio de sesión
	 */
	public static function login($user_id, $username, $success = true)
	{
		$action = $success ? 'login_success' : 'login_failed';
		$description = $success 
			? "Inicio de sesión exitoso: {$username}" 
			: "Intento de inicio de sesión fallido: {$username}";

		return self::record('auth', $action, $user_id, $description);
	}

	/**
	 * Registrar un cierre de sesión
	 */
	public static function logout($user_id, $username)
	{
		return self::record('auth', 'logout', $user_id, "Cierre de sesión: {$username}");
	}

	/**
	 * Registrar un cambio de contraseña
	 */
	public static function password_change($user_id, $username, $by_admin = false)
	{
		$description = $by_admin 
			? "Contraseña cambiada por administrador para: {$username}"
			: "Cambio de contraseña: {$username}";

		return self::record('auth', 'password_change', $user_id, $description);
	}

	/**
	 * Obtener logs con filtros
	 * 
	 * @param array $filters Filtros (tenant_id, user_id, module, action, date_from, date_to)
	 * @param int   $limit   Límite de registros
	 * @param int   $offset  Offset para paginación
	 * @return array
	 */
	public static function get($filters = [], $limit = 100, $offset = 0)
	{
		$query = DB::select()
			->from('audit_logs')
			->order_by('created_at', 'DESC');

		// Aplicar filtros
		if (isset($filters['tenant_id']))
		{
			$query->where('tenant_id', $filters['tenant_id']);
		}

		if (isset($filters['user_id']))
		{
			$query->where('user_id', $filters['user_id']);
		}

		if (isset($filters['module']))
		{
			$query->where('module', $filters['module']);
		}

		if (isset($filters['action']))
		{
			$query->where('action', $filters['action']);
		}

		if (isset($filters['date_from']))
		{
			$query->where('created_at', '>=', $filters['date_from']);
		}

		if (isset($filters['date_to']))
		{
			$query->where('created_at', '<=', $filters['date_to']);
		}

		if (isset($filters['search']))
		{
			$query->where_open()
				->or_where('description', 'LIKE', '%' . $filters['search'] . '%')
				->or_where('username', 'LIKE', '%' . $filters['search'] . '%')
				->where_close();
		}

		$query->limit($limit)->offset($offset);

		return $query->execute()->as_array();
	}

	/**
	 * Contar logs con filtros
	 */
	public static function count($filters = [])
	{
		$query = DB::select(DB::expr('COUNT(*) as total'))
			->from('audit_logs');

		if (isset($filters['tenant_id']))
		{
			$query->where('tenant_id', $filters['tenant_id']);
		}

		if (isset($filters['user_id']))
		{
			$query->where('user_id', $filters['user_id']);
		}

		if (isset($filters['module']))
		{
			$query->where('module', $filters['module']);
		}

		if (isset($filters['action']))
		{
			$query->where('action', $filters['action']);
		}

		if (isset($filters['date_from']))
		{
			$query->where('created_at', '>=', $filters['date_from']);
		}

		if (isset($filters['date_to']))
		{
			$query->where('created_at', '<=', $filters['date_to']);
		}

		if (isset($filters['search']))
		{
			$query->where_open()
				->or_where('description', 'LIKE', '%' . $filters['search'] . '%')
				->or_where('username', 'LIKE', '%' . $filters['search'] . '%')
				->where_close();
		}

		$result = $query->execute()->current();
		return $result ? $result['total'] : 0;
	}

	/**
	 * Obtener estadísticas de logs
	 */
	public static function stats($tenant_id = null)
	{
		$tenant_id = $tenant_id ?: Session::get('tenant_id', 1);

		// Total de logs
		$total = DB::select(DB::expr('COUNT(*) as total'))
			->from('audit_logs')
			->where('tenant_id', $tenant_id)
			->execute()
			->current();

		// Logs hoy
		$today = DB::select(DB::expr('COUNT(*) as total'))
			->from('audit_logs')
			->where('tenant_id', $tenant_id)
			->where('created_at', '>=', date('Y-m-d 00:00:00'))
			->execute()
			->current();

		// Logs esta semana
		$week = DB::select(DB::expr('COUNT(*) as total'))
			->from('audit_logs')
			->where('tenant_id', $tenant_id)
			->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime('-7 days')))
			->execute()
			->current();

		// Top módulos
		$top_modules = DB::select('module', DB::expr('COUNT(*) as count'))
			->from('audit_logs')
			->where('tenant_id', $tenant_id)
			->group_by('module')
			->order_by('count', 'DESC')
			->limit(5)
			->execute()
			->as_array();

		// Top usuarios
		$top_users = DB::select('username', DB::expr('COUNT(*) as count'))
			->from('audit_logs')
			->where('tenant_id', $tenant_id)
			->where('username', '!=', 'system')
			->group_by('username')
			->order_by('count', 'DESC')
			->limit(5)
			->execute()
			->as_array();

		return [
			'total' => $total['total'],
			'today' => $today['total'],
			'week' => $week['total'],
			'top_modules' => $top_modules,
			'top_users' => $top_users
		];
	}

	/**
	 * Limpiar logs antiguos
	 * 
	 * @param int $days Días de antigüedad para eliminar
	 * @return int Registros eliminados
	 */
	public static function cleanup($days = 90)
	{
		$date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
		
		$result = DB::delete('audit_logs')
			->where('created_at', '<', $date)
			->execute();

		return $result;
	}
}
