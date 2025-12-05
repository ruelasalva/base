<?php

/**
 * Controller_Admin_Inventario
 * 
 * Gestiona movimientos de inventario:
 * - Entradas, Salidas, Traspasos, Ajustes, Reubicaciones
 * - Control de ubicaciones en almacén
 */
class Controller_Admin_Inventario extends Controller_Admin
{
    /**
     * Listado de movimientos de inventario
     */
    public function action_index()
    {
        if (!Helper_Permission::can('inventario', 'view')) {
            Session::set_flash('error', 'No tienes permisos para ver inventario');
            Response::redirect('admin');
        }

        // Estadísticas
        $total_movements = Model_Inventorymovement::query()->where('deleted_at', null)->count();
        $entries = Model_Inventorymovement::count_by_type('entry');
        $exits = Model_Inventorymovement::count_by_type('exit');
        $transfers = Model_Inventorymovement::count_by_type('transfer');
        $pending = Model_Inventorymovement::count_by_status('pending');

        // Configurar paginación
        $config = array(
            'pagination_url' => Uri::create('admin/inventario/index'),
            'total_items' => 0,
            'per_page' => 20,
            'uri_segment' => 'page',
        );

        // Query base
        $query = Model_Inventorymovement::query();

        // Filtros
        if (Input::get('search')) {
            $search = Input::get('search');
            $query->where_open()
                ->where('code', 'LIKE', "%{$search}%")
                ->or_where('reference_code', 'LIKE', "%{$search}%")
                ->or_where('notes', 'LIKE', "%{$search}%")
                ->where_close();
        }

        if (Input::get('type')) {
            $query->where('type', Input::get('type'));
        }

        if (Input::get('status')) {
            $query->where('status', Input::get('status'));
        }

        if (Input::get('warehouse_id')) {
            $query->where('warehouse_id', Input::get('warehouse_id'));
        }

        if (Input::get('date_from')) {
            $query->where('movement_date', '>=', Input::get('date_from'));
        }

        if (Input::get('date_to')) {
            $query->where('movement_date', '<=', Input::get('date_to'));
        }

        $config['total_items'] = $query->count();
        $pagination = Pagination::forge('movements_pagination', $config);
        
        $movements = $query
            ->order_by('created_at', 'DESC')
            ->limit($pagination->per_page)
            ->offset($pagination->offset)
            ->get();

        // Obtener almacenes para filtro
        $warehouses = DB::select('*')->from('almacenes')->where('is_active', 1)->execute()->as_array();

        $pagination_info = array(
            'total' => $config['total_items'],
            'per_page' => $pagination->per_page,
            'current_page' => $pagination->current_page,
            'total_pages' => $pagination->total_pages,
            'offset' => $pagination->offset,
        );

        $data = array(
            'movements' => $movements,
            'warehouses' => $warehouses,
            'total_movements' => $total_movements,
            'entries' => $entries,
            'exits' => $exits,
            'transfers' => $transfers,
            'pending' => $pending,
            'pagination' => $pagination->render(),
            'pagination_info' => $pagination_info,
        );

        $this->template->title = 'Movimientos de Inventario';
        $this->template->content = View::forge('admin/inventario/index', $data);
    }

