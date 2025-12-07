<?php

/**
 * Clase para Notificaciones de Nómina
 * Sistema para notificar a empleados sobre recibos de nómina
 * 
 * Soporta múltiples canales:
 * - Email
 * - SMS (Twilio, Nexmo)
 * - Push Notifications
 * - WhatsApp Business API
 */
class Payroll_Notifier
{
	private $tenant_id;
	private $config;

	/**
	 * Constructor
	 */
	public function __construct($tenant_id)
	{
		$this->tenant_id = $tenant_id;
		$this->config = Config::get('notifications', array());
	}

	/**
	 * Notificar a empleado sobre recibo disponible
	 */
	public function notify_receipt_available($receipt_id, $channels = array('email'))
	{
		$receipt = Model_Payroll_Receipt::find($receipt_id);

		if (!$receipt || $receipt->tenant_id != $this->tenant_id) {
			throw new Exception('Recibo no encontrado');
		}

		$employee = Model_Employee::query()
			->where('employee_code', '=', $receipt->employee_code)
			->where('tenant_id', '=', $this->tenant_id)
			->where('deleted_at', 'IS', null)
			->get_one();

		if (!$employee) {
			throw new Exception('Empleado no encontrado');
		}

		$period = $receipt->period;
		$results = array();

		// Datos para la notificación
		$data = array(
			'employee_name' => $receipt->employee_name,
			'period_name' => $period->name,
			'payment_date' => date('d/m/Y', $period->payment_date),
			'net_payment' => number_format($receipt->net_payment, 2),
			'receipt_url' => Uri::create('portal/employee/receipt/' . $receipt->id),
			'pdf_url' => Uri::create('admin/nomina/generate_pdf/' . $receipt->id),
		);

		// Enviar por cada canal solicitado
		foreach ($channels as $channel) {
			try {
				switch ($channel) {
					case 'email':
						$results['email'] = $this->send_email_notification($employee, $data, $receipt);
						break;
					case 'sms':
						$results['sms'] = $this->send_sms_notification($employee, $data);
						break;
					case 'push':
						$results['push'] = $this->send_push_notification($employee, $data);
						break;
					case 'whatsapp':
						$results['whatsapp'] = $this->send_whatsapp_notification($employee, $data);
						break;
				}
			} catch (Exception $e) {
				$results[$channel] = array(
					'success' => false,
					'error' => $e->getMessage()
				);
			}
		}

		// Log de notificación
		\Model_Audit_Log::log_action(
			'nomina_notification',
			$receipt->id,
			'notify',
			'Notificación enviada a ' . $employee->email,
			null,
			$results
		);

		return $results;
	}

	/**
	 * Enviar notificación por email
	 */
	private function send_email_notification($employee, $data, $receipt)
	{
		if (!$employee->email) {
			return array('success' => false, 'error' => 'Empleado sin email configurado');
		}

		// Generar PDF adjunto
		$pdf_path = null;
		if (Config::get('notifications.attach_pdf', true)) {
			$pdf_path = $this->generate_pdf_attachment($receipt);
		}

		// Plantilla de email
		$subject = 'Tu recibo de nómina está disponible - ' . $data['period_name'];
		
		$html_body = View::forge('emails/payroll_receipt', array(
			'employee_name' => $data['employee_name'],
			'period_name' => $data['period_name'],
			'payment_date' => $data['payment_date'],
			'net_payment' => $data['net_payment'],
			'receipt_url' => $data['receipt_url'],
			'receipt' => $receipt,
		))->render();

		// Enviar email usando Email class de FuelPHP
		$email = Email::forge();
		$email->from(Config::get('notifications.from_email', 'noreply@empresa.com'), Config::get('notifications.from_name', 'Sistema de Nómina'));
		$email->to($employee->email, $employee->first_name . ' ' . $employee->last_name);
		$email->subject($subject);
		$email->html_body($html_body);

		// Adjuntar PDF si existe
		if ($pdf_path && file_exists($pdf_path)) {
			$email->attach($pdf_path);
		}

		try {
			$result = $email->send();
			
			// Limpiar archivo temporal
			if ($pdf_path && file_exists($pdf_path)) {
				unlink($pdf_path);
			}

			return array(
				'success' => $result,
				'channel' => 'email',
				'recipient' => $employee->email
			);
		} catch (Exception $e) {
			return array(
				'success' => false,
				'error' => $e->getMessage()
			);
		}
	}

