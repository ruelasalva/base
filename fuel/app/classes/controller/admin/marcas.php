<?php

/**
 * Controller_Admin_Marcas
 * 
 * Gestión de marcas y fabricantes
 * Incluye: listado, creación, edición, eliminación, activación/desactivación
 * 
 * Nota: La tabla brands usa 'deleted' y 'status' en lugar de 'deleted_at' e 'is_active'
 */
class Controller_Admin_Marcas extends Controller_Admin
{
	/**
	 * Listado de marcas
	 */
	public function action_index()
	{
		if (!Helper_Permission::can('marcas', 'view')) {
			Session::set_flash('error', 'No tienes permisos para ver marcas');
			Response::redirect('admin');
		}

		$config = array(
			'pagination_url' => Uri::create('admin/marcas/index'),
			'total_items' => Model_Brand::query()->where('deleted', 0)->count(),
			'per_page' => 20,
			'uri_segment' => 'page',
		);

		$pagination = Pagination::forge('marcas', $config);

		$brands = Model_Brand::query()
			->where('deleted', 0)
			->order_by('name', 'ASC')
			->limit($pagination->per_page)
			->offset($pagination->offset)
			->get();

	// Calcular estadísticas
	$stats = array(
		'total' => Model_Brand::query()->where('deleted', 0)->count(),
		'activas' => Model_Brand::query()->where('deleted', 0)->where('status', 1)->count(),
		'inactivas' => Model_Brand::query()->where('deleted', 0)->where('status', 0)->count(),
	);

	$this->template->title = 'Marcas y Fabricantes';
	$this->template->content = View::forge('admin/marcas/index', array(
		'brands' => $brands,
		'pagination' => $pagination,
		'stats' => $stats,
		'can_create' => Helper_Permission::can('marcas', 'create'),
		'can_edit' => Helper_Permission::can('marcas', 'edit'),
		'can_delete' => Helper_Permission::can('marcas', 'delete'),
	), false);
}`n`n/**
	 * Crear nueva marca
	 */
	public function action_create()
	{
		if (!Helper_Permission::can('marcas', 'create')) {
			Session::set_flash('error', 'No tienes permisos para crear marcas');
			Response::redirect('admin/marcas');
		}

		if (Input::method() == 'POST') {
			$val = Validation::forge();
			$val->add_field('name', 'Nombre', 'required|max_length[255]');
			$val->add_field('slug', 'Slug', 'required|max_length[255]');
			$val->add_field('status', 'Estado', 'required');

			if ($val->run()) {
				try {
					$brand = Model_Brand::forge();
					$brand->name = Input::post('name');
					$brand->slug = Input::post('slug');
					$brand->image = Input::post('image', '');
					$brand->status = Input::post('status', 1);
					$brand->deleted = 0;
					$brand->created_at = time();
					$brand->updated_at = time();
					
					if ($brand->save()) {
						Session::set_flash('success', 'Marca creada exitosamente');
						Response::redirect('admin/marcas');
					}
				} catch (Exception $e) {
					Session::set_flash('error', 'Error al crear la marca: ' . $e->getMessage());
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

	$this->template->title = 'Nueva Marca';
	$this->template->content = View::forge('admin/marcas/form', array(
		'brand' => null,
	), false);
}`n`n/**
	 * Editar marca existente
	 */
	public function action_edit($id = null)
	{
		if (!Helper_Permission::can('marcas', 'edit')) {
			Session::set_flash('error', 'No tienes permisos para editar marcas');
			Response::redirect('admin/marcas');
		}

		$brand = Model_Brand::find($id);
		if (!$brand || $brand->deleted == 1) {
			Session::set_flash('error', 'Marca no encontrada');
			Response::redirect('admin/marcas');
		}

		if (Input::method() == 'POST') {
			$val = Validation::forge();
			$val->add_field('name', 'Nombre', 'required|max_length[255]');
			$val->add_field('slug', 'Slug', 'required|max_length[255]');
			$val->add_field('status', 'Estado', 'required');

			if ($val->run()) {
				try {
					$brand->name = Input::post('name');
					$brand->slug = Input::post('slug');
					$brand->image = Input::post('image', '');
					$brand->status = Input::post('status', 1);
					$brand->updated_at = time();
					
					if ($brand->save()) {
						Session::set_flash('success', 'Marca actualizada exitosamente');
						Response::redirect('admin/marcas');
					}
				} catch (Exception $e) {
					Session::set_flash('error', 'Error al actualizar la marca: ' . $e->getMessage());
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

	$this->template->title = 'Editar Marca';
	$this->template->content = View::forge('admin/marcas/form', array(
		'brand' => $brand,
	), false);
}`n`n/**
	 * Ver detalle de marca
	 */
	public function action_view($id = null)
	{
		if (!Helper_Permission::can('marcas', 'view')) {
			Session::set_flash('error', 'No tienes permisos para ver marcas');
			Response::redirect('admin/marcas');
		}

		$brand = Model_Brand::find($id);
		if (!$brand || $brand->deleted == 1) {
			Session::set_flash('error', 'Marca no encontrada');
			Response::redirect('admin/marcas');
		}

	// Contar productos asociados
	$products_count = DB::select(DB::expr('COUNT(*) as total'))
		->from('products')
		->where('brand', $brand->name)
		->where('deleted_at', null)
		->execute()
		->get('total');

	$this->template->title = 'Detalle de Marca';
	$this->template->content = View::forge('admin/marcas/view', array(
		'brand' => $brand,
		'products_count' => $products_count,
		'can_edit' => Helper_Permission::can('marcas', 'edit'),
		'can_delete' => Helper_Permission::can('marcas', 'delete'),
	), false);
}`n`n/**
	 * Eliminar marca (soft delete)
	 */
	public function action_delete($id = null)
	{
		if (!Helper_Permission::can('marcas', 'delete')) {
			Session::set_flash('error', 'No tienes permisos para eliminar marcas');
			Response::redirect('admin/marcas');
		}

		$brand = Model_Brand::find($id);
		if (!$brand || $brand->deleted == 1) {
			Session::set_flash('error', 'Marca no encontrada');
			Response::redirect('admin/marcas');
		}

		// Verificar si tiene productos asociados
		$products_count = DB::select(DB::expr('COUNT(*) as total'))
			->from('products')
			->where('brand', $brand->name)
			->where('deleted_at', null)
			->execute()
			->get('total');

		if ($products_count > 0) {
			Session::set_flash('error', 'No se puede eliminar la marca porque tiene ' . $products_count . ' productos asociados');
			Response::redirect('admin/marcas');
		}

		try {
			$brand->deleted = 1;
			$brand->updated_at = time();
			if ($brand->save()) {
				Session::set_flash('success', 'Marca eliminada exitosamente');
			}
		} catch (Exception $e) {
			Session::set_flash('error', 'Error al eliminar la marca: ' . $e->getMessage());
		}

		Response::redirect('admin/marcas');
	}

	/**
	 * Cambiar estado activo/inactivo
	 */
	public function action_toggle_status($id = null)
	{
		if (!Helper_Permission::can('marcas', 'edit')) {
			Session::set_flash('error', 'No tienes permisos para cambiar el estado');
			Response::redirect('admin/marcas');
		}

		$brand = Model_Brand::find($id);
		if (!$brand || $brand->deleted == 1) {
			Session::set_flash('error', 'Marca no encontrada');
			Response::redirect('admin/marcas');
		}

		try {
			$brand->status = $brand->status ? 0 : 1;
			$brand->updated_at = time();
			if ($brand->save()) {
				$status = $brand->status ? 'activada' : 'desactivada';
				Session::set_flash('success', 'Marca ' . $status . ' exitosamente');
			}
		} catch (Exception $e) {
			Session::set_flash('error', 'Error al cambiar el estado: ' . $e->getMessage());
		}

		Response::redirect('admin/marcas');
	}
}
