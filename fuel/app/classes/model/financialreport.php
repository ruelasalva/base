<?php

/**
 * Model_FinancialReport
 * 
 * Modelo para generar estados financieros
 * Balance General y Estado de Resultados
 */
class Model_FinancialReport
{
	/**
	 * Obtener datos para Balance General
	 * 
	 * @param int $tenant_id
	 * @param string $period_end Periodo final (YYYY-MM)
	 * @return array
	 */
	public static function get_balance_sheet($tenant_id, $period_end)
	{
		// Obtener saldos de cuentas de balance (activo, pasivo, capital)
		$query = DB::select(
			'aa.id',
			'aa.account_code',
			'aa.name',
			'aa.account_type',
			'aa.nature',
			'aa.parent_id',
			array(DB::expr('COALESCE(SUM(ael.debit), 0)'), 'total_debit'),
			array(DB::expr('COALESCE(SUM(ael.credit), 0)'), 'total_credit'),
			array(DB::expr('COALESCE(SUM(ael.debit) - SUM(ael.credit), 0)'), 'balance')
		)
		->from(array('accounting_accounts', 'aa'))
		->join(array('accounting_entry_lines', 'ael'), 'LEFT')
		->on('aa.id', '=', 'ael.account_id')
		->join(array('accounting_entries', 'ae'), 'LEFT')
		->on('ael.entry_id', '=', 'ae.id')
		->where('aa.tenant_id', $tenant_id)
		->where('aa.account_type', 'IN', array('activo', 'pasivo', 'capital'))
		->where('aa.allows_movement', 1)
		->where('aa.is_active', 1);

		// Solo pólizas aplicadas hasta el periodo
		$query->and_where_open()
			->where('ae.status', 'aplicada')
			->where('ae.period', '<=', $period_end)
			->or_where('ae.id', 'IS', DB::expr('NULL'))
			->and_where_close();

		$query->group_by('aa.id', 'aa.account_code', 'aa.name', 'aa.account_type', 'aa.nature', 'aa.parent_id');
		$query->order_by('aa.account_code', 'ASC');

		$accounts = $query->execute()->as_array();

		// Agrupar por tipo
		$grouped = array(
			'activo' => array(),
			'pasivo' => array(),
			'capital' => array(),
		);

		foreach ($accounts as $account) {
			// Solo incluir cuentas con saldo
			if (abs($account['balance']) > 0.01) {
				$grouped[$account['account_type']][] = $account;
			}
		}

		// Calcular totales
		$totals = array();
		foreach ($grouped as $type => $accounts) {
			$total = 0;
			foreach ($accounts as $account) {
				$total += $account['balance'];
			}
			$totals[$type] = $total;
		}

		return array(
			'accounts' => $grouped,
			'totals' => $totals,
		);
	}

	/**
	 * Obtener datos para Estado de Resultados
	 * 
	 * @param int $tenant_id
	 * @param string $period_from Periodo inicial (YYYY-MM)
	 * @param string $period_to Periodo final (YYYY-MM)
	 * @return array
	 */
	public static function get_income_statement($tenant_id, $period_from, $period_to)
	{
		// Obtener saldos de cuentas de resultados (ingreso, gasto)
		$query = DB::select(
			'aa.id',
			'aa.account_code',
			'aa.name',
			'aa.account_type',
			'aa.nature',
			array(DB::expr('COALESCE(SUM(ael.debit), 0)'), 'total_debit'),
			array(DB::expr('COALESCE(SUM(ael.credit), 0)'), 'total_credit'),
			array(DB::expr('COALESCE(SUM(ael.debit) - SUM(ael.credit), 0)'), 'balance')
		)
		->from(array('accounting_accounts', 'aa'))
		->join(array('accounting_entry_lines', 'ael'), 'LEFT')
		->on('aa.id', '=', 'ael.account_id')
		->join(array('accounting_entries', 'ae'), 'LEFT')
		->on('ael.entry_id', '=', 'ae.id')
		->where('aa.tenant_id', $tenant_id)
		->where('aa.account_type', 'IN', array('ingreso', 'gasto'))
		->where('aa.allows_movement', 1)
		->where('aa.is_active', 1);

		// Solo pólizas aplicadas en el rango de periodos
		$query->and_where_open()
			->where('ae.status', 'aplicada')
			->where('ae.period', '>=', $period_from)
			->where('ae.period', '<=', $period_to)
			->or_where('ae.id', 'IS', DB::expr('NULL'))
			->and_where_close();

		$query->group_by('aa.id', 'aa.account_code', 'aa.name', 'aa.account_type', 'aa.nature');
		$query->order_by('aa.account_code', 'ASC');

		$accounts = $query->execute()->as_array();

		// Agrupar por tipo
		$grouped = array(
			'ingreso' => array(),
			'gasto' => array(),
		);

		foreach ($accounts as $account) {
			// Solo incluir cuentas con movimiento
			if (abs($account['balance']) > 0.01) {
				$grouped[$account['account_type']][] = $account;
			}
		}

		// Calcular totales
		$totals = array();
		foreach ($grouped as $type => $accounts) {
			$total = 0;
			foreach ($accounts as $account) {
				// Los ingresos tienen naturaleza acreedora, por lo que su balance es negativo
				// Los gastos tienen naturaleza deudora, por lo que su balance es positivo
				$total += abs($account['balance']);
			}
			$totals[$type] = $total;
		}

		// Calcular utilidad neta
		$net_income = $totals['ingreso'] - $totals['gasto'];

		return array(
			'accounts' => $grouped,
			'totals' => $totals,
			'net_income' => $net_income,
		);
	}

