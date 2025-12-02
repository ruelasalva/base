<?php
/**
 * CONTROLADOR DE ÓRDENES DE COMPRA PARA ADMIN
 * GESTIONA ALTAS, EDICIÓN Y CONSULTA DE OCs
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Compras_Facturas extends Controller_Admin
{
    /**
     * BEFORE
     */
    public function before()
    {
        parent::before();

        # SOLO ADMINISTRADORES Y COMPRADORES AUTORIZADOS
        if (!Auth::member(100) && !Auth::member(50)) {
            Session::set_flash('error', 'No tienes permisos para acceder a este módulo.');
            Response::redirect('admin');
        }
    }


/**
 * SUBE MULTIPLES ARCHIVOS XML, CREA PROVEEDOR Y OC BORRADOR SI ES NECESARIO, Y ASOCIA LA FACTURA
 * SOLO PARA ADMIN, FLUJO CENTRALIZADO Y DOCUMENTADO PARA AUDITORÍA
 */
public function action_subir_multiple() {
    $this->template->title   = 'SUBIR MÚLTIPLES FACTURAS XML';
    $this->template->content = View::forge('admin/compras/facturas/subir_multiple');
}

    /**
 * ==========================================================
 * SUBIR FACTURA (ADMIN)
 * ==========================================================
 * 
 * PERMITE AL ADMINISTRADOR SUBIR UNA FACTURA A NOMBRE DE UN PROVEEDOR.
 * PUEDE ESTAR LIGADA O NO A UNA ORDEN DE COMPRA (OC).
 * 
 * FLUJO:
 * ------
 * 1. CARGA ARCHIVOS PDF + XML.
 * 2. VALIDA RFC, UUID Y DUPLICADOS.
 * 3. VERIFICA RELACIÓN CON ORDEN DE COMPRA (SI EXISTE).
 * 4. GUARDA ARCHIVOS EN SERVIDOR.
 * 5. CREA REGISTRO EN TABLA providers_bills.
 * 6. SINCRONIZA ESTATUS CON Helper_Purchases::sync_status().
 * 
 * IMPORTANTE:
 * - SI NO EXISTE ORDEN, SE GUARDA order_id = NULL (NO 0)
 * - STATUS INICIAL: 10 = SUBIDA (ADMIN)
 * - LOGS REGISTRAN TODO EL FLUJO PARA DEPURACIÓN
 * ==========================================================
 */
