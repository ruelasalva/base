<?php

class Model_Deliverynote extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'code',
        'purchase_id',
        'purchase_order_id',
        'provider_id',
        'delivery_date',
        'received_date',
        'received_by',
        'status',
        'notes',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => false,
        ),
    );

    protected static $_table_name = 'delivery_notes';

    protected static $_belongs_to = array(
        'purchase' => array(
            'key_from' => 'purchase_id',
            'model_to' => 'Model_Purchase',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'purchase_order' => array(
            'key_from' => 'purchase_order_id',
            'model_to' => 'Model_Purchaseorder',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'provider' => array(
            'key_from' => 'provider_id',
            'model_to' => 'Model_Provider',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'receiver' => array(
            'key_from' => 'received_by',
            'model_to' => 'Model_User',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'creator' => array(
            'key_from' => 'created_by',
            'model_to' => 'Model_User',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

    protected static $_has_many = array(
        'items' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Deliverynoteitem',
            'key_to' => 'delivery_note_id',
            'cascade_save' => true,
            'cascade_delete' => true,
        ),
    );

    /**
     * Genera código automático CR-YYYYMM-####
     */
    public static function generate_code()
    {
        $year_month = date('Ym');
        $prefix = 'CR-' . $year_month . '-';
        
        $last = static::query()
            ->where('code', 'LIKE', $prefix . '%')
            ->order_by('code', 'DESC')
            ->get_one();
        
        if ($last) {
            $last_number = (int) substr($last->code, -4);
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }
        
        return $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Verifica si el contrarecibo puede ser editado
     */
    public function can_edit()
    {
        return in_array($this->status, array('pending', 'partial'));
    }

    /**
     * Verifica si el contrarecibo puede ser eliminado
     */
    public function can_delete()
    {
        return $this->status == 'pending' && count($this->items) == 0;
    }

    /**
     * Obtiene badge HTML según el estado
     */
    public function get_status_badge()
    {
        $badges = array(
            'pending' => '<span class="badge bg-warning">Pendiente</span>',
            'partial' => '<span class="badge bg-info">Parcial</span>',
            'completed' => '<span class="badge bg-success">Completado</span>',
            'rejected' => '<span class="badge bg-danger">Rechazado</span>',
            'cancelled' => '<span class="badge bg-secondary">Cancelado</span>',
        );
        
        return isset($badges[$this->status]) ? $badges[$this->status] : $this->status;
    }

    /**
     * Verifica si todas las cantidades fueron recibidas
     */
    public function is_complete()
    {
        if (count($this->items) == 0) {
            return false;
        }

        foreach ($this->items as $item) {
            if ($item->quantity_received < $item->quantity_ordered) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calcula el porcentaje de completitud
     */
    public function get_completion_percentage()
    {
        if (count($this->items) == 0) {
            return 0;
        }

        $total_ordered = 0;
        $total_received = 0;

        foreach ($this->items as $item) {
            $total_ordered += $item->quantity_ordered;
            $total_received += $item->quantity_received;
        }

        if ($total_ordered == 0) {
            return 0;
        }

        return round(($total_received / $total_ordered) * 100, 2);
    }

    /**
     * Calcula el total del contrarecibo
     */
    public function get_total()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->quantity_received * $item->unit_price;
        }
        return $total;
    }

    /**
     * Actualiza el estado automáticamente según las cantidades
     */
    public function update_status()
    {
        if (count($this->items) == 0) {
            $this->status = 'pending';
            return;
        }

        $total_ordered = 0;
        $total_received = 0;

        foreach ($this->items as $item) {
            $total_ordered += $item->quantity_ordered;
            $total_received += $item->quantity_received;
        }

        if ($total_received == 0) {
            $this->status = 'pending';
        } elseif ($total_received >= $total_ordered) {
            $this->status = 'completed';
        } else {
            $this->status = 'partial';
        }
    }

    /**
     * Marca el contrarecibo como completado
     */
    public function mark_as_completed($received_by = null)
    {
        $this->status = 'completed';
        $this->received_date = date('Y-m-d');
        
        if ($received_by) {
            $this->received_by = $received_by;
        }
        
        $this->save();
    }

    /**
     * Marca el contrarecibo como rechazado
     */
    public function mark_as_rejected($reason = null)
    {
        $this->status = 'rejected';
        
        if ($reason) {
            $this->notes = $this->notes ? $this->notes . "\n\n[RECHAZADO] " . $reason : "[RECHAZADO] " . $reason;
        }
        
        $this->save();
    }
}
