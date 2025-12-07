<?php

class Model_Customer extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"tenant_id" => array(
			"label" => "Tenant ID",
			"data_type" => "int",
		),
		"user_id" => array(
			"label" => "User ID",
			"data_type" => "int",
		),
		"code" => array(
			"label" => "Código",
			"data_type" => "varchar",
		),
		"customer_type" => array(
			"label" => "Tipo",
			"data_type" => "varchar",
		),
		"company_name" => array(
			"label" => "Razón Social",
			"data_type" => "varchar",
		),
		"first_name" => array(
			"label" => "Nombre",
			"data_type" => "varchar",
		),
		"last_name" => array(
			"label" => "Apellido",
			"data_type" => "varchar",
		),
		"email" => array(
			"label" => "Email",
			"data_type" => "varchar",
		),
		"phone" => array(
			"label" => "Teléfono",
			"data_type" => "varchar",
		),
		"phone_secondary" => array(
			"label" => "Teléfono 2",
			"data_type" => "varchar",
		),
		"tax_id" => array(
			"label" => "RFC",
			"data_type" => "varchar",
		),
		"credit_limit" => array(
			"label" => "Límite de Crédito",
			"data_type" => "decimal",
		),
		"balance" => array(
			"label" => "Saldo",
			"data_type" => "decimal",
		),
		"notes" => array(
			"label" => "Notas",
			"data_type" => "text",
		),
		"is_active" => array(
			"label" => "Activo",
			"data_type" => "int",
		),
		"created_at" => array(
			"label" => "Creado",
			"data_type" => "datetime",
		),
		"updated_at" => array(
			"label" => "Actualizado",
			"data_type" => "datetime",
		),
		"deleted_at" => array(
			"label" => "Eliminado",
			"data_type" => "datetime",
		),
    );

    /* Functions */
	public static function get_one($request)
	{
		$response = Model_Customer::query();

		if(Arr::get($request, 'id_user'))
		{
			$response = $response->where('user_id', $request['id_user']);
		}

		$response = $response->get_one();

		return $response;
    }

    public static function set_new_record($request)
	{
		$response = new Model_Customer($request);

		return ($response->save()) ? $response : false;
    }

    public static function do_update($request, $id)
	{
		$response = Model_Customer::find($id);
        $response->set($request);

		return ($response->save()) ? $response : false;
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

	protected static $_table_name = 'customers';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'sales' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Sale',
			'key_to'         => 'customer_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'addresses' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Customers_Address',
			'key_to'         => 'customer_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'tax_data' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Customers_Tax_Datum',
			'key_to'         => 'customer_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
		'wishlist' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Wishlist',
			'key_to'         => 'customer_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_belongs_to = array(
		'user' => array(
			'key_from'       => 'user_id',
			'model_to'       => 'Model_User',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'tenant' => array(
			'key_from'       => 'tenant_id',
			'model_to'       => 'Model_Tenant',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);
	
	/**
	 * Obtener nombre completo
	 */
	public function get_full_name()
	{
		if ($this->customer_type == 'business' && !empty($this->company_name)) {
			return $this->company_name;
		}
		return trim($this->first_name . ' ' . $this->last_name);
	}
	
	/**
	 * Badge de tipo de cliente
	 */
	public function get_type_badge()
	{
		$badges = [
			'individual' => '<span class="badge bg-info"><i class="fas fa-user me-1"></i>Individual</span>',
			'business' => '<span class="badge bg-primary"><i class="fas fa-building me-1"></i>Empresa</span>'
		];
		return $badges[$this->customer_type] ?? '<span class="badge bg-secondary">N/A</span>';
	}
	
	/**
	 * Badge de estado
	 */
	public function get_status_badge()
	{
		return $this->is_active 
			? '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Activo</span>'
			: '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Inactivo</span>';
	}
	
	/**
	 * Verificar si tiene crédito disponible
	 */
	public function has_credit_available($amount = 0)
	{
		if (empty($this->credit_limit)) {
			return false;
		}
		return ($this->balance + $amount) <= $this->credit_limit;
	}

}
