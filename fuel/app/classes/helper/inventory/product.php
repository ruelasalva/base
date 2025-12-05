<?php

/**
 * Helper_Inventory_Product
 * 
 * Funciones auxiliares para el módulo de productos de inventario
 * 
 * @package    App
 * @subpackage Helper
 */
class Helper_Inventory_Product
{
	/**
	 * Formatear código de producto
	 * Aplica formato estándar: PROD-XXXX
	 * 
	 * @param  string  $code
	 * @return string
	 */
	public static function format_code($code)
	{
		// Remover espacios
		$code = trim($code);
		
		// Convertir a mayúsculas
		$code = strtoupper($code);
		
		// Si no tiene prefijo, agregarlo
		if (!Str::starts_with($code, 'PROD-'))
		{
			$code = 'PROD-' . $code;
		}
		
		return $code;
	}

	/**
	 * Generar código único de producto
	 * 
	 * @param  int  $tenant_id
	 * @return string
	 */
	public static function generate_code($tenant_id = null)
	{
		$tenant_id = $tenant_id ?: Helper_User_Tenant::get_default_tenant_id();
		
		// Obtener último número
		$last = DB::select(DB::expr('MAX(CAST(SUBSTRING(code, 6) AS UNSIGNED)) as last_number'))
			->from('inventory_products')
			->where('tenant_id', $tenant_id)
			->where('code', 'like', 'PROD-%')
			->execute()
			->get('last_number');
		
		$next_number = ($last ? $last : 0) + 1;
		
		return 'PROD-' . str_pad($next_number, 4, '0', STR_PAD_LEFT);
	}

	/**
	 * Obtener estado del stock con badge HTML
	 * 
	 * @param  float  $stock
	 * @param  float  $min_stock
	 * @param  bool   $is_service
	 * @return string
	 */
	public static function get_stock_badge($stock, $min_stock = null, $is_service = false)
	{
		if ($is_service)
		{
			return '<span class="badge" style="background: #17a2b8; color: white;">Servicio</span>';
		}

		if ($stock <= 0)
		{
			return '<span class="badge" style="background: #dc3545; color: white;">Sin Stock</span>';
		}

		if ($min_stock && $stock <= $min_stock)
		{
			return '<span class="badge" style="background: #ffc107; color: #000;">Stock Bajo</span>';
		}

		return '<span class="badge" style="background: #28a745; color: white;">Stock OK</span>';
	}

	/**
	 * Calcular margen de ganancia
	 * 
	 * @param  float  $unit_price
	 * @param  float  $cost
	 * @return float
	 */
	public static function calculate_margin($unit_price, $cost)
	{
		if ($unit_price == 0)
		{
			return 0;
		}

		return round((($unit_price - $cost) / $unit_price) * 100, 2);
	}

	/**
	 * Formatear precio
	 * 
	 * @param  float  $price
	 * @param  string  $currency
	 * @return string
	 */
	public static function format_price($price, $currency = 'MXN')
	{
		$symbols = array(
			'MXN' => '$',
			'USD' => '$',
			'EUR' => '€',
		);

		$symbol = isset($symbols[$currency]) ? $symbols[$currency] : '$';
		
		return $symbol . number_format($price, 2);
	}

	/**
	 * Obtener lista de unidades de medida
	 * 
	 * @return array
	 */
	public static function get_units_of_measure()
	{
		return array(
			'PZA' => 'Pieza',
			'KG' => 'Kilogramo',
			'LT' => 'Litro',
			'MT' => 'Metro',
			'M2' => 'Metro cuadrado',
			'M3' => 'Metro cúbico',
			'CJ' => 'Caja',
			'PAQ' => 'Paquete',
			'GR' => 'Gramo',
			'ML' => 'Mililitro',
		);
	}

	/**
	 * Validar existencia de stock
	 * 
	 * @param  int    $product_id
	 * @param  float  $quantity
	 * @param  int    $tenant_id
	 * @return bool
	 */
	public static function has_stock($product_id, $quantity, $tenant_id = null)
	{
		$tenant_id = $tenant_id ?: Helper_User_Tenant::get_default_tenant_id();
		
		$product = Model_Inventory_Product::query()
			->where('id', $product_id)
			->where('tenant_id', $tenant_id)
			->where('deleted_at', null)
			->get_one();

		if (!$product)
		{
			return false;
		}

		// Los servicios siempre tienen "stock"
		if ($product->is_service)
		{
			return true;
		}

		return $product->stock >= $quantity;
	}

