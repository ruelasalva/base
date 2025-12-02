<?php
class Helper_Payments
{
    /**
     * Calcula la fecha tentativa de pago
     *
     * @param object $factura   Modelo de factura (para usar created_at o invoice_data según base_date_type)
     * @param object|null $provider   Modelo proveedor (puede tener reglas propias)
     * @return int|null               timestamp de la fecha tentativa
     */
    public static function next_payment_date($factura, $provider = null)
    {
        $result = static::calculate_with_log($factura, $provider);

        // Guardar log en Fuel
        foreach ($result['steps'] as $s) {
            \Log::debug('[CALCULO-PAGO] ' . $s);
        }

        return $result['final_date'];
    }

    /**
     * Calcula la fecha tentativa con LOG detallado
     *
     * @param object $factura
     * @param object|null $provider
     * @return array {
     * final_date: int timestamp,
     * steps: array de strings con el detalle
     * }
     */
    public static function calculate_with_log($factura, $provider = null)
    {
        $steps  = [];
        $config = \Model_Config::find(1);

        // ================== FECHA BASE SEGÚN base_date_type ==================
        $date = $factura->created_at; // default
        $steps[] = "Fecha base default (created_at): " . date('Y-m-d', $date);

        if (!empty($provider) && !empty($provider->payment_terms_id)) {
            $terms = \Model_Payments_Term::find($provider->payment_terms_id);
            if ($terms) {
                switch ((int)$terms->base_date_type) {
                    case 1: // contabilización
                        $date = $factura->created_at;
                        $steps[] = "Base date = contabilización (created_at): " . date('Y-m-d', $date);
                        break;
                    case 2: // sistema
                        $date = time();
                        $steps[] = "Base date = sistema (hoy): " . date('Y-m-d', $date);
                        break;
                    case 3: // documento
                        $inv_data = !empty($factura->invoice_data) ? unserialize($factura->invoice_data) : [];
                        if (!empty($inv_data['fecha'])) {
                            $date = strtotime($inv_data['fecha']);
                            $steps[] = "Base date = documento (XML): " . date('Y-m-d', $date);
                        }
                        break;
                }
            }
        }

        // ================== OFFSET Y TOLERANCE ==================
        $start_offset_days = 0;
        $terms_days        = 0;

        if (!empty($provider) && !empty($provider->payment_terms_id)) {
            $terms = \Model_Payments_Term::find($provider->payment_terms_id);
            if ($terms) {
                $start_offset_days = (int)$terms->start_offset_days;
                $terms_days        = (int)$terms->days_tolerance;
                $steps[] = "Condiciones de pago: offset={$start_offset_days}, tolerance={$terms_days}";
            }
        } else {
            // fallback a configuración global
            $terms_days = (int)$config->payment_terms_days;
            $steps[] = "Sin términos proveedor → usando global days={$terms_days}";
        }

        if ($start_offset_days > 0) {
            $date = strtotime("+{$start_offset_days} days", $date);
            $steps[] = "Aplicado offset inicial: +{$start_offset_days} días → " . date('Y-m-d', $date);
        }

        if ($terms_days > 0) {
            $date = strtotime("+{$terms_days} days", $date);
            $steps[] = "Aplicados días de crédito/tolerancia: +{$terms_days} días → " . date('Y-m-d', $date);
        }

        // ================== AJUSTE A DÍAS DE PAGO ==================
        $days_str     = $provider && !empty($provider->payment_days) ? $provider->payment_days : $config->payment_days;
        $payment_days = [];

        // Puede venir como "thursday" o "1,15,30"
        if (preg_match('/[a-z]/i', $days_str)) {
            // texto de días
            $map = [
                'monday'    => 1, 'tuesday'   => 2, 'wednesday' => 3,
                'thursday'  => 4, 'friday'    => 5, 'saturday'  => 6, 'sunday'    => 7,
            ];
            foreach (explode(',', strtolower($days_str)) as $d) {
                $d = trim($d);
                if (isset($map[$d])) $payment_days[] = $map[$d];
            }
            $steps[] = "Días de pago permitidos (semanales): " . implode(',', $payment_days);
            $check_type = 'weekday';
        } else {
            $payment_days = array_map('intval', explode(',', $days_str));
            $steps[] = "Días de pago permitidos (mensuales): " . implode(',', $payment_days);
            $check_type = 'monthday';
        }

        $holidays = !empty($config->holidays) ? explode(',', $config->holidays) : [];
        if ($holidays) {
            $steps[] = "Feriados registrados: " . implode(',', $holidays);
        }

        // === INICIO DE LA CORRECCIÓN ===
        $adjust_count = 0;
        $max_attempts = 90; // <-- Agregas el límite de seguridad
        while ($adjust_count < $max_attempts) { // <-- Corriges la condición del bucle
            $dow  = (int)date('N', $date);
            $dom  = (int)date('j', $date);
            $dstr = date('Y-m-d', $date);

            $valid = false;
            if ($check_type === 'weekday' && in_array($dow, $payment_days)) {
                $valid = true;
            }
            if ($check_type === 'monthday' && in_array($dom, $payment_days)) {
                $valid = true;
            }

            if ($valid && $dow < 6 && !in_array($dstr, $holidays)) {
                $steps[] = "Fecha válida encontrada: " . date('Y-m-d', $date);
                break;
            }

            $date = strtotime('+1 day', $date);
            $adjust_count++;
        }
        // === FIN DE LA CORRECCIÓN ===

        if ($adjust_count > 0 && $adjust_count < $max_attempts) {
            $steps[] = "Se ajustó la fecha en {$adjust_count} días hasta llegar a día válido.";
        }

        // (Opcional) Añadir un log si el bucle alcanza el límite
        if ($adjust_count >= $max_attempts) {
            $steps[] = "ADVERTENCIA: Se alcanzó el límite de 90 intentos sin encontrar un día de pago válido.";
            \Log::warning('[CALCULO-PAGO] Límite de intentos alcanzado para factura ID: '.$factura->id);
        }

        return [
            'final_date' => $date,
            'steps'      => $steps,
        ];
    }
}
