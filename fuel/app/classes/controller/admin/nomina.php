<?php

/**
 * Controller_Admin_Nomina
 * Controlador del Sistema de Nómina
 * 
 * @package    App
 * @subpackage Controller
 * @category   Payroll
 */
class Controller_Admin_Nomina extends Controller_Admin
{
	public function before()
	{
		parent::before();
		
		// Verificar permisos
		/*if (!Auth::has_access('nomina.index')) {
			Session::set_flash('error', 'No tienes permiso para acceder a este módulo');
			Response::redirect('admin/dashboard');
		}*/
	}

	/**
	 * Lista de períodos de nómina
	 */
	public function action_index()
	{
		$config = array(
			'pagination_url' => Uri::create('admin/nomina/index'),
			'total_items' => Model_Payroll_Period::count(array(
				'where' => array(
					array('tenant_id', '=', $this->tenant_id),
					array('deleted_at', 'IS', null),
				),
			)),
			'per_page' => 20,
			'uri_segment' => 4,
		);

		$pagination = Pagination::forge('nomina_pagination', $config);

		$periods = Model_Payroll_Period::query()
			->where('tenant_id', '=', $this->tenant_id)
			->where('deleted_at', 'IS', null)
			->order_by('year', 'desc')
			->order_by('period_number', 'desc')
			->limit($pagination->per_page)
			->offset($pagination->offset)
			->get();

		$this->template->title = 'Nómina';
		$this->template->content = View::forge('admin/nomina/index', [
			'periods' => $periods,
			'pagination' => $pagination,
		]);
	}

	/**
	 * Ver detalle de un período
	 */
	public function action_view($id = null)
	{
		$period = Model_Payroll_Period::find($id);

		if (!$period || $period->tenant_id != $this->tenant_id || $period->deleted_at) {
			Session::set_flash('error', 'Período de nómina no encontrado');
			Response::redirect('admin/nomina');
		}

		// Obtener recibos del período
		$receipts = Model_Payroll_Receipt::query()
			->where('period_id', '=', $id)
			->where('deleted_at', 'IS', null)
			->order_by('employee_name', 'asc')
			->get();

		// Audit logs
		$logs = \DB::select('*')->from('audit_logs')
			->where('module', '=', 'nomina')
			->where('record_id', '=', $id)
			->order_by('created_at', 'desc')
			->limit(20)
			->execute()
			->as_array();

		$this->template->title = 'Detalle de Nómina';
		$this->template->content = View::forge('admin/nomina/view', [
			'period' => $period,
			'receipts' => $receipts,
			'logs' => $logs,
		]);
	}

	/**
	 * Crear nuevo período
	 */
	public function action_create()
	{
		if (!Auth::has_access('nomina.create')) {
			Session::set_flash('error', 'No tienes permiso para crear períodos');
			Response::redirect('admin/nomina');
		}

		if (Input::method() == 'POST') {
			$val = Model_Payroll_Period::validate('create');

			if ($val->run()) {
				try {
					$period = Model_Payroll_Period::forge();
					$period->tenant_id = $this->tenant_id;
					$period->code = Input::post('code');
					$period->name = Input::post('name');
					$period->period_type = Input::post('period_type');
					$period->year = Input::post('year');
					$period->period_number = Input::post('period_number');
					$period->start_date = Input::post('start_date');
					$period->end_date = Input::post('end_date');
					$period->payment_date = Input::post('payment_date');
					$period->notes = Input::post('notes');
					$period->status = 'draft';

					if ($period->save()) {
						// Audit log
						\Model_Audit_Log::log_action(
							'nomina',
							$period->id,
							'create',
							'Período de nómina creado: ' . $period->name,
							null,
							$period->to_array()
						);

						Session::set_flash('success', 'Período de nómina creado exitosamente');
						Response::redirect('admin/nomina/view/' . $period->id);
					}
				} catch (\Exception $e) {
					Session::set_flash('error', 'Error al crear el período: ' . $e->getMessage());
				}
			} else {
				Session::set_flash('error', $val->error());
			}
		}

		$this->template->title = 'Crear Período de Nómina';
		$this->template->content = View::forge('admin/nomina/create');
	}

