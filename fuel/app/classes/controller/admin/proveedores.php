<?php

/**
 * CONTROLADOR PROVEEDORES - VERSIÓN MODERNA
 *
 * @package  app
 * @extends  Controller_Admin
 * @version  2.0
 * @author   Sistema Base
 */
class Controller_Admin_Proveedores extends Controller_Admin
{
	/**
	 * BEFORE - Verificar permisos
	 */
	public function before()
	{
		parent::before();

		if (!Auth::member(100)) {
			Session::set_flash('error', 'No tienes permisos para acceder a proveedores.');
			Response::redirect('admin');
		}
	}

	/**
	 * INDEX - Listado de proveedores
	 */
	public function action_index()
	{
		$data = [];
		$per_page = 50;

		// Construir query base
		$query = Model_Provider::query()
			->where('deleted_at', 'IS', null);

		// Búsqueda multi-campo
		if ($search = Input::get('search')) {
			$search_term = '%' . trim($search) . '%';
			$query->where_open()
				->where('company_name', 'like', $search_term)
				->or_where('tax_id', 'like', $search_term)
				->or_where('code', 'like', $search_term)
				->or_where('email', 'like', $search_term)
				->or_where('phone', 'like', $search_term)
				->or_where('contact_name', 'like', $search_term)
				->where_close();
		}

		// Filtro por estado
		if (Input::get('status') !== null) {
			if (Input::get('status') == 'active') {
				$query->where('is_active', 1)->where('is_suspended', 0);
			} elseif (Input::get('status') == 'suspended') {
				$query->where('is_suspended', 1);
			} elseif (Input::get('status') == 'inactive') {
				$query->where('is_active', 0);
			}
		}

		// Paginación
		$config = [
			'pagination_url' => Uri::current(),
			'total_items' => $query->count(),
			'per_page' => $per_page,
			'uri_segment' => 'page',
			'show_first' => true,
			'show_last' => true,
		];

		$pagination = Pagination::forge('providers', $config);

		// Obtener registros
		$providers = $query->order_by('created_at', 'desc')
			->limit($pagination->per_page)
			->offset($pagination->offset)
			->get();

		// Estadísticas
		$data['stats'] = [
			'total' => Model_Provider::query()->where('deleted_at', 'IS', null)->count(),
			'active' => Model_Provider::query()->where('deleted_at', 'IS', null)->where('is_active', 1)->where('is_suspended', 0)->count(),
			'suspended' => Model_Provider::query()->where('deleted_at', 'IS', null)->where('is_suspended', 1)->count(),
			'inactive' => Model_Provider::query()->where('deleted_at', 'IS', null)->where('is_active', 0)->count(),
		];

		$data['providers'] = $providers;
		$data['pagination'] = $pagination->render();
		$data['search'] = Input::get('search', '');
		$data['current_status'] = Input::get('status', '');

		$this->template->title = 'Proveedores';
		$this->template->content = View::forge('admin/proveedores/index', $data, false);
	}

	/**
	 * CREATE - Formulario de creación
	 */
	public function action_create()
	{
		$data = [];
		$provider = null;

		if (Input::method() == 'POST') {
			$val = $this->_validate_provider();

			if ($val->run()) {
				try {
					$provider = new Model_Provider();
					$provider->code = strtoupper(Input::post('code'));
					$provider->company_name = Input::post('company_name');
					$provider->contact_name = Input::post('contact_name');
					$provider->email = Input::post('email');
					$provider->phone = Input::post('phone');
					$provider->phone_secondary = Input::post('phone_secondary');
					$provider->tax_id = strtoupper(Input::post('tax_id'));
					$provider->website = Input::post('website');
					
					// Dirección
					$provider->address = Input::post('address');
					$provider->city = Input::post('city');
					$provider->state = Input::post('state');
					$provider->postal_code = Input::post('postal_code');
					$provider->country = Input::post('country', 'México');
					
					// Datos financieros
					$provider->payment_terms = Input::post('payment_terms');
					$provider->credit_limit = Input::post('credit_limit', 0);
					
					// Notas
					$provider->notes = Input::post('notes');
					
					// Estado
					$provider->is_active = 1;
					$provider->is_suspended = 0;

					if ($provider->save()) {
						Session::set_flash('success', 'Proveedor <b>' . htmlspecialchars($provider->company_name, ENT_QUOTES, 'UTF-8') . '</b> creado exitosamente.');
						Response::redirect('admin/proveedores/view/' . $provider->id);
					}
				} catch (Exception $e) {
					Session::set_flash('error', 'Error al guardar: ' . $e->getMessage());
				}
			} else {
				Session::set_flash('error', 'Por favor corrige los errores en el formulario.');
				$data['errors'] = $val->error();
			}
		}

		$data['provider'] = $provider;
		$data['states'] = $this->_get_mexico_states();

		$this->template->title = 'Nuevo Proveedor';
		$this->template->content = View::forge('admin/proveedores/form', $data, false);
	}

