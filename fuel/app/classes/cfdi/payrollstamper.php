<?php

/**
 * Clase para Timbrado de CFDI de Nómina
 * Integración con PAC (Proveedor Autorizado de Certificación)
 * 
 * Soporta múltiples PACs:
 * - Finkok
 * - SW Sapien
 * - Ecodex
 * - Otros (mediante adaptadores)
 */
class Cfdi_Payroll_Stamper
{
	private $pac_provider;
	private $pac_config;
	private $tenant_id;

	/**
	 * Constructor
	 */
	public function __construct($tenant_id)
	{
		$this->tenant_id = $tenant_id;
		$this->pac_provider = Config::get('cfdi.pac_provider', 'finkok'); // finkok, sw, ecodex
		$this->pac_config = Config::get('cfdi.pac_config.' . $this->pac_provider, array());
		
		// Validar configuración
		if (empty($this->pac_config)) {
			throw new Exception('Configuración del PAC no encontrada');
		}
	}

	/**
	 * Timbrar recibo de nómina
	 */
	public function stamp_receipt($receipt_id)
	{
		$receipt = Model_Payroll_Receipt::find($receipt_id);

		if (!$receipt || $receipt->tenant_id != $this->tenant_id) {
			throw new Exception('Recibo no encontrado');
		}

		if ($receipt->is_stamped) {
			throw new Exception('Este recibo ya está timbrado');
		}

		// Validar que el período esté aprobado
		$period = $receipt->period;
		if ($period->status != 'approved' && $period->status != 'paid') {
			throw new Exception('Solo se pueden timbrar recibos de períodos aprobados o pagados');
		}

		try {
			// 1. Generar XML de nómina según especificaciones SAT
			$xml = $this->generate_nomina_xml($receipt);

			// 2. Timbrar con el PAC
			$result = $this->send_to_pac($xml);

			// 3. Guardar resultado
			if ($result['success']) {
				$receipt->is_stamped = true;
				$receipt->cfdi_uuid = $result['uuid'];
				$receipt->cfdi_xml = $result['xml'];
				$receipt->cfdi_pdf = $result['pdf'] ?? null;
				$receipt->stamped_at = time();
				$receipt->save();

				// Audit log
				\Model_Audit_Log::log_action(
					'nomina_receipt',
					$receipt->id,
					'stamp',
					'Recibo timbrado exitosamente. UUID: ' . $result['uuid'],
					null,
					array('uuid' => $result['uuid'])
				);

				return array(
					'success' => true,
					'uuid' => $result['uuid'],
					'xml' => $result['xml'],
					'pdf' => $result['pdf'] ?? null,
					'message' => 'Recibo timbrado exitosamente'
				);
			} else {
				throw new Exception($result['error'] ?? 'Error desconocido al timbrar');
			}
		} catch (Exception $e) {
			// Log del error
			\Model_Audit_Log::log_action(
				'nomina_receipt',
				$receipt->id,
				'stamp_error',
				'Error al timbrar: ' . $e->getMessage(),
				null,
				null
			);

			return array(
				'success' => false,
				'error' => $e->getMessage()
			);
		}
	}

