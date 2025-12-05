<?php

/**
 * Controlador: Provider Receipts (Recepciones de Inventario)
 * Gestión de entradas de mercancía al inventario
 */
class Controller_Admin_Proveedores_Recepciones extends Controller_Admin_Base
{
    public function before()
    {
        parent::before();
        
        if (!Helper_Permission::can('proveedores', 'view')) {
            Session::set_flash('error', 'No tienes permisos para acceder a recepciones.');
            Response::redirect('admin');
        }
    }

    /**
     * Listado de recepciones
     */
    public function action_index()
    {
        $data = array();
        $tenant_id = Session::get('tenant_id', 1);
        
        // Filtros
        $status = Input::get('status', null);
        $provider_id = Input::get('provider_id', null);
        $date_from = Input::get('date_from', null);
        $date_to = Input::get('date_to', null);
        
        // Query base
        $query = DB::select('pir.*', 
                array('p.company_name', 'provider_name'), 
                array('po.code_order', 'order_number'),
                array('u.username', 'received_by_name'))
            ->from(array('provider_inventory_receipts', 'pir'))
            ->join(array('providers', 'p'), 'LEFT')
            ->on('pir.provider_id', '=', 'p.id')
            ->join(array('providers_orders', 'po'), 'LEFT')
            ->on('pir.purchase_order_id', '=', 'po.id')
            ->join(array('users', 'u'), 'LEFT')
            ->on('pir.received_by', '=', 'u.id')
            ->where('pir.tenant_id', $tenant_id)
            ->where('pir.deleted_at', null);
        
        // Aplicar filtros
        if ($status) {
            $query->where('pir.status', $status);
        }
        
        if ($provider_id) {
            $query->where('pir.provider_id', $provider_id);
        }
        
        if ($date_from) {
            $query->where('pir.receipt_date', '>=', $date_from);
        }
        
        if ($date_to) {
            $query->where('pir.receipt_date', '<=', $date_to);
        }
        
        // Paginación
        $per_page = 50;
        $total_items = count($query->execute()->as_array());
        
        $config = array(
            'pagination_url' => Uri::current(),
            'total_items' => $total_items,
            'per_page' => $per_page,
            'uri_segment' => 'page',
        );
        
        $pagination = Pagination::forge('receipts', $config);
        
        // Ejecutar query
        $receipts = $query
            ->order_by('pir.receipt_date', 'desc')
            ->order_by('pir.id', 'desc')
            ->limit($pagination->per_page)
            ->offset($pagination->offset)
            ->execute()
            ->as_array();
        
        // Lista de proveedores para filtro
        $providers = DB::select('id', 'company_name')
            ->from('providers')
            ->where('tenant_id', $tenant_id)
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('company_name')
            ->execute()
            ->as_array();
        
        $data['receipts'] = $receipts;
        $data['providers'] = $providers;
        $data['pagination'] = $pagination;
        $data['filters'] = compact('status', 'provider_id', 'date_from', 'date_to');
        
        $this->template->title = 'Recepciones de Inventario';
        $this->template->content = View::forge('admin/proveedores/recepciones/index', $data);
    }

