<?php

/**
 * Controlador de Empleados
 * Sistema Multi-Tenant
 * 
 * @package    App
 * @category   Controller
 * @author     Sistema Base
 */
class Controller_Admin_Empleados extends Controller_Admin
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

		if (!Helper_Permission::can('empleados', $required_permission)) {
			Session::set_flash('error', 'No tienes permisos para realizar esta acción.');
			Response::redirect('admin');
		}
	}

	/**
	 * Listado de empleados con búsqueda y paginación
	 */
	public function action_index()
	{
		$search = Input::get('search', '');
		$status = Input::get('status', '');
		$department_id = Input::get('department', '');
		$per_page = 25;

		$query = Model_Employee::query()
			->where('deleted_at', 'IS', null);

		// Filtro de búsqueda
		if (!empty($search)) {
			$query->where_open()
				->where('first_name', 'like', "%{$search}%")
				->or_where('last_name', 'like', "%{$search}%")
				->or_where('second_last_name', 'like', "%{$search}%")
				->or_where('code', 'like', "%{$search}%")
				->or_where('email', 'like', "%{$search}%")
				->or_where('rfc', 'like', "%{$search}%")
				->or_where('curp', 'like', "%{$search}%")
			->where_close();
		}

		// Filtro por estatus
		if (!empty($status)) {
			$query->where('employment_status', $status);
		}

		// Filtro por departamento
		if (!empty($department_id)) {
			$query->where('department_id', $department_id);
		}

		// Configuración de paginación
		$config = [
			'pagination_url' => Uri::create('admin/empleados/index'),
			'total_items' => $query->count(),
			'per_page' => $per_page,
			'uri_segment' => 'page',
		];

		$pagination = Pagination::forge('empleados', $config);

		$employees = $query
			->order_by('id', 'desc')
			->rows_limit($pagination->per_page)
			->rows_offset($pagination->offset)
			->get();

		// Obtener departamentos para el filtro
		$departments = Model_Department::query()
			->where('is_active', 1)
			->order_by('name', 'asc')
			->get();

		// Estadísticas
		$stats = [
			'total' => Model_Employee::query()->where('deleted_at', 'IS', null)->count(),
			'active' => Model_Employee::query()->where('employment_status', 'active')->where('deleted_at', 'IS', null)->count(),
			'inactive' => Model_Employee::query()->where('employment_status', 'inactive')->where('deleted_at', 'IS', null)->count(),
			'on_leave' => Model_Employee::query()->where('employment_status', 'on_leave')->where('deleted_at', 'IS', null)->count(),
		];

		$this->template->title = 'Empleados';
		$this->template->content = View::forge('admin/empleados/index', [
			'employees' => $employees,
			'departments' => $departments,
			'search' => $search,
			'status' => $status,
			'department_id' => $department_id,
			'pagination' => $pagination,
			'stats' => $stats
		]);
	}

	/**
	 * Formulario para crear nuevo empleado
	 */
	public function action_create()
	{
		if (Input::method() === 'POST') {
			$val = $this->_validation();

			if ($val->run()) {
				try {
					$employee = new Model_Employee([
						'tenant_id' => Session::get('tenant_id', 1),
						'code' => $val->validated('code'),
						'first_name' => $val->validated('first_name'),
						'last_name' => $val->validated('last_name'),
						'second_last_name' => $val->validated('second_last_name'),
						'gender' => $val->validated('gender'),
						'birthdate' => $val->validated('birthdate'),
						'curp' => strtoupper($val->validated('curp')),
						'rfc' => strtoupper($val->validated('rfc')),
						'nss' => $val->validated('nss'),
						'email' => $val->validated('email'),
						'phone' => $val->validated('phone'),
						'phone_emergency' => $val->validated('phone_emergency'),
						'emergency_contact_name' => $val->validated('emergency_contact_name'),
						'address' => $val->validated('address'),
						'city' => $val->validated('city'),
						'state' => $val->validated('state'),
						'postal_code' => $val->validated('postal_code'),
						'country' => $val->validated('country'),
						'department_id' => $val->validated('department_id'),
						'position_id' => $val->validated('position_id'),
						'hire_date' => $val->validated('hire_date'),
						'employment_type' => $val->validated('employment_type'),
						'employment_status' => $val->validated('employment_status'),
						'salary' => $val->validated('salary'),
						'salary_type' => $val->validated('salary_type'),
						'bank_name' => $val->validated('bank_name'),
						'bank_account' => $val->validated('bank_account'),
						'clabe' => $val->validated('clabe'),
						'notes' => $val->validated('notes'),
						'is_active' => 1
					]);

					if ($employee->save()) {
						// Registrar en log
						Helper_Log::record(
							'empleados',
							'create',
							$employee->id,
							'Empleado creado: ' . $employee->get_full_name(),
							null,
							$employee->to_array()
						);

						Session::set_flash('success', 'Empleado creado exitosamente.');
						Response::redirect('admin/empleados/view/' . $employee->id);
					}
				} catch (Exception $e) {
					\Log::error('Error al crear empleado: ' . $e->getMessage());
					Session::set_flash('error', 'Error al crear el empleado: ' . $e->getMessage());
				}
			} else {
				Session::set_flash('error', 'Por favor corrige los errores en el formulario.');
			}
		}

		// Obtener catálogos
		$departments = Model_Department::query()
			->where('is_active', 1)
			->order_by('name', 'asc')
			->get();

		$positions = Model_Position::query()
			->where('is_active', 1)
			->order_by('name', 'asc')
			->get();

		$this->template->title = 'Nuevo Empleado';
		$this->template->content = View::forge('admin/empleados/create', [
			'departments' => $departments,
			'positions' => $positions
		]);
	}

	/**
	 * Ver detalle de empleado
	 */
	public function action_view($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de empleado requerido.');
			Response::redirect('admin/empleados');
		}

		$employee = Model_Employee::find($id);

		if (!$employee || $employee->deleted_at !== null) {
			Session::set_flash('error', 'Empleado no encontrado.');
			Response::redirect('admin/empleados');
		}

		// Obtener logs del empleado
		$logs = DB::select('*')
			->from('audit_logs')
			->where('module', 'empleados')
			->where('record_id', $id)
			->order_by('created_at', 'desc')
			->limit(20)
			->execute()
			->as_array();

		$this->template->title = 'Empleado: ' . $employee->get_full_name();
		$this->template->content = View::forge('admin/empleados/view', [
			'employee' => $employee,
			'logs' => $logs
		]);
	}

	/**
	 * Editar empleado existente
	 */
	public function action_edit($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de empleado requerido.');
			Response::redirect('admin/empleados');
		}

		$employee = Model_Employee::find($id);

		if (!$employee || $employee->deleted_at !== null) {
			Session::set_flash('error', 'Empleado no encontrado.');
			Response::redirect('admin/empleados');
		}

		if (Input::method() === 'POST') {
			$val = $this->_validation();

			if ($val->run()) {
				try {
					$old_data = $employee->to_array();

					$employee->code = $val->validated('code');
					$employee->first_name = $val->validated('first_name');
					$employee->last_name = $val->validated('last_name');
					$employee->second_last_name = $val->validated('second_last_name');
					$employee->gender = $val->validated('gender');
					$employee->birthdate = $val->validated('birthdate');
					$employee->curp = strtoupper($val->validated('curp'));
					$employee->rfc = strtoupper($val->validated('rfc'));
					$employee->nss = $val->validated('nss');
					$employee->email = $val->validated('email');
					$employee->phone = $val->validated('phone');
					$employee->phone_emergency = $val->validated('phone_emergency');
					$employee->emergency_contact_name = $val->validated('emergency_contact_name');
					$employee->address = $val->validated('address');
					$employee->city = $val->validated('city');
					$employee->state = $val->validated('state');
					$employee->postal_code = $val->validated('postal_code');
					$employee->country = $val->validated('country');
					$employee->department_id = $val->validated('department_id');
					$employee->position_id = $val->validated('position_id');
					$employee->hire_date = $val->validated('hire_date');
					$employee->termination_date = $val->validated('termination_date');
					$employee->employment_type = $val->validated('employment_type');
					$employee->employment_status = $val->validated('employment_status');
					$employee->salary = $val->validated('salary');
					$employee->salary_type = $val->validated('salary_type');
					$employee->bank_name = $val->validated('bank_name');
					$employee->bank_account = $val->validated('bank_account');
					$employee->clabe = $val->validated('clabe');
					$employee->notes = $val->validated('notes');

					if ($employee->save()) {
						// Registrar en log
						Helper_Log::record(
							'empleados',
							'edit',
							$employee->id,
							'Empleado actualizado: ' . $employee->get_full_name(),
							$old_data,
							$employee->to_array()
						);

						Session::set_flash('success', 'Empleado actualizado exitosamente.');
						Response::redirect('admin/empleados/view/' . $employee->id);
					}
				} catch (Exception $e) {
					\Log::error('Error al actualizar empleado: ' . $e->getMessage());
					Session::set_flash('error', 'Error al actualizar el empleado: ' . $e->getMessage());
				}
			} else {
				Session::set_flash('error', 'Por favor corrige los errores en el formulario.');
			}
		}

		// Obtener catálogos
		$departments = Model_Department::query()
			->where('is_active', 1)
			->order_by('name', 'asc')
			->get();

		$positions = Model_Position::query()
			->where('is_active', 1)
			->order_by('name', 'asc')
			->get();

		$this->template->title = 'Editar Empleado';
		$this->template->content = View::forge('admin/empleados/edit', [
			'employee' => $employee,
			'departments' => $departments,
			'positions' => $positions
		]);
	}

	/**
	 * Eliminar empleado (soft delete)
	 */
	public function action_delete($id = null)
	{
		if (!$id) {
			Session::set_flash('error', 'ID de empleado requerido.');
			Response::redirect('admin/empleados');
		}

		$employee = Model_Employee::find($id);

		if (!$employee || $employee->deleted_at !== null) {
			Session::set_flash('error', 'Empleado no encontrado.');
			Response::redirect('admin/empleados');
		}

		try {
			$old_data = $employee->to_array();
			
			if ($employee->delete()) {
				// Registrar en log
				Helper_Log::record(
					'empleados',
					'delete',
					$employee->id,
					'Empleado eliminado: ' . $employee->get_full_name(),
					$old_data,
					null
				);

				Session::set_flash('success', 'Empleado eliminado exitosamente.');
			}
		} catch (Exception $e) {
			\Log::error('Error al eliminar empleado: ' . $e->getMessage());
			Session::set_flash('error', 'Error al eliminar el empleado: ' . $e->getMessage());
		}

		Response::redirect('admin/empleados');
	}

	/**
	 * Validación del formulario
	 */
	protected function _validation()
	{
		$val = Validation::forge();

		$val->add_field('code', 'Código', 'trim|max_length[50]');
		$val->add_field('first_name', 'Nombre', 'required|trim|max_length[100]');
		$val->add_field('last_name', 'Apellido Paterno', 'required|trim|max_length[100]');
		$val->add_field('second_last_name', 'Apellido Materno', 'trim|max_length[100]');
		$val->add_field('gender', 'Género', 'trim');
		$val->add_field('birthdate', 'Fecha de Nacimiento', 'trim');
		$val->add_field('curp', 'CURP', 'trim|max_length[18]');
		$val->add_field('rfc', 'RFC', 'trim|max_length[13]');
		$val->add_field('nss', 'NSS', 'trim|max_length[11]');
		$val->add_field('email', 'Email', 'required|valid_email|max_length[255]');
		$val->add_field('phone', 'Teléfono', 'trim|max_length[20]');
		$val->add_field('phone_emergency', 'Teléfono de Emergencia', 'trim|max_length[20]');
		$val->add_field('emergency_contact_name', 'Contacto de Emergencia', 'trim|max_length[200]');
		$val->add_field('address', 'Dirección', 'trim');
		$val->add_field('city', 'Ciudad', 'trim|max_length[100]');
		$val->add_field('state', 'Estado', 'trim|max_length[100]');
		$val->add_field('postal_code', 'Código Postal', 'trim|max_length[10]');
		$val->add_field('country', 'País', 'trim|max_length[100]');
		$val->add_field('department_id', 'Departamento', 'trim|numeric');
		$val->add_field('position_id', 'Puesto', 'trim|numeric');
		$val->add_field('hire_date', 'Fecha de Contratación', 'required|trim');
		$val->add_field('termination_date', 'Fecha de Baja', 'trim');
		$val->add_field('employment_type', 'Tipo de Empleo', 'trim');
		$val->add_field('employment_status', 'Estatus', 'trim');
		$val->add_field('salary', 'Salario', 'trim|valid_string[numeric]');
		$val->add_field('salary_type', 'Tipo de Salario', 'trim');
		$val->add_field('bank_name', 'Banco', 'trim|max_length[100]');
		$val->add_field('bank_account', 'Cuenta Bancaria', 'trim|max_length[50]');
		$val->add_field('clabe', 'CLABE', 'trim|max_length[18]');
		$val->add_field('notes', 'Notas', 'trim');

		return $val;
	}
}