	/**
	 * Exportar productos a CSV
	 * 
	 * @param  array  $filters
	 * @return string  Ruta del archivo generado
	 */
	public static function export_to_csv($filters = array())
	{
		$tenant_id = Helper_User_Tenant::get_default_tenant_id();
		
		$query = Model_Inventory_Product::query()
			->where('tenant_id', $tenant_id)
			->where('deleted_at', null);

		// Aplicar filtros
		if (isset($filters['category_id']) && $filters['category_id'])
		{
			$query->where('category_id', $filters['category_id']);
		}

		if (isset($filters['is_active']))
		{
			$query->where('is_active', $filters['is_active']);
		}

		$products = $query->get();

		// Crear archivo CSV
		$filename = 'productos_' . date('Y-m-d_His') . '.csv';
		$filepath = APPPATH . 'tmp/' . $filename;

		$fp = fopen($filepath, 'w');
		
		// Encabezados
		fputcsv($fp, array(
			'Código',
			'Nombre',
			'Categoría',
			'Precio',
			'Costo',
			'Stock',
			'Unidad',
			'Activo'
		));

		// Datos
		foreach ($products as $product)
		{
			fputcsv($fp, array(
				$product->code,
				$product->name,
				$product->category ? $product->category->name : 'Sin categoría',
				$product->unit_price,
				$product->cost,
				$product->stock,
				$product->unit_of_measure,
				$product->is_active ? 'Sí' : 'No'
			));
		}

		fclose($fp);

		return $filepath;
	}

	/**
	 * Obtener productos con stock bajo
	 * 
	 * @param  int  $tenant_id
	 * @return array
	 */
	public static function get_low_stock_products($tenant_id = null)
	{
		$tenant_id = $tenant_id ?: Helper_User_Tenant::get_default_tenant_id();
		
		$products = Model_Inventory_Product::query()
			->where('tenant_id', $tenant_id)
			->where('is_active', 1)
			->where('is_service', 0)
			->where('deleted_at', null)
			->where('stock', '<=', DB::expr('min_stock'))
			->where('min_stock', '>', 0)
			->get();

		return $products;
	}

	/**
	 * Obtener estadísticas de productos
	 * 
	 * @param  int  $tenant_id
	 * @return array
	 */
	public static function get_statistics($tenant_id = null)
	{
		$tenant_id = $tenant_id ?: Helper_User_Tenant::get_default_tenant_id();
		
		$stats = array();

		// Total de productos
		$stats['total'] = Model_Inventory_Product::query()
			->where('tenant_id', $tenant_id)
			->where('deleted_at', null)
			->count();

		// Productos activos
		$stats['active'] = Model_Inventory_Product::query()
			->where('tenant_id', $tenant_id)
			->where('is_active', 1)
			->where('deleted_at', null)
			->count();

		// Productos sin stock
		$stats['out_of_stock'] = Model_Inventory_Product::query()
			->where('tenant_id', $tenant_id)
			->where('is_active', 1)
			->where('is_service', 0)
			->where('stock', '<=', 0)
			->where('deleted_at', null)
			->count();

		// Productos con stock bajo
		$stats['low_stock'] = Model_Inventory_Product::query()
			->where('tenant_id', $tenant_id)
			->where('is_active', 1)
			->where('is_service', 0)
			->where('stock', '<=', DB::expr('min_stock'))
			->where('min_stock', '>', 0)
			->where('deleted_at', null)
			->count();

		// Valor total del inventario
		$result = DB::select(DB::expr('SUM(stock * cost) as total_value'))
			->from('inventory_products')
			->where('tenant_id', $tenant_id)
			->where('is_active', 1)
			->where('deleted_at', null)
			->execute()
			->current();

		$stats['inventory_value'] = $result ? $result['total_value'] : 0;

		return $stats;
	}
}