public function action_subir_factura($oc_id = null)
{
    // ==========================================================
    // INICIALIZACIÓN DE VARIABLES
    // ==========================================================
    $data    = [];
    $classes = [];
    $fields  = ['provider_id', 'pdf', 'xml', 'purchase'];

    foreach ($fields as $field)
    {
        $classes[$field] = ['form-group' => null, 'form-control' => null];
    }

    // ==========================================================
    // CARGAR CATÁLOGO DE PROVEEDORES (SELECT EN VISTA)
    // ==========================================================
    $data['providers'] = Model_Provider::query()
        ->order_by('name', 'asc')
        ->get();

    // ==========================================================
    // VALIDAR SI LLEGA UNA ORDEN DE COMPRA POR PARÁMETRO
    // ==========================================================
    $order = null;
    if ($oc_id)
    {
        $order = Model_Providers_Order::find($oc_id);
        if (!$order)
        {
            Session::set_flash('error', 'Orden de compra no encontrada.');
            Response::redirect('admin/compras/ordenes');
        }

        $data['order']        = $order;
        $data['purchase']     = $order->id;
        $data['order_total']  = $order->total;
        $data['provider_id']  = $order->provider_id;
    }

    // ==========================================================
    // PROCESAR FORMULARIO POST
    // ==========================================================
    if (\Input::method() == 'POST')
    {
        try
        {
            // --------------------------------------------------
            // VALIDAR PROVEEDOR
            // --------------------------------------------------
            $provider_id = Input::post('provider_id');
            if (empty($provider_id)) throw new Exception('Debes seleccionar un proveedor.');

            $provider = Model_Provider::find($provider_id);
            if (!$provider) throw new Exception('Proveedor inválido o no existente.');

            // --------------------------------------------------
            // VALIDAR ARCHIVOS PDF Y XML
            // --------------------------------------------------
            $pdf = $_FILES['pdf'] ?? null;
            $xml = $_FILES['xml'] ?? null;

            if (!$pdf || !$xml) throw new Exception('Debes subir ambos archivos: PDF y XML.');

            if (strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION)) != 'pdf')
                throw new Exception('El archivo PDF no tiene una extensión válida.');

            if (strtolower(pathinfo($xml['name'], PATHINFO_EXTENSION)) != 'xml')
                throw new Exception('El archivo XML no tiene una extensión válida.');

            // --------------------------------------------------
            // PROCESAR XML Y EXTRAER DATOS
            // --------------------------------------------------
            $xml_content  = file_get_contents($xml['tmp_name']);
            $invoice_data = Helper_Invoicexml::extract_invoice_data_from_xml($xml_content);
            if (!$invoice_data) throw new Exception('El XML no contiene datos válidos.');

            $uuid = $invoice_data['uuid'] ?? null;
            if (!$uuid) throw new Exception('El XML no contiene un UUID válido.');

            // --------------------------------------------------
            // VALIDAR RFC DEL RECEPTOR (EMPRESA)
            // --------------------------------------------------
            $config = Model_Config::query()->get_one();
            if (!$config) throw new Exception('No hay configuración de empresa registrada.');

            if (strcasecmp(trim($config->rfc), trim($invoice_data['receptor_rfc'])) !== 0)
                throw new Exception('El RFC del receptor en el XML no coincide con el RFC configurado de la empresa.');

            
            // ==========================================================
            // VALIDAR REQUERIMIENTO DE REP SEGÚN MÉTODO DE PAGO
            // ==========================================================
            $require_rep = 0; // Por defecto no requiere REP
            $metodo_pago = strtoupper(trim($invoice_data['metodo_pago'] ?? ''));

            if ($metodo_pago === 'PPD')
            {
                $require_rep = 1;
                \Log::info("[ADMIN][FACTURA] Factura UUID={$uuid} requiere REP (MétodoPago=PPD)");
            }
            else
            {
                \Log::info("[ADMIN][FACTURA] Factura UUID={$uuid} con MétodoPago={$metodo_pago}, no requiere REP.");
            }

            // --------------------------------------------------
            // VALIDAR DUPLICADOS (UUID + PROVEEDOR)
            // --------------------------------------------------
            $existing = Model_Providers_Bill::query()
                ->where('uuid', $uuid)
                ->where('provider_id', $provider->id)
                ->get_one();

            if ($existing) throw new Exception("Ya existe una factura registrada con este UUID: {$uuid}");

            // --------------------------------------------------
            // SERIALIZAR DATOS Y VALIDAR MONTOS
            // --------------------------------------------------
            $invoice_serialized = serialize($invoice_data);
            $total = (float) ($invoice_data['total'] ?? 0);

            // ==========================================================
            // VALIDAR ORDEN DE COMPRA
            // ==========================================================
            $purchase = $order ? $order->id : Input::post('purchase', null);

            if ($order && $total > $order->total)
                throw new Exception('El total de la factura no puede ser mayor al total de la orden de compra.');

            if (!$order)
            {
                // NO EXISTE ORDEN RELACIONADA → SE GUARDA NULL
                $purchase = null;
            }

            // ==========================================================
            // GUARDAR ARCHIVOS EN SERVIDOR
            // ==========================================================
            $upload_path = DOCROOT . 'assets/facturas/proveedores/' . $provider->id;
            if (!is_dir($upload_path)) mkdir($upload_path, 0755, true);

            $pdf_name = uniqid('factura_') . '.pdf';
            $xml_name = uniqid('factura_') . '.xml';

            move_uploaded_file($pdf['tmp_name'], $upload_path . '/' . $pdf_name);
            move_uploaded_file($xml['tmp_name'], $upload_path . '/' . $xml_name);

            // ==========================================================
            // GUARDAR FACTURA EN BASE DE DATOS
            // ==========================================================
            $bill = Model_Providers_Bill::forge([
                'provider_id'  => $provider->id,
                'pdf'          => $pdf_name,
                'xml'          => $xml_name,
                'uuid'         => $uuid,
                'total'        => $total,
                'require_rep'  => $require_rep,
                'order_id'     => $purchase,   // NULL si no hay orden
                'payment_date' => null,
                'deleted'      => 0,
                'invoice_data' => $invoice_serialized,
                'status'       => 10,          // 10 = Subida (Admin)
                'created_at'   => time(),
                'updated_at'   => time()
            ]);
            $bill->save();

            // ==========================================================
            // LOG Y SINCRONIZACIÓN DE ESTATUS
            // ==========================================================
            \Log::info("[ADMIN][FACTURA] Factura UUID={$uuid} registrada para proveedor={$provider->id}, total={$total}");

            if ($purchase)
            {
                // SINCRONIZA ESTATUS FACTURA → ORDEN
                Helper_Purchases::sync_status('bill', $bill->id, $bill->status);
            }

            // ==========================================================
            // RESPUESTA Y REDIRECCIÓN
            // ==========================================================
            Session::set_flash('success', 'Factura registrada correctamente.');

            if ($order)
                Response::redirect('admin/compras/ordenes/info/' . $order->id);
            else
                Response::redirect('admin/compras/facturas');

        }
        catch (\Exception $e)
        {
            // ==========================================================
            // CAPTURA DE ERRORES Y LOG DE DEPURACIÓN
            // ==========================================================
            \Log::error('[ADMIN][FACTURA] ' . $e->getMessage());
            Session::set_flash('error', $e->getMessage());
        }
    }

    // ==========================================================
    // CARGA DE VISTA FINAL
    // ==========================================================
    $data['classes'] = $classes;
    $this->template->title   = 'Subir Factura';
    $this->template->content = View::forge('admin/compras/facturas/subir_factura', $data, false);
}



    
    /**
 * ==========================================================
 * INDEX
 * ==========================================================
 *
 * MUESTRA EL LISTADO DE FACTURAS DE PROVEEDORES
 * PERMITE FILTRAR POR UUID O NOMBRE DE PROVEEDOR
 * Y VISUALIZA EL ESTATUS CON COLORES Y ÍCONOS
 *
 * @access  public
 * @return  void
 * ==========================================================
 */
