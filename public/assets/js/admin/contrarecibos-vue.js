// ========== admin/contrarecibos-vue.js ==========

// RUTA BASE SEGÚN TU SISTEMA
// Asegúrate de que esta URL base sea correcta, debe apuntar a la raíz de tus controladores AJAX
// Ejemplo: si tus endpoints son /get_proveedores,
// y tu urlBase es 'http://tu-dominio.com/', entonces los axios.post deben ser urlBase + 'get_proveedores'
const urlBase = document.getElementById('url-location').dataset.url;

new Vue({
    el: '#contrarecibos-app',
    data: {
        proveedores: [],
        proveedor_id: '',
        facturas: [], // Aquí se cargarán las facturas del proveedor seleccionado
        dias_credito: null, // Días de crédito del proveedor o global
        origenCredito: '', // 'proveedor' o 'config'
        loading: false,
        errorMsg: '', // Para mensajes de error generales
        facturaDetalle: null,
        modalDetalleVisible: false // Controla la visibilidad del modal de Vue
    },
    computed: {
        // Regresa sólo facturas seleccionadas
        seleccionadas() {
            return this.facturas.filter(f => f.seleccionada);
        },
        // Suma total de las facturas seleccionadas
        totalSeleccionado() {
            return this.seleccionadas.reduce((acum, f) => acum + (parseFloat(f.total) || 0), 0);
        },
        // Propiedad computada para el checkbox "seleccionar todo"
        allSelected: {
            get() {
                // Si no hay facturas, no se puede seleccionar todo.
                // Si hay facturas, verifica si todas las que "pueden_pagar" están seleccionadas.
                const facturasFiltrables = this.facturas.filter(f => f.puede_pagar);
                // Si no hay facturas filtrables, el checkbox "seleccionar todo" debe ser false
                if (facturasFiltrables.length === 0) {
                    return false;
                }
                return facturasFiltrables.every(f => f.seleccionada);
            },
            set(value) {
                this.facturas.forEach(f => {
                    // Solo selecciona/deselecciona si la factura "puede_pagar"
                    if (f.puede_pagar) {
                        f.seleccionada = value;
                    }
                });
            }
        }
    },
    mounted() {
        this.cargarProveedores();
    },
    methods: {
        // CARGA LISTA DE PROVEEDORES
        cargarProveedores() {
            this.loading = true;
            this.errorMsg = '';
            // URL explícita para el endpoint
            axios.post(urlBase + 'get_proveedores', {})
                .then(resp => {
                    if (resp.data && resp.data.success) {
                        this.proveedores = resp.data.proveedores;
                    } else {
                        this.errorMsg = resp.data.message || 'No se recibieron proveedores.';
                        Swal.fire('Error', this.errorMsg, 'error');
                    }
                })
                .catch(err => {
                    this.errorMsg = 'Error al conectar con el servidor para cargar proveedores.';
                    Swal.fire('Error', this.errorMsg, 'error');
                })
                .finally(() => this.loading = false);
        },

        // EVENTO AL CAMBIAR PROVEEDOR
        onProveedorChange() {
            this.facturas = []; // Limpiar facturas al cambiar de proveedor
            this.dias_credito = null;
            this.origenCredito = '';
            this.cargarFacturas();
        },

        // CARGA FACTURAS DE UN PROVEEDOR
        cargarFacturas() {
            if (!this.proveedor_id) {
                this.facturas = [];
                return;
            }
            this.loading = true;
            this.errorMsg = '';
            // URL explícita para el endpoint
            axios.post(urlBase + 'get_facturas_pendientes', { proveedor_id: this.proveedor_id })
                .then(resp => {
                    console.log("Facturas recibidas:", resp.data); // LOG PARA DEPURAR
                    if (resp.data && resp.data.success) {
                        this.facturas = resp.data.facturas.map(f => ({
                            ...f,
                            seleccionada: f.puede_pagar // Seleccionar por defecto si puede pagar
                        }));
                        this.dias_credito = resp.data.dias_credito;
                        this.origenCredito = resp.data.origen_credito;
                    } else {
                        this.facturas = [];
                        this.errorMsg = resp.data.message || 'No se pudieron cargar facturas.';
                        Swal.fire('Error', this.errorMsg, 'error');
                    }
                })
                .catch(err => {
                    this.facturas = [];
                    this.errorMsg = 'Error al conectar con el servidor para cargar facturas.';
                    Swal.fire('Error', this.errorMsg, 'error');
                })
                .finally(() => this.loading = false);
        },

        // RECARGA FACTURAS DEL PROVEEDOR ACTUAL
        recargarFacturas() {
            this.cargarFacturas();
        },

        // ABRE MODAL DETALLE
        verDetalle(factura) {
            this.facturaDetalle = factura;
            this.modalDetalleVisible = true; // Cambia el estado de Vue
            // Usar jQuery para mostrar el modal de Bootstrap
            // Asegúrate de que jQuery y Bootstrap JS estén cargados
            $('#modal-detalle-factura').modal('show');
        },
        // CIERRA MODAL DETALLE
        cerrarModalDetalle() {
            this.modalDetalleVisible = false; // Cambia el estado de Vue
            this.facturaDetalle = null;
            // Usar jQuery para ocultar el modal de Bootstrap
            $('#modal-detalle-factura').modal('hide');
        },

        // PERMITE EDITAR DÍAS DE CRÉDITO (opcional, según lógica)
        editarDiasCredito() {
            Swal.fire({
                title: 'Modificar días de crédito',
                input: 'number',
                inputValue: this.dias_credito || '',
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => {
                    if (!value || parseInt(value) <= 0) {
                        return 'Ingresa un valor mayor a 0';
                    }
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const newDays = parseInt(result.value);
                    if (newDays > 0) {
                        this.dias_credito = newDays;
                        // Si quieres guardar este cambio en la configuración global del backend
                        // axios.post(urlBase + 'admin/config/update_payment_terms_days', { days: this.dias_credito })
                        //      .then(resp => { Swal.fire('Guardado', 'Días de crédito global actualizados.', 'success'); this.cargarFacturas(); })
                        //      .catch(err => { Swal.fire('Error', 'No se pudo guardar la configuración.', 'error'); });
                        // Si no lo guardas en backend, al menos recarga las facturas para recalcular fechas
                        this.cargarFacturas();
                        Swal.fire('Actualizado', 'Días de crédito aplicados para el cálculo.', 'success');
                    }
                }
            });
        },

        // CREA EL CONTRARECIBO
        crearContrarecibo() {
            if (this.seleccionadas.length === 0) {
                Swal.fire('Selecciona al menos una factura', '', 'warning');
                return;
            }
            if (!this.dias_credito) {
                Swal.fire('Error', 'Debes configurar los días de crédito antes de generar el contrarecibo.', 'error');
                return;
            }

            this.loading = true;
            this.errorMsg = '';
            // URL explícita para el endpoint
            axios.post(urlBase + 'crear_contrarecibo', {
                proveedor_id: this.proveedor_id,
                facturas: this.seleccionadas.map(f => f.id) // Envía solo los IDs de las facturas seleccionadas
            })
                .then(resp => {
                    if (resp.data && resp.data.success) {
                        Swal.fire({
                            title: 'Contrarecibo generado',
                            html: `Se generó correctamente el contrarecibo <strong>${resp.data.receipt_number}</strong>.`,
                            icon: 'success'
                        }).then(() => {
                            this.cargarFacturas(); // Recargar facturas para ver los cambios de estado
                        });
                    } else {
                        this.errorMsg = resp.data.message || 'No se pudo crear el contrarecibo.';
                        Swal.fire('Error', this.errorMsg, 'error');
                    }
                })
                .catch(err => {
                    this.errorMsg = 'Error en el servidor al crear contrarecibo.';
                    Swal.fire('Error', this.errorMsg, 'error');
                })
                .finally(() => this.loading = false);
        }
    },
    filters: {
        currency(value) {
            if (value === null || value === undefined || isNaN(value)) return '$0.00';
            return '$' + parseFloat(value).toLocaleString('es-MX', { minimumFractionDigits: 2 });
        },
        formatDate(value) {
            if (!value || value === 'N/A') return 'N/A';
            // Asumiendo que el valor ya viene formateado 'DD/MM/YYYY' del backend
            return value;
        }
    }
});
