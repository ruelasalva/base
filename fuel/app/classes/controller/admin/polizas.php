<?php

/**
 * Controller_Admin_Polizas
 * 
 * Gestión de Pólizas Contables (Journal Entries)
 * Sistema de partida doble con validación automática
 */
class Controller_Admin_Polizas extends Controller_Admin
{
	/**
	 * Listado de pólizas
	 */
	public function action_index()
	{
		if (!Helper_Permission::can('polizas', 'view')) {
			Session::set_flash('error', 'No tienes permisos para ver pólizas');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);

		// Filtros
		$period = Input::get('period', date('Y-m'));
		$type_filter = Input::get('type', '');
		$status_filter = Input::get('status', '');
		$search = Input::get('search', '');

		$query = Model_AccountingEntry::query()
			->where('tenant_id', $tenant_id);

		if ($period) {
			$query->where('period', $period);
		}

		if ($type_filter) {
			$query->where('entry_type', $type_filter);
		}

		if ($status_filter) {
			$query->where('status', $status_filter);
		}

		if ($search) {
			$query->and_where_open()
				->where('entry_number', 'LIKE', '%' . $search . '%')
				->or_where('concept', 'LIKE', '%' . $search . '%')
				->or_where('reference', 'LIKE', '%' . $search . '%')
				->and_where_close();
		}

		$config = array(
			'pagination_url' => Uri::create('admin/polizas/index'),
			'total_items' => $query->count(),
			'per_page' => 25,
			'uri_segment' => 'page',
		);

		$pagination = Pagination::forge('polizas', $config);

		$entries = $query
			->order_by('entry_date', 'DESC')
			->order_by('entry_number', 'DESC')
			->limit($pagination->per_page)
			->offset($pagination->offset)
			->get();

		// Estadísticas
		$stats = array(
			'total' => Model_AccountingEntry::query()->where('tenant_id', $tenant_id)->where('period', $period)->count(),
			'borradores' => Model_AccountingEntry::query()->where('tenant_id', $tenant_id)->where('period', $period)->where('status', 'borrador')->count(),
			'aplicadas' => Model_AccountingEntry::query()->where('tenant_id', $tenant_id)->where('period', $period)->where('status', 'aplicada')->count(),
			'canceladas' => Model_AccountingEntry::query()->where('tenant_id', $tenant_id)->where('period', $period)->where('status', 'cancelada')->count(),
			'desbalanceadas' => Model_AccountingEntry::query()->where('tenant_id', $tenant_id)->where('period', $period)->where('is_balanced', 0)->count(),
		);

		$this->template->title = 'Pólizas Contables';
		$this->template->content = View::forge('admin/polizas/index', array(
			'entries' => $entries,
			'pagination' => $pagination,
			'stats' => $stats,
			'period' => $period,
			'type_filter' => $type_filter,
			'status_filter' => $status_filter,
			'search' => $search,
			'can_create' => Helper_Permission::can('polizas', 'create'),
			'can_edit' => Helper_Permission::can('polizas', 'edit'),
			'can_delete' => Helper_Permission::can('polizas', 'delete'),
		), false);
	}

