<?php

/**
 * Controller_Admin_Categorias
 * 
 * Gestión de categorías de productos con soporte para jerarquía (parent_id)
 * Incluye: listado, creación, edición, eliminación, activación/desactivación
 */
class Controller_Admin_Categorias extends Controller_Admin
{
	/**
	 * Listado de categorías
	 */
	public function action_index()
	{
		if (!Helper_Permission::can('categorias', 'view')) {
			Session::set_flash('error', 'No tienes permisos para ver categorías');
			Response::redirect('admin');
		}

		$config = array(
			'pagination_url' => Uri::create('admin/categorias/index'),
			'total_items' => Model_Category::query()->where('deleted_at', null)->count(),
			'per_page' => 20,
			'uri_segment' => 'page',
		);

		$pagination = Pagination::forge('categorias', $config);

		$categories = Model_Category::query()
			->where('deleted_at', null)
			->order_by('parent_id', 'ASC')
			->order_by('sort_order', 'ASC')
			->order_by('name', 'ASC')
			->limit($pagination->per_page)
			->offset($pagination->offset)
			->get();

	// Calcular estadísticas
	$stats = array(
		'total' => Model_Category::query()->where('deleted_at', null)->count(),
		'activas' => Model_Category::query()->where('deleted_at', null)->where('is_active', 1)->count(),
		'inactivas' => Model_Category::query()->where('deleted_at', null)->where('is_active', 0)->count(),
		'principales' => Model_Category::query()->where('deleted_at', null)->where('parent_id', null)->count(),
	);

	$this->template->title = 'Categorías de Productos';
	$this->template->content = View::forge('admin/categorias/index', array(
		'categories' => $categories,
		'pagination' => $pagination,
		'stats' => $stats,
		'can_create' => Helper_Permission::can('categorias', 'create'),
		'can_edit' => Helper_Permission::can('categorias', 'edit'),
		'can_delete' => Helper_Permission::can('categorias', 'delete'),
	), false); // false = no auto-render
}

/**
 * Crear nueva categoría
 */
public function action_create()
	{
		if (!Helper_Permission::can('categorias', 'create')) {
			Session::set_flash('error', 'No tienes permisos para crear categorías');
			Response::redirect('admin/categorias');
		}

		if (Input::method() == 'POST') {
			$val = Validation::forge();
			$val->add_field('name', 'Nombre', 'required|max_length[100]');
			$val->add_field('slug', 'Slug', 'required|max_length[100]');
			$val->add_field('parent_id', 'Categoría padre', 'valid_string[numeric]');
			$val->add_field('description', 'Descripción', 'max_length[1000]');
			$val->add_field('sort_order', 'Orden', 'valid_string[numeric]');
			$val->add_field('is_active', 'Estado', 'required');

			if ($val->run()) {
				try {
					$category = Model_Category::forge();
					$category->name = Input::post('name');
					$category->slug = Input::post('slug');
					$category->parent_id = Input::post('parent_id') ?: null;
					$category->description = Input::post('description');
					$category->sort_order = Input::post('sort_order', 0);
					$category->is_active = Input::post('is_active', 1);
					
					if ($category->save()) {
						Session::set_flash('success', 'Categoría creada exitosamente');
						Response::redirect('admin/categorias');
					}
				} catch (Exception $e) {
					Session::set_flash('error', 'Error al crear la categoría: ' . $e->getMessage());
				}
			} else {
				$errors = $val->error();
				$error_messages = array();
				foreach ($errors as $field => $error) {
					$error_messages[] = $error->get_message();
				}
				Session::set_flash('error', 'Errores en el formulario:<br>- ' . implode('<br>- ', $error_messages));
			}
		}

	// Obtener categorías principales para el dropdown
	$parent_categories = Model_Category::query()
		->where('deleted_at', null)
		->where('parent_id', null)
		->where('is_active', 1)
		->order_by('name', 'ASC')
		->get();

	$this->template->title = 'Nueva Categoría';
	$this->template->content = View::forge('admin/categorias/form', array(
		'category' => null,
		'parent_categories' => $parent_categories,
	), false);
}	/**
	 * Editar categoría existente
	 */
	public function action_edit($id = null)
	{
		if (!Helper_Permission::can('categorias', 'edit')) {
			Session::set_flash('error', 'No tienes permisos para editar categorías');
			Response::redirect('admin/categorias');
		}

		$category = Model_Category::find($id);
		if (!$category || $category->deleted_at) {
			Session::set_flash('error', 'Categoría no encontrada');
			Response::redirect('admin/categorias');
		}

		if (Input::method() == 'POST') {
			$val = Validation::forge();
			$val->add_field('name', 'Nombre', 'required|max_length[100]');
			$val->add_field('slug', 'Slug', 'required|max_length[100]');
			$val->add_field('parent_id', 'Categoría padre', 'valid_string[numeric]');
			$val->add_field('description', 'Descripción', 'max_length[1000]');
			$val->add_field('sort_order', 'Orden', 'valid_string[numeric]');
			$val->add_field('is_active', 'Estado', 'required');

			if ($val->run()) {
				try {
					$category->name = Input::post('name');
					$category->slug = Input::post('slug');
					$category->parent_id = Input::post('parent_id') ?: null;
					$category->description = Input::post('description');
					$category->sort_order = Input::post('sort_order', 0);
					$category->is_active = Input::post('is_active', 1);
					
					if ($category->save()) {
						Session::set_flash('success', 'Categoría actualizada exitosamente');
						Response::redirect('admin/categorias');
					}
				} catch (Exception $e) {
					Session::set_flash('error', 'Error al actualizar la categoría: ' . $e->getMessage());
				}
			} else {
				$errors = $val->error();
				$error_messages = array();
				foreach ($errors as $field => $error) {
					$error_messages[] = $error->get_message();
				}
				Session::set_flash('error', 'Errores en el formulario:<br>- ' . implode('<br>- ', $error_messages));
			}
		}

	// Obtener categorías principales (excluyendo la actual y sus hijos)
	$parent_categories = Model_Category::query()
		->where('deleted_at', null)
		->where('parent_id', null)
		->where('id', '!=', $id)
		->where('is_active', 1)
		->order_by('name', 'ASC')
		->get();

	$this->template->title = 'Editar Categoría';
	$this->template->content = View::forge('admin/categorias/form', array(
		'category' => $category,
		'parent_categories' => $parent_categories,
	), false);
}	/**
	 * Ver detalle de categoría
	 */
	public function action_view($id = null)
	{
		if (!Helper_Permission::can('categorias', 'view')) {
			Session::set_flash('error', 'No tienes permisos para ver categorías');
			Response::redirect('admin/categorias');
		}

		$category = Model_Category::find($id);
		if (!$category || $category->deleted_at) {
			Session::set_flash('error', 'Categoría no encontrada');
			Response::redirect('admin/categorias');
		}

		// Obtener categoría padre si existe
		$parent = null;
		if ($category->parent_id) {
			$parent = Model_Category::find($category->parent_id);
		}

		// Obtener subcategorías
		$subcategories = Model_Category::query()
			->where('parent_id', $id)
			->where('deleted_at', null)
			->order_by('sort_order', 'ASC')
			->order_by('name', 'ASC')
			->get();

	// Contar productos asociados
	$products_count = DB::select(DB::expr('COUNT(*) as total'))
		->from('products')
		->where('category_id', $id)
		->where('deleted_at', null)
		->execute()
		->get('total');

	$this->template->title = 'Detalle de Categoría';
	$this->template->content = View::forge('admin/categorias/view', array(
		'category' => $category,
		'parent' => $parent,
		'subcategories' => $subcategories,
		'products_count' => $products_count,
		'can_edit' => Helper_Permission::can('categorias', 'edit'),
		'can_delete' => Helper_Permission::can('categorias', 'delete'),
	), false);
}/**
 * Eliminar categoría (soft delete)
 */
public function action_delete($id = null)
	{
		if (!Helper_Permission::can('categorias', 'delete')) {
			Session::set_flash('error', 'No tienes permisos para eliminar categorías');
			Response::redirect('admin/categorias');
		}

		$category = Model_Category::find($id);
		if (!$category || $category->deleted_at) {
			Session::set_flash('error', 'Categoría no encontrada');
			Response::redirect('admin/categorias');
		}

		// Verificar si tiene subcategorías activas
		$subcategories_count = Model_Category::query()
			->where('parent_id', $id)
			->where('deleted_at', null)
			->count();

		if ($subcategories_count > 0) {
			Session::set_flash('error', 'No se puede eliminar la categoría porque tiene ' . $subcategories_count . ' subcategorías asociadas');
			Response::redirect('admin/categorias');
		}

		// Verificar si tiene productos asociados
		$products_count = DB::select(DB::expr('COUNT(*) as total'))
			->from('products')
			->where('category_id', $id)
			->where('deleted_at', null)
			->execute()
			->get('total');

		if ($products_count > 0) {
			Session::set_flash('error', 'No se puede eliminar la categoría porque tiene ' . $products_count . ' productos asociados');
			Response::redirect('admin/categorias');
		}

		try {
			$category->deleted_at = date('Y-m-d H:i:s');
			if ($category->save()) {
				Session::set_flash('success', 'Categoría eliminada exitosamente');
			}
		} catch (Exception $e) {
			Session::set_flash('error', 'Error al eliminar la categoría: ' . $e->getMessage());
		}

		Response::redirect('admin/categorias');
	}

	/**
	 * Cambiar estado activo/inactivo
	 */
	public function action_toggle_status($id = null)
	{
		if (!Helper_Permission::can('categorias', 'edit')) {
			Session::set_flash('error', 'No tienes permisos para cambiar el estado');
			Response::redirect('admin/categorias');
		}

		$category = Model_Category::find($id);
		if (!$category || $category->deleted_at) {
			Session::set_flash('error', 'Categoría no encontrada');
			Response::redirect('admin/categorias');
		}

		try {
			$category->is_active = $category->is_active ? 0 : 1;
			if ($category->save()) {
				$status = $category->is_active ? 'activada' : 'desactivada';
				Session::set_flash('success', 'Categoría ' . $status . ' exitosamente');
			}
		} catch (Exception $e) {
			Session::set_flash('error', 'Error al cambiar el estado: ' . $e->getMessage());
		}

		Response::redirect('admin/categorias');
	}
}
