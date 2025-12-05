<?php

/**
 * Model_Inventorymovementitem
 * 
 * Modelo para items individuales de movimientos de inventario
 */
class Model_Inventorymovementitem extends \Orm\Model
{
    protected static $_table_name = 'inventory_movement_items';
    
    protected static $_properties = array(
        'id',
        'movement_id',
        'product_id',
        'location_from_id',
        'location_to_id',
        'quantity',
        'unit_cost',
        'subtotal',
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
        'movement' => array(
            'key_from' => 'movement_id',
            'model_to' => 'Model_Inventorymovement',
            'key_to' => 'id',
        ),
        'product' => array(
            'key_from' => 'product_id',
            'model_to' => 'Model_Product',
            'key_to' => 'id',
        ),
        'location_from' => array(
            'key_from' => 'location_from_id',
            'model_to' => 'Model_Warehouselocation',
            'key_to' => 'id',
        ),
        'location_to' => array(
            'key_from' => 'location_to_id',
            'model_to' => 'Model_Warehouselocation',
            'key_to' => 'id',
        )
    );

    /**
     * Calcula el subtotal antes de guardar
     */
    public function _event_before_save()
    {
        $this->subtotal = $this->quantity * $this->unit_cost;
    }

    /**
     * Obtiene el subtotal
     */
    public function get_subtotal()
    {
        return $this->quantity * $this->unit_cost;
    }
}
