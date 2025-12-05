<?php

/**
 * Model_Warehousezone
 * 
 * Modelo para zonas dentro de almacenes
 */
class Model_Warehousezone extends \Orm\Model
{
    protected static $_table_name = 'warehouse_zones';
    
    protected static $_properties = array(
        'id',
        'warehouse_id',
        'code',
        'name',
        'type',
        'description',
        'is_active',
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

    protected static $_has_many = array(
        'locations' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Warehouselocation',
            'key_to' => 'zone_id',
        )
    );

    /**
     * Obtiene el badge HTML para el tipo de zona
     */
    public function get_type_badge()
    {
        $badges = array(
            'storage' => '<span class="badge bg-primary"><i class="fas fa-warehouse"></i> Almacenamiento</span>',
            'picking' => '<span class="badge bg-info"><i class="fas fa-hand-paper"></i> Picking</span>',
            'receiving' => '<span class="badge bg-success"><i class="fas fa-truck-loading"></i> Recepción</span>',
            'shipping' => '<span class="badge bg-warning"><i class="fas fa-shipping-fast"></i> Envío</span>',
            'cold' => '<span class="badge bg-info"><i class="fas fa-snowflake"></i> Refrigerado</span>',
            'hazardous' => '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Peligroso</span>',
        );
        
        return isset($badges[$this->type]) ? $badges[$this->type] : '<span class="badge bg-secondary">Otro</span>';
    }
}
