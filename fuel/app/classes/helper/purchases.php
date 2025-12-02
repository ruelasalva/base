<?php
/**
 * HELPER COMPRAS (EXTENDIDO)
 * USO: ADMIN / PROVEEDORES
 * Centraliza la conversión de estatus, sincronización entre documentos,
 * y validaciones de reglas de negocio del flujo:
 * ORDEN → FACTURA → CONTRARECIBO → REP → NOTA.
 */
class Helper_Purchases
{
    // ==========================================================
    // MAPA DE ESTATUS VISUALES
    // ==========================================================
    protected static $maps = [

        // ===================== ORDENES DE COMPRA =====================
        'order' => [
            0  => ['label'=>'Por autorizar', 'icon'=>'fas fa-user-check', 'tooltip'=>'Orden pendiente de aprobación.', 'badge'=>'secondary'],
            1  => ['label'=>'Abierta', 'icon'=>'fas fa-folder-open', 'tooltip'=>'Orden autorizada y activa.', 'badge'=>'primary'],
            2  => ['label'=>'Parcial', 'icon'=>'fas fa-tasks', 'tooltip'=>'Orden parcialmente facturada o recibida.', 'badge'=>'info'],
            3  => ['label'=>'Cerrada', 'icon'=>'fas fa-check-circle', 'tooltip'=>'Orden totalmente facturada y cerrada.', 'badge'=>'success'],
            4  => ['label'=>'Cancelada', 'icon'=>'fas fa-times-circle', 'tooltip'=>'Orden cancelada.', 'badge'=>'danger'],
            5  => ['label'=>'Rechazada', 'icon'=>'fas fa-ban', 'tooltip'=>'Orden rechazada durante autorización.', 'badge'=>'danger'],
            6  => ['label'=>'En pausa', 'icon'=>'fas fa-pause-circle', 'tooltip'=>'Orden detenida temporalmente.', 'badge'=>'warning'],
            7  => ['label'=>'En contrarecibo', 'icon'=>'fas fa-file-invoice', 'tooltip'=>'Orden con facturas en contrarecibo pendiente de pago.', 'badge'=>'info'],
            8  => ['label'=>'En pago', 'icon'=>'fas fa-hand-holding-usd', 'tooltip'=>'Orden en proceso de pago.', 'badge'=>'primary'],
            9  => ['label'=>'Pagada', 'icon'=>'fas fa-dollar-sign', 'tooltip'=>'Orden totalmente liquidada.', 'badge'=>'success'],
            10 => ['label'=>'Automática', 'icon'=>'fas fa-robot', 'tooltip'=>'Orden generada automáticamente desde factura o contrarecibo.', 'badge'=>'warning'],
        ],

        // ===================== FACTURAS =====================
        'bill' => [
            0   => ['label'=>'Enviada', 'icon'=>'fas fa-upload', 'tooltip'=>'Factura cargada por el proveedor.', 'badge'=>'secondary'],
            1   => ['label'=>'Pendiente', 'icon'=>'fas fa-clock', 'tooltip'=>'Factura pendiente de validación.', 'badge'=>'secondary'],
            2   => ['label'=>'En revisión', 'icon'=>'fas fa-search', 'tooltip'=>'Factura en proceso de validación SAT.', 'badge'=>'info'],
            3   => ['label'=>'Autorizada', 'icon'=>'fas fa-thumbs-up', 'tooltip'=>'Factura validada y lista para contrarecibo.', 'badge'=>'primary'],
            4   => ['label'=>'Rechazada', 'icon'=>'fas fa-ban', 'tooltip'=>'Factura con inconsistencias o rechazada.', 'badge'=>'danger'],
            5   => ['label'=>'Pagada', 'icon'=>'fas fa-dollar-sign', 'tooltip'=>'Factura completamente liquidada.', 'badge'=>'success'],
            6   => ['label'=>'Parcialmente pagada', 'icon'=>'fas fa-adjust', 'tooltip'=>'Factura con pagos parciales o REP en parcialidades.', 'badge'=>'info'],
            7   => ['label'=>'En contrarecibo', 'icon'=>'fas fa-file-invoice', 'tooltip'=>'Factura incluida en contrarecibo pendiente de pago.', 'badge'=>'warning'],
            8   => ['label'=>'En pago', 'icon'=>'fas fa-hand-holding-usd', 'tooltip'=>'Factura en proceso de pago.', 'badge'=>'primary'],
            9   => ['label'=>'Con REP aplicado', 'icon'=>'fas fa-check-circle', 'tooltip'=>'Factura con REP aplicado (pagada).', 'badge'=>'success'],
            10  => ['label'=>'Subida (Admin)', 'icon'=>'fas fa-user-shield', 'tooltip'=>'Factura cargada manualmente por administrador.', 'badge'=>'warning'],
            99  => ['label'=>'Cancelada', 'icon'=>'fas fa-times', 'tooltip'=>'Factura cancelada o anulada.', 'badge'=>'danger'],
        ],

        // ===================== CONTRARECIBOS =====================
        'receipt' => [
            0  => ['label'=>'Pendiente', 'icon'=>'fas fa-clock', 'tooltip'=>'Contrarecibo pendiente de autorización.', 'badge'=>'secondary'],
            1  => ['label'=>'Autorizado', 'icon'=>'fas fa-thumbs-up', 'tooltip'=>'Contrarecibo autorizado.', 'badge'=>'primary'],
            2  => ['label'=>'Pagado', 'icon'=>'fas fa-dollar-sign', 'tooltip'=>'Contrarecibo liquidado.', 'badge'=>'success'],
            3  => ['label'=>'Cancelado', 'icon'=>'fas fa-ban', 'tooltip'=>'Contrarecibo cancelado.', 'badge'=>'danger'],
            4  => ['label'=>'Parcial / Ajuste', 'icon'=>'fas fa-adjust', 'tooltip'=>'Contrarecibo con ajustes o pagos parciales.', 'badge'=>'info'],
            5  => ['label'=>'En pago', 'icon'=>'fas fa-hand-holding-usd', 'tooltip'=>'Contrarecibo en proceso de pago.', 'badge'=>'primary'],
            10 => ['label'=>'Creado (Admin)', 'icon'=>'fas fa-user-shield', 'tooltip'=>'Contrarecibo generado manualmente por administrador.', 'badge'=>'warning'],
        ],

        // ===================== REP =====================
        'rep' => [
            0  => ['label'=>'Pendiente (Proveedor)', 'icon'=>'fas fa-clock', 'tooltip'=>'REP cargado por proveedor.', 'badge'=>'secondary'],
            1  => ['label'=>'Emitido', 'icon'=>'fas fa-file-invoice', 'tooltip'=>'REP emitido por el sistema.', 'badge'=>'info'],
            2  => ['label'=>'Aplicado', 'icon'=>'fas fa-check-circle', 'tooltip'=>'REP aplicado correctamente.', 'badge'=>'success'],
            3  => ['label'=>'Cancelado', 'icon'=>'fas fa-ban', 'tooltip'=>'REP cancelado.', 'badge'=>'danger'],
            4  => ['label'=>'Parcial', 'icon'=>'fas fa-adjust', 'tooltip'=>'REP parcial (abono o parcialidad).', 'badge'=>'info'],
            10 => ['label'=>'Pendiente (Admin)', 'icon'=>'fas fa-user-shield', 'tooltip'=>'REP ingresado manualmente por administrador.', 'badge'=>'warning'],
        ],

        // ===================== NOTAS DE CRÉDITO =====================
        'cn' => [
            0  => ['label'=>'Vigente (Proveedor)', 'icon'=>'fas fa-flag', 'tooltip'=>'Nota de crédito cargada por proveedor.', 'badge'=>'primary'],
            1  => ['label'=>'Aplicada', 'icon'=>'fas fa-check-circle', 'tooltip'=>'Nota de crédito aplicada al saldo.', 'badge'=>'success'],
            2  => ['label'=>'Cancelada', 'icon'=>'fas fa-times', 'tooltip'=>'Nota de crédito cancelada.', 'badge'=>'danger'],
            3  => ['label'=>'Rechazada', 'icon'=>'fas fa-ban', 'tooltip'=>'Nota de crédito rechazada.', 'badge'=>'danger'],
            4  => ['label'=>'Pendiente de validación', 'icon'=>'fas fa-search', 'tooltip'=>'Nota recibida sin revisar.', 'badge'=>'info'],
            10 => ['label'=>'Vigente (Admin)', 'icon'=>'fas fa-user-shield', 'tooltip'=>'Nota de crédito cargada manualmente.', 'badge'=>'primary'],
        ],
    ];