	/**
	 * EDIT - Editar proveedor
	 */
	public function action_edit($id = null)
	{
		if (!$id || !is_numeric($id)) {
			Response::redirect('admin/proveedores');
		}

		$provider = Model_Provider::find($id);
		
		if (!$provider || $provider->deleted_at) {
			Session::set_flash('error', 'Proveedor no encontrado.');
			Response::redirect('admin/proveedores');
		}

		if (Input::method() == 'POST') {
			$val = $this->_validate_provider($id);

			if ($val->run()) {
				try {
					$provider->code = strtoupper(Input::post('code'));
					$provider->company_name = Input::post('company_name');
					$provider->contact_name = Input::post('contact_name');
					$provider->email = Input::post('email');
					$provider->phone = Input::post('phone');
					$provider->phone_secondary = Input::post('phone_secondary');
					$provider->tax_id = strtoupper(Input::post('tax_id'));
					$provider->website = Input::post('website');
					
					// Dirección
					$provider->address = Input::post('address');
					$provider->city = Input::post('city');
					$provider->state = Input::post('state');
					$provider->postal_code = Input::post('postal_code');
					$provider->country = Input::post('country', 'México');
					
					// Datos financieros
					$provider->payment_terms = Input::post('payment_terms');
					$provider->credit_limit = Input::post('credit_limit', 0);
					
					// Notas
					$provider->notes = Input::post('notes');

					if ($provider->save()) {
						Session::set_flash('success', 'Proveedor actualizado exitosamente.');
						Response::redirect('admin/proveedores/view/' . $provider->id);
					}
				} catch (Exception $e) {
					Session::set_flash('error', 'Error al actualizar: ' . $e->getMessage());
				}
			} else {
				Session::set_flash('error', 'Por favor corrige los errores en el formulario.');
				$data['errors'] = $val->error();
			}
		}

		$data['provider'] = $provider;
		$data['states'] = $this->_get_mexico_states();

		$this->template->title = 'Editar Proveedor - ' . $provider->company_name;
		$this->template->content = View::forge('admin/proveedores/form', $data, false);
	}

	/**
	 * VIEW - Ver detalles del proveedor
	 */
	public function action_view($id = null)
	{
		if (!$id || !is_numeric($id)) {
			Response::redirect('admin/proveedores');
		}

		$provider = Model_Provider::find($id);
		
		if (!$provider || $provider->deleted_at) {
			Session::set_flash('error', 'Proveedor no encontrado.');
			Response::redirect('admin/proveedores');
		}

		$data['provider'] = $provider;

		$this->template->title = 'Proveedor - ' . $provider->company_name;
		$this->template->content = View::forge('admin/proveedores/view', $data, false);
	}

	/**
	 * DELETE - Soft delete de proveedor
	 */
	public function action_delete($id = null)
	{
		if (!$id || !is_numeric($id)) {
			Response::redirect('admin/proveedores');
		}

		$provider = Model_Provider::find($id);
		
		if (!$provider || $provider->deleted_at) {
			Session::set_flash('error', 'Proveedor no encontrado.');
			Response::redirect('admin/proveedores');
		}

		try {
			$provider->deleted_at = date('Y-m-d H:i:s');
			$provider->save();

			Session::set_flash('success', 'Proveedor eliminado correctamente.');
		} catch (Exception $e) {
			Session::set_flash('error', 'Error al eliminar: ' . $e->getMessage());
		}

		Response::redirect('admin/proveedores');
	}

