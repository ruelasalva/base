<?php

/**
 * Controller_Admin_Reportesfinancieros
 * 
 * Reportes Financieros
 * Balance General y Estado de Resultados
 */
class Controller_Admin_Reportesfinancieros extends Controller_Admin
{
	/**
	 * Menú de reportes
	 */
	public function action_index()
	{
		if (!Helper_Permission::can('reportes_financieros', 'view')) {
			Session::set_flash('error', 'No tienes permisos para ver reportes financieros');
			Response::redirect('admin');
		}

		$this->template->title = 'Reportes Financieros';
		$this->template->content = View::forge('admin/reportesfinancieros/index', array(), false);
	}

	/**
	 * Balance General
	 */
	public function action_balance()
	{
		if (!Helper_Permission::can('reportes_financieros', 'view')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$period = Input::get('period', date('Y-m'));

		// Obtener datos del balance
		$balance_data = Model_FinancialReport::get_balance_sheet($tenant_id, $period);
		$ratios = Model_FinancialReport::get_financial_ratios($tenant_id, $period);

		$this->template->title = 'Balance General';
		$this->template->content = View::forge('admin/reportesfinancieros/balance', array(
			'period' => $period,
			'accounts' => $balance_data['accounts'],
			'totals' => $balance_data['totals'],
			'ratios' => $ratios,
		), false);
	}

	/**
	 * Estado de Resultados
	 */
	public function action_resultados()
	{
		if (!Helper_Permission::can('reportes_financieros', 'view')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$period_from = Input::get('period_from', date('Y-m'));
		$period_to = Input::get('period_to', date('Y-m'));

		// Obtener datos del estado de resultados
		$income_data = Model_FinancialReport::get_income_statement($tenant_id, $period_from, $period_to);

		$this->template->title = 'Estado de Resultados';
		$this->template->content = View::forge('admin/reportesfinancieros/resultados', array(
			'period_from' => $period_from,
			'period_to' => $period_to,
			'accounts' => $income_data['accounts'],
			'totals' => $income_data['totals'],
			'net_income' => $income_data['net_income'],
		), false);
	}

	/**
	 * Comparativo de periodos (Estado de Resultados)
	 */
	public function action_comparativo()
	{
		if (!Helper_Permission::can('reportes_financieros', 'view')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);
		
		// Generar últimos 6 meses por defecto
		$periods = array();
		for ($i = 5; $i >= 0; $i--) {
			$periods[] = date('Y-m', strtotime("-$i months"));
		}

		// Obtener datos comparativos
		$comparative = Model_FinancialReport::get_comparative_income_statement($tenant_id, $periods);

		$this->template->title = 'Comparativo de Resultados';
		$this->template->content = View::forge('admin/reportesfinancieros/comparativo', array(
			'periods' => $periods,
			'comparative' => $comparative,
		), false);
	}

	/**
	 * Exportar Balance General a CSV
	 */
	public function action_export_balance()
	{
		if (!Helper_Permission::can('reportes_financieros', 'export')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$period = Input::get('period', date('Y-m'));

		$export_data = Model_FinancialReport::export_balance_sheet($tenant_id, $period);

		// Generar CSV
		$csv_output = '';
		foreach ($export_data as $row) {
			$csv_output .= '"' . implode('","', $row) . '"' . "\n";
		}

		$filename = 'balance_general_' . $period . '.csv';

		return Response::forge($csv_output, 200, array(
			'Content-Type' => 'text/csv; charset=utf-8',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
		));
	}

	/**
	 * Exportar Estado de Resultados a CSV
	 */
	public function action_export_resultados()
	{
		if (!Helper_Permission::can('reportes_financieros', 'export')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$period_from = Input::get('period_from', date('Y-m'));
		$period_to = Input::get('period_to', date('Y-m'));

		$export_data = Model_FinancialReport::export_income_statement($tenant_id, $period_from, $period_to);

		// Generar CSV
		$csv_output = '';
		foreach ($export_data as $row) {
			$csv_output .= '"' . implode('","', $row) . '"' . "\n";
		}

		$filename = 'estado_resultados_' . $period_from . '_' . $period_to . '.csv';

		return Response::forge($csv_output, 200, array(
			'Content-Type' => 'text/csv; charset=utf-8',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
		));
	}
}