	/**
	 * Generar XML de nómina según especificaciones SAT
	 */
	private function generate_nomina_xml($receipt)
	{
		// Obtener información del emisor (empresa)
		$tenant = Model_Tenant::find($this->tenant_id);
		$period = $receipt->period;
		$employee = Model_Employee::find_by_code($receipt->employee_code, $this->tenant_id);

		// Obtener detalles del recibo
		$details = Model_Payroll_Receipt_Detail::query()
			->where('payroll_receipt_id', '=', $receipt->id)
			->where('tenant_id', '=', $this->tenant_id)
			->where('deleted_at', 'IS', null)
			->get();

		// Crear XML base
		$xml = new \DOMDocument('1.0', 'UTF-8');
		$xml->formatOutput = true;

		// Comprobante (raíz)
		$comprobante = $xml->createElementNS('http://www.sat.gob.mx/cfd/4', 'cfdi:Comprobante');
		$xml->appendChild($comprobante);

		// Atributos del comprobante
		$comprobante->setAttribute('Version', '4.0');
		$comprobante->setAttribute('Serie', 'NOM');
		$comprobante->setAttribute('Folio', $receipt->receipt_number);
		$comprobante->setAttribute('Fecha', date('Y-m-d\TH:i:s', $receipt->created_at));
		$comprobante->setAttribute('TipoDeComprobante', 'N'); // N = Nómina
		$comprobante->setAttribute('LugarExpedicion', $tenant->zip_code ?? '00000');
		$comprobante->setAttribute('SubTotal', number_format($receipt->total_perceptions, 2, '.', ''));
		$comprobante->setAttribute('Descuento', number_format($receipt->total_deductions, 2, '.', ''));
		$comprobante->setAttribute('Total', number_format($receipt->net_payment, 2, '.', ''));
		$comprobante->setAttribute('Moneda', 'MXN');
		$comprobante->setAttribute('Exportacion', '01'); // No aplica

		// Namespace adicionales
		$comprobante->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:nomina12', 'http://www.sat.gob.mx/nomina12');

		// Emisor
		$emisor = $xml->createElement('cfdi:Emisor');
		$emisor->setAttribute('Rfc', $tenant->rfc ?? 'XAXX010101000');
		$emisor->setAttribute('Nombre', $tenant->business_name ?? $tenant->name);
		$emisor->setAttribute('RegimenFiscal', $tenant->fiscal_regime ?? '601'); // 601 = General de Ley Personas Morales
		$comprobante->appendChild($emisor);

		// Receptor
		$receptor = $xml->createElement('cfdi:Receptor');
		$receptor->setAttribute('Rfc', $receipt->rfc ?? 'XAXX010101000');
		$receptor->setAttribute('Nombre', $receipt->employee_name);
		$receptor->setAttribute('DomicilioFiscalReceptor', $employee->zip_code ?? '00000');
		$receptor->setAttribute('RegimenFiscalReceptor', '605'); // 605 = Sueldos y Salarios e Ingresos Asimilados a Salarios
		$receptor->setAttribute('UsoCFDI', 'CN01'); // CN01 = Nómina
		$comprobante->appendChild($receptor);

		// Conceptos (siempre 1 concepto en nómina)
		$conceptos = $xml->createElement('cfdi:Conceptos');
		$concepto = $xml->createElement('cfdi:Concepto');
		$concepto->setAttribute('ClaveProdServ', '84111505'); // Clave SAT para servicios de personal
		$concepto->setAttribute('Cantidad', '1');
		$concepto->setAttribute('ClaveUnidad', 'ACT'); // Actividad
		$concepto->setAttribute('Descripcion', 'Pago de nómina');
		$concepto->setAttribute('ValorUnitario', number_format($receipt->total_perceptions, 2, '.', ''));
		$concepto->setAttribute('Importe', number_format($receipt->total_perceptions, 2, '.', ''));
		$concepto->setAttribute('Descuento', number_format($receipt->total_deductions, 2, '.', ''));
		$conceptos->appendChild($concepto);
		$comprobante->appendChild($conceptos);

		// Complemento de Nómina
		$complemento = $xml->createElement('cfdi:Complemento');
		$nomina = $xml->createElement('nomina12:Nomina');
		
		// Atributos de nómina
		$nomina->setAttribute('Version', '1.2');
		$nomina->setAttribute('TipoNomina', 'O'); // O = Ordinaria
		$nomina->setAttribute('FechaPago', date('Y-m-d', $period->payment_date));
		$nomina->setAttribute('FechaInicialPago', date('Y-m-d', $period->start_date));
		$nomina->setAttribute('FechaFinalPago', date('Y-m-d', $period->end_date));
		$nomina->setAttribute('NumDiasPagados', number_format($receipt->worked_days, 3, '.', ''));
		$nomina->setAttribute('TotalPercepciones', number_format($receipt->total_perceptions, 2, '.', ''));
		$nomina->setAttribute('TotalDeducciones', number_format($receipt->total_deductions, 2, '.', ''));

		// Emisor de nómina
		$emisorNomina = $xml->createElement('nomina12:Emisor');
		$emisorNomina->setAttribute('RegistroPatronal', $tenant->employer_registration ?? '');
		$nomina->appendChild($emisorNomina);

		// Receptor de nómina
		$receptorNomina = $xml->createElement('nomina12:Receptor');
		$receptorNomina->setAttribute('Curp', $receipt->curp ?? '');
		$receptorNomina->setAttribute('NumSeguridadSocial', $receipt->nss ?? '');
		$receptorNomina->setAttribute('FechaInicioRelLaboral', date('Y-m-d', $employee->hire_date ?? time()));
		$receptorNomina->setAttribute('Antigüedad', $this->calculate_seniority($employee->hire_date ?? time()));
		$receptorNomina->setAttribute('TipoContrato', $this->get_contract_type($employee->employment_type ?? 'permanent'));
		$receptorNomina->setAttribute('TipoJornada', '01'); // 01 = Diurna
		$receptorNomina->setAttribute('TipoRegimen', '02'); // 02 = Sueldos
		$receptorNomina->setAttribute('NumEmpleado', $receipt->employee_code);
		$receptorNomina->setAttribute('Departamento', $receipt->department_name ?? '');
		$receptorNomina->setAttribute('Puesto', $receipt->position_name ?? '');
		$receptorNomina->setAttribute('RiesgoPuesto', '1'); // 1 = Clase I
		$receptorNomina->setAttribute('PeriodicidadPago', $this->get_periodicity($period->period_type));
		$receptorNomina->setAttribute('SalarioBaseCotApor', number_format($receipt->daily_salary ?? 0, 2, '.', ''));
		$receptorNomina->setAttribute('SalarioDiarioIntegrado', number_format($receipt->daily_salary ?? 0, 2, '.', ''));
		$nomina->appendChild($receptorNomina);

		// Percepciones
		$percepciones = $xml->createElement('nomina12:Percepciones');
		$percepciones->setAttribute('TotalSueldos', number_format($receipt->total_perceptions, 2, '.', ''));
		$percepciones->setAttribute('TotalGravado', number_format($receipt->total_perceptions * 0.8, 2, '.', '')); // Aproximado
		$percepciones->setAttribute('TotalExento', number_format($receipt->total_perceptions * 0.2, 2, '.', '')); // Aproximado

		foreach ($details as $detail) {
			if ($detail->type == 'perception') {
				$percepcion = $xml->createElement('nomina12:Percepcion');
				$percepcion->setAttribute('TipoPercepcion', $this->map_perception_type($detail->concept_code));
				$percepcion->setAttribute('Clave', $detail->concept_code);
				$percepcion->setAttribute('Concepto', $detail->concept_name);
				$percepcion->setAttribute('ImporteGravado', number_format($detail->amount * 0.8, 2, '.', ''));
				$percepcion->setAttribute('ImporteExento', number_format($detail->amount * 0.2, 2, '.', ''));
				$percepciones->appendChild($percepcion);
			}
		}
		$nomina->appendChild($percepciones);

		// Deducciones
		$deducciones = $xml->createElement('nomina12:Deducciones');
		$deducciones->setAttribute('TotalOtrasDeducciones', number_format($receipt->total_deductions, 2, '.', ''));

		foreach ($details as $detail) {
			if ($detail->type == 'deduction') {
				$deduccion = $xml->createElement('nomina12:Deduccion');
				$deduccion->setAttribute('TipoDeduccion', $this->map_deduction_type($detail->concept_code));
				$deduccion->setAttribute('Clave', $detail->concept_code);
				$deduccion->setAttribute('Concepto', $detail->concept_name);
				$deduccion->setAttribute('Importe', number_format($detail->amount, 2, '.', ''));
				$deducciones->appendChild($deduccion);
			}
		}
		$nomina->appendChild($deducciones);

		$complemento->appendChild($nomina);
		$comprobante->appendChild($complemento);

		return $xml->saveXML();
	}

