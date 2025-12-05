<?php

class Controller_Admin_Compras extends Controller_Admin
{
    public function action_index()
    {
        // Filtros
        $status = Input::get('status', null);
        $provider_id = Input::get('provider_id', null);
        $search = Input::get('search', '');
        
        // Query base
        $query = Model_Purchase::query()
            ->where('deleted_at', null)
            ->related('provider')
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
                ->or_where('invoice_number', 'LIKE', "%{$search}%")
                ->or_where('notes', 'LIKE', "%{$search}%")
                ->or_where('provider.company_name', 'LIKE', "%{$search}%")
                ->where_close();
        }
        
        // Ordenar y paginar
        $query->order_by('invoice_date', 'DESC');
        
        $config = array(
            'pagination_url' => Uri::create('admin/compras/index'),
            'total_items' => $query->count(),
            'per_page' => 20,
            'uri_segment' => 3,
        );
        
        $pagination = Pagination::forge('purchases_pagination', $config);
        $purchases = $query->limit($pagination->per_page)->offset($pagination->offset)->get();
        
        // Estadísticas
        $stats = array(
            'total' => Model_Purchase::query()->where('deleted_at', null)->count(),
            'pending' => Model_Purchase::query()->where('deleted_at', null)->where('status', 'pending')->count(),
            'paid' => Model_Purchase::query()->where('deleted_at', null)->where('status', 'paid')->count(),
            'overdue' => Model_Purchase::query()->where('deleted_at', null)->where('status', 'overdue')->count(),
            'partial' => Model_Purchase::query()->where('deleted_at', null)->where('status', 'partial')->count(),
        );
        
        // Cargar proveedores para filtro
        $providers = Model_Provider::query()
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('company_name', 'ASC')
            ->get();
        
