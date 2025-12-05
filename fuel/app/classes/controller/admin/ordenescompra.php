<?php

class Controller_Admin_Ordenescompra extends Controller_Admin
{
    public function action_index()
    {
        // Filtros
        $status = Input::get('status', null);
        $type = Input::get('type', null);
        $search = Input::get('search', '');
        
        // Query base
        $query = Model_Purchaseorder::query()
            ->where('deleted_at', null)
            ->related('provider')
            ->related('creator');
        
        // Aplicar filtros
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        // Búsqueda
        if (!empty($search)) {
            $query->where_open()
                ->where('code', 'LIKE', "%{$search}%")
                ->or_where('notes', 'LIKE', "%{$search}%")
                ->or_where('provider.company_name', 'LIKE', "%{$search}%")
                ->where_close();
        }
        
        // Ordenar y paginar
        $query->order_by('created_at', 'DESC');
        
        $config = array(
            'pagination_url' => Uri::create('admin/ordenescompra/index'),
            'total_items' => $query->count(),
            'per_page' => 20,
            'uri_segment' => 3,
        );
        
        $pagination = Pagination::forge('ordenes_pagination', $config);
        $orders = $query->limit($pagination->per_page)->offset($pagination->offset)->get();
        
        // Estadísticas
        $stats = array(
            'total' => Model_Purchaseorder::query()->where('deleted_at', null)->count(),
            'draft' => Model_Purchaseorder::query()->where('deleted_at', null)->where('status', 'draft')->count(),
            'pending' => Model_Purchaseorder::query()->where('deleted_at', null)->where('status', 'pending')->count(),
            'approved' => Model_Purchaseorder::query()->where('deleted_at', null)->where('status', 'approved')->count(),
            'received' => Model_Purchaseorder::query()->where('deleted_at', null)->where('status', 'received')->count(),
        );
        
        $this->template->title = 'Órdenes de Compra';
        $this->template->content = View::forge('admin/ordenescompra/index', array(
            'orders' => $orders,
            'pagination' => $pagination->render(),
            'pagination_info' => array(
                'offset' => $pagination->offset,
                'per_page' => $pagination->per_page,
                'total_items' => $pagination->total_items,
                'total_pages' => $pagination->total_pages,
            ),
            'stats' => $stats,
            'current_status' => $status,
            'current_type' => $type,
            'search' => $search,
        ));
    }
    
