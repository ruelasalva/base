<?php

/**
 * Controlador de Productos
 * Sistema Multi-Tenant
 * 
 * @package    App
 * @category   Controller
 * @author     Sistema Base
 */
class Controller_Admin_Productos extends Controller_Admin
{
	/**
	 * Verificación de permisos antes de cualquier acción
	 */
	public function before()
	{
		parent::before();

		$action = Request::active()->action;
		$permission_map = [
			'index' => 'view',
			'create' => 'create',
			'edit' => 'edit',
			'delete' => 'delete',
			'view' => 'view'
		];

		$required_permission = isset($permission_map[$action]) ? $permission_map[$action] : 'view';

		if (!Helper_Permission::can('productos', $required_permission)) {
			Session::set_flash('error', 'No tienes permisos para realizar esta acción.');
			Response::redirect('admin');
		}
	}

	/**
	 * Listado de productos con búsqueda y paginación
	 */
	public function action_index()
	{
		$search = Input::get('search', '');
		$per_page = 25;

		$query = Model_Product::query()
			->where('deleted_at', 'IS', null);

		// Filtro de búsqueda
		if (!empty($search)) {
			$query->where_open()
				->where('name', 'like', "%{$search}%")
				->or_where('sku', 'like', "%{$search}%")
				->or_where('barcode', 'like', "%{$search}%")
				->or_where('codigo_venta', 'like', "%{$search}%")
				->or_where('codigo_compra', 'like', "%{$search}%")
				->or_where('codigo_externo', 'like', "%{$search}%")
				->or_where('brand', 'like', "%{$search}%")
				->or_where('tags', 'like', "%{$search}%")
			->where_close();
		}

		// Configuración de paginación
		$config = [
			'pagination_url' => Uri::create('admin/productos/index'),
			'total_items' => $query->count(),
			'per_page' => $per_page,
			'uri_segment' => 'page',
		];

		$pagination = Pagination::forge('productos', $config);

		$products = $query
			->order_by('id', 'desc')
			->rows_limit($pagination->per_page)
			->rows_offset($pagination->offset)
			->get();

		$this->template->title = 'Productos';
		$this->template->content = View::forge('admin/productos/index', [
			'products' => $products,
			'search' => $search,
			'pagination' => $pagination
		]);
	}

	/**
	 * Formulario para crear nuevo producto
	 */
	public function action_create()
	{
		if (Input::method() === 'POST') {
			$val = $this->_validation();

			if ($val->run()) {
				try {
					$product = new Model_Product([
						'tenant_id' => Session::get('tenant_id', 1),
						'sku' => $val->validated('sku'),
						'barcode' => $val->validated('barcode'),
						'codigo_venta' => $val->validated('codigo_venta'),
						'codigo_compra' => $val->validated('codigo_compra'),
						'codigo_externo' => $val->validated('codigo_externo'),
						'name' => $val->validated('name'),
						'slug' => $this->_generate_slug($val->validated('name')),
						'short_description' => $val->validated('short_description'),
						'description' => $val->validated('description'),
						'tags' => $val->validated('tags'),
						'category_id' => $val->validated('category_id') ?: null,
						'provider_id' => $val->validated('provider_id') ?: null,
						'brand' => $val->validated('brand'),
						'model' => $val->validated('model'),
						'unit' => $val->validated('unit'),
						'cost_price' => $val->validated('cost_price'),
						'sale_price' => $val->validated('sale_price'),
						'wholesale_price' => $val->validated('wholesale_price'),
						'min_price' => $val->validated('min_price'),
						'tax_rate' => $val->validated('tax_rate'),
						'weight' => $val->validated('weight'),
						'stock_quantity' => $val->validated('stock_quantity'),
						'min_stock' => $val->validated('min_stock'),
						'is_active' => 1,
						'is_available' => 1
					]);

					if ($product->save()) {
						// Asociar categorías si se seleccionaron
						if ($val->validated('category_id')) {
							$this->_sync_categories($product->id, [$val->validated('category_id')]);
						}

						Session::set_flash('success', 'Producto creado exitosamente.');
						Response::redirect('admin/productos');
					}
				} catch (Exception $e) {
					Session::set_flash('error', 'Error al crear el producto: ' . $e->getMessage());
				}
			} else {
				Session::set_flash('error', 'Por favor corrige los errores en el formulario.');
			}
		}

		$this->template->title = 'Nuevo Producto';
		$this->template->content = View::forge('admin/productos/form', [
			'product' => null,
			'categories' => $this->_get_categories(),
			'providers' => $this->_get_providers(),
			'validation' => isset($val) ? $val : null
		]);
	}

