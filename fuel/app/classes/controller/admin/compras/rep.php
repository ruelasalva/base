<?php
/**
 * CONTROLLER: ADMIN/COMPRAS/REP
 * MANEJO DE REP (RECEIPT ELECTRONIC PAYMENT) PARA FACTURAS DE PROVEEDOR
 */

class Controller_Admin_Compras_Rep extends Controller_Admin
{
    /**
     * MUESTRA EL LISTADO DE REP REGISTRADOS
     */
    /**
 * INDEX
 *
 * MUESTRA EL LISTADO DE FACTURAS QUE REQUIEREN REP
 *
 * @access  public
 * @param   string  $search
 * @return  void
 */
/**
 * INDEX
 *
 * MUESTRA LAS FACTURAS QUE REQUIEREN REP (PAGO 99)
 */
public function action_index($search = '')
{
    // ===============================
    // VARIABLES BASE
    // ===============================
    $data   = [];
    $bills  = [];
    $per_page = 50;

    // ===============================
    // CONSULTA BASE
    // ===============================
    $query = Model_Providers_Bill::query()
        ->related('provider')
        ->where('deleted', 0)
        ->where('require_rep', 1);

    // ===============================
    // FILTRO DE BÚSQUEDA
    // ===============================
    if ($search != '') {
        $original_search = $search;
        $search = str_replace('+', ' ', rawurldecode($search));
        $search = str_replace(' ', '%', $search);

        $query->where_open()
            ->where('uuid', 'like', '%' . $search . '%')
            ->or_where('folio', 'like', '%' . $search . '%')
            ->or_where('provider.name', 'like', '%' . $search . '%')
            ->where_close();
    }

    // ===============================
    // PAGINACIÓN
    // ===============================
    $config = [
        'name'           => 'admin',
        'pagination_url' => Uri::current(),
        'total_items'    => $query->count(),
        'per_page'       => $per_page,
        'uri_segment'    => 'pagina',
        'show_first'     => true,
        'show_last'      => true,
    ];
    $pagination = Pagination::forge('rep', $config);

    // ===============================
    // EJECUTAR CONSULTA
    // ===============================
    $facturas = $query
        ->order_by('id', 'desc')
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

    // ===============================
    // PROCESAR FACTURAS
    // ===============================
    foreach ($facturas as $bill) {
        // --- REPs ASOCIADOS ---
        $reps = Model_Providers_Bill_Rep::query()
            ->where('provider_bill_id', $bill->id)
            ->where('deleted', 0)
            ->get();

        // Calcular total pagado en REPs
        $rep_total_pagado = 0;
        $rep_ids = [];
        $rep_badges = [];

        foreach ($reps as $rep) {
            $rep_total_pagado += (float) $rep->amount_paid;
            $rep_ids[] = $rep->id;
            $rep_badges[] = '<span class="badge badge-success">REP</span>';
        }

        // Verificar si el total de REP cubre la factura
        $rep_completo = $rep_total_pagado >= (float) $bill->total;

        // --- DATOS FORMATEADOS ---
        $bills[] = [
            'id'                => $bill->id,
            'uuid'              => $bill->uuid,
            'provider'          => $bill->provider ? $bill->provider->name : '',
            'created_at'        => $bill->created_at ? date('d/m/Y H:i', $bill->created_at) : '---',
            'total'             => '$' . number_format($bill->total, 2, '.', ','),
            'status'            => $bill->status == 1 ? 'PAGADA' : 'PENDIENTE',
            'badge_color'       => $bill->status == 1 ? 'success' : 'warning',
            'has_rep'           => count($reps) > 0,
            'rep_ids'           => $rep_ids,
            'rep_badges'        => implode(' ', $rep_badges),
            'rep_total_pagado'  => $rep_total_pagado,
            'rep_completo'      => $rep_completo,
        ];
    }

    // ===============================
    // PASAR DATOS A LA VISTA
    // ===============================
    $data['bills']      = $bills;
    $data['search']     = str_replace('%', ' ', $search);
    $data['pagination'] = $pagination->render();

    // ===============================
    // RENDERIZAR
    // ===============================
    $this->template->title   = 'Seguimiento REP';
    $this->template->content = View::forge('admin/compras/rep/index', $data, false);
}



/**
 * BUSCAR
 *
 * REDIRECCIONA A LA URL DE BÚSQUEDA DE REPS
 *
 * @access  public
 * @return  void
 */
public function action_buscar()
{
    if (Input::method() == 'POST') {
        $data = [
            'search' => (Input::post('search') != '') ? Input::post('search') : '',
        ];

        $val = Validation::forge('search');
        $val->add_callable('Rules');
        $val->add_field('search', 'search', 'max_length[100]');

        if ($val->run($data)) {
            $search = str_replace(' ', '+', $val->validated('search'));
            $search = str_replace('*', '', $search);
            $search = ($val->validated('search') != '') ? $search : '';

            Response::redirect('admin/compras/rep/index/' . $search);
        } else {
            Response::redirect('admin/compras/rep');
        }
    } else {
        Response::redirect('admin/compras/rep');
    }
}


