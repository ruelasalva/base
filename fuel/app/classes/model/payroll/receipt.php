<?php

/**
 * Model_Payroll_Receipt
 * Sistema de Nómina - Recibos de Nómina
 * 
 * @package    App
 * @subpackage Model
 * @category   Payroll
 */
class Model_Payroll_Receipt extends \Orm\Model
{
	protected static $_properties = [
		'id',
		'tenant_id' => ['data_type' => 'int', 'default' => 1],
		'period_id' => ['data_type' => 'int', 'validation' => ['required']],
		'employee_id' => ['data_type' => 'int', 'validation' => ['required']],
		'receipt_number' => ['data_type' => 'varchar', 'validation' => ['required']],
		'payment_date' => ['data_type' => 'date', 'validation' => ['required']],
		'employee_code',
		'employee_name',
		'department_name',
		'position_name',
		'rfc',
		'nss',
		'curp',
		'base_salary' => ['data_type' => 'decimal', 'default' => 0.00],
		'daily_salary' => ['data_type' => 'decimal', 'default' => 0.00],
		'worked_days' => ['data_type' => 'decimal', 'default' => 0.00],
		'absence_days' => ['data_type' => 'decimal', 'default' => 0.00],
		'overtime_hours' => ['data_type' => 'decimal', 'default' => 0.00],
		'total_perceptions' => ['data_type' => 'decimal', 'default' => 0.00],
		'total_deductions' => ['data_type' => 'decimal', 'default' => 0.00],
		'net_payment' => ['data_type' => 'decimal', 'default' => 0.00],
		'is_stamped' => ['data_type' => 'int', 'default' => 0],
		'cfdi_uuid',
		'cfdi_xml',
		'cfdi_pdf',
		'stamped_at',
		'status' => ['data_type' => 'enum', 'default' => 'pending'],
		'payment_method',
		'bank_reference',
		'paid_at',
		'notes',
		'is_active' => ['data_type' => 'int', 'default' => 1],
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected static $_observers = [
		'Orm\Observer_CreatedAt' => ['events' => ['before_insert'], 'mysql_timestamp' => true],
		'Orm\Observer_UpdatedAt' => ['events' => ['before_update'], 'mysql_timestamp' => true],
	];

	protected static $_table_name = 'payroll_receipts';
	
	protected static $_belongs_to = [
		'period' => ['key_from' => 'period_id', 'model_to' => 'Model_Payroll_Period', 'key_to' => 'id'],
		'employee' => ['key_from' => 'employee_id', 'model_to' => 'Model_Employee', 'key_to' => 'id'],
	];

	protected static $_has_many = [
		'details' => [
			'key_from' => 'id',
			'model_to' => 'Model_Payroll_Receipt_Detail',
			'key_to' => 'receipt_id',
			'cascade_save' => true,
			'cascade_delete' => true,
		],
	];

	protected static $_soft_delete = ['deleted_field' => 'deleted_at', 'mysql_timestamp' => true];

	public function get_status_label()
	{
		$statuses = ['pending' => 'Pendiente', 'approved' => 'Aprobado', 'paid' => 'Pagado', 'cancelled' => 'Cancelado'];
		return isset($statuses[$this->status]) ? $statuses[$this->status] : $this->status;
	}

	public function get_status_badge()
	{
		$badges = [
			'pending' => '<span class="badge bg-warning">Pendiente</span>',
			'approved' => '<span class="badge bg-success">Aprobado</span>',
			'paid' => '<span class="badge bg-primary">Pagado</span>',
			'cancelled' => '<span class="badge bg-danger">Cancelado</span>',
		];
		return isset($badges[$this->status]) ? $badges[$this->status] : '<span class="badge bg-light">' . $this->status . '</span>';
	}
}
