<?php

/**
 * Model_Provider_Department
 * 
 * Relación N:N entre proveedores y departamentos
 * Indica qué departamentos surte cada proveedor
 */
class Model_Provider_Department extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'provider_id',
		'department_id',
		'is_primary',
		'notes',
		'deleted',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'provider_departments';

	protected static $_belongs_to = array(
		'provider' => array(
			'key_from' => 'provider_id',
			'model_to' => 'Model_Provider',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'department' => array(
			'key_from' => 'department_id',
			'model_to' => 'Model_Employees_Department',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	/**
	 * Obtiene el departamento principal de un proveedor
	 * 
	 * @param int $provider_id
	 * @return Model_Provider_Department|null
	 */
	public static function get_primary($provider_id)
	{
		$dept = static::query()
			->related('department')
			->where('provider_id', $provider_id)
			->where('is_primary', 1)
			->where('deleted', 0)
			->get_one();

		// Si no hay principal, devolver el primero
		if (!$dept) {
			$dept = static::query()
				->related('department')
				->where('provider_id', $provider_id)
				->where('deleted', 0)
				->order_by('id', 'asc')
				->get_one();
		}

		return $dept;
	}

	/**
	 * Obtiene todos los departamentos activos de un proveedor
	 * 
	 * @param int $provider_id
	 * @return array
	 */
	public static function get_active_departments($provider_id)
	{
		return static::query()
			->related('department')
			->where('provider_id', $provider_id)
			->where('deleted', 0)
			->order_by('is_primary', 'desc')
			->order_by('id', 'asc')
			->get();
	}

	/**
	 * Asigna un departamento a un proveedor
	 * 
	 * @param int $provider_id
	 * @param int $department_id
	 * @param bool $is_primary
	 * @param string $notes
	 * @return Model_Provider_Department
	 */
	public static function assign($provider_id, $department_id, $is_primary = false, $notes = null)
	{
		// Verificar si ya existe
		$existing = static::query()
			->where('provider_id', $provider_id)
			->where('department_id', $department_id)
			->get_one();

		if ($existing) {
			// Reactivar si estaba eliminado
			if ($existing->deleted == 1) {
				$existing->deleted = 0;
				$existing->is_primary = $is_primary ? 1 : 0;
				$existing->notes = $notes;
				$existing->save();
			}
			return $existing;
		}

		// Si es primary, quitar primary a los demás
		if ($is_primary) {
			\DB::update('provider_departments')
				->set(array('is_primary' => 0))
				->where('provider_id', $provider_id)
				->where('deleted', 0)
				->execute();
		}

		// Crear nueva asignación
		$assignment = static::forge(array(
			'provider_id' => $provider_id,
			'department_id' => $department_id,
			'is_primary' => $is_primary ? 1 : 0,
			'notes' => $notes,
			'deleted' => 0,
		));

		$assignment->save();

		return $assignment;
	}

	/**
	 * Elimina (soft delete) un departamento de un proveedor
	 * 
	 * @param int $provider_id
	 * @param int $department_id
	 * @return bool
	 */
	public static function unassign($provider_id, $department_id)
	{
		return \DB::update('provider_departments')
			->set(array('deleted' => 1))
			->where('provider_id', $provider_id)
			->where('department_id', $department_id)
			->execute();
	}
}
