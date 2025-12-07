<?php

/**
 * Controlador de Puestos
 * Sistema Multi-Tenant
 * 
 * @package    App
 * @category   Controller
 * @author     Sistema Base
 */
class Controller_Admin_Puestos extends Controller_Admin
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

		if (!Helper_Permission::can('puestos', $required_permission)) {
			Session::set_flash('error', 'No tienes permisos para realizar esta acción.');
			Response::redirect('admin');
		}
	}

	/**
	 * Listado de puestos
	 */
	public function action_index()
	{
		$search = Input::get('search', '');

		$query = Model_Position::query();

		if (!empty($search)) {
			$query->where_open()
				->where('name', 'like', "%{$search}%")
				->or_where('code', 'like', "%{$search}%")
			->where_close();
		}

		$positions = $query->order_by('name', 'asc')->get();

		$this->template->title = 'Puestos';
		$this->template->content = View::forge('admin/puestos/index', [
			'positions' => $positions,
			'search' => $search
		]);
	}

	/**
	 * Crear nuevo puesto
	 */
	public function action_create()
	{
		if (Input::method() === 'POST') {
			$val = $this->_validation();

			if ($val->run()) {
				try {
					$position = new Model_Position([
						'tenant_id' => Session::get('tenant_id', 1),
						'name' => $val->validated('name'),
						'code' => $val->validated('code'),
						'description' => $val->validated('description'),
						'salary_min' => $val->validated('salary_min'),
						'salary_max' => $val->validated('salary_max'),
						'is_active' => 1
					]);

					if ($position->save()) {
						Helper_Log::record(
							'puestos',
							'create',
							$position->id,
							'Puesto creado: ' . $position->name,
							null,
							$position->to_array()
						);

						Session::set_flash('success', 'Puesto creado exitosamente.');
						Response::redirect('admin/puestos');
					}
				} catch (Exception $e) {
					\Log::error('Error al crear puesto: ' . $e->getMessage());
					Session::set_flash('error', 'Error al crear el puesto: ' . $e->getMessage());
				}
			} else {
				Session::set_flash('error', 'Por favor corrige los errores en el formulario.');
			}
		}

		$this->template->title = 'Nuevo Puesto';
		$this->template->content = View::forge('admin/puestos/create');
	}

	/**
	 * Ver detalle de puesto
	 */
	public function action_view($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de puesto requerido.');
			Response::redirect('admin/puestos');
		}

		$position = Model_Position::find($id);

		if (!$position) {
			Session::set_flash('error', 'Puesto no encontrado.');
			Response::redirect('admin/puestos');
		}

		// Obtener logs del puesto
		$logs = DB::select('*')
			->from('audit_logs')
			->where('module', 'puestos')
			->where('record_id', $id)
			->order_by('created_at', 'desc')
			->limit(20)
			->execute()
			->as_array();

		$this->template->title = 'Puesto: ' . $position->name;
		$this->template->content = View::forge('admin/puestos/view', [
			'position' => $position,
			'logs' => $logs
		]);
	}

	/**
	 * Editar puesto
	 */
	public function action_edit($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de puesto requerido.');
			Response::redirect('admin/puestos');
		}

		$position = Model_Position::find($id);

		if (!$position) {
			Session::set_flash('error', 'Puesto no encontrado.');
			Response::redirect('admin/puestos');
		}

		if (Input::method() === 'POST') {
			$val = $this->_validation();

			if ($val->run()) {
				try {
					$old_data = $position->to_array();

					$position->name = $val->validated('name');
					$position->code = $val->validated('code');
					$position->description = $val->validated('description');
					$position->salary_min = $val->validated('salary_min');
					$position->salary_max = $val->validated('salary_max');
					$position->is_active = Input::post('is_active', 1);

					if ($position->save()) {
						Helper_Log::record(
							'puestos',
							'edit',
							$position->id,
							'Puesto actualizado: ' . $position->name,
							$old_data,
							$position->to_array()
						);

						Session::set_flash('success', 'Puesto actualizado exitosamente.');
						Response::redirect('admin/puestos');
					}
				} catch (Exception $e) {
					\Log::error('Error al actualizar puesto: ' . $e->getMessage());
					Session::set_flash('error', 'Error al actualizar el puesto: ' . $e->getMessage());
				}
			} else {
				Session::set_flash('error', 'Por favor corrige los errores en el formulario.');
			}
		}

		$this->template->title = 'Editar Puesto';
		$this->template->content = View::forge('admin/puestos/edit', [
			'position' => $position
		]);
	}

	/**
	 * Eliminar puesto
	 */
	public function action_delete($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de puesto requerido.');
			Response::redirect('admin/puestos');
		}

		$position = Model_Position::find($id);

		if (!$position) {
			Session::set_flash('error', 'Puesto no encontrado.');
			Response::redirect('admin/puestos');
		}

		// Verificar si tiene empleados asignados
		$employee_count = $position->count_active_employees();
		if ($employee_count > 0) {
			Session::set_flash('error', 'No se puede eliminar el puesto porque tiene ' . $employee_count . ' empleado(s) asignado(s).');
			Response::redirect('admin/puestos');
		}

		try {
			$old_data = $position->to_array();
			
			if ($position->delete()) {
				Helper_Log::record(
					'puestos',
					'delete',
					$position->id,
					'Puesto eliminado: ' . $position->name,
					$old_data,
					null
				);

				Session::set_flash('success', 'Puesto eliminado exitosamente.');
			}
		} catch (Exception $e) {
			\Log::error('Error al eliminar puesto: ' . $e->getMessage());
			Session::set_flash('error', 'Error al eliminar el puesto: ' . $e->getMessage());
		}

		Response::redirect('admin/puestos');
	}

	/**
	 * Validación del formulario
	 */
	protected function _validation()
	{
		$val = Validation::forge();

		$val->add_field('name', 'Nombre', 'required|trim|max_length[100]');
		$val->add_field('code', 'Código', 'trim|max_length[50]');
		$val->add_field('description', 'Descripción', 'trim');
		$val->add_field('salary_min', 'Salario Mínimo', 'trim|valid_string[numeric]');
		$val->add_field('salary_max', 'Salario Máximo', 'trim|valid_string[numeric]');

		return $val;
	}
}