    // ==========================================================
    // MÉTODOS DE UTILIDAD (LABELS, ICONOS, ETC.)
    // ==========================================================
    public static function label($module, $id)
    {
        return static::$maps[$module][$id]['label'] ?? 'Desconocido';
    }

    public static function badge_class($module, $id)
    {
        return static::$maps[$module][$id]['badge'] ?? 'light';
    }

    public static function icon($module, $id)
    {
        return static::$maps[$module][$id]['icon'] ?? 'fas fa-question-circle';
    }

    public static function tooltip($module, $id)
    {
        return static::$maps[$module][$id]['tooltip'] ?? '';
    }

    public static function options($module)
    {
        $opts = [];
        if (!isset(static::$maps[$module])) return $opts;
        foreach (static::$maps[$module] as $id => $meta) {
            $opts[$id] = $meta['label'];
        }
        return $opts;
    }

    public static function render_status($module, $id, $with_icon = true)
    {
        if (!isset(static::$maps[$module][$id])) {
            return '<span class="badge badge-light">Desconocido</span>';
        }
        $meta = static::$maps[$module][$id];
        $icon_html = $with_icon ? "<i class=\"{$meta['icon']} mr-1\"></i>" : '';
        return "<span class=\"badge badge-{$meta['badge']}\" data-toggle=\"tooltip\" title=\"{$meta['tooltip']}\">{$icon_html}{$meta['label']}</span>";
    }

