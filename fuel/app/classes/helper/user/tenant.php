<?php

/**
 * Helper_User_Tenant
 * 
 * Gestión de acceso multi-tenant para usuarios
 */
class Helper_User_Tenant
{
	/**
	 * Asigna un usuario a un tenant (backend)
	 * 
	 * @param int $user_id
	 * @param int $tenant_id
	 * @param bool $is_default
	 * @return bool
	 */
	public static function assign($user_id, $tenant_id, $is_default = false)
	{
		// Verificar si ya existe
		$existing = DB::select('id')
			->from('user_tenants')
			->where('user_id', $user_id)
			->where('tenant_id', $tenant_id)
			->execute()
			->current();

		if ($existing) {
			// Actualizar si ya existe
			DB::update('user_tenants')
				->set(array(
					'is_active' => 1,
					'is_default' => $is_default ? 1 : 0,
					'updated_at' => time()
				))
				->where('id', $existing['id'])
				->execute();

			return true;
		}

		// Si es default, quitar default a los demás
		if ($is_default) {
			DB::update('user_tenants')
				->set(array('is_default' => 0))
				->where('user_id', $user_id)
				->execute();
		}

		// Crear nuevo
		DB::insert('user_tenants')
			->set(array(
				'user_id' => $user_id,
				'tenant_id' => $tenant_id,
				'is_default' => $is_default ? 1 : 0,
				'is_active' => 1,
				'created_at' => time(),
				'updated_at' => time()
			))
			->execute();

		return true;
	}

	/**
	 * Remueve acceso de un usuario a un tenant
	 * 
	 * @param int $user_id
	 * @param int $tenant_id
	 * @return bool
	 */
	public static function unassign($user_id, $tenant_id)
	{
		DB::update('user_tenants')
			->set(array('is_active' => 0, 'updated_at' => time()))
			->where('user_id', $user_id)
			->where('tenant_id', $tenant_id)
			->execute();

		return true;
	}

	/**
	 * Obtiene todos los tenants de un usuario
	 * 
	 * @param int $user_id
	 * @return array
	 */
	public static function get_user_tenants($user_id)
	{
		return DB::select('ut.*', array('t.company_name', 'tenant_name'), array('t.domain', 'tenant_domain'))
			->from(array('user_tenants', 'ut'))
			->join(array('tenants', 't'), 'LEFT')
			->on('ut.tenant_id', '=', 't.id')
			->where('ut.user_id', $user_id)
			->where('ut.is_active', 1)
			->where('t.is_active', 1)
			->order_by('ut.is_default', 'DESC')
			->execute()
			->as_array();
	}

	/**
	 * Obtiene el tenant por defecto de un usuario
	 * 
	 * @param int $user_id
	 * @return array|null
	 */
	public static function get_default_tenant($user_id)
	{
		$result = DB::select('ut.*', array('t.company_name', 'tenant_name'), array('t.domain', 'tenant_domain'))
			->from(array('user_tenants', 'ut'))
			->join(array('tenants', 't'), 'LEFT')
			->on('ut.tenant_id', '=', 't.id')
			->where('ut.user_id', $user_id)
			->where('ut.is_default', 1)
			->where('ut.is_active', 1)
			->where('t.is_active', 1)
			->execute()
			->current();

		return $result ?: null;
	}

	/**
	 * Establece un tenant como default para un usuario
	 * 
	 * @param int $user_id
	 * @param int $tenant_id
	 * @return bool
	 */
	public static function set_default($user_id, $tenant_id)
	{
		// Quitar default a todos
		DB::update('user_tenants')
			->set(array('is_default' => 0))
			->where('user_id', $user_id)
			->execute();

		// Establecer nuevo default
		DB::update('user_tenants')
			->set(array('is_default' => 1, 'updated_at' => time()))
			->where('user_id', $user_id)
			->where('tenant_id', $tenant_id)
			->execute();

		return true;
	}

	/**
	 * Asigna un usuario a TODOS los tenants activos (Super Admin)
	 * 
	 * @param int $user_id
	 * @param int $default_tenant_id Tenant por defecto
	 * @return int Cantidad de tenants asignados
	 */
	public static function assign_all_tenants($user_id, $default_tenant_id = 1)
	{
		$tenants = DB::select('id')
			->from('tenants')
			->where('is_active', 1)
			->execute()
			->as_array();

		$count = 0;
		foreach ($tenants as $tenant) {
			$is_default = ($tenant['id'] == $default_tenant_id);
			static::assign($user_id, $tenant['id'], $is_default);
			$count++;
		}

		return $count;
	}

	/**
	 * Verifica si un usuario tiene acceso a un tenant específico
	 * 
	 * @param int $user_id
	 * @param int $tenant_id
	 * @return bool
	 */
	public static function has_access($user_id, $tenant_id)
	{
		$count = DB::select(DB::expr('COUNT(*) as total'))
			->from('user_tenants')
			->where('user_id', $user_id)
			->where('tenant_id', $tenant_id)
			->where('is_active', 1)
			->execute()
			->get('total');

		return $count > 0;
	}

	/**
	 * Obtiene todos los tenants disponibles
	 * 
	 * @return array
	 */
	public static function get_all_tenants()
	{
		return DB::select('id', 'domain', 'company_name', 'is_active')
			->from('tenants')
			->where('is_active', 1)
			->order_by('company_name', 'ASC')
			->execute()
			->as_array();
	}

	/**
	 * Sincroniza tenants de un usuario (útil para super admins)
	 * Agrega nuevos tenants que se hayan creado después
	 * 
	 * @param int $user_id
	 * @return int Cantidad de tenants agregados
	 */
	public static function sync_super_admin($user_id)
	{
		// Obtener tenants que NO tiene asignados
		$missing_tenants = DB::select('t.id')
			->from(array('tenants', 't'))
			->join(array('user_tenants', 'ut'), 'LEFT')
			->on('t.id', '=', 'ut.tenant_id')
			->on('ut.user_id', '=', DB::expr($user_id))
			->where('t.is_active', 1)
			->where('ut.id', 'IS', null)
			->execute()
			->as_array();

		$count = 0;
		foreach ($missing_tenants as $tenant) {
			static::assign($user_id, $tenant['id'], false);
			$count++;
		}

		return $count;
	}
}
