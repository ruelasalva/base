<?php

/**
 * Controller_Admin_Libromayor
 * 
 * Libro Mayor (General Ledger)
 * Muestra los movimientos contables agrupados por cuenta
 */
class Controller_Admin_Libromayor extends Controller_Admin
{
	/**
	 * Consulta del libro mayor
	 */
	public function action_index()
	{
		if (!Helper_Permission::can('libro_mayor', 'view')) {
			Session::set_flash('error', 'No tienes permisos para ver el libro mayor');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Filtros
		$account_id = Input::get('account_id', '');
		$account_type = Input::get('account_type', '');
		$period_from = Input::get('period_from', date('Y-m'));
		$period_to = Input::get('period_to', date('Y-m'));
		$entry_type = Input::get('entry_type', '');

		// Preparar filtros
		$filters = array();
		if ($account_id) {
			$filters['account_id'] = $account_id;
		}
		if ($account_type) {
			$filters['account_type'] = $account_type;
		}
		if ($period_from) {
			$filters['period_from'] = $period_from;
		}
		if ($period_to) {
			$filters['period_to'] = $period_to;
		}
		if ($entry_type) {
			$filters['entry_type'] = $entry_type;
		}

		// Paginación
		$config = array(
			'pagination_url' => Uri::create('admin/libromayor/index'),
			'total_items' => Model_LedgerEntry::count_movements($tenant_id, $filters),
			'per_page' => 50,
			'uri_segment' => 'page',
		);

		$pagination = Pagination::forge('libromayor', $config);

		// Obtener movimientos
		$movements = Model_LedgerEntry::get_ledger_movements(
			$tenant_id,
			$filters,
			$pagination->per_page,
			$pagination->offset
		);

		// Calcular saldo acumulado si hay cuenta específica
		$running_balance = 0;
		$initial_balance = 0;
		if ($account_id) {
			$initial_balance = Model_LedgerEntry::get_initial_balance($tenant_id, $account_id, $period_from);
			$running_balance = $initial_balance;
		}

		// Agregar saldo acumulado a cada movimiento
		foreach ($movements as &$movement) {
			$running_balance += ($movement['debit'] - $movement['credit']);
			$movement['running_balance'] = $running_balance;
		}

		// Estadísticas del periodo
		$stats = Model_LedgerEntry::get_statistics($tenant_id, $period_from);

		// Obtener cuentas para el filtro
		$accounts = Model_AccountingAccount::query()
			->where('tenant_id', $tenant_id)
			->where('allows_movement', 1)
			->where('is_active', 1)
			->order_by('account_code', 'ASC')
			->get();

		$this->template->title = 'Libro Mayor';
		$this->template->content = View::forge('admin/libromayor/index', array(
			'movements' => $movements,
			'pagination' => $pagination,
			'stats' => $stats,
			'accounts' => $accounts,
			'account_id' => $account_id,
			'account_type' => $account_type,
			'period_from' => $period_from,
			'period_to' => $period_to,
			'entry_type' => $entry_type,
			'initial_balance' => $initial_balance,
			'final_balance' => $running_balance,
		), false);
	}

	/**
	 * Ver saldos de todas las cuentas
	 */
	public function action_balances()
	{
		if (!Helper_Permission::can('libro_mayor', 'view')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$period = Input::get('period', date('Y-m'));

		// Obtener saldos por cuenta
		$balances = Model_LedgerEntry::get_account_balances($tenant_id, $period);

		// Agrupar por tipo de cuenta
		$grouped_balances = array(
			'activo' => array(),
			'pasivo' => array(),
			'capital' => array(),
			'ingreso' => array(),
			'gasto' => array(),
			'resultado' => array(),
		);

		foreach ($balances as $balance) {
			$type = $balance['account_type'];
			if (isset($grouped_balances[$type])) {
				$grouped_balances[$type][] = $balance;
			}
		}

		// Calcular totales por tipo
		$totals = array();
		foreach ($grouped_balances as $type => $accounts) {
			$total = 0;
			foreach ($accounts as $account) {
				$total += $account['balance'];
			}
			$totals[$type] = $total;
		}

		$this->template->title = 'Saldos por Cuenta';
		$this->template->content = View::forge('admin/libromayor/balances', array(
			'grouped_balances' => $grouped_balances,
			'totals' => $totals,
			'period' => $period,
		), false);
	}

	/**
	 * Exportar a Excel
	 */
	public function action_export()
	{
		if (!Helper_Permission::can('libro_mayor', 'view')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Obtener los mismos filtros
		$filters = array();
		if (Input::get('account_id')) {
			$filters['account_id'] = Input::get('account_id');
		}
		if (Input::get('account_type')) {
			$filters['account_type'] = Input::get('account_type');
		}
		if (Input::get('period_from')) {
			$filters['period_from'] = Input::get('period_from');
		}
		if (Input::get('period_to')) {
			$filters['period_to'] = Input::get('period_to');
		}
		if (Input::get('entry_type')) {
			$filters['entry_type'] = Input::get('entry_type');
		}

		// Obtener todos los movimientos (sin límite)
		$movements = Model_LedgerEntry::get_ledger_movements($tenant_id, $filters);

		// Generar CSV
		$csv_output = "Folio,Fecha,Tipo,Cuenta,Descripción,Cargo,Abono,Concepto\n";

		foreach ($movements as $movement) {
			$csv_output .= sprintf(
				'"%s","%s","%s","%s - %s","%s","%s","%s","%s"' . "\n",
				$movement['entry_number'],
				date('d/m/Y', strtotime($movement['entry_date'])),
				ucfirst($movement['entry_type']),
				$movement['account_code'],
				$movement['account_name'],
				$movement['description'],
				number_format($movement['debit'], 2),
				number_format($movement['credit'], 2),
				$movement['concept']
			);
		}

		$filename = 'libro_mayor_' . date('Ymd_His') . '.csv';

		return Response::forge($csv_output, 200, array(
			'Content-Type' => 'text/csv; charset=utf-8',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
		));
	}
}