public function action_index($search = '')
{
    // ==========================================================
    // INICIALIZACIÓN DE VARIABLES
    // ==========================================================
    $data          = [];
    $facturas_info = [];
    $per_page      = 100;

    // ==========================================================
    // QUERY BASE (FACTURAS + PROVEEDOR)
    // ==========================================================
    $query = Model_Providers_Bill::query()
        ->related('provider')
        ->order_by('created_at', 'desc');

    // ==========================================================
    // FILTRO DE BÚSQUEDA
    // ==========================================================
    if ($search != '')
    {
        // GUARDAR BÚSQUEDA ORIGINAL PARA MOSTRAR EN CAMPO
        $original_search = $search;

        // LIMPIAR Y NORMALIZAR CADENA
        $search = str_replace('+', ' ', rawurldecode($search));
        $search = trim($search);

        // BUSCAR POR UUID O NOMBRE DE PROVEEDOR
        $query->where_open()
            ->where('uuid', 'like', "%{$search}%")
            ->or_where('provider.name', 'like', "%{$search}%")
        ->where_close();

        \Log::debug("[ADMIN][FACTURAS] Filtro de búsqueda aplicado: {$search}");
    }

    // ==========================================================
    // CONFIGURACIÓN DE PAGINACIÓN
    // ==========================================================
    $config = [
        'name'           => 'compras_facturas',
        'pagination_url' => Uri::current(),
        'total_items'    => $query->count(),
        'per_page'       => $per_page,
        'uri_segment'    => 'pagina',
        'show_first'     => true,
        'show_last'      => true,
    ];

    $pagination = Pagination::forge('compras_facturas', $config);

    // ==========================================================
    // EJECUCIÓN DE CONSULTA CON LÍMITES
    // ==========================================================
    $facturas = $query
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

    // ==========================================================
    // CONSTRUCCIÓN DE ARREGLO DE RESULTADOS
    // ==========================================================
    if (!empty($facturas))
    {
        foreach ($facturas as $factura)
        {
            $provider_name = $factura->provider->name ?? 'Desconocido';

            // STATUS USANDO HELPER
            $status_label = Helper_Purchases::label('bill', $factura->status);
            $status_badge = Helper_Purchases::render_status('bill', $factura->status);

            // FORMATO DE FECHAS
            $created_at = !empty($factura->created_at)
                ? date('d/m/Y H:i', $factura->created_at)
                : '';
            $payment_date = !empty($factura->payment_date)
                ? date('d/m/Y', $factura->payment_date)
                : '-';

            // DATOS FORMATEADOS
            $facturas_info[] = [
                'id'           => $factura->id,
                'uuid'         => $factura->uuid,
                'provider'     => $provider_name,
                'total'        => number_format($factura->total, 2),
                'status_label' => $status_label,
                'status_badge' => $status_badge,
                'require_rep'  => $factura->require_rep ? '<span class="badge badge-warning"><i class="fas fa-file-invoice"></i> Requiere REP</span>' : '',
                'created_at'   => $created_at,
                'payment_date' => $payment_date,
            ];
        }
    }

    // ==========================================================
    // DATOS PARA LA VISTA
    // ==========================================================
    $data['facturas']   = $facturas_info;
    $data['search']     = $search;
    $data['pagination'] = $pagination->render();

    // ==========================================================
    // RENDER DE VISTA
    // ==========================================================
    $this->template->title   = 'Gestión de Compras | Facturas de Proveedores';
    $this->template->content = View::forge('admin/compras/facturas/index', $data, false);
}




	/**
 * ==========================================================
 * BUSCAR
 * ==========================================================
 *
 * REDIRECCIONA A LA URL DE RESULTADOS DE BÚSQUEDA
 * PERMITE BUSCAR FACTURAS POR UUID O NOMBRE DE PROVEEDOR
 *
 * @access  public
 * @return  void
 * ==========================================================
 */
