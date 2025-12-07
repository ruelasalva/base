<?php

/**
 * Model_Department
 * Sistema Multi-Tenant - Gestión de Departamentos
 * 
 * @package    App
 * @category   Model
 * @author     Sistema Base
 */
class Model_Department extends \Orm\Model
{
	protected static $_properties = [
		'id',
		'tenant_id' => [
			'data_type' => 'int',
			'default' => 1,
		],
		'parent_id' => [
			'data_type' => 'int',
			'default' => null,
			'label' => 'Departamento Padre',
		],
		'name' => [
			'data_type' => 'varchar',
			'label' => 'Nombre',
			'validation' => ['required', 'max_length' => [100]],
		],
		'code' => [
			'data_type' => 'varchar',
			'label' => 'Código',
			'validation' => ['max_length' => [50]],
		],
		'description' => [
			'data_type' => 'text',
			'label' => 'Descripción',
		],
		'manager_id' => [
			'data_type' => 'int',
			'label' => 'Responsable',
		],
		'is_active' => [
			'data_type' => 'int',
			'default' => 1,
		],
		'created_at' => [
			'data_type' => 'datetime',
		],
		'updated_at' => [
			'data_type' => 'datetime',
		],
	];

	protected static $_table_name = 'departments';

	protected static $_belongs_to = [
		'tenant' => [
			'key_from' => 'tenant_id',
			'model_to' => 'Model_Tenant',
			'key_to' => 'id',
		],
		'parent' => [
			'key_from' => 'parent_id',
			'model_to' => 'Model_Department',
			'key_to' => 'id',
		],
		'manager' => [
			'key_from' => 'manager_id',
			'model_to' => 'Model_Employee',
			'key_to' => 'id',
		],
	];

	protected static $_has_many = [
		'employees' => [
			'key_from' => 'id',
			'model_to' => 'Model_Employee',
			'key_to' => 'department_id',
		],
		'children' => [
			'key_from' => 'id',
			'model_to' => 'Model_Department',
			'key_to' => 'parent_id',
		],
	];

	protected static $_observers = [
		'Orm\\Observer_CreatedAt' => [
			'events' => ['before_insert'],
			'property' => 'created_at',
			'mysql_timestamp' => false,
		],
		'Orm\\Observer_UpdatedAt' => [
			'events' => ['before_update'],
			'property' => 'updated_at',
			'mysql_timestamp' => false,
		],
	];

	/**
	 * Obtener badge de estatus
	 */
	public function get_status_badge()
	{
		if ($this->is_active) {
			return '<span class="badge bg-success"><i class="fa fa-check me-1"></i>Activo</span>';
		}
		return '<span class="badge bg-secondary"><i class="fa fa-times me-1"></i>Inactivo</span>';
	}

	/**
	 * Obtener jerarquía del departamento
	 */
	public function get_hierarchy()
	{
		$hierarchy = [$this->name];
		$parent = $this->parent;
		
		while ($parent) {
			array_unshift($hierarchy, $parent->name);
			$parent = $parent->parent;
		}
		
		return implode(' > ', $hierarchy);
	}

	/**
	 * Contar empleados activos
	 */
	public function count_active_employees()
	{
		return Model_Employee::query()
			->where('department_id', $this->id)
			->where('employment_status', 'active')
			->where('deleted_at', null)
			->count();
	}
}