    /**
 * ACCIÓN PARA AGREGAR UN REP (Recibo Electrónico de Pago)
 * - Valida XML
 * - Evita duplicados
 * - Relaciona facturas existentes
 * - Usa Helpers y estructura estándar de Compras
 */
public function action_agregar()
{
    // =========================================================
    // INICIALIZACIÓN
    // =========================================================
    $data = [];
    $errors = [];
    $rep_xml = null;
    $checked = [];
    $errores_facturas = [];
    $facturas_guardadas = 0;
    $bloquear_guardado = false;

    \Log::info('[REP][INICIO] Acción agregar iniciada por usuario=' . Auth::get('id'));

    // =========================================================
    // SI VIENE DESDE UNA FACTURA
    // =========================================================
    $bill_id = Input::get('bill_id', null);
    $bill = null;

    if ($bill_id) {
        $bill = Model_Providers_Bill::find($bill_id, ['related' => ['provider']]);
        if (!$bill) {
            Session::set_flash('error', 'Factura no encontrada.');
            return Response::redirect('admin/compras/rep');
        }

        $data['bill'] = $bill;

        // OTRAS FACTURAS PENDIENTES DEL MISMO PROVEEDOR
        $data['facturas_pendientes'] = Model_Providers_Bill::query()
            ->where('provider_id', $bill->provider_id)
            ->where('deleted', 0)
            ->where('status', '!=', 1)
            ->where('id', '!=', $bill->id)
            ->order_by('created_at', 'desc')
            ->get();
    }

    // =========================================================
    // PROCESAR ENVÍO POST
    // =========================================================
    if (Input::method() == 'POST') {
        $xml = Input::file('xml_file');
        $pdf = Input::file('pdf_file');
        $checked = Input::post('bills', []);

        // =========================================================
        // PROCESAR XML (Primera carga)
        // =========================================================
        if ($xml && $xml['error'] === UPLOAD_ERR_OK && !Input::post('guardar_rep')) {
            \Log::info('[REP][XML] Archivo recibido: ' . $xml['name']);

            // Validar que sea un REP
            if (!Helper_Invoicexml::is_rep($xml['tmp_name'])) {
                $errors[] = 'El archivo XML cargado <b>NO es un REP válido</b>.';
                $bloquear_guardado = true;
            } else {
                // Leer XML
                $rep_xml = Helper_Invoicexml::get_rep_data($xml['tmp_name']);
                $data['rep_xml'] = $rep_xml;
                $facturas_xml_uuids = array_column($rep_xml['facturas'] ?? [], 'uuid');

                // === Buscar facturas existentes ===
                $facturas_encontradas = [];
                $facturas_no_encontradas = [];

                foreach ($rep_xml['facturas'] as &$factura_xml) {
                    $factura_en_bd = Model_Providers_Bill::query()
                        ->related('provider')
                        ->where('uuid', $factura_xml['uuid'])
                        ->where('deleted', 0)
                        ->get_one();

                    if ($factura_en_bd) {
                        $factura_xml['existe'] = true;
                        $factura_xml['proveedor'] = $factura_en_bd->provider->name ?? '';
                        $factura_xml['monto_factura'] = $factura_en_bd->total;
                        $factura_xml['id'] = $factura_en_bd->id;

                        // Facturas que vienen desde index o coinciden se marcan
                        if ($bill && $factura_en_bd->id == $bill->id) {
                            $checked[] = $factura_en_bd->id;
                        }

                        $facturas_encontradas[] = $factura_xml;
                    } else {
                        $factura_xml['existe'] = false;
                        $factura_xml['proveedor'] = '---';
                        $factura_xml['monto_factura'] = 0;
                        $factura_xml['id'] = null;
                        $facturas_no_encontradas[] = $factura_xml;
                    }
                }
                unset($factura_xml);

                // Organiza para mostrar primero las encontradas
                $data['rep_xml']['facturas'] = array_merge($facturas_encontradas, $facturas_no_encontradas);

                // Si el REP no contiene la factura base
                if ($bill && !in_array($bill->uuid, $facturas_xml_uuids)) {
                    $errors[] = 'El REP cargado <b>NO incluye la factura seleccionada</b> (UUID: <code>' . $bill->uuid . '</code>).';
                    $bloquear_guardado = true;
                }

                if (empty($rep_xml['facturas'])) {
                    $errors[] = 'No se encontraron facturas relacionadas en el REP.';
                    $bloquear_guardado = true;
                }
            }
            $data['checked'] = $checked;
        }

        // =========================================================
        // GUARDAR REP (Cuando se hace submit final)
        // =========================================================
        if (Input::post('guardar_rep') == '1') {
            \Log::info('[REP][GUARDAR] Iniciando guardado de REP');

            $rep_xml_serialized = Input::post('rep_xml', null);
            $rep_xml = $rep_xml_serialized ? unserialize(base64_decode($rep_xml_serialized)) : null;

            if (!$rep_xml || empty($rep_xml['facturas'])) {
                $errors[] = 'No hay información del REP procesada.';
                $bloquear_guardado = true;
            } else {
                $facturas_xml_uuids = array_column($rep_xml['facturas'], 'uuid');

                foreach ($checked as $factura_id) {
                    $factura_en_bd = Model_Providers_Bill::find($factura_id);
                    if (!$factura_en_bd) continue;

                    // Solo guarda si el UUID está en el REP
                    if (!in_array($factura_en_bd->uuid, $facturas_xml_uuids)) {
                        $errores_facturas[] = $factura_en_bd->uuid;
                        continue;
                    }

                    // Evita duplicados
                    $ya_existe = Model_Providers_Bill_Rep::query()
                        ->where('provider_bill_id', $factura_id)
                        ->where('uuid', $rep_xml['uuid_rep'])
                        ->get_one();

                    if ($ya_existe) {
                        $errores_facturas[] = $factura_en_bd->uuid . ' (ya tenía REP)';
                        continue;
                    }

                    // Subir archivos
                    $xml_path = '';
                    if ($xml && $xml['error'] === UPLOAD_ERR_OK) {
                        $xml_path = 'uploads/reps/' . date('Ymd_His') . '_' . basename($xml['name']);
                        move_uploaded_file($xml['tmp_name'], DOCROOT . $xml_path);
                    }
                    $pdf_path = '';
                    if ($pdf && $pdf['error'] === UPLOAD_ERR_OK) {
                        $pdf_path = 'uploads/reps/' . date('Ymd_His') . '_' . basename($pdf['name']);
                        move_uploaded_file($pdf['tmp_name'], DOCROOT . $pdf_path);
                    }

                    // Crear registro REP
                    $rep = Model_Providers_Bill_Rep::forge([
                        'provider_bill_id' => $factura_id,
                        'uuid'             => $rep_xml['uuid_rep'],
                        'payment_date'     => $rep_xml['payment_date'],
                        'amount_paid'      => $rep_xml['rep_total'],
                        'xml_file'         => $xml_path,
                        'pdf_file'         => $pdf_path,
                        'status'           => Helper_Purchases::status('rep', 'nuevo'),
                        'uploaded_by'      => Auth::get('id'),
                        'deleted'          => 0,
                    ]);
                    $rep->save();

                    $facturas_guardadas++;
                    \Log::info("[REP][OK] REP registrado para factura UUID={$factura_en_bd->uuid}");
                }
            }

            // =========================================================
            // RESULTADOS
            // =========================================================
            if (!empty($errores_facturas)) {
                $errors[] = 'No se pudieron asociar las siguientes facturas:<br><code>' .
                            implode('</code>, <code>', $errores_facturas) . '</code>';
                $bloquear_guardado = true;
            } elseif ($facturas_guardadas == 0) {
                $errors[] = 'No se registró ningún REP. Verifica la selección.';
                $bloquear_guardado = true;
            } else {
                Session::set_flash('success', $facturas_guardadas . ' REP(s) registrados correctamente.');
                Helper_Notifications::log('rep', "REP generado correctamente.", Auth::get('id'));
                return Response::redirect('admin/compras/rep');
            }
        }
    }

    // =========================================================
    // DATOS FINALES A LA VISTA
    // =========================================================
    $data['checked'] = $checked;
    $data['rep_xml'] = $rep_xml;
    $data['bill'] = $bill ?? null;
    $data['errors'] = $errors;
    $data['bloquear_guardado'] = $bloquear_guardado;

    $this->template->title   = 'Agregar REP';
    $this->template->content = View::forge('admin/compras/rep/agregar', $data, false);
}







