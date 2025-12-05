<?php

class Model_Purchaseorder extends \Orm\Model
{
	protected static $_properties = [
		'id',
		'code',
		'provider_id',
		'order_date',
		'delivery_date',
		'status',
		'type',
		'subtotal',
		'tax',
		'total',
		'notes',
		'approved_by',
		'approved_at',
		'rejected_by',
		'rejected_at',
		'rejection_reason',
		'received_by',
		'received_at',
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

	protected static $_table_name = 'purchase_orders';

	protected static $_has_many = [
		'items' => [
			'key_from' => 'id',
			'model_to' => 'Model_Purchaseorderitem',
			'key_to' => 'purchase_order_id',
			'cascade_save' => true,
			'cascade_delete' => true,
		]
	];

	protected static $_belongs_to = [
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
		'approver' => [
			'key_from' => 'approved_by',
			'model_to' => 'Model_User',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		]
	];

	/**
	 * Generar código único de orden
	 */
	public static function generate_code()
	{
		$year = date('Y');
		$month = date('m');
		
		// Obtener último código del mes
		$last_order = static::query()
			->where('code', 'like', "OC-{$year}{$month}%")
			->order_by('id', 'desc')
			->get_one();

		if ($last_order) {
			$last_number = (int) substr($last_order->code, -4);
			$new_number = $last_number + 1;
		} else {
			$new_number = 1;
		}

		return sprintf('OC-%s%s-%04d', $year, $month, $new_number);
	}

	/**
	 * Calcular totales basados en los items
	 */
	public function calculate_totals()
	{
		$this->subtotal = 0;
		$this->tax = 0;
		$this->total = 0;

		foreach ($this->items as $item) {
			$this->subtotal += $item->subtotal;
			$this->tax += $item->tax_amount;
			$this->total += $item->total;
		}

		return $this;
	}

	/**
	 * Aprobar orden
	 */
	public function approve($user_id)
	{
		if ($this->status !== 'pending') {
			throw new Exception('Solo se pueden aprobar órdenes pendientes');
		}

		$this->status = 'approved';
		$this->approved_by = $user_id;
		$this->approved_at = date('Y-m-d H:i:s');
		
		return $this->save();
	}

	/**
	 * Rechazar orden
	 */
	public function reject($user_id, $reason)
	{
		if ($this->status !== 'pending') {
			throw new Exception('Solo se pueden rechazar órdenes pendientes');
		}

		$this->status = 'rejected';
		$this->rejected_by = $user_id;
		$this->rejected_at = date('Y-m-d H:i:s');
		$this->rejection_reason = $reason;
		
		return $this->save();
	}

	/**
	 * Marcar como recibida
	 */
	public function receive($user_id)
	{
		if ($this->status !== 'approved') {
			throw new Exception('Solo se pueden recibir órdenes aprobadas');
		}

		$this->status = 'received';
		$this->received_by = $user_id;
		$this->received_at = date('Y-m-d H:i:s');
		
		return $this->save();
	}

	/**
	 * Obtener badge de estado
	 */
	public function get_status_badge()
	{
		$badges = [
			'draft' => '<span class="badge bg-secondary">Borrador</span>',
			'pending' => '<span class="badge bg-warning">Pendiente</span>',
			'approved' => '<span class="badge bg-success">Aprobada</span>',
			'rejected' => '<span class="badge bg-danger">Rechazada</span>',
			'received' => '<span class="badge bg-info">Recibida</span>',
			'cancelled' => '<span class="badge bg-dark">Cancelada</span>',
		];

		return isset($badges[$this->status]) ? $badges[$this->status] : $this->status;
	}

	/**
	 * Obtener badge de tipo
	 */
	public function get_type_badge()
	{
		$badges = [
			'inventory' => '<span class="badge bg-primary">Almacén</span>',
			'usage' => '<span class="badge bg-info">Uso</span>',
			'service' => '<span class="badge bg-purple">Servicio</span>',
		];

		return isset($badges[$this->type]) ? $badges[$this->type] : $this->type;
	}

	/**
	 * Verificar si se puede editar
	 */
	public function can_edit()
	{
		return in_array($this->status, ['draft', 'pending']);
	}

	/**
	 * Verificar si se puede aprobar
	 */
	public function can_approve()
	{
		return $this->status === 'pending';
	}

	/**
	 * Verificar si se puede rechazar
	 */
	public function can_reject()
	{
		return $this->status === 'pending';
	}

	/**
	 * Verificar si se puede recibir
	 */
	public function can_receive()
	{
		return $this->status === 'approved';
	}
}
