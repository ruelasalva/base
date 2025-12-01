<?php
/**
 * Modelo Tenant
 *
 * Modelo para gestión de tenants que utiliza exclusivamente la conexión
 * 'master' para consultar la configuración del tenant. Encapsula toda
 * la lógica de búsqueda y manejo de datos del tenant.
 *
 * SEGURIDAD: Este modelo SIEMPRE usa la conexión 'master' para proteger
 * los datos de configuración de tenants.
 *
 * @package    App
 * @subpackage Model
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

/**
 * Model_Tenant
 *
 * Gestiona la información de tenants desde la base de datos master.
 *
 * Estructura esperada de la tabla 'tenants':
 * <code>
 * CREATE TABLE tenants (
 *     id INT AUTO_INCREMENT PRIMARY KEY,
 *     domain VARCHAR(255) NOT NULL UNIQUE,
 *     db_name VARCHAR(255) NOT NULL,
 *     active_modules JSON,
 *     is_active TINYINT(1) DEFAULT 1,
 *     settings JSON,
 *     created_at DATETIME,
 *     updated_at DATETIME
 * );
 * </code>
 */
class Model_Tenant extends Model_Base
{
	/**
	 * @var string Nombre de la tabla
	 */
	protected static $_table_name = 'tenants';

	/**
	 * @var string Conexión de base de datos a utilizar (siempre master)
	 */
	protected static $_connection = 'master';

	/**
	 * @var string Clave primaria
	 */
	protected static $_primary_key = 'id';

	/**
	 * @var array Columnas que se pueden llenar
	 */
	protected static $_fillable = array(
		'domain',
		'db_name',
		'active_modules',
		'is_active',
		'settings',
	);

	/**
	 * @var array Columnas protegidas
	 */
	protected static $_guarded = array('id', 'created_at', 'updated_at');