    // ==========================================================
    // REGLAS DE SINCRONIZACIÓN DE ESTATUS
    // ==========================================================
    protected static $status_rules = [

        // ===== FACTURAS =====
        'bill' => [
            3 => ['order'=>1], // Factura autorizada → orden abierta
            7 => ['order'=>7], // Factura en contrarecibo → orden en contrarecibo
            8 => ['order'=>8], // Factura en pago → orden en pago
            9 => ['order'=>9], // Factura con REP → orden pagada
            5 => ['order'=>9], // Factura pagada → orden pagada
            99=>['order'=>4],  // Cancelada → orden cancelada
        ],

        // ===== CONTRARECIBOS =====
        'receipt' => [
            0 => ['bill'=>7, 'order'=>7],  // Pendiente → factura y orden en contrarecibo
            1 => ['bill'=>8, 'order'=>8],  // Autorizado → en pago
            2 => ['bill'=>9, 'order'=>9],  // Pagado → cerrado
            3 => ['bill'=>99, 'order'=>4], // Cancelado → ambos cancelados
            5 => ['bill'=>8, 'order'=>8],  // En pago → en proceso
        ],

        // ===== REP =====
        'rep' => [
            2 => ['bill'=>9, 'order'=>9],  // REP aplicado → factura y orden pagadas
            4 => ['bill'=>6, 'order'=>2],  // REP parcial → factura parcial, orden parcial
            3 => ['bill'=>99],             // REP cancelado → factura cancelada
        ],

        // ===== NOTAS DE CRÉDITO =====
        'note' => [
            1 => ['bill'=>5], // Aplicada → factura pagada
            2 => ['bill'=>3], // Cancelada → factura autorizada
            3 => ['bill'=>3], // Rechazada → factura autorizada
        ],
    ];

