<?php

/**
 * Controller_Admin_Recepciones
 * 
 * Gestiona las recepciones físicas de mercancía al almacén
 * Diferencia con Contrarecibos: Maneja ingreso físico con ubicaciones y condiciones
 */
class Controller_Admin_Recepciones extends Controller_Admin
{
    /**
     * Listado de recepciones con filtros y estadísticas
     */
    public function action_index()
    {
        // Verificar permisos
        if (!Helper_Permission::can('recepciones', 'view')) {
            Session::set_flash('error', 'No tienes permisos para acceder a este módulo');
            Response::redirect('admin');
        }

        // Obtener estadísticas
        $total_receipts = Model_Purchasereceipt::get_total_count();
        $pending = Model_Purchasereceipt::count_by_status('pending');
        $received = Model_Purchasereceipt::count_by_status('received');
        $verified = Model_Purchasereceipt::count_by_status('verified');
        $with_discrepancies = Model_Purchasereceipt::count_with_discrepancies();

        // Configurar paginación
        $config = array(
            'pagination_url' => Uri::create('admin/recepciones/index'),
            'total_items' => 0,
            'per_page' => 20,
            'uri_segment' => 'page',
        );

        // Query base
        $query = Model_Purchasereceipt::query()
            ->related('purchase_order')
            ->related('provider')
            ->related('receiver');

        // Filtros
        if (Input::get('search')) {
            $search = Input::get('search');
            $query->where_open()
                ->where('code', 'LIKE', "%{$search}%")
                ->or_where('almacen_name', 'LIKE', "%{$search}%")
                ->or_where('notes', 'LIKE', "%{$search}%")
                ->where_close();
        }

        if (Input::get('status')) {
            $query->where('status', Input::get('status'));
        }

        if (Input::get('provider_id')) {
            $query->where('provider_id', Input::get('provider_id'));
        }

        if (Input::get('date_from')) {
            $query->where('receipt_date', '>=', Input::get('date_from'));
        }

        if (Input::get('date_to')) {
            $query->where('receipt_date', '<=', Input::get('date_to'));
        }

        if (Input::get('has_discrepancy')) {
            $query->where('has_discrepancy', 1);
        }

        // Total para paginación
        $config['total_items'] = $query->count();
        
        // Crear paginación
        $pagination = Pagination::forge('receipts_pagination', $config);
        
        // Obtener registros
        $receipts = $query
            ->order_by('created_at', 'DESC')
            ->limit($pagination->per_page)
            ->offset($pagination->offset)
            ->get();

        // Obtener proveedores para filtro
        $providers = Model_Provider::query()
            ->where('is_active', 1)
            ->order_by('company_name', 'ASC')
            ->get();

        // Información de paginación (CORRECCIÓN: array, no objeto)
        $pagination_info = array(
            'total' => $config['total_items'],
            'per_page' => $pagination->per_page,
            'current_page' => $pagination->current_page,
            'total_pages' => $pagination->total_pages,
            'offset' => $pagination->offset,
        );

        $data = array(
            'receipts' => $receipts,
            'providers' => $providers,
            'total_receipts' => $total_receipts,
            'pending' => $pending,
            'received' => $received,
            'verified' => $verified,
            'with_discrepancies' => $with_discrepancies,
            'pagination' => $pagination->render(), // CORRECCIÓN: Pasar HTML
            'pagination_info' => $pagination_info,
        );

        $this->template->title = 'Recepciones de Mercancía';
        $this->template->content = View::forge('admin/recepciones/index', $data);
    }