	/**
	 * Enviar XML al PAC para timbrado
	 */
	private function send_to_pac($xml)
	{
		switch ($this->pac_provider) {
			case 'finkok':
				return $this->stamp_with_finkok($xml);
			case 'sw':
				return $this->stamp_with_sw($xml);
			case 'ecodex':
				return $this->stamp_with_ecodex($xml);
			default:
				throw new Exception('Proveedor PAC no soportado: ' . $this->pac_provider);
		}
	}

	/**
	 * Timbrar con Finkok
	 */
	private function stamp_with_finkok($xml)
	{
		$username = $this->pac_config['username'];
		$password = $this->pac_config['password'];
		$wsdl = $this->pac_config['wsdl'] ?? 'https://facturacion.finkok.com/servicios/soap/stamp.wsdl';

		try {
			$client = new \SoapClient($wsdl, array('trace' => 1));
			
			$params = array(
				'username' => $username,
				'password' => $password,
				'xml' => base64_encode($xml)
			);

			$response = $client->stamp($params);

			if (isset($response->stampResult->UUID)) {
				return array(
					'success' => true,
					'uuid' => $response->stampResult->UUID,
					'xml' => base64_decode($response->stampResult->xml),
					'pdf' => null // Finkok no devuelve PDF directamente
				);
			} else {
				return array(
					'success' => false,
					'error' => $response->stampResult->Incidencias->Incidencia->MensajeIncidencia ?? 'Error desconocido'
				);
			}
		} catch (Exception $e) {
			return array(
				'success' => false,
				'error' => 'Error de conexión con Finkok: ' . $e->getMessage()
			);
		}
	}

