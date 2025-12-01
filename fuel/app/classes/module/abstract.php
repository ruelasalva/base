<?php
/**
 * Clase Abstracta de Módulo (Contrato de Módulo)
 *
 * Define el contrato base que todo package/módulo del sistema multi-tenant
 * debe extender. Fuerza la implementación de métodos críticos de gestión
 * y ciclo de vida del módulo.
 *
 * Principio Rector: "Todo es un Módulo"
 *
 * @package    App
 * @subpackage Module
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

/**
 * Module_Abstract
 *
 * Clase abstracta que define el contrato para todos los módulos del sistema.
 * Proporciona métodos base y fuerza la implementación de métodos críticos.
 *
 * Ejemplo de uso:
 * <code>
 * class Partners_Module extends Module_Abstract
 * {
 *     public function get_module_name() { return 'Partners'; }
 *     public function get_module_key() { return 'partners'; }
 *     // ... implementar resto de métodos abstractos
 * }
 * </code>
 */
abstract class Module_Abstract
{
	/**
	 * @var string Versión del módulo
	 */
	protected $version = '1.0.0';

	/**
	 * @var array Dependencias del módulo (otros módulos requeridos)
	 */
	protected $dependencies = array();

	/**
	 * @var array Permisos que el módulo registra
	 */
	protected $permissions = array();

	/**
	 * @var bool Estado de inicialización del módulo
	 */
	protected $initialized = false;

	/**
	 * Obtener el nombre legible del módulo
	 *
	 * @return string Nombre del módulo (ej. "Partners", "Admin", "Providers")
	 */
	abstract public function get_module_name();

	/**
	 * Obtener la clave única del módulo
	 *
	 * Esta clave se usa para verificar si el módulo está activo en
	 * TENANT_ACTIVE_MODULES.
	 *
	 * @return string Clave del módulo (ej. "partners", "admin", "providers")
	 */
	abstract public function get_module_key();

	/**
	 * Obtener descripción del módulo
	 *
	 * @return string Descripción breve del módulo
	 */
	abstract public function get_description();

	/**
	 * Obtener las rutas del módulo
	 *
	 * Define las rutas que el módulo registra en el sistema de enrutamiento.
	 *
	 * @return array Array de rutas en formato FuelPHP
	 */
	abstract public function get_routes();

	/**
	 * Instalar el módulo para un tenant
	 *
	 * Este método se ejecuta cuando el módulo se activa por primera vez
	 * para un tenant específico. Debe crear las tablas necesarias y
	 * configuración inicial.
	 *
	 * IMPORTANTE: Las migraciones se ejecutan SOLO en la BD del tenant actual
	 * (conexión 'default'), NUNCA en la BD 'master'.
	 *
	 * @return bool True si la instalación fue exitosa
	 */
	abstract public function install();

	/**
	 * Desinstalar el módulo de un tenant
	 *
	 * Este método se ejecuta cuando el módulo se desactiva para un tenant.
	 * Puede limpiar datos específicos del módulo si es necesario.
	 *
	 * IMPORTANTE: Considerar si los datos deben preservarse o eliminarse.
	 *
	 * @param bool $preserve_data Si es true, preserva los datos existentes
	 * @return bool True si la desinstalación fue exitosa
	 */
	abstract public function uninstall($preserve_data = true);

	/**
	 * Inicializar el módulo
	 *
	 * Se ejecuta cuando el módulo se carga en el sistema.
	 * Registra clases, namespaces y rutas.
	 *
	 * @return void
	 */
	public function initialize()
	{
		if ($this->initialized)
		{
			return;
		}

		// Verificar que el módulo está activo para el tenant
		if ( ! $this->is_active())
		{
			\Log::debug(sprintf('Module %s: Not active for current tenant', $this->get_module_key()));
			return;
		}

		// Verificar dependencias
		if ( ! $this->check_dependencies())
		{
			\Log::error(sprintf('Module %s: Dependencies not met', $this->get_module_key()));
			return;
		}

		// Registrar clases del módulo
		$this->register_classes();

		// Registrar rutas del módulo
		$this->register_routes();

		// Hook de post-inicialización
		$this->on_initialize();

		$this->initialized = true;

		\Log::info(sprintf('Module %s: Initialized successfully', $this->get_module_key()));
	}

	/**
	 * Hook de post-inicialización
	 *
	 * Sobrescribir en módulos hijos para ejecutar lógica adicional
	 * después de la inicialización.
	 *
	 * @return void
	 */
	protected function on_initialize()
	{
		// Hook vacío - sobrescribir en módulos hijos
	}

	/**
	 * Verificar si el módulo está activo para el tenant actual
	 *
	 * @return bool
	 */
	public function is_active()
	{
		if ( ! defined('TENANT_ACTIVE_MODULES'))
		{
			return false;
		}

		$serialized = TENANT_ACTIVE_MODULES;

		if (empty($serialized) || ! is_string($serialized))
		{
			return false;
		}

		$active_modules = unserialize($serialized, array('allowed_classes' => false));

		if ($active_modules === false || ! is_array($active_modules))
		{
			return false;
		}

		return in_array($this->get_module_key(), $active_modules, true);
	}

