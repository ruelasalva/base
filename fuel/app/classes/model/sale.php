<?php

class Model_Sale extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"customer_id" => array(
			"label" => "Customer id",
			"data_type" => "int",
		),
		"payment_id" => array(
			"label" => "Payment id",
			"data_type" => "int",
		),
		"address_id" => array(
			"label" => "Address id",
			"data_type" => "int",
		),
		"total" => array(
			"label" => "Total",
			"data_type" => "float",
		),
		"discount" => array(
			"label" => "Discount",
			"data_type" => "float",
		),
		"transaction" => array(
			"label" => "Transaction",
			"data_type" => "varchar",
		),
		"status" => array(
			"label" => "Status",
			"data_type" => "int",
		),
        "order_id" => array(
			"label" => "Order id",
			"data_type" => "int",
		),
        "ordersap" => array(
			"label" => "Pedido Sap",
			"data_type" => "int",
		),
        "factsap" => array(
			"label" => "Factura Sap",
			"data_type" => "int",
		),
        "package_id" => array(
			"label" => "Package Id",
			"data_type" => "int",
		),
        "guide" => array(
			"label" => "Guide",
			"data_type" => "varchar",
		),
		"voucher" => array(
			"label" => "Voucher",
			"data_type" => "varchar",
		),
		"sale_date" => array(
			"label" => "Sale date",
			"data_type" => "int",
		),
		"admin_updated" => array(
			"label" => "Admin_updated",
			"data_type" => "int",
		),
		"created_at" => array(
			"label" => "Created at",
			"data_type" => "int",
		),
		"updated_at" => array(
			"label" => "Updated at",
			"data_type" => "int",
		),
    );

    /* Functions */
    public static function get_sold($request = null)
    {
        $response = Model_Sale::query();

        if(isset($request))
        {
            if(Arr::get($request, 'id_customer'))
            {
                $response = $response->where('customer_id', $request['id_customer']);
            }

            if(Arr::get($request, 'limit'))
            {
                $response = $response->limit($request['limit']);
            }
        }

        $response = $response->where('status', '>', 0)
        ->order_by('id', 'desc')
        ->get();

        return $response;
    }

    public static function get_one_sold($request)
    {
        $response = Model_Sale::query();

        if(Arr::get($request, 'id'))
        {
            $response = $response->where('id', $request['id']);
        }

        if(Arr::get($request, 'id_customer'))
        {
            $response = $response->where('customer_id', $request['id_customer']);
        }

        if(Arr::get($request, 'limit'))
        {
            $response = $response->limit($request['limit']);
        }

        $response = $response->where('status', '>', 0)
        ->get_one();

        return $response;
    }

    public static function do_update($request, $id_sale)
    {
        $response = Model_Sale::find($id_sale);
        $response->set($request);

        return ($response->save()) ? $response : false;
    }

    public static function get_last_order_not_sent($id_customer)
    {
        $response = Model_Sale::query()
        ->where('customer_id', $id_customer)
        ->where('status', 0)
        ->order_by('id', 'desc')
        ->get_one();

        return $response;
    }

    public static function set_new_order_not_sent($id_customer)
    {
        $response = new Model_Sale(array(
            'customer_id'   => $id_customer,
            'payment_id'    => 0,
            'address_id'    => 0,
            'total'         => 0,
            'discount'      => 0,
            'transaction'   => '',
            'status'        => 0,
            'ordersap'      => 0,
            'package_id'    => 0,
            'guide'         => 0,
            'voucher'       => '',
            'factsap'       => 0,
            'order_id'      => 0,
            'package_id'    => 0,
			'sale_date'     => 0,
			'admin_updated' => 0
        ));

        return ($response->save()) ? $response : false;
    }

    public static function get_last_order_purchased($id_customer)
    {
        $response = Model_Sale::query()
        ->where('customer_id', $id_customer)
        ->where('status', 1)
        ->order_by('id', 'desc')
        ->get_one();

        return $response;
    }

	public static function get_last_order_transfer($id_customer)
    {
        $response = Model_Sale::query()
        ->where('customer_id', $id_customer)
        ->where('status', 2)
        ->order_by('id', 'desc')
        ->get_one();

        return $response;
    }

	public static function check_transaction($sale_id, $transaction)
    {
        $response = Model_Sale::query()
		->where('id', $sale_id)
		->where('transaction', $transaction)
		->where('status', 0)
		->get_one();

        return $response;
    }


	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'sales';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
        'products' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Sales_Product',
            'key_to'         => 'sale_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        )
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
		'bill' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Bill',
			'key_to'         => 'sale_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'tax_data' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Sales_Tax_Datum',
			'key_to'         => 'sale_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_belongs_to = array(
        'customer' => array(
            'key_from'       => 'customer_id',
            'model_to'       => 'Model_Customer',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'payment' => array(
            'key_from'       => 'payment_id',
            'model_to'       => 'Model_Payment',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'order' => array(
            'key_from'       => 'order_id',
            'model_to'       => 'Model_Order',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'package' => array(
            'key_from'       => 'package_id',
            'model_to'       => 'Model_Package',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'address' => array(
            'key_from'       => 'address_id',
            'model_to'       => 'Model_Sales_Address',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        )
	);

	/**
	 * MÉTODOS HELPER MODERNOS
	 */

	/**
	 * Obtiene badge HTML según el estado de la venta
	 * 
	 * @return string HTML badge
	 */
	public function get_status_badge()
	{
		$badges = array(
			0 => '<span class="badge bg-secondary"><i class="fas fa-shopping-cart"></i> Carrito</span>',
			1 => '<span class="badge bg-success"><i class="fas fa-check"></i> Pagada</span>',
			2 => '<span class="badge bg-info"><i class="fas fa-exchange-alt"></i> En Transferencia</span>',
			3 => '<span class="badge bg-warning"><i class="fas fa-clock"></i> Pendiente</span>',
			4 => '<span class="badge bg-primary"><i class="fas fa-truck"></i> Enviada</span>',
			5 => '<span class="badge bg-success"><i class="fas fa-check-double"></i> Entregada</span>',
			-1 => '<span class="badge bg-danger"><i class="fas fa-times"></i> Cancelada</span>',
		);
		
		return isset($badges[$this->status]) ? $badges[$this->status] : '<span class="badge bg-light">Desconocido</span>';
	}

	/**
	 * Obtiene el nombre del estado de forma legible
	 * 
	 * @return string
	 */
	public function get_status_name()
	{
		$names = array(
			0 => 'Carrito',
			1 => 'Pagada',
			2 => 'En Transferencia',
			3 => 'Pendiente',
			4 => 'Enviada',
			5 => 'Entregada',
			-1 => 'Cancelada',
		);
		
		return isset($names[$this->status]) ? $names[$this->status] : 'Desconocido';
	}

	/**
	 * Calcula el subtotal (sin descuento)
	 * 
	 * @return float
	 */
	public function get_subtotal()
	{
		return $this->total + $this->discount;
	}

	/**
	 * Calcula el total neto (con descuento)
	 * 
	 * @return float
	 */
	public function get_total_net()
	{
		return $this->total - $this->discount;
	}

	/**
	 * Verifica si la venta puede ser editada
	 * 
	 * @return bool
	 */
	public function can_edit()
	{
		// Solo se pueden editar carritos o pendientes
		return in_array($this->status, array(0, 3));
	}

	/**
	 * Verifica si la venta puede ser cancelada
	 * 
	 * @return bool
	 */
	public function can_cancel()
	{
		// No se puede cancelar si ya está cancelada, entregada o fue pagada hace mucho
		return !in_array($this->status, array(-1, 5));
	}

	/**
	 * Verifica si la venta requiere factura
	 * 
	 * @return bool
	 */
	public function requires_invoice()
	{
		if ($this->customer && $this->customer->require_bill == 1) {
			return true;
		}
		return false;
	}

	/**
	 * Obtiene el total de items en la venta
	 * 
	 * @return int
	 */
	public function get_total_items()
	{
		$total = 0;
		foreach ($this->products as $item) {
			$total += $item->quantity;
		}
		return $total;
	}

	/**
	 * Genera código de referencia único para la venta
	 * 
	 * @return string
	 */
	public static function generate_code()
	{
		$prefix = 'VTA';
		$year_month = date('Ym');
		$code_prefix = $prefix . '-' . $year_month . '-';
		
		$last = DB::select(DB::expr('MAX(CAST(SUBSTRING(transaction, 12) AS UNSIGNED)) as last_number'))
			->from('sales')
			->where('transaction', 'LIKE', $code_prefix . '%')
			->execute()
			->current();
		
		$next_number = ($last && $last['last_number']) ? $last['last_number'] + 1 : 1;
		
		return $code_prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);
	}

	/**
	 * Registra log de cambios en la venta
	 * 
	 * @param string $action Acción realizada
	 * @param string $description Descripción del cambio
	 * @param mixed $old_value Valor anterior
	 * @param mixed $new_value Valor nuevo
	 * @return bool
	 */
	public function log_change($action, $description, $old_value = null, $new_value = null)
	{
		if (class_exists('Helper_Log')) {
			return Helper_Log::record(
				'sales',
				$action,
				$this->id,
				$description,
				$old_value,
				$new_value
			);
		}
		return false;
	}

}
