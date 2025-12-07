<?php

/**
 * Model_Payroll_Period
 * Sistema de Nómina - Períodos de Nómina
 * 
 * @package    App
 * @subpackage Model
 * @category   Payroll
 * @author     Sistema Base
 */
class Model_Payroll_Period extends \Orm\Model
{
	protected static $_properties = [
		'id',
		'tenant_id' => [
			'data_type' => 'int',
			'default' => 1,
		],
		'code' => [
			'data_type' => 'varchar',
			'label' => 'Código',
			'validation' => ['required', 'max_length' => [50]],
		],
		'name' => [
			'data_type' => 'varchar',
			'label' => 'Nombre',
			'validation' => ['required', 'max_length' => [100]],
		],
		'period_type' => [
			'data_type' => 'enum',
			'label' => 'Tipo de Período',
			'default' => 'monthly',
			'validation' => ['required'],
		],
		'year' => [
			'data_type' => 'int',
			'label' => 'Año',
			'validation' => ['required', 'numeric_min' => [2020]],
		],
		'period_number' => [
			'data_type' => 'int',
			'label' => 'Número de Período',
			'validation' => ['required', 'numeric_min' => [1]],
		],
		'start_date' => [
			'data_type' => 'date',
			'label' => 'Fecha de Inicio',
			'validation' => ['required'],
		],
		'end_date' => [
			'data_type' => 'date',
			'label' => 'Fecha de Fin',
			'validation' => ['required'],
		],
		'payment_date' => [
			'data_type' => 'date',
			'label' => 'Fecha de Pago',
			'validation' => ['required'],
		],
		'status' => [
			'data_type' => 'enum',
			'label' => 'Estado',
			'default' => 'draft',
		],
		'total_employees' => [
			'data_type' => 'int',
			'default' => 0,
		],
		'total_gross' => [
			'data_type' => 'decimal',
			'default' => 0.00,
		],
		'total_deductions' => [
			'data_type' => 'decimal',
			'default' => 0.00,
		],
		'total_net' => [
			'data_type' => 'decimal',
			'default' => 0.00,
		],
		'notes' => [
			'data_type' => 'text',
			'label' => 'Notas',
		],
		'calculated_by',
		'calculated_at',
		'approved_by',
		'approved_at',
		'paid_by',
		'paid_at',
		'closed_by',
		'closed_at',
		'is_active' => [
			'data_type' => 'int',
			'default' => 1,
		],
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected static $_observers = [
		'Orm\Observer_CreatedAt' => [
			'events' => ['before_insert'],
			'mysql_timestamp' => true,
		],
		'Orm\Observer_UpdatedAt' => [
			'events' => ['before_update'],
			'mysql_timestamp' => true,
		],
	];

	protected static $_table_name = 'payroll_periods';

	protected static $_has_many = [
		'receipts' => [
			'key_from' => 'id',
			'model_to' => 'Model_Payroll_Receipt',
			'key_to' => 'period_id',
			'cascade_save' => true,
			'cascade_delete' => false,
		],
	];

	protected static $_soft_delete = [
		'deleted_field' => 'deleted_at',
		'mysql_timestamp' => true,
	];

	/**
	 * Obtener el label del tipo de período
	 */
	public function get_period_type_label()
	{
		$types = [
			'monthly' => 'Mensual',
			'biweekly' => 'Quincenal',
			'weekly' => 'Semanal',
		];
		return isset($types[$this->period_type]) ? $types[$this->period_type] : $this->period_type;
	}

	/**
	 * Obtener el label del estado
	 */
	public function get_status_label()
	{
		$statuses = [
			'draft' => 'Borrador',
			'in_progress' => 'En Proceso',
			'calculated' => 'Calculada',
			'approved' => 'Aprobada',
			'paid' => 'Pagada',
			'closed' => 'Cerrada',
		];
		return isset($statuses[$this->status]) ? $statuses[$this->status] : $this->status;
	}

	/**
	 * Obtener el badge HTML del estado
	 */
	public function get_status_badge()
	{
		$badges = [
			'draft' => '<span class="badge bg-secondary">Borrador</span>',
			'in_progress' => '<span class="badge bg-info">En Proceso</span>',
			'calculated' => '<span class="badge bg-primary">Calculada</span>',
			'approved' => '<span class="badge bg-success">Aprobada</span>',
			'paid' => '<span class="badge bg-warning">Pagada</span>',
			'closed' => '<span class="badge bg-dark">Cerrada</span>',
		];
		return isset($badges[$this->status]) ? $badges[$this->status] : '<span class="badge bg-light">' . $this->status . '</span>';
	}

	/**
	 * Verificar si el período está editable
	 */
	public function is_editable()
	{
		return in_array($this->status, ['draft', 'in_progress']);
	}

	/**
	 * Verificar si el período puede ser calculado
	 */
	public function can_calculate()
	{
		return in_array($this->status, ['draft', 'in_progress']);
	}

	/**
	 * Verificar si el período puede ser aprobado
	 */
	public function can_approve()
	{
		return $this->status === 'calculated';
	}

	/**
	 * Verificar si el período puede ser pagado
	 */
	public function can_pay()
	{
		return $this->status === 'approved';
	}

	/**
	 * Contar recibos generados
	 */
	public function count_receipts()
	{
		return Model_Payroll_Receipt::count([
			'where' => [
				['period_id', '=', $this->id],
				['deleted_at', 'IS', null],
			],
		]);
	}

	/**
	 * Obtener empleados activos para el período
	 */
	public function get_active_employees()
	{
		return Model_Employee::query()
			->where('tenant_id', '=', $this->tenant_id)
			->where('employment_status', '=', 'active')
			->where('hire_date', '<=', $this->end_date)
			->where(function($query) {
				$query->where('termination_date', 'IS', null);
				$query->or_where('termination_date', '>=', $this->start_date);
			})
			->where('deleted_at', 'IS', null)
			->get();
	}

	/**
	 * Calcular nómina del período
	 */
	public function calculate_payroll($user_id = null)
	{
		try {
			\DB::start_transaction();

			// Obtener empleados activos
			$employees = $this->get_active_employees();
			
			if (empty($employees)) {
				throw new \Exception('No hay empleados activos para procesar');
			}

			$total_employees = 0;
			$total_gross = 0;
			$total_deductions = 0;
			$total_net = 0;

			foreach ($employees as $employee) {
				// Verificar si ya existe un recibo para este empleado
				$existing_receipt = Model_Payroll_Receipt::query()
					->where('period_id', '=', $this->id)
					->where('employee_id', '=', $employee->id)
					->where('deleted_at', 'IS', null)
					->get_one();

				if ($existing_receipt && $existing_receipt->status !== 'pending') {
					continue; // Skip si ya está procesado
				}

				// Crear o actualizar recibo
				$receipt = $existing_receipt ?: Model_Payroll_Receipt::forge();
				
				$receipt->tenant_id = $this->tenant_id;
				$receipt->period_id = $this->id;
				$receipt->employee_id = $employee->id;
				$receipt->receipt_number = $this->generate_receipt_number($employee);
				$receipt->payment_date = $this->payment_date;
				
				// Snapshot de datos del empleado
				$receipt->employee_code = $employee->code;
				$receipt->employee_name = $employee->get_full_name();
				$receipt->department_name = $employee->department ? $employee->department->name : null;
				$receipt->position_name = $employee->position ? $employee->position->name : null;
				$receipt->rfc = $employee->rfc;
				$receipt->nss = $employee->nss;
				$receipt->curp = $employee->curp;
				
				// Información salarial
				$receipt->base_salary = $employee->salary ?: 0;
				$receipt->daily_salary = $this->calculate_daily_salary($employee);
				$receipt->worked_days = $this->calculate_worked_days($employee);
				
				// Calcular percepciones y deducciones
				$perceptions = $this->calculate_perceptions($employee, $receipt);
				$deductions = $this->calculate_deductions($employee, $receipt, $perceptions);
				
				$receipt->total_perceptions = $perceptions;
				$receipt->total_deductions = $deductions;
				$receipt->net_payment = $perceptions - $deductions;
				$receipt->status = 'pending';
				
				$receipt->save();

				$total_employees++;
				$total_gross += $perceptions;
				$total_deductions += $deductions;
				$total_net += $receipt->net_payment;
			}

			// Actualizar totales del período
			$this->total_employees = $total_employees;
			$this->total_gross = $total_gross;
			$this->total_deductions = $total_deductions;
			$this->total_net = $total_net;
			$this->status = 'calculated';
			$this->calculated_by = $user_id;
			$this->calculated_at = date('Y-m-d H:i:s');
			$this->save();

			\DB::commit_transaction();

			return [
				'success' => true,
				'message' => 'Nómina calculada exitosamente',
				'employees' => $total_employees,
				'total_net' => $total_net,
			];

		} catch (\Exception $e) {
			\DB::rollback_transaction();
			
			return [
				'success' => false,
				'message' => 'Error al calcular nómina: ' . $e->getMessage(),
			];
		}
	}

	/**
	 * Generar número de recibo
	 */
	private function generate_receipt_number($employee)
	{
		return sprintf('REC-%s-%s-%04d',
			$this->code,
			$employee->code ?: $employee->id,
			$this->count_receipts() + 1
		);
	}

	/**
	 * Calcular salario diario
	 */
	private function calculate_daily_salary($employee)
	{
		if (!$employee->salary) return 0;

		switch ($employee->salary_type) {
			case 'daily':
				return $employee->salary;
			case 'monthly':
				return $employee->salary / 30;
			case 'biweekly':
				return $employee->salary / 15;
			case 'weekly':
				return $employee->salary / 7;
			default:
				return $employee->salary / 30;
		}
	}

	/**
	 * Calcular días trabajados
	 */
	private function calculate_worked_days($employee)
	{
		$start = new \DateTime($this->start_date);
		$end = new \DateTime($this->end_date);
		$interval = $start->diff($end);
		
		return $interval->days + 1;
	}

	/**
	 * Calcular percepciones
	 */
	private function calculate_perceptions($employee, $receipt)
	{
		// Por ahora solo el sueldo base
		// TODO: Agregar cálculo de otros conceptos de percepción
		return $employee->salary ?: 0;
	}

	/**
	 * Calcular deducciones
	 */
	private function calculate_deductions($employee, $receipt, $gross)
	{
		// Por ahora solo ISR básico (ejemplo simplificado)
		// TODO: Agregar cálculo completo de deducciones
		$isr = $gross * 0.10; // 10% simplificado
		$imss = $gross * 0.03; // 3% simplificado
		
		return $isr + $imss;
	}
}
