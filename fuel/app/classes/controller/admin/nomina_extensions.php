<?php

/**
 * Trait para extender el Controlador de Nómina
 * Acciones adicionales: preview_calculate, preview_approve, export_excel, generate_pdf
 * 
 * Para usar: agregar "use Trait_Nomina_Extensions;" dentro de Controller_Admin_Nomina
 */

trait Trait_Nomina_Extensions
{
	/**
	 * Vista previa para calcular nómina
	 */
	public function action_preview_calculate($id = null)
{
	if (!Auth::has_access('nomina.calculate')) {
		Session::set_flash('error', 'No tienes permiso para calcular nómina');
		Response::redirect('admin/nomina');
	}

	$period = Model_Payroll_Period::find($id);

	if (!$period || $period->tenant_id != $this->tenant_id || $period->deleted_at) {
		Session::set_flash('error', 'Período no encontrado');
		Response::redirect('admin/nomina');
	}

	// Obtener empleados activos
	$active_employees = $period->get_active_employees();

	// Estadísticas
	$stats = array(
		'total_employees' => count($active_employees),
		'with_department' => 0,
		'without_department' => 0,
		'with_salary' => 0,
		'without_salary' => 0,
		'total_salary' => 0,
		'avg_salary' => 0,
		'max_salary' => 0,
		'min_salary' => PHP_INT_MAX,
	);

	foreach ($active_employees as $employee) {
		if ($employee->department_id) {
			$stats['with_department']++;
		} else {
			$stats['without_department']++;
		}

		if ($employee->base_salary > 0) {
			$stats['with_salary']++;
			$stats['total_salary'] += $employee->base_salary;
			$stats['max_salary'] = max($stats['max_salary'], $employee->base_salary);
			$stats['min_salary'] = min($stats['min_salary'], $employee->base_salary);
		} else {
			$stats['without_salary']++;
		}
	}

	$stats['avg_salary'] = $stats['with_salary'] > 0 ? $stats['total_salary'] / $stats['with_salary'] : 0;
	if ($stats['min_salary'] == PHP_INT_MAX) $stats['min_salary'] = 0;

	// Obtener conceptos activos
	$perceptions = Model_Payroll_Concept::query()
		->where('tenant_id', '=', $this->tenant_id)
		->where('type', '=', 'perception')
		->where('is_active', '=', 1)
		->where('deleted_at', 'IS', null)
		->get();

	$deductions = Model_Payroll_Concept::query()
		->where('tenant_id', '=', $this->tenant_id)
		->where('type', '=', 'deduction')
		->where('is_active', '=', 1)
		->where('deleted_at', 'IS', null)
		->get();

	$this->template->title = 'Calcular Nómina - ' . $period->name;
	$this->template->content = View::forge('admin/nomina/calculate', array(
		'period' => $period,
		'active_employees' => $active_employees,
		'stats' => $stats,
		'perceptions' => $perceptions,
		'deductions' => $deductions,
	));
}

/**
 * Vista previa para aprobar nómina
 */
public function action_preview_approve($id = null)
{
	if (!Auth::has_access('nomina.approve')) {
		Session::set_flash('error', 'No tienes permiso para aprobar nómina');
		Response::redirect('admin/nomina');
	}

	$period = Model_Payroll_Period::find($id);

	if (!$period || $period->tenant_id != $this->tenant_id || $period->deleted_at) {
		Session::set_flash('error', 'Período no encontrado');
		Response::redirect('admin/nomina');
	}

	// Obtener recibos del período
	$receipts = Model_Payroll_Receipt::query()
		->where('payroll_period_id', '=', $period->id)
		->where('tenant_id', '=', $this->tenant_id)
		->where('deleted_at', 'IS', null)
		->order_by('employee_code', 'asc')
		->get();

	// Calcular resumen
	$summary = array(
		'total_employees' => count($receipts),
		'total_perceptions' => 0,
		'total_deductions' => 0,
		'total_net' => 0,
	);

	foreach ($receipts as $receipt) {
		$summary['total_perceptions'] += $receipt->total_perceptions;
		$summary['total_deductions'] += $receipt->total_deductions;
		$summary['total_net'] += $receipt->net_payment;
	}

	// Resumen por departamento
	$by_department = DB::select(
			DB::expr('COALESCE(department_name, "Sin Departamento") as department_name'),
			DB::expr('COUNT(*) as employees'),
			DB::expr('SUM(total_perceptions) as perceptions'),
			DB::expr('SUM(total_deductions) as deductions'),
			DB::expr('SUM(net_payment) as net'),
			DB::expr('AVG(net_payment) as average')
		)
		->from('payroll_receipts')
		->where('payroll_period_id', '=', $period->id)
		->where('tenant_id', '=', $this->tenant_id)
		->where('deleted_at', 'IS', null)
		->group_by('department_name')
		->execute()
		->as_array();

	// Alertas y validaciones
	$alerts = array();
	
	if ($summary['total_employees'] == 0) {
		$alerts[] = 'No hay recibos calculados para este período';
	}

	// Verificar empleados sin departamento
	$without_dept = DB::select(DB::expr('COUNT(*) as count'))
		->from('payroll_receipts')
		->where('payroll_period_id', '=', $period->id)
		->where('tenant_id', '=', $this->tenant_id)
		->where('department_name', 'IS', null)
		->where('deleted_at', 'IS', null)
		->execute()
		->get('count');

	if ($without_dept > 0) {
		$alerts[] = $without_dept . ' empleado(s) sin departamento asignado';
	}

	$this->template->title = 'Aprobar Nómina - ' . $period->name;
	$this->template->content = View::forge('admin/nomina/approve', array(
		'period' => $period,
		'receipts' => $receipts,
		'summary' => $summary,
		'by_department' => $by_department,
		'alerts' => $alerts,
	));
}

/**
 * Exportar nómina a Excel
 */
public function action_export_excel($id = null)
{
	if (!Auth::has_access('nomina.export')) {
		Session::set_flash('error', 'No tienes permiso para exportar');
		Response::redirect('admin/nomina');
	}

	$period = Model_Payroll_Period::find($id);

	if (!$period || $period->tenant_id != $this->tenant_id || $period->deleted_at) {
		Session::set_flash('error', 'Período no encontrado');
		Response::redirect('admin/nomina');
	}

	// Verificar si existe PHPSpreadsheet
	if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
		Session::set_flash('error', 'La librería PHPSpreadsheet no está instalada. Ejecute: composer require phpoffice/phpspreadsheet');
		Response::redirect('admin/nomina/view/' . $id);
	}

