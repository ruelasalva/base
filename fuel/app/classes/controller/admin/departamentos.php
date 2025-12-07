<?php

/**
 * Controlador de Departamentos
 * Sistema Multi-Tenant
 * 
 * @package    App
 * @category   Controller
 * @author     Sistema Base
 */
class Controller_Admin_Departamentos extends Controller_Admin
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

		if (!Helper_Permission::can('departamentos', $required_permission)) {
			Session::set_flash('error', 'No tienes permisos para realizar esta acción.');
			Response::redirect('admin');
		}
	}

	/**
	 * Listado de departamentos
	 */
	public function action_index()
	{
		$search = Input::get('search', '');

		$query = Model_Department::query();

		if (!empty($search)) {
			$query->where_open()
				->where('name', 'like', "%{$search}%")
				->or_where('code', 'like', "%{$search}%")
			->where_close();
		}

		$departments = $query->order_by('name', 'asc')->get();

		$this->template->title = 'Departamentos';
		$this->template->content = View::forge('admin/departamentos/index', [
			'departments' => $departments,
			'search' => $search
		]);
	}

	/**
	 * Crear nuevo departamento
	 */
	public function action_create()
	{
		if (Input::method() === 'POST') {
			$val = $this->_validation();

			if ($val->run()) {
				try {
					$department = new Model_Department([
						'tenant_id' => Session::get('tenant_id', 1),
						'parent_id' => $val->validated('parent_id'),
						'name' => $val->validated('name'),
						'code' => $val->validated('code'),
						'description' => $val->validated('description'),
						'manager_id' => $val->validated('manager_id'),
						'is_active' => 1
					]);

					if ($department->save()) {
						Helper_Log::record(
							'departamentos',
							'create',
							$department->id,
							'Departamento creado: ' . $department->name,
							null,
							$department->to_array()
						);

						Session::set_flash('success', 'Departamento creado exitosamente.');
						Response::redirect('admin/departamentos');
					}
				} catch (Exception $e) {
					\Log::error('Error al crear departamento: ' . $e->getMessage());
					Session::set_flash('error', 'Error al crear el departamento: ' . $e->getMessage());
				}
			} else {
				Session::set_flash('error', 'Por favor corrige los errores en el formulario.');
			}
		}

		// Obtener departamentos para selector de padre
		$departments = Model_Department::query()
			->order_by('name', 'asc')
			->get();

		// Obtener empleados para selector de manager
		$employees = Model_Employee::query()
			->where('employment_status', 'active')
			->where('deleted_at', 'IS', null)
			->order_by('first_name', 'asc')
			->get();

		$this->template->title = 'Nuevo Departamento';
		$this->template->content = View::forge('admin/departamentos/create', [
			'departments' => $departments,
			'employees' => $employees
		]);
	}

	/**
	 * Ver detalle de departamento
	 */
	public function action_view($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de departamento requerido.');
			Response::redirect('admin/departamentos');
		}

		$department = Model_Department::find($id);

		if (!$department) {
			Session::set_flash('error', 'Departamento no encontrado.');
			Response::redirect('admin/departamentos');
		}

		// Obtener logs del departamento
		$logs = DB::select('*')
			->from('audit_logs')
			->where('module', 'departamentos')
			->where('record_id', $id)
			->order_by('created_at', 'desc')
			->limit(20)
			->execute()
			->as_array();

		$this->template->title = 'Departamento: ' . $department->name;
		$this->template->content = View::forge('admin/departamentos/view', [
			'department' => $department,
			'logs' => $logs
		]);
	}

	/**
	 * Editar departamento
	 */
	public function action_edit($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de departamento requerido.');
			Response::redirect('admin/departamentos');
		}

		$department = Model_Department::find($id);

		if (!$department) {
			Session::set_flash('error', 'Departamento no encontrado.');
			Response::redirect('admin/departamentos');
		}

		if (Input::method() === 'POST') {
			$val = $this->_validation();

			if ($val->run()) {
				try {
					$old_data = $department->to_array();

					$department->parent_id = $val->validated('parent_id');
					$department->name = $val->validated('name');
					$department->code = $val->validated('code');
					$department->description = $val->validated('description');
					$department->manager_id = $val->validated('manager_id');
					$department->is_active = Input::post('is_active', 1);

					if ($department->save()) {
						Helper_Log::record(
							'departamentos',
							'edit',
							$department->id,
							'Departamento actualizado: ' . $department->name,
							$old_data,
							$department->to_array()
						);

						Session::set_flash('success', 'Departamento actualizado exitosamente.');
						Response::redirect('admin/departamentos');
					}
				} catch (Exception $e) {
					\Log::error('Error al actualizar departamento: ' . $e->getMessage());
					Session::set_flash('error', 'Error al actualizar el departamento: ' . $e->getMessage());
				}
			} else {
				Session::set_flash('error', 'Por favor corrige los errores en el formulario.');
			}
		}

		// Obtener departamentos excepto el actual para evitar referencias circulares
		$departments = Model_Department::query()
			->where('id', '!=', $id)
			->order_by('name', 'asc')
			->get();

		// Obtener empleados para selector de manager
		$employees = Model_Employee::query()
			->where('employment_status', 'active')
			->where('deleted_at', 'IS', null)
			->order_by('first_name', 'asc')
			->get();

		$this->template->title = 'Editar Departamento';
		$this->template->content = View::forge('admin/departamentos/edit', [
			'department' => $department,
			'departments' => $departments,
			'employees' => $employees
		]);
	}

	/**
	 * Eliminar departamento
	 */
	public function action_delete($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de departamento requerido.');
			Response::redirect('admin/departamentos');
		}

		$department = Model_Department::find($id);

		if (!$department) {
			Session::set_flash('error', 'Departamento no encontrado.');
			Response::redirect('admin/departamentos');
		}

		// Verificar si tiene empleados asignados
		$employee_count = $department->count_active_employees();
		if ($employee_count > 0) {
			Session::set_flash('error', 'No se puede eliminar el departamento porque tiene ' . $employee_count . ' empleado(s) asignado(s).');
			Response::redirect('admin/departamentos');
		}

		try {
			$old_data = $department->to_array();
			
			if ($department->delete()) {
				Helper_Log::record(
					'departamentos',
					'delete',
					$department->id,
					'Departamento eliminado: ' . $department->name,
					$old_data,
					null
				);

				Session::set_flash('success', 'Departamento eliminado exitosamente.');
			}
		} catch (Exception $e) {
			\Log::error('Error al eliminar departamento: ' . $e->getMessage());
			Session::set_flash('error', 'Error al eliminar el departamento: ' . $e->getMessage());
		}

		Response::redirect('admin/departamentos');
	}

	/**
	 * Validación del formulario
	 */
	protected function _validation()
	{
		$val = Validation::forge();

		$val->add_field('parent_id', 'Departamento Padre', 'trim|numeric');
		$val->add_field('name', 'Nombre', 'required|trim|max_length[100]');
		$val->add_field('code', 'Código', 'trim|max_length[50]');
		$val->add_field('description', 'Descripción', 'trim');
		$val->add_field('manager_id', 'Responsable', 'trim|numeric');

		return $val;
	}
}
