<?php

/**
 * HELPER DASHBOARD
 * 
 * Helper para gestión de dashboard y widgets
 * - Obtener widgets disponibles
 * - Configuración de widgets por usuario
 * - Datos para widgets
 * 
 * @package  app
 * @author   Base Multi-Tenant System
 */
class Helper_Dashboard
{
	/**
	 * ENSURE LOADED
	 * 
	 * Asegura que el Helper esté cargado (método de compatibilidad)
	 * 
	 * @return void
	 */
	public static function ensure_loaded()
	{
		// Método vacío para compatibilidad con llamadas desde Controller
		// El autoloader de FuelPHP ya carga el Helper automáticamente
	}

	/**
	 * OBTENER WIDGETS DISPONIBLES PARA EL USUARIO
	 * 
	 * Filtra widgets según módulos activos y permisos
	 * 
	 * @param int $user_id
	 * @param int $tenant_id
	 * @return array
	 */
	public static function get_available_widgets($user_id, $tenant_id)
	{
		try
		{
			// Obtener todos los widgets activos
			$widgets = DB::select()->from('dashboard_widgets')
				->where('is_active', 1)
				->execute()
				->as_array();

			// Obtener módulos activos del tenant
			$active_modules = Helper_Module::get_active_modules($tenant_id);
			$active_module_names = array_column($active_modules, 'name');

			// Filtrar widgets según módulos requeridos
			$available_widgets = [];
			foreach ($widgets as $widget)
			{
				// Si el widget requiere módulos específicos
				if (!empty($widget['requires_modules']))
				{
					$required = json_decode($widget['requires_modules'], true);
					
					if (is_array($required))
					{
						// Verificar que todos los módulos requeridos estén activos
						$has_all_modules = true;
						foreach ($required as $module)
						{
							if (!in_array($module, $active_module_names))
							{
								$has_all_modules = false;
								break;
							}
						}
						
						if (!$has_all_modules)
						{
							continue; // Saltar este widget
						}
					}
				}

				$available_widgets[] = $widget;
			}

			return $available_widgets;
		}
		catch (\Exception $e)
		{
			\Log::error('Error en get_available_widgets: ' . $e->getMessage());
			return [];
		}
	}

	/**
	 * OBTENER CONFIGURACIÓN DE WIDGETS DEL USUARIO
	 * 
	 * @param int $user_id
	 * @param int $tenant_id
	 * @return array
	 */
	public static function get_user_widgets($user_id, $tenant_id)
	{
		try
		{
			$prefs = DB::select('dashboard_widgets')
				->from('user_preferences')
				->where('user_id', $user_id)
				->where('tenant_id', $tenant_id)
				->execute()
				->current();

			if ($prefs && !empty($prefs['dashboard_widgets']))
			{
				$config = json_decode($prefs['dashboard_widgets'], true);
				return is_array($config) ? $config : self::get_default_widgets();
			}

			return self::get_default_widgets();
		}
		catch (\Exception $e)
		{
			\Log::error('Error en get_user_widgets: ' . $e->getMessage());
			return self::get_default_widgets();
		}
	}

	/**
	 * WIDGETS POR DEFECTO
	 * 
	 * @return array
	 */
	private static function get_default_widgets()
	{
		return [
			'widgets' => [
				'stats_users',
				'recent_activity'
			],
			'refresh_interval' => 300
		];
	}

