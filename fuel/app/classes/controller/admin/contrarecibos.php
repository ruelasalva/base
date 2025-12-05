<?php

class Controller_Admin_Contrarecibos extends Controller_Admin
{
    public function action_index()
    {
        // Filtros
        $status = Input::get('status', null);
        $provider_id = Input::get('provider_id', null);
        $search = Input::get('search', '');
        
        // Query base
        $query = Model_Deliverynote::query()
            ->where('deleted_at', null)
            ->related('provider')
            ->related('purchase')
            ->related('purchase_order')
            ->related('creator');
        
        // Aplicar filtros
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($provider_id) {
            $query->where('provider_id', $provider_id);
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
        $query->order_by('delivery_date', 'DESC');
        
        $config = array(
            'pagination_url' => Uri::create('admin/contrarecibos/index'),
            'total_items' => $query->count(),
            'per_page' => 20,
            'uri_segment' => 3,
        );
        
        $pagination = Pagination::forge('contrarecibos_pagination', $config);
        $delivery_notes = $query->limit($pagination->per_page)->offset($pagination->offset)->get();
        
        // Estadísticas
        $stats = array(
            'total' => Model_Deliverynote::query()->where('deleted_at', null)->count(),
            'pending' => Model_Deliverynote::query()->where('deleted_at', null)->where('status', 'pending')->count(),
            'partial' => Model_Deliverynote::query()->where('deleted_at', null)->where('status', 'partial')->count(),
            'completed' => Model_Deliverynote::query()->where('deleted_at', null)->where('status', 'completed')->count(),
            'rejected' => Model_Deliverynote::query()->where('deleted_at', null)->where('status', 'rejected')->count(),
        );
        
        // Cargar proveedores para filtro
        $providers = Model_Provider::query()
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('company_name', 'ASC')
            ->get();
        
        $this->template->title = 'Contrarecibos';
        $this->template->content = View::forge('admin/contrarecibos/index', array(
            'delivery_notes' => $delivery_notes,
            'pagination' => $pagination->render(),
            'pagination_info' => array(
                'offset' => $pagination->offset,
                'per_page' => $pagination->per_page,
                'total_items' => $pagination->total_items,
                'total_pages' => $pagination->total_pages,
            ),
            'stats' => $stats,
            'providers' => $providers,
            'current_status' => $status,
            'current_provider' => $provider_id,
            'search' => $search,
        ));
    }
    
    public function action_create()
    {
        if (Input::method() == 'POST') {
            $val = $this->_validate_delivery_note();
            
            if ($val->run()) {
                try {
                    DB::start_transaction();
                    
                    // Crear contrarecibo
                    $delivery_note = Model_Deliverynote::forge(array(
                        'code' => Model_Deliverynote::generate_code(),
                        'purchase_id' => Input::post('purchase_id') ?: null,
                        'purchase_order_id' => Input::post('purchase_order_id') ?: null,
                        'provider_id' => Input::post('provider_id'),
                        'delivery_date' => Input::post('delivery_date'),
                        'received_date' => Input::post('received_date') ?: null,
                        'received_by' => Input::post('received_by') ?: null,
                        'status' => Input::post('status', 'pending'),
                        'notes' => Input::post('notes'),
                        'created_by' => Auth::get_user_id()[1],
                    ));
                    
                    if (!$delivery_note->save()) {
                        throw new Exception('Error al guardar el contrarecibo');
                    }
                    
                    // Guardar líneas de productos
                    $products = Input::post('products', array());
                    $quantities_ordered = Input::post('quantities_ordered', array());
                    $quantities_received = Input::post('quantities_received', array());
                    $prices = Input::post('prices', array());
                    $item_notes = Input::post('item_notes', array());
                    
                    foreach ($products as $index => $product_id) {
                        if (!empty($product_id)) {
                            $item = Model_Deliverynoteitem::forge(array(
                                'delivery_note_id' => $delivery_note->id,
                                'product_id' => $product_id,
                                'quantity_ordered' => $quantities_ordered[$index] ?? 0,
                                'quantity_received' => $quantities_received[$index] ?? 0,
                                'unit_price' => $prices[$index] ?? 0,
                                'notes' => $item_notes[$index] ?? null,
                            ));
                            
                            if (!$item->save()) {
                                throw new Exception('Error al guardar línea de producto');
                            }
                        }
                    }
                    
                    // Actualizar estado automáticamente
                    $delivery_note->update_status();
                    $delivery_note->save();
                    
                    DB::commit_transaction();
                    
                    Session::set_flash('success', 'Contrarecibo creado exitosamente');
                    Response::redirect('admin/contrarecibos/view/' . $delivery_note->id);
                    
                } catch (Exception $e) {
                    DB::rollback_transaction();
                    Session::set_flash('error', 'Error al crear contrarecibo: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', $val->error());
            }
        }
        
        // Cargar datos para el formulario
        $providers = Model_Provider::query()
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('company_name', 'ASC')
            ->get();
        
        $purchase_orders = Model_Purchaseorder::query()
            ->where('deleted_at', null)
            ->where('status', 'approved')
            ->order_by('code', 'DESC')
            ->get();
        
        $purchases = Model_Purchase::query()
            ->where('deleted_at', null)
            ->order_by('code', 'DESC')
            ->get();
        
        $products = Model_Product::query()
            ->where('is_active', 1)
            ->order_by('name', 'ASC')
            ->get();
        
        $users = Model_User::query()
            ->order_by('username', 'ASC')
            ->get();
        
        $this->template->title = 'Nuevo Contrarecibo';
        $this->template->content = View::forge('admin/contrarecibos/form', array(
            'delivery_note' => null,
            'providers' => $providers,
            'purchase_orders' => $purchase_orders,
            'purchases' => $purchases,
            'products' => $products,
            'users' => $users,
        ));
    }
    
    public function action_edit($id = null)
    {
        $delivery_note = Model_Deliverynote::find($id);
        
        if (!$delivery_note || $delivery_note->deleted_at) {
            Session::set_flash('error', 'Contrarecibo no encontrado');
            Response::redirect('admin/contrarecibos/index');
        }
        
        if (!$delivery_note->can_edit()) {
            Session::set_flash('error', 'Este contrarecibo no puede ser editado');
            Response::redirect('admin/contrarecibos/view/' . $id);
        }
        
        if (Input::method() == 'POST') {
            $val = $this->_validate_delivery_note();
            
            if ($val->run()) {
                try {
                    DB::start_transaction();
                    
                    // Actualizar contrarecibo
                    $delivery_note->purchase_id = Input::post('purchase_id') ?: null;
                    $delivery_note->purchase_order_id = Input::post('purchase_order_id') ?: null;
                    $delivery_note->provider_id = Input::post('provider_id');
                    $delivery_note->delivery_date = Input::post('delivery_date');
                    $delivery_note->received_date = Input::post('received_date') ?: null;
                    $delivery_note->received_by = Input::post('received_by') ?: null;
                    $delivery_note->status = Input::post('status');
                    $delivery_note->notes = Input::post('notes');
                    
                    if (!$delivery_note->save()) {
                        throw new Exception('Error al actualizar el contrarecibo');
                    }
                    
                    // Eliminar líneas existentes
                    foreach ($delivery_note->items as $item) {
                        $item->delete();
                    }
                    
                    // Guardar nuevas líneas
                    $products = Input::post('products', array());
                    $quantities_ordered = Input::post('quantities_ordered', array());
                    $quantities_received = Input::post('quantities_received', array());
                    $prices = Input::post('prices', array());
                    $item_notes = Input::post('item_notes', array());
                    
                    foreach ($products as $index => $product_id) {
                        if (!empty($product_id)) {
                            $item = Model_Deliverynoteitem::forge(array(
                                'delivery_note_id' => $delivery_note->id,
                                'product_id' => $product_id,
                                'quantity_ordered' => $quantities_ordered[$index] ?? 0,
                                'quantity_received' => $quantities_received[$index] ?? 0,
                                'unit_price' => $prices[$index] ?? 0,
                                'notes' => $item_notes[$index] ?? null,
                            ));
                            
                            if (!$item->save()) {
                                throw new Exception('Error al guardar línea de producto');
                            }
                        }
                    }
                    
                    // Actualizar estado automáticamente
                    $delivery_note->update_status();
                    $delivery_note->save();
                    
                    DB::commit_transaction();
                    
                    Session::set_flash('success', 'Contrarecibo actualizado exitosamente');
                    Response::redirect('admin/contrarecibos/view/' . $delivery_note->id);
                    
                } catch (Exception $e) {
                    DB::rollback_transaction();
                    Session::set_flash('error', 'Error al actualizar contrarecibo: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', $val->error());
            }
        }
        
        // Cargar datos para el formulario
        $providers = Model_Provider::query()
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('company_name', 'ASC')
            ->get();
        
        $purchase_orders = Model_Purchaseorder::query()
            ->where('deleted_at', null)
            ->where('status', 'approved')
            ->order_by('code', 'DESC')
            ->get();
        
        $purchases = Model_Purchase::query()
            ->where('deleted_at', null)
            ->order_by('code', 'DESC')
            ->get();
        
        $products = Model_Product::query()
            ->where('is_active', 1)
            ->order_by('name', 'ASC')
            ->get();
        
        $users = Model_User::query()
            ->order_by('username', 'ASC')
            ->get();
        
        $this->template->title = 'Editar Contrarecibo';
        $this->template->content = View::forge('admin/contrarecibos/form', array(
            'delivery_note' => $delivery_note,
            'providers' => $providers,
            'purchase_orders' => $purchase_orders,
            'purchases' => $purchases,
            'products' => $products,
            'users' => $users,
        ));
    }
    
    public function action_view($id = null)
    {
        $delivery_note = Model_Deliverynote::find($id);
        
        if (!$delivery_note || $delivery_note->deleted_at) {
            Session::set_flash('error', 'Contrarecibo no encontrado');
            Response::redirect('admin/contrarecibos/index');
        }
        
        $this->template->title = 'Ver Contrarecibo';
        $this->template->content = View::forge('admin/contrarecibos/view', array(
            'delivery_note' => $delivery_note,
        ));
    }
    
    public function action_delete($id = null)
    {
        if (Input::method() == 'POST') {
            $delivery_note = Model_Deliverynote::find($id);
            
            if (!$delivery_note || $delivery_note->deleted_at) {
                Session::set_flash('error', 'Contrarecibo no encontrado');
                Response::redirect('admin/contrarecibos/index');
            }
            
            if (!$delivery_note->can_delete()) {
                Session::set_flash('error', 'Este contrarecibo no puede ser eliminado');
                Response::redirect('admin/contrarecibos/view/' . $id);
            }
            
            try {
                $delivery_note->deleted_at = date('Y-m-d H:i:s');
                $delivery_note->save();
                
                Session::set_flash('success', 'Contrarecibo eliminado exitosamente');
            } catch (Exception $e) {
                Session::set_flash('error', 'Error al eliminar: ' . $e->getMessage());
            }
        }
        
        Response::redirect('admin/contrarecibos/index');
    }
    
    public function action_mark_complete($id = null)
    {
        if (Input::method() == 'POST') {
            $delivery_note = Model_Deliverynote::find($id);
            
            if (!$delivery_note || $delivery_note->deleted_at) {
                Session::set_flash('error', 'Contrarecibo no encontrado');
                Response::redirect('admin/contrarecibos/index');
            }
            
            try {
                $received_by = Input::post('received_by') ?: Auth::get_user_id()[1];
                $delivery_note->mark_as_completed($received_by);
                
                Session::set_flash('success', 'Contrarecibo marcado como completado');
            } catch (Exception $e) {
                Session::set_flash('error', 'Error: ' . $e->getMessage());
            }
        }
        
        Response::redirect('admin/contrarecibos/view/' . $id);
    }
    
    public function action_mark_rejected($id = null)
    {
        if (Input::method() == 'POST') {
            $delivery_note = Model_Deliverynote::find($id);
            
            if (!$delivery_note || $delivery_note->deleted_at) {
                Session::set_flash('error', 'Contrarecibo no encontrado');
                Response::redirect('admin/contrarecibos/index');
            }
            
            try {
                $reason = Input::post('reason');
                $delivery_note->mark_as_rejected($reason);
                
                Session::set_flash('success', 'Contrarecibo marcado como rechazado');
            } catch (Exception $e) {
                Session::set_flash('error', 'Error: ' . $e->getMessage());
            }
        }
        
        Response::redirect('admin/contrarecibos/view/' . $id);
    }
    
    private function _validate_delivery_note()
    {
        $val = Validation::forge();
        
        $val->add_field('provider_id', 'Proveedor', 'required|valid_string[numeric]');
        $val->add_field('delivery_date', 'Fecha de Entrega', 'required|valid_string[numeric]');
        $val->add_field('status', 'Estado', 'required');
        
        return $val;
    }
}