	/**
	 * Editar período
	 */
	public function action_edit($id = null)
	{
		if (!Auth::has_access('nomina.edit')) {
			Session::set_flash('error', 'No tienes permiso para editar períodos');
			Response::redirect('admin/nomina');
		}

		$period = Model_Payroll_Period::find($id);

		if (!$period || $period->tenant_id != $this->tenant_id || $period->deleted_at) {
			Session::set_flash('error', 'Período no encontrado');
			Response::redirect('admin/nomina');
		}

		if (!$period->is_editable()) {
			Session::set_flash('error', 'Este período no puede ser editado en su estado actual');
			Response::redirect('admin/nomina/view/' . $id);
		}

		$val = Model_Payroll_Period::validate('edit');

		if ($val->run()) {
			$old_data = $period->to_array();

			$period->name = Input::post('name');
			$period->period_type = Input::post('period_type');
			$period->year = Input::post('year');
			$period->period_number = Input::post('period_number');
			$period->start_date = Input::post('start_date');
			$period->end_date = Input::post('end_date');
			$period->payment_date = Input::post('payment_date');
			$period->notes = Input::post('notes');

			if ($period->save()) {
				// Audit log
				\Model_Audit_Log::log_action(
					'nomina',
					$period->id,
					'update',
					'Período de nómina actualizado',
					$old_data,
					$period->to_array()
				);

				Session::set_flash('success', 'Período actualizado exitosamente');
				Response::redirect('admin/nomina/view/' . $id);
			}
		}

		$this->template->title = 'Editar Período';
		$this->template->content = View::forge('admin/nomina/edit', [
			'period' => $period,
		]);
	}

	/**
	 * Calcular nómina
	 */
	public function action_calculate($id = null)
	{
		if (!Auth::has_access('nomina.calculate')) {
			Session::set_flash('error', 'No tienes permiso para calcular nóminas');
			Response::redirect('admin/nomina');
		}

		$period = Model_Payroll_Period::find($id);

		if (!$period || $period->tenant_id != $this->tenant_id) {
			Session::set_flash('error', 'Período no encontrado');
			Response::redirect('admin/nomina');
		}

		if (!$period->can_calculate()) {
			Session::set_flash('error', 'Este período no puede ser calculado en su estado actual');
			Response::redirect('admin/nomina/view/' . $id);
		}

		if (Input::method() == 'POST') {
			$result = $period->calculate_payroll($this->current_user->id);

			if ($result['success']) {
				Session::set_flash('success', $result['message'] . ' - ' . $result['employees'] . ' empleados procesados');
			} else {
				Session::set_flash('error', $result['message']);
			}

			Response::redirect('admin/nomina/view/' . $id);
		}

		$this->template->title = 'Calcular Nómina';
		$this->template->content = View::forge('admin/nomina/calculate', [
			'period' => $period,
		]);
	}

	/**
	 * Aprobar nómina
	 */
	public function action_approve($id = null)
	{
		if (!Auth::has_access('nomina.approve')) {
			Session::set_flash('error', 'No tienes permiso para aprobar nóminas');
			Response::redirect('admin/nomina');
		}

		$period = Model_Payroll_Period::find($id);

		if (!$period || $period->tenant_id != $this->tenant_id) {
			Session::set_flash('error', 'Período no encontrado');
			Response::redirect('admin/nomina');
		}

		if (!$period->can_approve()) {
			Session::set_flash('error', 'Este período no puede ser aprobado');
			Response::redirect('admin/nomina/view/' . $id);
		}

		if (Input::method() == 'POST') {
			$period->status = 'approved';
			$period->approved_by = $this->current_user->id;
			$period->approved_at = date('Y-m-d H:i:s');

			if ($period->save()) {
				// Actualizar estado de recibos
				\DB::update('payroll_receipts')
					->value('status', 'approved')
					->where('period_id', '=', $id)
					->where('status', '=', 'pending')
					->execute();

				Session::set_flash('success', 'Nómina aprobada exitosamente');
			} else {
				Session::set_flash('error', 'Error al aprobar la nómina');
			}

			Response::redirect('admin/nomina/view/' . $id);
		}

		$this->template->title = 'Aprobar Nómina';
		$this->template->content = View::forge('admin/nomina/approve', [
			'period' => $period,
		]);
	}