	/**
	 * GUARDAR CONFIGURACIÓN DE WIDGETS
	 * 
	 * @param int $user_id
	 * @param int $tenant_id
	 * @param array $config
	 * @return bool
	 */
	public static function save_user_widgets($user_id, $tenant_id, $config)
	{
		try
		{
			$widgets_json = json_encode($config);

			// Verificar si ya existe configuración
			$exists = DB::select('id')
				->from('user_preferences')
				->where('user_id', $user_id)
				->where('tenant_id', $tenant_id)
				->execute()
				->current();

			if ($exists)
			{
				// Actualizar
				DB::update('user_preferences')
					->set(['dashboard_widgets' => $widgets_json])
					->where('id', $exists['id'])
					->execute();
			}
			else
			{
				// Insertar
				DB::insert('user_preferences')
					->set([
						'user_id' => $user_id,
						'tenant_id' => $tenant_id,
						'dashboard_widgets' => $widgets_json
					])
					->execute();
			}

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error('Error en save_user_widgets: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * OBTENER DATOS PARA WIDGET: Usuarios Activos
	 * 
	 * @param int $tenant_id
	 * @return array
	 */
	public static function widget_stats_users($tenant_id)
	{
		$total = DB::select(DB::expr('COUNT(*) as total'))
			->from('users')
			->where('tenant_id', $tenant_id)
			->execute()
			->get('total', 0);

		$active_today = DB::select(DB::expr('COUNT(DISTINCT user_id) as total'))
			->from('user_activity')
			->where('tenant_id', $tenant_id)
			->where('created_at', '>=', DB::expr('CURDATE()'))
			->execute()
			->get('total', 0);

		return [
			'total_users' => $total,
			'active_today' => $active_today,
			'active_percentage' => $total > 0 ? round(($active_today / $total) * 100) : 0
		];
	}

	/**
	 * OBTENER DATOS PARA WIDGET: Ventas del Día
	 * 
	 * @param int $tenant_id
	 * @return array
	 */
	public static function widget_sales_today($tenant_id)
	{
		// Verificar si el módulo de ventas está activo
		if (!Helper_Module::is_active('sales', $tenant_id))
		{
			return ['total_today' => 0, 'count' => 0, 'trend' => 0];
		}

		// Total de ventas hoy
		$today = DB::select(
				DB::expr('COUNT(*) as count'),
				DB::expr('COALESCE(SUM(total), 0) as total')
			)
			->from('sales')
			->where('tenant_id', $tenant_id)
			->where('date', '>=', DB::expr('CURDATE()'))
			->where('status', '!=', 'cancelled')
			->execute()
			->current();

		// Total de ventas ayer (para comparación)
		$yesterday = DB::select(DB::expr('COALESCE(SUM(total), 0) as total'))
			->from('sales')
			->where('tenant_id', $tenant_id)
			->where('date', '=', DB::expr('CURDATE() - INTERVAL 1 DAY'))
			->where('status', '!=', 'cancelled')
			->execute()
			->get('total', 0);

		$trend = 0;
		if ($yesterday > 0)
		{
			$trend = round((($today['total'] - $yesterday) / $yesterday) * 100, 1);
		}

		return [
			'total_today' => floatval($today['total']),
			'count' => intval($today['count']),
			'trend' => floatval($trend)
		];
	}

	/**
	 * OBTENER DATOS PARA WIDGET: Gráfica de Ventas Semanal
	 * 
	 * @param int $tenant_id
	 * @return array
	 */
	public static function widget_sales_chart_week($tenant_id)
	{
		if (!Helper_Module::is_active('sales', $tenant_id))
		{
			return ['labels' => [], 'data' => []];
		}

		$sales = DB::select(
				DB::expr('DATE(date) as sale_date'),
				DB::expr('COALESCE(SUM(total), 0) as daily_total')
			)
			->from('sales')
			->where('tenant_id', $tenant_id)
			->where('date', '>=', DB::expr('CURDATE() - INTERVAL 7 DAY'))
			->where('status', '!=', 'cancelled')
			->group_by('sale_date')
			->order_by('sale_date', 'ASC')
			->execute()
			->as_array();

		$labels = [];
		$data = [];
		foreach ($sales as $sale)
		{
			$labels[] = date('d/m', strtotime($sale['sale_date']));
			$data[] = floatval($sale['daily_total']);
		}

		return [
			'labels' => $labels,
			'data' => $data
		];
	}

	/**
	 * OBTENER DATOS PARA WIDGET: Top 10 Productos
	 * 
	 * @param int $tenant_id
	 * @return array
	 */
	public static function widget_top_products($tenant_id)
	{
		if (!Helper_Module::is_active('sales', $tenant_id) || !Helper_Module::is_active('inventory', $tenant_id))
		{
			return ['labels' => [], 'data' => []];
		}

		$products = DB::select(
				'p.name',
				DB::expr('COALESCE(SUM(si.quantity), 0) as total_sold')
			)
			->from(['sales_items', 'si'])
			->join(['products', 'p'], 'LEFT')
			->on('si.product_id', '=', 'p.id')
			->join(['sales', 's'], 'LEFT')
			->on('si.sale_id', '=', 's.id')
			->where('s.tenant_id', $tenant_id)
			->where('s.date', '>=', DB::expr('CURDATE() - INTERVAL 30 DAY'))
			->where('s.status', '!=', 'cancelled')
			->group_by('p.id', 'p.name')
			->order_by('total_sold', 'DESC')
			->limit(10)
			->execute()
			->as_array();

		$labels = [];
		$data = [];
		foreach ($products as $product)
		{
			$labels[] = $product['name'];
			$data[] = intval($product['total_sold']);
		}

		return [
			'labels' => $labels,
			'data' => $data
		];
	}

	/**
	 * OBTENER DATOS PARA WIDGET: Facturas Pendientes
	 * 
	 * @param int $tenant_id
	 * @return array
	 */
	public static function widget_pending_invoices($tenant_id)
	{
		if (!Helper_Module::is_active('facturacion', $tenant_id))
		{
			return ['count' => 0, 'items' => []];
		}

		$pending = DB::select('id', 'serie', 'folio', 'total', 'created_at')
			->from('invoices_cfdi')
			->where('tenant_id', $tenant_id)
			->where('status', 'draft')
			->order_by('created_at', 'DESC')
			->limit(5)
			->execute()
			->as_array();

		return [
			'count' => count($pending),
			'items' => $pending
		];
	}

	/**
	 * OBTENER DATOS PARA WIDGET: Inventario Crítico
	 * 
	 * @param int $tenant_id
	 * @return array
	 */
	public static function widget_critical_inventory($tenant_id)
	{
		if (!Helper_Module::is_active('inventory', $tenant_id))
		{
			return ['count' => 0, 'products' => []];
		}

		$critical = DB::select('id', 'name', 'stock', 'min_stock')
			->from('products')
			->where('tenant_id', $tenant_id)
			->where('stock', '<=', DB::expr('min_stock'))
			->where('is_active', 1)
			->order_by('stock', 'ASC')
			->limit(5)
			->execute()
			->as_array();

		return [
			'count' => count($critical),
			'products' => $critical
		];
	}

	/**
	 * OBTENER DATOS PARA WIDGET: Flujo de Efectivo
	 * 
	 * @param int $tenant_id
	 * @return array
	 */
	public static function widget_cash_flow($tenant_id)
	{
		if (!Helper_Module::is_active('contabilidad', $tenant_id))
		{
			return ['labels' => [], 'income' => [], 'expenses' => []];
		}

		// Últimos 30 días de ingresos y egresos
		$days = [];
		$income = [];
		$expenses = [];

		for ($i = 29; $i >= 0; $i--)
		{
			$date = date('Y-m-d', strtotime("-{$i} days"));
			$days[] = date('d/m', strtotime($date));

			// Simular datos (reemplazar con consultas reales a journals)
			$income[] = rand(5000, 25000);
			$expenses[] = rand(3000, 15000);
		}

		return [
			'labels' => $days,
			'income' => $income,
			'expenses' => $expenses
		];
	}

	/**
	 * OBTENER DATOS PARA WIDGET: Actividad Reciente
	 * 
	 * @param int $tenant_id
	 * @return array
	 */
	public static function widget_recent_activity($tenant_id)
	{
		try
		{
			$activities = DB::select('ua.id', 'ua.action', 'ua.description', 'ua.created_at', 'u.username')
				->from(['user_activity', 'ua'])
				->join(['users', 'u'], 'LEFT')
				->on('ua.user_id', '=', 'u.id')
				->order_by('ua.created_at', 'DESC')
				->limit(10)
				->execute()
				->as_array();

			// Formatear tiempo relativo
			foreach ($activities as &$activity)
			{
				$time = strtotime($activity['created_at']);
				$diff = time() - $time;
				
				if ($diff < 60) {
					$activity['time_ago'] = 'Hace ' . $diff . ' segundos';
				} elseif ($diff < 3600) {
					$activity['time_ago'] = 'Hace ' . floor($diff / 60) . ' minutos';
				} elseif ($diff < 86400) {
					$activity['time_ago'] = 'Hace ' . floor($diff / 3600) . ' horas';
				} else {
					$activity['time_ago'] = 'Hace ' . floor($diff / 86400) . ' días';
				}
				
				$activity['user'] = isset($activity['username']) ? $activity['username'] : 'Sistema';
			}

			return [
				'count' => count($activities),
				'activities' => $activities
			];
		}
		catch (\Exception $e)
		{
			\Log::error('Error en widget_recent_activity: ' . $e->getMessage());
			return ['count' => 0, 'activities' => []];
		}
	}

	/**
	 * OBTENER DATOS PARA WIDGET: Valor de Inventario
	 * 
	 * @param int $tenant_id
	 * @return array
	 */
	public static function widget_inventory_value($tenant_id)
	{
		if (!Helper_Module::is_active('inventory', $tenant_id))
		{
			return ['total_value' => 0, 'total_products' => 0, 'total_stock' => 0];
		}

		try
		{
			$result = DB::select(
					DB::expr('COUNT(*) as total_products'),
					DB::expr('COALESCE(SUM(stock), 0) as total_stock'),
					DB::expr('COALESCE(SUM(stock * price), 0) as total_value')
				)
				->from('products')
				->where('tenant_id', $tenant_id)
				->where('is_active', 1)
				->execute()
				->current();

			return [
				'total_value' => floatval($result['total_value']),
				'total_products' => intval($result['total_products']),
				'total_stock' => intval($result['total_stock'])
			];
		}
		catch (\Exception $e)
		{
			\Log::error('Error en widget_inventory_value: ' . $e->getMessage());
			return ['total_value' => 0, 'total_products' => 0, 'total_stock' => 0];
		}
	}

	/**
	 * OBTENER DATOS PARA WIDGET: Cuentas por Cobrar
	 * 
	 * @param int $tenant_id
	 * @return array
	 */
	public static function widget_accounts_receivable($tenant_id)
	{
		if (!Helper_Module::is_active('finance', $tenant_id))
		{
			return ['total_receivable' => 0, 'overdue_count' => 0, 'pending_count' => 0];
		}

		try
		{
			// Total de cuentas por cobrar
			$receivable = DB::select(
					DB::expr('COUNT(*) as pending_count'),
					DB::expr('COALESCE(SUM(amount), 0) as total_receivable')
				)
				->from('accounts_receivable')
				->where('status', 'pending')
				->execute()
				->current();

			// Cuentas vencidas
			$overdue = DB::select(DB::expr('COUNT(*) as overdue_count'))
				->from('accounts_receivable')
				->where('status', 'pending')
				->where('due_date', '<', DB::expr('CURDATE()'))
				->execute()
				->get('overdue_count', 0);

			return [
				'total_receivable' => floatval($receivable['total_receivable']),
				'pending_count' => intval($receivable['pending_count']),
				'overdue_count' => intval($overdue)
			];
		}
		catch (\Exception $e)
		{
			\Log::error('Error en widget_accounts_receivable: ' . $e->getMessage());
			return ['total_receivable' => 0, 'overdue_count' => 0, 'pending_count' => 0];
		}
	}
}
