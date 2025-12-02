if (document.getElementById('cotizacion-app')) {

    // ===============================
    // REGISTRO DEL COMPONENTE VUE-SELECT (SELECTS CON BÚSQUEDA)
    // ===============================
    Vue.component('v-select', window.VueSelect.VueSelect);

    new Vue({
        el: '#cotizacion-app',
        data: {
            // ================================
            // === VARIABLES DE AUTENTICACIÓN Y BASE ===
            // ================================
            access_id: window.access_id || '',
            access_token: window.access_token || '',
            urlBase: document.getElementById('url-location').dataset.url,

            // ================================
            // === CONTROL GENERAL DEL FORMULARIO ===
            // ================================
            loadingPartners: false,
            modoEdicion: false,
            bloqueadoSocio: false,
            camposEntregaActivos: false,

            // ================================
            // === DATOS PRINCIPALES DE LA COTIZACIÓN ===
            // ================================
            socioSeleccionado: null,
            partner_id: '',
            partner_contact_id: '',
            nuevo_available: 0,
            reference: '',
            valid_date: '',
            address_id: '',
            payment_id: '',
            employee_id: '',
            comments: '',
            discount_general: '',
            discount_general_id: '',
            discount_general_rate: 0,

            // ================================
            // === CATÁLOGOS Y OPCIONES ===
            // ================================
            tax_opts:       [],
            retention_opts: [],
            moneda_opts:    [],
            payments_opts:  [],
            employees_opts: [],
            marcas_opts:    [],
            partners_opts:  [],
            contacts_opts:  [],
            addresses_opts: [],
            products_opts:  [],
            discounts_opts: [],

            // ================================
            // === DATOS DE LA COTIZACIÓN TEMPORAL ===
            // ================================
            cotizacionRecuperable: false,
            cotizacionTemporal: null,
            fechaCatalogos: '',

            // ================================
            // === DATOS DE PARTIDAS Y PRODUCTOS ===
            // ================================
            partidas: [],
            productoSeleccionado: null,
            tipo_partida: 'Articulo',
            nuevo_producto_id: '',
            nuevo_quantity: 1,
            nuevo_unit_price: '',
            nuevo_product_image: '/assets/uploads/thumb_no_image.png',

            // ================================
            // === COLUMNAS DE LA TABLA (CONTROL VISUAL) ===
            // ================================
            showImpuesto: false,
            showRetencion: false,
            showDescuento: false,

            // ================================
            // === CAMPOS GENERALES DE IMPUESTO, RETENCIÓN Y MONEDA ===
            // ================================
            tax_general: '',
            retention_general: '',
            moneda_id: '',

            // ================================
            // === DATOS PARA MODALES MASIVOS (MARCA / RANGO) ===
            // ================================
            filtroMarca: '',
            marca_id: '',
            productos_marca: [],
            marcaSeleccionada: null,
            todosSeleccionados: false,
            rango_inicio: '',
            rango_fin: '',
            productos_rango: [],
            todosSeleccionadosRango: false,

            // ================================
            // === NUEVO DOMICILIO DE ENTREGA ===
            // ================================
            mostrarModalDomicilio: false,
            nuevoDomicilio: {
                iddelivery: '', street: '', number: '', internal_number: '',
                colony: '', zipcode: '', city: '', municipality: '',
                state: '', reception_hours: '', delivery_notes: '',
                name: '', last_name: '', phone: ''
            },
            erroresDomicilio: [],
            estados_opts: [],

            // ================================
            // === ASIGNACIÓN DE VENDEDOR ===
            // ================================
            vendedorAsignado: '',
            correoSocioPrincipal: '',
            correoPrincipalSeleccionado: true,
            correosAdicionales: '',
            alertaCorreo: '',
            exitoCorreo: '',
            cotizacionGuardada: false,
            quote_id: null,
            errores: {},
            nuevoContacto: { name: '', last_name: '', email: '', phone: '' },

            // ================================
            // ESTATUS PENDIENTES
            // ================================
            pending_id: null,
            isLoadingInitialData: false, //controla la carga inicial de datos
        },

        // =========================
        // FUNCIÓN QUE SE EJECUTA AL CARGAR LA VISTA
        // =========================
        mounted() {
            // === REGISTRA INSTANCIA GLOBAL Y CARGA TOKENS ===
            window._vueCotizacion = this;
            this.access_id = window.access_id || '';
            this.access_token = window.access_token || '';

            // === CONFIGURACIONES LOCALES Y ESCUCHA DE CONEXIÓN ===
            this.fechaCatalogos = '';
            localforage.getItem('catalogos_fecha_actualizacion').then(f => this.fechaCatalogos = f || '');
            localforage.getItem('catalogo_products').then(prods => {
                if (!prods || !prods.length) {
                    Swal.fire('Advertencia', 'No tienes catálogos offline descargados.', 'warning');
                }
            });

            if (localStorage.getItem('showImpuesto') !== null) {
                this.showImpuesto = localStorage.getItem('showImpuesto') === 'true';
            }
            if (localStorage.getItem('showRetencion') !== null) {
                this.showRetencion = localStorage.getItem('showRetencion') === 'true';
            }
            if (localStorage.getItem('showDescuento') !== null) {
                this.showDescuento = localStorage.getItem('showDescuento') === 'true';
            }

            window.addEventListener('online', () => this.sincronizarCotizacionesPendientes());

            // --- NUEVO FLUJO DE CARGA ASÍNCRONA Y ORDENADA ---
            const url = new URL(window.location.href);
            const pending_id = url.searchParams.get('pending_id');
            const editar = window.modoEdicion || false;
            const input = document.getElementById('cotizacion-pending-id');
            const input_id = input && input.value && !isNaN(input.value) ? parseInt(input.value) : null;

            // Primero, carga los catálogos, sin importar el flujo de la cotización.
            // Esto asegura que estén disponibles para cualquier operación.
            const catalogoPromise = Promise.all([
                this.cargarCatalogos(),
                this.cargarEstados()
            ]);

            // Define la lógica principal para cargar la cotización, que depende del tipo de flujo.
            let cotizacionPromise;

            if (pending_id && !isNaN(pending_id)) {
                this.pending_id = parseInt(pending_id);
                console.log('[DEBUG] Cotización pendiente detectada ID:', this.pending_id);
                cotizacionPromise = this.cargarCotizacionPendiente(this.pending_id);
            } else if (input_id) {
                this.pending_id = input_id;
                console.log('[DEBUG] Cotización pendiente detectada desde input:', this.pending_id);
                cotizacionPromise = this.cargarCotizacionPendiente(this.pending_id);
            } else if (editar && window.cotizacionEdicion) {
                console.log('[DEBUG] Modo edición activado');
                this.modoEdicion = true;
                cotizacionPromise = this.cargarCotizacionDesdeJSON(window.cotizacionEdicion);
            } else {
                console.log('[DEBUG] Nueva cotización');
                cotizacionPromise = this.recuperarCotizacion();
            }

            // Encadena la carga de catálogos y cotización.
            // Una vez que ambos procesos principales han terminado, carga los datos del socio.
            Promise.all([catalogoPromise, cotizacionPromise])
                .then(() => {
                    // El partner_id ya está disponible si se cargó una cotización o se recuperó
                    if (this.partner_id) {
                        // Llama a las funciones dependientes del socio.
                        // Esta llamada es crítica para que se carguen los contactos y domicilios.
                        this.buscarContactos();
                        this.buscarDirecciones();
                        this.obtenerProductosConPrecio();
                    }
                })
                .catch(error => {
                    console.error("Error durante la carga inicial:", error);
                    Swal.fire('Error', 'No se pudo completar la carga de datos iniciales.', 'error');
                });
        },



        computed: {
            // HABILITA BOTÓN DE FINALIZAR SÓLO SI CAMPOS CLAVE LLENOS
            puedeFinalizar() {
                return this.partner_id && this.partner_contact_id && this.reference && this.valid_date;
            },
            // FILTRO DE MARCAS PARA EL MODAL
            marcasFiltradas() {
                let txt = (this.filtroMarca || '').toLowerCase();
                if (!txt) return this.marcas_opts;
                return this.marcas_opts.filter(m => m.name.toLowerCase().includes(txt));
            },
            // CÁLCULO DEL DESCUENTO TOTAL EN TODOS LOS PRODUCTOS
            descuento() {
                let total = 0;
                for (let item of this.partidas) {
                    const cantidad = parseFloat(item.quantity || 0);
                    const precio = parseFloat(item.unit_price || 0);
                    const descuento = parseFloat(item.discount || 0);
                   total += cantidad * precio * (descuento / 100);
                }
                return total;
            },
            // CÁLCULO DE TOTALES
            totalsindescuento() {
                let total = 0;
                for (let item of this.partidas) {
                    const cantidad = parseFloat(item.quantity || 0);
                    const precio = parseFloat(item.unit_price || 0);
                    total += cantidad * precio ;
                }
                return total;
            },
            subtotal() {
                return this.partidas.reduce((t, item) => t + this.calcularTotalPartida(item), 0);
            },
            iva() {
                let total = 0;
                for (let item of this.partidas) {
                    let tasa = 0;
                    let tax = this.tax_opts.find(t => t.id == (item.tax_id || this.tax_general));
                    if (tax) tasa = parseFloat(tax.value || tax.rate || 0);

                    const cantidad = parseFloat(item.quantity || 0);
                    const precio = parseFloat(item.unit_price || 0);
                    const descuento = parseFloat(item.discount || 0);

                    const subtotalConDescuento = cantidad * precio * (1 - descuento / 100);

                    total += subtotalConDescuento * (tasa / 100);
                }
                return total;
            },
            retencion() {
                let total = 0;
                for (let item of this.partidas) {
                    let tasa = 0;
                    let ret = this.retention_opts.find(r => r.id == (item.retention_id || this.retention_general));
                    if (ret) tasa = parseFloat(ret.value || ret.rate || 0);

                    const cantidad = parseFloat(item.quantity || 0);
                    const precio = parseFloat(item.unit_price || 0);
                    const descuento = parseFloat(item.discount || 0);

                    const subtotalConDescuento = cantidad * precio * (1 - descuento / 100);

                    total += subtotalConDescuento * (tasa / 100);
                }
                return total;
            },
            total() {
                return this.subtotal + this.iva - this.retencion;
            }
        },

        // =========================
        // FILTRO DE MONEDA (MUESTRA EL SÍMBOLO)
        // =========================
        filters: {
            currency(value) {
                let simbolo = '$';
                let moneda = this.moneda_opts ? this.moneda_opts.find(m => m.id == this.moneda_id) : null;
                if (moneda && moneda.symbol) simbolo = moneda.symbol;
                if (!value) value = 0;
                return simbolo + parseFloat(value).toLocaleString('es-MX', { minimumFractionDigits: 2 });
            }
        },

        watch: {
            // OBSERVADORES PARA GUARDADO AUTOMÁTICO Y REACTIVIDAD
            tax_general(val) { this.partidas.forEach(p => p.tax_id = val); },
            retention_general(val) { this.partidas.forEach(p => p.retention_id = val); },
            productos_rango: {
                handler() {
                    this.todosSeleccionadosRango = this.productos_rango.length > 0 &&
                        this.productos_rango.every(prod => prod.selected);
                },
                deep: true
            },
            //DESUCNTO EN PARTIDAS
            discount_general_id(newId) {
                const selected = this.discounts_opts.find(d => d.id == newId);
                let rate = 0;
                if (selected && selected.final_effective && !isNaN(selected.final_effective)) {
                    rate = parseFloat(selected.final_effective);
                }
                this.discount_general_rate = rate;
                this.partidas.forEach(p => {
                    // Aplica solo si el producto no tiene descuento individual
                    if (!p.discount || p.discount === 0) {
                        this.$set(p, 'discount', this.discount_general_rate);
                    }
                });
            },
            marca_id() { this.todosSeleccionados = false; },
            productos_marca() { this.todosSeleccionados = false; },
            showImpuesto(val) { localStorage.setItem('showImpuesto', val); },
            showRetencion(val) { localStorage.setItem('showRetencion', val); },
            showDescuento(val) { localStorage.setItem('showDescuento', val); },
            partidas: { handler() { this.autoguardarCotizacion(); }, deep: true },
            partner_id(val) { this.autoguardarCotizacion(); },
            partner_contact_id(val) { this.autoguardarCotizacion(); },
            reference(val) { this.autoguardarCotizacion(); },
            valid_date(val) { this.autoguardarCotizacion(); },
            address_id(val) { this.autoguardarCotizacion(); },
            payment_id(val) { this.autoguardarCotizacion(); },
            employee_id(val) { this.autoguardarCotizacion(); },
            comments(val) { this.autoguardarCotizacion(); }
        },

        methods: {
            // ============================
            // DESCARGA TODOS LOS CATÁLOGOS (INCLUYE PRODUCTOS_PRICES)
            // ============================
            async descargarCatalogos() {
                try {
                    let res = await axios.post(this.urlBase + 'catalogos_cotizaciones_completo', {
                        access_id: this.access_id,
                        access_token: this.access_token
                    }, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.data.msg === 'ok') {
                        // === GUARDA TODOS LOS CATÁLOGOS, INCLUYENDO PRODUCTS_PRICES ===
                        await localforage.setItem('catalogo_products', res.data.products || []);
                        await localforage.setItem('catalogo_products_prices', res.data.products_prices || []);
                        await localforage.setItem('catalogo_partners', res.data.partners || []);
                        await localforage.setItem('catalogo_empleados', res.data.employees || []);
                        await localforage.setItem('catalogo_pagos', res.data.payments || []);
                        await localforage.setItem('catalogo_taxes', res.data.taxes || []);
                        await localforage.setItem('catalogo_retentions', res.data.retentions || []);
                        await localforage.setItem('catalogo_currencies', res.data.currencies || []);
                        await localforage.setItem('catalogo_discounts', res.data.discounts || []);
                        await localforage.setItem('catalogo_estados', res.data.states || []);
                        await localforage.setItem('catalogo_brands', res.data.brands || []);
                        let fecha = (new Date()).toLocaleString('es-MX');
                        await localforage.setItem('catalogos_fecha_actualizacion', fecha);
                        this.fechaCatalogos = fecha;
                        Swal.fire('¡Listo!', 'Los catálogos han sido descargados para uso offline.', 'success');
                    } else {
                        this.mostrarAlerta('No se pudo descargar catálogos: ' + (res.data.msg || 'Error.'));
                    }
                } catch (e) {
                    this.mostrarAlerta('Ocurrió un error al descargar los catálogos. Intenta de nuevo.');
                }
            },
            

            // ============================================
            // OBTIENE PRODUCTOS CON PRECIO PERSONALIZADO (ONLINE/OFFLINE)
            // ============================================
            async obtenerProductosConPrecio() {
                if (navigator.onLine) {
                    axios.post(this.urlBase + 'search_products_socios', {
                        access_id: this.access_id,
                        access_token: this.access_token,
                        partner_id: this.partner_id,
                        term: ''
                    }, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }).then(res => {
                        this.products_opts = res.data.msg === 'ok' ? res.data.products : [];
                    });
                } else {
                    await this.obtenerProductosConPrecioOffline();
                }
            },

            // ============================================
            // OBTIENE PRODUCTOS DEL CATÁLOGO OFFLINE Y ASIGNA EL PRECIO PERSONALIZADO
            // ============================================
            async obtenerProductosConPrecioOffline() {
                if (!this.partner_id) {
                    this.products_opts = [];
                    return;
                }
                const productos = await localforage.getItem('catalogo_products');
                const precios = await localforage.getItem('catalogo_products_prices');
                const partners = await localforage.getItem('catalogo_partners');
                const socio = partners ? partners.find(p => p.id == this.partner_id) : null;
                const type_id = socio ? socio.type_id : null;
                if (productos && precios && type_id) {
                    this.products_opts = productos.map(prod => {
                        const price = precios.find(p => p.product_id == prod.id && p.type_id == type_id);
                        return { ...prod, price: price ? price.price : 0 };
                    });
                } else {
                    this.products_opts = productos || [];
                }
            },

            // ===============================
            // BÚSQUEDA DE PRODUCTOS (ONLINE/OFFLINE) Y ASIGNACIÓN DE PRECIO
            // ===============================
            async buscarProductos(search, loading) {
                if (!search || search.length < 2) {
                    this.products_opts = [];
                    loading(false);
                    return;
                }

                if (navigator.onLine) {
                    axios.post(this.urlBase + 'search_products_socios', {
                        access_id: this.access_id,
                        access_token: this.access_token,
                        partner_id: this.partner_id,
                        term: search
                    }, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }).then(res => {
                        this.products_opts = res.data.msg === 'ok' ? res.data.products : [];
                        console.log("Productos recibidos:", this.products_opts);
                        loading(false);
                    }).catch(() => {
                        this.products_opts = [];
                        loading(false);
                    });
                } else {
                    await this.buscarProductosOffline(search);
                    loading(false);
                }
            },

            // ================================
            // BÚSQUEDA DE PRODUCTOS EN LOCALFORAGE
            // ================================
            async buscarProductosOffline(search) {
                if (!search || search.length < 2) {
                    this.products_opts = [];
                    return;
                }
                const productos = await localforage.getItem('catalogo_products');
                const precios = await localforage.getItem('catalogo_products_prices');
                const partners = await localforage.getItem('catalogo_partners');
                const socio = partners ? partners.find(p => p.id == this.partner_id) : null;
                const type_id = socio ? socio.type_id : null;
                if (productos && precios && type_id) {
                    let resultado = productos.filter(p =>
                        (p.name && p.name.toLowerCase().includes(search.toLowerCase())) ||
                        (p.code && p.code.toLowerCase().includes(search.toLowerCase()))
                    ).map(prod => {
                        let price = precios.find(pp => pp.product_id == prod.id && pp.type_id == type_id);
                        return { ...prod, price: price ? price.price : 0 };
                    });
                    this.products_opts = resultado;
                } else {
                    this.products_opts = [];
                }
            },


            async buscarProductos(search, loading) {
                if (!search || search.length < 2) {
                    this.products_opts = [];
                    loading(false);
                    return;
                }

                if (navigator.onLine) {
                    // AJAX NORMAL
                    axios.post(this.urlBase + 'search_products_socios', {
                        access_id: this.access_id,
                        access_token: this.access_token,
                        partner_id: this.partner_id,
                        term: search
                    }, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }).then(res => {
                        this.products_opts = res.data.msg === 'ok' ? res.data.products : [];
                        loading(false);
                    }).catch(() => {
                        this.products_opts = [];
                        loading(false);
                    });
                } else {
                    // OFFLINE
                    await this.buscarProductosOffline(search);
                    loading(false);
                }
            },
            // ============================
            getProductThumb(img) {
                if (!img) return '/assets/uploads/thumb_no_image.png';
                if (typeof img === 'string' && (img.startsWith('http') || img.includes('/uploads/'))) {
                    return img;
                }
                return '/assets/uploads/thumb_' + img;
            },
            setDefaultImagePartida(event) {
                event.target.src = '/assets/uploads/thumb_no_image.png';
            },


            // ========== MÉTODO DE AUTOGUARDADO ==========
            autoguardarCotizacion() {
                const datos = {
                    partner_id: this.partner_id,
                    socioSeleccionado: this.socioSeleccionado,
                    partner_contact_id: this.partner_contact_id,
                    reference: this.reference,
                    valid_date: this.valid_date,
                    address_id: this.address_id,
                    payment_id: this.payment_id,
                    employee_id: this.employee_id,
                    comments: this.comments,
                    partidas: this.partidas
                };
                localforage.setItem('cotizacion_en_curso', datos);
            },

            // ========= VERIFICA Y MUESTRA EL BANNER SI HAY COTIZACIÓN PENDIENTE =========
            recuperarCotizacion() {
                localforage.getItem('cotizacion_en_curso').then(datos => {
                    if (datos && datos.partidas && datos.partidas.length) {
                        this.cotizacionRecuperable = true;
                        this.cotizacionTemporal = datos;
                    }
                });
            },

            // ========== MÉTODO PARA RECUPERAR ==========
            aplicarCotizacionRecuperada() {
                if (!this.cotizacionTemporal) return;
                let datos = this.cotizacionTemporal;

                // Asigna todos los datos recuperados
                this.partner_id = datos.partner_id || '';
                this.socioSeleccionado = datos.socioSeleccionado;
                this.partner_contact_id = datos.partner_contact_id || '';
                this.reference = datos.reference || '';
                this.valid_date = datos.valid_date || '';
                this.address_id = datos.address_id || '';
                this.payment_id = datos.payment_id || '';
                this.employee_id = datos.employee_id || '';
                this.comments = datos.comments || '';
                // 🔹 Normaliza partidas recuperadas para que funcionen con la lógica de duplicados
                this.partidas = (datos.partidas || []).map(p => ({
                    id: p.id || p.product_id,           // usar siempre un ID uniforme
                    product_id: p.product_id || p.id,   // soporte para ambas variantes
                    code: p.code,
                    name: p.name,
                    unit_price: parseFloat(p.unit_price || 0),
                    quantity: parseInt(p.quantity || 0),
                    discount: p.discount || 0,
                    tax_id: p.tax_id || this.tax_general,
                    retention_id: p.retention_id || this.retention_general,
                    image: p.image || '',
                    tipo: p.tipo || 'Articulo'
                }));

                this.bloqueadoSocio = !!datos.partner_id;
                this.cotizacionRecuperable = false;
                this.cotizacionTemporal = null;
                localforage.removeItem('cotizacion_en_curso');

                // Busca el objeto socio en partners_opts (el catálogo ya cargado)
                if (this.partner_id && this.partners_opts && this.partners_opts.length) {
                    let socio = this.partners_opts.find(p => p.id == this.partner_id);
                    if (socio) {
                        this.socioSeleccionado = socio;
                    } else {
                        // Si no está en partners_opts, fuerza la búsqueda (o recarga el catálogo y vuelve a intentar)
                        this.buscarSocios('', () => {
                            let socio2 = this.partners_opts.find(p => p.id == this.partner_id);
                            if (socio2) this.socioSeleccionado = socio2;
                        });
                    }
                }

                // Recarga selects dependientes...
                if (this.partner_id) {
                    this.buscarContactos();
                    this.buscarDirecciones();
                    this.obtenerProductosConPrecio();
                }
                if (window.Swal) Swal.fire('¡Atención!', 'Se ha recuperado la cotización que estaba en curso.', 'info');
                else alert('Se recuperó la cotización anterior.');
            },


            // ========= ELIMINA EL AUTOGUARDADO Y OCULTA EL BANNER =========
            borrarCotizacionAutoguardada() {
                localforage.removeItem('cotizacion_en_curso');
                this.cotizacionRecuperable = false;
                this.cotizacionTemporal = null;
            },

            // ===============================
            // CARGA DE CATÁLOGOS GENERALES (IMPTOS, MONEDAS, ETC)
            // ===============================
            cargarCatalogos() {
                axios.post(this.urlBase + 'catalogos_cotizaciones', {
                    access_id: this.access_id,
                    access_token: this.access_token
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    if (res.data.msg === 'ok') {
                        this.tax_opts = res.data.taxes || [];
                        this.retention_opts = res.data.retentions || [];
                        this.moneda_opts = res.data.currencies || [];
                        this.discounts_opts = res.data.discounts || [];
                        this.payments_opts = res.data.payments || [];
                        this.employees_opts = res.data.employees || [];
                        this.marcas_opts = res.data.marcas || [];
                        // Defaults automáticos
                        this.tax_general = this.tax_opts[1]?.id || '';
                        this.retention_general = '';
                        this.moneda_id = this.moneda_opts[1]?.id || '';
                        this.payment_id = this.payments_opts.find(p => p.name.toLowerCase().includes('transf'))?.id || '';
                        if (this.employees_opts.length > 0) {
                            this.employee_id = this.employees_opts[0].id;
                        }
                        this.bloqueado = true;
                    } else {
                        this.mostrarAlerta('No se pudieron cargar los catálogos: ' + res.data.msg);
                    }
                }).catch(err => {
                    this.mostrarAlerta('Error al cargar catálogos.');
                });
            },

            // ===========================
            // SELECCION DE SOCIOS Y CARGA DE CONTACTOS, DIRECCIONES Y PRODUCTOS PERSONALIZADOS
            // ===========================
            buscarSocios(search, loading) {
                if (!search || search.length < 2) {
                    this.partners_opts = [];
                    loading(false);
                    return;
                }

                if (!navigator.onLine) {
                    localforage.getItem('catalogo_partners').then(partners => {
                        if (partners) {
                            this.partners_opts = partners.filter(p =>
                                (p.name && p.name.toLowerCase().includes(search.toLowerCase())) ||
                                (p.code_sap && p.code_sap.toLowerCase().includes(search.toLowerCase()))
                            );
                        } else {
                            this.partners_opts = [];
                        }
                        loading(false);
                    });
                    return;
                }

                // ONLINE
                this.loadingPartners = true;
                loading(true);
                this.partners_opts = [];
                axios.post(this.urlBase + 'search_partners', {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    term: search
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(response => {
                    this.loadingPartners = false;
                    loading(false);
                    if (response.data.msg === 'ok') {
                        this.partners_opts = response.data.data;
                        // Actualiza catálogo local para offline
                        localforage.setItem('catalogo_partners', response.data.data);
                    } else {
                        this.partners_opts = [];
                        this.mostrarAlerta('No se encontraron socios: ' + response.data.msg);
                    }
                }).catch(() => {
                    this.loadingPartners = false;
                    loading(false);
                    this.partners_opts = [];
                    this.mostrarAlerta('Error al buscar socios.');
                });
            },


            // Selección de socio y bloqueo
            onSeleccionarSocio(val) {
                this.socioSeleccionado = val;
                this.partner_id = val ? val.id : '';
                this.partner_contact_id = '';
                this.address_id = '';
                this.contacts_opts = [];
                this.addresses_opts = [];
                this.products_opts = [];
                this.vendedorAsignado = '';
                this.camposEntregaActivos = !!val;
                this.bloqueadoSocio = !!val; // <-- IMPORTANTE: Bloquea el select
                if (this.partner_id) {
                    this.buscarContactos();
                    this.buscarDirecciones();
                    this.obtenerProductosConPrecio();
                }
            },

            // Botón para permitir cambiar socio (desbloquear)
            reiniciarSocio() {
                if (this.partidas && this.partidas.length > 0) {
                    if (window.Swal) {
                        Swal.fire({
                            title: '¿Cambiar socio?',
                            text: 'Se perderán los productos agregados.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, cambiar',
                            cancelButtonText: 'Cancelar'
                        }).then(result => {
                            if (result.isConfirmed || result.value) this.resetSocioFields();
                        });
                    } else {
                        if (confirm('¿Cambiar socio? Se perderán los productos agregados.')) {
                            this.resetSocioFields();
                        }
                    }
                } else {
                    this.resetSocioFields();
                }
            },
            resetSocioFields() {
                this.socioSeleccionado = null;
                this.partner_id = '';
                this.partner_contact_id = '';
                this.reference = '';
                this.valid_date = '';
                this.contacts_opts = [];
                this.bloqueadoSocio = false; // <-- DESBLOQUEA EL SELECT
                this.partidas = [];
                // Enfoca el select
                this.$nextTick(() => {
                    if (this.$refs.socioSelect) {
                        let input = this.$refs.socioSelect.$el.querySelector('input[type="search"]');
                        if (input) input.focus();
                    }
                });
            },

            buscarContactos() {
                axios.post(this.urlBase + 'search_partners_contacts', {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    partner_id: this.partner_id
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    this.contacts_opts = res.data.msg === 'ok' ? res.data.data : [];
                });
            },

            abrirModalContacto() {
                // Limpia el formulario
                this.nuevoContacto = { name: '', last_name: '', email: '', phone: '' };
                $('#modalContacto').modal('show');
            },

            guardarNuevoContacto() {
                // Valida lo necesario aquí (si quieres)
                if (!this.nuevoContacto.name || !this.nuevoContacto.email) {
                    this.mostrarAlerta('Nombre y correo son obligatorios.');
                    return;
                }
                // AJAX al backend
                axios.post(this.urlBase + 'add_partner_contact', {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    partner_id: this.partner_id,
                    name: this.nuevoContacto.name,
                    last_name: this.nuevoContacto.last_name,
                    email: this.nuevoContacto.email,
                    phone: this.nuevoContacto.phone
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    if (res.data.msg === 'ok') {
                        // Cierra modal y actualiza lista de contactos
                        $('#modalContacto').modal('hide');
                        this.mostrarAlerta('Contacto agregado correctamente');
                        this.buscarContactos();
                        this.partner_contact_id = res.data.data.contact.id; // Selecciona el nuevo contacto
                    } else {
                        this.mostrarAlerta(res.data.msg || 'Error al guardar contacto');
                    }
                }).catch(() => {
                    this.mostrarAlerta('Error al guardar el contacto');
                });
            },

            buscarDirecciones(idSeleccionar = null) {
                axios.post(this.urlBase + 'get_partner_addresses', {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    partner_id: this.partner_id
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    this.addresses_opts = res.data.msg === 'ok' ? res.data.addresses : [];
                    // SOLO selecciona cuando está en la lista
                    if (idSeleccionar) {
                        // Busca si está ese id en la nueva lista (opcional)
                        let existe = this.addresses_opts.some(addr => addr.id == idSeleccionar);
                        if (existe) this.address_id = idSeleccionar;
                        else this.address_id = '';
                    }
                });
            },

            obtenerProductosConPrecio() {
                axios.post(this.urlBase + 'search_products_socios', {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    partner_id: this.partner_id,
                    term: ''
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    this.products_opts = res.data.msg === 'ok' ? res.data.products : [];
                });
            },

            buscarProductos(search, loading) {
                if (!search || search.length < 2) return;
                loading(true);
                axios.post(this.urlBase + 'search_products_socios', {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    partner_id: this.partner_id,
                    term: search
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    loading(false);
                    this.products_opts = res.data.msg === 'ok' ? res.data.products : [];
                }).catch(() => {
                    loading(false);
                    this.products_opts = [];
                });
            },

            abrirModalColumnas() {
                $('#modal-columnas').modal('show');
            },


            //cotizaciones pendientes
            async cargarCotizacionPendiente(pendingId) {
                try {
                    const res = await axios.post(this.urlBase + 'get_pending_quote', {
                        pending_id: pendingId
                    });

                    if (res.data && res.data.msg === 'ok') {
                        this.modoEdicion = true; // Si quieres bloquear cambio de socio
                        this.partner_id = res.data.partner.id;
                        this.socioSeleccionado = res.data.partner;

                        // Agrega los productos deserializados
                        if (res.data.products && Array.isArray(res.data.products)) {
                            this.partidas = res.data.products.map(p => ({
                                id: p.id,
                                code: p.code,
                                name: p.name,
                                quantity: p.quantity,
                                image: p.image,
                                unit_price: '', // Se llena después
                            }));
                        }

                        console.log('[DEBUG] Cotización pendiente cargada:', res.data);
                    } else {
                        Swal.fire('Aviso', 'No se pudo cargar la cotización pendiente.', 'warning');
                    }
                } catch (err) {
                    console.error('[ERROR] Falló al cargar cotización pendiente:', err);
                    Swal.fire('Error', 'Error al obtener la cotización.', 'error');
                }
            },


            // =========================
            // MANEJO DE INPUTS DE PRODUCTO: TABULAR ENTRE CAMPOS Y FOCUS
            // =========================
            focusPrecio() {
                this.$nextTick(() => {
                    if (this.$refs.inputPrecio) this.$refs.inputPrecio.focus();
                });
            },
            focusCantidad() {
                this.$nextTick(() => {
                    if (this.$refs.inputCantidad) this.$refs.inputCantidad.focus();
                });
            },

            onSeleccionarProducto(val) {
                this.productoSeleccionado = val;
                this.nuevo_producto_id = val ? val.id : '';
                if (val) {
                    this.nuevo_unit_price = val.price;
                    this.nuevo_available = val.available || val.stock || 0;
                    // SI EL CAMPO image VIENE VACÍO O NULL, MUESTRA thumb_no_image.png
                    if (!val.image || val.image === '' || val.image === null) {
                        this.nuevo_product_image = '/assets/uploads/thumb_no_image.png';
                    } else if (val.image.startsWith('http')) {
                        // SI YA ES UNA URL COMPLETA (poco probable), úsala directo
                        this.nuevo_product_image = val.image;
                    } else {
                        // SIEMPRE ARMA EL PATH DE LA MINIATURA (THUMB) A PARTIR DEL NOMBRE DE ARCHIVO
                        this.nuevo_product_image = '/assets/uploads/thumb_' + val.image;
                    }

                    this.focusPrecio();
                } else {
                    this.nuevo_unit_price = '';
                    this.nuevo_product_image = '/assets/uploads/thumb_no_image.png';
                    this.nuevo_available = 0;
                }
            },


            // =========================
            // AGREGAR PARTIDA (PRODUCTO) A LA TABLA DE COTIZACION
            // =========================
            agregarProducto() {
                this.errores = {};

                // Validación visual
                if (!this.productoSeleccionado) {
                    this.errores.producto = true;
                    this.mostrarAlerta('Selecciona un producto.');
                    return;
                }
                let prod = this.products_opts.find(p => p.id == this.productoSeleccionado.id);
                if (!prod) {
                    this.errores.producto = true;
                    this.mostrarAlerta('Producto inválido.');
                    return;
                }
                if (parseFloat(this.nuevo_unit_price) === 0) {
                    this.errores.precio = true;
                    this.mostrarAlerta('No se puede agregar un producto con precio 0.');
                    return;
                }
                let min = parseInt(prod.minimum_sale || 0);
                if (min > 0 && parseInt(this.nuevo_quantity) < min) {
                    this.errores.cantidad = true;
                    this.mostrarAlerta('La cantidad mínima para este producto es: ' + min);
                    return;
                }

                let existe = this.partidas.find(p => p.id == prod.id);
                if (existe) {
                    let cantidadAnterior = existe.quantity;
                    let precioAnterior = existe.unit_price;
                    let cantidadNueva = parseInt(this.nuevo_quantity);

                    // ✅ Sumar cantidad
                    existe.quantity += cantidadNueva;

                    // Subtotales antes y después
                    let subtotalAnterior = cantidadAnterior * precioAnterior;
                    let subtotalNuevo = existe.quantity * parseFloat(this.nuevo_unit_price);

                    // ✅ Revisar si el precio cambió
                    if (parseFloat(precioAnterior) !== parseFloat(this.nuevo_unit_price)) {
                        let precioNuevo = parseFloat(this.nuevo_unit_price);
                        existe.unit_price = precioNuevo; // Conserva el nuevo precio
                        Swal.fire({
                            icon: 'info',
                            title: 'Producto actualizado',
                            html: `
                El producto <b>${prod.code} - ${prod.name}</b> ya estaba en la cotización.<br><br>
                <b>Cantidad</b>: antes ${cantidadAnterior}, ahora ${existe.quantity}.<br>
                <b>Precio</b>: cambió de $${precioAnterior.toFixed(2)} a $${precioNuevo.toFixed(2)}.<br>
                <b>Subtotal</b>: pasó de $${subtotalAnterior.toFixed(2)} a $${subtotalNuevo.toFixed(2)}.
            `
                        });
                    } else {
                        // ⚡ Solo cantidades (mismo precio)
                        let subtotalNuevo = existe.quantity * precioAnterior;
                        Swal.fire({
                            icon: 'info',
                            title: 'Producto duplicado',
                            html: `
                El producto <b>${prod.code} - ${prod.name}</b> ya estaba en la cotización.<br><br>
                Se agregaron <b>${cantidadNueva}</b> unidades (antes ${cantidadAnterior}, ahora ${existe.quantity}).<br>
                <b>Subtotal actual</b>: $${subtotalNuevo.toFixed(2)}.
            `
                        });
                    }
                } else {
                    this.partidas.push({
                        tipo: this.tipo_partida,
                        code: prod.code,
                        name: prod.name,
                        unit_price: parseFloat(this.nuevo_unit_price),
                        quantity: parseInt(this.nuevo_quantity),
                        discount_id: this.discount_general_id,
                        discount: this.discount_general_rate,
                        tax_id: this.tax_general,
                        retention_id: this.retention_general,
                        image: prod.image || '',
                        id: prod.id
                    });
                }


                // Limpiar campos de nuevo producto
                this.productoSeleccionado = null;
                this.nuevo_producto_id = '';
                this.nuevo_quantity = 1;
                this.nuevo_unit_price = '';
                this.nuevo_product_image = '/assets/uploads/thumb_no_image.png';
                setTimeout(() => {
                    let input = this.$refs.productoSelect?.$el?.querySelector('input[type="search"]');
                    if (input) input.focus();
                }, 300);
            },

            // =========================
            // ELIMINAR PARTIDA DE LA TABLA DE PRODUCTOS
            // =========================
            eliminarProducto(idx) {
                this.partidas.splice(idx, 1);
            },

            // =========================
            // MODALES MASIVOS: POR MARCA Y POR RANGO DE CODIGO
            // =========================

            // -- MODAL MARCA --
            onSeleccionarMarca(marca) {
                this.marca_id = marca ? marca.id : '';
                this.cargarProductosMarca();
            },
            cargarProductosMarca() {
                this.productos_marca = [];
                if (!this.marca_id || !this.partner_id) return;

                axios.post(this.urlBase + 'get_partner_products_by_brand', {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    partner_id: this.partner_id,
                    filtro: this.marca_id
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    if (res.data.msg === 'ok') {
                        this.productos_marca = (res.data.products || []).map(prod => ({
                            ...prod,
                            selected: false,
                            cantidad: parseInt(prod.minimum_sale) > 0 ? parseInt(prod.minimum_sale) : 1
                        }));
                    } else {
                        this.productos_marca = [];
                    }
                });
            },
            toggleSeleccionarTodos() {
                this.productos_marca.forEach(prod => {
                    prod.selected = this.todosSeleccionados;
                });
            },
            limpiarModalMarca() {
                this.marcaSeleccionada = null;
                this.marca_id = '';
                this.productos_marca = [];
                this.todosSeleccionados = false;
                this.filtroMarca = '';
            },
            agregarSeleccionadosMarca() {
                for (let prod of this.productos_marca) {
                    if (prod.selected && prod.cantidad > 0) {
                        let existente = this.partidas.find(p => p.id == prod.id);
                        if (existente) {
                            existente.quantity += parseInt(prod.cantidad);
                        } else {
                            this.partidas.push({
                                tipo: 'Articulo',
                                code: prod.code,
                                name: prod.name,
                                unit_price: parseFloat(prod.price),
                                quantity: parseInt(prod.cantidad),
                                tax_id: this.tax_general,
                                retention_id: this.retention_general,
                                image: prod.image_url || '',
                                id: prod.id
                            });
                        }
                    }
                }
                this.limpiarModalMarca();
            },

            // -- MODAL RANGO --
            buscarProductosRango() {
                this.productos_rango = [];
                this.todosSeleccionadosRango = false;
                if (!this.rango_inicio || !this.rango_fin || !this.partner_id) {
                    this.mostrarAlerta('Faltan datos de rango.');
                    return;
                }
                axios.post(this.urlBase + 'get_partner_products_by_code_range', {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    partner_id: this.partner_id,
                    codigo_inicio: this.rango_inicio,
                    codigo_fin: this.rango_fin
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    if (res.data.msg == 'ok') {
                        this.productos_rango = (res.data.products || []).map(prod => ({
                            ...prod,
                            selected: false,
                            cantidad: parseInt(prod.minimum_sale) > 0 ? parseInt(prod.minimum_sale) : 1
                        }));
                    } else {
                        this.productos_rango = [];
                    }
                });
            },
            agregarSeleccionadosRango() {
                for (let prod of this.productos_rango) {
                    if (prod.selected && prod.cantidad > 0) {
                        let existente = this.partidas.find(p => p.id == prod.id);
                        if (existente) {
                            existente.quantity += parseInt(prod.cantidad);
                        } else {
                            this.partidas.push({
                                tipo: 'Articulo',
                                code: prod.code,
                                name: prod.name,
                                unit_price: parseFloat(prod.price),
                                quantity: parseInt(prod.cantidad),
                                tax_id: this.tax_general,
                                retention_id: this.retention_general,
                                image: prod.image_url || '',
                                id: prod.id
                            });
                        }
                    }
                }
                this.rango_inicio = '';
                this.rango_fin = '';
                this.productos_rango = [];
            },
            seleccionarTodosRango() {
                this.productos_rango.forEach(prod => {
                    prod.selected = this.todosSeleccionadosRango;
                });
            },
            limpiarModalRango() {
                this.rango_inicio = '';
                this.rango_fin = '';
                this.productos_rango = [];
                this.todosSeleccionadosRango = false;
            },
            cerrarModalRango() {
                this.limpiarModalRango();
                $('#modal-agregar-rango').modal('hide');
            },
            getMonedaSymbol(moneda_id) {
                const map = {};
                this.moneda_opts.forEach(m => {
                    map[m.id] = m.symbol || m.simbolo || '$';
                });
                return map[moneda_id] || '$';
            },
            calcularTotalPartida(item) {
                let subtotal = item.quantity * item.unit_price;
                let desc = item.discount ? (subtotal * (item.discount / 100)) : 0;
                return subtotal - desc;
            },
            recalculaTotales() {
                // Si requieres recalcular subtotales, puedes actualizar los totales aquí si tu lógica lo necesita.
                // Si tu total ya es computado, puedes dejarlo vacío, pero debe existir.
            },

            



            abrirModalDomicilio() {
                // SOLO SI HAY SOCIO SELECCIONADO
                if (!this.partner_id) return;
                this.mostrarModalDomicilio = true;
                this.erroresDomicilio = [];
                // Limpia los campos
                this.nuevoDomicilio = { iddelivery: '', street: '', number: '', internal_number: '', colony: '', zipcode: '', city: '', municipality: '', state: '', reception_hours: '', delivery_notes: '' };
                
            },
            cerrarModalDomicilio() {
                this.mostrarModalDomicilio = false;
            },

            cargarEstados() {
                axios.post(this.urlBase + 'catalogo_estados', {
                    access_id: this.access_id,
                    access_token: this.access_token
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    if (res.data.msg === 'ok') {
                        this.estados_opts = res.data.estados || [];
                    } else {
                        this.estados_opts = [];
                        this.mostrarAlerta('No se pudo cargar el catálogo de estados: ' + res.data.msg);
                    }
                }).catch(() => {
                    this.estados_opts = [];
                    this.mostrarAlerta('Error al cargar el catálogo de estados');
                });
            },

            guardarNuevoDomicilio() {
                // Validación simple (puedes extenderla)
                this.erroresDomicilio = [];
                if (!this.nuevoDomicilio.iddelivery) this.erroresDomicilio.push('El identificador es obligatorio.');
                if (!this.nuevoDomicilio.street) this.erroresDomicilio.push('La calle es obligatoria.');
                if (!this.nuevoDomicilio.number) this.erroresDomicilio.push('El número es obligatorio.');
                if (!this.nuevoDomicilio.colony) this.erroresDomicilio.push('La colonia es obligatoria.');
                if (!this.nuevoDomicilio.zipcode) this.erroresDomicilio.push('El código postal es obligatorio.');
                if (!this.nuevoDomicilio.city) this.erroresDomicilio.push('La ciudad es obligatoria.');
                if (!this.nuevoDomicilio.state) this.erroresDomicilio.push('El estado es obligatorio.');
                if (!this.nuevoDomicilio.reception_hours) this.erroresDomicilio.push('El horario de recepción es obligatorio.');
                // Contacto NO es obligatorio

                if (this.erroresDomicilio.length) return;

                let payload = {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    partner_id: this.partner_id,
                    // Agregar todos los campos:
                    ...this.nuevoDomicilio
                };

                // ENVÍA AL ENDPOINT
                axios.post(this.urlBase + 'save_entrega_cotizacion', payload, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    if (res.data.msg === 'ok') {
                        this.mostrarModalDomicilio = false;
                        // Pasa el id a buscarDirecciones
                        this.buscarDirecciones(res.data.id);
                        this.erroresDomicilio = [];
                        this.errores = {};
                    } else {
                        this.erroresDomicilio = res.data.errors || ['Ocurrió un error al guardar el domicilio.'];
                    }
                }).catch(() => {
                    this.erroresDomicilio = ['Error inesperado al guardar el domicilio.'];
                });

            },

            /// =========================
            // GUARDA COTIZACION OFFLINE EN LOCALFORAGE
            // =========================
            guardarCotizacionOffline(payload) {
                // Usa localforage para guardar un arreglo de cotizaciones pendientes
                localforage.getItem('cotizaciones_offline').then(cotis => {
                    cotis = cotis || [];
                    cotis.push({
                        datos: payload,
                        fecha: new Date().toISOString()
                    });
                    return localforage.setItem('cotizaciones_offline', cotis);
                }).then(() => {
                    Swal.fire('Guardado offline', 'La cotización fue almacenada en tu equipo y se enviará automáticamente cuando tengas internet.', 'info');
                    // Opcional: puedes reiniciar el formulario aquí si lo deseas
                    // this.reiniciarFormulario();
                }).catch(() => {
                    this.mostrarAlerta('No fue posible guardar la cotización localmente.');
                });
            },


            // === LOCALFORAGE: SINCRONIZA TODAS LAS COTIZACIONES PENDIENTES ===
            sincronizarCotizacionesPendientes() {
                localforage.getItem('cotizaciones_pendientes').then(pendientes => {
                    if (!pendientes || pendientes.length === 0) {
                        this.mostrarAlerta('No hay cotizaciones pendientes de sincronizar.');
                        return;
                    }

                    axios.post(this.urlBase + 'sync_cotizaciones', { cotizaciones: pendientes }, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(res => {
                            if (res.data.success) {
                                localforage.removeItem('cotizaciones_pendientes');
                                this.mostrarAlerta('¡Cotizaciones offline sincronizadas correctamente!');
                            } else {
                                this.mostrarAlerta('Error al sincronizar: ' + res.data.message);
                            }
                        })
                        .catch(() => {
                            this.mostrarAlerta('No se pudo sincronizar (sin conexión)');
                        });
                });
            },


            // =========================
            // FINALIZAR Y REINICIAR FORMULARIO
            // =========================
            finalizarCotizacion(accion) {
                // Limpia errores visuales previos
                this.errores = {};
                let vacios = [];
                if (!this.partner_id) {
                    vacios.push('Socio');
                    this.errores.partner_id = true;
                }
                if (!this.partner_contact_id) {
                    vacios.push('Contacto');
                    this.errores.partner_contact_id = true;
                }
                if (!this.reference) {
                    vacios.push('Referencia');
                    this.errores.reference = true;
                }
                if (!this.valid_date) {
                    vacios.push('Fecha válida');
                    this.errores.valid_date = true;
                }

                // Si faltan campos, lanza alerta y marca inputs
                if (vacios.length) {
                    let msg = 'Completa todos los campos obligatorios: ' + vacios.join(', ');
                    this.mostrarAlerta(msg);
                    return;
                }

                if (!this.partidas.length) {
                    this.mostrarAlerta('Agrega al menos un producto o servicio.');
                    return;
                }
                for (let item of this.partidas) {
                    if (parseFloat(item.unit_price) === 0) {
                        this.mostrarAlerta('No puedes cotizar productos con precio en 0.');
                        return;
                    }
                    let min = parseInt(this.products_opts.find(p => p.id == item.id)?.minimum_sale || 0);
                    if (min > 0 && parseInt(item.quantity) < min) {
                        this.mostrarAlerta('La cantidad mínima de "' + item.name + '" es: ' + min);
                        return;
                    }
                }

                // Armado de payload
                let payload = {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    partner_id: this.partner_id,
                    products: JSON.stringify(this.partidas),
                    comments: this.comments || '' 
                };
                if (this.payment_id) payload.payment_id = this.payment_id;
                if (this.address_id) payload.address_id = this.address_id;
                //if (this.employee_id) payload.seller_asig_id = this.employee_id;
                // 👇 Corrección: mandamos employee_id separado
                if (this.employee_id) {
                    payload.employee_id = this.employee_id;         // quien guarda la cotización
                    payload.seller_asig_id = this.seller_asig_id || this.employee_id; // asignación vendedor
                }
                if (this.partner_contact_id) payload.partner_contact_id = this.partner_contact_id;
                if (this.reference) payload.reference = this.reference;
                if (this.valid_date) payload.valid_date = this.valid_date;
                if (this.comments) payload.comments = this.comments;

                //SI VIENE DE PENDIENTE 
                if (this.pending_id) {
                    payload.pending_id = this.pending_id;
                    console.log('[DEBUG] Enviando pending_id:', this.pending_id);
                }

                // Enviar solicitud AJAX
                // Detectar si es edición o nueva
                let endpoint = (this.modoEdicion && this.quote_id)
                    ? 'finalizar_edicion'
                    : 'finalizar_cotizacion';

                // Si es edición, asegúrate de mandar el ID
                if (endpoint === 'finalizar_edicion') {
                    payload.quote_id = this.quote_id;
                }

                axios.post(this.urlBase + endpoint, payload, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    if (res.data.msg === 'ok') {
                        this.quote_id = res.data.data.quote_id;

                        // ===============================
                        // ACCIONES SEGÚN BOTÓN PRESIONADO
                        // ===============================
                        if (accion === 'nuevo') {
                            Swal.fire('¡Guardada!', 'La cotización quedó registrada, puedes capturar una nueva.', 'success');
                            this.resetCampos();
                            this.cotizacionGuardada = false;
                        }

                        if (accion === 'ver') {
                            Swal.fire('¡Guardada!', 'Cotización registrada. Ahora puedes enviarla por correo o seguir editando.', 'success');
                            this.cotizacionGuardada = true;

                            // ⚡ Si era nueva y ahora ya existe, redirige a edición
                            if (!this.modoEdicion) {
                                window.location.href = this.urlBase.replace('ajax/', '') + 'cotizaciones/editar/' + this.quote_id;
                            }
                            // ⚡ Si ya estabas en edición, solo actualiza el flag
                            else {
                                this.modoEdicion = true;
                            }
                        }

                        if (accion === 'cerrar') {
                            Swal.fire('¡Guardada!', 'Cotización registrada. Regresando al listado.', 'success');
                            setTimeout(() => {
                                window.location.href = '/sajor/index.php/admin/cotizaciones';
                            }, 1200);
                        }
                    } else {
                        this.mostrarAlerta(res.data.msg || 'Error al guardar la cotización');
                    }
                }).catch((err) => {
                    if (!navigator.onLine) {
                        this.guardarCotizacionOffline(payload);
                        Swal.fire('Sin conexión', 'La cotización fue guardada localmente y se sincronizará cuando vuelvas a tener internet.', 'info');
                        return;
                    }
                    this.mostrarAlerta('Ocurrió un error inesperado.');
                });

            },

            descargarPdfCotizacion() {
                if (!this.cotizacionGuardada || !this.cotizacion.id) {
                    alert('Por favor, guarda la cotización antes de descargar el PDF.');
                    return;
                }
                const cotizacionId = this.cotizacion.id; // Asume que el ID de la cotización está en this.cotizacion.id
                const downloadUrl = '<?php echo \Uri::create('/admin/cotizaciones/descargar_pdf/'); ?>' + cotizacionId;
                window.open(downloadUrl, '_blank'); // Abre en una nueva pestaña para forzar la descarga
            },



            reiniciarFormulario() {
                // Confirmación con SweetAlert2 compatible con versiones viejas
                if (window.Swal) {
                    Swal.fire({
                        title: '¿Seguro que deseas reiniciar la cotización?',
                        text: 'Se perderán todos los datos capturados.',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, reiniciar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.value || result.isConfirmed) {
                            this.resetCampos();
                        }
                    });
                } else {
                    if (confirm('¿Seguro que deseas reiniciar la cotización?')) {
                        this.resetCampos();
                    }
                }
            },

            

            // Resetea todos los campos y arreglos del formulario
            resetCampos() {
                this.socioSeleccionado = null;
                this.bloqueadoSocio = false;
                this.partner_id = '';
                this.partner_contact_id = '';
                this.reference = '';
                this.valid_date = '';
                this.address_id = '';
                this.payment_id = this.payments_opts.find(p => p.name.toLowerCase().includes('transf'))?.id || '';
                this.employee_id = this.employees_opts.find(e => e.id == window.access_id)?.id || '';
                this.comments = '';
                this.tax_general = this.tax_opts[1]?.id || '';
                this.retention_general = '';
                this.moneda_id = this.moneda_opts[1]?.id || '';
                this.tipo_partida = 'Articulo';
                this.productoSeleccionado = null;
                this.nuevo_producto_id = '';
                this.nuevo_quantity = 1;
                this.nuevo_unit_price = '';
                this.nuevo_product_image = '/assets/uploads/thumb_no_image.png';
                this.partidas = [];
                this.contacts_opts = [];
                this.addresses_opts = [];
                this.products_opts = [];
                this.vendedorAsignado = '';
                this.bloqueado = true;
                this.camposEntregaActivos = false;
                this.errores = {};
            },

            imprimirCotizacion() {
                if (!this.quote_id) {
                    this.mostrarAlerta('No se ha guardado la cotización.');
                    return;
                }
                // La ruta puede variar según tu estructura
                const url = this.urlBase + 'imprimir/' + this.quote_id;
                window.open(url, '_blank');
            },

            abrirModalCorreo() {
                this.alertaCorreo = '';
                this.exitoCorreo = '';
                $('#modal-correo-cotizacion').modal('show');
            },

            enviarCotizacionCorreo() {
                // Validación básica
                if (!this.correoPrincipalSeleccionado && !this.correosAdicionales) {
                    this.alertaCorreo = 'Debes seleccionar al menos un correo.';
                    return;
                }
                this.alertaCorreo = '';
                this.exitoCorreo = '';

                let destinatarios = [];
                if (this.correoPrincipalSeleccionado && this.correoSocioPrincipal) {
                    destinatarios.push(this.correoSocioPrincipal);
                }
                // Extrae correos adicionales y limpia espacios
                if (this.correosAdicionales) {
                    let adicionales = this.correosAdicionales
                        .split(',')
                        .map(correo => correo.trim())
                        .filter(correo => correo.length > 0);
                    destinatarios = destinatarios.concat(adicionales);
                }

                // Valida correos (sólo formato básico, puedes mejorar)
                let invalidos = destinatarios.filter(correo => !/^[\w\-.]+@[\w\-.]+\.\w{2,7}$/.test(correo));
                if (invalidos.length) {
                    this.alertaCorreo = 'Verifica los siguientes correos: ' + invalidos.join(', ');
                    return;
                }

                // Muestra cargando
                this.alertaCorreo = '';
                this.exitoCorreo = 'Enviando...';

                // AJAX para mandar correo
                axios.post(this.urlBase + 'enviar_cotizacion_correo', {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    quote_id: this.quote_id, // O el id correspondiente de la cotización
                    destinatarios: destinatarios
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(res => {
                    if (res.data.msg === 'ok') {
                        this.exitoCorreo = '¡Correo enviado exitosamente!';
                        this.alertaCorreo = '';
                        setTimeout(() => { $('#modal-correo-cotizacion').modal('hide'); }, 2000);
                    } else {
                        this.exitoCorreo = '';
                        this.alertaCorreo = res.data.message || 'Ocurrió un error al enviar el correo.';
                    }
                }).catch(() => {
                    this.exitoCorreo = '';
                    this.alertaCorreo = 'Ocurrió un error inesperado.';
                });
            },

            // =========================
            // SWEET ALERT DE MENSAJES (COMPATIBLE CON VERSIONES VIEJAS)
            // =========================
            mostrarAlerta(msg) {
                if (window.Swal) {
                    // Sin parámetro 'icon', solo texto y título
                    Swal.fire({ title: 'Atención', text: msg });
                } else {
                    alert(msg);
                }
            },

            // =========================
            // CONTROL VISUAL: OCULTAR/MOSTRAR IMPUESTO Y RETENCION EN LA TABLA
            // =========================
            toggleImpuesto() {
                this.showImpuesto = !this.showImpuesto;
            },
            toggleRetencion() {
                this.showRetencion = !this.showRetencion;
            }
        }
    });
}

