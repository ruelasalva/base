<?php
/**
 * Model_AccountingAccount
 * 
 * Catálogo de Cuentas Contables
 * Soporta jerarquía (parent_id), códigos SAT, naturaleza deudora/acreedora
 * 
 * Tabla: accounting_accounts
 */
class Model_AccountingAccount extends \Orm\Model
{
	protected static $_table_name = 'accounting_accounts';
	protected static $_primary_key = array('id');

	protected static $_properties = array(
		'id' => array(
			'label' => 'ID',
			'data_type' => 'int',
		),
		'tenant_id' => array(
			'label' => 'Tenant',
			'data_type' => 'int',
		),
		'parent_id' => array(
			'label' => 'Cuenta Padre',
			'data_type' => 'int',
			'null' => true,
		),
		'account_code' => array(
			'label' => 'Código de Cuenta',
			'data_type' => 'varchar',
			'validation' => array('required', 'max_length' => array(20)),
		),
		'sat_code' => array(
			'label' => 'Código SAT',
			'data_type' => 'varchar',
			'null' => true,
		),
		'name' => array(
			'label' => 'Nombre',
			'data_type' => 'varchar',
			'validation' => array('required', 'max_length' => array(150)),
		),
		'description' => array(
			'label' => 'Descripción',
			'data_type' => 'text',
			'null' => true,
		),
		'account_type' => array(
			'label' => 'Tipo de Cuenta',
			'data_type' => 'enum',
			'options' => array('activo', 'pasivo', 'capital', 'ingresos', 'egresos', 'resultado'),
			'validation' => array('required'),
		),
		'account_subtype' => array(
			'label' => 'Subtipo',
			'data_type' => 'varchar',
			'null' => true,
		),
		'nature' => array(
			'label' => 'Naturaleza',
			'data_type' => 'enum',
			'options' => array('deudora', 'acreedora'),
			'validation' => array('required'),
		),
		'level' => array(
			'label' => 'Nivel',
			'data_type' => 'int',
			'default' => 0,
		),
		'allows_movement' => array(
			'label' => 'Permite Movimientos',
			'data_type' => 'tinyint',
			'default' => 1,
		),
		'is_active' => array(
			'label' => 'Activa',
			'data_type' => 'tinyint',
			'default' => 1,
		),
		'created_by' => array(
			'label' => 'Creado Por',
			'data_type' => 'int',
			'null' => true,
		),
		'created_at' => array(
			'label' => 'Fecha de Creación',
			'data_type' => 'datetime',
		),
		'updated_at' => array(
			'label' => 'Fecha de Actualización',
			'data_type' => 'datetime',
		),
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
	 * Relación: Cuenta padre
	 */
	protected static $_belongs_to = array(
		'parent' => array(
			'key_from' => 'parent_id',
			'model_to' => 'Model_AccountingAccount',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	/**
	 * Relación: Subcuentas
	 */
	protected static $_has_many = array(
		'children' => array(
			'key_from' => 'id',
			'model_to' => 'Model_AccountingAccount',
			'key_to' => 'parent_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	/**
	 * Obtener cuentas de mayor (nivel más alto que permiten movimientos)
	 */
	public static function get_detail_accounts($tenant_id = null)
	{
		$query = static::query()
			->where('allows_movement', 1)
			->where('is_active', 1)
			->order_by('account_code', 'ASC');

		if ($tenant_id) {
			$query->where('tenant_id', $tenant_id);
		}

		return $query->get();
	}

	/**
	 * Obtener cuentas por tipo
	 */
	public static function get_by_type($type, $tenant_id = null)
	{
		$query = static::query()
			->where('account_type', $type)
			->where('is_active', 1)
			->order_by('account_code', 'ASC');

		if ($tenant_id) {
			$query->where('tenant_id', $tenant_id);
		}

		return $query->get();
	}

	/**
	 * Validar si tiene subcuentas
	 */
	public function has_children()
	{
		return static::query()
			->where('parent_id', $this->id)
			->count() > 0;
	}

	/**
	 * Calcular el nivel en la jerarquía
	 */
	public function calculate_level()
	{
		$level = 0;
		$parent_id = $this->parent_id;

		while ($parent_id) {
			$level++;
			$parent = static::find($parent_id);
			$parent_id = $parent ? $parent->parent_id : null;
		}

		return $level;
	}
}
