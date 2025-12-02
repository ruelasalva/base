if (document.getElementById('notascredito-app')) {
    
    Vue.component('v-select', window.VueSelect.VueSelect);
    new Vue({
        el: '#notascredito-app',
        data: {
            access_id: window.access_id || '',
            access_token: window.access_token || '',
            urlBase: document.getElementById('url-location').dataset.url,

            // formulario
            provider_id: '',
            proveedorSeleccionado: null,
            destino: 'facturas',
            observations: '',
            totalNota: 0,
            totalAplicado: 0,
            restante: 0,

            // catálogos
            providers_opts: [],
            facturas_opts: [],
            ocs_opts: [],
            loadingProviders: false,

            // selección
            seleccionFacturas: {},
            ocSeleccionada: ''
        },

        mounted() {
            console.log('[MOUNTED] App cargada con urlBase=', this.urlBase);
            this.cargarProveedoresIniciales();
        },

        methods: {
            // ======================
            // INICIALES
            // ======================
            cargarProveedoresIniciales() {
                console.log('[INI] Cargando proveedores iniciales...');
                axios.post(this.urlBase + 'search_providers', {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    term: ''
                }, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => {
                        console.log('[INI] Respuesta proveedores iniciales:', res.data);
                        this.providers_opts = res.data.msg === 'ok' ? (res.data.data || []) : [];
                        console.log('[INI] Providers cargados:', this.providers_opts.length);
                    })
                    .catch(err => {
                        console.error('[INI][ERROR] Al cargar proveedores iniciales:', err);
                    });
            },

            // ======================
            // BÚSQUEDA DE PROVEEDORES
            // ======================
            buscarProveedores(search, loading) {
                if (!search || search.length < 2) {
                    this.providers_opts = [];
                    return;
                }
                loading(true);
                axios.post(this.urlBase + 'search_providers', {
                    access_id: this.access_id,
                    access_token: this.access_token,
                    term: search
                }, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => {
                        this.providers_opts = res.data.msg === 'ok' ? res.data.data : [];
                        loading(false);
                    })
                    .catch(() => loading(false));
            },

            onSeleccionarProveedorManual() {
                console.log("[VUE][onSeleccionarProveedorManual] ID seleccionado:", this.provider_id);
                let prov = this.providers_opts.find(p => p.id == this.provider_id);
                if (prov) {
                    console.log("[VUE][onSeleccionarProveedorManual] Proveedor encontrado:", prov);
                    this.onSeleccionarProveedor(prov);
                } else {
                    console.warn("[VUE][onSeleccionarProveedorManual] No se encontró proveedor con ese ID");
                }
            },


            // ======================
            // SELECCIÓN DE PROVEEDOR
            // ======================
            onSeleccionarProveedor(prov) {
                console.log('[SELECT] Proveedor seleccionado:', prov);

                // Guardar objeto y su id
                this.proveedorSeleccionado = prov;
                this.provider_id = prov ? prov.id : '';

                if (!this.provider_id) {
                    console.warn('[SELECT] No hay provider_id, se limpian facturas y OCs');
                    this.facturas_opts = [];
                    this.ocs_opts = [];
                    return;
                }

                // ======================
                // FACTURAS
                // ======================
                console.log('[FACTURAS] Cargando facturas para provider_id=', this.provider_id);
                axios.post(this.urlBase + 'get_compras_facturas/' + this.provider_id, {
                    access_id: this.access_id,
                    access_token: this.access_token
                }, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => {
                        console.log('[FACTURAS] Respuesta:', res.data);
                        this.facturas_opts = res.data.msg === 'ok' ? (res.data.facturas || []) : [];
                        this.seleccionFacturas = {};
                        this.facturas_opts.forEach(f => {
                            this.$set(this.seleccionFacturas, f.id, { selected: false, amount: 0 });
                        });
                        console.log('[FACTURAS] Facturas cargadas:', this.facturas_opts.length);
                    })
                    .catch(err => {
                        console.error('[FACTURAS][ERROR] al cargar facturas:', err);
                    });

                // ======================
                // ORDENES DE COMPRA
                // ======================
                console.log('[OC] Cargando órdenes de compra para provider_id=', this.provider_id);
                axios.post(this.urlBase + 'get_compras_ocs/' + this.provider_id, {
                    access_id: this.access_id,
                    access_token: this.access_token
                }, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => {
                        console.log('[OC] Respuesta:', res.data);
                        this.ocs_opts = res.data.msg === 'ok' ? (res.data.ocs || []) : [];
                        console.log('[OC] OCs cargadas:', this.ocs_opts.length);
                    })
                    .catch(err => {
                        console.error('[OC][ERROR] al cargar OCs:', err);
                    });
            },

            // ======================
            // FACTURAS
            // ======================
            toggleFactura(id) {
                console.log('[FACTURA] toggleFactura id=', id);
                if (!this.seleccionFacturas[id]) {
                    this.$set(this.seleccionFacturas, id, { selected: true, amount: 0 });
                } else {
                    this.seleccionFacturas[id].selected = !this.seleccionFacturas[id].selected;
                }
                this.calcularTotales();
            },
            updateAmount(id, amount) {
                console.log('[FACTURA] updateAmount id=', id, ' amount=', amount);
                if (!this.seleccionFacturas[id]) {
                    this.$set(this.seleccionFacturas, id, { selected: true, amount: 0 });
                }
                this.seleccionFacturas[id].amount = parseFloat(amount) || 0;
                this.calcularTotales();
            },

            // ======================
            // TOTALES
            // ======================
            calcularTotales() {
                console.log('[TOTALES] Recalculando...');
                let aplicado = 0;
                Object.keys(this.seleccionFacturas).forEach(fid => {
                    let d = this.seleccionFacturas[fid];
                    if (d.selected) aplicado += parseFloat(d.amount || 0);
                });
                this.totalAplicado = aplicado;
                this.restante = parseFloat(this.totalNota || 0) - aplicado;
                console.log('[TOTALES] totalAplicado=', this.totalAplicado, ' restante=', this.restante);
            }
        }
    });
}