    /**
 * MUESTRA EL DETALLE DE UN REP (RECIBO ELECTRÓNICO DE PAGO)
 * URL: admin/compras/rep/info/{id}
 */
public function action_info($id = 0)
{
    // ========================
    // VALIDACIÓN Y OBTENCIÓN
    // ========================
    $id = (int)$id;
    if (!$id || !is_numeric($id)) {
        Session::set_flash('error', 'ID de REP no válido.');
        Response::redirect('admin/compras/rep');
    }

    // OBTIENE EL REP CON SUS RELACIONES
    $rep = Model_Providers_Bill_Rep::find($id, [
        'related' => [
            'provider_bill',
            'provider_bill.provider',
            'user'
        ]
    ]);

    if (!$rep) {
        Session::set_flash('error', 'REP no encontrado.');
        Response::redirect('admin/compras/rep');
    }

     $facturas_asociadas = Model_Providers_Bill_Rep::query()
    ->where('uuid', $rep->uuid)
    ->related('provider_bill')           // padre
    ->related('provider_bill.provider')  // hijo del padre
    ->get();

    // PREPARA FECHAS Y DATOS
    $created_at = $rep->created_at;
    if ($created_at && !is_numeric($created_at)) {
        $created_at = strtotime($created_at);
    }
    $updated_at = $rep->updated_at;
    if ($updated_at && !is_numeric($updated_at)) {
        $updated_at = strtotime($updated_at);
    }

    // DATOS PARA LA VISTA
    $data = [
        'rep'                => $rep,
        'facturas_asociadas' => $facturas_asociadas,
        'created_at'         => $created_at,
        'updated_at'         => $updated_at,
    ];


    $this->template->title = 'Detalle REP';
    $this->template->content = View::forge('admin/compras/rep/info', $data, false);
}



