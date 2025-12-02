<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_GENERALES_DESCUENTOS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Generales_Descuentos extends Controller_Admin
{
	/**
	 * BEFORE
	 *
	 * @return Void
	 */
	public function before()
	{
		parent::before();
		if (!Auth::check()) {
			Session::set_flash('error', 'Debes iniciar sesión.');
			Response::redirect('admin/login');
		}
	}

	/**
	 * INDEX
	 *
	 * MUESTRA UN LISTADO DE DESCUENTOS
	 *
	 * @return Void
	 */
	public function action_index($search = '')
	{
		if (!Helper_Permission::can('catalogo_descuentos', 'view')) {
			Session::set_flash('error', 'No tienes permiso para ver descuentos.');
			Response::redirect('admin');
		}

		$data = array();
		$discounts = Model_Discount::query();

		if ($search != '') {
			$original_search = $search;
			$search = str_replace('+', ' ', rawurldecode($search));
			$search = str_replace(' ', '%', $search);
			$discounts = $discounts->where_open()
				->where(DB::expr("CONCAT(`t0`.`name`, ' ', `t0`.`structure`, ' ', `t0`.`type`)"), 'like', '%'.$search.'%')
				->where_close();
		}

		$config = array(
			'name' => 'admin',
			'pagination_url' => Uri::current(),
			'total_items' => $discounts->count(),
			'per_page' => 100,
			'uri_segment' => 'pagina',
		);

		$pagination = Pagination::forge('admin', $config);

		$data['discounts'] = $discounts->order_by('id', 'desc')
			->rows_limit($pagination->per_page)
			->rows_offset($pagination->offset)
			->get();

		$data['search'] = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		$this->template->title = 'Catálogo de Descuentos';
		$this->template->content = View::forge('admin/catalogo/generales/descuentos/index', $data,false);
	}

    /**
	 * AGREGAR
	 *
	 * AGREGA UN NUEVO DESCUENTO A LA BASE DE DATOS
	 *
	 * @return Void
	 */

	public function action_agregar()
	{
		# VERIFICAR PERMISO PARA CREAR DESCUENTOS
		if (!Helper_Permission::can('catalogo_descuentos', 'create')) {
			Session::set_flash('error', 'No tienes permiso para crear descuentos.');
			Response::redirect('admin/catalogo/generales/descuentos');
		}

		# INICIALIZAR VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('name', 'structure', 'type', 'final_effective');

		# INICIALIZAR CLASES POR CAMPO
		foreach($fields as $field)
		{
			$classes[$field] = array (
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SI SE ENVÍA FORMULARIO POST
		if (Input::method() === 'POST')
		{
			# VALIDACIÓN DE CAMPOS
			$val = Validation::forge('discount');
			$val->add_callable('Rules');
			$val->add_field('name',      'nombre',    'required|min_length[1]|max_length[100]');
			$val->add_field('structure', 'estructura','required|min_length[1]|max_length[50]');
			$val->add_field('type',      'tipo',      'required');

			# VALIDACIÓN EXITOSA
			if ($val->run())
			{
				# VALIDAR QUE EL TIPO SEA SIMPLE O COMPUESTO
				$type = $val->validated('type');
				if (!in_array($type, array('simple', 'compuesto')))
				{
					Session::set_flash('error', 'El tipo debe ser simple o compuesto.');
					$data['errors'] = array('type' => 'Tipo inválido.');
				}
				else
				{
					# CALCULAR PORCENTAJE EFECTIVO FINAL
					$structure   = $val->validated('structure');
					$percentages = array_map('floatval', explode('+', $structure));
					$effective   = 0;
					foreach ($percentages as $p) {
						$effective += (100 - $effective) * ($p / 100);
					}

					# CREAR MODELO
					$discount = Model_Discount::forge(array(
						'name'            => $val->validated('name'),
						'structure'       => $structure,
						'type'            => $type,
						'final_effective' => $effective,
						'active'          => 1, // VALOR POR DEFECTO
						'created_at'      => time(),
						'updated_at'      => time(),
						'deleted'         => 0,
					));

					# GUARDAR EN BASE DE DATOS
					if ($discount->save())
					{
						Session::set_flash('success', 'Se agregó el descuento <b>'.$val->validated('name').'</b> correctamente.');
						Response::redirect('admin/catalogo/generales/descuentos');
					}
					else
					{
						Session::set_flash('error', 'No se pudo guardar el descuento.');
					}
				}
			}
			else
			{
				# MENSAJE DE ERROR Y CLASES
				Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');
				$data['errors'] = $val->error();

				foreach ($classes as $name => $class)
				{
					$classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
					$classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
					$data[$name] = Input::post($name);
				}
			}
		}

		# CLASES A VISTA
		$data['classes'] = $classes;

		# CARGAR VISTA
		$this->template->title   = 'Agregar descuento';
		$this->template->content = View::forge('admin/catalogo/generales/descuentos/agregar', $data);
	}




	// ============================
	// INFORMACIÓN DE UN DESCUENTO
	// ============================
	/**
 * ACCIÓN PARA VER INFORMACIÓN DE UN DESCUENTO
 */
public function action_info($id = 0)
{
	# VALIDAR QUE EL ID SEA VÁLIDO
	if ($id == 0 || !is_numeric($id)) {
		Session::set_flash('error', 'ID inválido.');
		Response::redirect('admin/catalogo/generales/descuentos');
	}

	# BUSCAR EL REGISTRO DE DESCUENTO
	$discount = Model_Discount::find($id);

	# VALIDAR QUE EXISTA
	if (!$discount) {
		Session::set_flash('error', 'No se encontró el descuento especificado.');
		Response::redirect('admin/catalogo/generales/descuentos');
	}

	# CARGAR INFORMACIÓN A VARIABLES PARA LA VISTA
	$data = array(
		'id'              => $discount->id,
		'name'            => $discount->name,
		'structure'       => $discount->structure,
		'type'            => $discount->type,
		'final_effective' => $discount->final_effective,
		'active'          => $discount->active,
		'created_at'      => $discount->created_at,
		'updated_at'      => $discount->updated_at,
	);

	# CARGAR VISTA
	$this->template->title   = 'Detalle del descuento';
	$this->template->content = View::forge('admin/catalogo/generales/descuentos/info', $data);
}

	// ============================
	// EDITAR UN DESCUENTO EXISTENTE
	// ============================
	/**
	 * ACCIÓN PARA EDITAR UN DESCUENTO EXISTENTE
	 */
	public function action_editar($id = 0)
	{
		# VERIFICAR PERMISO PARA EDITAR DESCUENTOS
		if (!Helper_Permission::can('catalogo_descuentos', 'edit')) {
			Session::set_flash('error', 'No tienes permiso para editar descuentos.');
			Response::redirect('admin/catalogo/generales/descuentos');
		}

		# VALIDAR ID
		if ($id == 0 || !is_numeric($id)) {
			Session::set_flash('error', 'ID inválido.');
			Response::redirect('admin/catalogo/generales/descuentos');
		}

		# OBTENER MODELO
		$discount = Model_Discount::find($id);
		if (!$discount) {
			Session::set_flash('error', 'No se encontró el descuento solicitado.');
			Response::redirect('admin/catalogo/generales/descuentos');
		}

		# VARIABLES PARA LA VISTA
		$data    = array();
		$classes = array();
		$fields  = array('name', 'structure', 'type', 'final_effective');

		foreach($fields as $field)
		{
			$classes[$field] = array (
				'form-group'   => null,
				'form-control' => null,
			);
			$data[$field] = $discount->{$field};
		}

		# SI SE ENVÍA FORMULARIO POST
		if (Input::method() === 'POST')
		{
			$val = Validation::forge('discount');
			$val->add_callable('Rules');
			$val->add_field('name',      'nombre',    'required|min_length[1]|max_length[100]');
			$val->add_field('structure', 'estructura','required|min_length[1]|max_length[50]');
			$val->add_field('type',      'tipo',      'required');

			if ($val->run())
			{
				$type = $val->validated('type');
				if (!in_array($type, array('simple', 'compuesto')))
				{
					Session::set_flash('error', 'El tipo debe ser simple o compuesto.');
					$data['errors'] = array('type' => 'Tipo inválido.');
				}
				else
				{
					$structure   = $val->validated('structure');
					$percentages = array_map('floatval', explode('+', $structure));
					$effective   = 0;
					foreach ($percentages as $p) {
						$effective += (100 - $effective) * ($p / 100);
					}

					# ACTUALIZAR CAMPOS
					$discount->name            = $val->validated('name');
					$discount->structure       = $structure;
					$discount->type            = $type;
					$discount->final_effective = $effective;
					$discount->updated_at      = time();

					if ($discount->save())
					{
						Session::set_flash('success', 'Se actualizó el descuento <b>'.$discount->name.'</b> correctamente.');
						Response::redirect('admin/catalogo/generales/descuentos');
					}
					else
					{
						Session::set_flash('error', 'No se pudo actualizar el descuento.');
					}
				}
			}
			else
			{
				Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');
				$data['errors'] = $val->error();

				foreach ($classes as $name => $class)
				{
					$classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
					$classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
					$data[$name] = Input::post($name);
				}
			}
		}

		$data['id']      = $discount->id;
		$data['classes'] = $classes;

		$this->template->title   = 'Editar descuento';
		$this->template->content = View::forge('admin/catalogo/generales/descuentos/editar', $data);
	}

	// ============================
	// ELIMINAR UN DESCUENTO (BORRADO LÓGICO)
	// ============================
	public function action_eliminar($id = 0)
	{
		# VERIFICAR PERMISO PARA ELIMINAR
		if (!Helper_Permission::can('catalogo_descuentos', 'delete')) {
			Session::set_flash('error', 'No tienes permiso para eliminar descuentos.');
			Response::redirect('admin/catalogo/generales/descuentos');
		}

		# VALIDAR ID
		if ($id == 0 || !is_numeric($id)) {
			Session::set_flash('error', 'ID inválido.');
			Response::redirect('admin/catalogo/generales/descuentos');
		}

		# BUSCAR MODELO
		$discount = Model_Discount::find($id);
		if (!$discount) {
			Session::set_flash('error', 'No se encontró el descuento solicitado.');
			Response::redirect('admin/catalogo/generales/descuentos');
		}

		# ACTUALIZAR COMO ELIMINADO
		$discount->deleted    = 1;
		$discount->updated_at = time();

		if ($discount->save()) {
			Session::set_flash('success', 'Se eliminó correctamente el descuento <b>'.$discount->name.'</b>.');
		} else {
			Session::set_flash('error', 'No se pudo eliminar el descuento.');
		}

		Response::redirect('admin/catalogo/generales/descuentos');
	}

}