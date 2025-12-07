<?php

/**
 * Model_Position
 * Sistema Multi-Tenant - Gestión de Puestos
 * 
 * @package    App
 * @category   Model
 * @author     Sistema Base
 */
class Model_Position extends \Orm\Model
{
	protected static $_properties = [
		'id',
		'tenant_id' => [
			'data_type' => 'int',
			'default' => 1,
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
		'salary_min' => [
			'data_type' => 'decimal',
			'label' => 'Salario Mínimo',
		],
		'salary_max' => [
			'data_type' => 'decimal',
			'label' => 'Salario Máximo',
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

	protected static $_table_name = 'positions';

	protected static $_belongs_to = [
		'tenant' => [
			'key_from' => 'tenant_id',
			'model_to' => 'Model_Tenant',
			'key_to' => 'id',
		],
	];

	protected static $_has_many = [
		'employees' => [
			'key_from' => 'id',
			'model_to' => 'Model_Employee',
			'key_to' => 'position_id',
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
	 * Obtener rango salarial formateado
	 */
	public function get_salary_range()
	{
		if (empty($this->salary_min) && empty($this->salary_max)) {
			return 'No especificado';
		}
		
		if (empty($this->salary_min)) {
			return 'Hasta $' . number_format($this->salary_max, 2);
		}
		
		if (empty($this->salary_max)) {
			return 'Desde $' . number_format($this->salary_min, 2);
		}
		
		return '$' . number_format($this->salary_min, 2) . ' - $' . number_format($this->salary_max, 2);
	}

	/**
	 * Contar empleados activos en este puesto
	 */
	public function count_active_employees()
	{
		return Model_Employee::query()
			->where('position_id', $this->id)
			->where('employment_status', 'active')
			->where('deleted_at', null)
			->count();
	}
}
