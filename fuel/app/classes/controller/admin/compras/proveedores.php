<?php

/**
 * CONTROLADOR ADMIN_COMPRAS_PROVEEDORES
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Compras_Proveedores extends Controller_Admin
{
	/**
	 * BEFORE
	 *
	 * @return Void
	 */
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
        parent::before();

		# SI EL USUARIO NO TIENE PERMISOS
		if(!Auth::member(100) && !Auth::member(50))
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No tienes los permisos para acceder a esta sección.');

			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin');
		}
	}


	public function action_index($search = '')
{
    // =======================================
    // INICIALIZACIÓN
    // =======================================
    $data = [];
    $providers_info = [];
    $per_page = 50;

    // =======================================
    // QUERY BASE DE PROVEEDORES
    // =======================================
    $query = Model_Provider::query()
        //->where('deleted', 0)
        ->order_by('updated_at', 'asc'); // 🔹 primero los que tuvieron actividad reciente

    // =======================================
    // FILTRO DE BÚSQUEDA
    // =======================================
    if (!empty($search)) {
        $search = str_replace('+', ' ', rawurldecode($search));
        $search_like = '%' . str_replace(' ', '%', $search) . '%';
        $query->where_open()
              ->where(DB::expr("CONCAT(`name`, ' ', `rfc`)"), 'like', $search_like)
              ->or_where('name', 'like', $search_like)
              ->or_where('rfc', 'like', $search_like)
              ->where_close();
    }

    // =======================================
    // PAGINACIÓN
    // =======================================
    $config = [
        'name'           => 'admin',
        'pagination_url' => Uri::current(),
        'total_items'    => $query->count(),
        'per_page'       => $per_page,
        'uri_segment'    => 'pagina',
        'show_first'     => true,
        'show_last'      => true,
    ];
    $pagination = Pagination::forge('providers', $config);

    // =======================================
    // OBTENER PROVEEDORES PAGINADOS
    // =======================================
    $providers = $query
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

    // =======================================
    // PROCESAR INFORMACIÓN POR PROVEEDOR
    // =======================================
    foreach ($providers as $prov) {
        // === ÓRDENES DE COMPRA ===
        $ordenes = Model_Providers_Order::query()
            ->where('provider_id', $prov->id)
            ->where('deleted', 0)
            ->get();
        $ordenes_count = count($ordenes);
        $ordenes_monto = array_sum(array_map(fn($oc) => (float) $oc->total, $ordenes));

        // === FACTURAS ===
        $facturas = Model_Providers_Bill::query()
            ->where('provider_id', $prov->id)
            ->where('deleted', 0)
            ->get();
        $total_facturado = array_sum(array_map(fn($f) => (float) $f->total, $facturas));
        $facturas_count  = count($facturas);

        // Facturas pagadas
        $facturas_pagadas = array_filter($facturas, fn($f) => $f->status == 5);
        $facturas_pagadas_count = count($facturas_pagadas);
        $total_pagado_facturas  = array_sum(array_map(fn($f) => (float) $f->total, $facturas_pagadas));

        // === CONTRARECIBOS ===
        $contrarecibos = Model_Providers_Receipt::query()
            ->where('provider_id', $prov->id)
            ->where('deleted', 0)
            ->get();
        $contrarecibos_count = count($contrarecibos);
        $contrarecibos_monto = array_sum(array_map(fn($cr) => (float) $cr->total, $contrarecibos));

        // === REPs ===
        $reps = Model_Providers_Bill_Rep::query()
            ->related('provider_bill')
            ->where('provider_bill.provider_id', $prov->id)
            ->where('deleted', 0)
            ->get();
        $reps_count = count($reps);
        $reps_monto = array_sum(array_map(fn($r) => (float) $r->amount_paid, $reps));

        // === NOTAS DE CRÉDITO ===
        $notas = Model_Providers_Creditnote::query()
            ->where('provider_id', $prov->id)
            ->where('deleted', 0)
            ->get();
        $notas_count = count($notas);
        $notas_monto = array_sum(array_map(fn($n) => (float) $n->total, $notas));

        // === SALDO GENERAL ===
        $saldo_pendiente = $total_facturado - ($contrarecibos_monto + $reps_monto + $notas_monto);

        // === ÚLTIMA ACTIVIDAD (más reciente entre órdenes, facturas o reps) ===
        $ultima_actividad = max(
            $prov->updated_at,
            $ordenes ? max(array_map(fn($o) => $o->updated_at, $ordenes)) : 0,
            $facturas ? max(array_map(fn($f) => $f->updated_at, $facturas)) : 0,
            $reps ? max(array_map(fn($r) => $r->updated_at, $reps)) : 0,
            $contrarecibos ? max(array_map(fn($c) => $c->updated_at, $contrarecibos)) : 0
        );

        // =======================================
        // AGREGAR A LISTA
        // =======================================
        $providers_info[] = [
            'id'                       => $prov->id,
            'name'                     => $prov->name,
            'rfc'                      => $prov->rfc,
            'ordenes_count'            => $ordenes_count,
            'ordenes_monto'            => $ordenes_monto,
            'facturas_count'           => $facturas_count,
            'total_facturado'          => $total_facturado,
            'facturas_pagadas_count'   => $facturas_pagadas_count,
            'total_pagado_facturas'    => $total_pagado_facturas,
            'contrarecibos_count'      => $contrarecibos_count,
            'contrarecibos_monto'      => $contrarecibos_monto,
            'reps_count'               => $reps_count,
            'reps_monto'               => $reps_monto,
            'notas_count'              => $notas_count,
            'notas_monto'              => $notas_monto,
            'saldo_pendiente_general'  => $saldo_pendiente,
            'ultima_actividad'         => $ultima_actividad ? date('d/m/Y H:i', $ultima_actividad) : '---',
        ];
    }

    // =======================================
    // ENVIAR DATOS A LA VISTA
    // =======================================
    $data['providers']  = $providers_info;
    $data['pagination'] = $pagination->render();
    $data['search']     = $search;

    // SELECT2 DE BÚSQUEDA
    $data['provider_opts'] = [];
    foreach (Model_Provider::query()->order_by('name', 'asc')->get() as $prov) {
        $data['provider_opts'][] = [
            'id'   => $prov->id,
            'text' => $prov->name . ' (' . $prov->rfc . ')',
        ];
    }

    // RENDER
    $this->template->title   = 'Reporte por Proveedor';
    $this->template->content = View::forge('admin/compras/proveedores/index', $data, false);
}



    /**
     * BUSCAR
     *
     * REDIRECCIONA A LA URL DE BUSCAR REGISTROS
     *
     * @access  public
     * @return  Void
     */
    public function action_buscar()
    {
        # SI SE UTILIZO EL METODO POST
        if(Input::method() == 'POST')
        {
            # SE OBTIENEN LOS VALORES
            $data = array(
                'search' => ($_POST['search'] != '') ? $_POST['search'] : '',
            );

            # SE CREA LA VALIDACION DE LOS CAMPOS
            $val = Validation::forge('search');
            $val->add_callable('Rules');
            $val->add_field('search', 'search', 'max_length[100]');

            # SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
            if($val->run($data))
            {
                # SE REMPLAZAN ALGUNOS CARACTERES
                $search = str_replace(' ', '+', $val->validated('search'));
                $search = str_replace('*', '', $search);

                # SE ALMACENA LA CADENA DE BUSQUEDA
                $search = ($val->validated('search') != '') ? $search : '';

                # SE REDIRECCIONA A BUSCAR
                Response::redirect('admin/compras/proveedores/index/'.$search);
            }
            else
            {
                # SE REDIRECCIONA AL USUARIO
                Response::redirect('admin/compras/proveedores');
            }
        }
        else
        {
            # SE REDIRECCIONA AL USUARIO
            Response::redirect('admin/compras/proveedores');
        }
    }

    

  public function action_info($provider_id = 0)
{
    if (!$provider_id || !is_numeric($provider_id)) {
        return Response::redirect('admin/compras/proveedores');
    }

    $provider = Model_Provider::find($provider_id);
    if (!$provider) {
        Session::set_flash('error', 'Proveedor no encontrado.');
        return Response::redirect('admin/compras/proveedores');
    }

    // ---------- ÓRDENES / FACTURAS ----------
    $ordenes = Model_Providers_Order::query()
        ->where('provider_id', $provider_id)
        ->where('deleted', 0)
        ->order_by('date_order', 'desc')
        ->get();

    $facturas = Model_Providers_Bill::query()
        ->where('provider_id', $provider_id)
        ->where('deleted', 0)
        ->order_by('created_at', 'desc')
        ->get();

    // Para filtrar REPs / Notas por proveedor
    $bill_ids = [];
    foreach ($facturas as $f) { $bill_ids[] = $f->id; }

    // ---------- CONTRARECIBOS ----------
    $contrarecibos = Model_Providers_Receipt::query()
        ->where('provider_id', $provider_id)
        ->where('deleted', 0)
        ->order_by('receipt_date', 'desc')
        ->get();
    $contrarecibos_count = count($contrarecibos);
    $contrarecibos_monto = 0.0;
    foreach ($contrarecibos as $c) { $contrarecibos_monto += (float)$c->total; }

    // Aliases para compatibilidad con la vista anterior
    $pagos_count = $contrarecibos_count;
    $pagos_monto = $contrarecibos_monto;

    // ---------- REPs ----------
    $reps = [];
    $reps_count = 0;
    $reps_monto = 0.0;
    if (!empty($bill_ids)) {
        $reps = Model_Providers_Bill_Rep::query()
            ->where('provider_bill_id', 'in', $bill_ids)
            ->where('deleted', 0)
            ->order_by('payment_date', 'desc')
            ->get();
        $reps_count = count($reps);
        foreach ($reps as $r) { $reps_monto += (float)$r->amount_paid; }
    }

    // ---------- Notas de crédito ----------
    $notas = [];
    $notas_count = 0;
    $notas_monto = 0.0;
    if (!empty($bill_ids)) {
        $notas = Model_Providers_Creditnote::query()
            ->where('invoice_id', 'in', $bill_ids)
            ->where('deleted', 0)
            ->order_by('created_at', 'desc')
            ->get();
        $notas_count = count($notas);
        foreach ($notas as $n) { $notas_monto += (float)$n->total; }
    }

    // ---------- Totales globales ----------
    $ordenes_count = count($ordenes);
    $ordenes_monto = 0.0;
    foreach ($ordenes as $oc) { $ordenes_monto += (float)$oc->total; }

    $facturas_count = count($facturas);
    $facturas_monto = 0.0;
    foreach ($facturas as $f) { $facturas_monto += (float)$f->total; }

    $saldo_pendiente = $facturas_monto - $contrarecibos_monto;

    // ---------- Timeline (con labels del Helper) ----------
    $timeline = [];

    foreach ($ordenes as $oc) {
        $timeline[] = [
            'fecha'        => !empty($oc->date_order) ? (is_numeric($oc->date_order) ? date('Y-m-d', $oc->date_order) : date('Y-m-d', strtotime($oc->date_order))) : '',
            'tipo'         => 'Orden de Compra',
            'ref'          => $oc->code_order,
            'monto'        => $oc->total,
            'doc_id'       => $oc->id,
            'ruta'         => 'orden',
            'status_label' => Helper_Purchases::label('order', $oc->status),
            'badge'        => Helper_Purchases::badge_class('order', $oc->status),
        ];
    }

    foreach ($facturas as $f) {
        $timeline[] = [
            'fecha'        => !empty($f->created_at) ? date('Y-m-d', is_numeric($f->created_at) ? $f->created_at : strtotime($f->created_at)) : '',
            'tipo'         => 'Factura',
            'ref'          => $f->uuid,
            'monto'        => $f->total,
            'doc_id'       => $f->id,
            'ruta'         => 'factura',
            'status_label' => Helper_Purchases::label('bill', $f->status),
            'badge'        => Helper_Purchases::badge_class('bill', $f->status),
        ];
    }

    foreach ($contrarecibos as $c) {
        $timeline[] = [
            'fecha'        => !empty($c->receipt_date) ? (is_numeric($c->receipt_date) ? date('Y-m-d', $c->receipt_date) : date('Y-m-d', strtotime($c->receipt_date))) : '',
            'tipo'         => 'Contrarecibo',
            'ref'          => $c->receipt_number,
            'monto'        => $c->total,
            'doc_id'       => $c->id,
            'ruta'         => 'contrarecibo',
            'status_label' => Helper_Purchases::label('receipt', $c->status),
            'badge'        => Helper_Purchases::badge_class('receipt', $c->status),
        ];
    }

    foreach ($reps as $r) {
        $timeline[] = [
            'fecha'        => !empty($r->payment_date) ? date('Y-m-d', strtotime($r->payment_date)) : '',
            'tipo'         => 'REP',
            'ref'          => $r->uuid,
            'monto'        => $r->amount_paid,
            'doc_id'       => $r->id,
            'ruta'         => 'rep',
            'status_label' => Helper_Purchases::label('rep', $r->status),
            'badge'        => Helper_Purchases::badge_class('rep', $r->status),
        ];
    }

    foreach ($notas as $n) {
        $timeline[] = [
            'fecha'        => !empty($n->created_at) ? date('Y-m-d', is_numeric($n->created_at) ? $n->created_at : strtotime($n->created_at)) : '',
            'tipo'         => 'Nota de Crédito',
            'ref'          => $n->uuid,
            'monto'        => $n->total,
            'doc_id'       => $n->id,
            'ruta'         => 'nota',
            'status_label' => Helper_Purchases::label('cn', $n->status),
            'badge'        => Helper_Purchases::badge_class('cn', $n->status),
        ];
    }

    usort($timeline, function($a,$b){
        return strcmp(($b['fecha'] ?? ''), ($a['fecha'] ?? ''));
    });

    $data = [
        'provider'               => $provider,
        'ordenes'                => $ordenes,
        'facturas'               => $facturas,
        'contrarecibos'          => $contrarecibos,
        'reps'                   => $reps,
        'notas'                  => $notas,

        'ordenes_count'          => $ordenes_count,
        'ordenes_monto'          => $ordenes_monto,
        'facturas_count'         => $facturas_count,
        'facturas_monto'         => $facturas_monto,
        'contrarecibos_count'    => $contrarecibos_count,
        'contrarecibos_monto'    => $contrarecibos_monto,
        'reps_count'             => $reps_count,
        'reps_monto'             => $reps_monto,
        'notas_count'            => $notas_count,
        'notas_monto'            => $notas_monto,
        'saldo_pendiente'        => $saldo_pendiente,

        // compatibilidad con variables antiguas de la vista
        'pagos_count'            => $pagos_count,
        'pagos_monto'            => $pagos_monto,

        'timeline'               => $timeline,
    ];

    $this->template->title   = 'Reporte del Proveedor';
    $this->template->content = View::forge('admin/compras/proveedores/info', $data, false);
}





}
