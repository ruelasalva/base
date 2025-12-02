<?php
/**
 * CONTROLADOR DE CONTRARECIBOS (RECEIPTS)
 * Permite crear, listar, autorizar y consultar contrarecibos para pagos a proveedores.
 * Estructura tradicional: index, buscar, agregar, info, autorizar, eliminar.
 */
class Controller_Admin_Compras_Contrarecibos extends Controller_Admin
{
    /**
     * INDEX
     * Lista todos los contrarecibos, permite filtrar por estatus, proveedor, fechas.
     */
     public function action_index() // Manteniendo el nombre action_index si es tu método principal
    {
        // Obtener parámetros de búsqueda
        $search = Input::get('search', '');
        $estatus = Input::get('estatus', '');
        $fecha = Input::get('fecha', ''); // Formato YYYY-MM-DD

        // Construir la consulta base para los contrarecibos
        $query = Model_Providers_Receipt::query()
            ->related('provider') // Cargar el proveedor directamente
            ->related('details') // Cargar los detalles del contrarecibo
            ->related('details.bill') // Cargar las facturas a través de los detalles
            ->related('details.bill.order') // Cargar las órdenes a través de las facturas
            ->order_by('receipt_date', 'desc'); // Ordenar por fecha de recepción

        // Aplicar filtros de búsqueda
        if (!empty($search)) {
            $matching_receipt_ids = [];

            // 1. Buscar por número de contrarecibo (directamente en Model_Providers_Receipt)
            $receipt_number_matches = Model_Providers_Receipt::query()
                ->where('receipt_number', 'LIKE', '%' . $search . '%')
                ->get();
            foreach ($receipt_number_matches as $r) {
                $matching_receipt_ids[] = $r->id;
            }

            // 2. Buscar por nombre de proveedor (a través de Model_Provider)
            $provider_matches = Model_Provider::query()
                ->where('name', 'LIKE', '%' . $search . '%')
                ->get();
            foreach ($provider_matches as $p) {
                $receipts_by_provider = Model_Providers_Receipt::query()
                    ->where('provider_id', $p->id)
                    ->get();
                foreach ($receipts_by_provider as $r) {
                    $matching_receipt_ids[] = $r->id;
                }
            }

            // 3. Buscar por UUID de factura (a través de Model_Providers_Receipts_Details y Model_Providers_Bill)
            $bill_uuid_matches = Model_Providers_Receipts_Details::query()
                ->related('bill')
                ->where('bill.uuid', 'LIKE', '%' . $search . '%')
                ->get();
            foreach ($bill_uuid_matches as $detail) {
                $matching_receipt_ids[] = $detail->receipt_id;
            }

            // 4. Buscar por código de OC (a través de Model_Providers_Receipts_Details, Model_Providers_Bill y Model_Providers_Order)
            $order_code_matches = Model_Providers_Receipts_Details::query()
                ->related('bill')
                ->related('bill.order')
                ->where('bill.order.code_order', 'LIKE', '%' . $search . '%')
                ->get();
            foreach ($order_code_matches as $detail) {
                $matching_receipt_ids[] = $detail->receipt_id;
            }

            // Filtrar la consulta principal por los IDs de recibo encontrados
            $matching_receipt_ids = array_unique($matching_receipt_ids);

            if (!empty($matching_receipt_ids)) {
                $query->where('id', 'IN', $matching_receipt_ids);
            } else {
                // Si no se encontró ninguna coincidencia, asegurar que la consulta principal no devuelva resultados
                $query->where('id', 0); 
            }
        }

        // Aplicar filtros de estatus y fecha
        if (!empty($estatus)) {
            $query->where('status', $estatus);
        }

        if (!empty($fecha)) {
            // Convertir la fecha de YYYY-MM-DD a timestamp para la búsqueda
            $start_of_day = strtotime($fecha . ' 00:00:00');
            $end_of_day = strtotime($fecha . ' 23:59:59');
            $query->where('receipt_date', '>=', $start_of_day)
                ->where('receipt_date', '<=', $end_of_day);
        }

        // Configuración de paginación
        $config = array(
            'pagination_url' => Uri::current(),
            'total_items'    => 0, // Se actualizará después
            'per_page'       => 20, // Cantidad de elementos por página
            'uri_segment'    => 'page',
            'num_links'      => 5,
            'show_first'     => true,
            'show_last'      => true,
            'first_mark'     => '&laquo; Primero',
            'last_mark'      => 'Último &raquo;',
            'prev_mark'      => '&lsaquo; Anterior',
            'next_mark'      => 'Siguiente &rsaquo;',
            'name'           => 'pagination_contrarecibos', // Nombre único para la paginación
        );

        // Obtener el total de elementos para la paginación
        $config['total_items'] = $query->count();

        // Crear la instancia de paginación
        $pagination = Pagination::forge($config['name'], $config);

        // Obtener los contrarecibos para la página actual
        $contrarecibos = $query->limit($pagination->per_page)
                        ->offset($pagination->offset)
                            ->get();

        // Generar los badges HTML para cada estatus
         // 1. Crear un arreglo vacío para guardar los badges
        $badges_html = [];

        // 2. Recorrer cada contrarecibo obtenido de la base de datos
        foreach ($contrarecibos as $cr) {
            // Para cada contrarecibo, generar su HTML y guardarlo en el arreglo
            // usando el ID del contrarecibo como llave.
            $badges_html[$cr->id] = Helper_Purchases::render_status('receipt', $cr->status);
        }


        // Preparar los datos para la vista
        $data = array(
            'contrarecibos' => $contrarecibos,
            'pagination'    => $pagination->render(),
            'search'        => $search,
            'estatus'       => $estatus,
            'fecha'         => $fecha,
            'badges'        => $badges_html,
        );

    
        $status_opts = Helper_Purchases::options('receipt');


        // Cargar la vista con los datos
    $this->template->title = 'Contrarecibos';
    $this->template->content = View::forge('admin/compras/contrarecibos/index', $data , false);
    }

