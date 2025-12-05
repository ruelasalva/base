<?php

class Model_Deliverynoteitem extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'delivery_note_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'notes',
        'created_at',
        'updated_at',
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

    protected static $_table_name = 'delivery_note_items';

    protected static $_belongs_to = array(
        'delivery_note' => array(
            'key_from' => 'delivery_note_id',
            'model_to' => 'Model_Deliverynote',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'product' => array(
            'key_from' => 'product_id',
            'model_to' => 'Model_Product',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

    /**
     * Obtiene el subtotal de esta línea
     */
    public function get_subtotal()
    {
        return $this->quantity_received * $this->unit_price;
    }

    /**
     * Verifica si la cantidad recibida es menor a la ordenada
     */
    public function is_partial()
    {
        return $this->quantity_received > 0 && $this->quantity_received < $this->quantity_ordered;
    }

    /**
     * Verifica si la cantidad fue recibida completamente
     */
    public function is_complete()
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }

    /**
     * Obtiene el porcentaje recibido
     */
    public function get_percentage()
    {
        if ($this->quantity_ordered == 0) {
            return 0;
        }
        return round(($this->quantity_received / $this->quantity_ordered) * 100, 2);
    }
}
