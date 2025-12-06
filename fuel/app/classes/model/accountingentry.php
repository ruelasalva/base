<?php
/**
 * Model_AccountingEntry
 * 
 * Pólizas Contables (Journal Entries)
 * Sistema de partida doble: total_debit = total_credit
 * 
 * Tabla: accounting_entries
 */
class Model_AccountingEntry extends \Orm\Model
{
	protected static $_table_name = 'accounting_entries';
	protected static $_primary_key = array('id');

	protected static $_properties = array(
		'id',
		'tenant_id',
		'entry_number',
		'entry_type',
		'entry_date',
		'created_date',
		'period',
		'fiscal_year',
		'concept',
		'reference',
		'total_debit',
		'total_credit',
		'status',
		'is_balanced',
		'created_by',
		'applied_by',
		'applied_at',
		'cancelled_by',
		'cancelled_at',
		'cancellation_reason',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => true,
		),
	);

	/**
	 * Relación: Líneas de la póliza (movimientos)
	 */
	protected static $_has_many = array(
		'lines' => array(
			'key_from' => 'id',
			'model_to' => 'Model_AccountingEntryLine',
			'key_to' => 'entry_id',
			'cascade_save' => true,
			'cascade_delete' => true,
		),
	);

	/**
	 * Generar folio único para la póliza
	 */
	public static function generate_entry_number($tenant_id, $type, $period)
	{
		$prefix = strtoupper(substr($type, 0, 1)); // I, E, D, A, J, C
		$year = substr($period, 0, 4);
		$month = substr($period, 5, 2);

		// Obtener último número del periodo
		$last = static::query()
			->where('tenant_id', $tenant_id)
			->where('entry_type', $type)
			->where('period', $period)
			->order_by('id', 'DESC')
			->get_one();

		$number = 1;
		if ($last && preg_match('/(\d+)$/', $last->entry_number, $matches)) {
			$number = intval($matches[1]) + 1;
		}

		return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $number);
	}

	/**
	 * Validar que esté balanceada (cargos = abonos)
	 */
	public function validate_balance()
	{
		$this->is_balanced = (abs($this->total_debit - $this->total_credit) < 0.01);
		return $this->is_balanced;
	}

	/**
	 * Recalcular totales desde las líneas
	 */
	public function recalculate_totals()
	{
		$lines = Model_AccountingEntryLine::query()
			->where('entry_id', $this->id)
			->get();

		$this->total_debit = 0;
		$this->total_credit = 0;

		foreach ($lines as $line) {
			$this->total_debit += $line->debit;
			$this->total_credit += $line->credit;
		}

		$this->validate_balance();
	}

	/**
	 * Aplicar póliza (cambiar a estado "aplicada")
	 */
	public function apply($user_id)
	{
		if ($this->status != 'borrador') {
			throw new Exception('Solo se pueden aplicar pólizas en borrador');
		}

		if (!$this->is_balanced) {
			throw new Exception('La póliza no está balanceada');
		}

		$this->status = 'aplicada';
		$this->applied_by = $user_id;
		$this->applied_at = date('Y-m-d H:i:s');

		return $this->save();
	}

	/**
	 * Cancelar póliza
	 */
	public function cancel($user_id, $reason)
	{
		if ($this->status == 'cancelada') {
			throw new Exception('La póliza ya está cancelada');
		}

		$this->status = 'cancelada';
		$this->cancelled_by = $user_id;
		$this->cancelled_at = date('Y-m-d H:i:s');
		$this->cancellation_reason = $reason;

		return $this->save();
	}

	/**
	 * Obtener pólizas por periodo
	 */
	public static function get_by_period($tenant_id, $period)
	{
		return static::query()
			->where('tenant_id', $tenant_id)
			->where('period', $period)
			->order_by('entry_date', 'DESC')
			->order_by('entry_number', 'DESC')
			->get();
	}
}
