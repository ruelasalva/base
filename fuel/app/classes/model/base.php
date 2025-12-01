<?php
/**
 * Modelo Base
 *
 * Modelo base del que heredan todos los modelos de la aplicación.
 * Proporciona funcionalidad común para todos los modelos.
 *
 * @package    app
 * @extends    Model
 */
class Model_Base extends Model
{
	/**
	 * @var  string  Nombre de la tabla (definir en modelos hijos)
	 */
	protected static $_table_name = '';

	/**
	 * @var  string  Clave primaria
	 */
	protected static $_primary_key = 'id';

	/**
	 * @var  array  Columnas que se pueden llenar
	 */
	protected static $_fillable = array();

	/**
	 * @var  array  Columnas protegidas
	 */
	protected static $_guarded = array('id', 'created_at', 'updated_at');

	/**
	 * Obtener todos los registros
	 *
	 * Nota: Sobrescribir en modelos hijos para especificar columnas si es necesario
	 *
	 * @return  array
	 */
	public static function get_all()
	{
		$columns = ! empty(static::$_fillable) ? static::$_fillable : '*';

		return DB::select_array((array) $columns)
			->from(static::$_table_name)
			->execute()
			->as_array();
	}

	/**
	 * Obtener registro por ID
	 *
	 * @param   int  $id  ID del registro
	 * @return  array|null
	 */
	public static function get_by_id($id)
	{
		$result = DB::select('*')
			->from(static::$_table_name)
			->where(static::$_primary_key, '=', $id)
			->execute()
			->as_array();

		return count($result) > 0 ? $result[0] : null;
	}

	/**
	 * Insertar un nuevo registro
	 *
	 * @param   array  $data  Datos a insertar
	 * @return  array  Array con insert_id y affected_rows
	 */
	public static function insert_record(array $data)
	{
		return DB::insert(static::$_table_name)
			->set($data)
			->execute();
	}

	/**
	 * Actualizar un registro
	 *
	 * @param   int    $id    ID del registro
	 * @param   array  $data  Datos a actualizar
	 * @return  int    Número de filas afectadas
	 */
	public static function update_record($id, array $data)
	{
		return DB::update(static::$_table_name)
			->set($data)
			->where(static::$_primary_key, '=', $id)
			->execute();
	}

	/**
	 * Eliminar un registro
	 *
	 * @param   int  $id  ID del registro
	 * @return  int  Número de filas afectadas
	 */
	public static function delete_record($id)
	{
		return DB::delete(static::$_table_name)
			->where(static::$_primary_key, '=', $id)
			->execute();
	}
}
