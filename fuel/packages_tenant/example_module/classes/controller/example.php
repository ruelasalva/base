<?php
/**
 * Example Module - Example Controller
 *
 * Este controlador demuestra cómo usar Controller_Module_Base
 * para obtener verificación automática de módulo activo y permisos.
 *
 * @package    Example_Module
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace Example_Module;

/**
 * Controller_Example
 *
 * Controlador de ejemplo que extiende Controller_Module_Base
 * para demostrar la arquitectura modular unificada.
 */
class Controller_Example extends \Controller_Module_Base
{
	/**
	 * @var string Clave del módulo
	 */
	protected $module_key = 'example_module';

	/**
	 * @var string Permiso requerido para acceder al controlador
	 */
	protected $required_permission = 'example_module.access';

	/**
	 * @var array Permisos específicos por acción
	 */
	protected $action_permissions = array(
		'agregar'  => 'example_module.create',
		'editar'   => 'example_module.edit',
		'eliminar' => 'example_module.delete',
	);

	/**
	 * @var bool Requerir autenticación (por defecto es true)
	 *
	 * NOTA: En producción, siempre debe ser true.
	 * Se establece en false solo para demostración sin sistema de auth configurado.
	 * En un entorno real, configurar FuelPHP Auth y mantener este valor en true.
	 */
	protected $require_auth = true;

	/**
	 * Index action - displays module status
	 *
	 * @return void
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Example Module',
			'status' => 'active',
			'message' => 'This module is active for the current tenant.',
		);

		$this->template->title = 'Example Module';
		$this->template->content = \View::forge('example_module/index', $data, false);
	}

	/**
	 * Info action - displays module information
	 *
	 * @return \Response
	 */
	public function action_info()
	{
		// Desactivar auto-render para respuesta JSON
		$this->auto_render = false;

		$tenant_modules = \Model_Tenant::get_current_active_modules();

		$data = array(
			'module_key' => EXAMPLE_MODULE_KEY,
			'active_modules' => $tenant_modules,
			'module_info' => array(
				'name' => 'Example Module',
				'version' => '1.0.0',
				'is_active' => $this->is_module_active($this->module_key),
			),
		);

		return \Response::forge(json_encode($data), 200, array(
			'Content-Type' => 'application/json',
		));
	}

	/**
	 * Agregar action - ejemplo de acción protegida
	 *
	 * @return void
	 */
	public function action_agregar()
	{
		$data = array(
			'title' => 'Agregar nuevo ejemplo',
		);

		$this->template->title = $data['title'];
		$this->template->content = \View::forge('example_module/agregar', $data, false);
	}
}