    /**
     * AGREGAR CONTRARECIBO
     * Muestra facturas listas para pago (con OC, proveedor, sin contrarecibo), permite seleccionar y generar.
     */
    public function action_agregar()
{
    // =========================
    // INICIALIZACIÓN DE VARIABLES
    // =========================
    $data = [];
    $data['proveedores_opts'] = Model_Provider::get_for_input(); // Debe retornar array id=>nombre (custom)
    $data['facturas_opts'] = []; // Se llena al elegir proveedor vía AJAX o puede estar precargado
    $data['ordenes_opts'] = [];  // Igual que facturas
    $data['dias_credito'] = '';
    $data['fecha_pago'] = '';
    $data['estatus'] = 'pendiente';
    $data['notas'] = '';
    $data['alerta'] = '';

    // =========================
    // SI LLEGA FORMULARIO POR POST
    // =========================
    if (Input::method() == 'POST') {
        // VALIDAR CAMPOS PRINCIPALES
        $val = Validation::forge();
        $val->add_callable('Rules');
        $val->add_field('proveedor_id', 'Proveedor', 'required|valid_string[numeric]');
        $val->add_field('factura_id', 'Factura', 'required|valid_string[numeric]');
        $val->add_field('orden_id', 'Orden de Compra', 'required|valid_string[numeric]');
        $val->add_field('fecha_recepcion', 'Fecha de Recepción', 'required|valid_date[Y-m-d]');
        $val->add_field('dias_credito', 'Días de Crédito', 'required|valid_string[numeric]');
        $val->add_field('fecha_pago', 'Fecha de Pago', 'required|valid_date[Y-m-d]');
        $val->add_field('estatus', 'Estatus', 'required');
        $val->add_field('notas', 'Notas', 'max_length[500]');

        if ($val->run()) {
            try {
                // =======================================
                // GUARDAR NUEVO CONTRARECIBO
                // =======================================
                $contrarecibo = new Model_Providers_Receipt([
                    'provider_id'      => (int) Input::post('proveedor_id'),
                    'factura_id'       => (int) Input::post('factura_id'),
                    'orden_id'         => (int) Input::post('orden_id'),
                    'fecha_recepcion'  => strtotime(Input::post('fecha_recepcion')),
                    'dias_credito'     => (int) Input::post('dias_credito'),
                    'fecha_pago'       => strtotime(Input::post('fecha_pago')),
                    'estatus'          => Input::post('estatus'),
                    'notas'            => Input::post('notas'),
                    'created_at'       => time(),
                    'updated_at'       => time(),
                ]);

                // LOG DE INTENTO DE GUARDADO
                \Log::info('INTENTANDO GUARDAR CONTRARECIBO: ' . json_encode($contrarecibo->to_array()));

                if ($contrarecibo->save()) {
                    \Log::info('CONTRARECIBO GUARDADO CORRECTAMENTE ID: ' . $contrarecibo->id);
                    Session::set_flash('success', 'Contrarecibo creado correctamente.');
                    Response::redirect('admin/compras/contrarecibos/info/' . $contrarecibo->id);
                } else {
                    \Log::error('ERROR AL GUARDAR CONTRARECIBO');
                    Session::set_flash('error', 'No se pudo guardar el contrarecibo.');
                }

            } catch (\Exception $e) {
                \Log::error('ERROR EXCEPCIÓN AL GUARDAR CONTRARECIBO: ' . $e->getMessage());
                Session::set_flash('error', 'Error inesperado: ' . $e->getMessage());
            }
        } else {
            // VALIDACIÓN FALLIDA: DEVUELVE ERRORES
            $data['errores'] = $val->error();
            Session::set_flash('error', 'Verifica los datos y corrige los errores marcados.');
        }

        // RECARGA VALORES PARA REPINTAR EN CASO DE ERROR
        $data['proveedor_id']    = Input::post('proveedor_id');
        $data['factura_id']      = Input::post('factura_id');
        $data['orden_id']        = Input::post('orden_id');
        $data['fecha_recepcion'] = Input::post('fecha_recepcion');
        $data['dias_credito']    = Input::post('dias_credito');
        $data['fecha_pago']      = Input::post('fecha_pago');
        $data['estatus']         = Input::post('estatus');
        $data['notas']           = Input::post('notas');
    }

    // =========================
    // LLENADO DINÁMICO DE OPCIONES (GET o al entrar por primera vez)
    // =========================
    // OPCIONES DE FACTURAS Y ORDENES POR PROVEEDOR (SI YA FUE SELECCIONADO)
    if (!empty($data['proveedor_id'])) {
        $data['facturas_opts'] = Model_Providers_Bill::get_for_provider($data['proveedor_id']);
        $data['ordenes_opts'] = Model_Providers_Order::get_for_provider($data['proveedor_id']);

        // INTENTA TOMAR LOS DÍAS DE CRÉDITO DEL PROVEEDOR
        $proveedor = Model_Provider::find($data['proveedor_id']);
        $data['dias_credito'] = $proveedor && $proveedor->dias_credito ? $proveedor->dias_credito : '';
    }

    // CÁLCULO AUTOMÁTICO DE FECHA DE PAGO
    if (!empty($data['fecha_recepcion']) && !empty($data['dias_credito'])) {
        $fecha_pago = Helper_Receipts::calcula_fecha_pago($data['fecha_recepcion'], $data['dias_credito']);
        $data['fecha_pago'] = $fecha_pago ?: '';
    }

    // =========================
    // CARGA LA VISTA
    // =========================
    $this->template->title = 'Nuevo Contrarecibo';
    $this->template->content = View::forge('admin/compras/contrarecibos/agregar', $data);
}


