<?php

/**
 * Controller_Admin_Rrhh
 * Dashboard Ejecutivo de Recursos Humanos
 * 
 * @package    App
 * @subpackage Controller
 * @category   RRHH
 */
class Controller_Admin_Rrhh extends Controller_Admin
{
	public function before()
	{
		parent::before();
		
		/*if (!Auth::has_access('rrhh.index')) {
			Session::set_flash('error', 'No tienes permiso para acceder a este módulo');
			Response::redirect('admin/dashboard');
		}*/
	}

	/**
	 * Dashboard principal de RRHH
	 */
	public function action_index()
	{
		// KPIs Principales
		$kpis = $this->get_kpis();
		
		// Estadísticas de empleados
		$employee_stats = $this->get_employee_statistics();
		
		// Estadísticas de nómina
		$payroll_stats = $this->get_payroll_statistics();
		
		// Datos para gráficos
		$charts_data = $this->get_charts_data();
		
		// Alertas y notificaciones
		$alerts = $this->get_alerts();

		$this->template->title = 'Dashboard de Recursos Humanos';
		$this->template->content = View::forge('admin/rrhh/index', [
			'kpis' => $kpis,
			'employee_stats' => $employee_stats,
			'payroll_stats' => $payroll_stats,
			'charts_data' => $charts_data,
			'alerts' => $alerts,
		]);
	}

	/**
	 * Obtener KPIs principales
	 */
	private function get_kpis()
	{
		$tenant_id = $this->tenant_id;

		// Total de empleados activos
		$total_employees = Model_Employee::count([
			'where' => [
				['tenant_id', '=', $tenant_id],
				['employment_status', '=', 'active'],
				['deleted_at', 'IS', null],
			],
		]);

		// Total de departamentos
		$total_departments = Model_Department::count([
			'where' => [
				['tenant_id', '=', $tenant_id],
				['is_active', '=', 1],
				['deleted_at', 'IS', null],
			],
		]);

		// Nuevas contrataciones este mes
		$new_hires = Model_Employee::count([
			'where' => [
				['tenant_id', '=', $tenant_id],
				['hire_date', '>=', date('Y-m-01')],
				['deleted_at', 'IS', null],
			],
		]);

		// Rotación del mes (bajas)
		$terminations = Model_Employee::count([
			'where' => [
				['tenant_id', '=', $tenant_id],
				['termination_date', '>=', date('Y-m-01')],
				['deleted_at', 'IS', null],
			],
		]);

		// Nómina del mes actual
		$current_payroll = \DB::select(\DB::expr('SUM(total_net) as total'))
			->from('payroll_periods')
			->where('tenant_id', '=', $tenant_id)
			->where('year', '=', date('Y'))
			->where('period_number', '=', date('n'))
			->where('deleted_at', 'IS', null)
			->execute()
			->get('total', 0);

		// Promedio salarial
		$avg_salary = \DB::select(\DB::expr('AVG(salary) as average'))
			->from('employees')
			->where('tenant_id', '=', $tenant_id)
			->where('employment_status', '=', 'active')
			->where('deleted_at', 'IS', null)
			->where('salary', '>', 0)
			->execute()
			->get('average', 0);

		return [
			'total_employees' => $total_employees,
			'total_departments' => $total_departments,
			'new_hires' => $new_hires,
			'terminations' => $terminations,
			'current_payroll' => $current_payroll,
			'avg_salary' => $avg_salary,
			'turnover_rate' => $total_employees > 0 ? round(($terminations / $total_employees) * 100, 2) : 0,
		];
	}

