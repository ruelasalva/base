<?php
/**
 * Modulo Ejemplo - Example Model
 *
 * @package    Modulo_Ejemplo
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace Modulo_Ejemplo;

/**
 * Example Model for the Modulo Ejemplo package
 *
 * This model demonstrates a basic multi-tenant module model structure.
 */
class Model_Ejemplo extends \Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'ejemplos';

	/**
	 * @var array Properties
	 */
	protected static $_properties = array(
		'id',
		'name',
		'description',
		'created_at',
		'updated_at',
	);

	/**
	 * Get all examples
	 *
	 * @return array
	 */
	public static function get_all()
	{
		return \DB::select('*')
			->from(static::$_table_name)
			->execute()
			->as_array();
	}

	/**
	 * Get example by ID
	 *
	 * @param int $id
	 * @return array|null
	 */
	public static function get_by_id($id)
	{
		$result = \DB::select('*')
			->from(static::$_table_name)
			->where('id', '=', $id)
			->execute()
			->as_array();

		return ! empty($result) ? $result[0] : null;
	}
}
