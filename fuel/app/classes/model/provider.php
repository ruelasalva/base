<?php

class Model_Provider extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'code',
		'company_name',
		'contact_name',
		'email',
		'phone',
		'phone_secondary',
		'address',
		'city',
		'state',
		'postal_code',
		'country',
		'tax_id',
		'website',
		'notes',
		'payment_terms',
		'credit_limit',
		'is_active',
		'created_at',
		'updated_at',
		'deleted_at',
		'is_suspended',
		'suspended_reason',
		'suspended_at',
		'activated_at',
		'activated_by'
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events'          => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events'          => array('before_save'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'providers';

	public static function get_for_input()
{
    $arr = [];
    $proveedores = static::query()->order_by('company_name', 'asc')->get();
    foreach ($proveedores as $p) {
        $arr[$p->id] = $p->company_name . ' (' . $p->tax_id . ')';
    }
    return $arr;
}


	protected static $_primary_key = array('id');

	// Relación con usuario que activó el proveedor
	protected static $_belongs_to = array(
		'user' => array(
			'key_from' => 'activated_by',
			'model_to' => 'Model_User',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	protected static $_has_one = array(
	
	'provider_tax_datum' => array(
		'model_to' => 'Model_Providers_Tax_Datum',
		'key_from' => 'id',
		'key_to' => 'provider_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	),

	'provider_delivery' => array(
		'model_to' => 'Model_Providers_Delivery',
		'key_from' => 'id',
		'key_to' => 'provider_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	),

	'provider_purchase' => array(
		'model_to' => 'Model_Providers_Purchase',
		'key_from' => 'id',
		'key_to' => 'provider_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	),

	'provider_account' => array(
		'model_to' => 'Model_Providers_Account',
		'key_from' => 'id',
		'key_to' => 'provider_id',
		'cascade_save' => true,
		'cascade_delete' => false,
	),
	'payment_term' => array(
		'model_to' => 'Model_Payments_Term',
		'key_from' => 'payment_terms',
		'key_to' => 'id',
		'cascade_save' => false,
		'cascade_delete' => false,
	)
	
);

	protected static $_has_many = array(
		'identities' => array(
			'model_to' => 'Model_User_Identity',
			'key_from' => 'id',
			'key_to' => 'identity_id',
			'conditions' => array(
				'where' => array(
					array('identity_type', '=', 'provider')
				)
			),
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'departments' => array(
			'model_to' => 'Model_Provider_Department',
			'key_from' => 'id',
			'key_to' => 'provider_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	/**
	 * Obtiene el usuario asociado a este proveedor (si existe)
	 * 
	 * @return Model_User|null
	 */
	public function get_user()
	{
		$identity = Model_User_Identity::query()
			->related('user')
			->where('identity_type', 'provider')
			->where('identity_id', $this->id)
			->get_one();
		
		return $identity ? $identity->user : null;
	}

	/**
	 * Verifica si este proveedor tiene acceso al portal
	 * 
	 * @return bool
	 */
	public function has_portal_access()
	{
		$identity = Model_User_Identity::query()
			->where('identity_type', 'provider')
			->where('identity_id', $this->id)
			->where('can_login', 1)
			->get_one();
		
		return !is_null($identity);
	}

	/**
	 * Obtiene el departamento principal que surte este proveedor
	 * 
	 * @return Model_Provider_Department|null
	 */
	public function get_primary_department()
	{
		return Model_Provider_Department::get_primary($this->id);
	}

	/**
	 * Obtiene todos los departamentos activos que surte este proveedor
	 * 
	 * @return array
	 */
	public function get_active_departments()
	{
		return Model_Provider_Department::get_active_departments($this->id);
	}

}