   public function action_info($id = 0)
{
    // ===========================================
    // VALIDAR ID
    // ===========================================
    if (!$id || !is_numeric($id)) {
        Session::set_flash('error', 'ID de contrarecibo no válido.');
        return Response::redirect('admin/compras/contrarecibos');
    }

    // ===========================================
    // CARGAR CONTRARECIBO Y RELACIONES COMPLETAS
    // ===========================================
    $receipt = Model_Providers_Receipt::query()
        ->where('id', $id)
        ->related('provider')
        ->related('details', [
            'related' => [
                'bill' => [
                    'related' => ['order'] // carga "bill" y su "order"
                ]
            ]
        ])
        ->get_one();

    if (!$receipt) {
        Session::set_flash('error', 'Contrarecibo no encontrado.');
        return Response::redirect('admin/compras/contrarecibos');
    }

    // ===========================================
    // CARGAR PRODUCTOS (DETALLES DE LAS FACTURAS)
    // ===========================================
    $productos = [];
    if (!empty($receipt->details)) {
        foreach ($receipt->details as $d) {
            if (!empty($d->bill)) {
                $bill_details = Model_Providers_Bill_Detail::query()
                    ->where('bill_id', $d->bill->id)
                    ->get();

                foreach ($bill_details as $bd) {
                    // Mantener referencia a la factura para mostrar UUID
                    $bd->bill = $d->bill;
                    $productos[] = $bd;
                }
            }
        }
    }

    // ===========================================
    // DATOS BASE DEL CONTRARECIBO
    // ===========================================
    $data = [
        'contrarecibo'       => $receipt,
        'receipt_number'     => $receipt->receipt_number ?: $receipt->id,
        'provider'           => $receipt->provider ? $receipt->provider->name : '---',
        'rfc'                => $receipt->provider ? $receipt->provider->rfc : '---',
        'total'              => number_format($receipt->total ?? 0, 2),
        'status_label'       => Helper_Purchases::label('receipt', $receipt->status),
        'badge_color'        => Helper_Purchases::badge_class('receipt', $receipt->status),
        'notes'              => $receipt->notes ?: '---',
        'receipt_date'       => $receipt->receipt_date ? date('d/m/Y', $receipt->receipt_date) : '---',
        'payment_date'       => $receipt->payment_date ? date('d/m/Y', $receipt->payment_date) : '---',
        'payment_date_real'  => $receipt->payment_date_actual ? date('d/m/Y', $receipt->payment_date_actual) : '---',
        'created_at'         => $receipt->created_at ? date('d/m/Y H:i', $receipt->created_at) : '---',
        'updated_at'         => $receipt->updated_at ? date('d/m/Y H:i', $receipt->updated_at) : '---',
    ];

    // ===========================================
    // FACTURAS RELACIONADAS
    // ===========================================
    $data['facturas'] = [];

    if (!empty($receipt->details)) {
        foreach ($receipt->details as $d) {
            if (!empty($d->bill)) {
                $b = $d->bill;

                // --- DESERIALIZAR FECHA DESDE INVOICE_DATA ---
                $fecha_factura = '---';
                if (!empty($b->invoice_data)) {
                    // Primero intentar unserialize()
                    $tmp = @unserialize($b->invoice_data);
                    if ($tmp !== false && isset($tmp['fecha'])) {
                        $fecha_factura = date('d/m/Y', strtotime($tmp['fecha']));
                    } else {
                        // Intentar JSON
                        $json = json_decode($b->invoice_data, true);
                        if (json_last_error() === JSON_ERROR_NONE && isset($json['fecha'])) {
                            $fecha_factura = date('d/m/Y', strtotime($json['fecha']));
                        }
                    }
                }

                $data['facturas'][] = [
                    'uuid'           => $b->uuid ?? '---',
                    'order_code'     => !empty($b->order) ? $b->order->code_order : 'Sin OC',
                    'fecha_factura'  => $fecha_factura,
                    'fecha_carga'    => $b->created_at ? date('d/m/Y', $b->created_at) : '---',
                    'status'         => Helper_Purchases::label('bill', $b->status),
                    'badge'          => Helper_Purchases::badge_class('bill', $b->status),
                    'total'          => number_format($b->total ?? 0, 2),
                ];
            }
        }
    }

    // ===========================================
    // PRODUCTOS
    // ===========================================
    $data['productos'] = [];
    if (!empty($productos)) {
        foreach ($productos as $p) {
            $data['productos'][] = [
                'descripcion' => $p->description,
                'cantidad'    => $p->quantity,
                'precio'      => number_format($p->unit_price, 2),
                'subtotal'    => number_format($p->subtotal, 2),
            ];
        }
    }

    // ===========================================
    // RENDERIZAR VISTA
    // ===========================================
    $this->template->title   = 'Detalle Contrarecibo #' . $data['receipt_number'];
    $this->template->content = View::forge('admin/compras/contrarecibos/info', $data, false);
}




    

