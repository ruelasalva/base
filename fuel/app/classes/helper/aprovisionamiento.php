<?php
/**
 * Helper_Aprovisionamiento
 * 
 * Gestión automatizada de contrarecibos con cálculo inteligente de fechas
 * basado en días hábiles, festivos y configuración por tenant.
 * 
 * @package    Base
 * @category   Helpers
 * @author     Sistema Aprovisionamiento
 * @version    1.0
 */
class Helper_Aprovisionamiento
{
    /**
     * Genera un contrarecibo automáticamente basado en configuración
     * 
     * @param int $bill_id ID de la factura (providers_bills)
     * @param int $tenant_id ID del tenant (default 1)
     * @return array ['success' => bool, 'receipt_id' => int, 'dates' => array, 'errors' => array]
     */
    public static function generar_contrarecibo_automatico($bill_id, $tenant_id = 1)
    {
        try {
            // 1. Obtener configuración del tenant
            $config = \DB::select()
                ->from('providers_billing_config')
                ->where('tenant_id', $tenant_id)
                ->execute()
                ->current();
            
            if (!$config) {
                return [
                    'success' => false,
                    'errors' => ['No existe configuración de facturación para el tenant ' . $tenant_id]
                ];
            }
            
            // 2. Obtener información de la factura
            $bill = \DB::select()
                ->from('providers_bills')
                ->where('id', $bill_id)
                ->where('deleted', 0)
                ->execute()
                ->current();
            
            if (!$bill) {
                return [
                    'success' => false,
                    'errors' => ['Factura no encontrada o eliminada: ' . $bill_id]
                ];
            }
            
            // 3. Verificar si ya existe contrarecibo para esta factura
            $existing_receipt = \DB::select(\DB::expr('COUNT(*) as count'))
                ->from('providers_receipts')
                ->join('providers_receipts_details', 'INNER')
                ->on('providers_receipts.id', '=', 'providers_receipts_details.receipt_id')
                ->where('providers_receipts_details.bill_id', $bill_id)
                ->where('providers_receipts.deleted', 0)
                ->execute()
                ->get('count');
            
            if ($existing_receipt > 0) {
                return [
                    'success' => false,
                    'errors' => ['Ya existe un contrarecibo para esta factura']
                ];
            }
            
            // 4. Calcular fecha oficial de recepción
            $upload_timestamp = $bill['created_at'] ?: time();
            $upload_date = new \DateTime();
            $upload_date->setTimestamp($upload_timestamp);
            
            $holidays = json_decode($config['holidays'] ?: '[]', true);
            $valid_days = array_map('intval', explode(',', $config['invoice_receive_days']));
            $limit_time = $config['invoice_receive_limit_time'];
            
            $receipt_calculation = self::calculate_receipt_date(
                $upload_date,
                $valid_days,
                $limit_time,
                $holidays
            );
            
            // 5. Calcular fecha programada de pago
            $payment_calculation = self::calculate_payment_date(
                $receipt_calculation['date'],
                (int)$config['payment_terms_days'],
                array_map('intval', explode(',', $config['payment_days'])),
                $holidays
            );
            
            // 6. Crear contrarecibo
            $receipt_data = [
                'provider_id' => $bill['provider_id'],
                'order_id' => $bill['order_id'],
                'receipt_number' => self::generate_receipt_number($bill['provider_id']),
                'total' => $bill['total'],
                'status' => 1, // Pendiente
                'notes' => 'Contrarecibo generado automáticamente',
                'receipt_date' => $receipt_calculation['timestamp'],
                'official_receipt_date' => $receipt_calculation['timestamp'],
                'payment_date' => $payment_calculation['timestamp'],
                'programmed_payment_date' => $payment_calculation['timestamp'],
                'calculation_notes' => json_encode([
                    'upload_date' => $upload_date->format('Y-m-d H:i:s'),
                    'receipt_calculation' => $receipt_calculation['notes'],
                    'payment_calculation' => $payment_calculation['notes'],
                    'config_used' => [
                        'valid_days' => $valid_days,
                        'limit_time' => $limit_time,
                        'payment_terms' => $config['payment_terms_days'],
                        'payment_days' => $config['payment_days']
                    ]
                ], JSON_UNESCAPED_UNICODE),
                'generated_by' => \Auth::get_user_id() ?: 0,
                'deleted' => 0,
                'created_at' => time(),
                'updated_at' => time()
            ];
            
            list($receipt_id, $rows) = \DB::insert('providers_receipts')
                ->set($receipt_data)
                ->execute();
            
            // 7. Crear detalle del contrarecibo (relación con factura)
            \DB::insert('providers_receipts_details')
                ->set([
                    'receipt_id' => $receipt_id,
                    'bill_id' => $bill_id,
                    'amount' => $bill['total'],
                    'notes' => 'Factura UUID: ' . ($bill['uuid'] ?: 'N/A'),
                    'created_at' => time(),
                    'updated_at' => time()
                ])
                ->execute();
            
            // 8. Registrar en logs
            Helper_ProviderLog::record(
                'providers_receipts',
                $receipt_id,
                'generate_auto',
                'Contrarecibo generado automáticamente para factura #' . $bill_id,
                null,
                $receipt_data,
                $bill['provider_id']
            );
            
            return [
                'success' => true,
                'receipt_id' => $receipt_id,
                'receipt_number' => $receipt_data['receipt_number'],
                'dates' => [
                    'upload' => $upload_date->format('Y-m-d H:i:s'),
                    'official_receipt' => $receipt_calculation['date']->format('Y-m-d'),
                    'programmed_payment' => $payment_calculation['date']->format('Y-m-d')
                ],
                'calculation' => [
                    'receipt_notes' => $receipt_calculation['notes'],
                    'payment_notes' => $payment_calculation['notes']
                ]
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error generando contrarecibo automático: ' . $e->getMessage());
            return [
                'success' => false,
                'errors' => ['Error interno: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Calcula la fecha oficial de recepción considerando días hábiles y hora límite
     * 
     * @param \DateTime $upload_date Fecha de subida
     * @param array $valid_days Días válidos (1=Lun, 7=Dom)
     * @param string $limit_time Hora límite (HH:MM:SS)
     * @param array $holidays Array de fechas festivas ['YYYY-MM-DD']
     * @return array ['date' => DateTime, 'timestamp' => int, 'notes' => string]
     */
    private static function calculate_receipt_date(\DateTime $upload_date, array $valid_days, $limit_time, array $holidays)
    {
        $receipt_date = clone $upload_date;
        $notes = [];
        
        // Verificar si la hora de subida excede el límite
        list($limit_hour, $limit_minute) = explode(':', $limit_time);
        $upload_hour = (int)$receipt_date->format('H');
        $upload_minute = (int)$receipt_date->format('i');
        
        if ($upload_hour > $limit_hour || ($upload_hour == $limit_hour && $upload_minute > $limit_minute)) {
            $receipt_date->modify('+1 day');
            $notes[] = sprintf('Subido después de %s, se considera siguiente día', $limit_time);
        }
        
        // Avanzar hasta encontrar un día válido (no fin de semana, no festivo)
        $max_iterations = 30; // Prevenir loop infinito
        $iterations = 0;
        
        while ($iterations < $max_iterations) {
            $day_of_week = (int)$receipt_date->format('N'); // 1=Lun, 7=Dom
            $date_str = $receipt_date->format('Y-m-d');
            
            // Verificar si es día válido
            if (!in_array($day_of_week, $valid_days)) {
                $receipt_date->modify('+1 day');
                $notes[] = sprintf('%s no es día válido para recepción', $date_str);
                $iterations++;
                continue;
            }
            
            // Verificar si es festivo
            if (in_array($date_str, $holidays)) {
                $receipt_date->modify('+1 day');
                $notes[] = sprintf('%s es día festivo', $date_str);
                $iterations++;
                continue;
            }
            
            // Día válido encontrado
            break;
        }
        
        if (empty($notes)) {
            $notes[] = 'Recibido en día hábil';
        }
        
        return [
            'date' => $receipt_date,
            'timestamp' => $receipt_date->getTimestamp(),
            'notes' => implode('. ', $notes)
        ];
    }
    
    /**
     * Calcula la fecha programada de pago basada en términos y días de pago
     * 
     * @param \DateTime $receipt_date Fecha de recepción oficial
     * @param int $payment_terms Días de crédito (ej: 30)
     * @param array $payment_days Días de pago permitidos (5=Viernes)
     * @param array $holidays Array de festivos
     * @return array ['date' => DateTime, 'timestamp' => int, 'notes' => string]
     */
    private static function calculate_payment_date(\DateTime $receipt_date, $payment_terms, array $payment_days, array $holidays)
    {
        $payment_date = clone $receipt_date;
        $notes = [];
        
        // Avanzar los días de crédito (solo días hábiles)
        $business_days_added = 0;
        $max_iterations = 100;
        $iterations = 0;
        
        while ($business_days_added < $payment_terms && $iterations < $max_iterations) {
            $payment_date->modify('+1 day');
            $day_of_week = (int)$payment_date->format('N');
            $date_str = $payment_date->format('Y-m-d');
            
            // Saltar fines de semana (sábado=6, domingo=7)
            if ($day_of_week == 6 || $day_of_week == 7) {
                $iterations++;
                continue;
            }
            
            // Saltar festivos
            if (in_array($date_str, $holidays)) {
                $iterations++;
                continue;
            }
            
            $business_days_added++;
            $iterations++;
        }
        
        $notes[] = sprintf('Agregados %d días hábiles desde recepción', $payment_terms);
        
        // Ajustar al día de pago más cercano permitido
        if (!empty($payment_days)) {
            $current_day = (int)$payment_date->format('N');
            
            if (!in_array($current_day, $payment_days)) {
                // Buscar el siguiente día de pago permitido
                $days_to_add = 0;
                $max_search = 7;
                
                for ($i = 1; $i <= $max_search; $i++) {
                    $test_date = clone $payment_date;
                    $test_date->modify('+' . $i . ' day');
                    $test_day = (int)$test_date->format('N');
                    $test_str = $test_date->format('Y-m-d');
                    
                    if (in_array($test_day, $payment_days) && !in_array($test_str, $holidays)) {
                        $days_to_add = $i;
                        break;
                    }
                }
                
                if ($days_to_add > 0) {
                    $payment_date->modify('+' . $days_to_add . ' day');
                    $day_names = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                    $notes[] = sprintf('Ajustado al siguiente día de pago permitido (%s)', $day_names[$payment_date->format('N')]);
                }
            }
        }
        
        // Verificar que la fecha final no sea festivo
        while (in_array($payment_date->format('Y-m-d'), $holidays)) {
            $payment_date->modify('+1 day');
            $notes[] = 'Ajustado por día festivo';
        }
        
        return [
            'date' => $payment_date,
            'timestamp' => $payment_date->getTimestamp(),
            'notes' => implode('. ', $notes)
        ];
    }
    
    /**
     * Genera un número de contrarecibo único
     * 
     * @param int $provider_id
     * @return string Formato: CR-YYYY-PROV{id}-{seq}
     */
    private static function generate_receipt_number($provider_id)
    {
        $year = date('Y');
        $prefix = sprintf('CR-%s-PROV%04d-', $year, $provider_id);
        
        // Obtener último número de secuencia para este proveedor este año
        $last_receipt = \DB::select(\DB::expr('MAX(receipt_number) as last'))
            ->from('providers_receipts')
            ->where('receipt_number', 'LIKE', $prefix . '%')
            ->execute()
            ->get('last');
        
        if ($last_receipt) {
            preg_match('/-(\d+)$/', $last_receipt, $matches);
            $seq = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
        } else {
            $seq = 1;
        }
        
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Obtiene estadísticas de contrarecibos para dashboard
     * 
     * @param int $tenant_id
     * @return array
     */
    public static function get_dashboard_stats($tenant_id = 1)
    {
        // Contrarecibos pendientes de pago
        $pending_payment = \DB::select(\DB::expr('COUNT(*) as count'), \DB::expr('SUM(total) as total'))
            ->from('providers_receipts')
            ->where('deleted', 0)
            ->where('status', 1) // Pendiente
            ->where('payment_date_actual', null)
            ->execute()
            ->current();
        
        // Contrarecibos vencidos (fecha programada pasada)
        $overdue = \DB::select(\DB::expr('COUNT(*) as count'))
            ->from('providers_receipts')
            ->where('deleted', 0)
            ->where('status', 1)
            ->where('programmed_payment_date', '<', time())
            ->where('payment_date_actual', null)
            ->execute()
            ->get('count');
        
        // Contrarecibos pagados este mes
        $start_of_month = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $paid_this_month = \DB::select(\DB::expr('COUNT(*) as count'), \DB::expr('SUM(total) as total'))
            ->from('providers_receipts')
            ->where('deleted', 0)
            ->where('payment_date_actual', '>=', $start_of_month)
            ->execute()
            ->current();
        
        return [
            'pending' => [
                'count' => (int)$pending_payment['count'],
                'total' => (float)$pending_payment['total']
            ],
            'overdue' => (int)$overdue,
            'paid_this_month' => [
                'count' => (int)$paid_this_month['count'],
                'total' => (float)$paid_this_month['total']
            ]
        ];
    }
}
