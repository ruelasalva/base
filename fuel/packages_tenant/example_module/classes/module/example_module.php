<?php
/**
 * Example Module - Module Class (Contrato de Módulo)
 *
 * Implementación de referencia de un módulo que extiende Module_Abstract.
 *
 * @package    Example_Module
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace Example_Module;

/**
 * Module
 *
 * Clase principal del módulo que implementa el contrato de módulo.
 * Esta clase es el punto central de gestión del ciclo de vida del módulo.
 */
class Module extends \Module_Abstract
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
	protected $permissions = array(
		'example_module.access',
		'example_module.create',
		'example_module.edit',
		'example_module.delete',
	);

	/**
	 * Obtener el nombre legible del módulo
	 *
	 * @return string
	 */
	public function get_module_name()
	{
		return 'Example Module';
	}

	/**
	 * Obtener la clave única del módulo
	 *
	 * @return string
	 */
	public function get_module_key()
	{
		return 'example_module';
	}

	/**
	 * Obtener descripción del módulo
	 *
	 * @return string
	 */
	public function get_description()
	{
		return 'Módulo de ejemplo que demuestra la arquitectura modular unificada del sistema ERP multi-tenant.';
	}

	/**
	 * Obtener las rutas del módulo
	 *
	 * @return array
	 */
	public function get_routes()
	{
		return array(
			'example'        => 'example_module/example/index',
			'example/(:any)' => 'example_module/example/$1',
		);
	}

	/**
	 * Instalar el módulo
	 *
	 * Este método se ejecuta cuando el módulo se activa por primera vez
	 * para un tenant específico. Ejecuta las migraciones en la BD del tenant.
	 *
	 * @return bool True si la instalación fue exitosa
	 */
	public function install()
	{
		try
		{
			// Ejecutar migraciones en la BD del tenant (conexión 'default')
			$this->run_migrations('up');

			// Insertar datos iniciales si es necesario
			$this->setup_initial_data();

			\Log::info('Example Module: Installation completed successfully');
			return true;
		}
		catch (\Exception $e)
		{
			\Log::error('Example Module: Installation failed - ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Desinstalar el módulo
	 *
	 * Este método se ejecuta cuando el módulo se desactiva para un tenant.
	 *
	 * @param bool $preserve_data Si es true, preserva los datos existentes
	 * @return bool True si la desinstalación fue exitosa
	 */
	public function uninstall($preserve_data = true)
	{
		try
		{
			if ( ! $preserve_data)
			{
				// Revertir migraciones (eliminar tablas)
				$this->run_migrations('down');
				\Log::info('Example Module: Data removed');
			}

			\Log::info('Example Module: Uninstallation completed');
			return true;
		}
		catch (\Exception $e)
		{
			\Log::error('Example Module: Uninstallation failed - ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Registrar clases del módulo con el autoloader
	 *
	 * @return void
	 */
	protected function register_classes()
	{
		$base_path = $this->get_path() . DIRECTORY_SEPARATOR . 'classes';

		\Autoloader::add_classes(array(
			'Example_Module\\Controller_Example' => $base_path . '/controller/example.php',
			'Example_Module\\Model_Example'      => $base_path . '/model/example.php',
			'Example_Module\\Service_Example'    => $base_path . '/service/example.php',
		));

		\Autoloader::add_namespace('Example_Module', $base_path . '/');
	}

	/**
	 * Configurar datos iniciales del módulo
	 *
	 * @return void
	 */
	protected function setup_initial_data()
	{
		// Aquí se insertarían datos iniciales si fueran necesarios
		// Por ejemplo, configuraciones por defecto, registros de ejemplo, etc.
	}

	/**
	 * Hook de post-inicialización
	 *
	 * @return void
	 */
	protected function on_initialize()
	{
		// Hook para lógica adicional después de la inicialización
		// Por ejemplo, registrar eventos, configurar servicios, etc.
	}
}