    /**
     * Crear nueva recepción
     */
    public function action_create()
    {
        // Verificar permisos
        if (!Helper_Permission::can('recepciones', 'create')) {
            Session::set_flash('error', 'No tienes permisos para crear recepciones');
            Response::redirect('admin/recepciones');
        }

        if (Input::method() == 'POST') {
            $val = $this->_validate_receipt();
            
            if ($val->run()) {
                try {
                    DB::start_transaction();

                    // Crear recepción
                    $receipt = Model_Purchasereceipt::forge();
                    $receipt->code = Model_Purchasereceipt::generate_code();
                    $receipt->purchase_order_id = Input::post('purchase_order_id');
                    $receipt->provider_id = Input::post('provider_id');
                    $receipt->almacen_name = Input::post('almacen_name');
                    $receipt->receipt_date = Input::post('receipt_date');
                    $receipt->status = 'pending';
                    $receipt->notes = Input::post('notes');
                    $receipt->created_by = Auth::get_user_id()[1];

                    if ($receipt->save()) {
                        // Guardar items
                        $items_data = Input::post('items', array());
                        
                        foreach ($items_data as $item_data) {
                            if (empty($item_data['product_id'])) continue;

                            $item = Model_Purchasereceiptitem::forge();
                            $item->purchase_receipt_id = $receipt->id;
                            $item->purchase_order_item_id = $item_data['purchase_order_item_id'] ?? null;
                            $item->product_id = $item_data['product_id'];
                            $item->location = $item_data['location'] ?? null;
                            $item->quantity_ordered = $item_data['quantity_ordered'];
                            $item->quantity_received = $item_data['quantity_received'];
                            $item->unit_cost = $item_data['unit_cost'];
                            $item->condition = $item_data['condition'] ?? 'good';
                            $item->batch_number = $item_data['batch_number'] ?? null;
                            $item->expiry_date = $item_data['expiry_date'] ?? null;
                            $item->notes = $item_data['notes'] ?? null;
                            $item->save();
                        }

                        // Calcular totales
                        $receipt->calculate_totals();
                        $receipt->update_status();
                        $receipt->save();

                        DB::commit_transaction();

                        Session::set_flash('success', 'Recepción creada exitosamente: ' . $receipt->code);
                        Response::redirect('admin/recepciones/view/' . $receipt->id);
                    }
                } catch (Exception $e) {
                    DB::rollback_transaction();
                    Session::set_flash('error', 'Error al crear la recepción: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', $val->error());
            }
        }

        // Obtener órdenes de compra pendientes/aprobadas
        $purchase_orders = Model_Purchaseorder::query()
            ->where('status', 'IN', array('approved', 'partial'))
            ->order_by('created_at', 'DESC')
            ->get();

        // Obtener proveedores activos (CORRECCIÓN: usar company_name)
        $providers = Model_Provider::query()
            ->where('is_active', 1)
            ->order_by('company_name', 'ASC')
            ->get();

        // Obtener productos activos (CORRECCIÓN: usar is_active)
        $products = Model_Product::query()
            ->where('is_active', 1)
            ->order_by('name', 'ASC')
            ->get();

        $data = array(
            'receipt' => null,
            'purchase_orders' => $purchase_orders,
            'providers' => $providers,
            'products' => $products,
        );

        $this->template->title = 'Nueva Recepción de Mercancía';
        $this->template->content = View::forge('admin/recepciones/form', $data);
    }

    /**
     * Editar recepción existente
     */
    public function action_edit($id = null)
    {
        // Verificar permisos
        if (!Helper_Permission::can('recepciones', 'edit')) {
            Session::set_flash('error', 'No tienes permisos para editar recepciones');
            Response::redirect('admin/recepciones');
        }

        $receipt = Model_Purchasereceipt::find($id);

        if (!$receipt) {
            Session::set_flash('error', 'Recepción no encontrada');
            Response::redirect('admin/recepciones');
        }

        if (!$receipt->can_edit()) {
            Session::set_flash('error', 'Esta recepción no puede ser editada');
            Response::redirect('admin/recepciones/view/' . $id);
        }

        if (Input::method() == 'POST') {
            $val = $this->_validate_receipt();
            
            if ($val->run()) {
                try {
                    DB::start_transaction();

                    // Actualizar datos básicos
                    $receipt->purchase_order_id = Input::post('purchase_order_id');
                    $receipt->provider_id = Input::post('provider_id');
                    $receipt->almacen_name = Input::post('almacen_name');
                    $receipt->receipt_date = Input::post('receipt_date');
                    $receipt->notes = Input::post('notes');

                    if (Input::post('received_date')) {
                        $receipt->received_date = Input::post('received_date');
                        $receipt->received_by = Auth::get_user_id()[1];
                    }

                    // Eliminar items anteriores
                    DB::delete('purchase_receipt_items')
                        ->where('purchase_receipt_id', $receipt->id)
                        ->execute();

                    // Guardar items actualizados
                    $items_data = Input::post('items', array());
                    
                    foreach ($items_data as $item_data) {
                        if (empty($item_data['product_id'])) continue;

                        $item = Model_Purchasereceiptitem::forge();
                        $item->purchase_receipt_id = $receipt->id;
                        $item->purchase_order_item_id = $item_data['purchase_order_item_id'] ?? null;
                        $item->product_id = $item_data['product_id'];
                        $item->location = $item_data['location'] ?? null;
                        $item->quantity_ordered = $item_data['quantity_ordered'];
                        $item->quantity_received = $item_data['quantity_received'];
                        $item->unit_cost = $item_data['unit_cost'];
                        $item->condition = $item_data['condition'] ?? 'good';
                        $item->batch_number = $item_data['batch_number'] ?? null;
                        $item->expiry_date = $item_data['expiry_date'] ?? null;
                        $item->notes = $item_data['notes'] ?? null;
                        $item->save();
                    }

                    // Recalcular totales
                    $receipt->calculate_totals();
                    $receipt->update_status();
                    $receipt->save();

                    DB::commit_transaction();

                    Session::set_flash('success', 'Recepción actualizada exitosamente');
                    Response::redirect('admin/recepciones/view/' . $receipt->id);
                } catch (Exception $e) {
                    DB::rollback_transaction();
                    Session::set_flash('error', 'Error al actualizar la recepción: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', $val->error());
            }
        }

        // Obtener órdenes de compra
        $purchase_orders = Model_Purchaseorder::query()
            ->where('status', 'IN', array('approved', 'partial'))
            ->order_by('created_at', 'DESC')
            ->get();

        // Obtener proveedores activos (CORRECCIÓN: usar company_name)
        $providers = Model_Provider::query()
            ->where('is_active', 1)
            ->order_by('company_name', 'ASC')
            ->get();

        // Obtener productos activos (CORRECCIÓN: usar is_active)
        $products = Model_Product::query()
            ->where('is_active', 1)
            ->order_by('name', 'ASC')
            ->get();

        $data = array(
            'receipt' => $receipt,
            'purchase_orders' => $purchase_orders,
            'providers' => $providers,
            'products' => $products,
        );

        $this->template->title = 'Editar Recepción: ' . $receipt->code;
        $this->template->content = View::forge('admin/recepciones/form', $data);
    }

    /**
     * Ver detalle de recepción
     */
    public function action_view($id = null)
    {
        // Verificar permisos
        if (!Helper_Permission::can('recepciones', 'view')) {
            Session::set_flash('error', 'No tienes permisos para ver recepciones');
            Response::redirect('admin/recepciones');
        }

        $receipt = Model_Purchasereceipt::query()
            ->related('purchase_order')
            ->related('provider')
            ->related('receiver')
            ->related('verifier')
            ->related('creator')
            ->related('items')
            ->related('items.product')
            ->where('id', $id)
            ->get_one();

        if (!$receipt) {
            Session::set_flash('error', 'Recepción no encontrada');
            Response::redirect('admin/recepciones');
        }

        $data = array(
            'receipt' => $receipt,
        );

        $this->template->title = 'Recepción: ' . $receipt->code;
        $this->template->content = View::forge('admin/recepciones/view', $data);
    }

    /**
     * Eliminar recepción (soft delete)
     */
    public function action_delete($id = null)
    {
        // Verificar permisos
        if (!Helper_Permission::can('recepciones', 'delete')) {
            Session::set_flash('error', 'No tienes permisos para eliminar recepciones');
            Response::redirect('admin/recepciones');
        }

        $receipt = Model_Purchasereceipt::find($id);

        if (!$receipt) {
            Session::set_flash('error', 'Recepción no encontrada');
            Response::redirect('admin/recepciones');
        }

        if (!$receipt->can_delete()) {
            Session::set_flash('error', 'Esta recepción no puede ser eliminada');
            Response::redirect('admin/recepciones');
        }

        try {
            $code = $receipt->code;
            $receipt->delete();
            Session::set_flash('success', "Recepción {$code} eliminada exitosamente");
        } catch (Exception $e) {
            Session::set_flash('error', 'Error al eliminar la recepción: ' . $e->getMessage());
        }

        Response::redirect('admin/recepciones');
    }

    /**
     * Marcar recepción como verificada
     */
    public function action_mark_verified($id = null)
    {
        // Verificar permisos
        if (!Helper_Permission::can('recepciones', 'verify')) {
            Session::set_flash('error', 'No tienes permisos para verificar recepciones');
            Response::redirect('admin/recepciones');
        }

        $receipt = Model_Purchasereceipt::find($id);

        if (!$receipt) {
            Session::set_flash('error', 'Recepción no encontrada');
            Response::redirect('admin/recepciones');
        }

        if ($receipt->status !== 'received') {
            Session::set_flash('error', 'Solo se pueden verificar recepciones en estado "Recibido"');
            Response::redirect('admin/recepciones/view/' . $id);
        }

        try {
            $notes = Input::post('verification_notes');
            $receipt->mark_as_verified(null, $notes);
            Session::set_flash('success', 'Recepción verificada exitosamente');
        } catch (Exception $e) {
            Session::set_flash('error', 'Error al verificar la recepción: ' . $e->getMessage());
        }

        Response::redirect('admin/recepciones/view/' . $id);
    }

    /**
     * Marcar recepción como cancelada
     */
    public function action_cancel($id = null)
    {
        // Verificar permisos
        if (!Helper_Permission::can('recepciones', 'cancel')) {
            Session::set_flash('error', 'No tienes permisos para cancelar recepciones');
            Response::redirect('admin/recepciones');
        }

        $receipt = Model_Purchasereceipt::find($id);

        if (!$receipt) {
            Session::set_flash('error', 'Recepción no encontrada');
            Response::redirect('admin/recepciones');
        }

        if ($receipt->status === 'verified') {
            Session::set_flash('error', 'No se pueden cancelar recepciones verificadas');
            Response::redirect('admin/recepciones/view/' . $id);
        }

        try {
            $reason = Input::post('cancellation_reason');
            $receipt->mark_as_cancelled($reason);
            Session::set_flash('success', 'Recepción cancelada exitosamente');
        } catch (Exception $e) {
            Session::set_flash('error', 'Error al cancelar la recepción: ' . $e->getMessage());
        }

        Response::redirect('admin/recepciones/view/' . $id);
    }

    /**
     * Obtener items de una orden de compra (AJAX)
     */
    public function action_get_order_items($order_id = null)
    {
        if (!$order_id) {
            return Response::forge(json_encode(array('error' => 'ID de orden requerido')), 400);
        }

        $order = Model_Purchaseorder::query()
            ->related('items')
            ->related('items.product')
            ->where('id', $order_id)
            ->get_one();

        if (!$order) {
            return Response::forge(json_encode(array('error' => 'Orden no encontrada')), 404);
        }

        $items = array();
        foreach ($order->items as $item) {
            $items[] = array(
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'product_code' => $item->product->code,
                'quantity_ordered' => $item->quantity,
                'unit_cost' => $item->unit_price,
            );
        }

        return Response::forge(json_encode(array(
            'success' => true,
            'provider_id' => $order->provider_id,
            'items' => $items,
        )), 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Validación de recepción
     */
    protected function _validate_receipt()
    {
        $val = Validation::forge();
        
        $val->add_field('purchase_order_id', 'Orden de Compra', 'required|numeric');
        $val->add_field('provider_id', 'Proveedor', 'required|numeric');
        $val->add_field('almacen_name', 'Almacén', 'required|max_length[100]');
        $val->add_field('receipt_date', 'Fecha de Recepción', 'required|valid_date');
        $val->add_field('notes', 'Notas', 'max_length[5000]');

        return $val;
    }
}