public function action_buscar()
{
    // ==========================================================
    // VALIDAR SI SE UTILIZA MÉTODO POST
    // ==========================================================
    if (Input::method() == 'POST')
    {
        // SE OBTIENEN LOS DATOS
        $search = trim(Input::post('search', ''));

        // SE CREA VALIDACIÓN DE CAMPOS
        $val = Validation::forge('search');
        $val->add_callable('Rules');
        $val->add_field('search', 'búsqueda', 'max_length[100]');

        if ($val->run(['search' => $search]))
        {
            // LIMPIAR Y FORMATEAR LA CADENA
            $search = str_replace('*', '', $search);
            $search = str_replace(' ', '+', $search);

            // LOG DE DEPURACIÓN
            \Log::debug("[ADMIN][FACTURAS] Redirección de búsqueda: {$search}");

            // REDIRECCIONAR A INDEX CON PARÁMETRO
            Response::redirect('admin/compras/facturas/index/' . $search);
        }
        else
        {
            // LOG DE ERROR DE VALIDACIÓN
            \Log::debug("[ADMIN][FACTURAS] Error de validación en búsqueda.");

            // REDIRECCIONA AL LISTADO GENERAL
            Response::redirect('admin/compras/facturas');
        }
    }
    else
    {
        // MÉTODO INVÁLIDO → REDIRECCIÓN SEGURA
        \Log::debug("[ADMIN][FACTURAS] Acceso GET en action_buscar, redirigiendo.");
        Response::redirect('admin/compras/facturas');
    }
}


	
	/**
 * INFO
 *
 * MUESTRA LA INFORMACIÓN DE UNA FACTURA DE PROVEEDOR (ADMIN)
 *
 * @access  public
 * @return  Void
 */
