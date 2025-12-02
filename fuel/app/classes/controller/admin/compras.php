<?php

/**
 * CONTROLADOR ADMIN_COMPRAS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Compras extends Controller_Admin
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
		if(!Auth::member(100))
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No tienes los permisos para acceder a esta sección.');

			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin');
		}
	}


	public function action_index()
{
    // ======================
    // FILTRO MES / AÑO CON SESIÓN
    // ======================
    $mes  = Input::get('mes', Session::get('dashboard_mes', date('m')));
    $anio = Input::get('anio', Session::get('dashboard_anio', date('Y')));

    // Guardar selección en sesión
    Session::set('dashboard_mes', $mes);
    Session::set('dashboard_anio', $anio);

    $inicio = strtotime("{$anio}-{$mes}-01");
    $fin    = strtotime(date("Y-m-t", $inicio));

    // ======================
    // PREVENIR WARNINGS
    // ======================
    $ordenes_abiertas   = 0;
    $ultimos_rep_info   = [];
    $ultimos_cr_info    = [];
    $ultimas_notas_info = [];
    $meses = [
        '01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
        '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'
    ];

    // ======================
    // KPIs FILTRADOS
    // ======================
    $ordenes_sin_factura = Model_Providers_Order::query()
        ->where('deleted', 0)
        ->where('status', 1)
        ->where('created_at', '>=', $inicio)
        ->where('created_at', '<=', $fin)
        ->count();

    $ordenes_abiertas = Model_Providers_Order::query()
        ->where('deleted', 0)
        ->where('status', 1)
        ->where('created_at', '>=', $inicio)
        ->where('created_at', '<=', $fin)
        ->count();

    $ordenes_concluidas = Model_Providers_Order::query()
        ->where('deleted', 0)
        ->where('status', 'IN', [3, 4])
        ->where('created_at', '>=', $inicio)
        ->where('created_at', '<=', $fin)
        ->count();

    $facturas_sin_orden = Model_Providers_Bill::query()
        ->where('deleted', 0)
        ->where('order_id', null)
        ->where('created_at', '>=', $inicio)
        ->where('created_at', '<=', $fin)
        ->count();

    $facturas_proceso = Model_Providers_Bill::query()
        ->where('deleted', 0)
        ->where('status', 'IN', [1, 2])
        ->where('created_at', '>=', $inicio)
        ->where('created_at', '<=', $fin)
        ->count();

    $facturas_pagadas = Model_Providers_Bill::query()
        ->where('deleted', 0)
        ->where('status', 3)
        ->where('created_at', '>=', $inicio)
        ->where('created_at', '<=', $fin)
        ->count();

    $facturas_rechazadas = Model_Providers_Bill::query()
        ->where('deleted', 0)
        ->where('status', 4)
        ->where('created_at', '>=', $inicio)
        ->where('created_at', '<=', $fin)
        ->count();

    $pagos_programados = Model_Providers_Bill_Rep::query()
        ->where('deleted', 0)
        ->where('status', 'IN', [0, 10])
        ->where('created_at', '>=', $inicio)
        ->where('created_at', '<=', $fin)
        ->count();

    $pagos_realizados = Model_Providers_Bill_Rep::query()
        ->where('deleted', 0)
        ->where('status', 2)
        ->where('created_at', '>=', $inicio)
        ->where('created_at', '<=', $fin)
        ->count();

    $notas_credito = Model_Providers_Creditnote::query()
        ->where('deleted', 0)
        ->where('created_at', '>=', $inicio)
        ->where('created_at', '<=', $fin)
        ->count();

    // Proveedores activos (no tienen deleted)
    $proveedores_activos = Model_Provider::query()->count();

    // ======================
    // PORCENTAJES
    // ======================
    $porcentaje_ordenes = ($ordenes_concluidas + $ordenes_sin_factura) > 0
        ? round(($ordenes_concluidas / ($ordenes_concluidas + $ordenes_sin_factura)) * 100, 2)
        : 0;

    $porcentaje_facturas = ($facturas_pagadas + $facturas_proceso) > 0
        ? round(($facturas_pagadas / ($facturas_pagadas + $facturas_proceso)) * 100, 2)
        : 0;

    // ======================
    // TOP 5 PROVEEDORES DEL MES
    // ======================
    $top_proveedores = DB::select(
            'providers.name',
            [DB::expr('COUNT(providers_bills.id)'), 'facturas'],
            [DB::expr('SUM(providers_bills.total)'), 'total']
        )
        ->from('providers_bills')
        ->join('providers', 'LEFT')->on('providers.id', '=', 'providers_bills.provider_id')
        ->where('providers_bills.deleted', '=', 0)
        ->where('providers_bills.created_at', '>=', $inicio)
        ->where('providers_bills.created_at', '<=', $fin)
        ->group_by('providers.name')
        ->order_by(DB::expr('SUM(providers_bills.total)'), 'desc')
        ->limit(5)
        ->execute()
        ->as_array();

    // ======================
    // LISTAS RÁPIDAS
    // ======================
    $ultimas_ordenes = Model_Providers_Order::query()
        ->related('provider')
        ->where('deleted', 0)
        ->order_by('created_at', 'desc')
        ->limit(5)
        ->get();

    $ultimas_facturas = Model_Providers_Bill::query()
        ->related('provider')
        ->where('deleted', 0)
        ->order_by('created_at', 'desc')
        ->limit(5)
        ->get();

    $ultimos_pagos = Model_Providers_Bill_Rep::query()
        ->where('deleted', 0)
        ->order_by('created_at', 'desc')
        ->limit(5)
        ->get();

    $ultimos_contrarecibos = Model_Providers_Receipt::query()
        ->where('deleted', 0)
        ->order_by('created_at', 'desc')
        ->limit(5)
        ->get();

    $ultimas_notas = Model_Providers_Creditnote::query()
        ->where('deleted', 0)
        ->order_by('created_at', 'desc')
        ->limit(5)
        ->get();

    // ======================
    // PROCESAR INFO PARA VISTA
    // ======================
    foreach ($ultimos_pagos as $r) {
        $ultimos_rep_info[] = [
            'code'  => $r->uuid,
            'fecha' => $r->payment_date ? date('d/m/Y', strtotime($r->payment_date)) : '-',
        ];
    }

    foreach ($ultimos_contrarecibos as $cr) {
        $ultimos_cr_info[] = [
            'code'  => $cr->receipt_number ?? $cr->code_receipt ?? '---',
            'fecha' => $cr->receipt_date ? date('d/m/Y', strtotime($cr->receipt_date)) : date('d/m/Y', $cr->created_at),
        ];
    }

    foreach ($ultimas_notas as $n) {
        $ultimas_notas_info[] = [
            'code'  => $n->uuid,
            'fecha' => date('d/m/Y', $n->created_at),
        ];
    }

    // ======================
    // PASAR DATOS A LA VISTA
    // ======================
    $view = View::forge('admin/compras/index');

    // Filtros seleccionados y meses
    $view->set('mes', $mes, false);
    $view->set('anio', $anio, false);
    $view->set('meses', $meses, false);

    // KPIs
    $view->set('ordenes_sin_factura', (int)$ordenes_sin_factura, false);
    $view->set('facturas_sin_orden', (int)$facturas_sin_orden, false);
    $view->set('ordenes_abiertas', (int)$ordenes_abiertas, false);
    $view->set('facturas_proceso', (int)$facturas_proceso, false);
    $view->set('facturas_rechazadas', (int)$facturas_rechazadas, false);
    $view->set('notas_credito', (int)$notas_credito, false);
    $view->set('pagos_programados', (int)$pagos_programados, false);
    $view->set('proveedores_activos', (int)$proveedores_activos, false);

    // Concluidos
    $view->set('ordenes_concluidas', (int)$ordenes_concluidas, false);
    $view->set('facturas_pagadas', (int)$facturas_pagadas, false);
    $view->set('pagos_realizados', (int)$pagos_realizados, false);
    $view->set('porcentaje_ordenes', (float)$porcentaje_ordenes, false);
    $view->set('porcentaje_facturas', (float)$porcentaje_facturas, false);

    // Listas rápidas
    $view->set('ultimas_ordenes', $ultimas_ordenes, false);
    $view->set('ultimas_facturas', $ultimas_facturas, false);
    $view->set('ultimos_pagos', $ultimos_rep_info, false);
    $view->set('ultimos_contrarecibos', $ultimos_cr_info, false);
    $view->set('ultimos_rep', $ultimos_rep_info, false);
    $view->set('ultimas_notas', $ultimas_notas_info, false);
    $view->set('top_proveedores', $top_proveedores, false);

    // Charts JSON
    $dashboard_data = [
        'ordenes_sin_factura' => $ordenes_sin_factura,
        'facturas_sin_orden'  => $facturas_sin_orden,
        'ordenes_abiertas'    => $ordenes_abiertas,
        'facturas_proceso'    => $facturas_proceso,
        'facturas_rechazadas' => $facturas_rechazadas,
        'notas_credito'       => $notas_credito,
        'pagos_programados'   => $pagos_programados,
        'proveedores_activos' => $proveedores_activos,
    ];

    $view->set_global('dashboard_data_json', json_encode($dashboard_data));

    $this->template->title   = 'Dashboard de Compras';
    $this->template->content = $view;
}




    /**
     * Convierte resultados ORM a array simple
     */
    private function to_array($items)
    {
        $out = [];
        foreach ($items as $item) {
            $out[] = $item->to_array();
        }
        return $out;
    }	


}
