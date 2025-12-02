<?php
/**
 * Helper de Permisos RBAC
 * Sistema de permisos basado en roles dinámico
 */
class Helper_Permission
{
	/**
	 * Cache de permisos del usuario actual
	 */
	protected static $user_permissions = null;
	
	/**
	 * Cache de roles del usuario actual
	 */
	protected static $user_roles = null;

	/**
	 * Verificar si el usuario actual tiene un permiso específico
	 * 
	 * @param   string  $module  Módulo del sistema
	 * @param   string  $action  Acción a verificar
	 * @param   int     $tenant_id  ID del tenant (null = actual)
	 * @return  bool
	 */
	public static function can($module, $action, $tenant_id = null)
	{
		// Si no hay sesión, no tiene permisos
		if (!Auth::check())
		{
			return false;
		}

		$user_id = Auth::get('id');
		$tenant_id = $tenant_id ?: Session::get('tenant_id', 1);

		// Cargar permisos si no están en cache
		if (static::$user_permissions === null)
		{
			static::load_permissions($user_id, $tenant_id);
		}

		// Super admin tiene todos los permisos
		if (static::is_super_admin())
		{
			return true;
		}

		// Verificar si tiene el permiso específico
		$permission_key = "{$module}.{$action}";
		return in_array($permission_key, static::$user_permissions);
	}

	/**
	 * Verificar si el usuario tiene un rol específico
	 * 
	 * @param   string  $role_name  Nombre del rol
	 * @param   int     $tenant_id  ID del tenant (null = actual)
	 * @return  bool
	 */
	public static function has_role($role_name, $tenant_id = null)
	{
		if (!Auth::check())
		{
			return false;
		}

		$user_id = Auth::get('id');
		$tenant_id = $tenant_id ?: Session::get('tenant_id', 1);

		// Cargar roles si no están en cache
		if (static::$user_roles === null)
		{
			static::load_permissions($user_id, $tenant_id);
		}

		return in_array($role_name, static::$user_roles);
	}

	/**
	 * Verificar si el usuario es super administrador
	 * 
	 * @return  bool
	 */
	public static function is_super_admin()
	{
		return static::has_role('super_admin');
	}

	/**
	 * Verificar si el usuario es administrador (super_admin o admin)
	 * 
	 * @return  bool
	 */
	public static function is_admin()
	{
		return static::has_role('super_admin') || static::has_role('admin');
	}

	/**
	 * Cargar permisos y roles del usuario desde la BD
	 * 
	 * @param   int  $user_id
	 * @param   int  $tenant_id
	 */
	protected static function load_permissions($user_id, $tenant_id)
	{
		static::$user_permissions = [];
		static::$user_roles = [];

		// Obtener roles del usuario para este tenant
		$roles = DB::select('r.name', 'r.level')
			->from(array('user_roles', 'ur'))
			->join(array('roles', 'r'), 'INNER')
			->on('ur.role_id', '=', 'r.id')
			->where('ur.user_id', '=', $user_id)
			->and_where_open()
				->where('ur.tenant_id', '=', $tenant_id)
				->or_where('ur.tenant_id', 'IS', null) // Roles globales
			->and_where_close()
			->where('r.is_active', '=', 1)
			->execute()
			->as_array();

		foreach ($roles as $role)
		{
			static::$user_roles[] = $role['name'];
		}

		// Si no tiene roles, no tiene permisos
		if (empty(static::$user_roles))
		{
			return;
		}

		// Obtener IDs de roles
		$role_ids = DB::select('ur.role_id')
			->from(array('user_roles', 'ur'))
			->where('ur.user_id', '=', $user_id)
			->and_where_open()
				->where('ur.tenant_id', '=', $tenant_id)
				->or_where('ur.tenant_id', 'IS', null)
			->and_where_close()
			->execute()
			->as_array();

		$role_ids = array_column($role_ids, 'role_id');

		if (empty($role_ids))
		{
			return;
		}

		// Obtener permisos de esos roles
		$permissions = DB::select('p.module', 'p.action')
			->from(array('role_permissions', 'rp'))
			->join(array('permissions', 'p'), 'INNER')
			->on('rp.permission_id', '=', 'p.id')
			->where('rp.role_id', 'IN', $role_ids)
			->where('p.is_active', '=', 1)
			->execute()
			->as_array();

		foreach ($permissions as $perm)
		{
			static::$user_permissions[] = "{$perm['module']}.{$perm['action']}";
		}

		// Eliminar duplicados
		static::$user_permissions = array_unique(static::$user_permissions);
	}

	/**
	 * Limpiar cache de permisos
	 */
	public static function clear_cache()
	{
		static::$user_permissions = null;
		static::$user_roles = null;
	}
}
