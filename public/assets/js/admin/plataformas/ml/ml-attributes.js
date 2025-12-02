// ======================================================================
//  MERCADO LIBRE – ATRIBUTOS DE PRODUCTO (VUE)
//  Versión Final – ERP SAJOR (FuelPHP 1.8.2)
// ======================================================================

document.addEventListener('DOMContentLoaded', function () {

    if (typeof Vue === 'undefined' || typeof axios === 'undefined') {
        console.error('[ML][ATTR] Vue o Axios no están cargados.');
        return;
    }

    const appEl = document.getElementById('ml-attributes-app');
    const urlEl = document.getElementById('url-location');

    if (!appEl || !urlEl) {
        console.warn('[ML][ATTR] No se encontró ml-attributes-app o url-location.');
        return;
    }

    const urlBase = urlEl.dataset.url;
    var cfg = window.ML_ATTR_CONFIG || {};

    // ======================================================
    //  INSTANCIA VUE
    // ======================================================
    const app = new Vue({
        el: '#ml-attributes-app',

        data() {
            return {
                urlBase: urlBase,
                configId: cfg.configId || null,
                productId: cfg.productId || null,
                category_id: cfg.categoryId || '',
                attributes: [],
                values: {},
                loading: false,
                saving: false,
                errors: []
            };
        },

        // ===============================
        // MÉTRICAS DE VALIDACIÓN
        // ===============================
        computed: {
            requiredTotal() {
                return this.attributes.filter(a => a.is_required == 1).length;
            },
            requiredCompleted() {
                return this.attributes.filter(a => {
                    if (a.is_required != 1) return false;
                    let v = this.values[a.category_attribute_id];
                    return v !== null && v !== undefined && String(v).trim() !== '';
                }).length;
            },
            optionalCount() {
                return this.attributes.filter(a => a.is_required != 1).length;
            }
        },

        mounted() {
            if (this.category_id) {
                this.fetchAttributes();
            }
        },

        methods: {

            api(endpoint) {
                return this.urlBase + endpoint;
            },

            // =======================================================
            // ORDENAR ATRIBUTOS: Obligatorios arriba
            // =======================================================
            sortAttributes() {
                this.attributes = this.attributes.sort((a, b) => {

                    // 1. Requeridos primero
                    if (a.is_required == 1 && b.is_required != 1) return -1;
                    if (a.is_required != 1 && b.is_required == 1) return 1;

                    // 2. Catálogo (catalog_required)
                    if (a.is_catalog_required == 1 && b.is_catalog_required != 1) return -1;
                    if (a.is_catalog_required != 1 && b.is_catalog_required == 1) return 1;

                    // 3. Variación
                    if (a.is_variation == 1 && b.is_variation != 1) return -1;
                    if (a.is_variation != 1 && b.is_variation == 1) return 1;

                    // 4. Orden alfabético
                    return a.name.localeCompare(b.name);
                });
            },

            // =======================================================
            // 1. OBTENER ATRIBUTOS
            // =======================================================
            fetchAttributes() {
                if (!this.category_id) return;

                this.loading = true;
                this.errors = [];

                axios.post(
                    this.api('get_category_attributes_ml'),
                    {
                        config_id: this.configId,
                        product_id: this.productId,
                        category_id: this.category_id
                    },
                    { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
                )
                    .then(res => {
                        if (res.data && res.data.success) {

                            this.attributes = res.data.attributes || [];
                            this.values = res.data.values || {};

                            if (this.attributes.length === 0) {
                                // Sincronización automática
                                axios.post(
                                    this.api('sync_category_attributes_ml'),
                                    {
                                        config_id: this.configId,
                                        category_id: this.category_id
                                    },
                                    { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
                                )
                                    .then(syncRes => {
                                        if (syncRes.data && syncRes.data.success) {
                                            this.fetchAttributes();
                                        } else {
                                            this.errors.push(syncRes.data.msg || 'No se pudo sincronizar atributos ML.');
                                        }
                                    })
                                    .catch(err => {
                                        console.error('[ML][SYNC][AUTO] Error:', err);
                                        this.errors.push('Error al sincronizar atributos ML.');
                                    });

                                return;
                            }

                            // Ordenar después de cargar
                            this.sortAttributes();

                        } else {
                            this.errors.push(res.data.error || res.data.msg || 'No se pudieron obtener atributos.');
                        }
                    })
                    .catch(err => {
                        console.error('[ML][FETCH] Error:', err);
                        this.errors.push('Error de comunicación al obtener atributos.');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },

            // =======================================================
            // 2. GUARDAR ATRIBUTO INDIVIDUAL
            // =======================================================
            onChange(attr) {
                this.saving = true;

                const payload = {
                    config_id: this.configId,
                    product_id: this.productId,
                    category_id: this.category_id,
                    attribute_id: attr.ml_attribute_id,
                    value: this.values[attr.category_attribute_id]
                };

                console.log("[ML][SAVE][REQUEST]", payload);

                axios.post(
                    this.api('save_product_attributes_ml'),
                    payload,
                    { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
                )
                    .then(res => {
                        console.log("[ML][SAVE][RESPONSE]", res.data);

                        if (!res.data || !res.data.success) {
                            this.errors.push(res.data.msg || 'Error al guardar atributo.');
                        }
                    })
                    .catch(err => {
                        console.error('[ML][SAVE] Error:', err);
                        this.errors.push('Error al guardar atributo ' + attr.name);
                    })
                    .finally(() => {
                        this.saving = false;
                    });
            }
,

            // =======================================================
            // 3. SINCRONIZACIÓN MANUAL (desde el botón externo)
            // =======================================================
            syncNow() {

                if (!this.category_id) {
                    this.errors.push('Asigna una categoría ML antes de sincronizar.');
                    return;
                }

                if (!confirm('¿Sincronizar atributos oficiales de Mercado Libre para esta categoría?')) {
                    return;
                }

                this.loading = true;
                this.errors = [];

                axios.post(
                    this.api('sync_category_attributes_ml'),
                    {
                        config_id: this.configId,
                        category_id: this.category_id
                    },
                    { headers: { "X-Requested-With": "XMLHttpRequest" } }
                )
                    .then(res => {
                        if (res.data && res.data.success) {

                            // Actualizar atributos inmediatamente
                            this.fetchAttributes();

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sincronización completada',
                                    html:
                                        'Atributos: <b>' + (res.data.attrs_synced || 0) + '</b><br>' +
                                        'Valores: <b>' + (res.data.values_synced || 0) + '</b>',
                                    timer: 2500
                                });
                            } else {
                                alert('Atributos sincronizados.');
                            }

                        } else {
                            this.errors.push(res.data.msg || 'Error durante sincronización.');
                        }
                    })
                    .catch(err => {
                        console.error('[ML][SYNC] Error:', err);
                        this.errors.push('Error al sincronizar atributos ML.');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }
        }
    });

    // ======================================================
    // Conectar botón externo → Vue.syncNow()
    // ======================================================
    const btnSync = document.getElementById('btn-sync-ml-attrs');
    if (btnSync) {
        btnSync.addEventListener('click', function () {
            app.syncNow();
        });
    }

});
