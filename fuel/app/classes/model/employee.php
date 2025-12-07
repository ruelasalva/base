<?php

/**
 * Model_Employee
 * Sistema Multi-Tenant - Gestión de Empleados
 * 
 * @package    App
 * @category   Model
 * @author     Sistema Base
 */
class Model_Employee extends \Orm\Model
{
	protected static $_properties = [
		'id',
		'tenant_id' => [
			'data_type' => 'int',
			'default' => 1,
		],
		'user_id' => [
			'data_type' => 'int',
			'default' => null,
			'label' => 'Usuario del Sistema',
		],
		'code' => [
			'data_type' => 'varchar',
			'label' => 'Código',
			'validation' => ['max_length' => [50]],
		],
		'first_name' => [
			'data_type' => 'varchar',
			'label' => 'Nombre',
			'validation' => ['required', 'max_length' => [100]],
		],
		'last_name' => [
			'data_type' => 'varchar',
			'label' => 'Apellido Paterno',
			'validation' => ['required', 'max_length' => [100]],
		],
		'second_last_name' => [
			'data_type' => 'varchar',
			'label' => 'Apellido Materno',
			'validation' => ['max_length' => [100]],
		],
		'gender' => [
			'data_type' => 'enum',
			'label' => 'Género',
		],
		'birthdate' => [
			'data_type' => 'date',
			'label' => 'Fecha de Nacimiento',
		],
		'curp' => [
			'data_type' => 'varchar',
			'label' => 'CURP',
			'validation' => ['max_length' => [18]],
		],
		'rfc' => [
			'data_type' => 'varchar',
			'label' => 'RFC',
			'validation' => ['max_length' => [13]],
		],
		'nss' => [
			'data_type' => 'varchar',
			'label' => 'NSS',
			'validation' => ['max_length' => [11]],
		],
		'email' => [
			'data_type' => 'varchar',
			'label' => 'Email',
			'validation' => ['required', 'valid_email', 'max_length' => [255]],
		],
		'phone' => [
			'data_type' => 'varchar',
			'label' => 'Teléfono',
			'validation' => ['max_length' => [20]],
		],
		'phone_emergency' => [
			'data_type' => 'varchar',
			'label' => 'Teléfono de Emergencia',
			'validation' => ['max_length' => [20]],
		],
		'emergency_contact_name' => [
			'data_type' => 'varchar',
			'label' => 'Contacto de Emergencia',
			'validation' => ['max_length' => [200]],
		],
		'address' => [
			'data_type' => 'text',
			'label' => 'Dirección',
		],
		'city' => [
			'data_type' => 'varchar',
			'label' => 'Ciudad',
			'validation' => ['max_length' => [100]],
		],
		'state' => [
			'data_type' => 'varchar',
			'label' => 'Estado',
			'validation' => ['max_length' => [100]],
		],
		'postal_code' => [
			'data_type' => 'varchar',
			'label' => 'Código Postal',
			'validation' => ['max_length' => [10]],
		],
		'country' => [
			'data_type' => 'varchar',
			'label' => 'País',
			'default' => 'México',
			'validation' => ['max_length' => [100]],
		],
		'department_id' => [
			'data_type' => 'int',
			'label' => 'Departamento',
		],
		'position_id' => [
			'data_type' => 'int',
			'label' => 'Puesto',
		],
		'hire_date' => [
			'data_type' => 'date',
			'label' => 'Fecha de Contratación',
			'validation' => ['required'],
		],
		'termination_date' => [
			'data_type' => 'date',
			'label' => 'Fecha de Baja',
		],
		'employment_type' => [
			'data_type' => 'enum',
			'label' => 'Tipo de Empleo',
			'default' => 'full_time',
		],
		'employment_status' => [
			'data_type' => 'enum',
			'label' => 'Estatus',
			'default' => 'active',
		],
		'salary' => [
			'data_type' => 'decimal',
			'label' => 'Salario',
		],
		'salary_type' => [
			'data_type' => 'enum',
			'label' => 'Tipo de Salario',
			'default' => 'monthly',
		],
		'bank_name' => [
			'data_type' => 'varchar',
			'label' => 'Banco',
			'validation' => ['max_length' => [100]],
		],
		'bank_account' => [
			'data_type' => 'varchar',
			'label' => 'Cuenta Bancaria',
			'validation' => ['max_length' => [50]],
		],
		'clabe' => [
			'data_type' => 'varchar',
			'label' => 'CLABE',
			'validation' => ['max_length' => [18]],
		],
		'notes' => [
			'data_type' => 'text',
			'label' => 'Notas',
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
		'deleted_at' => [
			'data_type' => 'datetime',
		],
	];

	protected static $_table_name = 'employees';

	protected static $_belongs_to = [
		'tenant' => [
			'key_from' => 'tenant_id',
			'model_to' => 'Model_Tenant',
			'key_to' => 'id',
		],
		'user' => [
			'key_from' => 'user_id',
			'model_to' => 'Model_User',
			'key_to' => 'id',
		],
		'department' => [
			'key_from' => 'department_id',
			'model_to' => 'Model_Department',
			'key_to' => 'id',
		],
		'position' => [
			'key_from' => 'position_id',
			'model_to' => 'Model_Position',
			'key_to' => 'id',
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
	 * Obtener nombre completo del empleado
	 */
	public function get_full_name()
	{
		$name = $this->first_name . ' ' . $this->last_name;
		if (!empty($this->second_last_name)) {
			$name .= ' ' . $this->second_last_name;
		}
		return $name;
	}

	/**
	 * Obtener badge de estatus
	 */
	public function get_status_badge()
	{
		$statuses = [
			'active' => '<span class="badge bg-success"><i class="fa fa-check me-1"></i>Activo</span>',
			'inactive' => '<span class="badge bg-secondary"><i class="fa fa-pause me-1"></i>Inactivo</span>',
			'suspended' => '<span class="badge bg-warning"><i class="fa fa-ban me-1"></i>Suspendido</span>',
			'on_leave' => '<span class="badge bg-info"><i class="fa fa-plane me-1"></i>Permiso</span>',
			'terminated' => '<span class="badge bg-danger"><i class="fa fa-times me-1"></i>Terminado</span>',
		];

		return isset($statuses[$this->employment_status]) ? $statuses[$this->employment_status] : 
			'<span class="badge bg-secondary">Desconocido</span>';
	}

	/**
	 * Obtener badge de tipo de empleo
	 */
	public function get_employment_type_badge()
	{
		$types = [
			'full_time' => '<span class="badge bg-primary">Tiempo Completo</span>',
			'part_time' => '<span class="badge bg-info">Medio Tiempo</span>',
			'contract' => '<span class="badge bg-warning text-dark">Contrato</span>',
			'intern' => '<span class="badge bg-secondary">Practicante</span>',
			'temporary' => '<span class="badge bg-dark">Temporal</span>',
		];

		return isset($types[$this->employment_type]) ? $types[$this->employment_type] : 
			'<span class="badge bg-secondary">-</span>';
	}

	/**
	 * Verificar si el empleado tiene usuario del sistema
	 */
	public function has_system_user()
	{
		return !empty($this->user_id);
	}

	/**
	 * Calcular edad
	 */
	public function get_age()
	{
		if (empty($this->birthdate)) {
			return null;
		}
		
		$birthDate = new DateTime($this->birthdate);
		$today = new DateTime();
		$age = $today->diff($birthDate);
		
		return $age->y;
	}

	/**
	 * Calcular antigüedad en años
	 */
	public function get_seniority_years()
	{
		if (empty($this->hire_date)) {
			return 0;
		}
		
		$hireDate = new DateTime($this->hire_date);
		$today = new DateTime();
		$diff = $today->diff($hireDate);
		
		return $diff->y;
	}

	/**
	 * Formatear salario
	 */
	public function get_formatted_salary()
	{
		if (empty($this->salary)) {
			return 'No especificado';
		}
		
		return '$' . number_format($this->salary, 2) . ' ' . $this->get_salary_type_label();
	}

	/**
	 * Obtener etiqueta de tipo de salario
	 */
	public function get_salary_type_label()
	{
		$types = [
			'monthly' => 'mensual',
			'biweekly' => 'quincenal',
			'weekly' => 'semanal',
			'hourly' => 'por hora',
			'daily' => 'diario',
		];

		return isset($types[$this->salary_type]) ? $types[$this->salary_type] : '';
	}

	/**
	 * Soft delete
	 */
	public function delete($cascade = null, $use_transaction = false)
	{
		$this->deleted_at = date('Y-m-d H:i:s');
		$this->is_active = 0;
		return $this->save();
	}
}
