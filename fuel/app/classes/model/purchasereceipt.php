<?php

/**
 * Model_Purchasereceipt
 * 
 * Modelo para gestionar las recepciones físicas de mercancía al almacén
 * Diferencia con Contrarecibos: Este modelo maneja el ingreso físico al inventario
 * con ubicaciones, condiciones y actualizaciones de stock
 */
class Model_Purchasereceipt extends \Orm\Model
{
    protected static $_table_name = 'purchase_receipts';
    
    protected static $_properties = array(
        'id',
        'code',
        'purchase_order_id',
        'provider_id',
        'almacen_id',
        'almacen_name',
        'receipt_date',
        'received_date',
        'received_by',
        'verified_by',
        'verified_date',
        'status', // pending, received, verified, discrepancy, cancelled
        'total_items',
        'total_quantity_expected',
        'total_quantity_received',
        'total_amount',
        'has_discrepancy',
        'discrepancy_notes',
        'notes',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => true,
        ),
    );

    protected static $_soft_delete = array(
        'deleted_field' => 'deleted_at',
        'mysql_timestamp' => true,
    );

    protected static $_has_many = array(
        'items' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Purchasereceiptitem',
            'key_to' => 'purchase_receipt_id',
            'cascade_save' => true,
            'cascade_delete' => true,
        )
    );

    protected static $_belongs_to = array(
        'purchase_order' => array(
            'key_from' => 'purchase_order_id',
            'model_to' => 'Model_Purchaseorder',
            'key_to' => 'id',
        ),
        'provider' => array(
            'key_from' => 'provider_id',
            'model_to' => 'Model_Provider',
            'key_to' => 'id',
        ),
        'receiver' => array(
            'key_from' => 'received_by',
            'model_to' => 'Model_User',
            'key_to' => 'id',
        ),
        'verifier' => array(
            'key_from' => 'verified_by',
            'model_to' => 'Model_User',
            'key_to' => 'id',
        ),
        'creator' => array(
            'key_from' => 'created_by',
            'model_to' => 'Model_User',
            'key_to' => 'id',
        )
    );

    /**
     * Genera código único para la recepción: REC-YYYYMM-####
     */
    public static function generate_code()
    {
        $year_month = date('Ym');
        $prefix = 'REC-' . $year_month . '-';
        
        $last = DB::select(DB::expr('MAX(CAST(SUBSTRING(code, 13) AS UNSIGNED)) as last_number'))
            ->from('purchase_receipts')
            ->where('code', 'LIKE', $prefix . '%')
            ->where('deleted_at', 'IS', null)
            ->execute()
            ->current();
        
        $next_number = ($last && $last['last_number']) ? $last['last_number'] + 1 : 1;
        
        return $prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Verifica si la recepción puede ser editada
     */
    public function can_edit()
    {
        return in_array($this->status, array('pending', 'received'));
    }

    /**
     * Verifica si la recepción puede ser eliminada
     */
    public function can_delete()
    {
        return $this->status === 'pending';
    }

    /**
     * Obtiene el badge HTML para el estado
     */
    public function get_status_badge()
    {
        $badges = array(
            'pending' => '<span class="badge bg-warning">Pendiente</span>',
            'received' => '<span class="badge bg-info">Recibido</span>',
            'verified' => '<span class="badge bg-success">Verificado</span>',
            'discrepancy' => '<span class="badge bg-danger">Con Discrepancias</span>',
            'cancelled' => '<span class="badge bg-secondary">Cancelado</span>',
        );
        
        return isset($badges[$this->status]) ? $badges[$this->status] : '<span class="badge bg-light">Desconocido</span>';
    }

    /**
     * Obtiene el badge HTML para la condición
     */
    public static function get_condition_badge($condition)
    {
        $badges = array(
            'good' => '<span class="badge bg-success"><i class="fas fa-check"></i> Bueno</span>',
            'damaged' => '<span class="badge bg-warning"><i class="fas fa-exclamation-triangle"></i> Dañado</span>',
            'defective' => '<span class="badge bg-danger"><i class="fas fa-times"></i> Defectuoso</span>',
            'expired' => '<span class="badge bg-dark"><i class="fas fa-calendar-times"></i> Caducado</span>',
        );
        
        return isset($badges[$condition]) ? $badges[$condition] : '<span class="badge bg-light">Desconocido</span>';
    }

    /**
     * Calcula porcentaje de recepción completa
     */
    public function get_completion_percentage()
    {
        if ($this->total_quantity_expected == 0) {
            return 0;
        }
        
        return round(($this->total_quantity_received / $this->total_quantity_expected) * 100, 2);
    }

    /**
     * Verifica si está completa (100% recibido)
     */
    public function is_complete()
    {
        return $this->total_quantity_received >= $this->total_quantity_expected;
    }

    /**
     * Verifica si tiene recepción parcial
     */
    public function is_partial()
    {
        return $this->total_quantity_received > 0 && $this->total_quantity_received < $this->total_quantity_expected;
    }

    /**
     * Calcula totales desde los items
     */
    public function calculate_totals()
    {
        $items = $this->items;
        
        $this->total_items = count($items);
        $this->total_quantity_expected = 0;
        $this->total_quantity_received = 0;
        $this->total_amount = 0;
        $this->has_discrepancy = 0;
        
        foreach ($items as $item) {
            $this->total_quantity_expected += $item->quantity_ordered;
            $this->total_quantity_received += $item->quantity_received;
            $this->total_amount += $item->subtotal;
            
            // Verificar discrepancias
            if ($item->quantity_received != $item->quantity_ordered || $item->condition != 'good') {
                $this->has_discrepancy = 1;
            }
        }
    }

    /**
     * Actualiza el estado según las cantidades recibidas
     */
    public function update_status()
    {
        if ($this->status === 'cancelled') {
            return;
        }

        if ($this->total_quantity_received == 0) {
            $this->status = 'pending';
        } elseif ($this->has_discrepancy == 1) {
            $this->status = 'discrepancy';
        } elseif ($this->is_complete()) {
            $this->status = 'received';
        } else {
            $this->status = 'received';
        }
    }

    /**
     * Marca como verificado
     */
    public function mark_as_verified($user_id = null, $notes = null)
    {
        $this->status = 'verified';
        $this->verified_by = $user_id ?: \Auth::get_user_id()[1];
        $this->verified_date = date('Y-m-d H:i:s');
        
        if ($notes) {
            $this->notes = $this->notes ? $this->notes . "\n\n" . $notes : $notes;
        }
        
        $this->save();
    }

    /**
     * Marca como cancelado
     */
    public function mark_as_cancelled($reason = null)
    {
        $this->status = 'cancelled';
        
        if ($reason) {
            $this->notes = $this->notes ? $this->notes . "\n\n[CANCELADO] " . $reason : "[CANCELADO] " . $reason;
        }
        
        $this->save();
    }

    /**
     * Obtiene el total de recepciones
     */
    public static function get_total_count()
    {
        return static::query()
            ->where('deleted_at', null)
            ->count();
    }

    /**
     * Obtiene recepciones por estado
     */
    public static function count_by_status($status)
    {
        return static::query()
            ->where('status', $status)
            ->where('deleted_at', null)
            ->count();
    }

    /**
     * Obtiene recepciones con discrepancias
     */
    public static function count_with_discrepancies()
    {
        return static::query()
            ->where('has_discrepancy', 1)
            ->where('deleted_at', null)
            ->count();
    }

    /**
     * Obtiene valor total de recepciones en un período
     */
    public static function get_total_amount_by_period($start_date, $end_date)
    {
        $result = DB::select(DB::expr('SUM(total_amount) as total'))
            ->from('purchase_receipts')
            ->where('receipt_date', '>=', $start_date)
            ->where('receipt_date', '<=', $end_date)
            ->where('status', '!=', 'cancelled')
            ->where('deleted_at', 'IS', null)
            ->execute()
            ->current();
        
        return $result ? floatval($result['total']) : 0;
    }
}
