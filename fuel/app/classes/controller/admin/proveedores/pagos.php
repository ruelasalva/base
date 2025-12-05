<?php

/**
 * Controlador: Provider Payments
 * Gestión de pagos a proveedores
 */
class Controller_Admin_Proveedores_Pagos extends Controller_Admin_Base
{
    public function before()
    {
        parent::before();
        
        if (!Helper_Permission::can('proveedores', 'view')) {
            Session::set_flash('error', 'No tienes permisos para acceder a pagos de proveedores.');
            Response::redirect('admin');
        }
    }

    /**
     * Listado de pagos
     */
    public function action_index()
    {
        $data = array();
        $tenant_id = Session::get('tenant_id', 1);
        
        // Filtros
        $provider_id = Input::get('provider_id', null);
        $status = Input::get('status', null);
        $date_from = Input::get('date_from', null);
        $date_to = Input::get('date_to', null);
        
        // Query base
        $query = DB::select('pp.*', array('p.company_name', 'provider_name'))
            ->from(array('provider_payments', 'pp'))
            ->join(array('providers', 'p'), 'LEFT')
            ->on('pp.provider_id', '=', 'p.id')
            ->where('pp.tenant_id', $tenant_id)
            ->where('pp.deleted_at', null);
        
        // Aplicar filtros
        if ($provider_id) {
            $query->where('pp.provider_id', $provider_id);
        }
        
        if ($status) {
            $query->where('pp.status', $status);
        }
        
        if ($date_from) {
            $query->where('pp.payment_date', '>=', $date_from);
        }
        
        if ($date_to) {
            $query->where('pp.payment_date', '<=', $date_to);
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
        
        $pagination = Pagination::forge('payments', $config);
        
        // Ejecutar query con paginación
        $payments = $query
            ->order_by('pp.payment_date', 'desc')
            ->order_by('pp.id', 'desc')
            ->limit($pagination->per_page)
            ->offset($pagination->offset)
            ->execute()
            ->as_array();
        
        // Obtener lista de proveedores para filtro
        $providers = DB::select('id', 'company_name')
            ->from('providers')
            ->where('tenant_id', $tenant_id)
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->order_by('company_name')
            ->execute()
            ->as_array();
        
        $data['payments'] = $payments;
        $data['providers'] = $providers;
        $data['pagination'] = $pagination;
        $data['filters'] = array(
            'provider_id' => $provider_id,
            'status' => $status,
            'date_from' => $date_from,
            'date_to' => $date_to,
        );
        
        $this->template->title = 'Pagos a Proveedores';
        $this->template->content = View::forge('admin/proveedores/pagos/index', $data);
    }

    /**
     * Crear nuevo pago
     */
    public function action_create($provider_id = null)
    {
        if (!Helper_Permission::can('proveedores', 'create')) {
            Session::set_flash('error', 'No tienes permisos para crear pagos.');
            Response::redirect('admin/proveedores/pagos');
        }
        
        $data = array();
        $tenant_id = Session::get('tenant_id', 1);
        
        if (Input::method() == 'POST') {
            $val = $this->_validate_payment();
            
            if ($val->run()) {
                try {
                    DB::start_transaction();
                    
                    // Crear pago
                    $payment = Model_Provider_Payment::forge(array(
                        'tenant_id' => $tenant_id,
                        'provider_id' => Input::post('provider_id'),
                        'payment_number' => Model_Provider_Payment::generate_number(),
                        'payment_date' => Input::post('payment_date'),
                        'payment_method' => Input::post('payment_method'),
                        'reference_number' => Input::post('reference_number'),
                        'amount' => Input::post('amount'),
                        'currency' => Input::post('currency', 'MXN'),
                        'exchange_rate' => Input::post('exchange_rate', 1.0000),
                        'bank_account_id' => Input::post('bank_account_id'),
                        'notes' => Input::post('notes'),
                        'status' => Input::post('status', 'draft'),
                        'created_by' => Auth::get('id'),
                    ));
                    
                    $payment->save();
                    
                    // Asignar pagos a facturas/órdenes
                    $allocations = Input::post('allocations', array());
                    foreach ($allocations as $allocation) {
                        if ($allocation['amount'] > 0) {
                            $alloc = Model_Provider_Payment_Allocation::forge(array(
                                'payment_id' => $payment->id,
                                'invoice_id' => isset($allocation['invoice_id']) ? $allocation['invoice_id'] : null,
                                'order_id' => isset($allocation['order_id']) ? $allocation['order_id'] : null,
                                'amount_allocated' => $allocation['amount'],
                            ));
                            $alloc->save();
                        }
                    }
                    
                    // Si el pago está completado, generar póliza contable
                    if ($payment->status == 'completed') {
                        $this->_generate_accounting_entry($payment);
                    }
                    
                    // Log
                    Model_Provider_Log::log_action(
                        $payment->provider_id,
                        'create_payment',
                        'payment',
                        $payment->id,
                        'Pago creado: ' . $payment->payment_number,
                        null,
                        $payment->to_array()
                    );
                    
                    DB::commit_transaction();
                    
                    Session::set_flash('success', 'Pago registrado correctamente.');
                    Response::redirect('admin/proveedores/pagos/view/' . $payment->id);
                    
                } catch (Exception $e) {
                    DB::rollback_transaction();
                    Session::set_flash('error', 'Error al crear pago: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', $val->error());
            }
        }
        
        // Obtener proveedores
        $providers = DB::select('id', 'company_name', 'tax_id')
            ->from('providers')
            ->where('tenant_id', $tenant_id)
            ->where('deleted_at', null)
            ->where('is_active', 1)
            ->where('is_suspended', 0)
            ->order_by('company_name')
            ->execute()
            ->as_array();
        
        // Si viene de un proveedor específico
        if ($provider_id) {
            $provider = Model_Provider::find($provider_id);
            if ($provider) {
                // Obtener facturas/órdenes pendientes
                $pending_invoices = $this->_get_pending_invoices($provider_id);
                $data['pending_invoices'] = $pending_invoices;
                $data['provider'] = $provider;
            }
        }
        
        $data['providers'] = $providers;
        $data['payment_methods'] = array(
            'transferencia' => 'Transferencia Bancaria',
            'efectivo' => 'Efectivo',
            'cheque' => 'Cheque',
            'tarjeta' => 'Tarjeta',
            'otro' => 'Otro',
        );
        
        $this->template->title = 'Nuevo Pago a Proveedor';
        $this->template->content = View::forge('admin/proveedores/pagos/create', $data);
    }

    /**
     * Ver detalle de pago
     */
    public function action_view($id = null)
    {
        if (!$id) {
            Response::redirect('admin/proveedores/pagos');
        }
        
        $data = array();
        $payment = Model_Provider_Payment::find($id);
        
        if (!$payment) {
            Session::set_flash('error', 'Pago no encontrado.');
            Response::redirect('admin/proveedores/pagos');
        }
        
        // Obtener asignaciones
        $allocations = DB::select('ppa.*', 
                array('pb.code_order', 'invoice_number'),
                array('po.code_order', 'order_number'))
            ->from(array('provider_payment_allocations', 'ppa'))
            ->join(array('providers_bills', 'pb'), 'LEFT')
            ->on('ppa.invoice_id', '=', 'pb.id')
            ->join(array('providers_orders', 'po'), 'LEFT')
            ->on('ppa.order_id', '=', 'po.id')
            ->where('ppa.payment_id', $id)
            ->execute()
            ->as_array();
        
        $data['payment'] = $payment;
        $data['allocations'] = $allocations;
        
        $this->template->title = 'Detalle de Pago: ' . $payment->payment_number;
        $this->template->content = View::forge('admin/proveedores/pagos/view', $data);
    }

    /**
     * Completar pago (cambiar a status completed)
     */
    public function action_complete($id = null)
    {
        if (!$id || !Helper_Permission::can('proveedores', 'edit')) {
            Response::redirect('admin/proveedores/pagos');
        }
        
        $payment = Model_Provider_Payment::find($id);
        
        if (!$payment) {
            Session::set_flash('error', 'Pago no encontrado.');
            Response::redirect('admin/proveedores/pagos');
        }
        
        if ($payment->status == 'completed') {
            Session::set_flash('info', 'El pago ya está completado.');
            Response::redirect('admin/proveedores/pagos/view/' . $id);
        }
        
        try {
            DB::start_transaction();
            
            $old_status = $payment->status;
            $payment->status = 'completed';
            $payment->save();
            
            // Generar póliza contable
            $this->_generate_accounting_entry($payment);
            
            // Log
            Model_Provider_Log::log_action(
                $payment->provider_id,
                'complete_payment',
                'payment',
                $payment->id,
                'Pago completado: ' . $payment->payment_number,
                array('status' => $old_status),
                array('status' => 'completed')
            );
            
            DB::commit_transaction();
            
            Session::set_flash('success', 'Pago completado correctamente.');
            
        } catch (Exception $e) {
            DB::rollback_transaction();
            Session::set_flash('error', 'Error al completar pago: ' . $e->getMessage());
        }
        
        Response::redirect('admin/proveedores/pagos/view/' . $id);
    }

    /**
     * Cancelar pago
     */
    public function action_cancel($id = null)
    {
        if (!$id || !Helper_Permission::can('proveedores', 'delete')) {
            Response::redirect('admin/proveedores/pagos');
        }
        
        $payment = Model_Provider_Payment::find($id);
        
        if (!$payment) {
            Session::set_flash('error', 'Pago no encontrado.');
            Response::redirect('admin/proveedores/pagos');
        }
        
        if ($payment->status == 'cancelled') {
            Session::set_flash('info', 'El pago ya está cancelado.');
            Response::redirect('admin/proveedores/pagos/view/' . $id);
        }
        
        try {
            DB::start_transaction();
            
            $old_status = $payment->status;
            $payment->status = 'cancelled';
            $payment->save();
            
            // Si había póliza, cancelarla
            // TODO: Implementar cancelación de póliza
            
            // Log
            Model_Provider_Log::log_action(
                $payment->provider_id,
                'cancel_payment',
                'payment',
                $payment->id,
                'Pago cancelado: ' . $payment->payment_number,
                array('status' => $old_status),
                array('status' => 'cancelled')
            );
            
            DB::commit_transaction();
            
            Session::set_flash('success', 'Pago cancelado correctamente.');
            
        } catch (Exception $e) {
            DB::rollback_transaction();
            Session::set_flash('error', 'Error al cancelar pago: ' . $e->getMessage());
        }
        
        Response::redirect('admin/proveedores/pagos/view/' . $id);
    }

    /**
     * Generar póliza contable
     */
    private function _generate_accounting_entry($payment)
    {
        // TODO: Integrar con módulo de contabilidad
        // 
        // Asiento tipo:
        // Debe:  Proveedores (CxP)     $amount
        // Haber: Bancos                $amount
        
        $entry_data = array(
            'date' => $payment->payment_date,
            'type' => 'egreso',
            'reference' => $payment->payment_number,
            'description' => 'Pago a proveedor: ' . $payment->provider->company_name,
            'movements' => array(
                array(
                    'account_id' => Config::get('accounting.providers_payable_account'),
                    'type' => 'debit',
                    'amount' => $payment->amount,
                ),
                array(
                    'account_id' => Config::get('accounting.bank_account'),
                    'type' => 'credit',
                    'amount' => $payment->amount,
                ),
            ),
        );
        
        // Helper_Accounting::create_entry($entry_data);
        
        return true;
    }

    /**
     * Obtener facturas/órdenes pendientes de pago
     */
    private function _get_pending_invoices($provider_id)
    {
        return DB::select('pb.*', DB::expr('(pb.total - COALESCE(SUM(ppa.amount_allocated), 0)) as pending_amount'))
            ->from(array('providers_bills', 'pb'))
            ->join(array('provider_payment_allocations', 'ppa'), 'LEFT')
            ->on('pb.id', '=', 'ppa.invoice_id')
            ->where('pb.provider_id', $provider_id)
            ->where('pb.deleted', 0)
            ->having('pending_amount', '>', 0)
            ->group_by('pb.id')
            ->order_by('pb.date_bill')
            ->execute()
            ->as_array();
    }

    /**
     * Validación de pago
     */
    private function _validate_payment()
    {
        $val = Validation::forge();
        
        $val->add_field('provider_id', 'Proveedor', 'required|valid_string[numeric]');
        $val->add_field('payment_date', 'Fecha de Pago', 'required|valid_string[numeric]');
        $val->add_field('payment_method', 'Método de Pago', 'required');
        $val->add_field('amount', 'Monto', 'required|valid_string[numeric]');
        $val->add_field('currency', 'Moneda', 'required|exact_length[3]');
        
        return $val;
    }

    /**
     * Reporte de pagos
     */
    public function action_report()
    {
        if (!Helper_Permission::can('proveedores', 'view')) {
            Session::set_flash('error', 'No tienes permisos para ver reportes.');
            Response::redirect('admin/proveedores/pagos');
        }
        
        $data = array();
        $tenant_id = Session::get('tenant_id', 1);
        
        // Parámetros de reporte
        $date_from = Input::get('date_from', date('Y-m-01'));
        $date_to = Input::get('date_to', date('Y-m-d'));
        
        // Resumen de pagos
        $summary = DB::select(
                DB::expr('COUNT(*) as total_payments'),
                DB::expr('SUM(amount) as total_amount'),
                DB::expr('SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as completed_amount'),
                DB::expr('SUM(CASE WHEN status = "draft" THEN amount ELSE 0 END) as draft_amount')
            )
            ->from('provider_payments')
            ->where('tenant_id', $tenant_id)
            ->where('payment_date', '>=', $date_from)
            ->where('payment_date', '<=', $date_to)
            ->where('deleted_at', null)
            ->execute()
            ->current();
        
        // Pagos por proveedor
        $by_provider = DB::select('p.company_name', 
                DB::expr('COUNT(pp.id) as total_payments'),
                DB::expr('SUM(pp.amount) as total_amount'))
            ->from(array('provider_payments', 'pp'))
            ->join(array('providers', 'p'))
            ->on('pp.provider_id', '=', 'p.id')
            ->where('pp.tenant_id', $tenant_id)
            ->where('pp.payment_date', '>=', $date_from)
            ->where('pp.payment_date', '<=', $date_to)
            ->where('pp.deleted_at', null)
            ->group_by('pp.provider_id')
            ->order_by(DB::expr('SUM(pp.amount)'), 'desc')
            ->limit(10)
            ->execute()
            ->as_array();
        
        $data['summary'] = $summary;
        $data['by_provider'] = $by_provider;
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        
        $this->template->title = 'Reporte de Pagos a Proveedores';
        $this->template->content = View::forge('admin/proveedores/pagos/report', $data);
    }
}