// ENFOQUE AUTOMÁTICO AL ABRIR LOS MODALES
$('#modal-agregar-marca').on('shown.bs.modal', function () {
    let input = document.querySelector('input[placeholder="Buscar marca..."]');
    if (input) input.focus();
});
$('#modal-agregar-rango').on('shown.bs.modal', function () {
    let input = document.querySelector('input[placeholder="Código inicio"]');
    if (!input) input = this.querySelector('input[type="text"]');
    if (input) input.focus();
});

// =========================
// SINCRONIZACION AUTOMATICA DE COTIZACIONES OFFLINE
// =========================
window.addEventListener('online', function () {
    // Al reconectarse, intenta sincronizar cotizaciones offline
    localforage.getItem('cotizaciones_offline').then(cotis => {
        if (!cotis || cotis.length === 0) return;

        // Si hay cotizaciones offline, intenta enviarlas una a una
        const enviarPendientes = async () => {
            for (let i = 0; i < cotis.length; i++) {
                let cot = cotis[i];
                try {
                    let res = await axios.post(_vueCotizacion.urlBase + 'finalizar_cotizacion', cot.datos, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.data && res.data.msg === 'ok') {
                        // Eliminada de la lista si fue exitosa
                        cotis.splice(i, 1);
                        i--; // Corrige el índice al eliminar
                    }
                } catch (e) {
                    // Si sigue sin conexión, detén el ciclo
                    break;
                }
            }
            // Actualiza la lista en localforage
            return localforage.setItem('cotizaciones_offline', cotis);
        };

        enviarPendientes().then(() => {
            if (!cotis || cotis.length === 0) {
                Swal.fire('¡Cotizaciones sincronizadas!', 'Tus cotizaciones pendientes ya fueron enviadas.', 'success');
            }
        });
    });
});


// =========================
// MODO EDICION: CARGA DATOS DE COTIZACION PENDIENTE
// =========================    
if (window.modoEdicion && window.cotizacionEdicion) {
    console.log('[DEBUG] Modo edición activado.');
    let q = window.cotizacionEdicion;

    this.modoEdicion = true;
    this.partner_id = q.partner_id;
    this.partner_contact_id = q.partner_contact_id;
    this.reference = q.reference;
    this.valid_date = q.valid_date;
    this.address_id = q.address_id;
    this.payment_id = q.payment_id;
    this.employee_id = q.employee_id;
    this.comments = q.comments;
    this.partidas = q.products.map(p => ({
        product_id: p.product_id,
        name: p.name,
        quantity: p.quantity,
        unit_price: p.unit_price
    }));

    this.bloqueadoSocio = true; // Bloqueamos selección de socio en modo edición
}

