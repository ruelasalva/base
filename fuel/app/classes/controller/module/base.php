<?php
/**
 * Controlador Base de Módulo
 *
 * Controlador base genérico para todos los módulos del sistema multi-tenant.
 * Implementa verificación de módulo activo y control de acceso basado en roles
 * en el método before().
 *
 * Extiende de Controller_Base para heredar funcionalidad de templating y CRUD.
 *
 * @package    App
 * @subpackage Controller
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

/**
 * Controller_Module_Base
 *
 * Controlador base que todo controlador de módulo debe extender.
 * Proporciona:
 * - Verificación automática de módulo activo
 * - Integración con sistema de permisos/roles de FuelPHP
 * - Redirección a página de acceso denegado si no cumple requisitos
 *
 * Ejemplo de uso:
 * <code>
 * class Controller_Partners_Dashboard extends Controller_Module_Base
 * {
 *     protected $module_key = 'partners';
 *     protected $required_permission = 'partners.access';
 *
 *     public function action_index()
 *     {
 *         // Código del controlador
 *     }
 * }
 * </code>
 */
class Controller_Module_Base extends Controller_Base
{
	/**
	 * @var string Clave del módulo que este controlador pertenece
	 *
	 * Debe ser sobrescrita en cada controlador hijo.
	 * Ejemplo: 'partners', 'admin', 'providers'
	 */
	protected $module_key = '';

	/**
	 * @var string Permiso requerido para acceder al controlador
	 *
	 * Si está vacío, solo se verifica que el módulo esté activo.
	 * Formato: 'modulo.permiso' o 'modulo.recurso.accion'
	 */
	protected $required_permission = '';

	/**
	 * @var array Permisos específicos por acción
	 *
	 * Permite definir permisos específicos para cada action_*.
	 * Ejemplo: array('editar' => 'partners.edit', 'eliminar' => 'partners.delete')
	 */
	protected $action_permissions = array();

	/**
	 * @var array Roles que tienen acceso al controlador
	 *
	 * Si está vacío, no se verifica rol específico.
	 */
	protected $allowed_roles = array();

	/**
	 * @var bool Si se requiere autenticación
	 */
	protected $require_auth = true;

	/**
	 * @var string URL de redirección cuando falla la verificación de módulo
	 */
	protected $module_denied_url = 'error/403';

	/**
	 * @var string URL de redirección cuando falla la autenticación
	 */
	protected $auth_denied_url = 'auth/login';

	/**
	 * @var string URL de redirección cuando falla la verificación de permisos
	 */
	protected $permission_denied_url = 'error/403';

	/**
	 * BEFORE
	 *
	 * Método ejecutado antes de cada acción.
	 * Implementa las verificaciones de seguridad en orden:
	 * 1. Verificación de módulo activo
	 * 2. Verificación de autenticación (si requerida)
	 * 3. Verificación de permisos/roles
	 *
	 * @return void
	 * @throws HttpNotFoundException Si el módulo no está activo
	 */
	public function before()
	{
		// Llamar al before() del padre primero para inicializar template
		parent::before();

		// 1. Verificar que el módulo está activo para el tenant
		if ( ! $this->verify_module_active())
		{
			$this->on_module_denied();
			return;
		}

		// 2. Verificar autenticación si es requerida
		if ($this->require_auth && ! $this->verify_authentication())
		{
			$this->on_auth_denied();
			return;
		}

		// 3. Verificar permisos de acceso
		if ( ! $this->verify_access_permissions())
		{
			$this->on_permission_denied();
			return;
		}
	}

