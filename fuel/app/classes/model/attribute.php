<?php

/**
 * Modelo de Atributo
 * Define atributos para filtros (color, talla, material, etc.)
 */
class Model_Attribute extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'tenant_id',
		'name',
		'slug',
		'type', // text, select, multiselect, number, boolean
		'is_filterable',
		'is_searchable',
		'sort_order',
		'is_active',
		'created_at',
		'updated_at',
		'deleted_at',
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

	protected static $_table_name = 'attributes';

	protected static $_has_many = array(
		'values' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Attribute_Value',
			'key_to' => 'attribute_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);

	/**
	 * Obtener atributos filtrables
	 */
	public static function get_filterable($tenant_id = null)
	{
		$tenant_id = $tenant_id ?: Session::get('tenant_id', 1);
		
		return static::query()
			->where('tenant_id', $tenant_id)
			->where('is_filterable', 1)
			->where('is_active', 1)
			->where('deleted_at', 'IS', null)
			->order_by('sort_order', 'asc')
			->get();
	}
}