    /**
     * Crear nueva recepción
     */
    public function action_create($order_id = null)
    {
        if (!Helper_Permission::can('proveedores', 'create')) {
            Session::set_flash('error', 'No tienes permisos para crear recepciones.');
            Response::redirect('admin/proveedores/recepciones');
        }
        
        $data = array();
        $tenant_id = Session::get('tenant_id', 1);
        
        if (Input::method() == 'POST') {
            $val = $this->_validate_receipt();
            
            if ($val->run()) {
                try {
                    DB::start_transaction();
                    
                    // Crear recepción
                    $receipt = Model_Provider_Inventory_Receipt::forge(array(
                        'tenant_id' => $tenant_id,
                        'provider_id' => Input::post('provider_id'),
                        'purchase_order_id' => Input::post('purchase_order_id'),
                        'receipt_number' => Model_Provider_Inventory_Receipt::generate_number(),
                        'receipt_date' => Input::post('receipt_date'),
                        'warehouse_id' => Input::post('warehouse_id'),
                        'received_by' => Auth::get('id'),
                        'invoice_number' => Input::post('invoice_number'),
                        'invoice_date' => Input::post('invoice_date'),
                        'status' => 'received', // Por defecto received
                        'notes' => Input::post('notes'),
                        'total_amount' => 0, // Se calcula después
                    ));
                    
                    $receipt->save();
                    
                    // Guardar detalles
                    $products = Input::post('products', array());
                    $total_amount = 0;
                    
                    foreach ($products as $product_data) {
                        if ($product_data['quantity_received'] > 0) {
                            $subtotal = $product_data['quantity_received'] * $product_data['unit_cost'];
                            $tax = $subtotal * ($product_data['tax_rate'] / 100);
                            $total = $subtotal + $tax;
                            
                            $detail = Model_Provider_Inventory_Receipt_Detail::forge(array(
                                'receipt_id' => $receipt->id,
                                'product_id' => $product_data['product_id'],
                                'quantity_ordered' => $product_data['quantity_ordered'],
                                'quantity_received' => $product_data['quantity_received'],
                                'unit_cost' => $product_data['unit_cost'],
                                'subtotal' => $subtotal,
                                'tax_amount' => $tax,
                                'total' => $total,
                                'lot_number' => isset($product_data['lot_number']) ? $product_data['lot_number'] : null,
                                'expiration_date' => isset($product_data['expiration_date']) ? $product_data['expiration_date'] : null,
                                'notes' => isset($product_data['notes']) ? $product_data['notes'] : null,
                            ));
                            
                            $detail->save();
                            $total_amount += $total;
                        }
                    }
                    
                    // Actualizar total de recepción
                    $receipt->total_amount = $total_amount;
                    $receipt->save();
                    
                    // Auto-verificar y afectar inventario si está configurado
                    if (Config::get('provider.receipt_auto_post', false)) {
                        $this->_post_to_inventory($receipt);
                    }
                    
                    // Log
                    Model_Provider_Log::log_action(
                        $receipt->provider_id,
                        'create_receipt',
                        'receipt',
                        $receipt->id,
                        'Recepción creada: ' . $receipt->receipt_number,
                        null,
                        $receipt->to_array()
                    );
                    
                    DB::commit_transaction();
                    
                    Session::set_flash('success', 'Recepción registrada correctamente.');
                    Response::redirect('admin/proveedores/recepciones/view/' . $receipt->id);
                    
                } catch (Exception $e) {
                    DB::rollback_transaction();
                    Session::set_flash('error', 'Error al crear recepción: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', $val->error());
            }
        }
        
        // Si viene de una orden específica
        if ($order_id) {
            $order = DB::select('*')
                ->from('providers_orders')
                ->where('id', $order_id)
                ->execute()
                ->current();
            
            if ($order) {
                // Obtener detalles de la orden
                $order_details = DB::select('pod.*', 'prod.name as product_name', 'prod.code as product_code')
                    ->from(array('providers_orders_details', 'pod'))
                    ->join(array('products', 'prod'), 'LEFT')
                    ->on('pod.product_id', '=', 'prod.id')
                    ->where('pod.order_id', $order_id)
                    ->where('pod.deleted', 0)
                    ->execute()
                    ->as_array();
                
                $data['order'] = $order;
                $data['order_details'] = $order_details;
            }
        }
        
        // Obtener proveedores
        $providers = DB::select('id', 'company_name')
            ->from('providers')
            ->where('tenant_id', $tenant_id)
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('company_name')
            ->execute()
            ->as_array();
        
        // Obtener almacenes
        $warehouses = DB::select('id', 'name')
            ->from('warehouses')
            ->where('tenant_id', $tenant_id)
            ->where('is_active', 1)
            ->order_by('name')
            ->execute()
            ->as_array();
        
        $data['providers'] = $providers;
        $data['warehouses'] = $warehouses;
        
        $this->template->title = 'Nueva Recepción de Inventario';
        $this->template->content = View::forge('admin/proveedores/recepciones/create', $data);
    }

    /**
     * Ver detalle de recepción
     */
    public function action_view($id = null)
    {
        if (!$id) {
            Response::redirect('admin/proveedores/recepciones');
        }
        
        $receipt = Model_Provider_Inventory_Receipt::find($id);
        
        if (!$receipt) {
            Session::set_flash('error', 'Recepción no encontrada.');
            Response::redirect('admin/proveedores/recepciones');
        }
        
        // Obtener detalles
        $details = DB::select('pird.*', 'p.name as product_name', 'p.code as product_code')
            ->from(array('provider_inventory_receipt_details', 'pird'))
            ->join(array('products', 'p'), 'LEFT')
            ->on('pird.product_id', '=', 'p.id')
            ->where('pird.receipt_id', $id)
            ->execute()
            ->as_array();
        
        $data['receipt'] = $receipt;
        $data['details'] = $details;
        
        $this->template->title = 'Recepción: ' . $receipt->receipt_number;
        $this->template->content = View::forge('admin/proveedores/recepciones/view', $data);
    }

    /**
     * Verificar recepción
     */
    public function action_verify($id = null)
    {
        if (!$id || !Helper_Permission::can('proveedores', 'edit')) {
            Response::redirect('admin/proveedores/recepciones');
        }
        
        $receipt = Model_Provider_Inventory_Receipt::find($id);
        
        if (!$receipt) {
            Session::set_flash('error', 'Recepción no encontrada.');
            Response::redirect('admin/proveedores/recepciones');
        }
        
        if ($receipt->status == 'verified' || $receipt->status == 'posted') {
            Session::set_flash('info', 'La recepción ya está verificada.');
            Response::redirect('admin/proveedores/recepciones/view/' . $id);
        }
        
        try {
            $receipt->status = 'verified';
            $receipt->verified_by = Auth::get('id');
            $receipt->verified_at = date('Y-m-d H:i:s');
            $receipt->save();
            
            // Log
            Model_Provider_Log::log_action(
                $receipt->provider_id,
                'verify_receipt',
                'receipt',
                $receipt->id,
                'Recepción verificada: ' . $receipt->receipt_number
            );
            
            Session::set_flash('success', 'Recepción verificada correctamente.');
            
        } catch (Exception $e) {
            Session::set_flash('error', 'Error al verificar recepción: ' . $e->getMessage());
        }
        
        Response::redirect('admin/proveedores/recepciones/view/' . $id);
    }

    /**
     * Afectar inventario (post)
     */
    public function action_post($id = null)
    {
        if (!$id || !Helper_Permission::can('proveedores', 'edit')) {
            Response::redirect('admin/proveedores/recepciones');
        }
        
        $receipt = Model_Provider_Inventory_Receipt::find($id);
        
        if (!$receipt) {
            Session::set_flash('error', 'Recepción no encontrada.');
            Response::redirect('admin/proveedores/recepciones');
        }
        
        if ($receipt->status == 'posted') {
            Session::set_flash('info', 'La recepción ya está afectada en inventario.');
            Response::redirect('admin/proveedores/recepciones/view/' . $id);
        }
        
        try {
            DB::start_transaction();
            
            $this->_post_to_inventory($receipt);
            
            Session::set_flash('success', 'Recepción afectada en inventario correctamente.');
            
            DB::commit_transaction();
            
        } catch (Exception $e) {
            DB::rollback_transaction();
            Session::set_flash('error', 'Error al afectar inventario: ' . $e->getMessage());
        }
        
        Response::redirect('admin/proveedores/recepciones/view/' . $id);
    }

    /**
     * Afectar inventario (lógica interna)
     */
    private function _post_to_inventory($receipt)
    {
        // Obtener detalles de la recepción
        $details = DB::select('*')
            ->from('provider_inventory_receipt_details')
            ->where('receipt_id', $receipt->id)
            ->execute()
            ->as_array();
        
        foreach ($details as $detail) {
            // Actualizar existencias del producto
            $product = Model_Product::find($detail['product_id']);
            
            if ($product) {
                // Incrementar stock
                $product->stock += $detail['quantity_received'];
                
                // Actualizar costo promedio
                $total_cost = ($product->stock - $detail['quantity_received']) * $product->cost;
                $new_cost = $total_cost + ($detail['quantity_received'] * $detail['unit_cost']);
                $product->cost = $new_cost / $product->stock;
                
                $product->save();
                
                // Crear movimiento de inventario
                $movement = Model_Inventory_Movement::forge(array(
                    'tenant_id' => $receipt->tenant_id,
                    'warehouse_id' => $receipt->warehouse_id,
                    'product_id' => $detail['product_id'],
                    'movement_type' => 'entry',
                    'quantity' => $detail['quantity_received'],
                    'unit_cost' => $detail['unit_cost'],
                    'reference_type' => 'receipt',
                    'reference_id' => $receipt->id,
                    'reference_number' => $receipt->receipt_number,
                    'notes' => 'Entrada por recepción de proveedor',
                    'created_by' => Auth::get('id'),
                ));
                
                $movement->save();
            }
        }
        
        // Actualizar estado de recepción
        $receipt->status = 'posted';
        $receipt->posted_by = Auth::get('id');
        $receipt->posted_at = date('Y-m-d H:i:s');
        $receipt->save();
        
        // Generar póliza contable
        $this->_generate_accounting_entry($receipt);
        
        // Log
        Model_Provider_Log::log_action(
            $receipt->provider_id,
            'post_receipt',
            'receipt',
            $receipt->id,
            'Recepción afectada en inventario: ' . $receipt->receipt_number
        );
        
        return true;
    }

    /**
     * Generar póliza contable
     */
    private function _generate_accounting_entry($receipt)
    {
        // Asiento contable:
        // Debe:  Inventario / Activo    $total_amount
        // Haber: Proveedores (CxP)      $total_amount
        
        $entry_data = array(
            'date' => $receipt->receipt_date,
            'type' => 'entrada',
            'reference' => $receipt->receipt_number,
            'description' => 'Recepción de mercancía - Proveedor: ' . $receipt->provider->company_name,
            'movements' => array(
                array(
                    'account_id' => Config::get('accounting.inventory_account'),
                    'type' => 'debit',
                    'amount' => $receipt->total_amount,
                ),
                array(
                    'account_id' => Config::get('accounting.providers_payable_account'),
                    'type' => 'credit',
                    'amount' => $receipt->total_amount,
                ),
            ),
        );
        
        // Helper_Accounting::create_entry($entry_data);
        
        return true;
    }

    /**
     * Validación
     */
    private function _validate_receipt()
    {
        $val = Validation::forge();
        
        $val->add_field('provider_id', 'Proveedor', 'required|valid_string[numeric]');
        $val->add_field('receipt_date', 'Fecha de Recepción', 'required');
        $val->add_field('warehouse_id', 'Almacén', 'required|valid_string[numeric]');
        
        return $val;
    }
}

