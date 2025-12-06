<?php

/**
 * Model_LedgerEntry
 * 
 * Modelo para consultar el Libro Mayor (General Ledger)
 * No es una tabla física, sino consultas sobre accounting_entry_lines
 */
class Model_LedgerEntry
{
	/**
	 * Obtener movimientos del libro mayor con filtros
	 * 
	 * @param int $tenant_id
	 * @param array $filters (account_id, period_from, period_to, entry_type, status)
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public static function get_ledger_movements($tenant_id, $filters = array(), $limit = null, $offset = null)
	{
		$query = DB::select(
			'ae.entry_number',
			'ae.entry_date',
			'ae.entry_type',
			'ae.period',
			'ae.concept',
			'ae.status',
			'ael.line_number',
			'ael.description',
			'ael.debit',
			'ael.credit',
			'ael.reference',
			array('aa.account_code', 'account_code'),
			array('aa.name', 'account_name'),
			array('aa.account_type', 'account_type')
		)
		->from(array('accounting_entry_lines', 'ael'))
		->join(array('accounting_entries', 'ae'), 'INNER')
		->on('ael.entry_id', '=', 'ae.id')
		->join(array('accounting_accounts', 'aa'), 'INNER')
		->on('ael.account_id', '=', 'aa.id')
		->where('ae.tenant_id', $tenant_id)
		->where('ae.status', 'aplicada'); // Solo pólizas aplicadas

		// Filtro por cuenta
		if (!empty($filters['account_id'])) {
			$query->where('ael.account_id', $filters['account_id']);
		}

		// Filtro por tipo de cuenta
		if (!empty($filters['account_type'])) {
			$query->where('aa.account_type', $filters['account_type']);
		}

		// Filtro por periodo desde
		if (!empty($filters['period_from'])) {
			$query->where('ae.period', '>=', $filters['period_from']);
		}

		// Filtro por periodo hasta
		if (!empty($filters['period_to'])) {
			$query->where('ae.period', '<=', $filters['period_to']);
		}

		// Filtro por tipo de póliza
		if (!empty($filters['entry_type'])) {
			$query->where('ae.entry_type', $filters['entry_type']);
		}

		// Ordenar por fecha y folio
		$query->order_by('ae.entry_date', 'ASC');
		$query->order_by('ae.entry_number', 'ASC');
		$query->order_by('ael.line_number', 'ASC');

		if ($limit) {
			$query->limit($limit);
		}

		if ($offset) {
			$query->offset($offset);
		}

		return $query->execute()->as_array();
	}

	/**
	 * Contar total de movimientos con filtros
	 * 
	 * @param int $tenant_id
	 * @param array $filters
	 * @return int
	 */
	public static function count_movements($tenant_id, $filters = array())
	{
		$query = DB::select(DB::expr('COUNT(*) as total'))
			->from(array('accounting_entry_lines', 'ael'))
			->join(array('accounting_entries', 'ae'), 'INNER')
			->on('ael.entry_id', '=', 'ae.id')
			->join(array('accounting_accounts', 'aa'), 'INNER')
			->on('ael.account_id', '=', 'aa.id')
			->where('ae.tenant_id', $tenant_id)
			->where('ae.status', 'aplicada');

		if (!empty($filters['account_id'])) {
			$query->where('ael.account_id', $filters['account_id']);
		}

		if (!empty($filters['account_type'])) {
			$query->where('aa.account_type', $filters['account_type']);
		}

		if (!empty($filters['period_from'])) {
			$query->where('ae.period', '>=', $filters['period_from']);
		}

		if (!empty($filters['period_to'])) {
			$query->where('ae.period', '<=', $filters['period_to']);
		}

		if (!empty($filters['entry_type'])) {
			$query->where('ae.entry_type', $filters['entry_type']);
		}

		$result = $query->execute()->as_array();
		return $result[0]['total'];
	}