    // ==========================================================
    // SINCRONIZACIÓN ENTRE DOCUMENTOS
    // ==========================================================
    public static function sync_status($type, $id, $new_status)
    {
        \Log::info("[PURCHASES][SYNC] {$type}={$id} → status={$new_status}");

        if (!isset(self::$status_rules[$type][$new_status])) {
            \Log::debug("[SYNC] No hay reglas para {$type}={$new_status}");
            return;
        }

        $rules = self::$status_rules[$type][$new_status];

        // === FACTURA ===
        if ($type === 'bill') {
            $bill = Model_Providers_Bill::find($id);
            if (!$bill) return;
            foreach ($rules as $target => $status) {
                if ($target === 'order' && $bill->order_id) {
                    $order = Model_Providers_Order::find($bill->order_id);
                    if ($order) {
                        $order->status = $status;
                        $order->save();
                        \Log::info("[SYNC] Orden {$order->id} → {$status}");
                    }
                }
            }
        }

        // === CONTRARECIBO ===
        elseif ($type === 'receipt') {
            $receipt = Model_Providers_Receipt::find($id);
            if (!$receipt) return;
            foreach ($receipt->details as $detail) {
                $bill = Model_Providers_Bill::find($detail->bill_id);
                if ($bill) {
                    if (isset($rules['bill'])) {
                        $bill->status = $rules['bill'];
                        $bill->save();
                        \Log::info("[SYNC] Factura {$bill->id} → {$rules['bill']}");
                    }
                    if (isset($rules['order']) && $bill->order_id) {
                        $order = Model_Providers_Order::find($bill->order_id);
                        if ($order) {
                            $order->status = $rules['order'];
                            $order->save();
                            \Log::info("[SYNC] Orden {$order->id} → {$rules['order']}");
                        }
                    }
                }
            }
        }

        // === REP ===
        elseif ($type === 'rep') {
            $rep = Model_Providers_Bill_Rep::find($id);
            if (!$rep) return;
            if ($rep->bill_id) {
                $bill = Model_Providers_Bill::find($rep->bill_id);
                if ($bill && isset($rules['bill'])) {
                    $bill->status = $rules['bill'];
                    $bill->save();
                    \Log::info("[SYNC] Factura {$bill->id} → {$rules['bill']}");
                }
                if (isset($rules['order']) && $bill->order_id) {
                    $order = Model_Providers_Order::find($bill->order_id);
                    if ($order) {
                        $order->status = $rules['order'];
                        $order->save();
                        \Log::info("[SYNC] Orden {$order->id} → {$rules['order']}");
                    }
                }
            }
        }

        // === NOTAS ===
        elseif ($type === 'note') {
            $note = Model_Providers_Creditnote::find($id);
            if (!$note) return;
            foreach ($note->bills as $bill) {
                $bill->status = $rules['bill'];
                $bill->save();
                \Log::info("[SYNC] Nota {$note->id} → Factura {$bill->id} → {$rules['bill']}");
            }
        }

        \Log::info("[PURCHASES][SYNC] Completado {$type}={$id}");
    }

    // ==========================================================
    // FUNCIONES AUXILIARES
    // ==========================================================
    public static function status_code($module, $label)
    {
        if (!isset(self::$maps[$module])) return null;
        foreach (self::$maps[$module] as $id => $meta) {
            if (strcasecmp($meta['label'], $label) === 0) return $id;
        }
        return null;
    }

    public static function can_generate_receipt($order_status)
    {
        $permitidos = [1, 2]; // Abierta o parcial
        return in_array($order_status, $permitidos);
    }

    public static function sum_bills($order_id)
    {
        $bills = Model_Providers_Bill::query()
            ->where('order_id', $order_id)
            ->where('status', 'in', [2, 3, 7, 8])
            ->get();

        $total = 0;
        foreach ($bills as $bill) {
            $total += (float) $bill->total;
        }
        return $total;
    }

    public static function can_create_receipt_for_order($order)
    {
        if (!$order) return false;
        if (!self::can_generate_receipt($order->status)) return false;
        $total_facturas = self::sum_bills($order->id);
        return $total_facturas > 0;
    }

    // ==========================================================
    // OBTENER LISTA DE ESTATUS POR MÓDULO
    // ==========================================================
    /**
     * Devuelve un arreglo simple [id => label] de todos los estatus
     * definidos para un módulo específico dentro del mapa $maps.
     *
     * USO:
     * ----
     * $list = Helper_Purchases::status_list('order');
     *
     * Retorna:
     * [
     *   0 => 'Por autorizar',
     *   1 => 'Abierta',
     *   2 => 'Parcial',
     *   ...
     * ]
     *
     * Si el módulo no existe, devuelve un arreglo vacío.
     */
    public static function status_list($module)
    {
        // Validar existencia del módulo
        if (!isset(static::$maps[$module]) || !is_array(static::$maps[$module])) {
            \Log::debug("[PURCHASES][STATUS_LIST] Módulo no encontrado: {$module}");
            return [];
        }

        // Extraer solo id => label
        $list = [];
        foreach (static::$maps[$module] as $id => $meta) {
            if (isset($meta['label'])) {
                $list[$id] = $meta['label'];
            }
        }

        // Log opcional para depuración
        \Log::debug("[PURCHASES][STATUS_LIST] {$module}: " . json_encode($list));

        return $list;
    }