	/**
	 * Crear nueva póliza
	 */
	public function action_create()
	{
		if (!Helper_Permission::can('polizas', 'create')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin/polizas');
		}

		$tenant_id = Session::get('tenant_id', 1);

		if (Input::method() == 'POST') {
			$val = Validation::forge();
			$val->add_field('entry_type', 'Tipo', 'required');
			$val->add_field('entry_date', 'Fecha', 'required');
			$val->add_field('concept', 'Concepto', 'required');

			if ($val->run()) {
				try {
					DB::start_transaction();

					$entry_date = Input::post('entry_date');
					$period = date('Y-m', strtotime($entry_date));
					$fiscal_year = date('Y', strtotime($entry_date));
					$type = Input::post('entry_type');

					// Generar folio
					$entry_number = Model_AccountingEntry::generate_entry_number($tenant_id, $type, $period);

					// Crear póliza
					$entry = Model_AccountingEntry::forge();
					$entry->tenant_id = $tenant_id;
					$entry->entry_number = $entry_number;
					$entry->entry_type = $type;
					$entry->entry_date = $entry_date;
					$entry->created_date = date('Y-m-d H:i:s');
					$entry->period = $period;
					$entry->fiscal_year = $fiscal_year;
					$entry->concept = Input::post('concept');
					$entry->reference = Input::post('reference');
					$entry->status = 'borrador';
					$entry->created_by = Auth::get('id');
					$entry->total_debit = 0;
					$entry->total_credit = 0;
					$entry->is_balanced = 0;

					if ($entry->save()) {
						// Procesar líneas/partidas
						$accounts = Input::post('account_id', array());
						$descriptions = Input::post('description', array());
						$debits = Input::post('debit', array());
						$credits = Input::post('credit', array());
						$references = Input::post('line_reference', array());

						$line_number = 1;
						$total_debit = 0;
						$total_credit = 0;

						foreach ($accounts as $index => $account_id) {
							if (empty($account_id)) continue;

							$debit = floatval($debits[$index]);
							$credit = floatval($credits[$index]);

							if ($debit == 0 && $credit == 0) continue;

							$line = Model_AccountingEntryLine::forge();
							$line->entry_id = $entry->id;
							$line->line_number = $line_number++;
							$line->account_id = $account_id;
							$line->description = $descriptions[$index];
							$line->debit = $debit;
							$line->credit = $credit;
							$line->reference = $references[$index] ?? null;
							$line->save();

							$total_debit += $debit;
							$total_credit += $credit;
						}

						// Actualizar totales
						$entry->total_debit = $total_debit;
						$entry->total_credit = $total_credit;
						$entry->validate_balance();
						$entry->save();

						DB::commit_transaction();

						Session::set_flash('success', 'Póliza creada: ' . $entry_number);
						Response::redirect('admin/polizas/view/' . $entry->id);
					}
				} catch (Exception $e) {
					DB::rollback_transaction();
					Session::set_flash('error', 'Error: ' . $e->getMessage());
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

		// Obtener cuentas que permiten movimientos
		$accounts = Model_AccountingAccount::query()
			->where('tenant_id', $tenant_id)
			->where('allows_movement', 1)
			->where('is_active', 1)
			->order_by('account_code', 'ASC')
			->get();

		$this->template->title = 'Nueva Póliza';
		$this->template->content = View::forge('admin/polizas/form', array(
			'entry' => null,
			'accounts' => $accounts,
		), false);
	}

	/**
	 * Ver detalle de póliza
	 */
	public function action_view($id = null)
	{
		if (!Helper_Permission::can('polizas', 'view')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin/polizas');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$entry = Model_AccountingEntry::find($id);

		if (!$entry || $entry->tenant_id != $tenant_id) {
			Session::set_flash('error', 'Póliza no encontrada');
			Response::redirect('admin/polizas');
		}

		// Obtener líneas con información de cuentas
		$lines = DB::select('el.*', array('aa.account_code', 'account_code'), array('aa.name', 'account_name'))
			->from(array('accounting_entry_lines', 'el'))
			->join(array('accounting_accounts', 'aa'), 'LEFT')
			->on('el.account_id', '=', 'aa.id')
			->where('el.entry_id', $id)
			->order_by('el.line_number', 'ASC')
			->execute()
			->as_array();

		$this->template->title = 'Detalle de Póliza';
		$this->template->content = View::forge('admin/polizas/view', array(
			'entry' => $entry,
			'lines' => $lines,
			'can_edit' => Helper_Permission::can('polizas', 'edit'),
			'can_delete' => Helper_Permission::can('polizas', 'delete'),
		), false);
	}

	/**
	 * Aplicar póliza (cambiar de borrador a aplicada)
	 */
	public function action_apply($id = null)
	{
		if (!Helper_Permission::can('polizas', 'edit')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin/polizas');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$entry = Model_AccountingEntry::find($id);

		if (!$entry || $entry->tenant_id != $tenant_id) {
			Session::set_flash('error', 'Póliza no encontrada');
			Response::redirect('admin/polizas');
		}

		try {
			$entry->apply(Auth::get('id'));
			Session::set_flash('success', 'Póliza aplicada exitosamente');
		} catch (Exception $e) {
			Session::set_flash('error', $e->getMessage());
		}

		Response::redirect('admin/polizas/view/' . $id);
	}

	/**
	 * Cancelar póliza
	 */
	public function action_cancel($id = null)
	{
		if (!Helper_Permission::can('polizas', 'delete')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin/polizas');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$entry = Model_AccountingEntry::find($id);

		if (!$entry || $entry->tenant_id != $tenant_id) {
			Session::set_flash('error', 'Póliza no encontrada');
			Response::redirect('admin/polizas');
		}

		if (Input::method() == 'POST') {
			$reason = Input::post('cancellation_reason');
			if (empty($reason)) {
				Session::set_flash('error', 'Debe indicar el motivo de cancelación');
			} else {
				try {
					$entry->cancel(Auth::get('id'), $reason);
					Session::set_flash('success', 'Póliza cancelada');
					Response::redirect('admin/polizas/view/' . $id);
				} catch (Exception $e) {
					Session::set_flash('error', $e->getMessage());
				}
			}
		}

		$this->template->title = 'Cancelar Póliza';
		$this->template->content = View::forge('admin/polizas/cancel', array(
			'entry' => $entry,
		), false);
	}

	/**
	 * Eliminar póliza (solo borradores)
	 */
	public function action_delete($id = null)
	{
		if (!Helper_Permission::can('polizas', 'delete')) {
			Session::set_flash('error', 'No tienes permisos');
			Response::redirect('admin/polizas');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$entry = Model_AccountingEntry::find($id);

		if (!$entry || $entry->tenant_id != $tenant_id) {
			Session::set_flash('error', 'Póliza no encontrada');
			Response::redirect('admin/polizas');
		}

		if ($entry->status != 'borrador') {
			Session::set_flash('error', 'Solo se pueden eliminar pólizas en borrador');
			Response::redirect('admin/polizas');
		}

		try {
			$entry->delete();
			Session::set_flash('success', 'Póliza eliminada');
		} catch (Exception $e) {
			Session::set_flash('error', 'Error: ' . $e->getMessage());
		}

		Response::redirect('admin/polizas');
	}
}