    /**
     * Crear nuevo movimiento
     */
    public function action_create($type = 'entry')
    {
        if (!Helper_Permission::can('inventario', 'create')) {
            Session::set_flash('error', 'No tienes permisos para crear movimientos');
            Response::redirect('admin/inventario');
        }

        if (Input::method() == 'POST') {
            $val = $this->_validate_movement();
            
            if ($val->run()) {
                try {
                    DB::start_transaction();

                    $movement = Model_Inventorymovement::forge();
                    $movement->code = Model_Inventorymovement::generate_code(Input::post('type'));
                    $movement->type = Input::post('type');
                    $movement->subtype = Input::post('subtype');
                    $movement->warehouse_id = Input::post('warehouse_id');
                    $movement->warehouse_to_id = Input::post('warehouse_to_id');
                    $movement->movement_date = Input::post('movement_date');
                    $movement->status = 'draft';
                    $movement->notes = Input::post('notes');
                    $movement->reason = Input::post('reason');
                    $movement->created_by = Auth::get_user_id()[1];

                    if ($movement->save()) {
                        // Guardar items
                        $items_data = Input::post('items', array());
                        
                        foreach ($items_data as $item_data) {
                            if (empty($item_data['product_id'])) continue;

                            $item = Model_Inventorymovementitem::forge();
                            $item->movement_id = $movement->id;
                            $item->product_id = $item_data['product_id'];
                            $item->location_from_id = $item_data['location_from_id'] ?? null;
                            $item->location_to_id = $item_data['location_to_id'] ?? null;
                            $item->quantity = $item_data['quantity'];
                            $item->unit_cost = $item_data['unit_cost'];
                            $item->batch_number = $item_data['batch_number'] ?? null;
                            $item->expiry_date = $item_data['expiry_date'] ?? null;
                            $item->notes = $item_data['notes'] ?? null;
                            $item->save();
                        }

                        $movement->calculate_totals();
                        $movement->save();

                        DB::commit_transaction();

                        Session::set_flash('success', 'Movimiento creado exitosamente: ' . $movement->code);
                        Response::redirect('admin/inventario/view/' . $movement->id);
                    }
                } catch (Exception $e) {
                    DB::rollback_transaction();
                    Session::set_flash('error', 'Error al crear el movimiento: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', $val->error());
            }
        }

        $warehouses = DB::select('*')->from('almacenes')->where('is_active', 1)->execute()->as_array();
        $products = Model_Product::query()->where('is_active', 1)->order_by('name', 'ASC')->get();
        $locations = Model_Warehouselocation::query()->where('is_active', 1)->get();

        $data = array(
            'movement' => null,
            'type' => $type,
            'warehouses' => $warehouses,
            'products' => $products,
            'locations' => $locations,
        );

        $this->template->title = 'Nuevo Movimiento de Inventario';
        $this->template->content = View::forge('admin/inventario/form', $data);
    }

    /**
     * Editar movimiento
     */
    public function action_edit($id = null)
    {
        if (!Helper_Permission::can('inventario', 'edit')) {
            Session::set_flash('error', 'No tienes permisos para editar movimientos');
            Response::redirect('admin/inventario');
        }

        $movement = Model_Inventorymovement::find($id);

        if (!$movement) {
            Session::set_flash('error', 'Movimiento no encontrado');
            Response::redirect('admin/inventario');
        }

        if (!$movement->can_edit()) {
            Session::set_flash('error', 'Este movimiento no puede ser editado');
            Response::redirect('admin/inventario/view/' . $id);
        }

        if (Input::method() == 'POST') {
            $val = $this->_validate_movement();
            
            if ($val->run()) {
                try {
                    DB::start_transaction();

                    $movement->type = Input::post('type');
                    $movement->subtype = Input::post('subtype');
                    $movement->warehouse_id = Input::post('warehouse_id');
                    $movement->warehouse_to_id = Input::post('warehouse_to_id');
                    $movement->movement_date = Input::post('movement_date');
                    $movement->notes = Input::post('notes');
                    $movement->reason = Input::post('reason');

                    // Eliminar items anteriores
                    DB::delete('inventory_movement_items')
                        ->where('movement_id', $movement->id)
                        ->execute();

                    // Guardar items actualizados
                    $items_data = Input::post('items', array());
                    
                    foreach ($items_data as $item_data) {
                        if (empty($item_data['product_id'])) continue;

                        $item = Model_Inventorymovementitem::forge();
                        $item->movement_id = $movement->id;
                        $item->product_id = $item_data['product_id'];
                        $item->location_from_id = $item_data['location_from_id'] ?? null;
                        $item->location_to_id = $item_data['location_to_id'] ?? null;
                        $item->quantity = $item_data['quantity'];
                        $item->unit_cost = $item_data['unit_cost'];
                        $item->batch_number = $item_data['batch_number'] ?? null;
                        $item->expiry_date = $item_data['expiry_date'] ?? null;
                        $item->notes = $item_data['notes'] ?? null;
                        $item->save();
                    }

                    $movement->calculate_totals();
                    $movement->save();

                    DB::commit_transaction();

                    Session::set_flash('success', 'Movimiento actualizado exitosamente');
                    Response::redirect('admin/inventario/view/' . $movement->id);
                } catch (Exception $e) {
                    DB::rollback_transaction();
                    Session::set_flash('error', 'Error al actualizar: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', $val->error());
            }
        }

        $warehouses = DB::select('*')->from('almacenes')->where('is_active', 1)->execute()->as_array();
        $products = Model_Product::query()->where('is_active', 1)->order_by('name', 'ASC')->get();
        $locations = Model_Warehouselocation::query()->where('is_active', 1)->get();

        $data = array(
            'movement' => $movement,
            'type' => $movement->type,
            'warehouses' => $warehouses,
            'products' => $products,
            'locations' => $locations,
        );

        $this->template->title = 'Editar Movimiento: ' . $movement->code;
        $this->template->content = View::forge('admin/inventario/form', $data);
    }

    /**
     * Ver detalle
     */
    public function action_view($id = null)
    {
        if (!Helper_Permission::can('inventario', 'view')) {
            Session::set_flash('error', 'No tienes permisos para ver movimientos');
            Response::redirect('admin/inventario');
        }

        $movement = Model_Inventorymovement::query()
            ->related('items')
            ->related('items.product')
            ->related('items.location_from')
            ->related('items.location_to')
            ->related('approver')
            ->related('applier')
            ->related('creator')
            ->where('id', $id)
            ->get_one();

        if (!$movement) {
            Session::set_flash('error', 'Movimiento no encontrado');
            Response::redirect('admin/inventario');
        }

        // Obtener almacenes
        $warehouse_from = DB::select('*')->from('almacenes')->where('id', $movement->warehouse_id)->execute()->current();
        $warehouse_to = $movement->warehouse_to_id ? DB::select('*')->from('almacenes')->where('id', $movement->warehouse_to_id)->execute()->current() : null;

        $data = array(
            'movement' => $movement,
            'warehouse_from' => $warehouse_from,
            'warehouse_to' => $warehouse_to,
        );

        $this->template->title = 'Movimiento: ' . $movement->code;
        $this->template->content = View::forge('admin/inventario/view', $data);
    }

    /**
     * Eliminar movimiento
     */
    public function action_delete($id = null)
    {
        if (!Helper_Permission::can('inventario', 'delete')) {
            Session::set_flash('error', 'No tienes permisos para eliminar movimientos');
            Response::redirect('admin/inventario');
        }

        $movement = Model_Inventorymovement::find($id);

        if (!$movement) {
            Session::set_flash('error', 'Movimiento no encontrado');
            Response::redirect('admin/inventario');
        }

        if (!$movement->can_delete()) {
            Session::set_flash('error', 'Este movimiento no puede ser eliminado');
            Response::redirect('admin/inventario');
        }

        try {
            $code = $movement->code;
            $movement->delete();
            Session::set_flash('success', "Movimiento {$code} eliminado exitosamente");
        } catch (Exception $e) {
            Session::set_flash('error', 'Error al eliminar: ' . $e->getMessage());
        }

        Response::redirect('admin/inventario');
    }

    /**
     * Aprobar movimiento
     */
    public function action_approve($id = null)
    {
        if (!Helper_Permission::can('inventario', 'approve')) {
            Session::set_flash('error', 'No tienes permisos para aprobar movimientos');
            Response::redirect('admin/inventario');
        }

        $movement = Model_Inventorymovement::find($id);

        if (!$movement || !$movement->can_approve()) {
            Session::set_flash('error', 'No se puede aprobar este movimiento');
            Response::redirect('admin/inventario');
        }

        try {
            $movement->mark_as_approved();
            Session::set_flash('success', 'Movimiento aprobado exitosamente');
        } catch (Exception $e) {
            Session::set_flash('error', 'Error al aprobar: ' . $e->getMessage());
        }

        Response::redirect('admin/inventario/view/' . $id);
    }

    /**
     * Aplicar movimiento al inventario
     */
    public function action_apply($id = null)
    {
        if (!Helper_Permission::can('inventario', 'apply')) {
            Session::set_flash('error', 'No tienes permisos para aplicar movimientos');
            Response::redirect('admin/inventario');
        }

        $movement = Model_Inventorymovement::find($id);

        if (!$movement || !$movement->can_apply()) {
            Session::set_flash('error', 'No se puede aplicar este movimiento');
            Response::redirect('admin/inventario');
        }

        // Validar stock para salidas y traspasos
        if (!$movement->validate_stock()) {
            Session::set_flash('error', 'Stock insuficiente para aplicar este movimiento');
            Response::redirect('admin/inventario/view/' . $id);
        }

        try {
            $movement->apply_movement();
            Session::set_flash('success', 'Movimiento aplicado exitosamente. El inventario ha sido actualizado.');
        } catch (Exception $e) {
            Session::set_flash('error', 'Error al aplicar movimiento: ' . $e->getMessage());
        }

        Response::redirect('admin/inventario/view/' . $id);
    }

    /**
     * Validación
     */
    protected function _validate_movement()
    {
        $val = Validation::forge();
        
        $val->add_field('type', 'Tipo de Movimiento', 'required');
        $val->add_field('warehouse_id', 'Almacén', 'required|numeric');
        $val->add_field('movement_date', 'Fecha', 'required|valid_date');

        return $val;
    }
}
