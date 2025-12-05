<?php

/**
 * Model_Inventory_Product
 * 
 * Modelo para la tabla products (sistema administrativo de inventario)
 * Diferente del Model_Product existente (eCommerce)
 * 
 * @package    App
 * @subpackage Model
 */
class Model_Inventory_Product extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'tenant_id',
		'code',
		'barcode',
		'name',
		'description',
		'category_id',
		'unit_of_measure',
		'unit_price',
		'cost',
		'stock',
		'min_stock',
		'max_stock',
		'tax_rate',
		'image',
		'is_active',
		'is_service',
		'created_by',
		'updated_by',
		'created_at',
		'updated_at',
		'deleted_at'
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_Self' => array(
			'events' => array('before_insert', 'before_update', 'before_delete'),
		),
	);

	protected static $_table_name = 'inventory_products';

	protected static $_primary_key = array('id');

	protected static $_belongs_to = array(
		'category' => array(
			'key_from' => 'category_id',
			'model_to' => 'Model_Inventory_Product_Category',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'created_by_user' => array(
			'key_from' => 'created_by',
			'model_to' => 'Model_User',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'updated_by_user' => array(
			'key_from' => 'updated_by',
			'model_to' => 'Model_User',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	protected static $_has_many = array(
		'logs' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Inventory_Product_Log',
			'key_to' => 'product_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);

	/**
	 * Validación de datos
	 */
	public static function validate($factory)
	{
		$val = Validation::forge($factory);
		
		$val->add_field('code', 'Código', 'required|max_length[50]');
		$val->add_field('name', 'Nombre', 'required|max_length[255]');
		$val->add_field('unit_of_measure', 'Unidad de medida', 'required|max_length[20]');
		$val->add_field('unit_price', 'Precio unitario', 'required|numeric_min[0]');
		$val->add_field('cost', 'Costo', 'required|numeric_min[0]');
		$val->add_field('stock', 'Stock', 'numeric_min[0]');
		$val->add_field('tax_rate', 'Tasa de IVA', 'numeric_min[0]|numeric_max[100]');
		
		return $val;
	}

	/**
	 * Observer: Antes de insertar
	 */
	public function _event_before_insert()
	{
		// Asignar tenant_id actual si no está definido
		if (empty($this->tenant_id))
		{
			$this->tenant_id = Helper_User_Tenant::get_default_tenant_id();
		}

		// Asignar usuario creador
		if (empty($this->created_by) && Auth::check())
		{
			$this->created_by = Auth::get('id');
		}

		// Validar código único por tenant
		$exists = static::query()
			->where('tenant_id', $this->tenant_id)
			->where('code', $this->code)
			->where('deleted_at', null)
			->count();

		if ($exists > 0)
		{
			throw new \FuelException('El código del producto ya existe en este tenant');
		}
	}

	/**
	 * Observer: Antes de actualizar
	 */
	public function _event_before_update()
	{
		// Asignar usuario que actualiza
		if (Auth::check())
		{
			$this->updated_by = Auth::get('id');
		}

		// Registrar cambios en log
		$this->log_changes();
	}

	/**
	 * Observer: Antes de eliminar
	 */
	public function _event_before_delete()
	{
		// Soft delete
		$this->deleted_at = date('Y-m-d H:i:s');
		$this->save();

		// Log de eliminación
		Model_Inventory_Product_Log::log_action($this->id, 'deleted', 'Producto eliminado');

		// Prevenir eliminación física
		return false;
	}

	/**
	 * Registrar cambios en log
	 */
	protected function log_changes()
	{
		$changes = $this->get_diff();
		
		if (!empty($changes))
		{
			$old_values = array();
			$new_values = array();
			
			foreach ($changes as $field => $values)
			{
				$old_values[$field] = $values[0];
				$new_values[$field] = $values[1];
			}

			Model_Inventory_Product_Log::log_action(
				$this->id,
				'updated',
				'Producto actualizado',
				$old_values,
				$new_values
			);
		}
	}

	/**
	 * Obtener productos activos para select
	 * 
	 * @param  int  $tenant_id  ID del tenant (null = actual)
	 * @return array
	 */
	public static function get_for_select($tenant_id = null)
	{
		$tenant_id = $tenant_id ?: Helper_User_Tenant::get_default_tenant_id();
		
		$products = static::query()
			->where('tenant_id', $tenant_id)
			->where('is_active', 1)
			->where('deleted_at', null)
			->order_by('name', 'asc')
			->get();

		$result = array();
		foreach ($products as $product)
		{
			$result[$product->id] = $product->code . ' - ' . $product->name;
		}

		return $result;
	}

	/**
	 * Calcular margen de ganancia
	 * 
	 * @return float
	 */
	public function calculate_margin()
	{
		if ($this->unit_price == 0)
		{
			return 0;
		}

		return (($this->unit_price - $this->cost) / $this->unit_price) * 100;
	}

	/**
	 * Obtener estado del stock
	 * 
	 * @return string (ok, low, out)
	 */
	public function get_stock_status()
	{
		if ($this->is_service)
		{
			return 'service';
		}

		if ($this->stock <= 0)
		{
			return 'out';
		}

		if ($this->min_stock && $this->stock <= $this->min_stock)
		{
			return 'low';
		}

		return 'ok';
	}

	/**
	 * Obtener badge HTML del estado de stock
	 * 
	 * @return string
	 */
	public function get_stock_badge()
	{
		$status = $this->get_stock_status();
		
		$badges = array(
			'service' => '<span class="badge badge-info">Servicio</span>',
			'ok' => '<span class="badge badge-success">Stock OK</span>',
			'low' => '<span class="badge badge-warning">Stock Bajo</span>',
			'out' => '<span class="badge badge-danger">Sin Stock</span>',
		);

		return isset($badges[$status]) ? $badges[$status] : '';
	}

	/**
	 * Ajustar stock
	 * 
	 * @param  float   $quantity  Cantidad (positivo = entrada, negativo = salida)
	 * @param  string  $reason    Razón del ajuste
	 * @return bool
	 */
	public function adjust_stock($quantity, $reason = null)
	{
		$old_stock = $this->stock;
		$this->stock += $quantity;

		if ($this->stock < 0)
		{
			$this->stock = 0;
		}

		if ($this->save())
		{
			// Log del ajuste
			Model_Inventory_Product_Log::log_action(
				$this->id,
				'stock_adjusted',
				$reason ?: 'Ajuste de stock',
				array('stock' => $old_stock),
				array('stock' => $this->stock)
			);

			return true;
		}

		return false;
	}
}