	/**
	 * Verificar si el módulo está activo para el tenant actual
	 *
	 * @return bool True si el módulo está activo
	 */
	protected function verify_module_active()
	{
		// Si no se especifica module_key, asumir que está activo
		if (empty($this->module_key))
		{
			\Log::warning('Controller_Module_Base: No module_key defined in ' . get_class($this));
			return true;
		}

		// Verificar contra TENANT_ACTIVE_MODULES
		if ( ! defined('TENANT_ACTIVE_MODULES'))
		{
			\Log::error('Controller_Module_Base: TENANT_ACTIVE_MODULES not defined');
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

		$is_active = in_array($this->module_key, $active_modules, true);

		if ( ! $is_active)
		{
			\Log::warning(sprintf(
				'Controller_Module_Base: Module "%s" is not active for current tenant',
				$this->module_key
			));
		}

		return $is_active;
	}

	/**
	 * Verificar si el usuario está autenticado
	 *
	 * Integra con el paquete Auth de FuelPHP.
	 *
	 * @return bool True si el usuario está autenticado
	 */
	protected function verify_authentication()
	{
		// Verificar si el paquete Auth está cargado
		try
		{
			if ( ! class_exists('Auth'))
			{
				// Si Auth no existe, intentar cargar el package
				\Package::load('auth');
			}

			// Verificar autenticación usando FuelPHP Auth
			return \Auth::check();
		}
		catch (\Exception $e)
		{
			// Si hay error cargando Auth, asumir no autenticado
			\Log::error('Controller_Module_Base: Error checking auth - ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Verificar permisos de acceso
	 *
	 * Verifica permisos en el siguiente orden:
	 * 1. Permiso específico de la acción actual (si definido)
	 * 2. Permiso requerido del controlador (si definido)
	 * 3. Verificación de roles (si definidos)
	 *
	 * @return bool True si el usuario tiene los permisos necesarios
	 */
	protected function verify_access_permissions()
	{
		// Si no se requiere autenticación, no verificar permisos
		if ( ! $this->require_auth)
		{
			return true;
		}

		// Obtener la acción actual
		$action = $this->get_current_action();

		// 1. Verificar permiso específico de acción
		if ( ! empty($this->action_permissions) && isset($this->action_permissions[$action]))
		{
			$permission = $this->action_permissions[$action];

			if ( ! $this->has_permission($permission))
			{
				\Log::warning(sprintf(
					'Controller_Module_Base: User lacks action permission "%s" for action "%s"',
					$permission,
					$action
				));
				return false;
			}
		}
		// 2. Verificar permiso general del controlador
		elseif ( ! empty($this->required_permission))
		{
			if ( ! $this->has_permission($this->required_permission))
			{
				\Log::warning(sprintf(
					'Controller_Module_Base: User lacks required permission "%s"',
					$this->required_permission
				));
				return false;
			}
		}

		// 3. Verificar roles si están definidos
		if ( ! empty($this->allowed_roles))
		{
			if ( ! $this->has_any_role($this->allowed_roles))
			{
				\Log::warning('Controller_Module_Base: User does not have required role');
				return false;
			}
		}

		return true;
	}

	/**
	 * Verificar si el usuario tiene un permiso específico
	 *
	 * Integra con el sistema de permisos de FuelPHP Auth.
	 *
	 * @param string $permission El permiso a verificar
	 * @return bool True si el usuario tiene el permiso
	 */
	protected function has_permission($permission)
	{
		try
		{
			if ( ! class_exists('Auth'))
			{
				\Package::load('auth');
			}

			// Usar Auth::has_access() de FuelPHP
			// El formato es 'area.permission' o 'area.permission[action]'
			return \Auth::has_access($permission);
		}
		catch (\Exception $e)
		{
			\Log::error('Controller_Module_Base: Error checking permission - ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Verificar si el usuario tiene alguno de los roles especificados
	 *
	 * @param array $roles Lista de roles permitidos
	 * @return bool True si el usuario tiene al menos uno de los roles
	 */
	protected function has_any_role(array $roles)
	{
		try
		{
			if ( ! class_exists('Auth'))
			{
				\Package::load('auth');
			}

			// Obtener el rol/grupo del usuario actual
			$user_groups = \Auth::get_groups();

			if (empty($user_groups))
			{
				return false;
			}

			foreach ($user_groups as $group)
			{
				// $group es un array [driver, group_id]
				if (is_array($group) && isset($group[1]))
				{
					if (in_array($group[1], $roles, true))
					{
						return true;
					}
				}
			}

			return false;
		}
		catch (\Exception $e)
		{
			\Log::error('Controller_Module_Base: Error checking roles - ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Obtener el nombre de la acción actual
	 *
	 * @return string Nombre de la acción sin el prefijo 'action_'
	 */
	protected function get_current_action()
	{
		$request = \Request::active();

		if ($request)
		{
			return $request->action;
		}

		return 'index';
	}

	/**
	 * Handler cuando el módulo no está activo
	 *
	 * Sobrescribir para comportamiento personalizado.
	 *
	 * @return void
	 */
	protected function on_module_denied()
	{
		\Session::set_flash('error', 'El módulo solicitado no está disponible para su organización.');
		\Response::redirect($this->module_denied_url);
	}

	/**
	 * Handler cuando falla la autenticación
	 *
	 * Sobrescribir para comportamiento personalizado.
	 *
	 * @return void
	 */
	protected function on_auth_denied()
	{
		\Session::set_flash('warning', 'Por favor inicie sesión para continuar.');

		// Guardar URL actual para redirección después del login
		\Session::set('redirect_after_login', \Uri::current());

		\Response::redirect($this->auth_denied_url);
	}

	/**
	 * Handler cuando fallan los permisos
	 *
	 * Sobrescribir para comportamiento personalizado.
	 *
	 * @return void
	 */
	protected function on_permission_denied()
	{
		\Session::set_flash('error', 'No tiene permisos para acceder a este recurso.');
		\Response::redirect($this->permission_denied_url);
	}

	/**
	 * Obtener la clave del módulo
	 *
	 * @return string
	 */
	public function get_module_key()
	{
		return $this->module_key;
	}

	/**
	 * Verificar si un módulo específico está activo
	 *
	 * Útil para verificar dependencias de otros módulos.
	 *
	 * @param string $module_key Clave del módulo a verificar
	 * @return bool
	 */
	protected function is_module_active($module_key)
	{
		return Model_Tenant::is_current_module_active($module_key);
	}

	/**
	 * Obtener información del usuario actual
	 *
	 * @return array|null Datos del usuario o null si no autenticado
	 */
	protected function get_current_user()
	{
		try
		{
			if ( ! class_exists('Auth'))
			{
				\Package::load('auth');
			}

			if ( ! \Auth::check())
			{
				return null;
			}

			return \Auth::get_user();
		}
		catch (\Exception $e)
		{
			return null;
		}
	}

	/**
	 * Obtener el ID del usuario actual
	 *
	 * @return int|null
	 */
	protected function get_current_user_id()
	{
		try
		{
			if ( ! class_exists('Auth'))
			{
				\Package::load('auth');
			}

			$user = \Auth::get_user_id();

			if (is_array($user) && isset($user[1]))
			{
				return $user[1];
			}

			return null;
		}
		catch (\Exception $e)
		{
			return null;
		}
	}
}