	/**
	 * Enviar notificación por SMS
	 */
	private function send_sms_notification($employee, $data)
	{
		if (!$employee->phone) {
			return array('success' => false, 'error' => 'Empleado sin teléfono configurado');
		}

		$provider = Config::get('notifications.sms_provider', 'twilio'); // twilio, nexmo
		$message = "Hola {$data['employee_name']}, tu recibo de nómina de {$data['period_name']} está disponible. Neto a pagar: \${$data['net_payment']}. Fecha de pago: {$data['payment_date']}.";

		switch ($provider) {
			case 'twilio':
				return $this->send_twilio_sms($employee->phone, $message);
			case 'nexmo':
				return $this->send_nexmo_sms($employee->phone, $message);
			default:
				return array('success' => false, 'error' => 'Proveedor SMS no configurado');
		}
	}

	/**
	 * Enviar SMS usando Twilio
	 */
	private function send_twilio_sms($phone, $message)
	{
		$account_sid = Config::get('notifications.twilio.account_sid');
		$auth_token = Config::get('notifications.twilio.auth_token');
		$from_number = Config::get('notifications.twilio.from_number');

		if (!$account_sid || !$auth_token || !$from_number) {
			return array('success' => false, 'error' => 'Twilio no configurado');
		}

		try {
			$url = "https://api.twilio.com/2010-04-01/Accounts/{$account_sid}/Messages.json";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_USERPWD, $account_sid . ':' . $auth_token);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
				'From' => $from_number,
				'To' => $phone,
				'Body' => $message
			)));

			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$result = json_decode($response, true);

			if ($httpCode == 201) {
				return array(
					'success' => true,
					'channel' => 'sms',
					'provider' => 'twilio',
					'recipient' => $phone,
					'message_id' => $result['sid'] ?? null
				);
			} else {
				return array(
					'success' => false,
					'error' => $result['message'] ?? 'Error al enviar SMS'
				);
			}
		} catch (Exception $e) {
			return array(
				'success' => false,
				'error' => 'Error de conexión con Twilio: ' . $e->getMessage()
			);
		}
	}

	/**
	 * Enviar SMS usando Nexmo/Vonage
	 */
	private function send_nexmo_sms($phone, $message)
	{
		$api_key = Config::get('notifications.nexmo.api_key');
		$api_secret = Config::get('notifications.nexmo.api_secret');
		$from_number = Config::get('notifications.nexmo.from_number', 'NOMINA');

		if (!$api_key || !$api_secret) {
			return array('success' => false, 'error' => 'Nexmo no configurado');
		}

		try {
			$url = 'https://rest.nexmo.com/sms/json';
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
				'api_key' => $api_key,
				'api_secret' => $api_secret,
				'from' => $from_number,
				'to' => $phone,
				'text' => $message
			)));

			$response = curl_exec($ch);
			curl_close($ch);

			$result = json_decode($response, true);

			if (isset($result['messages'][0]['status']) && $result['messages'][0]['status'] == '0') {
				return array(
					'success' => true,
					'channel' => 'sms',
					'provider' => 'nexmo',
					'recipient' => $phone,
					'message_id' => $result['messages'][0]['message-id'] ?? null
				);
			} else {
				return array(
					'success' => false,
					'error' => $result['messages'][0]['error-text'] ?? 'Error al enviar SMS'
				);
			}
		} catch (Exception $e) {
			return array(
				'success' => false,
				'error' => 'Error de conexión con Nexmo: ' . $e->getMessage()
			);
		}
	}

	/**
	 * Enviar notificación push
	 */
	private function send_push_notification($employee, $data)
	{
		// Obtener device tokens del empleado
		$devices = DB::select('device_token', 'platform')
			->from('employee_devices')
			->where('employee_id', '=', $employee->id)
			->where('is_active', '=', 1)
			->execute()
			->as_array();

		if (empty($devices)) {
			return array('success' => false, 'error' => 'Empleado sin dispositivos registrados');
		}

		$provider = Config::get('notifications.push_provider', 'fcm'); // fcm (Firebase), apns (Apple)
		$title = 'Recibo de Nómina Disponible';
		$body = "Tu recibo de {$data['period_name']} está listo. Neto: \${$data['net_payment']}";
		
		$results = array();
		foreach ($devices as $device) {
			switch ($provider) {
				case 'fcm':
					$results[] = $this->send_fcm_push($device['device_token'], $title, $body, $data);
					break;
			}
		}

		return array(
			'success' => count($results) > 0,
			'channel' => 'push',
			'devices_notified' => count($results)
		);
	}

	/**
	 * Enviar push notification usando Firebase Cloud Messaging
	 */
	private function send_fcm_push($device_token, $title, $body, $data)
	{
		$server_key = Config::get('notifications.fcm.server_key');

		if (!$server_key) {
			return array('success' => false, 'error' => 'FCM no configurado');
		}

		try {
			$url = 'https://fcm.googleapis.com/fcm/send';
			
			$notification = array(
				'title' => $title,
				'body' => $body,
				'icon' => 'ic_notification',
				'sound' => 'default',
				'click_action' => 'OPEN_RECEIPT'
			);

			$payload = array(
				'to' => $device_token,
				'notification' => $notification,
				'data' => $data
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization: key=' . $server_key,
				'Content-Type: application/json'
			));
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$result = json_decode($response, true);

			return array(
				'success' => $httpCode == 200 && isset($result['success']) && $result['success'] == 1,
				'message_id' => $result['results'][0]['message_id'] ?? null
			);
		} catch (Exception $e) {
			return array(
				'success' => false,
				'error' => 'Error de conexión con FCM: ' . $e->getMessage()
			);
		}
	}

	/**
	 * Enviar notificación por WhatsApp
	 */
	private function send_whatsapp_notification($employee, $data)
	{
		if (!$employee->phone) {
			return array('success' => false, 'error' => 'Empleado sin teléfono configurado');
		}

		$provider = Config::get('notifications.whatsapp_provider', 'twilio'); // twilio, 360dialog
		$message = "🔔 *Recibo de Nómina Disponible*\n\nHola {$data['employee_name']},\n\nTu recibo de *{$data['period_name']}* está listo.\n\n💰 Neto a pagar: *\${$data['net_payment']}*\n📅 Fecha de pago: {$data['payment_date']}\n\nDescarga tu recibo aquí: {$data['receipt_url']}";

		switch ($provider) {
			case 'twilio':
				return $this->send_twilio_whatsapp($employee->phone, $message);
			default:
				return array('success' => false, 'error' => 'Proveedor WhatsApp no configurado');
		}
	}

	/**
	 * Enviar WhatsApp usando Twilio
	 */
	private function send_twilio_whatsapp($phone, $message)
	{
		$account_sid = Config::get('notifications.twilio.account_sid');
		$auth_token = Config::get('notifications.twilio.auth_token');
		$whatsapp_from = Config::get('notifications.twilio.whatsapp_from', 'whatsapp:+14155238886');

		if (!$account_sid || !$auth_token) {
			return array('success' => false, 'error' => 'Twilio WhatsApp no configurado');
		}

		try {
			$url = "https://api.twilio.com/2010-04-01/Accounts/{$account_sid}/Messages.json";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_USERPWD, $account_sid . ':' . $auth_token);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
				'From' => $whatsapp_from,
				'To' => 'whatsapp:' . $phone,
				'Body' => $message
			)));

			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$result = json_decode($response, true);

			if ($httpCode == 201) {
				return array(
					'success' => true,
					'channel' => 'whatsapp',
					'provider' => 'twilio',
					'recipient' => $phone,
					'message_id' => $result['sid'] ?? null
				);
			} else {
				return array(
					'success' => false,
					'error' => $result['message'] ?? 'Error al enviar WhatsApp'
				);
			}
		} catch (Exception $e) {
			return array(
				'success' => false,
				'error' => 'Error de conexión con Twilio WhatsApp: ' . $e->getMessage()
			);
		}
	}

	/**
	 * Generar PDF temporal para adjuntar
	 */
	private function generate_pdf_attachment($receipt)
	{
		// Usa la clase de generación de PDF existente
		// Retorna la ruta del archivo temporal
		$temp_path = APPPATH . 'tmp/receipts/';
		
		if (!is_dir($temp_path)) {
			mkdir($temp_path, 0777, true);
		}

		$filename = $temp_path . 'receipt_' . $receipt->id . '_' . time() . '.pdf';
		
		// Aquí se integraría con el generador de PDF existente
		// Por ahora retornamos null
		return null;
	}

	/**
	 * Notificar a todos los empleados de un período
	 */
	public function notify_period_receipts($period_id, $channels = array('email'))
	{
		$period = Model_Payroll_Period::find($period_id);

		if (!$period || $period->tenant_id != $this->tenant_id) {
			throw new Exception('Período no encontrado');
		}

		// Obtener todos los recibos del período
		$receipts = Model_Payroll_Receipt::query()
			->where('payroll_period_id', '=', $period_id)
			->where('tenant_id', '=', $this->tenant_id)
			->where('deleted_at', 'IS', null)
			->get();

		$results = array(
			'total' => count($receipts),
			'success' => 0,
			'failed' => 0,
			'details' => array()
		);

		foreach ($receipts as $receipt) {
			try {
				$result = $this->notify_receipt_available($receipt->id, $channels);
				$results['success']++;
				$results['details'][] = array(
					'receipt_id' => $receipt->id,
					'employee_code' => $receipt->employee_code,
					'status' => 'success',
					'channels' => $result
				);
			} catch (Exception $e) {
				$results['failed']++;
				$results['details'][] = array(
					'receipt_id' => $receipt->id,
					'employee_code' => $receipt->employee_code,
					'status' => 'failed',
					'error' => $e->getMessage()
				);
			}

			// Pequeña pausa para no saturar APIs
			usleep(100000); // 0.1 segundos
		}

		return $results;
	}
}