    /**
     * EDITAR CONTRARECIBO
     * Muestra el formulario para editar un contrarecibo existente.
     *
     * @access public
     * @param int $id ID del contrarecibo a editar.
     * @return View
     */
    public function action_editar($id = 0)
    {
        // 1. Validar ID y cargar contrarecibo
        if (!$id || !is_numeric($id)) {
            Session::set_flash('error', 'ID de contrarecibo no válido.');
            Response::redirect('admin/compras/contrarecibos');
        }

        $receipt = Model_Providers_Receipt::query()
            ->where('id', $id)
            ->related('provider')
            ->related('details')
            ->related('details.bill')
            ->related('details.bill.order') // Para mostrar detalles completos de las facturas
            ->get_one();

        if (!$receipt) {
            Session::set_flash('error', 'Contrarecibo no encontrado.');
            Response::redirect('admin/compras/contrarecibos');
        }

        // Si el contrarecibo ya está pagado o cancelado, no permitir edición de estado
        if ($receipt->status == 2 || $receipt->status == 3) {
            Session::set_flash('info', 'Este contrarecibo ya ha sido pagado o cancelado y no puede ser editado.');
            Response::redirect('admin/compras/contrarecibos/info/' . $id);
        }

        // Preparar datos para la vista del formulario
        $data = [
            'contrarecibo' => $receipt,
            'status_options' => [
                1 => 'En revisión',
                2 => 'Pagado',
                3 => 'Cancelado',
            ],
            // Puedes añadir más datos si son necesarios para el formulario
        ];

        $this->template->title = 'Editar Contrarecibo #' . $receipt->receipt_number;
        $this->template->content = View::forge('admin/compras/contrarecibos/editar', $data);
    }