	/**
	 * SUSPEND - Suspender proveedor
	 */
	public function action_suspend($id = null)
	{
		if (!$id || !is_numeric($id)) {
			Response::redirect('admin/proveedores');
		}

		$provider = Model_Provider::find($id);
		
		if (!$provider || $provider->deleted_at) {
			Session::set_flash('error', 'Proveedor no encontrado.');
			Response::redirect('admin/proveedores');
		}

		if (Input::method() == 'POST') {
			$reason = Input::post('reason');
			
			if (empty($reason)) {
				Session::set_flash('error', 'Debes especificar una razón para la suspensión.');
				Response::redirect('admin/proveedores/view/' . $id);
			}

			try {
				$provider->is_suspended = 1;
				$provider->suspended_reason = $reason;
				$provider->suspended_at = date('Y-m-d H:i:s');
				$provider->save();

				Session::set_flash('success', 'Proveedor suspendido correctamente.');
			} catch (Exception $e) {
				Session::set_flash('error', 'Error al suspender: ' . $e->getMessage());
			}
		}

		Response::redirect('admin/proveedores/view/' . $id);
	}

	/**
	 * ACTIVATE - Activar proveedor suspendido
	 */
	public function action_activate($id = null)
	{
		if (!$id || !is_numeric($id)) {
			Response::redirect('admin/proveedores');
		}

		$provider = Model_Provider::find($id);
		
		if (!$provider || $provider->deleted_at) {
			Session::set_flash('error', 'Proveedor no encontrado.');
			Response::redirect('admin/proveedores');
		}

		try {
			$provider->is_suspended = 0;
			$provider->suspended_reason = null;
			$provider->activated_at = date('Y-m-d H:i:s');
			$provider->activated_by = Auth::get('id');
			$provider->save();

			Session::set_flash('success', 'Proveedor activado correctamente.');
		} catch (Exception $e) {
			Session::set_flash('error', 'Error al activar: ' . $e->getMessage());
		}

		Response::redirect('admin/proveedores/view/' . $id);
	}

	/**
	 * VALIDACIÓN
	 */
	private function _validate_provider($id = null)
	{
		$val = Validation::forge('provider');
		
		$val->add_field('code', 'Código', 'required|max_length[50]');
		$val->add_field('company_name', 'Razón Social', 'required|min_length[3]|max_length[255]');
		$val->add_field('contact_name', 'Nombre de Contacto', 'max_length[255]');
		$val->add_field('email', 'Email', 'valid_email|max_length[255]');
		$val->add_field('phone', 'Teléfono', 'max_length[20]');
		$val->add_field('phone_secondary', 'Teléfono Secundario', 'max_length[20]');
		$val->add_field('tax_id', 'RFC', 'required|min_length[12]|max_length[13]');
		$val->add_field('website', 'Sitio Web', 'max_length[255]');
		$val->add_field('address', 'Dirección', 'max_length[500]');
		$val->add_field('city', 'Ciudad', 'max_length[100]');
		$val->add_field('state', 'Estado', 'max_length[100]');
		$val->add_field('postal_code', 'Código Postal', 'max_length[10]');
		$val->add_field('country', 'País', 'max_length[100]');
		$val->add_field('payment_terms', 'Términos de Pago', 'max_length[100]');
		$val->add_field('credit_limit', 'Límite de Crédito', 'numeric_min[0]');
		$val->add_field('notes', 'Notas');

		return $val;
	}

	/**
	 * ESTADOS DE MÉXICO
	 */
	private function _get_mexico_states()
	{
		return [
			'Aguascalientes', 'Baja California', 'Baja California Sur', 'Campeche',
			'Chiapas', 'Chihuahua', 'Ciudad de México', 'Coahuila', 'Colima',
			'Durango', 'Estado de México', 'Guanajuato', 'Guerrero', 'Hidalgo',
			'Jalisco', 'Michoacán', 'Morelos', 'Nayarit', 'Nuevo León', 'Oaxaca',
			'Puebla', 'Querétaro', 'Quintana Roo', 'San Luis Potosí', 'Sinaloa',
			'Sonora', 'Tabasco', 'Tamaulipas', 'Tlaxcala', 'Veracruz', 'Yucatán',
			'Zacatecas'
		];
	}
}