    public function action_create()
    {
        if (Input::method() == 'POST') {
            $val = $this->_validate_order();
            
            if ($val->run()) {
                try {
                    DB::start_transaction();
                    
                    // Crear orden
                    $order = Model_Purchaseorder::forge(array(
                        'code' => Model_Purchaseorder::generate_code(),
                        'provider_id' => Input::post('provider_id'),
                        'order_date' => Input::post('order_date'),
                        'delivery_date' => Input::post('delivery_date') ?: null,
                        'type' => Input::post('type', 'inventory'),
                        'status' => Input::post('status', 'draft'),
                        'notes' => Input::post('notes'),
                        'created_by' => Auth::get_user_id()[1],
                        'created_at' => date('Y-m-d H:i:s'),
                    ));
                    
                    if (!$order->save()) {
                        throw new Exception('Error al guardar la orden');
                    }
                    
                    // Crear items
                    $items = Input::post('items', array());
                    foreach ($items as $item_data) {
                        if (empty($item_data['description']) || empty($item_data['quantity'])) {
                            continue;
                        }
                        
                        $item = Model_Purchaseorder_Item::forge(array(
                            'purchase_order_id' => $order->id,
                            'product_id' => !empty($item_data['product_id']) ? $item_data['product_id'] : null,
                            'item_type' => $item_data['item_type'],
                            'description' => $item_data['description'],
                            'quantity' => $item_data['quantity'],
                            'unit_price' => $item_data['unit_price'],
                            'tax_rate' => $item_data['tax_rate'] ?: 0,
                        ));
                        
                        if (!$item->save()) {
                            throw new Exception('Error al guardar item');
                        }
                    }
                    
                    // Calcular totales
                    $order->calculate_totals();
                    
                    DB::commit_transaction();
                    
                    Session::set_flash('success', 'Orden de compra creada exitosamente.');
                    Response::redirect('admin/ordenescompra');
                    
                } catch (Exception $e) {
                    DB::rollback_transaction();
                    Session::set_flash('error', 'Error: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', 'Por favor corrija los errores.');
            }
        }
        
        // Cargar proveedores y productos
        $providers = Model_Provider::query()
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('company_name', 'ASC')
            ->get();
        
        $products = Model_Product::query()
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('name', 'ASC')
            ->get();
        
        $this->template->title = 'Editar Orden de Compra';
        $this->template->content = View::forge('admin/ordenescompra/form', array(
            'order' => null,
            'providers' => $providers,
            'products' => $products,
            'is_edit' => false,
        ));
    }
    
    public function action_edit($id = null)
    {
        $order = Model_Purchaseorder::query()
            ->where('id', $id)
            ->where('deleted_at', null)
            ->related('items')
            ->get_one();
        
        if (!$order) {
            Session::set_flash('error', 'Orden no encontrada.');
            Response::redirect('admin/ordenescompra');
        }
        
        // Solo se pueden editar borradores
        if (!$order->can_edit()) {
            Session::set_flash('error', 'Esta orden no puede ser editada.');
            Response::redirect('admin/ordenescompra/view/' . $id);
        }
        
        if (Input::method() == 'POST') {
            $val = $this->_validate_order();
            
            if ($val->run()) {
                try {
                    DB::start_transaction();
                    
                    // Actualizar orden
                    $order->provider_id = Input::post('provider_id');
                    $order->order_date = Input::post('order_date');
                    $order->delivery_date = Input::post('delivery_date') ?: null;
                    $order->type = Input::post('type');
                    $order->status = Input::post('status', 'draft');
                    $order->notes = Input::post('notes');
                    $order->updated_at = date('Y-m-d H:i:s');
                    
                    if (!$order->save()) {
                        throw new Exception('Error al actualizar la orden');
                    }
                    
                    // Eliminar items antiguos
                    foreach ($order->items as $old_item) {
                        $old_item->delete();
                    }
                    
                    // Crear nuevos items
                    $items = Input::post('items', array());
                    foreach ($items as $item_data) {
                        if (empty($item_data['description']) || empty($item_data['quantity'])) {
                            continue;
                        }
                        
                        $item = Model_Purchaseorder_Item::forge(array(
                            'purchase_order_id' => $order->id,
                            'product_id' => !empty($item_data['product_id']) ? $item_data['product_id'] : null,
                            'item_type' => $item_data['item_type'],
                            'description' => $item_data['description'],
                            'quantity' => $item_data['quantity'],
                            'unit_price' => $item_data['unit_price'],
                            'tax_rate' => $item_data['tax_rate'] ?: 0,
                        ));
                        
                        if (!$item->save()) {
                            throw new Exception('Error al guardar item');
                        }
                    }
                    
                    // Calcular totales
                    $order->calculate_totals();
                    
                    DB::commit_transaction();
                    
                    Session::set_flash('success', 'Orden actualizada exitosamente.');
                    Response::redirect('admin/ordenescompra');
                    
                } catch (Exception $e) {
                    DB::rollback_transaction();
                    Session::set_flash('error', 'Error: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', 'Por favor corrija los errores.');
            }
        }
        
        // Cargar proveedores y productos
        $providers = Model_Provider::query()
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('company_name', 'ASC')
            ->get();
        
        $products = Model_Product::query()
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('name', 'ASC')
            ->get();
        
        $this->template->title = 'Nueva Orden de Compra';
        $this->template->content = View::forge('admin/ordenescompra/form', array(
            'order' => $order,
            'providers' => $providers,
            'products' => $products,
            'is_edit' => true,
        ));
    }
    
    public function action_view($id = null)
    {
        $order = Model_Purchaseorder::query()
            ->where('id', $id)
            ->where('deleted_at', null)
            ->related('provider')
            ->related('creator')
            ->related('approver')
            ->related('rejecter')
            ->related('receiver')
            ->related('items')
            ->related('items.product')
            ->get_one();
        
        if (!$order) {
            Session::set_flash('error', 'Orden no encontrada.');
            Response::redirect('admin/ordenescompra');
        }
        
        $this->template->title = 'Ver Orden de Compra';
        $this->template->content = View::forge('admin/ordenescompra/view', array(
            'order' => $order,
        ));
    }
    
    public function action_delete($id = null)
    {
        $order = Model_Purchaseorder::find($id);
        
        if (!$order || $order->deleted_at) {
            Session::set_flash('error', 'Orden no encontrada.');
            Response::redirect('admin/ordenescompra');
        }
        
        // Solo se pueden eliminar borradores
        if ($order->status != 'draft') {
            Session::set_flash('error', 'Solo se pueden eliminar órdenes en borrador.');
            Response::redirect('admin/ordenescompra');
        }
        
        $order->deleted_at = date('Y-m-d H:i:s');
        
        if ($order->save()) {
            Session::set_flash('success', 'Orden eliminada exitosamente.');
        } else {
            Session::set_flash('error', 'No se pudo eliminar la orden.');
        }
        
        Response::redirect('admin/ordenescompra');
    }
    
    public function action_approve($id = null)
    {
        $order = Model_Purchaseorder::find($id);
        
        if (!$order || $order->deleted_at) {
            Session::set_flash('error', 'Orden no encontrada.');
            Response::redirect('admin/ordenescompra');
        }
        
        if (!$order->can_approve()) {
            Session::set_flash('error', 'Esta orden no puede ser aprobada.');
            Response::redirect('admin/ordenescompra/view/' . $id);
        }
        
        if ($order->approve(Auth::get_user_id()[1])) {
            Session::set_flash('success', 'Orden aprobada exitosamente.');
        } else {
            Session::set_flash('error', 'No se pudo aprobar la orden.');
        }
        
        Response::redirect('admin/ordenescompra/view/' . $id);
    }
    
    public function action_reject($id = null)
    {
        $order = Model_Purchaseorder::find($id);
        
        if (!$order || $order->deleted_at) {
            Session::set_flash('error', 'Orden no encontrada.');
            Response::redirect('admin/ordenescompra');
        }
        
        if (!$order->can_reject()) {
            Session::set_flash('error', 'Esta orden no puede ser rechazada.');
            Response::redirect('admin/ordenescompra/view/' . $id);
        }
        
        $reason = Input::post('rejection_reason', '');
        
        if (empty($reason)) {
            Session::set_flash('error', 'Debe proporcionar una razón de rechazo.');
            Response::redirect('admin/ordenescompra/view/' . $id);
        }
        
        if ($order->reject(Auth::get_user_id()[1], $reason)) {
            Session::set_flash('success', 'Orden rechazada.');
        } else {
            Session::set_flash('error', 'No se pudo rechazar la orden.');
        }
        
        Response::redirect('admin/ordenescompra/view/' . $id);
    }
    
    public function action_receive($id = null)
    {
        $order = Model_Purchaseorder::find($id);
        
        if (!$order || $order->deleted_at) {
            Session::set_flash('error', 'Orden no encontrada.');
            Response::redirect('admin/ordenescompra');
        }
        
        if (!$order->can_receive()) {
            Session::set_flash('error', 'Esta orden no puede ser recibida.');
            Response::redirect('admin/ordenescompra/view/' . $id);
        }
        
        if ($order->receive(Auth::get_user_id()[1])) {
            Session::set_flash('success', 'Orden marcada como recibida.');
        } else {
            Session::set_flash('error', 'No se pudo marcar la orden como recibida.');
        }
        
        Response::redirect('admin/ordenescompra/view/' . $id);
    }
    
    private function _validate_order()
    {
        $val = Validation::forge('order');
        
        $val->add_field('provider_id', 'Proveedor', 'required|valid_string[numeric]');
        $val->add_field('order_date', 'Fecha de Orden', 'required|valid_string[alpha,numeric,dashes]');
        $val->add_field('type', 'Tipo', 'required|match_pattern[/^(inventory|usage|service)$/]');
        $val->add_field('status', 'Estado', 'match_pattern[/^(draft|pending|approved)$/]');
        
        return $val;
    }
}
