<?php

/**
 * HELPER MODULE
 * 
 * Helper para gestión de módulos del sistema
 * - Obtener módulos disponibles y activos
 * - Activar/desactivar módulos por tenant
 * - Verificar dependencias
 * - Ejecutar migraciones automáticas
 * 
 * @package  app
 * @author   Base Multi-Tenant System
 */
class Helper_Module
{
	/**
	 * CACHE DE MÓDULOS
	 */
	private static $modules_cache = null;
	private static $tenant_modules_cache = [];

	/**
	 * OBTENER TODOS LOS MÓDULOS
	 * 
	 * @param string $category Filtrar por categoría (opcional)
	 * @return array
	 */
	public static function get_all($category = null)
	{
		if (self::$modules_cache === null)
		{
			$query = DB::select()->from('modules')
				->order_by('menu_order', 'ASC');
			
			if ($category)
			{
				$query->where('category', $category);
			}
			
			self::$modules_cache = $query->execute()->as_array();
		}
		
		return self::$modules_cache;
	}

	/**
	 * OBTENER MÓDULOS ACTIVOS PARA UN TENANT
	 * 
	 * @param int $tenant_id
	 * @return array
	 */
	public static function get_active_modules($tenant_id)
	{
		if (!isset(self::$tenant_modules_cache[$tenant_id]))
		{
			$result = DB::select('m.*')
				->from(['modules', 'm'])
				->join(['tenant_modules', 'tm'], 'INNER')
				->on('m.id', '=', 'tm.module_id')
				->where('tm.tenant_id', $tenant_id)
				->where('tm.is_active', 1)
				->order_by('m.menu_order', 'ASC')
				->execute()
				->as_array();
			
			self::$tenant_modules_cache[$tenant_id] = $result;
		}
		
		return self::$tenant_modules_cache[$tenant_id];
	}

	/**
	 * VERIFICAR SI UN MÓDULO ESTÁ ACTIVO PARA UN TENANT
	 * 
	 * @param string $module_name Nombre del módulo
	 * @param int $tenant_id
	 * @return bool
	 */
	public static function is_active($module_name, $tenant_id = null)
	{
		if ($tenant_id === null)
		{
			$tenant_id = Session::get('tenant_id', 1);
		}

		$result = DB::select('tm.is_active')
			->from(['modules', 'm'])
			->join(['tenant_modules', 'tm'], 'INNER')
			->on('m.id', '=', 'tm.module_id')
			->where('m.name', $module_name)
			->where('tm.tenant_id', $tenant_id)
			->execute()
			->current();

		return $result ? (bool) $result['is_active'] : false;
	}

	/**
	 * ACTIVAR UN MÓDULO PARA UN TENANT
	 * 
	 * @param int $module_id
	 * @param int $tenant_id
	 * @param int $user_id Usuario que activa
	 * @return array ['success' => bool, 'message' => string]
	 */
	public static function enable($module_id, $tenant_id, $user_id)
	{
		try
		{
			// Obtener información del módulo
			$module = DB::select()->from('modules')
				->where('id', $module_id)
				->execute()
				->current();

			if (!$module)
			{
				return ['success' => false, 'message' => 'Módulo no encontrado'];
			}

			// Verificar si es módulo core (no se puede desactivar)
			if ($module['is_core'])
			{
				return ['success' => false, 'message' => 'Los módulos core no pueden ser desactivados'];
			}

		// Verificar dependencias
		if (!empty($module['requires_modules']))
		{
			$required = json_decode($module['requires_modules'], true);
			if (is_array($required) && count($required) > 0)
			{
				foreach ($required as $req_module)
				{
					if (!self::is_active($req_module, $tenant_id))
					{
						return ['success' => false, 'message' => "Requiere el módulo: {$req_module}"];
					}
				}
			}
		}			// Verificar si ya está activo
			$existing = DB::select('id')->from('tenant_modules')
				->where('tenant_id', $tenant_id)
				->where('module_id', $module_id)
				->execute()
				->current();

			if ($existing)
			{
				// Actualizar
				DB::update('tenant_modules')
					->set([
						'is_active' => 1,
						'activated_at' => Date::forge()->format('mysql'),
						'activated_by' => $user_id
					])
					->where('id', $existing['id'])
					->execute();
			}
			else
			{
				// Insertar
				DB::insert('tenant_modules')
					->set([
						'tenant_id' => $tenant_id,
						'module_id' => $module_id,
						'is_active' => 1,
						'activated_by' => $user_id
					])
					->execute();
			}

			// Ejecutar migración si es necesario
			if ($module['has_migration'] && $module['migration_file'])
			{
				self::run_migration($module['migration_file']);
			}

			// Limpiar cache
			self::clear_cache($tenant_id);

			return ['success' => true, 'message' => "Módulo {$module['display_name']} activado correctamente"];
		}
		catch (Exception $e)
		{
			\Log::error('Error activando módulo: ' . $e->getMessage());
			return ['success' => false, 'message' => 'Error al activar el módulo'];
		}
	}