        $this->template->title = 'Facturas de Compra';
        $this->template->content = View::forge('admin/compras/index', array(
            'purchases' => $purchases,
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
            $val = $this->_validate_purchase();
            
            if ($val->run()) {
                try {
                    // Manejar archivos subidos
                    $xml_file = $this->_upload_file('xml_file', 'xml');
                    $pdf_file = $this->_upload_file('pdf_file', 'pdf');
                    
                    $purchase = Model_Purchase::forge(array(
                        'code' => Model_Purchase::generate_code(),
                        'purchase_order_id' => Input::post('purchase_order_id') ?: null,
                        'provider_id' => Input::post('provider_id'),
                        'invoice_number' => Input::post('invoice_number'),
                        'invoice_date' => Input::post('invoice_date'),
                        'due_date' => Input::post('due_date') ?: null,
                        'payment_date' => Input::post('payment_date') ?: null,
                        'status' => Input::post('status', 'pending'),
                        'payment_method' => Input::post('payment_method'),
                        'subtotal' => Input::post('subtotal', 0),
                        'tax' => Input::post('tax', 0),
                        'total' => Input::post('total', 0),
                        'paid_amount' => Input::post('paid_amount', 0),
                        'notes' => Input::post('notes'),
                        'xml_file' => $xml_file,
                        'pdf_file' => $pdf_file,
                        'created_by' => Auth::get_user_id()[1],
                        'created_at' => date('Y-m-d H:i:s'),
                    ));
                    
                    if ($purchase->save()) {
                        $purchase->calculate_balance();
                        
                        Session::set_flash('success', 'Factura de compra creada exitosamente.');
                        Response::redirect('admin/compras');
                    } else {
                        Session::set_flash('error', 'No se pudo guardar la factura.');
                    }
                } catch (Exception $e) {
                    Session::set_flash('error', 'Error: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', 'Por favor corrija los errores.');
            }
        }
        
        // Cargar proveedores y órdenes de compra
        $providers = Model_Provider::query()
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('company_name', 'ASC')
            ->get();
        
        $purchase_orders = Model_Purchaseorder::query()
            ->where('deleted_at', null)
            ->where('status', 'approved')
            ->related('provider')
            ->order_by('order_date', 'DESC')
            ->get();
        
        $this->template->title = 'Nueva Factura de Compra';
        $this->template->content = View::forge('admin/compras/form', array(
            'purchase' => null,
            'providers' => $providers,
            'purchase_orders' => $purchase_orders,
            'is_edit' => false,
        ));
    }
    
    public function action_edit($id = null)
    {
        $purchase = Model_Purchase::query()
            ->where('id', $id)
            ->where('deleted_at', null)
            ->get_one();
        
        if (!$purchase) {
            Session::set_flash('error', 'Factura no encontrada.');
            Response::redirect('admin/compras');
        }
        
        if (!$purchase->can_edit()) {
            Session::set_flash('error', 'Esta factura no puede ser editada.');
            Response::redirect('admin/compras/view/' . $id);
        }
        
        if (Input::method() == 'POST') {
            $val = $this->_validate_purchase();
            
            if ($val->run()) {
                try {
                    // Manejar archivos subidos
                    $xml_file = $this->_upload_file('xml_file', 'xml');
                    $pdf_file = $this->_upload_file('pdf_file', 'pdf');
                    
                    $purchase->purchase_order_id = Input::post('purchase_order_id') ?: null;
                    $purchase->provider_id = Input::post('provider_id');
                    $purchase->invoice_number = Input::post('invoice_number');
                    $purchase->invoice_date = Input::post('invoice_date');
                    $purchase->due_date = Input::post('due_date') ?: null;
                    $purchase->payment_date = Input::post('payment_date') ?: null;
                    $purchase->status = Input::post('status');
                    $purchase->payment_method = Input::post('payment_method');
                    $purchase->subtotal = Input::post('subtotal', 0);
                    $purchase->tax = Input::post('tax', 0);
                    $purchase->total = Input::post('total', 0);
                    $purchase->paid_amount = Input::post('paid_amount', 0);
                    $purchase->notes = Input::post('notes');
                    $purchase->updated_at = date('Y-m-d H:i:s');
                    
                    if ($xml_file) $purchase->xml_file = $xml_file;
                    if ($pdf_file) $purchase->pdf_file = $pdf_file;
                    
                    if ($purchase->save()) {
                        $purchase->calculate_balance();
                        
                        Session::set_flash('success', 'Factura actualizada exitosamente.');
                        Response::redirect('admin/compras');
                    } else {
                        Session::set_flash('error', 'No se pudo actualizar la factura.');
                    }
                } catch (Exception $e) {
                    Session::set_flash('error', 'Error: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', 'Por favor corrija los errores.');
            }
        }
        
        // Cargar proveedores y órdenes de compra
        $providers = Model_Provider::query()
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('company_name', 'ASC')
            ->get();
        
        $purchase_orders = Model_Purchaseorder::query()
            ->where('deleted_at', null)
            ->where('status', 'approved')
            ->related('provider')
            ->order_by('order_date', 'DESC')
            ->get();
        
        $this->template->title = 'Editar Factura de Compra';
        $this->template->content = View::forge('admin/compras/form', array(
            'purchase' => $purchase,
            'providers' => $providers,
            'purchase_orders' => $purchase_orders,
            'is_edit' => true,
        ));
    }
    
    public function action_view($id = null)
    {
        $purchase = Model_Purchase::query()
            ->where('id', $id)
            ->where('deleted_at', null)
            ->related('provider')
            ->related('purchase_order')
            ->related('creator')
            ->get_one();
        
        if (!$purchase) {
            Session::set_flash('error', 'Factura no encontrada.');
            Response::redirect('admin/compras');
        }
        
        $this->template->title = 'Ver Factura de Compra';
        $this->template->content = View::forge('admin/compras/view', array(
            'purchase' => $purchase,
        ));
    }
    
    public function action_delete($id = null)
    {
        $purchase = Model_Purchase::find($id);
        
        if (!$purchase || $purchase->deleted_at) {
            Session::set_flash('error', 'Factura no encontrada.');
            Response::redirect('admin/compras');
        }
        
        if (!$purchase->can_delete()) {
            Session::set_flash('error', 'Esta factura no puede ser eliminada porque ya tiene pagos registrados.');
            Response::redirect('admin/compras');
        }
        
        $purchase->deleted_at = date('Y-m-d H:i:s');
        
        if ($purchase->save()) {
            Session::set_flash('success', 'Factura eliminada exitosamente.');
        } else {
            Session::set_flash('error', 'No se pudo eliminar la factura.');
        }
        
        Response::redirect('admin/compras');
    }
    
    public function action_mark_paid($id = null)
    {
        $purchase = Model_Purchase::find($id);
        
        if (!$purchase || $purchase->deleted_at) {
            Session::set_flash('error', 'Factura no encontrada.');
            Response::redirect('admin/compras');
        }
        
        $payment_date = Input::post('payment_date', date('Y-m-d'));
        $payment_method = Input::post('payment_method', 'Efectivo');
        
        if ($purchase->mark_as_paid($payment_date, $payment_method)) {
            Session::set_flash('success', 'Factura marcada como pagada.');
        } else {
            Session::set_flash('error', 'No se pudo actualizar la factura.');
        }
        
        Response::redirect('admin/compras/view/' . $id);
    }
    
    public function action_add_payment($id = null)
    {
        $purchase = Model_Purchase::find($id);
        
        if (!$purchase || $purchase->deleted_at) {
            Session::set_flash('error', 'Factura no encontrada.');
            Response::redirect('admin/compras');
        }
        
        $amount = Input::post('amount', 0);
        $payment_date = Input::post('payment_date', date('Y-m-d'));
        $payment_method = Input::post('payment_method');
        
        if ($amount <= 0) {
            Session::set_flash('error', 'El monto debe ser mayor a cero.');
            Response::redirect('admin/compras/view/' . $id);
        }
        
        if ($purchase->add_payment($amount, $payment_date, $payment_method)) {
            Session::set_flash('success', 'Pago registrado exitosamente.');
        } else {
            Session::set_flash('error', 'No se pudo registrar el pago.');
        }
        
        Response::redirect('admin/compras/view/' . $id);
    }
    
    private function _validate_purchase()
    {
        $val = Validation::forge('purchase');
        
        $val->add_field('provider_id', 'Proveedor', 'required|valid_string[numeric]');
        $val->add_field('invoice_number', 'Número de Factura', 'required|max_length[100]');
        $val->add_field('invoice_date', 'Fecha de Factura', 'required|valid_string[alpha,numeric,dashes]');
        $val->add_field('subtotal', 'Subtotal', 'required|valid_string[numeric,dots]');
        $val->add_field('tax', 'IVA', 'required|valid_string[numeric,dots]');
        $val->add_field('total', 'Total', 'required|valid_string[numeric,dots]');
        
        return $val;
    }
    
    private function _upload_file($field_name, $type = 'pdf')
    {
        if (empty($_FILES[$field_name]['name'])) {
            return null;
        }
        
        $config = array(
            'path' => DOCROOT . 'uploads/compras/',
            'randomize' => true,
            'ext_whitelist' => $type === 'xml' ? array('xml') : array('pdf'),
        );
        
        Upload::process($config);
        
        if (Upload::is_valid()) {
            Upload::save();
            $file = Upload::get_files(0);
            return 'uploads/compras/' . $file['saved_as'];
        }
        
        return null;
    }
}
