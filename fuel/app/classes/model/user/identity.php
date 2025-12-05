<?php

/**
 * Model_User_Identity
 * 
 * Tabla pivot polimórfica que relaciona usuarios con sus diferentes identidades
 * (empleado, proveedor, cliente, partner)
 */
class Model_User_Identity extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'user_id',
		'identity_type',
		'identity_id',
		'is_primary',
		'can_login',
		'access_level',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'user_identities';

	protected static $_belongs_to = array(
		'user' => array(
			'key_from' => 'user_id',
			'model_to' => 'Model_User',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	/**
	 * Obtiene la entidad específica según el tipo de identidad
	 * 
	 * @return mixed Model_Employee|Model_Provider|Model_Customer|null
	 */
	public function get_identity()
	{
		switch($this->identity_type) {
			case 'employee':
				return Model_Employee::find($this->identity_id);
			
			case 'provider':
				return Model_Provider::find($this->identity_id);
			
			case 'customer':
				return Model_Customer::find($this->identity_id);
			
			case 'partner':
				// Si implementas partners en el futuro
				return null;
			
			default:
				return null;
		}
	}

	/**
	 * Obtiene todas las identidades de un usuario
	 * 
	 * @param int $user_id
	 * @return array
	 */
	public static function get_user_identities($user_id)
	{
		return static::query()
			->where('user_id', $user_id)
			->order_by('is_primary', 'desc')
			->get();
	}

	/**
	 * Obtiene la identidad principal de un usuario
	 * 
	 * @param int $user_id
	 * @return Model_User_Identity|null
	 */
	public static function get_primary_identity($user_id)
	{
		return static::query()
			->where('user_id', $user_id)
			->where('is_primary', 1)
			->get_one();
	}

	/**
	 * Verifica si un usuario tiene una identidad específica
	 * 
	 * @param int $user_id
	 * @param string $type 'employee'|'provider'|'customer'|'partner'
	 * @param int $entity_id
	 * @return bool
	 */
	public static function has_identity($user_id, $type, $entity_id)
	{
		$count = static::query()
			->where('user_id', $user_id)
			->where('identity_type', $type)
			->where('identity_id', $entity_id)
			->count();
		
		return $count > 0;
	}

	/**
	 * Crea una nueva identidad para un usuario
	 * 
	 * @param int $user_id
	 * @param string $type
	 * @param int $entity_id
	 * @param bool $is_primary
	 * @param bool $can_login
	 * @param string $access_level
	 * @return Model_User_Identity
	 */
	public static function create_identity($user_id, $type, $entity_id, $is_primary = false, $can_login = true, $access_level = 'full')
	{
		// Si es primary, quitar primary a las demás
		if ($is_primary) {
			\DB::update('user_identities')
				->set(array('is_primary' => 0))
				->where('user_id', $user_id)
				->execute();
		}

		$identity = static::forge(array(
			'user_id' => $user_id,
			'identity_type' => $type,
			'identity_id' => $entity_id,
			'is_primary' => $is_primary ? 1 : 0,
			'can_login' => $can_login ? 1 : 0,
			'access_level' => $access_level,
		));

		$identity->save();

		return $identity;
	}
}