	/**
	 * Obtener razones financieras básicas
	 * 
	 * @param int $tenant_id
	 * @param string $period
	 * @return array
	 */
	public static function get_financial_ratios($tenant_id, $period)
	{
		$balance = self::get_balance_sheet($tenant_id, $period);
		$totals = $balance['totals'];

		$ratios = array();

		// Capital de trabajo (Activo circulante - Pasivo circulante)
		// Simplificado: Activo total - Pasivo total
		$ratios['working_capital'] = $totals['activo'] - $totals['pasivo'];

		// Razón de endeudamiento (Pasivo / Activo)
		$ratios['debt_ratio'] = $totals['activo'] > 0 
			? ($totals['pasivo'] / $totals['activo']) * 100 
			: 0;

		// Patrimonio
		$ratios['equity'] = $totals['capital'];

		// Razón de autonomía (Capital / Activo)
		$ratios['autonomy_ratio'] = $totals['activo'] > 0 
			? ($totals['capital'] / $totals['activo']) * 100 
			: 0;

		return $ratios;
	}

	/**
	 * Obtener comparativo de periodos para Estado de Resultados
	 * 
	 * @param int $tenant_id
	 * @param array $periods Array de periodos ['2025-01', '2025-02', ...]
	 * @return array
	 */
	public static function get_comparative_income_statement($tenant_id, $periods)
	{
		$comparative = array();

		foreach ($periods as $period) {
			$statement = self::get_income_statement($tenant_id, $period, $period);
			$comparative[$period] = array(
				'ingresos' => $statement['totals']['ingreso'],
				'gastos' => $statement['totals']['gasto'],
				'utilidad' => $statement['net_income'],
			);
		}

		return $comparative;
	}

	/**
	 * Exportar Balance General a array para PDF/Excel
	 * 
	 * @param int $tenant_id
	 * @param string $period
	 * @return array
	 */
	public static function export_balance_sheet($tenant_id, $period)
	{
		$data = self::get_balance_sheet($tenant_id, $period);
		
		$export = array();
		$export[] = array('Balance General al ' . $period);
		$export[] = array('');
		
		// Activos
		$export[] = array('ACTIVOS');
		foreach ($data['accounts']['activo'] as $account) {
			$export[] = array(
				$account['account_code'],
				$account['name'],
				number_format(abs($account['balance']), 2)
			);
		}
		$export[] = array('', 'TOTAL ACTIVOS', number_format($data['totals']['activo'], 2));
		$export[] = array('');
		
		// Pasivos
		$export[] = array('PASIVOS');
		foreach ($data['accounts']['pasivo'] as $account) {
			$export[] = array(
				$account['account_code'],
				$account['name'],
				number_format(abs($account['balance']), 2)
			);
		}
		$export[] = array('', 'TOTAL PASIVOS', number_format($data['totals']['pasivo'], 2));
		$export[] = array('');
		
		// Capital
		$export[] = array('CAPITAL');
		foreach ($data['accounts']['capital'] as $account) {
			$export[] = array(
				$account['account_code'],
				$account['name'],
				number_format(abs($account['balance']), 2)
			);
		}
		$export[] = array('', 'TOTAL CAPITAL', number_format($data['totals']['capital'], 2));
		
		return $export;
	}

	/**
	 * Exportar Estado de Resultados a array para PDF/Excel
	 * 
	 * @param int $tenant_id
	 * @param string $period_from
	 * @param string $period_to
	 * @return array
	 */
	public static function export_income_statement($tenant_id, $period_from, $period_to)
	{
		$data = self::get_income_statement($tenant_id, $period_from, $period_to);
		
		$export = array();
		$export[] = array('Estado de Resultados del ' . $period_from . ' al ' . $period_to);
		$export[] = array('');
		
		// Ingresos
		$export[] = array('INGRESOS');
		foreach ($data['accounts']['ingreso'] as $account) {
			$export[] = array(
				$account['account_code'],
				$account['name'],
				number_format(abs($account['balance']), 2)
			);
		}
		$export[] = array('', 'TOTAL INGRESOS', number_format($data['totals']['ingreso'], 2));
		$export[] = array('');
		
		// Gastos
		$export[] = array('GASTOS');
		foreach ($data['accounts']['gasto'] as $account) {
			$export[] = array(
				$account['account_code'],
				$account['name'],
				number_format(abs($account['balance']), 2)
			);
		}
		$export[] = array('', 'TOTAL GASTOS', number_format($data['totals']['gasto'], 2));
		$export[] = array('');
		
		// Utilidad
		$export[] = array('', 'UTILIDAD NETA', number_format($data['net_income'], 2));
		
		return $export;
	}
}