    /**
     * POST_EDITAR CONTRARECIBO
     * Procesa el formulario de edición para actualizar un contrarecibo.
     *
     * @access public
     * @param int $id ID del contrarecibo a actualizar.
     * @return Response
     */
    public function post_editar($id = 0)
    {
        // 1. Validar ID y cargar contrarecibo
        if (!$id || !is_numeric($id)) {
            Session::set_flash('error', 'ID de contrarecibo no válido.');
            Response::redirect('admin/compras/contrarecibos');
        }

        $receipt = Model_Providers_Receipt::find($id);

        if (!$receipt) {
            Session::set_flash('error', 'Contrarecibo no encontrado.');
            Response::redirect('admin/compras/contrarecibos');
        }

        // Pre-procesar la fecha de pago para asegurar el formato YYYY-MM-DD
        $payment_date_input_raw = Input::post('payment_date');
        $payment_date_processed = null;

        if (!empty($payment_date_input_raw)) {
            // Intentar parsear como DD/MM/YYYY
            $dt_obj = DateTime::createFromFormat('d/m/Y', $payment_date_input_raw);
            if ($dt_obj && $dt_obj->format('d/m/Y') === $payment_date_input_raw) {
                $payment_date_processed = $dt_obj->format('Y-m-d');
            } else {
                // Si no es DD/MM/YYYY, intentar parsear como YYYY-MM-DD
                $dt_obj_ymd = DateTime::createFromFormat('Y-m-d', $payment_date_input_raw);
                if ($dt_obj_ymd && $dt_obj_ymd->format('Y-m-d') === $payment_date_input_raw) {
                    $payment_date_processed = $payment_date_input_raw;
                }
            }
        }
        // Sobrescribir el valor POST con el formato procesado para la validación
        Input::post('payment_date', $payment_date_processed);


        // 2. Validar los datos del formulario
        $val = Validation::forge();
        $val->add_callable('Rules');
        $val->add_field('status', 'Estatus', 'required|numeric_min[1]|numeric_max[3]'); // 1: En revisión, 2: Pagado, 3: Cancelado
        $val->add_field('notes', 'Notas', 'max_length[500]');

        // Validación condicional para payment_date
        $new_status_raw = Input::post('status');
        if ($new_status_raw == 2) { // Si el estado es "Pagado"
            $val->add_field('payment_date', 'Fecha de Pago', 'required|valid_date[Y-m-d]');
        } else {
            // Si no es "Pagado", es opcional y debe ser válido si se proporciona
            $val->add_field('payment_date', 'Fecha de Pago', 'valid_date[Y-m-d,true]'); // true permite valores vacíos
        }

        if ($val->run()) {
            try {
                $new_status = (int) Input::post('status');
                $old_status = $receipt->status;

                // Actualizar propiedades del contrarecibo
                $receipt->status = $new_status;
                $receipt->updated_at = time();
                $receipt->notes = Input::post('notes'); // Asegúrate de que tu modelo tenga este campo si lo usas

                // Lógica específica si el estado cambia a "Pagado" (2)
                if ($new_status == 2 && $old_status != 2) {
                    // Usar el valor ya procesado de Input::post('payment_date')
                    $receipt->payment_date_actual = !empty(Input::post('payment_date')) ? strtotime(Input::post('payment_date')) : time(); 
                } elseif ($new_status != 2) {
                    // Si el estado no es "Pagado", la fecha de pago real se limpia o se mantiene la original si no se cambia
                    $receipt->payment_date_actual = null; // Limpiar payment_date_actual si no es pagado
                }

                // Guardar los cambios en el contrarecibo
                if (!$receipt->save()) {
                    throw new Exception('No se pudo actualizar el contrarecibo.');
                }

                // 3. Actualizar el estado de las facturas y órdenes de compra asociadas
                // Solo si el contrarecibo se marca como Pagado (2) o Cancelado (3)
                if ($new_status == 2 || $new_status == 3) {
                    // Cargar los detalles del contrarecibo para obtener las facturas
                    $receipt_details = Model_Providers_Receipts_Details::query()
                        ->where('receipt_id', $receipt->id)
                        ->get();

                    foreach ($receipt_details as $detail) {
                        $bill = Model_Providers_Bill::find($detail->bill_id);
                        if ($bill) {
                            $bill->status = $new_status; // Actualizar status de la factura
                            if (!$bill->save()) {
                                \Log::error("Error al actualizar factura {$bill->id} al cambiar estado de contrarecibo {$receipt->id}.");
                            }

                            // Si la factura está asociada a una orden de compra, actualizar también su estado
                            if ($bill->order_id) {
                                $order = Model_Providers_Order::find($bill->order_id);
                                if ($order) {
                                    $order->status = $new_status; // Actualizar status de la orden de compra
                                    if (!$order->save()) {
                                        \Log::error("Error al actualizar orden de compra {$order->id} al cambiar estado de factura {$bill->id}.");
                                    }
                                }
                            }
                        }
                    }
                }

                Session::set_flash('success', 'Contrarecibo actualizado exitosamente.');
                Response::redirect('admin/compras/contrarecibos/info/' . $id);

            } catch (Exception $e) {
                \Log::error("Error al actualizar contrarecibo {$id}: " . $e->getMessage());
                Session::set_flash('error', 'Error al actualizar el contrarecibo: ' . $e->getMessage());
                Response::redirect('admin/compras/contrarecibos/editar/' . $id);
            }
        } else {
            // Validación fallida
            Session::set_flash('error', 'Errores de validación: ' . implode(', ', $val->error())); 
            // Recargar la vista de edición con los errores
            $data = [
                'contrarecibo' => $receipt,
                'status_options' => [
                    0 => 'Subida',
                    1 => 'Pendiente',
                    2 => 'Pagada',
                    3 => 'Autorizada',
                    4 => 'Rechazada',
                    5 => 'revision',
                    99 => 'Cancelada',
                ],
                'errores' => $val->error(), 
            ];
            $this->template->title = 'Editar Contrarecibo #' . $receipt->receipt_number;
            $this->template->content = View::forge('admin/compras/contrarecibos/editar', $data);
        }
    }  


