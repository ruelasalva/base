<?php

/**
 * Model_Purchasereceiptitem
 * 
 * Modelo para los items individuales de una recepción de mercancía
 * Incluye cantidad ordenada vs recibida, condición del producto y ubicación
 */
class Model_Purchasereceiptitem extends \Orm\Model
{
    protected static $_table_name = 'purchase_receipt_items';
    
    protected static $_properties = array(
        'id',
        'purchase_receipt_id',
        'purchase_order_item_id',
        'product_id',
        'location',
        'quantity_ordered',
        'quantity_received',
        'unit_cost',
        'subtotal',
        'condition', // good, damaged, defective, expired
        'batch_number',
        'expiry_date',
        'notes',
        'created_at',
        'updated_at'
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

    protected static $_belongs_to = array(
        'purchase_receipt' => array(
            'key_from' => 'purchase_receipt_id',
            'model_to' => 'Model_Purchasereceipt',
            'key_to' => 'id',
        ),
        'purchase_order_item' => array(
            'key_from' => 'purchase_order_item_id',
            'model_to' => 'Model_Purchaseorderitem',
            'key_to' => 'id',
        ),
        'product' => array(
            'key_from' => 'product_id',
            'model_to' => 'Model_Product',
            'key_to' => 'id',
        )
    );

    /**
     * Calcula el subtotal antes de guardar
     */
    public function _event_before_save()
    {
        $this->subtotal = $this->quantity_received * $this->unit_cost;
    }

    /**
     * Obtiene el subtotal
     */
    public function get_subtotal()
    {
        return $this->quantity_received * $this->unit_cost;
    }

    /**
     * Verifica si tiene discrepancia en cantidad
     */
    public function has_quantity_discrepancy()
    {
        return $this->quantity_received != $this->quantity_ordered;
    }

    /**
     * Verifica si tiene discrepancia en condición
     */
    public function has_condition_discrepancy()
    {
        return $this->condition != 'good';
    }

    /**
     * Verifica si tiene alguna discrepancia
     */
    public function has_discrepancy()
    {
        return $this->has_quantity_discrepancy() || $this->has_condition_discrepancy();
    }

    /**
     * Obtiene el porcentaje de recepción
     */
    public function get_percentage()
    {
        if ($this->quantity_ordered == 0) {
            return 0;
        }
        
        return round(($this->quantity_received / $this->quantity_ordered) * 100, 2);
    }

    /**
     * Verifica si está completo (100% recibido)
     */
    public function is_complete()
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }

    /**
     * Verifica si es parcial
     */
    public function is_partial()
    {
        return $this->quantity_received > 0 && $this->quantity_received < $this->quantity_ordered;
    }

    /**
     * Obtiene el badge HTML para la condición
     */
    public function get_condition_badge()
    {
        return Model_Purchasereceipt::get_condition_badge($this->condition);
    }

    /**
     * Obtiene diferencia de cantidad (recibido - ordenado)
     */
    public function get_quantity_difference()
    {
        return $this->quantity_received - $this->quantity_ordered;
    }

    /**
     * Obtiene icono según discrepancia
     */
    public function get_discrepancy_icon()
    {
        if (!$this->has_discrepancy()) {
            return '<i class="fas fa-check-circle text-success"></i>';
        }
        
        if ($this->has_condition_discrepancy()) {
            return '<i class="fas fa-exclamation-triangle text-danger"></i>';
        }
        
        $diff = $this->get_quantity_difference();
        if ($diff < 0) {
            return '<i class="fas fa-arrow-down text-warning"></i>';
        } elseif ($diff > 0) {
            return '<i class="fas fa-arrow-up text-info"></i>';
        }
        
        return '<i class="fas fa-check-circle text-success"></i>';
    }
}