	/**
	 * Estadísticas de empleados
	 */
	private function get_employee_statistics()
	{
		$tenant_id = $this->tenant_id;

		// Por departamento
		$by_department = \DB::select(
				'd.name as department',
				\DB::expr('COUNT(e.id) as count')
			)
			->from(['employees', 'e'])
			->join(['departments', 'd'], 'LEFT')
			->on('e.department_id', '=', 'd.id')
			->where('e.tenant_id', '=', $tenant_id)
			->where('e.employment_status', '=', 'active')
			->where('e.deleted_at', 'IS', null)
			->group_by('e.department_id')
			->order_by('count', 'desc')
			->execute()
			->as_array();

		// Por género
		$by_gender = \DB::select('gender', \DB::expr('COUNT(*) as count'))
			->from('employees')
			->where('tenant_id', '=', $tenant_id)
			->where('employment_status', '=', 'active')
			->where('deleted_at', 'IS', null)
			->where('gender', 'IS NOT', null)
			->group_by('gender')
			->execute()
			->as_array();

		// Por tipo de empleo
		$by_type = \DB::select('employment_type', \DB::expr('COUNT(*) as count'))
			->from('employees')
			->where('tenant_id', '=', $tenant_id)
			->where('employment_status', '=', 'active')
			->where('deleted_at', 'IS', null)
			->group_by('employment_type')
			->execute()
			->as_array();

		// Por antigüedad
		$by_seniority = \DB::select(
				\DB::expr('CASE 
					WHEN TIMESTAMPDIFF(YEAR, hire_date, CURDATE()) < 1 THEN "Menos de 1 año"
					WHEN TIMESTAMPDIFF(YEAR, hire_date, CURDATE()) BETWEEN 1 AND 3 THEN "1-3 años"
					WHEN TIMESTAMPDIFF(YEAR, hire_date, CURDATE()) BETWEEN 3 AND 5 THEN "3-5 años"
					WHEN TIMESTAMPDIFF(YEAR, hire_date, CURDATE()) BETWEEN 5 AND 10 THEN "5-10 años"
					ELSE "Más de 10 años"
				END as seniority'),
				\DB::expr('COUNT(*) as count')
			)
			->from('employees')
			->where('tenant_id', '=', $tenant_id)
			->where('employment_status', '=', 'active')
			->where('deleted_at', 'IS', null)
			->group_by('seniority')
			->execute()
			->as_array();

		return [
			'by_department' => $by_department,
			'by_gender' => $by_gender,
			'by_type' => $by_type,
			'by_seniority' => $by_seniority,
		];
	}

	/**
	 * Estadísticas de nómina
	 */
	private function get_payroll_statistics()
	{
		$tenant_id = $this->tenant_id;
		$current_year = date('Y');

		// Nómina por mes del año actual
		$monthly_payroll = \DB::select(
				'period_number',
				'total_gross',
				'total_deductions',
				'total_net'
			)
			->from('payroll_periods')
			->where('tenant_id', '=', $tenant_id)
			->where('year', '=', $current_year)
			->where('deleted_at', 'IS', null)
			->order_by('period_number', 'asc')
			->execute()
			->as_array();

		// Total anual
		$annual_total = \DB::select(
				\DB::expr('SUM(total_gross) as gross'),
				\DB::expr('SUM(total_deductions) as deductions'),
				\DB::expr('SUM(total_net) as net')
			)
			->from('payroll_periods')
			->where('tenant_id', '=', $tenant_id)
			->where('year', '=', $current_year)
			->where('deleted_at', 'IS', null)
			->execute()
			->get();

		// Último período procesado
		$last_period = Model_Payroll_Period::query()
			->where('tenant_id', '=', $tenant_id)
			->where('status', 'IN', ['calculated', 'approved', 'paid', 'closed'])
			->where('deleted_at', 'IS', null)
			->order_by('year', 'desc')
			->order_by('period_number', 'desc')
			->get_one();

		return [
			'monthly_payroll' => $monthly_payroll,
			'annual_total' => $annual_total,
			'last_period' => $last_period,
		];
	}

	/**
	 * Datos para gráficos
	 */
	private function get_charts_data()
	{
		$tenant_id = $this->tenant_id;

		// Gráfico de contrataciones por mes (últimos 12 meses)
		$hires_by_month = [];
		for ($i = 11; $i >= 0; $i--) {
			$month = date('Y-m', strtotime("-$i months"));
			$count = Model_Employee::count([
				'where' => [
					['tenant_id', '=', $tenant_id],
					['hire_date', '>=', $month . '-01'],
					['hire_date', '<=', date('Y-m-t', strtotime($month))],
					['deleted_at', 'IS', null],
				],
			]);
			$hires_by_month[] = ['month' => $month, 'count' => $count];
		}

		// Distribución salarial por rango
		$salary_ranges = [
			['min' => 0, 'max' => 10000, 'label' => 'Hasta $10,000'],
			['min' => 10000, 'max' => 20000, 'label' => '$10,000 - $20,000'],
			['min' => 20000, 'max' => 30000, 'label' => '$20,000 - $30,000'],
			['min' => 30000, 'max' => 50000, 'label' => '$30,000 - $50,000'],
			['min' => 50000, 'max' => 999999, 'label' => 'Más de $50,000'],
		];

		$salary_distribution = [];
		foreach ($salary_ranges as $range) {
			$count = Model_Employee::count([
				'where' => [
					['tenant_id', '=', $tenant_id],
					['employment_status', '=', 'active'],
					['salary', '>=', $range['min']],
					['salary', '<', $range['max']],
					['deleted_at', 'IS', null],
				],
			]);
			$salary_distribution[] = ['label' => $range['label'], 'count' => $count];
		}

		return [
			'hires_by_month' => $hires_by_month,
			'salary_distribution' => $salary_distribution,
		];
	}

	/**
	 * Obtener alertas
	 */
	private function get_alerts()
	{
		$tenant_id = $this->tenant_id;
		$alerts = [];

		// Cumpleaños del mes
		$birthdays = Model_Employee::query()
			->where('tenant_id', '=', $tenant_id)
			->where('employment_status', '=', 'active')
			->where(\DB::expr('MONTH(birthdate)'), '=', date('n'))
			->where('deleted_at', 'IS', null)
			->get();

		if (count($birthdays) > 0) {
			$alerts[] = [
				'type' => 'info',
				'icon' => 'fa-birthday-cake',
				'title' => 'Cumpleaños del mes',
				'message' => count($birthdays) . ' empleados cumplen años este mes',
				'link' => 'admin/empleados',
			];
		}

		// Empleados sin departamento
		$no_department = Model_Employee::count([
			'where' => [
				['tenant_id', '=', $tenant_id],
				['employment_status', '=', 'active'],
				['department_id', 'IS', null],
				['deleted_at', 'IS', null],
			],
		]);

		if ($no_department > 0) {
			$alerts[] = [
				'type' => 'warning',
				'icon' => 'fa-exclamation-triangle',
				'title' => 'Empleados sin departamento',
				'message' => $no_department . ' empleados no tienen departamento asignado',
				'link' => 'admin/empleados',
			];
		}

		// Nóminas pendientes
		$pending_payroll = Model_Payroll_Period::count([
			'where' => [
				['tenant_id', '=', $tenant_id],
				['status', 'IN', ['draft', 'in_progress']],
				['deleted_at', 'IS', null],
			],
		]);

		if ($pending_payroll > 0) {
			$alerts[] = [
				'type' => 'primary',
				'icon' => 'fa-money-bill-wave',
				'title' => 'Nóminas pendientes',
				'message' => $pending_payroll . ' períodos de nómina pendientes de procesar',
				'link' => 'admin/nomina',
			];
		}

		return $alerts;
	}

	/**
	 * Analytics avanzado
	 */
	public function action_analytics()
	{
		if (!Auth::has_access('rrhh.analytics')) {
			Session::set_flash('error', 'No tienes permiso para ver analytics');
			Response::redirect('admin/rrhh');
		}

		// Análisis más profundo...
		$this->template->title = 'Analytics de RRHH';
		$this->template->content = View::forge('admin/rrhh/analytics');
	}

	/**
	 * Reportes ejecutivos
	 */
	public function action_reports()
	{
		if (!Auth::has_access('rrhh.reports')) {
			Session::set_flash('error', 'No tienes permiso para ver reportes');
			Response::redirect('admin/rrhh');
		}

		$this->template->title = 'Reportes Ejecutivos';
		$this->template->content = View::forge('admin/rrhh/reports');
	}
}
