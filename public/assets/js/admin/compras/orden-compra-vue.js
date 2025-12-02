/**
 * Orden de Compra – Frontend (Vue)
 *
 * Propósito
 * ---------
 * Capturar y editar Órdenes de Compra con partidas, usando endpoints AJAX.
 *
 * Endpoints (backend)
 * -------------------
 * - catalogos_compras (POST): catálogos + defaults
 * - guardar_ajax (POST): crea OC
 * - editar_ajax  (POST): actualiza OC
 * - obtener_oc   (GET) : trae OC para edición (ya lo tienes)
 *
 * Seguridad
 * ---------
 * Requiere enviar access_id y access_token en cada POST (están en window.*).
 *
 * Campos clave
 * ------------
 * proveedor_id, codigo_oc, fecha(YYYY-MM-DD), moneda(id), notas?
 * partidas[]: { tipo, product_id?, code_product?, description, quantity, unit_price, tax_id?, retention_id? }
 *
 * Cálculo
 * -------
 * subtotal = Σ(quantity * unit_price)
 * impuesto = Σ(subtotal_parcial * tasaImpuesto)
 * retención = Σ(subtotal_parcial * tasaRetención)
 * total = subtotal + impuesto - retención
 */

/* ============================================================
 * ORDEN DE COMPRA – FRONTEND (Vue)
 * ============================================================
 * Flujo estándar FuelPHP / Vue:
 *  - Captura y edición de órdenes
 *  - Soporta prefill de proveedor desde admin/proveedores/info
 *  - Carga catálogos (productos, impuestos, monedas, proveedores, tipos doc)
 * ============================================================ */