	require_once VENDORPATH . 'autoload.php';

	$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();

	// Título
	$sheet->setCellValue('A1', 'NÓMINA - ' . strtoupper($period->name));
	$sheet->mergeCells('A1:K1');
	$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
	$sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

	// Información del período
	$sheet->setCellValue('A2', 'Código:');
	$sheet->setCellValue('B2', $period->code);
	$sheet->setCellValue('D2', 'Tipo:');
	$sheet->setCellValue('E2', $period->get_period_type_label());
	$sheet->setCellValue('G2', 'Del:');
	$sheet->setCellValue('H2', date('d/m/Y', $period->start_date));
	$sheet->setCellValue('I2', 'Al:');
	$sheet->setCellValue('J2', date('d/m/Y', $period->end_date));

	$sheet->setCellValue('A3', 'Fecha de Pago:');
	$sheet->setCellValue('B3', date('d/m/Y', $period->payment_date));
	$sheet->setCellValue('D3', 'Estado:');
	$sheet->setCellValue('E3', $period->get_status_label());

	// Encabezados
	$row = 5;
	$headers = array('No.', 'Código', 'Nombre', 'Departamento', 'Puesto', 'Salario Base', 'Días', 'Percepciones', 'Deducciones', 'Neto', 'CLABE');
	$col = 'A';
	
	foreach ($headers as $header) {
		$sheet->setCellValue($col . $row, $header);
		$sheet->getStyle($col . $row)->getFont()->setBold(true);
		$sheet->getStyle($col . $row)->getFill()
			->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
			->getStartColor()->setARGB('FF4472C4');
		$sheet->getStyle($col . $row)->getFont()->getColor()->setARGB('FFFFFFFF');
		$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$col++;
	}

	// Obtener recibos
	$receipts = Model_Payroll_Receipt::query()
		->where('payroll_period_id', '=', $period->id)
		->where('tenant_id', '=', $this->tenant_id)
		->where('deleted_at', 'IS', null)
		->order_by('employee_code', 'asc')
		->get();

	// Datos
	$row = 6;
	$num = 1;
	$totals = array(
		'base_salary' => 0,
		'perceptions' => 0,
		'deductions' => 0,
		'net' => 0,
	);

	foreach ($receipts as $receipt) {
		$sheet->setCellValue('A' . $row, $num++);
		$sheet->setCellValue('B' . $row, $receipt->employee_code);
		$sheet->setCellValue('C' . $row, $receipt->employee_name);
		$sheet->setCellValue('D' . $row, $receipt->department_name ?? 'N/A');
		$sheet->setCellValue('E' . $row, $receipt->position_name ?? 'N/A');
		$sheet->setCellValue('F' . $row, $receipt->base_salary);
		$sheet->setCellValue('G' . $row, $receipt->worked_days);
		$sheet->setCellValue('H' . $row, $receipt->total_perceptions);
		$sheet->setCellValue('I' . $row, $receipt->total_deductions);
		$sheet->setCellValue('J' . $row, $receipt->net_payment);
		$sheet->setCellValue('K' . $row, $receipt->bank_account ?? '');

		// Formato de moneda
		$sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
		$sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
		$sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
		$sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');

		$totals['base_salary'] += $receipt->base_salary;
		$totals['perceptions'] += $receipt->total_perceptions;
		$totals['deductions'] += $receipt->total_deductions;
		$totals['net'] += $receipt->net_payment;

		$row++;
	}

