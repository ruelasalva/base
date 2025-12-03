<?php

/**
 * CONTROLLER ADMIN LOGS
 * 
 * Visualización y gestión del log de auditoría
 * - Consulta de logs con filtros
 * - Estadísticas de actividad
 * - Exportación de logs
 * - Limpieza de logs antiguos
 * 
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Logs extends Controller_Admin
{
	/**
	 * INDEX - LISTA DE LOGS CON FILTROS
	 */
	public function action_index()
	{
		// Verificar permisos
		if (!Helper_Permission::can('logs', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver logs');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener filtros
		$filters = [
			'tenant_id' => $tenant_id,
			'module' => Input::get('module', null),
			'action' => Input::get('action', null),
			'user_id' => Input::get('user_id', null),
			'date_from' => Input::get('date_from', null),
			'date_to' => Input::get('date_to', null),
			'search' => Input::get('search', null)
		];

		// Limpiar filtros vacíos
		$filters = array_filter($filters, function($value) {
			return $value !== null && $value !== '';
		});

		// Paginación
		$page = Input::get('page', 1);
		$per_page = 50;
		$offset = ($page - 1) * $per_page;

		// Obtener logs
		$logs = Helper_Log::get($filters, $per_page, $offset);
		$total = Helper_Log::count($filters);
		$total_pages = ceil($total / $per_page);

		// Obtener módulos únicos para filtro
		$modules = DB::select(DB::expr('DISTINCT module'))
			->from('audit_logs')
			->where('tenant_id', $tenant_id)
			->order_by('module', 'ASC')
			->execute()
			->as_array();

		// Obtener acciones únicas para filtro
		$actions = DB::select(DB::expr('DISTINCT action'))
			->from('audit_logs')
			->where('tenant_id', $tenant_id)
			->order_by('action', 'ASC')
			->execute()
			->as_array();

		// Obtener usuarios para filtro
		$users = DB::select('id', 'username')
			->from('users')
			->where('deleted_at', null)
			->order_by('username', 'ASC')
			->execute()
			->as_array();

		// Estadísticas
		$stats = Helper_Log::stats($tenant_id);

		$data = [
			'title' => 'Log de Auditoría',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'logs' => $logs,
			'filters' => $filters,
			'modules' => array_column($modules, 'module'),
			'actions' => array_column($actions, 'action'),
			'users' => $users,
			'stats' => $stats,
			'pagination' => [
				'page' => $page,
				'per_page' => $per_page,
				'total' => $total,
				'total_pages' => $total_pages
			],
			'can_export' => Helper_Permission::can('logs', 'export'),
			'can_delete' => Helper_Permission::can('logs', 'delete')
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/logs/index', $data);
	}

	/**
	 * VIEW - VER DETALLE DE UN LOG
	 */
	public function action_view($id = null)
	{
		if (!$id)
		{
			Session::set_flash('error', 'ID de log no válido');
			Response::redirect('admin/logs');
		}

		// Verificar permisos
		if (!Helper_Permission::can('logs', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver logs');
			Response::redirect('admin/logs');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener log
		$log = DB::select()
			->from('audit_logs')
			->where('id', $id)
			->where('tenant_id', $tenant_id)
			->execute()
			->current();

		if (!$log)
		{
			Session::set_flash('error', 'Log no encontrado');
			Response::redirect('admin/logs');
		}

		// Decodificar datos JSON
		if ($log['old_data'])
		{
			$log['old_data_decoded'] = json_decode($log['old_data'], true);
		}

		if ($log['new_data'])
		{
			$log['new_data_decoded'] = json_decode($log['new_data'], true);
		}

		// Obtener usuario (si existe)
		$user = null;
		if ($log['user_id'])
		{
			$user = DB::select('id', 'username', 'email', 'first_name', 'last_name')
				->from('users')
				->where('id', $log['user_id'])
				->execute()
				->current();
		}

		$data = [
			'title' => 'Detalle de Log #' . $id,
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'log' => $log,
			'user' => $user
		];

		$this->template->title = $data['title'];
		$this->template->content = View::forge('admin/logs/view', $data);
	}

	/**
	 * EXPORT - EXPORTAR LOGS A CSV
	 */
	public function action_export()
	{
		// Verificar permisos
		if (!Helper_Permission::can('logs', 'export'))
		{
			Session::set_flash('error', 'No tienes permisos para exportar logs');
			Response::redirect('admin/logs');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener filtros
		$filters = [
			'tenant_id' => $tenant_id,
			'module' => Input::get('module', null),
			'action' => Input::get('action', null),
			'user_id' => Input::get('user_id', null),
			'date_from' => Input::get('date_from', null),
			'date_to' => Input::get('date_to', null),
			'search' => Input::get('search', null)
		];

		$filters = array_filter($filters);

		// Obtener todos los logs
		$logs = Helper_Log::get($filters, 10000, 0);

		// Crear CSV
		$filename = 'logs_' . date('Y-m-d_His') . '.csv';
		
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		
		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
		
		fputcsv($output, ['ID', 'Fecha', 'Usuario', 'Módulo', 'Acción', 'Registro ID', 'Descripción', 'IP']);
		
		foreach ($logs as $log)
		{
			fputcsv($output, [
				$log['id'],
				$log['created_at'],
				$log['username'],
				$log['module'],
				$log['action'],
				$log['record_id'],
				$log['description'],
				$log['ip_address']
			]);
		}
		
		fclose($output);
		exit;
	}

	/**
	 * CLEANUP - LIMPIAR LOGS ANTIGUOS (AJAX)
	 */
	public function action_cleanup()
	{
		if (Input::method() !== 'POST')
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'Método no permitido'
			]), 405, ['Content-Type' => 'application/json']);
		}

		if (!Helper_Permission::can('logs', 'delete'))
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'No tienes permisos para eliminar logs'
			]), 403, ['Content-Type' => 'application/json']);
		}

		try
		{
			$days = Input::post('days', 90);
			$deleted = Helper_Log::cleanup($days);

			Helper_Log::record('logs', 'cleanup', null, "Limpieza de logs antiguos: {$deleted} registros eliminados (>{$days} días)");

			return Response::forge(json_encode([
				'success' => true,
				'message' => "Se eliminaron {$deleted} registros antiguos",
				'deleted' => $deleted
			]), 200, ['Content-Type' => 'application/json']);
		}
		catch (Exception $e)
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => $e->getMessage()
			]), 400, ['Content-Type' => 'application/json']);
		}
	}
}