	/**
	 * DESACTIVAR UN MÓDULO PARA UN TENANT
	 * 
	 * @param int $module_id
	 * @param int $tenant_id
	 * @return array ['success' => bool, 'message' => string]
	 */
	public static function disable($module_id, $tenant_id)
	{
		try
		{
			// Obtener información del módulo
			$module = DB::select()->from('modules')
				->where('id', $module_id)
				->execute()
				->current();

			if (!$module)
			{
				return ['success' => false, 'message' => 'Módulo no encontrado'];
			}

			// Verificar si es módulo core
			if ($module['is_core'])
			{
				return ['success' => false, 'message' => 'Los módulos core no pueden ser desactivados'];
			}

			// Desactivar
			DB::update('tenant_modules')
				->set([
					'is_active' => 0,
					'deactivated_at' => Date::forge()->format('mysql')
				])
				->where('tenant_id', $tenant_id)
				->where('module_id', $module_id)
				->execute();

			// Limpiar cache
			self::clear_cache($tenant_id);

			return ['success' => true, 'message' => "Módulo {$module['display_name']} desactivado"];
		}
		catch (Exception $e)
		{
			\Log::error('Error desactivando módulo: ' . $e->getMessage());
			return ['success' => false, 'message' => 'Error al desactivar el módulo'];
		}
	}

	/**
	 * EJECUTAR MIGRACIÓN DE UN MÓDULO
	 * 
	 * @param string $migration_file
	 * @return bool
	 */
	private static function run_migration($migration_file)
	{
		$migration_path = APPPATH . 'migrations/modules/' . $migration_file;
		
		if (!file_exists($migration_path))
		{
			\Log::warning("Archivo de migración no encontrado: {$migration_path}");
			return false;
		}

		try
		{
			$sql = file_get_contents($migration_path);
			DB::query($sql)->execute();
			\Log::info("Migración ejecutada: {$migration_file}");
			return true;
		}
		catch (Exception $e)
		{
			\Log::error("Error ejecutando migración {$migration_file}: " . $e->getMessage());
			return false;
		}
	}

	/**
	 * LIMPIAR CACHE DE MÓDULOS
	 * 
	 * @param int $tenant_id
	 */
	public static function clear_cache($tenant_id = null)
	{
		self::$modules_cache = null;
		
		if ($tenant_id)
		{
			unset(self::$tenant_modules_cache[$tenant_id]);
		}
		else
		{
			self::$tenant_modules_cache = [];
		}
	}

	/**
	 * OBTENER MÓDULOS POR CATEGORÍA CON ESTADO ACTIVO/INACTIVO
	 * 
	 * @param int $tenant_id
	 * @return array Agrupado por categoría
	 */
	public static function get_modules_by_category($tenant_id)
	{
		$all_modules = self::get_all();
		$active_modules = self::get_active_modules($tenant_id);
		
		$active_ids = array_column($active_modules, 'id');
		
		$grouped = [];
		foreach ($all_modules as $module)
		{
			$category = $module['category'];
			if (!isset($grouped[$category]))
			{
				$grouped[$category] = [];
			}
			
			$module['is_active_for_tenant'] = in_array($module['id'], $active_ids);
			$grouped[$category][] = $module;
		}
		
		return $grouped;
	}
}