	/**
	 * Obtener tenant por dominio
	 *
	 * @param string $domain El dominio a buscar
	 * @return array|null Datos del tenant o null si no existe
	 */
	public static function find_by_domain($domain)
	{
		// Validar dominio
		if (empty($domain) || ! is_string($domain))
		{
			return null;
		}

		// Sanitizar dominio (solo caracteres válidos de dominio)
		$domain = strtolower(trim($domain));

		// Validar formato de dominio más estricto:
		// - Permitir localhost
		// - Permitir dominios estándar (sin puntos al inicio/fin, sin doble puntos)
		// - Limitar longitud de etiquetas individuales (máximo 63 caracteres)
		if ($domain !== 'localhost')
		{
			// No permitir puntos al inicio o final, ni puntos dobles
			if (preg_match('/^\.|\.$|\.\./', $domain))
			{
				\Log::warning('Model_Tenant: Invalid domain format (dots): ' . substr($domain, 0, 50));
				return null;
			}

			// Validar cada etiqueta del dominio
			$labels = explode('.', $domain);
			foreach ($labels as $label)
			{
				// Cada etiqueta: 1-63 caracteres, alfanuméricos y guiones (no al inicio/fin)
				if ( ! preg_match('/^[a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?$/', $label))
				{
					\Log::warning('Model_Tenant: Invalid domain label format: ' . substr($domain, 0, 50));
					return null;
				}
			}
		}

		try
		{
			$result = DB::select('*')
				->from(static::$_table_name)
				->where('domain', '=', $domain)
				->where('is_active', '=', 1)
				->execute(static::$_connection)
				->as_array();

			if (empty($result))
			{
				return null;
			}

			$tenant = $result[0];

			// Deserializar módulos activos
			$tenant['active_modules_array'] = static::parse_active_modules($tenant['active_modules']);

			// Deserializar settings si existen
			if ( ! empty($tenant['settings']))
			{
				$tenant['settings_array'] = static::parse_json($tenant['settings']);
			}
			else
			{
				$tenant['settings_array'] = array();
			}

			return $tenant;
		}
		catch (\Exception $e)
		{
			\Log::error('Model_Tenant: Error finding by domain - ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Obtener tenant por ID
	 *
	 * @param int $id ID del tenant
	 * @return array|null
	 */
	public static function find_by_id($id)
	{
		$id = filter_var($id, FILTER_VALIDATE_INT);

		if ($id === false || $id < 1)
		{
			return null;
		}

		try
		{
			$result = DB::select('*')
				->from(static::$_table_name)
				->where('id', '=', $id)
				->execute(static::$_connection)
				->as_array();

			if (empty($result))
			{
				return null;
			}

			$tenant = $result[0];

			// Deserializar módulos activos
			$tenant['active_modules_array'] = static::parse_active_modules($tenant['active_modules']);

			// Deserializar settings
			if ( ! empty($tenant['settings']))
			{
				$tenant['settings_array'] = static::parse_json($tenant['settings']);
			}
			else
			{
				$tenant['settings_array'] = array();
			}

			return $tenant;
		}
		catch (\Exception $e)
		{
			\Log::error('Model_Tenant: Error finding by ID - ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Obtener todos los tenants activos
	 *
	 * @return array Lista de tenants activos
	 */
	public static function get_all_active()
	{
		try
		{
			$results = DB::select('*')
				->from(static::$_table_name)
				->where('is_active', '=', 1)
				->order_by('domain', 'asc')
				->execute(static::$_connection)
				->as_array();

			foreach ($results as &$tenant)
			{
				$tenant['active_modules_array'] = static::parse_active_modules($tenant['active_modules']);
			}

			return $results;
		}
		catch (\Exception $e)
		{
			\Log::error('Model_Tenant: Error getting all active - ' . $e->getMessage());
			return array();
		}
	}

	/**
	 * Verificar si un módulo está activo para un tenant
	 *
	 * @param int    $tenant_id  ID del tenant
	 * @param string $module_key Clave del módulo
	 * @return bool
	 */
	public static function is_module_active($tenant_id, $module_key)
	{
		$tenant = static::find_by_id($tenant_id);

		if ($tenant === null)
		{
			return false;
		}

		return in_array($module_key, $tenant['active_modules_array'], true);
	}

	/**
	 * Activar un módulo para un tenant
	 *
	 * @param int    $tenant_id  ID del tenant
	 * @param string $module_key Clave del módulo a activar
	 * @return bool True si la operación fue exitosa
	 */
	public static function activate_module($tenant_id, $module_key)
	{
		$tenant = static::find_by_id($tenant_id);

		if ($tenant === null)
		{
			return false;
		}

		// Validar module_key (solo alfanuméricos y guiones bajos)
		if ( ! preg_match('/^[a-z][a-z0-9_]*$/', $module_key))
		{
			\Log::warning('Model_Tenant: Invalid module key format: ' . substr($module_key, 0, 50));
			return false;
		}

		$modules = $tenant['active_modules_array'];

		// Si ya está activo, no hacer nada
		if (in_array($module_key, $modules, true))
		{
			return true;
		}

		// Agregar módulo
		$modules[] = $module_key;

		return static::update_active_modules($tenant_id, $modules);
	}

	/**
	 * Desactivar un módulo para un tenant
	 *
	 * @param int    $tenant_id  ID del tenant
	 * @param string $module_key Clave del módulo a desactivar
	 * @return bool True si la operación fue exitosa
	 */
	public static function deactivate_module($tenant_id, $module_key)
	{
		$tenant = static::find_by_id($tenant_id);

		if ($tenant === null)
		{
			return false;
		}

		$modules = $tenant['active_modules_array'];

		// Remover módulo del array
		$modules = array_values(array_diff($modules, array($module_key)));

		return static::update_active_modules($tenant_id, $modules);
	}

	/**
	 * Actualizar los módulos activos de un tenant
	 *
	 * @param int   $tenant_id ID del tenant
	 * @param array $modules   Array de claves de módulos activos
	 * @return bool True si la actualización fue exitosa
	 */
	public static function update_active_modules($tenant_id, array $modules)
	{
		$tenant_id = filter_var($tenant_id, FILTER_VALIDATE_INT);

		if ($tenant_id === false || $tenant_id < 1)
		{
			return false;
		}

		try
		{
			$json = json_encode(array_values($modules));

			if ($json === false)
			{
				\Log::error('Model_Tenant: Failed to encode modules JSON');
				return false;
			}

			$affected = DB::update(static::$_table_name)
				->set(array(
					'active_modules' => $json,
					'updated_at'     => date('Y-m-d H:i:s'),
				))
				->where('id', '=', $tenant_id)
				->execute(static::$_connection);

			return $affected > 0;
		}
		catch (\Exception $e)
		{
			\Log::error('Model_Tenant: Error updating modules - ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Obtener los módulos activos del tenant actual
	 *
	 * Usa la constante TENANT_ACTIVE_MODULES definida durante el bootstrap.
	 *
	 * @return array Array de claves de módulos activos
	 */
	public static function get_current_active_modules()
	{
		if ( ! defined('TENANT_ACTIVE_MODULES'))
		{
			return array();
		}

		$serialized = TENANT_ACTIVE_MODULES;

		if (empty($serialized) || ! is_string($serialized))
		{
			return array();
		}

		$modules = unserialize($serialized, array('allowed_classes' => false));

		if ($modules === false || ! is_array($modules))
		{
			return array();
		}

		return $modules;
	}

	/**
	 * Verificar si un módulo está activo para el tenant actual
	 *
	 * @param string $module_key Clave del módulo a verificar
	 * @return bool
	 */
	public static function is_current_module_active($module_key)
	{
		$modules = static::get_current_active_modules();

		return in_array($module_key, $modules, true);
	}

	/**
	 * Parsear JSON de módulos activos
	 *
	 * @param string|null $json JSON string de módulos activos
	 * @return array Array de claves de módulos
	 */
	protected static function parse_active_modules($json)
	{
		if (empty($json))
		{
			return array();
		}

		$modules = json_decode($json, true);

		if (json_last_error() !== JSON_ERROR_NONE)
		{
			\Log::warning('Model_Tenant: Failed to parse active_modules JSON');
			return array();
		}

		return is_array($modules) ? array_values($modules) : array();
	}

	/**
	 * Parsear JSON genérico
	 *
	 * @param string|null $json JSON string
	 * @return array|null Array parseado o null en caso de error
	 */
	protected static function parse_json($json)
	{
		if (empty($json))
		{
			return null;
		}

		$data = json_decode($json, true);

		if (json_last_error() !== JSON_ERROR_NONE)
		{
			return null;
		}

		return $data;
	}

	/**
	 * Crear un nuevo tenant
	 *
	 * @param array $data Datos del tenant (domain, db_name, etc.)
	 * @return int|false ID del nuevo tenant o false en caso de error
	 */
	public static function create_tenant(array $data)
	{
		// Validar datos requeridos
		if (empty($data['domain']) || empty($data['db_name']))
		{
			\Log::error('Model_Tenant: Missing required fields (domain, db_name)');
			return false;
		}

		// Validar formato de dominio
		if ( ! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-\.]*[a-zA-Z0-9]$|^localhost$/', $data['domain']))
		{
			\Log::error('Model_Tenant: Invalid domain format');
			return false;
		}

		// Validar formato de nombre de base de datos
		if ( ! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $data['db_name']))
		{
			\Log::error('Model_Tenant: Invalid database name format');
			return false;
		}

		try
		{
			// Preparar datos para inserción
			$insert_data = array(
				'domain'         => strtolower(trim($data['domain'])),
				'db_name'        => $data['db_name'],
				'active_modules' => isset($data['active_modules']) ? json_encode($data['active_modules']) : '[]',
				'is_active'      => isset($data['is_active']) ? (int) $data['is_active'] : 1,
				'settings'       => isset($data['settings']) ? json_encode($data['settings']) : null,
				'created_at'     => date('Y-m-d H:i:s'),
				'updated_at'     => date('Y-m-d H:i:s'),
			);

			$result = DB::insert(static::$_table_name)
				->set($insert_data)
				->execute(static::$_connection);

			return $result[0]; // Retorna el insert_id
		}
		catch (\Exception $e)
		{
			\Log::error('Model_Tenant: Error creating tenant - ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Actualizar datos de un tenant
	 *
	 * @param int   $tenant_id ID del tenant
	 * @param array $data      Datos a actualizar
	 * @return bool True si la actualización fue exitosa
	 */
	public static function update_tenant($tenant_id, array $data)
	{
		$tenant_id = filter_var($tenant_id, FILTER_VALIDATE_INT);

		if ($tenant_id === false || $tenant_id < 1)
		{
			return false;
		}

		try
		{
			$update_data = array();

			// Solo actualizar campos permitidos
			$allowed = array('domain', 'db_name', 'is_active');

			foreach ($allowed as $field)
			{
				if (isset($data[$field]))
				{
					$update_data[$field] = $data[$field];
				}
			}

			// Manejar active_modules y settings especialmente (JSON)
			if (isset($data['active_modules']))
			{
				$update_data['active_modules'] = is_array($data['active_modules'])
					? json_encode($data['active_modules'])
					: $data['active_modules'];
			}

			if (isset($data['settings']))
			{
				$update_data['settings'] = is_array($data['settings'])
					? json_encode($data['settings'])
					: $data['settings'];
			}

			if (empty($update_data))
			{
				return true; // Nada que actualizar
			}

			$update_data['updated_at'] = date('Y-m-d H:i:s');

			$affected = DB::update(static::$_table_name)
				->set($update_data)
				->where('id', '=', $tenant_id)
				->execute(static::$_connection);

			return $affected >= 0;
		}
		catch (\Exception $e)
		{
			\Log::error('Model_Tenant: Error updating tenant - ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Desactivar un tenant (soft delete)
	 *
	 * @param int $tenant_id ID del tenant
	 * @return bool True si la operación fue exitosa
	 */
	public static function deactivate_tenant($tenant_id)
	{
		return static::update_tenant($tenant_id, array('is_active' => 0));
	}

	/**
	 * Reactivar un tenant
	 *
	 * @param int $tenant_id ID del tenant
	 * @return bool True si la operación fue exitosa
	 */
	public static function reactivate_tenant($tenant_id)
	{
		return static::update_tenant($tenant_id, array('is_active' => 1));
	}
}