	/**
	 * Formulario para editar producto existente
	 */
	public function action_edit($id = null)
	{
		$product = Model_Product::find($id);

		if (!$product || $product->deleted_at) {
			Session::set_flash('error', 'Producto no encontrado.');
			Response::redirect('admin/productos');
		}

		if (Input::method() === 'POST') {
			$val = $this->_validation();

			if ($val->run()) {
				try {
					$product->sku = $val->validated('sku');
					$product->barcode = $val->validated('barcode');
					$product->codigo_venta = $val->validated('codigo_venta');
					$product->codigo_compra = $val->validated('codigo_compra');
					$product->codigo_externo = $val->validated('codigo_externo');
					$product->name = $val->validated('name');
					$product->slug = $this->_generate_slug($val->validated('name'), $id);
					$product->short_description = $val->validated('short_description');
					$product->description = $val->validated('description');
					$product->tags = $val->validated('tags');
					$product->category_id = $val->validated('category_id') ?: null;
					$product->provider_id = $val->validated('provider_id') ?: null;
					$product->brand = $val->validated('brand');
					$product->model = $val->validated('model');
					$product->unit = $val->validated('unit');
					$product->cost_price = $val->validated('cost_price');
					$product->sale_price = $val->validated('sale_price');
					$product->wholesale_price = $val->validated('wholesale_price');
					$product->min_price = $val->validated('min_price');
					$product->tax_rate = $val->validated('tax_rate');
					$product->weight = $val->validated('weight');
					$product->stock_quantity = $val->validated('stock_quantity');
					$product->min_stock = $val->validated('min_stock');

					if ($product->save()) {
						if ($val->validated('category_id')) {
							$this->_sync_categories($product->id, [$val->validated('category_id')]);
						}

						Session::set_flash('success', 'Producto actualizado exitosamente.');
						Response::redirect('admin/productos');
					}
				} catch (Exception $e) {
					Session::set_flash('error', 'Error al actualizar el producto: ' . $e->getMessage());
				}
			} else {
				Session::set_flash('error', 'Por favor corrige los errores en el formulario.');
			}
		}

		$this->template->title = 'Editar Producto';
		$this->template->content = View::forge('admin/productos/form', [
			'product' => $product,
			'categories' => $this->_get_categories(),
			'providers' => $this->_get_providers(),
			'validation' => isset($val) ? $val : null
		]);
	}

	/**
	 * Ver detalles de un producto
	 */
	public function action_view($id = null)
	{
		$product = Model_Product::find($id);

		if (!$product || $product->deleted_at) {
			Session::set_flash('error', 'Producto no encontrado.');
			Response::redirect('admin/productos');
		}

		$this->template->title = 'Detalle del Producto';
		$this->template->content = View::forge('admin/productos/view', [
			'product' => $product
		]);
	}

	/**
	 * Eliminar producto (soft delete)
	 */
	public function action_delete($id = null)
	{
		$product = Model_Product::find($id);

		if (!$product || $product->deleted_at) {
			Session::set_flash('error', 'Producto no encontrado.');
			Response::redirect('admin/productos');
		}

		try {
			$product->deleted_at = date('Y-m-d H:i:s');
			$product->is_active = 0;
			$product->is_available = 0;
			
			if ($product->save()) {
				Session::set_flash('success', 'Producto eliminado exitosamente.');
			}
		} catch (Exception $e) {
			Session::set_flash('error', 'Error al eliminar el producto: ' . $e->getMessage());
		}

		Response::redirect('admin/productos');
	}

