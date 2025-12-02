<?php
/**
 * CONTROLADOR DE ÓRDENES DE COMPRA PARA ADMIN
 * GESTIONA ALTAS, EDICIÓN Y CONSULTA DE OCs
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Compras_Ordenes extends Controller_Admin
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
 * LISTADO DE ÓRDENES DE COMPRA (Optimizado)
 */
public function action_index($search = '')
{
    $data         = [];
    $ordenes_info = [];
    $per_page     = 100;

    // ==============================
    // Filtros desde GET
    // ==============================
    $status       = Input::get('status', '');
    $fecha_desde  = Input::get('fecha_desde', date('Y') . '-01-01');
    $fecha_hasta  = Input::get('fecha_hasta', date('Y') . '-12-31');

    // Normalizar search
    $search = Input::get('search', $search);
    $search = trim($search);
    $search = str_replace('+', ' ', rawurldecode($search));

    // ==============================
    // Consulta base
    // ==============================
    $query = Model_Providers_Order::query()
        ->related('provider')
        ->related('currency')
        ->related('bills')
        ->order_by('id', 'desc')
        ->where('deleted', 0);

    // ==============================
    // Búsqueda general
    // ==============================
    if (!empty($search)) {
        $query->where_open()
            ->where('code_order', 'like', '%' . $search . '%')
            ->or_where('provider.name', 'like', '%' . $search . '%')
        ->where_close();
    }

    // ==============================
    // Filtro por estatus
    // ==============================
    if ($status !== '' && is_numeric($status)) {
        $query->where('status', '=', (int) $status);
    }

    // ==============================
    // Filtro por fechas
    // ==============================
    $desde_ts = strtotime($fecha_desde . ' 00:00:00');
    $hasta_ts = strtotime($fecha_hasta . ' 23:59:59');

    $query->where('date_order', '>=', $desde_ts);
    $query->where('date_order', '<=', $hasta_ts);

    // ==============================
    // Paginación
    // ==============================
    $config = [
        'name'           => 'ordenes',
        'pagination_url' => Uri::current() . '?' . http_build_query(Input::get()),
        'total_items'    => $query->count(),
        'per_page'       => $per_page,
        'uri_segment'    => 'pagina',
        'show_first'     => true,
        'show_last'      => true,
    ];
    $pagination = Pagination::forge('ordenes', $config);

    // Obtener datos
    $ordenes = $query
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

    // ==============================
    // Formatear información
    // ==============================
    foreach ($ordenes as $order) {

        $provider_name = $order->provider->name ?? 'Sin proveedor';
        $currency_code = $order->currency->code ?? '';
        $currency_name = $order->currency->name ?? '';
        $currency_label = ($currency_code && $currency_name)
            ? "$currency_code - $currency_name"
            : $currency_code;

        $facturas_count = $order->bills ? count($order->bills) : 0;

        $authorized_by = '---';
        if ($order->authorized_by) {
            $u = Model_User::find($order->authorized_by);
            if ($u) {
                $authorized_by = $u->username ?? ($u->name ?? 'Usuario ID ' . $u->id);
            }
        }

        $ordenes_info[] = [
            'id'             => $order->id,
            'code_order'     => $order->code_order,
            'provider'       => $provider_name,
            'currency_label' => $currency_label,
            'total'          => number_format($order->total, 2, '.', ','),
            'status'         => Helper_Purchases::label('order', $order->status),
            'badge_color'    => Helper_Purchases::badge_class('order', $order->status),

            'date_order'     => $order->date_order ? date('d/m/Y', $order->date_order) : '',
            'created_at'     => $order->created_at ? date('d/m/Y H:i', $order->created_at) : '',

            'facturas_count' => $facturas_count,
            'invoiced_total' => $order->invoiced_total ?? 0,
            'balance_total'  => $order->balance_total ?? 0,

            'authorized_by'  => $authorized_by,
        ];
    }

    // ==============================
    // Catálogo de estatus
    // ==============================
    $status_list = Helper_Purchases::status_list('order');

    // ==============================
    // Vista
    // ==============================
    $data = [
        'ordenes'      => $ordenes_info,
        'search'       => $search,
        'status'       => $status,
        'fecha_desde'  => $fecha_desde,
        'fecha_hasta'  => $fecha_hasta,
        'status_list'  => $status_list,
        'pagination'   => $pagination->render(),
    ];

    $this->template->title   = 'Órdenes de Compra';
    $this->template->content = View::forge('admin/compras/ordenes/index', $data, false);
}


    /**
     * AGREGAR NUEVA ORDEN DE COMPRA
     */
    public function action_agregar()
{
    // PERMISOS
    if (!Helper_Permission::can('compras_ordenes', 'create')) {
        Session::set_flash('error', 'No tienes permiso para crear órdenes de compra.');
        Response::redirect('admin/compras/ordenes');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('provider_id', 'code_order', 'date_order', 'currency_id', 'notes');
    $errors = array ();
    $currency_opts = array();
    $tax_opts = array();

    $provider_id = Input::get('provider_id', 0);
    $provider = $provider_id ? Model_Provider::find($provider_id) : null;
    


    // Inicializar clases para cada campo
    foreach ($fields as $field) {
        $classes[$field] = array(
            'form-group'   => null,
            'form-control' => null,
        );
    }

    $providers = Model_Provider::query()->get();
    $data['providers'] = $providers;
    $data['next_code'] = Helper_OC::next_code();

    \Log::debug('[ORDEN] Entrando a action_agregar');

    if (Input::method() == 'POST') {
        // VALIDACIÓN FuelPHP
        $val = Validation::forge('order');
        $val->add_field('provider_id', 'Proveedor', 'required|valid_string[numeric]');
        $val->add_field('code_order', 'Código OC', 'required|min_length[1]|max_length[100]');
        $val->add_field('date_order', 'Fecha', 'required');
        $val->add_field('currency_id', 'Moneda', 'required');
        $val->add_field('notes', 'Notas', 'max_length[255]');

        if ($val->run()) {
            \Log::debug('[ORDEN] Validación correcta. Intentando guardar la orden...');
            try {
                // CAPTURA DE PRODUCTOS/SERVICIOS
                $productos = Input::post('productos', []);
                $total_orden = 0;

                // PRIMERO validamos y calculamos el total
                $detalles_validos = [];
                foreach ($productos as $idx => $prod) {
                    $tipo = isset($prod['tipo']) ? $prod['tipo'] : 'producto';
                    $is_producto = $tipo == 'producto';

                    $product_id = $is_producto ? (isset($prod['product_id']) ? $prod['product_id'] : null) : null;
                    $code_product = $is_producto
                        ? (isset($prod['code_product']) ? $prod['code_product'] : '')
                        : (isset($prod['code_product']) ? $prod['code_product'] : '');

                    // Validaciones:
                    if ($is_producto) {
                        // Si es producto, product_id es obligatorio
                        if (empty($product_id)) continue;
                    } else {
                        // Si es servicio, el código y descripción son obligatorios
                        if (empty($code_product) && empty($prod['description'])) continue;
                    }
                    if (empty($prod['description']) || empty($prod['quantity'])) continue;

                    // Cast numérico y cálculos
                    $cantidad = floatval($prod['quantity']);
                    $precio   = floatval($prod['unit_price']);
                    $ivaRate  = isset($prod['iva']) ? floatval($prod['iva']) : 0;
                    $subtotal = $cantidad * $precio;
                    $ivaMonto = $subtotal * $ivaRate;   
                    $total    = $subtotal + $ivaMonto;

                    $total_orden += $total;

                    // Guardamos el detalle válido para después
                    $detalles_validos[] = [
                        'product_id'   => $product_id,
                        'code_product' => $code_product,
                        'description'  => $prod['description'],
                        'quantity'     => $cantidad,
                        'unit_price'   => $precio,
                        'subtotal'     => $subtotal,
                        'iva'          => $ivaMonto,
                        'total'        => $total,
                    ];
                }

                // OPCIONAL: impide crear una OC sin partidas válidas
                if (empty($detalles_validos)) {
                    Session::set_flash('error', 'Debes capturar al menos un producto o servicio válido.');
                    return Response::redirect('admin/compras/ordenes/agregar');
                }

            

                // CREAR ORDEN
                $order = Model_Providers_Order::forge([
                    'provider_id' => Input::post('provider_id'),
                    'code_order'  => Input::post('code_order'),
                    'date_order'  => strtotime(Input::post('date_order')),
                    'currency_id' => Input::post('currency_id'),
                    'status'      => 0,
                    'total'       => $total_orden,
                    'notes'       => Input::post('notes'),
                    'deleted'     => 0,
                    'retention'   => 0, //validar despues super importante no se me pase
                    'has_invoice'   => 0, //validar despues super importante no se me pase
                    'origin'      => 0, //0 sera origin de la web y 1 sera del sap
                    'created_at'  => time(),
                    //'updated_at'  => time(),
                ]);
                $order->save();
                \Log::debug('[ORDEN] Orden guardada con ID: ' . $order->id);

                // Guardar detalles
                foreach ($detalles_validos as $detalle) {
                    \Log::debug('[DETALLE] Intentando guardar: ' . json_encode($detalle));
                    Model_Providers_Order_Detail::forge([
                        'order_id'     => $order->id,
                        'product_id'   => $detalle['product_id'],
                        'code_product' => $detalle['code_product'],
                        'description'  => $detalle['description'],
                        'quantity'     => $detalle['quantity'],
                        'unit_price'   => $detalle['unit_price'],
                        'subtotal'     => $detalle['subtotal'],
                        'iva'          => $detalle['iva'],
                        'total'        => $detalle['total'],
                        'delivered'        => 0,
                        'invoiced'        => 0,
                        'received_at'   => time(),
                        'created_at'   => time(),
                        //'updated_at'   => time(),
                    ])->save();
                    \Log::debug('[ORDEN] Detalle agregado: ' . json_encode($detalle));
                    
                }

                Session::set_flash('success', 'Orden de compra creada correctamente.');
                Response::redirect('admin/compras/ordenes');
            } catch (Exception $e) {
                \Log::error('[ORDEN] EXCEPCIÓN al guardar orden: ' . $e->getMessage());
                Session::set_flash('error', $e->getMessage());
            }
        } else {
            // ALMACENA LOS ERRORES DETECTADOS
            $data['errors'] = $val->error();
            foreach ($classes as $name => $class) {
                $classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
                $classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
                // Devuelve el valor capturado para que el usuario no pierda la información
                $data[$name] = Input::post($name);
            }
            Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');
        }
    }

    // busqueda de prodcutos
    $productos = Model_Product::query()->get();
    $data['all_products'] = [];
    foreach ($productos as $p) {
        $data['all_products'][] = [
            'id'   => $p->id,
            'code' => $p->code,
            'name' => $p->name
        ];
    }
    // INICIALIZAR OPCIONES DE MONEDA
    $currency_opts = array('' => 'Selecciona moneda...');

    // BUSCAR MONEDAS ACTIVAS
    $currencies = Model_Currency::query()
        ->where('deleted', 0)
        ->order_by('name', 'asc')
        ->get();

    if (!empty($currencies)) {
        foreach ($currencies as $currency) {
            $currency_opts[$currency->id] = $currency->code . ' - ' . $currency->name . (!empty($currency->symbol) ? " ({$currency->symbol})" : '');
        }
    }

    // Controlador (action_agregar o action_editar)
    $tax_opts = array('' => 'Selecciona impuesto...');
    $taxes = Model_Tax::query()
        ->order_by('name', 'asc')
        ->get();

    if (!empty($taxes)) {
        foreach ($taxes as $tax) {
            $label = $tax->code . ' (' . ($tax->rate * 100) . '%)';
            $tax_opts[$tax->id] = $label;
        }
    }

    // PREPARAR CATÁLOGO DE IMPUESTOS PARA JS (tax_opts_js)
$tax_opts_js = [];
if (!empty($taxes)) {
    foreach ($taxes as $tax) {
        $tax_opts_js[] = [
            'id'    => $tax->id,
            'rate'  => floatval($tax->rate),
            'label' => $tax->code . ' (' . ($tax->rate * 100) . '%)'
        ];
    }
}

// ----- BUSCAR RETENCIONES (Model_Retention) -----
$retention_opts = array('' => 'Selecciona retención...');
$retentions = Model_Retention::query()
    ->order_by('code', 'asc')
    ->get();

$retention_opts_js = [];
if (!empty($retentions)) {
    foreach ($retentions as $ret) {
        $label = $ret->code . ' (' . ($ret->rate * 100) . '%)';
        $retention_opts[$ret->id] = $label;
        $retention_opts_js[] = [
            'id'    => $ret->id,
            'rate'  => floatval($ret->rate),
            'label' => $label
        ];
    }
}

    $data['provider_id'] = $provider_id;
    $data['provider_name'] = $provider ? $provider->name : '';

$data['retention_opts']    = $retention_opts;
$data['retention_opts_js'] = $retention_opts_js;

    $data['tax_opts_js'] = $tax_opts_js;
    $data['tax_opts'] = $tax_opts;
    $data['currency_opts'] = $currency_opts;
    $data['classes'] = $classes;

    $this->template->title = 'Agregar Orden de Compra';
    $this->template->content = View::forge('admin/compras/ordenes/agregar', $data, false);
}


    /**
     * EDITAR ORDEN DE COMPRA
     */
   /**
 * CONTROLADOR PARA CARGAR LA VISTA DE EDICIÓN
 */
public function action_editar($order_id = 0)
{

   

    // PERMISO PARA EDITAR
    if (!Helper_Permission::can('compras_ordenes', 'edit')) {
        Session::set_flash('error', 'No tienes permiso para editar órdenes de compra.');
        Response::redirect('admin/compras/ordenes');
    }
    if (!$order_id || !is_numeric($order_id)) {
        Response::redirect('admin/compras/ordenes');
    }
    // Busca orden (sin detalles, esos se cargan por AJAX)
    $order = Model_Providers_Order::query()
        ->where('id', $order_id)
        ->where('deleted', 0)
        ->where('status','='. 0)
        ->get_one();
    if (!$order) {
        Session::set_flash('error', '<br> La orden autorizada no se puede editar.');
        Response::redirect('admin/compras/ordenes');
    }

    // BLOQUEAR SI EXISTEN FACTURAS RELACIONADAS
    $facturas_vinculadas = Model_Providers_Bill::query()
        ->where('order_id', $order_id)
        ->where('deleted', 0)
        ->count();

    if ($facturas_vinculadas > 0) {
        Session::set_flash('warning', 'No puedes modificar o eliminar una orden con facturas asociadas.');
        Response::redirect('admin/compras/ordenes/info/' . $order_id);
    }


    // Pasa solo lo básico para JS
    $data = [
        'order'      => $order,
        'order_id'   => $order->id,
        'providers'  => Model_Provider::query()->get(), // Para combo de proveedores
    ];
    // Renderiza la vista
    $this->template->title   = 'Editar Orden de Compra';
    $this->template->content = View::forge('admin/compras/ordenes/editar', $data, false);
}



// =========================================================
    // VER DETALLE DE ORDEN
    // =========================================================    
    public function action_info($order_id = 0)
{

    if (!Helper_Permission::can('compras_ordenes', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver órdenes.');
        Response::redirect('admin/compras/ordenes');
    }

    if (!$order_id || !is_numeric($order_id)) {
        Session::set_flash('error', 'Orden no válida.');
        Response::redirect('admin/compras/ordenes');
    }

    // 🔹 Cargar solo relaciones válidas
    $order = Model_Providers_Order::query()
        ->related('provider')
        ->related('currency')
        ->related('bills')
        ->where('id', $order_id)
        ->where('deleted', 0)
        ->get_one();

    if (!$order) {
        Session::set_flash('error', 'No se encontró la orden.');
        Response::redirect('admin/compras/ordenes');
    }

    // === DATOS GENERALES ===
    $data = [
        'id'          => $order->id,
        'code_order'  => $order->code_order,
        'provider'    => $order->provider ? $order->provider->name : '---',
        'rfc'         => $order->provider ? $order->provider->rfc : '---',
        'currency'    => $order->currency ? $order->currency->code : '---',
        'date_order'  => $order->date_order,
        'notes'       => $order->notes,
        'total'       => $order->total,
        'created_at'  => $order->created_at,
        'updated_at'  => $order->updated_at,
        'origin'      => $order->origin,
        'status'      => Helper_Purchases::render_status('order', $order->status),
        'authorized_by' => $order->authorized_by ? Model_User::find($order->authorized_by)->username : '---',
        'authorized_at' => $order->authorized_at ? date('d/m/Y H:i', strtotime($order->authorized_at)) : '---',
        'invoiced_total' => number_format($order->invoiced_total, 2),
        'balance_total'  => number_format($order->balance_total, 2),

    ];

    // === DÍAS DE CRÉDITO Y FECHA TENTATIVA ===
    $data['credit_days']       = (int) ($order->provider->pay_days ?? 0);
    $tentative_payment_ts      = Helper_Payments::next_payment_date($order, $order->provider);
    $data['tentative_payment'] = $tentative_payment_ts ? date('Y-m-d', $tentative_payment_ts) : '---';

    // === DETALLES ===
    $details = Model_Providers_Order_Detail::query()
        ->where('order_id', $order_id)
        ->where('deleted', 0)
        ->get();

    $data['detalles'] = [];
    foreach ($details as $d) {
        $data['detalles'][] = [
            'code_product' => $d->code_product,
            'description'  => $d->description,
            'quantity'     => $d->quantity,
            'unit_price'   => $d->unit_price,
            'subtotal'     => $d->subtotal,
            'iva'          => $d->iva,
            'retencion'    => $d->retencion,
            'total'        => $d->total,
        ];
    }

    // === FACTURAS RELACIONADAS ===
    $data['facturas'] = [];
    $bills_ids = [];

    if (!empty($order->bills)) {
        foreach ($order->bills as $b) {
            $data['facturas'][] = [
                'id'      => $b->id,
                'uuid'    => $b->uuid,
                'total'   => $b->total,
                'status'  => Helper_Purchases::label('bill', $b->status),
                'badge'   => Helper_Purchases::badge_class('bill', $b->status),
                'created' => date('d/m/Y', $b->created_at)
            ];
            $bills_ids[] = $b->id;
        }
    }

    // === CONTRARECIBOS ===
    $data['contrarecibos'] = [];

    // 🔹 Verificamos cómo se relaciona: si tiene order_id, usamos eso.
    if (DB::query("SHOW COLUMNS FROM providers_receipts LIKE 'order_id'")->execute()->count() > 0) {
        $receipts = Model_Providers_Receipt::query()
            ->where('order_id', $order_id)
            ->where('deleted', 0)
            ->get();
    }
    // 🔹 Si no tiene order_id pero sí provider_id (caso general)
    else {
        $receipts = Model_Providers_Receipt::query()
            ->where('provider_id', $order->provider_id)
            ->where('deleted', 0)
            ->get();
    }

    foreach ($receipts as $r) {
        $data['contrarecibos'][] = [
            'id'     => $r->id,
            'code'   => $r->receipt_number ?? $r->code_receipt ?? '---',
            'total'  => $r->total,
            'date'   => $r->receipt_date ? date('d/m/Y', strtotime($r->receipt_date)) : date('d/m/Y', $r->created_at),
            'status' => Helper_Purchases::label('receipt', $r->status),
            'badge'  => Helper_Purchases::badge_class('receipt', $r->status),
        ];
    }


    // === REPS / PAGOS ===
    $data['reps'] = [];
    if (!empty($bills_ids)) {
        $reps = Model_Providers_Bill_Rep::query()
            ->where('provider_bill_id', 'in', $bills_ids)
            ->where('deleted', 0)
            ->get();

        foreach ($reps as $rep) {
            $data['reps'][] = [
                'id'     => $rep->id,
                'folio'  => $rep->folio,
                'amount' => $rep->amount,
                'date'   => date('d/m/Y', $rep->created_at),
            ];
        }
    }

    // === NOTAS DE CRÉDITO ===
    $data['creditnotes'] = [];
    if (!empty($bills_ids)) {
        $creditnotes = Model_Providers_Creditnote::query()
            ->where('invoice_id', 'in', $bills_ids)
            ->where('deleted', 0)
            ->get();

        foreach ($creditnotes as $cn) {
            $data['creditnotes'][] = [
                'id'     => $cn->id,
                'uuid'   => $cn->uuid,
                'total'  => $cn->total,
                'date'   => date('d/m/Y', $cn->created_at),
                'status' => Helper_Purchases::label('cn', $cn->status),
                'badge'  => Helper_Purchases::badge_class('cn', $cn->status),
            ];
        }
    }

// =========================================================
// LÓGICA PARA CONTROLAR VISIBILIDAD DE BOTONES EN LA VISTA
// =========================================================

// =========================================================
// CALCULAR TOTAL FACTURADO (usando $data['facturas'])
// =========================================================
$total_facturado = 0;
if (!empty($data['facturas'])) {
    foreach ($data['facturas'] as $f) {
        $total_facturado += (float) $f['total'];
    }
}

// Si el total facturado es igual o mayor al total de la orden
$orden_completa = $total_facturado >= (float) $order->total;

// Guardar para depuración
\Log::debug("[ORDEN][TOTAL] OC={$order->id} | Facturado={$total_facturado} | TotalOrden={$order->total}");

// =========================================================
// DETECTAR SI LA ORDEN YA TIENE CONTRARECIBO (DIRECTO O INDIRECTO)
// =========================================================

// 1️⃣ Contrarecibo directo (campo order_id en providers_receipts)
$tiene_contrarecibo_directo = Model_Providers_Receipt::query()
    ->where('order_id', $order->id)
    ->count() > 0;

// 2️⃣ Contrarecibo indirecto (campo order_id en providers_receipts_details)
$tiene_contrarecibo_indirecto = Model_Providers_Receipts_Details::query()
    ->where('order_id', $order->id)
    ->count() > 0;

// 3️⃣ Consolidar resultado
$tiene_contrarecibo = $tiene_contrarecibo_directo || $tiene_contrarecibo_indirecto;

// =========================================================
// BANDERAS DE ACCIÓN
// =========================================================
$puede_contrarecibo = !$tiene_contrarecibo && !$orden_completa && $order->status == 1;
$puede_autorizar    = $order->status == 0;
$puede_editar       = $order->status == 0;

// =========================================================
// PASAR VARIABLES A LA VISTA
// =========================================================
$data['puede_autorizar']        = $puede_autorizar;
$data['puede_editar']           = $puede_editar;
$data['puede_contrarecibo']     = $puede_contrarecibo;
$data['orden_completa']         = $orden_completa;
$data['tiene_contrarecibo']     = $tiene_contrarecibo;
$data['tiene_contrarecibo_dir'] = $tiene_contrarecibo_directo;
$data['tiene_contrarecibo_indir'] = $tiene_contrarecibo_indirecto;

$data['invoiced_total']  = $order->invoiced_total ?? 0;
$data['balance_total']   = $order->balance_total ?? 0;
$data['authorized_at']   = $order->authorized_at ?? null;

// Resolver nombre del usuario que autorizó, si aplica
$data['authorized_by_name'] = '---';
if (!empty($order->authorized_by)) {
    $user = Model_User::find($order->authorized_by);
    if ($user) {
        $data['authorized_by_name'] = $user->username ?? ($user->name ?? 'Usuario ID '.$user->id);
    }
}


// Log opcional
\Log::debug("[ORDEN][CHECK CONTRARECIBO] OC={$order->id} | Facturado={$total_facturado} | Directo=" .
    ($tiene_contrarecibo_directo ? 'Sí' : 'No') . " | Indirecto=" .
    ($tiene_contrarecibo_indirecto ? 'Sí' : 'No'));

    // === RENDERIZAR ===
    
    $this->template->title   = 'Detalle de Orden de Compra';
    $this->template->content = View::forge('admin/compras/ordenes/info', $data, false);
}




    /**
     * ELIMINAR ORDEN (SOFT DELETE)
     */
    public function action_eliminar($order_id = 0)
    {
        $order = Model_Providers_Order::find($order_id);
        if (!$order) {
            Session::set_flash('error', 'La orden no existe.');
            Response::redirect('admin/compras/ordenes');
        }
        // BLOQUEAR SI EXISTEN FACTURAS RELACIONADAS
        $facturas_vinculadas = Model_Providers_Bill::query()
            ->where('order_id', $order_id)
            ->where('deleted', 0)
            ->count();

        if ($facturas_vinculadas > 0) {
            Session::set_flash('warning', 'No puedes modificar o eliminar una orden con facturas asociadas.');
            Response::redirect('admin/compras/ordenes/info/' . $order_id);
        }

        $order->deleted = 1;
        $order->updated_at = time();
        $order->save();
        Session::set_flash('success', 'Orden eliminada.');
        Response::redirect('admin/compras/ordenes');
    }



    // =========================================================
// AUTORIZAR ORDEN DE COMPRA (PANTALLA DE REVISIÓN/EDICIÓN)
// =========================================================
public function action_autorizar($order_id = 0)
{
    // Validar ID
    $order_id = (int) $order_id;

    // Cargar orden con relaciones necesarias
    $order = Model_Providers_Order::find($order_id, array(
        'related' => array(
            'provider',
            'details' => array(
                'where' => array('deleted' => 0),
                'order_by' => array('id' => 'asc'),
            ),
        ),
    ));

    if (!$order) {
        \Session::set_flash('error', 'La orden de compra no fue encontrada.');
        \Response::redirect('admin/compras/ordenes');
    }

    // Si ya está autorizada, puedes decidir:
    // 0 = capturada, 1 = autorizada, 2 = rechazada, etc. (ajusta a tu catálogo)
    if ((int) $order->status === 1) {
        \Session::set_flash('info', 'La orden ya fue autorizada previamente.');
        \Response::redirect('admin/compras/ordenes/info/' . $order_id);
    }

    // Prefill proveedor para Vue
    $prefill_provider_id   = $order->provider_id;
    $prefill_provider_name = $order->provider ? $order->provider->name : '';

    // View data
    $data = array(
        'order'                => $order,
        'order_id'             => $order->id,
        'prefill_provider_id'  => $prefill_provider_id,
        'prefill_provider_name'=> $prefill_provider_name,
    );

    $this->template->title   = 'Autorizar Orden de Compra';
    $this->template->content = \View::forge('admin/compras/ordenes/autorizar', $data, false);
}


// =========================================================
// ASOCIAR FACTURAS SIN ORDEN
// =========================================================
public function action_asociar($order_id = 0)
{
    // =========================================================
    // VALIDAR ID DE ORDEN
    // =========================================================
    if (!$order_id || !is_numeric($order_id)) {
        Session::set_flash('error', 'Orden no válida.');
        Response::redirect('admin/compras/ordenes');
    }

    // =========================================================
    // OBTENER ORDEN
    // =========================================================
    $order = Model_Providers_Order::find($order_id);
    if (!$order) {
        Session::set_flash('error', 'No se encontró la orden especificada.');
        Response::redirect('admin/compras/ordenes');
    }

    // =========================================================
    // FACTURAS DISPONIBLES (SIN ORDEN ASIGNADA)
    // =========================================================
    $facturas_sin_orden = Model_Providers_Bill::query()
        ->where('deleted', 0)
        ->where('provider_id', $order->provider_id)
        ->where('order_id', null)
        ->order_by('created_at', 'desc')
        ->get();

    // =========================================================
    // PROCESAR ENVÍO (POST)
    // =========================================================
    if (Input::method() == 'POST') {
        $facturas_seleccionadas = Input::post('facturas', []);
        $asociadas = 0;
        $total_facturas_nuevas = 0;

        // =========================================================
        // VALIDAR SELECCIÓN
        // =========================================================
        if (empty($facturas_seleccionadas)) {
            Session::set_flash('info', 'Debes seleccionar al menos una factura para asociar.');
            Response::redirect('admin/compras/ordenes/asociar/' . $order_id);
        }

        // =========================================================
        // CALCULAR TOTAL YA ASOCIADO
        // =========================================================
        $facturas_asociadas = Model_Providers_Bill::query()
            ->where('order_id', $order->id)
            ->where('deleted', 0)
            ->get();

        $total_asociadas_actuales = 0;
        foreach ($facturas_asociadas as $fexistente) {
            $total_asociadas_actuales += (float) $fexistente->total;
        }

        // =========================================================
        // CALCULAR TOTAL DE LAS NUEVAS SELECCIONADAS
        // =========================================================
        foreach ($facturas_seleccionadas as $fid) {
            $factura = Model_Providers_Bill::find($fid);
            if ($factura && $factura->provider_id == $order->provider_id && $factura->deleted == 0) {
                $total_facturas_nuevas += (float) $factura->total;
            }
        }

        // =========================================================
        // VALIDAR MONTO TOTAL COMBINADO (ya asociadas + nuevas)
        // =========================================================
        $total_combinado = $total_asociadas_actuales + $total_facturas_nuevas;

        // =========================================================
        // ACTUALIZAR TOTALES EN ORDEN (FACTURADO Y SALDO)
        // =========================================================
        $order->invoiced_total = $total_combinado;
        $order->balance_total  = max(0, $order->total - $total_combinado);
        $order->updated_at     = time();
        $order->save();

        \Log::info("[ORDEN][ASOCIAR] Totales actualizados en OC={$order->id} | Facturado={$order->invoiced_total} | Saldo={$order->balance_total}");


        if ($total_combinado > $order->total) {
            $mensaje = 'No es posible asociar estas facturas.<br>'
                . 'El total combinado ($' . number_format($total_combinado, 2) . ') '
                . 'supera el total de la orden ($' . number_format($order->total, 2) . ').<br>'
                . 'Actualmente ya hay facturas asociadas por $' . number_format($total_asociadas_actuales, 2) . '.';
            
            \Log::warning("[ORDEN][ASOCIAR] Intento inválido en orden #{$order->id}. Facturas actuales: {$total_asociadas_actuales}, nuevas: {$total_facturas_nuevas}, total combinado: {$total_combinado}");
            
            Session::set_flash('warning', $mensaje);
            Response::redirect('admin/compras/ordenes/asociar/' . $order_id);
        }

        // =========================================================
        // ASOCIAR FACTURAS A LA ORDEN
        // =========================================================
        foreach ($facturas_seleccionadas as $fid) {
            $factura = Model_Providers_Bill::find($fid);
            if ($factura && $factura->provider_id == $order->provider_id && $factura->deleted == 0) {
                $factura->order_id = $order->id;
                $factura->updated_at = time();
                $factura->save();
                $asociadas++;
            }
        }

        // =========================================================
        // RESULTADO FINAL
        // =========================================================
        if ($asociadas > 0) {
            Session::set_flash(
                'success',
                "{$asociadas} factura(s) asociadas correctamente a la orden <b>#{$order->code_order}</b>.<br>"
                . "Total asociado ahora: $" . number_format($total_combinado, 2)
            );
        } else {
            Session::set_flash('info', 'No se asociaron facturas. Verifica la selección.');
        }

        Response::redirect('admin/compras/ordenes/info/' . $order_id);
    }

    // =========================================================
    // DATOS PARA LA VISTA
    // =========================================================
    $data = [
        'order'              => $order,
        'facturas_sin_orden' => $facturas_sin_orden,
    ];

    $this->template->title   = 'Asociar Facturas a Orden';
    $this->template->content = View::forge('admin/compras/ordenes/asociar', $data, false);
}

public function action_imprimir($order_id = 0)
{
    // ============================================
    // VALIDAR ID
    // ============================================
    if (!$order_id || !is_numeric($order_id)) {
        throw new \HttpNotFoundException("Orden no válida.");
    }

    // ============================================
    // CARGAR ORDEN
    // ============================================
    $order = Model_Providers_Order::query()
        ->related('provider')
        ->related('currency')
        ->where('id', $order_id)
        ->get_one();

    if (!$order) {
        throw new \HttpNotFoundException("Orden no encontrada.");
    }

    // ============================================
    // CARGAR DETALLES
    // ============================================
    $details = Model_Providers_Order_Detail::query()
        ->where('order_id', $order_id)
        ->where('deleted', 0)
        ->order_by('id', 'asc')
        ->get();

    $detalles = [];
    $subtotal_general = 0;
    $iva_general = 0;
    $ret_general = 0;
    $total_general = 0;

    foreach ($details as $d) {

        $sub = (float) ($d->subtotal ?? 0);
        $iva = (float) ($d->iva ?? 0);
        $ret = (float) ($d->retencion ?? 0);
        $tot = (float) ($d->total ?? 0);

        $subtotal_general += $sub;
        $iva_general += $iva;
        $ret_general += $ret;
        $total_general += $tot;

        $detalles[] = [
            'tipo'         => $d->product_id ? 'artículo' : 'servicio',
            'code_product' => $d->code_product,
            'description'  => $d->description,
            'quantity'     => $d->quantity,
            'unit_price'   => $d->unit_price,
            'subtotal'     => $sub,
            'iva'          => $iva,
            'retencion'    => $ret,
            'total'        => $tot,
        ];
    }

    // ============================================
    // DATA PARA LA VISTA
    // ============================================
    $data = [
        'order'            => $order,
        'detalles'         => $detalles,
        'proveedor'        => $order->provider,
        'subtotal_general' => $subtotal_general,
        'iva_general'      => $iva_general,
        'ret_general'      => $ret_general,
        'total_general'    => $total_general,
        'vista_previa'     => true, // marcar que es vista previa
    ];

    // ============================================
    // SI VIENE ?pdf=1 -> GENERA PDF
    // ============================================
    if (Input::get('pdf') == 1) {

        $html = \View::forge('admin/compras/ordenes/imprimir', $data)->render();

        $dompdf = new \Dompdf\Dompdf();
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $filename = 'OC_'.$order->code_order.'.pdf';
        $dompdf->stream($filename, ['Attachment' => 1]);
        exit();
    }

    // ============================================
    // VISTA PREVIA EN HTML
    // ============================================
    $this->template->title = 'Imprimir Orden de Compra';
    $this->template->content = \View::forge('admin/compras/ordenes/imprimir', $data, false);
}



}