public function action_info($factura_id = 0)
{
    # VERIFICAR SI EL USUARIO ESTÁ AUTENTICADO Y ES ADMINISTRADOR
    if (!Auth::check() || Auth::get('group') < 50) {
        Session::set_flash('error', 'No tienes permisos para acceder a esta sección.');
        Response::redirect('admin/compras/facturas');
    }

    # SE INICIALIZAN VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('status','message', 'payment_date');
    $errors  = array();

    foreach ($fields as $field) {
        $classes[$field] = array('form-group' => null, 'form-control' => null);
    }

    # OBTENER LA FACTURA Y SU PROVEEDOR
    $factura = Model_Providers_Bill::query()
        ->where('id', $factura_id)
        ->related('provider')
        ->get_one();

    # VALIDAR SI EXISTE
    if (!$factura) {
        Session::set_flash('error', 'La factura no existe o fue eliminada.');
        Response::redirect('admin/compras/facturas');
    }

    # OBTENER DATOS DEL PROVEEDOR
    $provider_name = isset($factura->provider) ? $factura->provider->name : 'Desconocido';

    # DESERIALIZAR DATOS DEL XML
    $invoice_data = !empty($factura->invoice_data) ? unserialize($factura->invoice_data) : [];

    # SI SE ENVÍA FORMULARIO PARA ACTUALIZAR EL ESTADO
    if (Input::method() == 'POST') {
        try {
            $new_status   = Input::post('status');
            $message      = Input::post('message', '');
            $payment_date = Input::post('payment_date', null);

            # VALIDAR ESTADO
            if (!is_numeric($new_status)) {
                $errors['status'] = 'Selecciona un estado válido.';
                $classes['status']['form-group']  = 'has-danger';
                $classes['status']['form-control'] = 'is-invalid';
            }

            # VALIDAR FECHA
            if (!empty($payment_date) && !strtotime($payment_date)) {
                $errors['payment_date'] = 'El formato de fecha es inválido.';
                $classes['payment_date']['form-group']  = 'has-danger';
                $classes['payment_date']['form-control'] = 'is-invalid';
            }

            if (!empty($errors)) {
                $data['errors']  = $errors;
                $data['classes'] = $classes;
            } else {
                $factura->status       = $new_status;
                $factura->message      = !empty($message) ? $message : null;
                $factura->payment_date = !empty($payment_date) ? strtotime($payment_date) : null;

                if (!$factura->save()) {
                    throw new Exception('Hubo un problema al actualizar el estado.');
                }

                \Log::debug("✅ Estado de la factura ID {$factura->id} actualizado a '{$new_status}'.");

                Session::set_flash('success', 'Estado actualizado correctamente.');
                Response::redirect('admin/compras/facturas/info/' . $factura_id);
            }
        } catch (Exception $e) {
            \Log::error('Error al actualizar estado: ' . $e->getMessage());
            Session::set_flash('error', $e->getMessage());
        }
    }

    # PREPARAR PRODUCTOS
    $productos = [];
    if (!empty($invoice_data['productos'])) {
        foreach ($invoice_data['productos'] as $producto) {
            $productos[] = [
                'noidentificacion' => $producto['noidentificacion'] ?? 'N/A',
                'descripcion'      => $producto['descripcion'] ?? 'N/A',
                'cantidad'         => $producto['cantidad'] ?? 'N/A',
                'clave_unidad'     => $producto['clave_unidad'] ?? 'N/A',
                'valor_unitario'   => isset($producto['valor_unitario']) ? number_format($producto['valor_unitario'], 2) : '0.00',
                'importe'          => isset($producto['importe']) ? number_format($producto['importe'], 2) : '0.00',
            ];
        }
    }

    # USAR EL HELPER PARA ESTATUS
    $badge_html = Helper_Purchases::render_status('bill', $factura->status);
    
    # OBTENER PROVEEDOR RELACIONADO
    $provider = $factura->provider ?? null;

    # Calcular fecha tentativa de pago usando el helper
    $tentative_payment_ts = Helper_Payments::next_payment_date($factura, $provider);
    $tentative_payment    = $tentative_payment_ts ? date('Y-m-d', $tentative_payment_ts) : '';




    # PASAR DATOS A LA VISTA
    $data['tentative_payment'] = $tentative_payment;
    $data['badge_html']    = $badge_html;
    $data['factura']       = $factura;
    $data['provider_name'] = $provider_name;
    $data['invoice_data']  = $invoice_data;
    $data['productos']     = $productos;
    $data['classes']       = $classes;
    $data['errors']        = $errors;

    # CARGAR VISTA
    $this->template->title   = 'Información de la Factura';
    $this->template->content = View::forge('admin/compras/facturas/info', $data,false);
}



    /**
	 * ELIMINAR
	 *  ELIMINA UNA FACTURA (MARCAR COMO ELIMINADA)
	 *
	 * @access  public
	 * @return  Void
	 */
    public function action_eliminar($factura_id)
    {
        # VERIFICAR SI EL USUARIO ESTÁ AUTENTICADO Y TIENE PERMISOS DE ADMINISTRADOR
        if (!Auth::check() || Auth::get('group') < 50) {
            Session::set_flash('error', 'No tienes permisos para realizar esta acción.');
            Response::redirect('admin/compras');
        }

        # OBTENER LA FACTURA
        $factura = Model_Providers_Bill::query()
            ->where('id', $factura_id)
            ->get_one();

        # VALIDAR SI LA FACTURA EXISTE
        if (!$factura) {
            Session::set_flash('error', 'La factura no existe o ya fue eliminada.');
            Response::redirect('admin/compras');
        }

        try {
            # ACTUALIZAR EL ESTADO A CANCELADA (3) Y MARCAR COMO ELIMINADA (deleted = 1)
            $factura->status = 99;
            $factura->deleted = 1;

            if (!$factura->save()) {
                throw new Exception('Error al eliminar la factura.');
            }

            # REGISTRAR EN LOGS
            \Log::info("🗑️ Factura ID {$factura->id} marcada como eliminada.");

            # MENSAJE DE ÉXITO
            Session::set_flash('success', 'Factura eliminada correctamente.');
            Response::redirect('admin/compras');

        } catch (Exception $e) {
            \Log::error('Error al eliminar factura: ' . $e->getMessage());
            Session::set_flash('error', 'Hubo un problema al eliminar la factura.');
            Response::redirect('admin/compras/facturas/info/' . $factura_id);
        }
    }

    
}
