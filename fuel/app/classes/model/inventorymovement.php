<?php

/**
 * Model_Inventorymovement
 * 
 * Modelo para gestionar movimientos de inventario:
 * - Entradas (recepciones, compras)
 * - Salidas (ventas, mermas)
 * - Traspasos (entre almacenes)
 * - Ajustes (correcciones)
 * - Reubicaciones (dentro del mismo almacén)
 */
class Model_Inventorymovement extends \Orm\Model
{
    protected static $_table_name = 'inventory_movements';
    
    protected static $_properties = array(
        'id',
        'code',
        'type', // entry, exit, transfer, adjustment, relocation
        'subtype',
        'warehouse_id',
        'warehouse_to_id',
        'reference_type',
        'reference_id',
        'reference_code',
        'movement_date',
        'status', // draft, pending, approved, applied, cancelled
        'total_items',
        'total_quantity',
        'total_cost',
        'notes',
        'reason',
        'approved_by',
        'approved_at',
        'applied_by',
        'applied_at',
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
            'model_to' => 'Model_Inventorymovementitem',
            'key_to' => 'movement_id',
            'cascade_save' => true,
            'cascade_delete' => true,
        )
    );

    protected static $_belongs_to = array(
        'approver' => array(
            'key_from' => 'approved_by',
            'model_to' => 'Model_User',
            'key_to' => 'id',
        ),
        'applier' => array(
            'key_from' => 'applied_by',
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
     * Genera código único según el tipo: ENT-YYYYMM-####
     */
    public static function generate_code($type = 'entry')
    {
        $prefixes = array(
            'entry' => 'ENT',
            'exit' => 'SAL',
            'transfer' => 'TRA',
            'adjustment' => 'AJU',
            'relocation' => 'REU'
        );
        
        $prefix = isset($prefixes[$type]) ? $prefixes[$type] : 'MOV';
        $year_month = date('Ym');
        $code_prefix = $prefix . '-' . $year_month . '-';
        
        $last = DB::select(DB::expr('MAX(CAST(SUBSTRING(code, 13) AS UNSIGNED)) as last_number'))
            ->from('inventory_movements')
            ->where('code', 'LIKE', $code_prefix . '%')
            ->where('deleted_at', 'IS', null)
            ->execute()
            ->current();
        
        $next_number = ($last && $last['last_number']) ? $last['last_number'] + 1 : 1;
        
        return $code_prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtiene el badge HTML para el tipo
     */
    public function get_type_badge()
    {
        $badges = array(
            'entry' => '<span class="badge bg-success"><i class="fas fa-arrow-down"></i> Entrada</span>',
            'exit' => '<span class="badge bg-danger"><i class="fas fa-arrow-up"></i> Salida</span>',
            'transfer' => '<span class="badge bg-info"><i class="fas fa-exchange-alt"></i> Traspaso</span>',
            'adjustment' => '<span class="badge bg-warning"><i class="fas fa-adjust"></i> Ajuste</span>',
            'relocation' => '<span class="badge bg-secondary"><i class="fas fa-map-marker-alt"></i> Reubicación</span>',
        );
        
        return isset($badges[$this->type]) ? $badges[$this->type] : '<span class="badge bg-light">Desconocido</span>';
    }

    /**
     * Obtiene el badge HTML para el estado
     */
    public function get_status_badge()
    {
        $badges = array(
            'draft' => '<span class="badge bg-secondary">Borrador</span>',
            'pending' => '<span class="badge bg-warning">Pendiente</span>',
            'approved' => '<span class="badge bg-info">Aprobado</span>',
            'applied' => '<span class="badge bg-success">Aplicado</span>',
            'cancelled' => '<span class="badge bg-dark">Cancelado</span>',
        );
        
        return isset($badges[$this->status]) ? $badges[$this->status] : '<span class="badge bg-light">Desconocido</span>';
    }

    /**
     * Verifica si el movimiento puede ser editado
     */
    public function can_edit()
    {
        return in_array($this->status, array('draft', 'pending'));
    }

    /**
     * Verifica si el movimiento puede ser eliminado
     */
    public function can_delete()
    {
        return $this->status === 'draft';
    }

    /**
     * Verifica si el movimiento puede ser aprobado
     */
    public function can_approve()
    {
        return $this->status === 'pending';
    }

    /**
     * Verifica si el movimiento puede ser aplicado
     */
    public function can_apply()
    {
        return $this->status === 'approved';
    }

    /**
     * Calcula totales desde los items
     */
    public function calculate_totals()
    {
        $items = $this->items;
        
        $this->total_items = count($items);
        $this->total_quantity = 0;
        $this->total_cost = 0;
        
        foreach ($items as $item) {
            $this->total_quantity += $item->quantity;
            $this->total_cost += $item->subtotal;
        }
    }

    /**
     * Marca como aprobado
     */
    public function mark_as_approved($user_id = null)
    {
        $this->status = 'approved';
        $this->approved_by = $user_id ?: \Auth::get_user_id()[1];
        $this->approved_at = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Aplica el movimiento al inventario
     */
    public function apply_movement($user_id = null)
    {
        if (!$this->can_apply()) {
            throw new Exception('El movimiento debe estar aprobado para poder aplicarse');
        }

        DB::start_transaction();
        
        try {
            foreach ($this->items as $item) {
                $this->_apply_item($item);
            }
            
            $this->status = 'applied';
            $this->applied_by = $user_id ?: \Auth::get_user_id()[1];
            $this->applied_at = date('Y-m-d H:i:s');
            $this->save();
            
            DB::commit_transaction();
            return true;
        } catch (Exception $e) {
            DB::rollback_transaction();
            throw $e;
        }
    }

    /**
     * Aplica un item individual al inventario
     */
    protected function _apply_item($item)
    {
        switch ($this->type) {
            case 'entry':
                $this->_apply_entry($item);
                break;
            case 'exit':
                $this->_apply_exit($item);
                break;
            case 'transfer':
                $this->_apply_transfer($item);
                break;
            case 'adjustment':
                $this->_apply_adjustment($item);
                break;
            case 'relocation':
                $this->_apply_relocation($item);
                break;
        }
    }

    /**
     * Aplica entrada de inventario
     */
    protected function _apply_entry($item)
    {
        // Buscar o crear registro en inventory
        $inventory = DB::select('*')
            ->from('inventory')
            ->where('product_id', $item->product_id)
            ->where('warehouse_id', $this->warehouse_id)
            ->execute()
            ->current();

        if ($inventory) {
            // Actualizar existente
            DB::update('inventory')
                ->set(array('quantity' => DB::expr('quantity + ' . $item->quantity)))
                ->where('id', $inventory['id'])
                ->execute();
        } else {
            // Crear nuevo
            DB::insert('inventory')->set(array(
                'product_id' => $item->product_id,
                'warehouse_id' => $this->warehouse_id,
                'quantity' => $item->quantity,
                'reserved' => 0
            ))->execute();
        }

        // Si hay ubicación destino, actualizar inventory_locations
        if ($item->location_to_id) {
            $this->_update_inventory_location($item->product_id, $this->warehouse_id, 
                                             $item->location_to_id, $item->quantity, 'add');
        }
    }

    /**
     * Aplica salida de inventario
     */
    protected function _apply_exit($item)
    {
        // Restar de inventory
        DB::update('inventory')
            ->set(array('quantity' => DB::expr('quantity - ' . $item->quantity)))
            ->where('product_id', $item->product_id)
            ->where('warehouse_id', $this->warehouse_id)
            ->execute();

        // Si hay ubicación origen, actualizar inventory_locations
        if ($item->location_from_id) {
            $this->_update_inventory_location($item->product_id, $this->warehouse_id, 
                                             $item->location_from_id, $item->quantity, 'subtract');
        }
    }

    /**
     * Aplica traspaso entre almacenes
     */
    protected function _apply_transfer($item)
    {
        // Restar del almacén origen
        DB::update('inventory')
            ->set(array('quantity' => DB::expr('quantity - ' . $item->quantity)))
            ->where('product_id', $item->product_id)
            ->where('warehouse_id', $this->warehouse_id)
            ->execute();

        // Sumar al almacén destino
        $inventory_to = DB::select('*')
            ->from('inventory')
            ->where('product_id', $item->product_id)
            ->where('warehouse_id', $this->warehouse_to_id)
            ->execute()
            ->current();

        if ($inventory_to) {
            DB::update('inventory')
                ->set(array('quantity' => DB::expr('quantity + ' . $item->quantity)))
                ->where('id', $inventory_to['id'])
                ->execute();
        } else {
            DB::insert('inventory')->set(array(
                'product_id' => $item->product_id,
                'warehouse_id' => $this->warehouse_to_id,
                'quantity' => $item->quantity,
                'reserved' => 0
            ))->execute();
        }
    }

    /**
     * Aplica ajuste de inventario
     */
    protected function _apply_adjustment($item)
    {
        // El ajuste establece una cantidad específica
        DB::update('inventory')
            ->set(array('quantity' => $item->quantity))
            ->where('product_id', $item->product_id)
            ->where('warehouse_id', $this->warehouse_id)
            ->execute();
    }

    /**
     * Aplica reubicación dentro del mismo almacén
     */
    protected function _apply_relocation($item)
    {
        // Restar de ubicación origen
        if ($item->location_from_id) {
            $this->_update_inventory_location($item->product_id, $this->warehouse_id, 
                                             $item->location_from_id, $item->quantity, 'subtract');
        }

        // Sumar a ubicación destino
        if ($item->location_to_id) {
            $this->_update_inventory_location($item->product_id, $this->warehouse_id, 
                                             $item->location_to_id, $item->quantity, 'add');
        }
    }

    /**
     * Actualiza inventory_locations
     */
    protected function _update_inventory_location($product_id, $warehouse_id, $location_id, $quantity, $operation = 'add')
    {
        $inv_loc = DB::select('*')
            ->from('inventory_locations')
            ->where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->where('location_id', $location_id)
            ->execute()
            ->current();

        if ($inv_loc) {
            $new_qty = $operation === 'add' 
                ? DB::expr('quantity + ' . $quantity)
                : DB::expr('quantity - ' . $quantity);
                
            DB::update('inventory_locations')
                ->set(array('quantity' => $new_qty))
                ->where('id', $inv_loc['id'])
                ->execute();
        } else if ($operation === 'add') {
            DB::insert('inventory_locations')->set(array(
                'product_id' => $product_id,
                'warehouse_id' => $warehouse_id,
                'location_id' => $location_id,
                'quantity' => $quantity
            ))->execute();
        }
    }

    /**
     * Obtiene stock disponible de un producto en un almacén
     */
    public static function get_available_stock($product_id, $warehouse_id)
    {
        $result = DB::select('quantity', 'reserved')
            ->from('inventory')
            ->where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->execute()
            ->current();

        if ($result) {
            return floatval($result['quantity']) - floatval($result['reserved']);
        }
        
        return 0;
    }

    /**
     * Valida si hay stock suficiente para una salida
     */
    public function validate_stock()
    {
        if ($this->type !== 'exit' && $this->type !== 'transfer') {
            return true;
        }

        foreach ($this->items as $item) {
            $available = static::get_available_stock($item->product_id, $this->warehouse_id);
            
            if ($available < $item->quantity) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Estadísticas por tipo
     */
    public static function count_by_type($type)
    {
        return static::query()
            ->where('type', $type)
            ->where('deleted_at', null)
            ->count();
    }

    /**
     * Estadísticas por estado
     */
    public static function count_by_status($status)
    {
        return static::query()
            ->where('status', $status)
            ->where('deleted_at', null)
            ->count();
    }
}
