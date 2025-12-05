<?php

/**
 * Model_Warehouselocation
 * 
 * Modelo para ubicaciones específicas dentro de almacenes
 */
class Model_Warehouselocation extends \Orm\Model
{
    protected static $_table_name = 'warehouse_locations';
    
    protected static $_properties = array(
        'id',
        'warehouse_id',
        'zone_id',
        'code',
        'aisle',
        'rack',
        'level',
        'bin',
        'capacity',
        'current_usage',
        'is_active',
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
        'zone' => array(
            'key_from' => 'zone_id',
            'model_to' => 'Model_Warehousezone',
            'key_to' => 'id',
        )
    );

    /**
     * Obtiene el porcentaje de uso
     */
    public function get_usage_percentage()
    {
        if ($this->capacity == 0) {
            return 0;
        }
        
        return round(($this->current_usage / $this->capacity) * 100, 2);
    }

    /**
     * Verifica si tiene espacio disponible
     */
    public function has_space($quantity = 0)
    {
        if (!$this->capacity) {
            return true; // Sin límite
        }
        
        return ($this->current_usage + $quantity) <= $this->capacity;
    }

    /**
     * Obtiene el badge de disponibilidad
     */
    public function get_availability_badge()
    {
        $percentage = $this->get_usage_percentage();
        
        if ($percentage >= 90) {
            return '<span class="badge bg-danger">Lleno</span>';
        } elseif ($percentage >= 70) {
            return '<span class="badge bg-warning">Alto</span>';
        } elseif ($percentage >= 40) {
            return '<span class="badge bg-info">Medio</span>';
        } else {
            return '<span class="badge bg-success">Disponible</span>';
        }
    }
}