	/**
	 * Validación del formulario
	 */
	private function _validation()
	{
		$val = Validation::forge();
		
		$val->add('sku', 'SKU')
			->add_rule('required')
			->add_rule('max_length', 50);

		$val->add('barcode', 'Código de Barras')
			->add_rule('max_length', 50);

		$val->add('codigo_venta', 'Código de Venta')
			->add_rule('max_length', 100);

		$val->add('codigo_compra', 'Código de Compra')
			->add_rule('max_length', 100);

		$val->add('codigo_externo', 'Código Externo')
			->add_rule('max_length', 100);

		$val->add('name', 'Nombre')
			->add_rule('required')
			->add_rule('max_length', 255);

		$val->add('short_description', 'Descripción Corta')
			->add_rule('max_length', 500);

		$val->add('description', 'Descripción');

		$val->add('tags', 'Tags / Palabras Clave');

		$val->add('category_id', 'Categoría')
			->add_rule('numeric_min', 0);

		$val->add('provider_id', 'Proveedor')
			->add_rule('numeric_min', 0);

		$val->add('brand', 'Marca')
			->add_rule('max_length', 100);

		$val->add('model', 'Modelo')
			->add_rule('max_length', 100);

		$val->add('unit', 'Unidad')
			->add_rule('max_length', 20);

		$val->add('cost_price', 'Precio de Costo')
			->add_rule('required')
			->add_rule('valid_string', ['numeric']);

		$val->add('sale_price', 'Precio de Venta')
			->add_rule('required')
			->add_rule('valid_string', ['numeric']);

		$val->add('wholesale_price', 'Precio Mayorista')
			->add_rule('valid_string', ['numeric']);

		$val->add('min_price', 'Precio Mínimo')
			->add_rule('valid_string', ['numeric']);

		$val->add('tax_rate', 'Tasa de Impuesto')
			->add_rule('valid_string', ['numeric']);

		$val->add('weight', 'Peso')
			->add_rule('valid_string', ['numeric']);

		$val->add('stock_quantity', 'Stock Inicial')
			->add_rule('required')
			->add_rule('numeric_min', 0);

		$val->add('min_stock', 'Stock Mínimo')
			->add_rule('numeric_min', 0);

		return $val;
	}

	/**
	 * Generar slug único
	 */
	private function _generate_slug($name, $exclude_id = null)
	{
		$slug = Inflector::friendly_title($name, '-', true);
		$original_slug = $slug;
		$counter = 1;

		while (true) {
			$query = Model_Product::query()->where('slug', $slug);
			
			if ($exclude_id) {
				$query->where('id', '!=', $exclude_id);
			}

			if (!$query->get_one()) {
				break;
			}

			$slug = $original_slug . '-' . $counter;
			$counter++;
		}

		return $slug;
	}

	/**
	 * Obtener categorías activas
	 */
	private function _get_categories()
	{
		return Model_Category::query()
			->where('deleted_at', 'IS', null)
			->where('is_active', 1)
			->order_by('name', 'asc')
			->get();
	}

	/**
	 * Obtener proveedores activos
	 */
	private function _get_providers()
	{
		return Model_Provider::query()
			->where('deleted_at', 'IS', null)
			->where('is_active', 1)
			->order_by('company_name', 'asc')
			->get();
	}

	/**
	 * Sincronizar categorías del producto
	 */
	private function _sync_categories($product_id, $category_ids)
	{
		// Eliminar asociaciones existentes
		DB::delete('product_categories')
			->where('product_id', $product_id)
			->execute();

		// Crear nuevas asociaciones
		foreach ($category_ids as $index => $category_id) {
			DB::insert('product_categories')
				->set([
					'product_id' => $product_id,
					'category_id' => $category_id,
					'is_primary' => ($index === 0) ? 1 : 0
				])
				->execute();
		}
	}
}
