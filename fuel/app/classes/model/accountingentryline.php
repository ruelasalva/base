<?php
/**
 * Model_AccountingEntryLine
 * 
 * Líneas/Partidas de Pólizas Contables
 * Cada línea tiene un cargo (debit) O un abono (credit), nunca ambos
 * 
 * Tabla: accounting_entry_lines
 */
class Model_AccountingEntryLine extends \Orm\Model
{
	protected static $_table_name = 'accounting_entry_lines';
	protected static $_primary_key = array('id');

	protected static $_properties = array(
		'id',
		'entry_id',
		'line_number',
		'account_id',
		'description',
		'debit',
		'credit',
		'reference',
		'created_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => true,
		),
	);

	/**
	 * Relación: Póliza padre
	 */
	protected static $_belongs_to = array(
		'entry' => array(
			'key_from' => 'entry_id',
			'model_to' => 'Model_AccountingEntry',
			'key_to' => 'id',
		),
		'account' => array(
			'key_from' => 'account_id',
			'model_to' => 'Model_AccountingAccount',
			'key_to' => 'id',
		),
	);

	/**
	 * Validar que no tenga cargo y abono al mismo tiempo
	 */
	public function _validation_no_debit_and_credit($value)
	{
		if ($this->debit > 0 && $this->credit > 0) {
			throw new Validation_Error('Una partida no puede tener cargo y abono al mismo tiempo');
		}

		if ($this->debit == 0 && $this->credit == 0) {
			throw new Validation_Error('La partida debe tener cargo o abono');
		}

		return true;
	}
}
