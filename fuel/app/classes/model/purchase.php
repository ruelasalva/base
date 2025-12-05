<?php

class Model_Purchase extends \Orm\Model
{
	protected static $_properties = [
		'id',
		'code',
		'purchase_order_id',
		'provider_id',
		'invoice_number',
		'invoice_date',
		'due_date',
		'payment_date',
		'status',
		'payment_method',
		'subtotal',
		'tax',
		'total',
		'paid_amount',
		'balance',
		'notes',
		'xml_file',
		'pdf_file',
		'created_by',
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected static $_observers = [
		'Orm\Observer_CreatedAt' => [
			'events' => ['before_insert'],
			'mysql_timestamp' => false,
		],
		'Orm\Observer_UpdatedAt' => [
			'events' => ['before_save'],
			'mysql_timestamp' => false,
		],
	];

	protected static $_table_name = 'purchases';

	protected static $_belongs_to = [
		'purchase_order' => [
			'key_from' => 'purchase_order_id',
			'model_to' => 'Model_Purchaseorder',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		],
		'provider' => [
			'key_from' => 'provider_id',
			'model_to' => 'Model_Provider',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		],
		'creator' => [
			'key_from' => 'created_by',
			'model_to' => 'Model_User',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		],
	];

	/**
	 * Generar código único de factura
	 */
	public static function generate_code()
	{
		$year = date('Y');
		$month = date('m');
		
		// Obtener último código del mes
		$last = static::query()
			->where('code', 'like', "FC-{$year}{$month}%")
			->order_by('id', 'desc')
			->get_one();

		if ($last) {
			$last_number = (int)substr($last->code, -4);
			$new_number = str_pad($last_number + 1, 4, '0', STR_PAD_LEFT);
		} else {
			$new_number = '0001';
		}

		return "FC-{$year}{$month}-{$new_number}";
	}

	/**
	 * Calcular saldo pendiente
	 */
	public function calculate_balance()
	{
		$this->balance = $this->total - $this->paid_amount;
		
		// Actualizar estado automáticamente
		if ($this->balance <= 0) {
			$this->status = 'paid';
		} elseif ($this->paid_amount > 0) {
			$this->status = 'partial';
		} elseif ($this->due_date && strtotime($this->due_date) < time()) {
			$this->status = 'overdue';
		} else {
			$this->status = 'pending';
		}
		
		$this->save();
		
		return $this;
	}

	/**
	 * Marcar como pagada
	 */
	public function mark_as_paid($payment_date = null, $payment_method = null)
	{
		$this->paid_amount = $this->total;
		$this->balance = 0;
		$this->status = 'paid';
		$this->payment_date = $payment_date ?: date('Y-m-d');
		
		if ($payment_method) {
			$this->payment_method = $payment_method;
		}
		
		return $this->save();
	}

	/**
	 * Registrar pago parcial
	 */
	public function add_payment($amount, $payment_date = null, $payment_method = null)
	{
		$this->paid_amount += $amount;
		
		if ($this->paid_amount > $this->total) {
			$this->paid_amount = $this->total;
		}
		
		if ($payment_date) {
			$this->payment_date = $payment_date;
		}
		
		if ($payment_method) {
			$this->payment_method = $payment_method;
		}
		
		$this->calculate_balance();
		
		return $this->save();
	}

	/**
	 * Verificar si puede ser editada
	 */
	public function can_edit()
	{
		return in_array($this->status, ['pending', 'partial']);
	}

	/**
	 * Verificar si puede ser eliminada
	 */
	public function can_delete()
	{
		return $this->status === 'pending' && $this->paid_amount == 0;
	}

	/**
	 * Verificar si está vencida
	 */
	public function is_overdue()
	{
		if (!$this->due_date || $this->status === 'paid') {
			return false;
		}
		
		return strtotime($this->due_date) < time();
	}

	/**
	 * Obtener badge de estado
	 */
	public function get_status_badge()
	{
		$badges = [
			'pending' => '<span class="badge bg-warning">Pendiente</span>',
			'partial' => '<span class="badge bg-info">Pago Parcial</span>',
			'paid' => '<span class="badge bg-success">Pagada</span>',
			'overdue' => '<span class="badge bg-danger">Vencida</span>',
			'cancelled' => '<span class="badge bg-secondary">Cancelada</span>',
		];
		
		return $badges[$this->status] ?? '<span class="badge bg-secondary">Desconocido</span>';
	}

	/**
	 * Obtener días de vencimiento
	 */
	public function get_days_overdue()
	{
		if (!$this->due_date || $this->status === 'paid') {
			return 0;
		}
		
		$diff = time() - strtotime($this->due_date);
		return max(0, floor($diff / 86400));
	}

	/**
	 * Obtener porcentaje pagado
	 */
	public function get_payment_percentage()
	{
		if ($this->total == 0) {
			return 0;
		}
		
		return round(($this->paid_amount / $this->total) * 100, 2);
	}
}
