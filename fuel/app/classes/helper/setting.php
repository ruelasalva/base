<?php
/**
 * Helper_Setting
 * 
 * Gestión de configuraciones del sistema multi-tenant
 * Cacheo automático de configuraciones para rendimiento
 * 
 * @package    Base
 * @category   Helpers
 * @author     Admin
 */

class Helper_Setting
{
	/**
	 * Cache de configuraciones en memoria
	 */
	private static $cache = [];

	/**
	 * Obtener valor de configuración
	 * 
	 * @param string $key Clave en formato 'category.setting_key'
	 * @param mixed $default Valor por defecto si no existe
	 * @param int|null $tenant_id ID del tenant (null = actual)
	 * @return mixed
	 */
	public static function get($key, $default = null, $tenant_id = null)
	{
		try {
			if ($tenant_id === null) {
				$tenant_id = Auth::check() ? Auth::get('tenant_id') : 1;
			}

			// Verificar cache
			$cache_key = "{$tenant_id}.{$key}";
			if (isset(self::$cache[$cache_key])) {
				return self::$cache[$cache_key];
			}

			// Separar categoría y clave
			$parts = explode('.', $key, 2);
			if (count($parts) != 2) {
				\Log::warning("Helper_Setting::get() - Formato de clave inválido: {$key}");
				return $default;
			}

			list($category, $setting_key) = $parts;

			// Buscar en base de datos
			$setting = DB::select('setting_value', 'setting_type')
				->from('system_settings')
				->where('tenant_id', $tenant_id)
				->where('category', $category)
				->where('setting_key', $setting_key)
				->execute()
				->current();

			if (empty($setting)) {
				return $default;
			}

			// Convertir según tipo
			$value = self::cast_value($setting['setting_value'], $setting['setting_type']);

			// Guardar en cache
			self::$cache[$cache_key] = $value;

			return $value;

		} catch (Exception $e) {
			\Log::error('Helper_Setting::get() - Error: ' . $e->getMessage());
			return $default;
		}
	}