    /**
     * MUESTRA FORMULARIO PARA EDITAR UN REP
     */
    public function action_editar($id = 0)
{
    // ==============================
    // VALIDACIÓN Y OBTENCIÓN DEL REP
    // ==============================
    $id = (int)$id;
    if (!$id || !is_numeric($id)) {
        Session::set_flash('error', 'ID de REP no válido.');
        Response::redirect('admin/compras/rep');
    }

    $rep = Model_Providers_Bill_Rep::find($id, [
        'related' => [
            'provider_bill',
            'provider_bill.provider',
            'user'
        ]
    ]);

    if (!$rep) {
        Session::set_flash('error', 'REP no encontrado.');
        Response::redirect('admin/compras/rep');
    }

    $errors = [];
    $success = false;

    // ==============================
    // SI RECIBE POST (GUARDAR CAMBIOS)
    // ==============================
    if (Input::method() == 'POST') {
        // VALIDAR INPUTS
        $payment_date = Input::post('payment_date', '');
        $amount_paid = Input::post('amount_paid', '');
        $status = Input::post('status', 1);

        // ARCHIVOS (opcional, si suben reemplaza)
        $pdf_file = Input::file('pdf_file');
        $xml_file = Input::file('xml_file');

        if (empty($payment_date)) $errors[] = 'La fecha de pago es obligatoria.';
        if (empty($amount_paid) || !is_numeric($amount_paid)) $errors[] = 'El monto pagado es obligatorio y debe ser numérico.';

        if (empty($errors)) {
            $rep->payment_date = $payment_date;
            $rep->amount_paid  = $amount_paid;
            $rep->status       = $status;
            $rep->updated_at   = date('Y-m-d H:i:s');

            // PDF FILE (reemplaza si hay nuevo)
            if ($pdf_file && $pdf_file['error'] == UPLOAD_ERR_OK) {
                // Guarda nuevo archivo y reemplaza si existe
                $pdf_name = 'rep_' . $rep->id . '_' . time() . '.pdf';
                $pdf_path = DOCROOT . 'assets/rep_pdf/' . $pdf_name;
                move_uploaded_file($pdf_file['tmp_name'], $pdf_path);
                $rep->pdf_file = $pdf_name;
            }
            // XML FILE (si permites editarlo, poco común, igual que arriba)
            if ($xml_file && $xml_file['error'] == UPLOAD_ERR_OK) {
                $xml_name = 'rep_' . $rep->id . '_' . time() . '.xml';
                $xml_path = DOCROOT . 'assets/rep_xml/' . $xml_name;
                move_uploaded_file($xml_file['tmp_name'], $xml_path);
                $rep->xml_file = $xml_name;
            }

            $rep->save();
            \Log::info('REP actualizado', ['rep_id' => $rep->id, 'user' => Auth::get('id')]);
            Session::set_flash('success', 'REP actualizado correctamente.');
            Response::redirect('admin/compras/rep/info/' . $rep->id);
        }
    }

    // ==============================
    // ENVÍA DATOS A LA VISTA
    // ==============================
    $data = [
        'rep' => $rep,
        'provider' => $rep->provider_bill->provider ?? null,
        'bill' => $rep->provider_bill ?? null,
        'errors' => $errors,
    ];

    $this->template->title = 'Editar REP';
    $this->template->content = View::forge('admin/compras/rep/editar', $data, false);
}

}