	/**
	 * Calcular saldos por cuenta en un periodo
	 * 
	 * @param int $tenant_id
	 * @param string $period Formato: YYYY-MM
	 * @param int $account_id (opcional)
	 * @return array
	 */
	public static function get_account_balances($tenant_id, $period, $account_id = null)
	{
		$query = DB::select(
			'aa.id',
			'aa.account_code',
			'aa.name',
			'aa.account_type',
			'aa.nature',
			array(DB::expr('SUM(ael.debit)'), 'total_debit'),
			array(DB::expr('SUM(ael.credit)'), 'total_credit'),
			array(DB::expr('SUM(ael.debit) - SUM(ael.credit)'), 'balance')
		)
		->from(array('accounting_accounts', 'aa'))
		->join(array('accounting_entry_lines', 'ael'), 'LEFT')
		->on('aa.id', '=', 'ael.account_id')
		->join(array('accounting_entries', 'ae'), 'LEFT')
		->on('ael.entry_id', '=', 'ae.id')
		->where('aa.tenant_id', $tenant_id)
		->where('aa.allows_movement', 1);

		if ($period) {
			$query->where('ae.period', '<=', $period);
			$query->where('ae.status', 'aplicada');
		}

		if ($account_id) {
			$query->where('aa.id', $account_id);
		}

		$query->group_by('aa.id', 'aa.account_code', 'aa.name', 'aa.account_type', 'aa.nature');
		$query->having(DB::expr('SUM(ael.debit)'), '>', 0);
		$query->or_having(DB::expr('SUM(ael.credit)'), '>', 0);
		$query->order_by('aa.account_code', 'ASC');

		return $query->execute()->as_array();
	}

	/**
	 * Obtener saldo inicial de una cuenta hasta un periodo
	 * 
	 * @param int $tenant_id
	 * @param int $account_id
	 * @param string $period_until Formato: YYYY-MM
	 * @return float
	 */
	public static function get_initial_balance($tenant_id, $account_id, $period_until)
	{
		$result = DB::select(
			array(DB::expr('SUM(ael.debit) - SUM(ael.credit)'), 'balance')
		)
		->from(array('accounting_entry_lines', 'ael'))
		->join(array('accounting_entries', 'ae'), 'INNER')
		->on('ael.entry_id', '=', 'ae.id')
		->where('ae.tenant_id', $tenant_id)
		->where('ael.account_id', $account_id)
		->where('ae.period', '<', $period_until)
		->where('ae.status', 'aplicada')
		->execute()
		->as_array();

		return $result[0]['balance'] ?? 0;
	}

	/**
	 * Obtener estadísticas del libro mayor
	 * 
	 * @param int $tenant_id
	 * @param string $period
	 * @return array
	 */
	public static function get_statistics($tenant_id, $period)
	{
		// Total de movimientos
		$total_movements = DB::select(DB::expr('COUNT(*) as total'))
			->from(array('accounting_entry_lines', 'ael'))
			->join(array('accounting_entries', 'ae'), 'INNER')
			->on('ael.entry_id', '=', 'ae.id')
			->where('ae.tenant_id', $tenant_id)
			->where('ae.period', $period)
			->where('ae.status', 'aplicada')
			->execute()
			->as_array();

		// Total cargos y abonos
		$totals = DB::select(
			array(DB::expr('SUM(ael.debit)'), 'total_debit'),
			array(DB::expr('SUM(ael.credit)'), 'total_credit')
		)
		->from(array('accounting_entry_lines', 'ael'))
		->join(array('accounting_entries', 'ae'), 'INNER')
		->on('ael.entry_id', '=', 'ae.id')
		->where('ae.tenant_id', $tenant_id)
		->where('ae.period', $period)
		->where('ae.status', 'aplicada')
		->execute()
		->as_array();

		// Cuentas con movimiento
		$accounts_with_movement = DB::select(DB::expr('COUNT(DISTINCT ael.account_id) as total'))
			->from(array('accounting_entry_lines', 'ael'))
			->join(array('accounting_entries', 'ae'), 'INNER')
			->on('ael.entry_id', '=', 'ae.id')
			->where('ae.tenant_id', $tenant_id)
			->where('ae.period', $period)
			->where('ae.status', 'aplicada')
			->execute()
			->as_array();

		// Pólizas aplicadas
		$applied_entries = DB::select(DB::expr('COUNT(DISTINCT ae.id) as total'))
			->from(array('accounting_entries', 'ae'))
			->where('ae.tenant_id', $tenant_id)
			->where('ae.period', $period)
			->where('ae.status', 'aplicada')
			->execute()
			->as_array();

		return array(
			'total_movements' => $total_movements[0]['total'],
			'total_debit' => $totals[0]['total_debit'] ?? 0,
			'total_credit' => $totals[0]['total_credit'] ?? 0,
			'accounts_with_movement' => $accounts_with_movement[0]['total'],
			'applied_entries' => $applied_entries[0]['total'],
		);
	}
}