	/**
	 * Establecer valor de configuración
	 * 
	 * @param string $key Clave en formato 'category.setting_key'
	 * @param mixed $value Valor a guardar
	 * @param string $type Tipo: string, integer, boolean, json, text
	 * @param string|null $description Descripción
	 * @param int|null $tenant_id ID del tenant
	 * @return bool
	 */
	public static function set($key, $value, $type = 'string', $description = null, $tenant_id = null)
	{
		try {
			if ($tenant_id === null) {
				$tenant_id = Auth::check() ? Auth::get('tenant_id') : 1;
			}

			// Separar categoría y clave
			$parts = explode('.', $key, 2);
			if (count($parts) != 2) {
				\Log::warning("Helper_Setting::set() - Formato de clave inválido: {$key}");
				return false;
			}

			list($category, $setting_key) = $parts;

			// Convertir valor a string según tipo
			$string_value = self::value_to_string($value, $type);

			// Verificar si existe
			$exists = DB::select(DB::expr('COUNT(*) as total'))
				->from('system_settings')
				->where('tenant_id', $tenant_id)
				->where('category', $category)
				->where('setting_key', $setting_key)
				->execute()
				->get('total');

			if ($exists > 0) {
				// Actualizar
				$rows = DB::update('system_settings')
					->set([
						'setting_value' => $string_value,
						'setting_type' => $type,
						'description' => $description,
						'updated_at' => date('Y-m-d H:i:s')
					])
					->where('tenant_id', $tenant_id)
					->where('category', $category)
					->where('setting_key', $setting_key)
					->execute();

				$success = $rows > 0;
			} else {
				// Insertar
				list($insert_id, $rows) = DB::insert('system_settings')
					->set([
						'tenant_id' => $tenant_id,
						'category' => $category,
						'setting_key' => $setting_key,
						'setting_value' => $string_value,
						'setting_type' => $type,
						'description' => $description
					])
					->execute();

				$success = $rows > 0;
			}

			if ($success) {
				// Limpiar cache
				$cache_key = "{$tenant_id}.{$key}";
				unset(self::$cache[$cache_key]);

				// Registrar en auditoría
				Helper_Log::record('settings', $exists > 0 ? 'update' : 'create', null,
					"Configuración {$key} " . ($exists > 0 ? 'actualizada' : 'creada'),
					$exists > 0 ? ['value' => $string_value] : null,
					['key' => $key, 'value' => $string_value, 'type' => $type]
				);
			}

			return $success;

		} catch (Exception $e) {
			\Log::error('Helper_Setting::set() - Error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Obtener todas las configuraciones de una categoría
	 * 
	 * @param string $category Categoría
	 * @param int|null $tenant_id ID del tenant
	 * @return array
	 */
	public static function get_category($category, $tenant_id = null)
	{
		try {
			if ($tenant_id === null) {
				$tenant_id = Auth::check() ? Auth::get('tenant_id') : 1;
			}

			$settings = DB::select('setting_key', 'setting_value', 'setting_type', 'description')
				->from('system_settings')
				->where('tenant_id', $tenant_id)
				->where('category', $category)
				->order_by('setting_key', 'ASC')
				->execute()
				->as_array();

			$result = [];
			foreach ($settings as $setting) {
				$result[$setting['setting_key']] = [
					'value' => self::cast_value($setting['setting_value'], $setting['setting_type']),
					'type' => $setting['setting_type'],
					'description' => $setting['description']
				];
			}

			return $result;

		} catch (Exception $e) {
			\Log::error('Helper_Setting::get_category() - Error: ' . $e->getMessage());
			return [];
		}
	}

	/**
	 * Obtener todas las categorías disponibles
	 * 
	 * @param int|null $tenant_id ID del tenant
	 * @return array
	 */
	public static function get_categories($tenant_id = null)
	{
		try {
			if ($tenant_id === null) {
				$tenant_id = Auth::check() ? Auth::get('tenant_id') : 1;
			}

			$categories = DB::select(DB::expr('DISTINCT category'))
				->from('system_settings')
				->where('tenant_id', $tenant_id)
				->order_by('category', 'ASC')
				->execute()
				->as_array();

			return array_column($categories, 'category');

		} catch (Exception $e) {
			\Log::error('Helper_Setting::get_categories() - Error: ' . $e->getMessage());
			return [];
		}
	}

	/**
	 * Eliminar una configuración
	 * 
	 * @param string $key Clave completa
	 * @param int|null $tenant_id ID del tenant
	 * @return bool
	 */
	public static function delete($key, $tenant_id = null)
	{
		try {
			if ($tenant_id === null) {
				$tenant_id = Auth::check() ? Auth::get('tenant_id') : 1;
			}

			$parts = explode('.', $key, 2);
			if (count($parts) != 2) {
				return false;
			}

			list($category, $setting_key) = $parts;

			$rows = DB::delete('system_settings')
				->where('tenant_id', $tenant_id)
				->where('category', $category)
				->where('setting_key', $setting_key)
				->execute();

			if ($rows > 0) {
				// Limpiar cache
				$cache_key = "{$tenant_id}.{$key}";
				unset(self::$cache[$cache_key]);

				// Auditoría
				Helper_Log::record('settings', 'delete', null,
					"Configuración {$key} eliminada",
					['key' => $key],
					null
				);
			}

			return $rows > 0;

		} catch (Exception $e) {
			\Log::error('Helper_Setting::delete() - Error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Limpiar cache de configuraciones
	 */
	public static function clear_cache()
	{
		self::$cache = [];
	}

	/**
	 * Convertir valor de string a tipo específico
	 * 
	 * @param string $value Valor en string
	 * @param string $type Tipo
	 * @return mixed
	 */
	private static function cast_value($value, $type)
	{
		if ($value === null) {
			return null;
		}

		switch ($type) {
			case 'integer':
				return (int)$value;

			case 'boolean':
				return filter_var($value, FILTER_VALIDATE_BOOLEAN);

			case 'json':
				return json_decode($value, true);

			case 'string':
			case 'text':
			default:
				return $value;
		}
	}

	/**
	 * Convertir valor a string para almacenar
	 * 
	 * @param mixed $value Valor
	 * @param string $type Tipo
	 * @return string
	 */
	private static function value_to_string($value, $type)
	{
		if ($value === null) {
			return null;
		}

		switch ($type) {
			case 'boolean':
				return $value ? '1' : '0';

			case 'json':
				return json_encode($value);

			case 'integer':
			case 'string':
			case 'text':
			default:
				return (string)$value;
		}
	}
}