	/**
	 * Eliminar período
	 */
	public function action_delete($id = null)
	{
		if (!Auth::has_access('nomina.delete')) {
			Session::set_flash('error', 'No tienes permiso para eliminar períodos');
			Response::redirect('admin/nomina');
		}

		$period = Model_Payroll_Period::find($id);

		if (!$period || $period->tenant_id != $this->tenant_id || $period->deleted_at) {
			Session::set_flash('error', 'Período no encontrado');
			Response::redirect('admin/nomina');
		}

		if (!in_array($period->status, ['draft', 'in_progress'])) {
			Session::set_flash('error', 'No se puede eliminar un período procesado');
			Response::redirect('admin/nomina');
		}

		if ($period->delete()) {
			Session::set_flash('success', 'Período eliminado exitosamente');
		} else {
			Session::set_flash('error', 'Error al eliminar el período');
		}

		Response::redirect('admin/nomina');
	}

	/**
	 * Gestión de conceptos de nómina
	 */
	public function action_concepts()
	{
		if (!Auth::has_access('nomina.concepts')) {
			Session::set_flash('error', 'No tienes permiso para gestionar conceptos');
			Response::redirect('admin/nomina');
		}

		$concepts = Model_Payroll_Concept::query()
			->where('tenant_id', '=', $this->tenant_id)
			->where('deleted_at', 'IS', null)
			->order_by('type', 'asc')
			->order_by('display_order', 'asc')
			->get();

		$this->template->title = 'Conceptos de Nómina';
		$this->template->content = View::forge('admin/nomina/concepts', [
			'concepts' => $concepts,
		]);
	}

	/**
	 * Exportar dispersión bancaria
	 */
	public function action_export($id = null)
	{
		if (!Auth::has_access('nomina.export')) {
			Session::set_flash('error', 'No tienes permiso para exportar');
			Response::redirect('admin/nomina');
		}

		$period = Model_Payroll_Period::find($id);

		if (!$period || $period->tenant_id != $this->tenant_id) {
			Session::set_flash('error', 'Período no encontrado');
			Response::redirect('admin/nomina');
		}

		if (!in_array($period->status, ['approved', 'paid', 'closed'])) {
			Session::set_flash('error', 'El período debe estar aprobado para exportar');
			Response::redirect('admin/nomina/view/' . $id);
		}

		// Obtener recibos
		$receipts = Model_Payroll_Receipt::query()
			->where('period_id', '=', $id)
			->where('status', '!=', 'cancelled')
			->where('deleted_at', 'IS', null)
			->get();

		// Generar archivo TXT de dispersión
		$content = $this->generate_dispersion_file($period, $receipts);

		$filename = 'dispersion_' . $period->code . '_' . date('YmdHis') . '.txt';

		return Response::forge($content, 200, [
			'Content-Type' => 'text/plain',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
		]);
	}

	/**
	 * Generar archivo de dispersión
	 */
	private function generate_dispersion_file($period, $receipts)
	{
		$lines = [];
		$lines[] = '# ARCHIVO DE DISPERSIÓN BANCARIA';
		$lines[] = '# Período: ' . $period->name;
		$lines[] = '# Fecha: ' . date('Y-m-d H:i:s');
		$lines[] = '# Total empleados: ' . count($receipts);
		$lines[] = '# Monto total: $' . number_format($period->total_net, 2);
		$lines[] = '';

		foreach ($receipts as $receipt) {
			// Formato: CLABE|MONTO|REFERENCIA|NOMBRE
			$employee = $receipt->employee;
			$clabe = $employee ? $employee->clabe : '';
			
			$lines[] = sprintf('%s|%.2f|%s|%s',
				$clabe,
				$receipt->net_payment,
				$receipt->receipt_number,
				$receipt->employee_name
			);
		}

		return implode("\n", $lines);
	}
}