	// Totales
	$sheet->setCellValue('A' . $row, 'TOTALES');
	$sheet->mergeCells('A' . $row . ':E' . $row);
	$sheet->setCellValue('F' . $row, $totals['base_salary']);
	$sheet->setCellValue('H' . $row, $totals['perceptions']);
	$sheet->setCellValue('I' . $row, $totals['deductions']);
	$sheet->setCellValue('J' . $row, $totals['net']);

	$sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);
	$sheet->getStyle('A' . $row . ':K' . $row)->getFill()
		->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
		->getStartColor()->setARGB('FFE2EFDA');
	$sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
	$sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
	$sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
	$sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');

	// Bordes
	$sheet->getStyle('A5:K' . $row)->getBorders()->getAllBorders()
		->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

	// Ajustar ancho de columnas
	foreach(range('A','K') as $col) {
		$sheet->getColumnDimension($col)->setAutoSize(true);
	}

	// Generar archivo
	$filename = 'Nomina_' . $period->code . '_' . date('Ymd_His') . '.xlsx';
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="' . $filename . '"');
	header('Cache-Control: max-age=0');

	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	$writer->save('php://output');
	exit;
}

/**
 * Generar PDF de recibo individual
 */
public function action_generate_pdf($receipt_id = null)
{
	if (!Auth::has_access('nomina.view')) {
		Session::set_flash('error', 'No tienes permiso para ver recibos');
		Response::redirect('admin/nomina');
	}

	$receipt = Model_Payroll_Receipt::find($receipt_id);

	if (!$receipt || $receipt->tenant_id != $this->tenant_id || $receipt->deleted_at) {
		Session::set_flash('error', 'Recibo no encontrado');
		Response::redirect('admin/nomina');
	}

	// Verificar si existe TCPDF
	if (!class_exists('TCPDF')) {
		Session::set_flash('error', 'La librería TCPDF no está instalada. Ejecute: composer require tecnickcom/tcpdf');
		Response::redirect('admin/nomina/view/' . $receipt->payroll_period_id);
	}

	require_once VENDORPATH . 'autoload.php';

	// Obtener período
	$period = $receipt->period;

	// Obtener detalles
	$details = Model_Payroll_Receipt_Detail::query()
		->where('payroll_receipt_id', '=', $receipt->id)
		->where('tenant_id', '=', $this->tenant_id)
		->where('deleted_at', 'IS', null)
		->order_by('type', 'asc')
		->order_by('concept_code', 'asc')
		->get();

	// Crear PDF
	$pdf = new \TCPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);

	// Configuración del documento
	$pdf->SetCreator('Sistema ERP');
	$pdf->SetAuthor('Sistema de Nómina');
	$pdf->SetTitle('Recibo de Nómina - ' . $receipt->employee_code);
	$pdf->SetSubject('Recibo de Nómina');

	// Quitar header y footer por defecto
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

	// Márgenes
	$pdf->SetMargins(15, 15, 15);
	$pdf->SetAutoPageBreak(true, 15);

	// Agregar página
	$pdf->AddPage();

	// Contenido HTML del recibo
	$html = '