    /** * CREAR CONTRARECIBO DESDE ORDEN
 * Flujo: orden con facturas validadas → contrarecibo.
 */
/**
 * CREAR CONTRARECIBO
 *
 * GENERA UN CONTRARECIBO DESDE UNA ORDEN DE COMPRA
 * USA HELPER_PURCHASES Y HELPER_PAYMENTS PARA MANTENER COHERENCIA ENTRE DOCUMENTOS
 *
 * @access  public
 * @param   int $order_id
 * @return  void
 */
public function action_crear($order_id = null)
{
    # ==========================================================
    # INICIALIZACIÓN DE VARIABLES
    # ==========================================================
    $data  = array();
    $error = false;

    \Log::info("[CONTRARECIBO][INICIO] ORDEN={$order_id}");

    # ==========================================================
    # VALIDACIÓN DE ID DE ORDEN
    # ==========================================================
    if (!$order_id || !is_numeric($order_id)) {
        Session::set_flash('error', 'ID de orden inválido.');
        \Log::error("[CONTRARECIBO] ID inválido recibido.");
        return Response::redirect('admin/compras/ordenes');
    }

    # ==========================================================
    # CONSULTA DE LA ORDEN
    # ==========================================================
    $orden = Model_Providers_Order::find($order_id, ['related' => ['provider','bills']]);
    if (!$orden) {
        Session::set_flash('error', 'Orden no encontrada.');
        \Log::error("[CONTRARECIBO] Orden no encontrada ID={$order_id}");
        return Response::redirect('admin/compras/ordenes');
    }

    # ==========================================================
    # VALIDAR SI LA ORDEN PUEDE GENERAR CONTRARECIBO
    # ==========================================================
    if (!Helper_Purchases::label('order', $orden->status)) {
        Session::set_flash('error','Estatus de orden desconocido o inválido.');
        return Response::redirect('admin/compras/ordenes/info/'.$orden->id);
    }

    # ==========================================================
    # FACTURAS VÁLIDAS (STATUS = AUTORIZADA)
    # ==========================================================
    $facturas_validas = array_filter($orden->bills, fn($b) => (int)$b->status === 3);
    if (empty($facturas_validas)) {
        Session::set_flash('error','No hay facturas autorizadas para generar el contrarecibo.');
        \Log::warning("[CONTRARECIBO] Sin facturas válidas para OC={$orden->id}");
        return Response::redirect('admin/compras/ordenes/info/'.$orden->id);
    }

    # ==========================================================
    # CÁLCULO DE TOTALES
    # ==========================================================
    $total_facturas = array_sum(array_map(fn($b) => (float)$b->total, $facturas_validas));
    $total_orden    = (float)$orden->total;

    \Log::info("[CONTRARECIBO] Total facturas={$total_facturas} | Total orden={$total_orden}");

    # ==========================================================
    # VALIDACIÓN DE MONTOS
    # ==========================================================
    if ($total_facturas > $total_orden) {
        $msg = sprintf(
            "No se puede generar el contrarecibo: total de facturas ($%.2f) excede total de la orden ($%.2f).",
            $total_facturas, $total_orden
        );
        Session::set_flash('error', $msg);
        \Log::error("[CONTRARECIBO] EXCEDE | OC={$orden->id} | FACTURAS={$total_facturas}");
        return Response::redirect('admin/compras/ordenes/info/'.$orden->id);
    }

    $usar_total = ($total_facturas < $total_orden) ? $total_facturas : $total_orden;
    if ($total_facturas < $total_orden) {
        \Log::warning("[CONTRARECIBO] Monto parcial OC={$orden->total} FACTURAS={$total_facturas}");
        Session::set_flash('warning',
            "Contrarecibo parcial: Orden #{$orden->code_order} por $".number_format($total_orden,2).
            ", facturado $".number_format($total_facturas,2)."."
        );
    }

    # ==========================================================
    # PROCESAMIENTO PRINCIPAL
    # ==========================================================
    DB::start_transaction();
    try {
        # === CALCULAR FECHA DE PAGO ===
        $fecha_pago = Helper_Payments::next_payment_date($orden, $orden->provider);
        \Log::info("[CONTRARECIBO] Fecha de pago=".date('Y-m-d',$fecha_pago));

        # === CREAR CONTRARECIBO ===
        $recibo = Model_Providers_Receipt::forge([
            'provider_id'  => $orden->provider_id,
            'order_id'     => $orden->id,
            'subtotal'     => $usar_total,
            'iva'          => 0,
            'retencion'    => 0,
            'total'        => $usar_total,
            'status'       => 0, // pendiente
            'deleted'      => 0,
            'payment_date' => $fecha_pago,
            'created_at'   => time(),
            'updated_at'   => time(),
        ]);
        $recibo->save();

        \Log::info("[CONTRARECIBO] Recibo #{$recibo->id} creado TOTAL={$usar_total}");

        # === ASOCIAR FACTURAS AL CONTRARECIBO ===
        foreach ($facturas_validas as $bill) {
            Model_Providers_Receipts_Details::forge([
                'receipt_id' => $recibo->id,
                'bill_id'    => $bill->id,
                'created_at' => time(),
            ])->save();
        }

        # === SINCRONIZAR ESTATUS (USA LAS REGLAS DEL HELPER) ===
        Helper_Purchases::sync_status('receipt', $recibo->id, 1); // 1 = autorizado

        DB::commit_transaction();

        Session::set_flash('success', "Contrarecibo #{$recibo->id} generado correctamente por $".number_format($usar_total,2).".");
        \Log::info("[CONTRARECIBO][OK] Recibo={$recibo->id} generado desde OC={$orden->id}");

        return Response::redirect('admin/compras/contrarecibos/info/'.$recibo->id);

    } catch (\Throwable $e) {
        DB::rollback_transaction();
        \Log::error("[CONTRARECIBO][ERROR] ".$e->getMessage());
        Session::set_flash('error','Error al generar contrarecibo: '.$e->getMessage());
        return Response::redirect('admin/compras/ordenes/info/'.$orden->id);
    }
}




/**
 * CREAR CONTRARECIBO DESDE FACTURA
 * 
 * Flujo: factura validada pero sin OC → crear OC automática (origin=3) + contrarecibo.
 */
public function action_crear_desde_factura($bill_id = null)
{
    if (!$bill_id || !is_numeric($bill_id)) {
        Session::set_flash('error','ID de factura inválido.');
        return Response::redirect('admin/compras/facturas');
    }

    try {
        \Log::debug("[CONTRARECIBO][FACTURA] INICIO - Factura ID: {$bill_id}");

        // === 1. Cargar factura ===
        $factura = Model_Providers_Bill::find($bill_id, ['related' => ['provider']]);
        if (!$factura) {
            \Log::error("[CONTRARECIBO][FACTURA] Factura no encontrada: {$bill_id}");
            Session::set_flash('error','Factura no encontrada.');
            return Response::redirect('admin/compras/facturas');
        }
        \Log::debug("[CONTRARECIBO][FACTURA] Factura cargada - UUID: {$factura->uuid}, Total: {$factura->total}");

        // === 2. Validar status ===
        if ((int)$factura->status !== 2) {
            \Log::warning("[CONTRARECIBO][FACTURA] Factura no está validada (status={$factura->status})");
            Session::set_flash('error','La factura debe estar validada antes de crear un contrarecibo.');
            return Response::redirect('admin/compras/facturas/info/'.$bill_id);
        }

        // === 3. Crear OC automática si no existe ===
        $order_id = $factura->order_id;
        if (empty($order_id)) {
            \Log::debug("[CONTRARECIBO][FACTURA] Factura sin OC → creando OC automática.");

        $orden = Model_Providers_Order::forge([
                'provider_id' => $factura->provider_id,
                'code_order'  => 'AUTO-' . time(),
                'date_order'  => $factura->created_at ?? time(), // usar fecha factura o actual
                'total'       => $factura->total,
                'currency_id' => $factura->currency_id ?? 1, // ⚠️ pon un default válido si no existe
                'tax_id'      => $factura->tax_id ?? 1,      // ⚠️ igual aquí
                'status'      => 2, // cerrada
                'origin'      => 3, // generado automáticamente
                'deleted'     => 0,
                'has_invoice'     => 0,
                'notes'     => 'creadeo automáticamente desde factura ID '.$factura->id,
                'created_at'  => time(),
                'updated_at'  => time(),
            ]);
            $orden->save();
            $order_id = $orden->id;

            // actualizar factura con la nueva orden
            $factura->order_id = $order_id;
            $factura->save();

            \Log::info("[CONTRARECIBO][FACTURA] OC automática creada - ID: {$order_id}");
        } else {
            \Log::debug("[CONTRARECIBO][FACTURA] Factura ya vinculada a OC existente ID={$order_id}");
        }

        // === 4. Crear contrarecibo ===
        DB::start_transaction();
        $recibo = Model_Providers_Receipt::forge([
            'order_id'    => $order_id,
            'provider_id' => $factura->provider_id,
            'subtotal'    => $factura->total, // si tienes desglose, cámbialo
            'iva'         => 0,
            'retencion'   => 0,
            'total'       => $factura->total,
            'fecha_pago'  => $factura->payment_date ?: time(),
            'status'      => 0, // pendiente
            'deleted'     => 0,
            'created_at'  => time(),
            'updated_at'  => time(),
        ]);
        $recibo->save();

        \Log::info("[CONTRARECIBO][FACTURA] Contrarecibo creado - ID: {$recibo->id}");

        // === 5. Relacionar detalle ===
        $detail = Model_Providers_Receipts_Details::forge([
            'receipt_id' => $recibo->id,
            'bill_id'    => $factura->id,
            'created_at' => time(),
        ]);
        $detail->save();

        // actualizar factura → en pago
        $factura->status = 4;
        $factura->save();
        \Log::debug("[CONTRARECIBO][FACTURA] Factura marcada como 'en pago' (ID={$factura->id})");

        DB::commit_transaction();

        Session::set_flash('success','Contrarecibo #'.$recibo->id.' generado correctamente.');
        return Response::redirect('admin/compras/contrarecibos/info/'.$recibo->id);

    } catch (\Throwable $e) {
        DB::rollback_transaction();
        \Log::error("[CONTRARECIBO][FACTURA][ERROR] ".$e->getMessage()." TRACE: ".$e->getTraceAsString());
        Session::set_flash('error','Error al generar contrarecibo.');
        return Response::redirect('admin/compras/facturas/info/'.$bill_id);
    }
}





}