    // ==========================================================
    // ==========================================================
    // INSIGNIAS VISUALES DE PRODUCTOS
    // ==========================================================
    /**
     * RENDERIZA LAS INSIGNIAS DE ESTADO DE PRODUCTOS SEGÚN SU CONTEXTO
     * 
     * CONTEXTO DISPONIBLE:
     *  - Público (sitio principal)
     *  - Socios (portal de negocio)
     *  - Admin (panel administrativo)
     * 
     * PRIORIDAD: SOON > NEW > OUT
     * SOLO UNA INSIGNIA DEBE ESTAR ACTIVA A LA VEZ.
     * 
     * CAMPOS ESPERADOS:
     *  - soon (int) → 1 si está próximo a lanzamiento
     *  - newproduct (int) → 1 si es nuevo
     *  - temporarily_sold_out (int) → 1 si está agotado temporalmente
     * 
     * Los estilos se manejan vía CSS (.badge-wrapper, .badge-label, etc.)
     * con variantes específicas por área:
     *  - .public-products
     *  - .socios-catalogo
     *  - .admin-products
     */
    // ==========================================================
    // INSIGNIAS VISUALES DE PRODUCTOS (CON LOGS)
    // ==========================================================
    public static function render_product_badge($p, $context = 'public')
    {
        // Si es objeto ORM → convertir a arreglo
        if (is_object($p)) {
            $p = [
                'id' => $p->id ?? null,
                'soon' => (int)$p->soon,
                'newproduct' => (int)$p->newproduct,
                'temporarily_sold_out' => (int)$p->temporarily_sold_out,
            ];
        }

        // LOG DE DATOS ENTRANTES
        \Log::debug("[BADGE] Producto ID={$p['id']} | soon={$p['soon']} | new={$p['newproduct']} | out={$p['temporarily_sold_out']} | contexto={$context}");

        $badge_html = '';

        // === PÚBLICO GENERAL ===
        if ($context === 'public') {
            if ($p['soon']) {
                $badge_html = '<div class="badge-wrapper"><span class="badge-label badge-soon">PRÓXIMAMENTE</span></div>';
            } elseif ($p['newproduct']) {
                $badge_html = '<div class="badge-wrapper"><span class="badge-label badge-new">¡PRODUCTO NUEVO!</span></div>';
            } elseif ($p['temporarily_sold_out']) {
                $badge_html = '<div class="badge-wrapper"><span class="badge-label badge-out">AGOTADO TEMPORALMENTE</span></div>';
            }
        }

        // === SOCIOS ===
        if ($context === 'partner') {
            if ($p['soon']) {
                $badge_html = '<div class="badge-wrapper badge-partner"><span class="badge-label badge-soon">PRÓXIMAMENTE</span></div>';
            } elseif ($p['newproduct']) {
                $badge_html = '<div class="badge-wrapper badge-partner"><span class="badge-label badge-new">¡NUEVO!</span></div>';
            } elseif ($p['temporarily_sold_out']) {
                $badge_html = '<div class="badge-wrapper badge-partner"><span class="badge-label badge-out">AGOTADO</span></div>';
            }
        }

        // === ADMIN ===
        if ($context === 'admin') {
            if ($p['soon']) {
                $badge_html = '<div class="badge-wrapper badge-admin"><span class="badge-label badge-soon"><i class="fas fa-hourglass-half mr-1"></i>PRÓXIMAMENTE</span></div>';
            } elseif ($p['newproduct']) {
                $badge_html = '<div class="badge-wrapper badge-admin"><span class="badge-label badge-new"><i class="fas fa-star mr-1"></i>NUEVO</span></div>';
            } elseif ($p['temporarily_sold_out']) {
                $badge_html = '<div class="badge-wrapper badge-admin"><span class="badge-label badge-out"><i class="fas fa-box-open mr-1"></i>AGOTADO</span></div>';
            }
        }

        // LOG DEL RESULTADO
        if ($badge_html === '') {
            \Log::debug("[BADGE] Producto ID={$p['id']} → sin badge mostrado");
        } else {
            \Log::debug("[BADGE] Producto ID={$p['id']} → badge renderizado correctamente");
        }

        return $badge_html;
    }







    }
    // ==========================================================
    // DOCUMENTACIÓN INTERNA: MATRIZ DE DEPENDENCIAS DE ESTATUS
    // ==========================================================
    /**
     * TABLA DE SINCRONIZACIÓN ENTRE DOCUMENTOS DE COMPRAS
     * 
     * OBJETIVO:
     * Mantener coherencia entre los estados de:
     *  - ORDEN DE COMPRA
     *  - FACTURA
     *  - CONTRARECIBO
     *  - REP
     *  - NOTA DE CRÉDITO
     * 
     * CADA CAMBIO DE ESTATUS EN UN DOCUMENTO PUEDE IMPACTAR
     * DIRECTAMENTE EN OTROS MÓDULOS DEL FLUJO DE COMPRAS.
     * 
     * -------------------------------------------------------------------
     * |  DOCUMENTO BASE  |  ESTATUS ACTUAL                | IMPACTO EN OTROS MÓDULOS |
     * -------------------------------------------------------------------
     * | FACTURA          |  Autorizada (3)               |  Orden pasa a ABIERTA (1)                          |
     * | FACTURA          |  En contrarecibo (7)          |  Orden pasa a EN CONTRARECIBO (7)                  |
     * | FACTURA          |  En pago (8)                  |  Orden pasa a EN PAGO (8)                          |
     * | FACTURA          |  Pagada / REP aplicado (5,9)  |  Orden pasa a PAGADA (9)                           |
     * | FACTURA          |  Cancelada (99)               |  Orden pasa a CANCELADA (4)                        |
     * -------------------------------------------------------------------
     * | CONTRARECIBO     |  Pendiente (0)                |  Factura → En contrarecibo (7), Orden → En contrarecibo (7)  |
     * | CONTRARECIBO     |  Autorizado (1)               |  Factura → En pago (8),        Orden → En pago (8)          |
     * | CONTRARECIBO     |  En pago (5)                  |  Factura → En pago (8),        Orden → En pago (8)          |
     * | CONTRARECIBO     |  Pagado (2)                   |  Factura → Pagada (9),         Orden → Pagada (9)           |
     * | CONTRARECIBO     |  Cancelado (3)                |  Factura → Cancelada (99),     Orden → Cancelada (4)        |
     * -------------------------------------------------------------------
     * | REP              |  Aplicado (2)                 |  Factura → REP aplicado (9),   Orden → Pagada (9)           |
     * | REP              |  Parcial (4)                  |  Factura → Parcial (6),        Orden → Parcial (2)          |
     * | REP              |  Cancelado (3)                |  Factura → Cancelada (99)                               |
     * -------------------------------------------------------------------
     * | NOTA DE CRÉDITO  |  Aplicada (1)                 |  Factura → Pagada (5)                                  |
     * | NOTA DE CRÉDITO  |  Cancelada / Rechazada (2,3)  |  Factura → Autorizada (3)                               |
     * -------------------------------------------------------------------
     * | ORDEN            |  Abierta (1)                  |  Puede recibir facturas nuevas                         |
     * | ORDEN            |  Parcial (2)                  |  Facturación incompleta o con REP parcial              |
     * | ORDEN            |  En contrarecibo (7)          |  Facturas vinculadas en contrarecibo                   |
     * | ORDEN            |  En pago (8)                  |  Contrarecibo autorizado o en proceso de pago          |
     * | ORDEN            |  Pagada (9)                   |  Flujo cerrado: pago completado                        |
     * -------------------------------------------------------------------
     * 
     * REFERENCIAS DE MÓDULOS INVOLUCRADOS:
     * ------------------------------------
     * Model_Providers_Order
     * Model_Providers_Bill
     * Model_Providers_Receipt
     * Model_Providers_Receipts_Details
     * Model_Providers_Bill_Rep
     * Model_Providers_Creditnote
     * 
     * CONTROLADORES RELACIONADOS:
     * ----------------------------
     * - admin/compras/ordenes.php
     * - admin/compras/facturas.php
     * - admin/compras/contrarecibos.php
     * - admin/compras/rep.php
     * - admin/compras/notas.php
     * 
     * HISTORIA DE CAMBIOS:
     * ---------------------
     * v1.0  (2025-10-08)  Integración extendida con flujos de REP y contrarecibo.
     * v1.1  (2025-10-08)  Se agregan estados intermedios (En pago, En contrarecibo, Parcial).
     * v1.2  (2025-10-08)  Sincronización automática en Helper_Purchases::sync_status()
     * v1.3  (2025-10-08)  Documentación completa agregada al final del helper.
     */