	/**
	 * Timbrar con SW Sapien
	 */
	private function stamp_with_sw($xml)
	{
		$token = $this->pac_config['token'];
		$url = $this->pac_config['url'] ?? 'https://api.sw.com.mx/cfdi33/issue/v4';

		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Bearer ' . $token,
				'Content-Type: application/json'
			));
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
				'xml' => base64_encode($xml)
			)));

			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$result = json_decode($response, true);

			if ($httpCode == 200 && isset($result['data']['uuid'])) {
				return array(
					'success' => true,
					'uuid' => $result['data']['uuid'],
					'xml' => base64_decode($result['data']['cfdi']),
					'pdf' => null
				);
			} else {
				return array(
					'success' => false,
					'error' => $result['message'] ?? 'Error desconocido'
				);
			}
		} catch (Exception $e) {
			return array(
				'success' => false,
				'error' => 'Error de conexión con SW: ' . $e->getMessage()
			);
		}
	}

	/**
	 * Timbrar con Ecodex (implementación básica)
	 */
	private function stamp_with_ecodex($xml)
	{
		// Implementación similar a los anteriores
		throw new Exception('Ecodex no implementado aún. Contacte al administrador.');
	}

	/**
	 * Mapear tipo de percepción a catálogo SAT
	 */
	private function map_perception_type($code)
	{
		$mapping = array(
			'P001' => '001', // Sueldos, Salarios
			'P002' => '019', // Horas extra
			'P003' => '010', // Prima dominical
			'P004' => '002', // Aguinaldo
			'P005' => '003', // Prima vacacional
			'P006' => '013', // Bonificación
			'P007' => '028', // Vales de despensa
			'P008' => '046', // Comisiones
		);

		return $mapping[$code] ?? '038'; // 038 = Otras percepciones
	}

	/**
	 * Mapear tipo de deducción a catálogo SAT
	 */
	private function map_deduction_type($code)
	{
		$mapping = array(
			'D001' => '002', // ISR
			'D002' => '001', // IMSS
			'D003' => '004', // INFONAVIT
			'D004' => '007', // Préstamo INFONAVIT
			'D005' => '019', // Pensión alimenticia
		);

		return $mapping[$code] ?? '004'; // 004 = Otras deducciones
	}

	/**
	 * Obtener periodicidad según tipo de período
	 */
	private function get_periodicity($period_type)
	{
		$mapping = array(
			'weekly' => '01',    // Semanal
			'biweekly' => '02',  // Catorcenal
			'monthly' => '04'    // Mensual
		);

		return $mapping[$period_type] ?? '99'; // 99 = Otra periodicidad
	}

	/**
	 * Obtener tipo de contrato
	 */
	private function get_contract_type($employment_type)
	{
		$mapping = array(
			'permanent' => '01', // Contrato por tiempo indeterminado
			'temporary' => '02', // Contrato por obra determinada
			'honorarium' => '09' // Por honorarios
		);

		return $mapping[$employment_type] ?? '01';
	}

	/**
	 * Calcular antigüedad en formato ISO 8601
	 */
	private function calculate_seniority($hire_date)
	{
		$start = new DateTime(date('Y-m-d', $hire_date));
		$end = new DateTime();
		$interval = $start->diff($end);
		
		return 'P' . $interval->y . 'Y' . $interval->m . 'M' . $interval->d . 'D';
	}

	/**
	 * Cancelar timbrado (en caso de error)
	 */
	public function cancel_stamp($receipt_id, $cancellation_reason = '01')
	{
		$receipt = Model_Payroll_Receipt::find($receipt_id);

		if (!$receipt || !$receipt->is_stamped) {
			throw new Exception('Recibo no encontrado o no está timbrado');
		}

		// Implementar lógica de cancelación según el PAC
		// Cada PAC tiene su propio método de cancelación
		
		return array(
			'success' => false,
			'error' => 'Función de cancelación no implementada aún'
		);
	}
}
