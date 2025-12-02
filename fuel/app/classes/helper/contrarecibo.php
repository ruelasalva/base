<?php
/**
 * HELPER_CONTRARECIBOS
 * Funciones utilitarias para el cálculo de fecha de pago en contrarecibos.
 * Considera días de crédito, días inhábiles, configuración de empresa y reglas de pago.
 */
class Helper_Contrarecibos
{
    /**
     * CALCULAR_FECHA_PAGO
     * Calcula la fecha de pago según fecha de recepción, días de crédito y reglas.
     *
     * @param string|int $fecha_recepcion  Fecha de recepción (Y-m-d o timestamp)
     * @param int        $dias_credito     Número de días de crédito del proveedor
     * @param array      $reglas           Array de reglas. Ejemplo:
     *  [
     *      'dias_pago' => ['martes', 'viernes'], // solo martes y viernes se paga
     *      'dias_inhabiles' => ['2025-09-16', '2025-12-25'], // feriados
     *      'dias_no_pago' => ['sabado', 'domingo'], // no se paga estos días
     *      'min_dias' => 3 // mínimo de días de espera desde recepción
     *  ]
     * @return string   Fecha de pago en formato Y-m-d
     */
    public static function calcular_fecha_pago($fecha_recepcion, $dias_credito = 0, $reglas = array())
    {
        // 1. Normaliza fecha de recepción
        if (!is_numeric($fecha_recepcion)) {
            $fecha = strtotime($fecha_recepcion);
        } else {
            $fecha = $fecha_recepcion;
        }

        // 2. Aplica mínimo de días (si hay)
        $min_dias = isset($reglas['min_dias']) ? (int)$reglas['min_dias'] : 0;
        $dias_totales = max($dias_credito, $min_dias);

        // 3. Suma los días de crédito a la fecha de recepción
        $fecha_pago = strtotime("+$dias_totales days", $fecha);

        // 4. Aplica reglas de días inhábiles y de pago
        $dias_inhabiles = isset($reglas['dias_inhabiles']) ? $reglas['dias_inhabiles'] : array();
        $dias_pago = isset($reglas['dias_pago']) ? $reglas['dias_pago'] : array();
        $dias_no_pago = isset($reglas['dias_no_pago']) ? $reglas['dias_no_pago'] : array();

        $dia_pago_valido = false;
        $intentos = 0;

        // Recorre hasta encontrar el siguiente día válido
        while (!$dia_pago_valido && $intentos < 31) {
            $dia_semana = strtolower(strftime('%A', $fecha_pago)); // Ej: 'martes'
            $fecha_str = date('Y-m-d', $fecha_pago);

            $es_inhabil = in_array($fecha_str, $dias_inhabiles);
            $es_no_pago = in_array($dia_semana, $dias_no_pago);

            if ($es_inhabil || $es_no_pago) {
                // Siguiente día
                $fecha_pago = strtotime('+1 day', $fecha_pago);
                $intentos++;
                continue;
            }
            // Si hay reglas de solo días de pago, respétalas
            if (!empty($dias_pago) && !in_array($dia_semana, $dias_pago)) {
                $fecha_pago = strtotime('+1 day', $fecha_pago);
                $intentos++;
                continue;
            }
            $dia_pago_valido = true;
        }

        return date('Y-m-d', $fecha_pago);
    }
}