if (document.getElementById('orden-compra-app')) {
    new Vue({
        el: '#orden-compra-app',

        data: {
            // === PROVEEDOR ===
            proveedor_id: '',
            proveedor_nombre: '',
            proveedorBloqueado: false,
            providers_opts: [],        // Catálogo de proveedores desde catalogos_compras
            filteredProviders: [],     // Resultado de búsqueda local

            // === CAMPOS NUEVOS ===
            document_type_id: '',       // Tipo de documento
            document_type_opts: [],     // Catálogo tipos de documento

            // === COLUMNAS VISIBLES ===
            columnasVisibles: {
                tipo: { label: 'Tipo', visible: true },
                producto: { label: 'Producto / Código', visible: true },
                descripcion: { label: 'Descripción', visible: true },
                cantidad: { label: 'Cantidad', visible: true },
                precio: { label: 'Precio', visible: true },
                cuenta: { label: 'Cuenta Contable', visible: false },
                impuesto: { label: 'Impuesto', visible: true },
                retencion: { label: 'Retención', visible: false }
            },

            // === CATÁLOGOS ===
            all_products: [],
            currency_opts: [],
            tax_opts: [],
            retention_opts: [],
            type_opts: [],
            tipo_general: 'servicio',
            codigo_producto_tipo: 'interno',
            products_internal: [],
            products_order: [],
            productos_filtrados: [],

            // === DEFAULTS ===
            default_currency_id: '',
            default_tax_id: '',
            default_type_id: '',

            // === CUENTAS ===
            cuentas_opts: [],
            sugerencias: [],
            focusedIndex: null,

            // === VARIABLES DE CABECERA ===
            codigo_oc: '',
            fecha: '',
            fecha_pago: '',
            moneda: '',
            notas: '',

            // === GENERALES ===
            tax_general: '',
            retention_general: '',
            tipo_general: '',

            // === PARTIDAS ===
            partidas: [{
                tipo: '',
                product_id: '',
                code_product: '',
                description: '',
                quantity: 1,
                unit_price: 0,
                tax_id: '',
                retention_id: ''
            }],

            // === TOKENS ===
            access_id: window.access_id || '',
            access_token: window.access_token || '',

            // === CONTROL UI ===
            editarCodigoOc: false,
            loading: true,
            load_error: false,
            modoEdicion: false,
            ordenId: null
        },

        mounted() {
            const urlBase = document.getElementById('url-location').dataset.url;

            // --- Prefill proveedor (desde admin/proveedores/info) ---
            if (window.prefillProviderId && window.prefillProviderId > 0) {
                this.proveedor_id = window.prefillProviderId;
                this.proveedor_nombre = window.prefillProviderName;
                this.proveedorBloqueado = true;
            }

            // --- Cargar catálogos principales ---
            axios.post(urlBase + 'catalogos_compras', {
                access_id: this.access_id,
                access_token: this.access_token
            }, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(resp => {
                    // Catálogos
                    this.all_products = resp.data.all_products;
                    this.currency_opts = resp.data.currency_opts;
                    this.tax_opts = resp.data.tax_opts;
                    this.retention_opts = resp.data.retention_opts;
                    this.type_opts = resp.data.type_opts;
                    this.document_type_opts = resp.data.document_type_opts || [];
                    this.providers_opts = resp.data.providers_opts || [];

                    // Defaults
                    this.default_currency_id = resp.data.default_currency_id || '';
                    this.default_tax_id = resp.data.default_tax_id || '';
                    this.default_type_id = resp.data.default_type_id || 'articulo';

                    // Iniciales
                    this.moneda = this.default_currency_id;
                    this.tax_general = this.default_tax_id;
                    this.tipo_general = this.default_type_id;
                    this.tipo_general = 'servicio';
                    this.catalogoActivo = false;

                    // Código automático
                    const el = document.getElementById('oc-defaults');
                    if (el && el.dataset.nextCode) this.codigo_oc = el.dataset.nextCode;

                    // Fecha automática (YYYY-MM-DD)
                    this.fecha = new Date().toISOString().slice(0, 10);

                    this.products_internal = resp.data.products_internal || [];
                    this.products_order = resp.data.products_order || [];
                    this._actualizarProductos();


                    // Partida inicial
                    this.partidas = [{
                        tipo: this.tipo_general,
                        product_id: '',
                        code_product: '',
                        description: '',
                        quantity: 1,
                        unit_price: 0,
                        tax_id: this.tax_general,
                        retention_id: ''
                    }];

                    this.loading = false;
                })
                .catch(() => {
                    this.load_error = true;
                    this.loading = false;
                });
        },

        watch: {
            tax_general(n) { this.partidas.forEach(i => i.tax_id = n); },
            retention_general(n) { this.partidas.forEach(i => i.retention_id = n); },
            tipo_general(n) { this.partidas.forEach(i => i.tipo = n); },
            codigo_producto_tipo() {
                this._actualizarProductos();
            },

        },

        methods: {
            // ======================================
            // PROVEEDORES
            // ======================================
            filtrarProveedores() {
                const term = (this.proveedor_nombre || '').toLowerCase().trim();
                if (term.length < 2) {
                    this.filteredProviders = [];
                    return;
                }
                this.filteredProviders = this.providers_opts.filter(p =>
                    (p.name && p.name.toLowerCase().includes(term)) ||
                    (p.code && p.code.toLowerCase().includes(term))
                );
            },

            seleccionarProveedor(prov) {
                this.proveedor_id = prov.id;
                this.proveedor_nombre = prov.name;
                this.filteredProviders = [];
            },

            tipo_general(nuevo) {
                // Si cambia tipo general, actualiza todas las partidas
                this.partidas.forEach(i => i.tipo = nuevo);

                // Si cambia a artículo, activa catálogo
                if (nuevo === 'articulo') {
                    this.catalogoActivo = true;
                } else {
                    this.catalogoActivo = false;
                }
            },

            // ======================================
            // CÓDIGO DOCUMENTO
            // ======================================
            toggleCodigoOc() {
                this.editarCodigoOc = !this.editarCodigoOc;
                if (!this.editarCodigoOc && (!this.codigo_oc || this.codigo_oc.trim() === '')) {
                    const el = document.getElementById('oc-defaults');
                    if (el && el.dataset.nextCode) this.codigo_oc = el.dataset.nextCode;
                }
            },

            _actualizarProductos() {
                this.productos_filtrados =
                    this.codigo_producto_tipo === 'proveedor'
                        ? this.products_order
                        : this.products_internal;
            },


            // ======================================
            // PARTIDAS
            // ======================================
            agregarPartida() {
                this.partidas.push({
                    tipo: this.tipo_general,
                    product_id: '',
                    code_product: '',
                    description: '',
                    quantity: 1,
                    unit_price: 0,
                    tax_id: this.tax_general,
                    retention_id: this.retention_general
                });
            },

            eliminarPartida(idx) {
                if (this.partidas.length > 1) this.partidas.splice(idx, 1);
            },

            // ======================================
            // GUARDAR ORDEN
            // ======================================
            guardarYVer() { this._guardarOrden('ver'); },
            guardarYNuevo() { this._guardarOrden('nuevo'); },
            guardarYCerrar() { this._guardarOrden('cerrar'); },
            cancelar() { this._limpiarFormulario(); },

            _guardarOrden(modo) {
                if (!this.proveedor_id || !this.codigo_oc || !this.fecha || !this.moneda || !this.partidas.length) {
                    Swal.fire('Completa todos los campos obligatorios', '', 'warning');
                    return;
                }

                const datos = {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    proveedor_id: this.proveedor_id,
                    codigo_oc: this.codigo_oc,
                    fecha: this.fecha,
                    moneda: this.moneda,
                    notas: this.notas,
                    document_type_id: this.document_type_id,
                    tax_id: this.tax_general,
                    retention_id: this.retention_general,
                    partidas: this.partidas
                };

                const urlBase = document.getElementById('url-location').dataset.url;
                this.loading = true;

                axios.post(urlBase + 'guardar_ajax', datos, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(resp => {
                        if (resp.data.success) {
                            const id = resp.data.id;
                            if (modo === 'ver') {
                                Swal.fire('¡Orden guardada!', 'Redirigiendo...', 'success');
                                window.location.href = urlBase.replace('ajax/', '') + 'compras/ordenes/editar/' + id;
                            } else if (modo === 'nuevo') {
                                this._limpiarFormulario();
                                Swal.fire('¡Orden guardada!', 'Puedes capturar una nueva orden.', 'success');
                            } else if (modo === 'cerrar') {
                                Swal.fire('¡Orden guardada!', '', 'success');
                                window.location.href = urlBase.replace('ajax/', '') + 'compras/ordenes';
                            }
                        } else {
                            Swal.fire('Error', resp.data.error || 'No se pudo guardar', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Ocurrió un problema inesperado', 'error'))
                    .finally(() => this.loading = false);
            },

            _limpiarFormulario() {
                this.proveedor_id = '';
                this.proveedor_nombre = '';
                this.codigo_oc = '';
                this.fecha = new Date().toISOString().slice(0, 10);
                this.moneda = this.default_currency_id;
                this.tax_general = this.default_tax_id;
                this.retention_general = '';
                this.tipo_general = this.default_type_id;
                this.document_type_id = '';
                this.notas = '';
                this.partidas = [{
                    tipo: this.tipo_general,
                    product_id: '',
                    code_product: '',
                    description: '',
                    quantity: 1,
                    unit_price: 0,
                    tax_id: this.tax_general,
                    retention_id: this.retention_general
                }];
            }
        },

        // ======================================
        // CÁLCULOS
        // ======================================
        computed: {
            subtotal() {
                return this.partidas.reduce((s, i) =>
                    s + ((+i.quantity || 0) * (+i.unit_price || 0)), 0);
            },
            impuestoTotal() {
                return this.partidas.reduce((s, i) => {
                    const sub = (+i.quantity || 0) * (+i.unit_price || 0);
                    const t = this.tax_opts.find(x => x.id == i.tax_id);
                    const rate = t ? +t.rate : 0;
                    return s + (sub * rate);
                }, 0);
            },
            retencionTotal() {
                return this.partidas.reduce((s, i) => {
                    const sub = (+i.quantity || 0) * (+i.unit_price || 0);
                    const r = this.retention_opts.find(x => x.id == i.retention_id);
                    const rate = r ? +r.rate : 0;
                    return s + (sub * rate);
                }, 0);
            },
            total() {
                return this.subtotal + this.impuestoTotal - this.retencionTotal;
            }
        },

        filters: {
            currency(v) {
                v = Number(v) || 0;
                return '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 2 });
            }
        }
    });
}


/* ===== Editar OC ===== */
/* ===== Editar OC ===== */
if (document.getElementById('orden-compra-editar-app')) {
    new Vue({
        el: '#orden-compra-editar-app',
        data: {
            /** Tokens de seguridad */
            access_id: window.access_id || '',
            access_token: window.access_token || '',

            /** Estados UI */
            loading: true,
            load_error: false,

            /** Cabecera OC */
            proveedor_id: window.proveedor_id || '',
            proveedor_nombre: window.proveedor_nombre || '',
            proveedor_rfc: window.proveedor_rfc || '',
            codigo_oc: '',
            fecha: '',
            moneda: '',
            notas: '',

            /** Parámetros generales */
            tax_general: '',
            retention_general: '',
            tipo_general: 'articulo',

            /** Partidas */
            agregarPartida:[],
            partidas: [],

            /** Catálogos */
            all_products: [],
            currency_opts: [],
            tax_opts: [],
            retention_opts: [],
            type_opts: [],

            /** Control interno */
            ordenId: window.order_id || null,
            default_currency_id: '',
            default_tax_id: '',
            default_type_id: 'articulo'
        },

        /** Carga catálogos y la OC al iniciar */
        mounted() {
            const urlBase = document.getElementById('url-location').dataset.url;
            Promise.all([
                axios.post(urlBase + 'catalogos_compras', {
                    access_id: this.access_id,
                    access_token: this.access_token
                }, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }),
                axios.get(urlBase + 'obtener_oc?id=' + this.ordenId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
            ])
                .then(([cats, oc]) => {
                    // Catálogos
                    this.all_products = cats.data.all_products;
                    this.currency_opts = cats.data.currency_opts;
                    this.tax_opts = cats.data.tax_opts;
                    this.retention_opts = cats.data.retention_opts;
                    this.type_opts = cats.data.type_opts;

                    // Defaults
                    this.default_currency_id = cats.data.default_currency_id || '';
                    this.default_tax_id = cats.data.default_tax_id || '';
                    this.default_type_id = cats.data.default_type_id || 'articulo';

                    // Datos OC
                    this.proveedor_id = oc.data.proveedor_id;
                    this.codigo_oc = oc.data.codigo_oc;
                    this.fecha = oc.data.fecha;
                    this.moneda = oc.data.moneda;
                    this.notas = oc.data.notas;
                    this.tax_general = oc.data.tax_general || this.default_tax_id;
                    this.retention_general = oc.data.retention_general || '';
                    this.tipo_general = oc.data.tipo_general || this.default_type_id;
                    this.partidas = oc.data.partidas ? JSON.parse(JSON.stringify(oc.data.partidas)) : [];

                    this.loading = false;
                })
                .catch(() => { this.load_error = true; this.loading = false; });
        },

        methods: {
            /** Botones de acción */
            guardarYVer() { this.guardarOrden('ver'); },
            guardarYCerrar() { this.guardarOrden('cerrar'); },
            cancelar() {
                const urlBase = document.getElementById('url-location').dataset.url;
                window.location.href = urlBase.replace('ajax/', '') + 'compras/ordenes/info/' + this.ordenId;
            },

            // =============== MÉTODOS QUE FALTABAN ===============
            aplicarGeneral(tipo) {
                if (tipo === 'tax') this.partidas.forEach(p => p.tax_id = this.tax_general);
                if (tipo === 'ret') this.partidas.forEach(p => p.retention_id = this.retention_general);
                if (tipo === 'type') this.partidas.forEach(p => p.tipo = this.tipo_general);
            },
            agregarPartida() {
                this.partidas.push({
                    tipo: this.tipo_general,
                    product_id: '',
                    code_product: '',
                    description: '',
                    quantity: 1,
                    unit_price: 0,
                    tax_id: this.tax_general,
                    retention_id: this.retention_general,
                    accounts_chart_id: ''
                });
            },
            eliminarPartida(idx) {
                if (this.partidas.length > 1) this.partidas.splice(idx, 1);
            },
            autocompletarDescripcion(idx) {
                const val = (this.partidas[idx].code_product || '').toLowerCase().trim();
                const hit = this.all_products.find(p => {
                    const combo = (p.code + ' - ' + p.name).toLowerCase();
                    return combo === val || p.code.toLowerCase() === val || p.name.toLowerCase() === val;
                });
                if (hit) {
                    this.partidas[idx].description = hit.name;
                    this.partidas[idx].product_id = hit.id;
                    this.partidas[idx].code_product = hit.code;
                    // opcional: this.partidas[idx].unit_price = hit.price || this.partidas[idx].unit_price;
                }
            },
            /**
             * Autocompletar descripción desde catálogo
             */
            autocompletarDescripcion(event, idx) {
                const valor = event.target.value || '';
                let prod = this.all_products.find(p => p.code === valor || p.name === valor);
                if (prod) {
                    this.partidas[idx].description = prod.name;
                    this.partidas[idx].product_id = prod.id;
                    this.partidas[idx].code_product = prod.code;
                }
            },

            // =============== GUARDAR EDICIÓN ===============
            /**
             * Guardar cambios de la OC
             * @param {'ver'|'cerrar'} modo Qué hacer tras guardar
             */
            guardarOrden(modo) {
                if (!this.proveedor_id || !this.codigo_oc || !this.fecha || !this.moneda || this.partidas.length === 0) {
                    Swal.fire('Completa todos los campos obligatorios', '', 'warning');
                    return;
                }

                const datos = {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    id: this.ordenId,
                    proveedor_id: this.proveedor_id,
                    codigo_oc: this.codigo_oc,
                    fecha: this.fecha,
                    moneda: this.moneda,
                    notas: this.notas,
                    tax_id: this.tax_general,
                    retention_id: this.retention_general,
                    partidas: this.partidas
                };

                const urlBase = document.getElementById('url-location').dataset.url;
                this.loading = true;

                axios.post(urlBase + 'editar_ajax', datos, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(resp => {
                        if (resp.data.success) {
                            if (modo === 'ver') {
                                Swal.fire('¡Orden actualizada!', 'Los cambios fueron guardados.', 'success');
                            } else {
                                Swal.fire('¡Orden actualizada!', '', 'success');
                                window.location.href = urlBase.replace('ajax/', '') + 'compras/ordenes';
                            }
                        } else {
                            Swal.fire('Error', resp.data.error || 'No se pudo guardar', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Ocurrió un problema inesperado', 'error'))
                    .finally(() => this.loading = false);
            }
        },

        computed: {
            subtotal() {
                return this.partidas.reduce((s, i) =>
                    s + (parseFloat(i.quantity || 0).toFixed(2) * parseFloat(i.unit_price || 0).toFixed(2)), 0);
            },

            impuestoTotal() {
                return this.partidas.reduce((s, i) => {
                    const sub = (+i.quantity || 0) * (+i.unit_price || 0);
                    const t = this.tax_opts.find(x => x.id == i.tax_id);
                    const rate = t ? +t.rate : 0;
                    return s + (sub * rate);
                }, 0);
            },
            retencionTotal() {
                return this.partidas.reduce((s, i) => {
                    const sub = (+i.quantity || 0) * (+i.unit_price || 0);
                    const r = this.retention_opts.find(x => x.id == i.retention_id);
                    const rate = r ? +r.rate : 0;
                    return s + (sub * rate);
                }, 0);
            },
            total() {
                return this.subtotal + this.impuestoTotal - this.retencionTotal;
            }
        },

        filters: {
            currency(v) {
                v = Number(v) || 0;
                return '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 2 });
            }
        }
    });
}




/* ============================================================
 * ORDEN DE COMPRA – AUTORIZAR (REVISIÓN TOTAL)
 * ============================================================ */
if (document.getElementById('orden-compra-autorizar-app')) {
    new Vue({
        el: '#orden-compra-autorizar-app',
        data: {
            // Tokens
            access_id: window.access_id || '',
            access_token: window.access_token || '',

            // Estado UI
            loading: true,
            load_error: false,

            // Cabecera
            proveedor_id: window.prefillProviderId || '',
            proveedor_nombre: window.prefillProviderName || '',
            codigo_oc: '',
            fecha: '',
            fecha_pago: '',
            moneda: '',
            notas: '',
            document_type_id: '',
            codigo_producto_tipo: 'proveedor', // en autorizar hace sentido dejar proveedor por default

            // Generales
            tax_general: '',
            retention_general: '',
            tipo_general: 'servicio', // como pediste: servicio como default

            // Partidas
            partidas: [],

            // Catálogos
            all_products: [],
            productos_filtrados: [],
            currency_opts: [],
            tax_opts: [],
            retention_opts: [],
            type_opts: [],
            document_type_opts: [],
            providers_opts: [],

            // Columnas
            columnasVisibles: {
                tipo: { label: 'Tipo', visible: true },
                producto: { label: 'Producto / Código', visible: true },
                descripcion: { label: 'Descripción', visible: true },
                cantidad: { label: 'Cantidad', visible: true },
                precio: { label: 'Precio', visible: true },
                cuenta: { label: 'Cuenta Contable', visible: true },
                impuesto: { label: 'Impuesto', visible: true },
                retencion: { label: 'Retención', visible: true }
            },

            // Cuentas contables
            sugerencias: [],
            focusedIndex: null,

            // Proveedores buscador
            filteredProviders: [],

            // Control interno
            ordenId: window.order_id || null
        },

        mounted() {
            const urlBase = document.getElementById('url-location').dataset.url;

            Promise.all([
                axios.post(urlBase + 'catalogos_compras', {
                    access_id: this.access_id,
                    access_token: this.access_token
                }, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }),
                axios.get(urlBase + 'obtener_oc?id=' + this.ordenId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
            ])
                .then(([cats, oc]) => {
                    // Catálogos
                    this.all_products = cats.data.all_products || [];
                    this.currency_opts = cats.data.currency_opts || [];
                    this.tax_opts = cats.data.tax_opts || [];
                    this.retention_opts = cats.data.retention_opts || [];
                    this.type_opts = cats.data.type_opts || [];
                    this.document_type_opts = cats.data.document_type_opts || [];
                    this.providers_opts = cats.data.providers_opts || [];

                    // Defaults
                    const default_currency_id = cats.data.default_currency_id || '';
                    const default_tax_id = cats.data.default_tax_id || '';
                    const default_type_id = cats.data.default_type_id || 'servicio';

                    // Datos OC (ajusta keys a tu JSON actual de obtener_oc)
                    const d = oc.data || {};
                    this.proveedor_id = d.proveedor_id || this.proveedor_id;
                    this.proveedor_nombre = d.proveedor_nombre || this.proveedor_nombre;
                    this.codigo_oc = d.codigo_oc || d.code_order || '';
                    this.fecha = d.fecha || d.date || '';
                    this.fecha_pago = d.fecha_pago || d.pay_date || '';
                    this.moneda = d.moneda || d.currency_id || default_currency_id;
                    this.notas = d.notas || d.notes || '';
                    this.document_type_id = d.document_type_id || '';
                    this.codigo_producto_tipo = d.codigo_producto_tipo || 'proveedor';

                    this.tax_general = d.tax_general || default_tax_id;
                    this.retention_general = d.retention_general || '';
                    this.tipo_general = d.tipo_general || default_type_id;

                    this.partidas = Array.isArray(d.partidas)
                        ? JSON.parse(JSON.stringify(d.partidas))
                        : [];

                    // Si viene vacío, al menos una partida
                    if (!this.partidas.length) {
                        this.partidas.push({
                            tipo: this.tipo_general,
                            product_id: '',
                            code_product: '',
                            description: '',
                            quantity: 1,
                            unit_price: 0,
                            tax_id: this.tax_general,
                            retention_id: '',
                            accounts_chart_id: '',
                            cuenta_busqueda: ''
                        });
                    }

                    // Al inicio, filtramos productos según tipo de código
                    this.filtrarProductosCatalogo();

                    this.loading = false;
                })
                .catch(() => {
                    this.load_error = true;
                    this.loading = false;
                });
        },

        watch: {
            tipo_general(n) { this.aplicarGeneral('type'); },
            tax_general(n) { this.aplicarGeneral('tax'); },
            retention_general(n) { this.aplicarGeneral('ret'); },
            codigo_producto_tipo() { this.filtrarProductosCatalogo(); }
        },

        methods: {
            // Aplica valores generales
            aplicarGeneral(tipo) {
                if (!this.partidas.length) return;
                this.partidas.forEach(p => {
                    if (tipo === 'tax') p.tax_id = this.tax_general;
                    if (tipo === 'ret') p.retention_id = this.retention_general;
                    if (tipo === 'type') p.tipo = this.tipo_general;
                });
            },

            // Filtro de productos según catálogo interno/proveedor
            filtrarProductosCatalogo() {
                if (this.codigo_producto_tipo === 'proveedor') {
                    this.productos_filtrados = this.all_products.filter(p => !!p.code_order || !!p.name_order);
                } else {
                    this.productos_filtrados = this.all_products;
                }
            },

            // Autocomplete producto
            autocompletarDescripcion(idx) {
                const raw = this.partidas[idx].code_product || '';
                const val = raw.toLowerCase();

                // Elegir qué campos comparar según catálogo
                let hit = null;

                if (this.codigo_producto_tipo === 'proveedor') {
                    hit = this.productos_filtrados.find(p => {
                        const combo = ((p.code_order || '') + ' - ' + (p.name_order || '')).toLowerCase();
                        return combo === val
                            || (p.code_order || '').toLowerCase() === val
                            || (p.name_order || '').toLowerCase() === val;
                    });
                } else {
                    hit = this.productos_filtrados.find(p => {
                        const combo = (p.code + ' - ' + p.name).toLowerCase();
                        return combo === val
                            || (p.code || '').toLowerCase() === val
                            || (p.name || '').toLowerCase() === val;
                    });
                }

                if (hit) {
                    this.partidas[idx].product_id = hit.id;
                    this.partidas[idx].description = this.codigo_producto_tipo === 'proveedor'
                        ? (hit.name_order || hit.name)
                        : hit.name;
                    this.partidas[idx].code_product = this.codigo_producto_tipo === 'proveedor'
                        ? (hit.code_order || hit.code)
                        : hit.code;
                }
            },

            // Buscar cuentas contables
            buscarCuentas(idx, texto) {
                const q = (texto || '').trim();
                this.focusedIndex = idx;

                if (q.length < 2) {
                    this.sugerencias = [];
                    return;
                }

                const urlBase = document.getElementById('url-location').dataset.url;
                axios.post(urlBase + 'search_accounts', {
                    q: q,
                    access_id: this.access_id,
                    access_token: this.access_token
                }, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => {
                        this.sugerencias = Array.isArray(r.data) ? r.data : [];
                    })
                    .catch(() => {
                        this.sugerencias = [];
                    });
            },

            seleccionarCuentaDirecta(acc, idx) {
                this.partidas[idx].accounts_chart_id = acc.id;
                this.partidas[idx].cuenta_busqueda = acc.code + ' - ' + acc.name;
                this.focusedIndex = null;
                this.sugerencias = [];
            },

            // Proveedor buscador
            filtrarProveedores() {
                const term = (this.proveedor_nombre || '').toLowerCase();
                if (term.length < 2) {
                    this.filteredProviders = [];
                    return;
                }
                this.filteredProviders = this.providers_opts.filter(p =>
                    p.name.toLowerCase().includes(term) ||
                    (p.code && p.code.toLowerCase().includes(term))
                );
            },

            seleccionarProveedor(prov) {
                this.proveedor_id = prov.id;
                this.proveedor_nombre = prov.name;
                this.filteredProviders = [];
            },

            // Partidas
            agregarPartida() {
                this.partidas.push({
                    tipo: this.tipo_general,
                    product_id: '',
                    code_product: '',
                    description: '',
                    quantity: 1,
                    unit_price: 0,
                    tax_id: this.tax_general,
                    retention_id: this.retention_general,
                    accounts_chart_id: '',
                    cuenta_busqueda: ''
                });
            },

            eliminarPartida(idx) {
                if (this.partidas.length > 1) {
                    this.partidas.splice(idx, 1);
                }
            },

            // ===== Guardar sin autorizar =====
            guardarCambios() {
                this._guardarOrden('solo_guardar');
            },

            // ===== Guardar y autorizar =====
            guardarYAutorizar() {
                this._guardarOrden('autorizar');
            },

            _guardarOrden(modo) {
                if (!this.proveedor_id || !this.codigo_oc || !this.fecha || !this.moneda || !this.partidas.length) {
                    Swal.fire('Campos incompletos', 'Verifica proveedor, fechas, moneda y partidas.', 'warning');
                    return;
                }

                const datos = {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    id: this.ordenId,
                    proveedor_id: this.proveedor_id,
                    codigo_oc: this.codigo_oc,
                    fecha: this.fecha,
                    fecha_pago: this.fecha_pago,
                    moneda: this.moneda,
                    notas: this.notas,
                    document_type_id: this.document_type_id,
                    codigo_producto_tipo: this.codigo_producto_tipo,
                    tax_id: this.tax_general,
                    retention_id: this.retention_general,
                    partidas: this.partidas,
                    modo: modo   // 'solo_guardar' o 'autorizar'
                };

                const urlBase = document.getElementById('url-location').dataset.url;
                this.loading = true;

                axios.post(urlBase + 'autorizar_ajax', datos, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(resp => {
                        if (resp.data && resp.data.success) {
                            if (modo === 'autorizar') {
                                Swal.fire('Orden autorizada', 'Se guardaron los cambios y se autorizó la orden.', 'success')
                                    .then(() => {
                                        window.location.href = urlBase.replace('ajax/', '') + 'compras/ordenes/info/' + this.ordenId;
                                    });
                            } else {
                                Swal.fire('Cambios guardados', 'La orden se actualizó correctamente.', 'success');
                            }
                        } else {
                            Swal.fire('Error', resp.data.error || 'No se pudo guardar la orden.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Ocurrió un problema inesperado al guardar.', 'error');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },

            cancelar() {
                const urlBase = document.getElementById('url-location').dataset.url;
                window.location.href = urlBase.replace('ajax/', '') + 'compras/ordenes/info/' + this.ordenId;
            }
        },

        computed: {
            subtotal() {
                return this.partidas.reduce((s, i) =>
                    s + ((+i.quantity || 0) * (+i.unit_price || 0)), 0);
            },
            impuestoTotal() {
                return this.partidas.reduce((s, i) => {
                    const sub = (+i.quantity || 0) * (+i.unit_price || 0);
                    const t = this.tax_opts.find(x => x.id == i.tax_id);
                    const rate = t ? +t.rate : 0;
                    return s + (sub * rate);
                }, 0);
            },
            retencionTotal() {
                return this.partidas.reduce((s, i) => {
                    const sub = (+i.quantity || 0) * (+i.unit_price || 0);
                    const r = this.retention_opts.find(x => x.id == i.retention_id);
                    const rate = r ? +r.rate : 0;
                    return s + (sub * rate);
                }, 0);
            },
            total() {
                return this.subtotal + this.impuestoTotal - this.retencionTotal;
            }
        },

        filters: {
            currency(v) {
                v = Number(v) || 0;
                return '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 2 });
            }
        }
    });
}