<style>
	h1 { font-size: 18pt; text-align: center; color: #2c3e50; }
	h2 { font-size: 14pt; color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
	table { border-collapse: collapse; width: 100%; }
	th { background-color: #3498db; color: white; padding: 8px; text-align: left; }
	td { padding: 6px; border-bottom: 1px solid #ddd; }
	.text-right { text-align: right; }
	.text-center { text-align: center; }
	.total { font-weight: bold; background-color: #ecf0f1; }
	.perception { color: #27ae60; }
	.deduction { color: #e74c3c; }
	.net { background-color: #3498db; color: white; font-size: 14pt; padding: 10px; }
	.info-box { background-color: #f8f9fa; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
</style>

<h1>RECIBO DE NÓMINA</h1>

<div class="info-box">
	<table>
		<tr>
			<td width="50%"><strong>Período:</strong> ' . htmlspecialchars($period->name) . '</td>
			<td width="50%"><strong>Folio:</strong> ' . htmlspecialchars($receipt->receipt_number) . '</td>
		</tr>
		<tr>
			<td><strong>Tipo:</strong> ' . $period->get_period_type_label() . '</td>
			<td><strong>Fecha de Pago:</strong> ' . date('d/m/Y', $period->payment_date) . '</td>
		</tr>
		<tr>
			<td colspan="2"><strong>Del:</strong> ' . date('d/m/Y', $period->start_date) . ' <strong>Al:</strong> ' . date('d/m/Y', $period->end_date) . '</td>
		</tr>
	</table>
</div>

<h2>Datos del Empleado</h2>
<div class="info-box">
	<table>
		<tr>
			<td width="25%"><strong>Código:</strong></td>
			<td width="25%">' . htmlspecialchars($receipt->employee_code) . '</td>
			<td width="25%"><strong>RFC:</strong></td>
			<td width="25%">' . htmlspecialchars($receipt->rfc ?? 'N/A') . '</td>
		</tr>
		<tr>
			<td><strong>Nombre:</strong></td>
			<td colspan="3">' . htmlspecialchars($receipt->employee_name) . '</td>
		</tr>
		<tr>
			<td><strong>Departamento:</strong></td>
			<td>' . htmlspecialchars($receipt->department_name ?? 'N/A') . '</td>
			<td><strong>Puesto:</strong></td>
			<td>' . htmlspecialchars($receipt->position_name ?? 'N/A') . '</td>
		</tr>
		<tr>
			<td><strong>NSS:</strong></td>
			<td>' . htmlspecialchars($receipt->nss ?? 'N/A') . '</td>
			<td><strong>CURP:</strong></td>
			<td>' . htmlspecialchars($receipt->curp ?? 'N/A') . '</td>
		</tr>
		<tr>
			<td><strong>Salario Base:</strong></td>
			<td>$' . number_format($receipt->base_salary, 2) . '</td>
			<td><strong>Días Trabajados:</strong></td>
			<td>' . number_format($receipt->worked_days, 1) . '</td>
		</tr>
	</table>
</div>

<h2>Percepciones</h2>
<table>
	<thead>
		<tr>
			<th width="15%">Código</th>
			<th width="55%">Concepto</th>
			<th width="15%" class="text-right">Cantidad</th>
			<th width="15%" class="text-right">Importe</th>
		</tr>
	</thead>
	<tbody>';

	foreach ($details as $detail) {
		if ($detail->type == 'perception') {
			$html .= '<tr>
				<td>' . htmlspecialchars($detail->concept_code) . '</td>
				<td>' . htmlspecialchars($detail->concept_name) . '</td>
				<td class="text-right">' . number_format($detail->quantity, 2) . '</td>
				<td class="text-right perception">$' . number_format($detail->amount, 2) . '</td>
			</tr>';
		}
	}

	$html .= '<tr class="total">
			<td colspan="3" class="text-right">TOTAL PERCEPCIONES:</td>
			<td class="text-right perception">$' . number_format($receipt->total_perceptions, 2) . '</td>
		</tr>
	</tbody>
</table>

<h2>Deducciones</h2>
<table>
	<thead>
		<tr>
			<th width="15%">Código</th>
			<th width="55%">Concepto</th>
			<th width="15%" class="text-right">Cantidad</th>
			<th width="15%" class="text-right">Importe</th>
		</tr>
	</thead>
	<tbody>';

	foreach ($details as $detail) {
		if ($detail->type == 'deduction') {
			$html .= '<tr>
				<td>' . htmlspecialchars($detail->concept_code) . '</td>
				<td>' . htmlspecialchars($detail->concept_name) . '</td>
				<td class="text-right">' . number_format($detail->quantity, 2) . '</td>
				<td class="text-right deduction">$' . number_format($detail->amount, 2) . '</td>
			</tr>';
		}
	}

	$html .= '<tr class="total">
			<td colspan="3" class="text-right">TOTAL DEDUCCIONES:</td>
			<td class="text-right deduction">$' . number_format($receipt->total_deductions, 2) . '</td>
		</tr>
	</tbody>
</table>

<br>
<div class="net text-center">
	<strong>NETO A PAGAR: $' . number_format($receipt->net_payment, 2) . '</strong>
</div>

<br>
<p style="font-size: 9pt; text-align: center; color: #7f8c8d;">
	Recibo generado electrónicamente el ' . date('d/m/Y H:i:s') . '<br>
	Este documento es una representación impresa de un recibo electrónico.
</p>';

	// Escribir HTML
	$pdf->writeHTML($html, true, false, true, false, '');

	// Salida del PDF
	$filename = 'Recibo_' . $receipt->employee_code . '_' . $period->code . '.pdf';
	$pdf->Output($filename, 'D'); // 'D' para descarga, 'I' para ver en navegador
	exit;
}

} // Fin del trait Trait_Nomina_Extensions