	/**
	 * Verificar que las dependencias del módulo están activas
	 *
	 * @return bool True si todas las dependencias están activas
	 */
	public function check_dependencies()
	{
		if (empty($this->dependencies))
		{
			return true;
		}

		if ( ! defined('TENANT_ACTIVE_MODULES'))
		{
			return false;
		}

		$serialized = TENANT_ACTIVE_MODULES;

		if (empty($serialized) || ! is_string($serialized))
		{
			return false;
		}

		$active_modules = unserialize($serialized, array('allowed_classes' => false));

		if ($active_modules === false || ! is_array($active_modules))
		{
			return false;
		}

		foreach ($this->dependencies as $dependency)
		{
			if ( ! in_array($dependency, $active_modules, true))
			{
				\Log::warning(sprintf('Module %s: Missing dependency: %s', $this->get_module_key(), $dependency));
				return false;
			}
		}

		return true;
	}

	/**
	 * Obtener la versión del módulo
	 *
	 * @return string
	 */
	public function get_version()
	{
		return $this->version;
	}

	/**
	 * Obtener las dependencias del módulo
	 *
	 * @return array
	 */
	public function get_dependencies()
	{
		return $this->dependencies;
	}

	/**
	 * Obtener los permisos que registra el módulo
	 *
	 * @return array
	 */
	public function get_permissions()
	{
		return $this->permissions;
	}

	/**
	 * Obtener la ruta base del módulo
	 *
	 * @return string Ruta absoluta al directorio del módulo
	 */
	public function get_path()
	{
		$reflection = new \ReflectionClass($this);
		return dirname(dirname($reflection->getFileName()));
	}

	/**
	 * Registrar las clases del módulo con el autoloader
	 *
	 * Sobrescribir en módulos hijos para registrar clases específicas.
	 *
	 * @return void
	 */
	protected function register_classes()
	{
		// Implementación vacía - sobrescribir en módulos hijos
	}

	/**
	 * Registrar las rutas del módulo
	 *
	 * @return void
	 */
	protected function register_routes()
	{
		$routes = $this->get_routes();

		if ( ! empty($routes) && is_array($routes))
		{
			\Router::add($routes);
		}
	}

	/**
	 * Ejecutar migraciones del módulo en la BD del tenant
	 *
	 * IMPORTANTE: Las migraciones se ejecutan en la conexión 'default'
	 * que ya está configurada para el tenant actual.
	 *
	 * @param string $direction 'up' para migrar, 'down' para revertir
	 * @return bool True si las migraciones fueron exitosas
	 */
	protected function run_migrations($direction = 'up')
	{
		$migrations_path = $this->get_path() . DIRECTORY_SEPARATOR . 'migrations';

		if ( ! is_dir($migrations_path))
		{
			\Log::debug(sprintf('Module %s: No migrations directory found', $this->get_module_key()));
			return true;
		}

		try
		{
			// Obtener archivos de migración ordenados
			$migration_files = glob($migrations_path . DIRECTORY_SEPARATOR . '*.php');

			if (empty($migration_files))
			{
				return true;
			}

			// Ordenar migraciones
			sort($migration_files);

			// Si es 'down', invertir el orden
			if ($direction === 'down')
			{
				$migration_files = array_reverse($migration_files);
			}

			foreach ($migration_files as $file)
			{
				require_once $file;

				// Obtener nombre de clase de migración del archivo
				$class_name = $this->get_migration_class_name($file);

				if (class_exists($class_name))
				{
					$migration = new $class_name();

					if ($direction === 'up' && method_exists($migration, 'up'))
					{
						$migration->up();
						\Log::info(sprintf('Module %s: Ran migration up: %s', $this->get_module_key(), basename($file)));
					}
					elseif ($direction === 'down' && method_exists($migration, 'down'))
					{
						$migration->down();
						\Log::info(sprintf('Module %s: Ran migration down: %s', $this->get_module_key(), basename($file)));
					}
				}
			}

			return true;
		}
		catch (\Exception $e)
		{
			\Log::error(sprintf('Module %s: Migration error: %s', $this->get_module_key(), $e->getMessage()));
			return false;
		}
	}

	/**
	 * Obtener nombre de clase de migración desde el nombre de archivo
	 *
	 * @param string $file Ruta del archivo de migración
	 * @return string Nombre de la clase de migración
	 */
	protected function get_migration_class_name($file)
	{
		$filename = basename($file, '.php');

		// Formato esperado: 001_create_table_name
		// Clase esperada: Migration_001_Create_Table_Name
		$parts = explode('_', $filename, 2);

		if (count($parts) === 2)
		{
			$class_name = 'Migration_' . $parts[0] . '_' . \Inflector::camelize($parts[1]);
		}
		else
		{
			$class_name = 'Migration_' . \Inflector::camelize($filename);
		}

		return $class_name;
	}

	/**
	 * Obtener información completa del módulo
	 *
	 * @return array
	 */
	public function get_info()
	{
		return array(
			'name'         => $this->get_module_name(),
			'key'          => $this->get_module_key(),
			'description'  => $this->get_description(),
			'version'      => $this->get_version(),
			'dependencies' => $this->get_dependencies(),
			'permissions'  => $this->get_permissions(),
			'is_active'    => $this->is_active(),
			'path'         => $this->get_path(),
		);
	}
}
