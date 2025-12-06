<?php

/**
 * Controller_Admin_Cuentascontables
 * 
 * Gestión del Catálogo de Cuentas Contables
 * Soporta jerarquía, códigos SAT, naturaleza contable
 */
class Controller_Admin_Cuentascontables extends Controller_Admin
{
	/**
	 * Listado de cuentas contables
	 */
	public function action_index()
	{
		if (!Helper_Permission::can('cuentas_contables', 'view')) {
			Session::set_flash('error', 'No tienes permisos para ver cuentas contables');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Filtros
		$search = Input::get('search', '');
		$type_filter = Input::get('type', '');
		$nature_filter = Input::get('nature', '');

		$query = Model_AccountingAccount::query()
			->where('tenant_id', $tenant_id);

		if ($search) {
			$query->and_where_open()
				->where('account_code', 'LIKE', '%' . $search . '%')
				->or_where('name', 'LIKE', '%' . $search . '%')
				->or_where('sat_code', 'LIKE', '%' . $search . '%')
				->and_where_close();
		}

		if ($type_filter) {
			$query->where('account_type', $type_filter);
		}

		if ($nature_filter) {
			$query->where('nature', $nature_filter);
		}

		$config = array(
			'pagination_url' => Uri::create('admin/cuentascontables/index'),
			'total_items' => $query->count(),
			'per_page' => 25,
			'uri_segment' => 'page',
		);

		$pagination = Pagination::forge('cuentascontables', $config);

		$accounts = $query
			->order_by('account_code', 'ASC')
			->limit($pagination->per_page)
			->offset($pagination->offset)
			->get();

		// Estadísticas
		$stats = array(
			'total' => Model_AccountingAccount::query()->where('tenant_id', $tenant_id)->count(),
			'activas' => Model_AccountingAccount::query()->where('tenant_id', $tenant_id)->where('is_active', 1)->count(),
			'inactivas' => Model_AccountingAccount::query()->where('tenant_id', $tenant_id)->where('is_active', 0)->count(),
			'con_movimientos' => Model_AccountingAccount::query()->where('tenant_id', $tenant_id)->where('allows_movement', 1)->count(),
			'activos' => Model_AccountingAccount::query()->where('tenant_id', $tenant_id)->where('account_type', 'activo')->count(),
			'pasivos' => Model_AccountingAccount::query()->where('tenant_id', $tenant_id)->where('account_type', 'pasivo')->count(),
			'capital' => Model_AccountingAccount::query()->where('tenant_id', $tenant_id)->where('account_type', 'capital')->count(),
			'ingresos' => Model_AccountingAccount::query()->where('tenant_id', $tenant_id)->where('account_type', 'ingresos')->count(),
			'egresos' => Model_AccountingAccount::query()->where('tenant_id', $tenant_id)->where('account_type', 'egresos')->count(),
		);

		$this->template->title = 'Catálogo de Cuentas Contables';
		$this->template->content = View::forge('admin/cuentascontables/index', array(
			'accounts' => $accounts,
			'pagination' => $pagination,
			'stats' => $stats,
			'search' => $search,
			'type_filter' => $type_filter,
			'nature_filter' => $nature_filter,
			'can_create' => Helper_Permission::can('cuentas_contables', 'create'),
			'can_edit' => Helper_Permission::can('cuentas_contables', 'edit'),
			'can_delete' => Helper_Permission::can('cuentas_contables', 'delete'),
		), false);
	}

	/**
	 * Crear nueva cuenta contable
	 */
	public function action_create()
	{
		if (!Helper_Permission::can('cuentas_contables', 'create')) {
			Session::set_flash('error', 'No tienes permisos para crear cuentas contables');
			Response::redirect('admin/cuentascontables');
		}

		$tenant_id = Session::get('tenant_id', 1);

		if (Input::method() == 'POST') {
			$val = Validation::forge();
			$val->add_field('account_code', 'Código de Cuenta', 'required|max_length[20]');
			$val->add_field('name', 'Nombre', 'required|max_length[150]');
			$val->add_field('account_type', 'Tipo de Cuenta', 'required');
			$val->add_field('nature', 'Naturaleza', 'required');

			if ($val->run()) {
				try {
					// Verificar código duplicado
					$exists = Model_AccountingAccount::query()
						->where('tenant_id', $tenant_id)
						->where('account_code', Input::post('account_code'))
						->count();

					if ($exists > 0) {
						Session::set_flash('error', 'Ya existe una cuenta con ese código');
					} else {
						$account = Model_AccountingAccount::forge();
						$account->tenant_id = $tenant_id;
						$account->parent_id = Input::post('parent_id') ?: null;
						$account->account_code = Input::post('account_code');
						$account->sat_code = Input::post('sat_code') ?: null;
						$account->name = Input::post('name');
						$account->description = Input::post('description');
						$account->account_type = Input::post('account_type');
						$account->account_subtype = Input::post('account_subtype');
						$account->nature = Input::post('nature');
						$account->allows_movement = Input::post('allows_movement', 1);
						$account->is_active = Input::post('is_active', 1);
						$account->created_by = Auth::get('id');

						// Calcular nivel
						$account->level = 0;
						if ($account->parent_id) {
							$parent = Model_AccountingAccount::find($account->parent_id);
							$account->level = $parent ? $parent->level + 1 : 0;
						}

						if ($account->save()) {
							Session::set_flash('success', 'Cuenta contable creada exitosamente');
							Response::redirect('admin/cuentascontables');
						}
					}
				} catch (Exception $e) {
					Session::set_flash('error', 'Error al crear la cuenta: ' . $e->getMessage());
				}
			} else {
				$errors = $val->error();
				$error_messages = array();
				foreach ($errors as $field => $error) {
					$error_messages[] = $error->get_message();
				}
				Session::set_flash('error', 'Errores:<br>- ' . implode('<br>- ', $error_messages));
			}
		}

		// Obtener cuentas padre (solo las que permiten hijos)
		$parent_accounts = Model_AccountingAccount::query()
			->where('tenant_id', $tenant_id)
			->where('is_active', 1)
			->order_by('account_code', 'ASC')
			->get();

		$this->template->title = 'Nueva Cuenta Contable';
		$this->template->content = View::forge('admin/cuentascontables/form', array(
			'account' => null,
			'parent_accounts' => $parent_accounts,
		), false);
	}

	/**
	 * Editar cuenta contable
	 */
	public function action_edit($id = null)
	{
		if (!Helper_Permission::can('cuentas_contables', 'edit')) {
			Session::set_flash('error', 'No tienes permisos para editar cuentas contables');
			Response::redirect('admin/cuentascontables');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$account = Model_AccountingAccount::find($id);

		if (!$account || $account->tenant_id != $tenant_id) {
			Session::set_flash('error', 'Cuenta no encontrada');
			Response::redirect('admin/cuentascontables');
		}

		if (Input::method() == 'POST') {
			$val = Validation::forge();
			$val->add_field('account_code', 'Código de Cuenta', 'required|max_length[20]');
			$val->add_field('name', 'Nombre', 'required|max_length[150]');
			$val->add_field('account_type', 'Tipo de Cuenta', 'required');
			$val->add_field('nature', 'Naturaleza', 'required');

			if ($val->run()) {
				try {
					// Verificar código duplicado
					$exists = Model_AccountingAccount::query()
						->where('tenant_id', $tenant_id)
						->where('account_code', Input::post('account_code'))
						->where('id', '!=', $id)
						->count();

					if ($exists > 0) {
						Session::set_flash('error', 'Ya existe otra cuenta con ese código');
					} else {
						// Validar que no se asigne como padre a sí misma o a sus hijos
						$parent_id = Input::post('parent_id');
						if ($parent_id == $id) {
							Session::set_flash('error', 'Una cuenta no puede ser su propio padre');
						} else {
							$account->parent_id = $parent_id ?: null;
							$account->account_code = Input::post('account_code');
							$account->sat_code = Input::post('sat_code') ?: null;
							$account->name = Input::post('name');
							$account->description = Input::post('description');
							$account->account_type = Input::post('account_type');
							$account->account_subtype = Input::post('account_subtype');
							$account->nature = Input::post('nature');
							$account->allows_movement = Input::post('allows_movement', 1);
							$account->is_active = Input::post('is_active', 1);

							// Recalcular nivel
							$account->level = $account->calculate_level();

							if ($account->save()) {
								Session::set_flash('success', 'Cuenta actualizada exitosamente');
								Response::redirect('admin/cuentascontables');
							}
						}
					}
				} catch (Exception $e) {
					Session::set_flash('error', 'Error al actualizar: ' . $e->getMessage());
				}
			} else {
				$errors = $val->error();
				$error_messages = array();
				foreach ($errors as $field => $error) {
					$error_messages[] = $error->get_message();
				}
				Session::set_flash('error', 'Errores:<br>- ' . implode('<br>- ', $error_messages));
			}
		}

		// Obtener cuentas padre (excluir la actual y sus hijos)
		$parent_accounts = Model_AccountingAccount::query()
			->where('tenant_id', $tenant_id)
			->where('id', '!=', $id)
			->where('is_active', 1)
			->order_by('account_code', 'ASC')
			->get();

		$this->template->title = 'Editar Cuenta Contable';
		$this->template->content = View::forge('admin/cuentascontables/form', array(
			'account' => $account,
			'parent_accounts' => $parent_accounts,
		), false);
	}

	/**
	 * Ver detalle de cuenta
	 */
	public function action_view($id = null)
	{
		if (!Helper_Permission::can('cuentas_contables', 'view')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin/cuentascontables');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$account = Model_AccountingAccount::find($id);

		if (!$account || $account->tenant_id != $tenant_id) {
			Session::set_flash('error', 'Cuenta no encontrada');
			Response::redirect('admin/cuentascontables');
		}

		// Obtener cuenta padre
		$parent = null;
		if ($account->parent_id) {
			$parent = Model_AccountingAccount::find($account->parent_id);
		}

		// Obtener subcuentas
		$children = Model_AccountingAccount::query()
			->where('parent_id', $id)
			->order_by('account_code', 'ASC')
			->get();

		$this->template->title = 'Detalle de Cuenta Contable';
		$this->template->content = View::forge('admin/cuentascontables/view', array(
			'account' => $account,
			'parent' => $parent,
			'children' => $children,
			'can_edit' => Helper_Permission::can('cuentas_contables', 'edit'),
			'can_delete' => Helper_Permission::can('cuentas_contables', 'delete'),
		), false);
	}

	/**
	 * Eliminar cuenta (solo si no tiene subcuentas ni movimientos)
	 */
	public function action_delete($id = null)
	{
		if (!Helper_Permission::can('cuentas_contables', 'delete')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin/cuentascontables');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$account = Model_AccountingAccount::find($id);

		if (!$account || $account->tenant_id != $tenant_id) {
			Session::set_flash('error', 'Cuenta no encontrada');
			Response::redirect('admin/cuentascontables');
		}

		// Verificar subcuentas
		if ($account->has_children()) {
			Session::set_flash('error', 'No se puede eliminar: tiene subcuentas asociadas');
			Response::redirect('admin/cuentascontables');
		}

		try {
			$account->delete();
			Session::set_flash('success', 'Cuenta eliminada exitosamente');
		} catch (Exception $e) {
			Session::set_flash('error', 'Error al eliminar: ' . $e->getMessage());
		}

		Response::redirect('admin/cuentascontables');
	}

	/**
	 * Cambiar estado activo/inactivo
	 */
	public function action_toggle_status($id = null)
	{
		if (!Helper_Permission::can('cuentas_contables', 'edit')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin/cuentascontables');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$account = Model_AccountingAccount::find($id);

		if (!$account || $account->tenant_id != $tenant_id) {
			Session::set_flash('error', 'Cuenta no encontrada');
			Response::redirect('admin/cuentascontables');
		}

		try {
			$account->is_active = $account->is_active ? 0 : 1;
			if ($account->save()) {
				$status = $account->is_active ? 'activada' : 'desactivada';
				Session::set_flash('success', 'Cuenta ' . $status . ' exitosamente');
			}
		} catch (Exception $e) {
			Session::set_flash('error', 'Error: ' . $e->getMessage());
		}

		Response::redirect('admin/cuentascontables');
	}
}
